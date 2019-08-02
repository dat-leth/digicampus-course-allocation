from models import *
from config import rq, db

import bundle_allocation
import requests
import numpy as np
import collections
import uuid


# TODO: Add robustness on failing database
# TODO: Add ability to set epsilon and gamma via API
@rq.job
def generate(student_preferences, callback_url):
    epsilon = 1
    gamma = 10

    schema = RootSchema()

    # Request student preferences resource from remote server
    res = requests.get(student_preferences)
    res.raise_for_status()

    # Deserialize data
    courses, bundle_items, students, overlaps = schema.load(res.json())

    # Generate bundles for each student
    for s in students:
        s.generate_bundles(overlaps)

    # Generate fractional BPS solution
    bps_alloc = bundle_allocation.generate_frac_alloc(students)

    # Scale if needed
    delta = np.min(np.ones(bps_alloc.vector.shape) - bps_alloc.vector)
    if delta == 0.0:
        scale_factor = 1 - epsilon / (gamma * np.linalg.norm(bps_alloc.vector))
        bps_alloc.vector *= scale_factor
        epsilon = (epsilon * (gamma - 1)) / gamma
        delta = np.min(np.ones(bps_alloc.vector.shape) - bps_alloc.vector)

    # Select integral allocation
    convex_combination = bundle_allocation.lottery(bps_alloc, students, epsilon, delta)
    chosen_alloc = np.random.choice([alloc for alloc, coeff in convex_combination],
                                    p=[coeff for alloc, coeff in convex_combination])

    # Allocate student to bundle_item/course
    bundle_item_alloc = collections.defaultdict(list)
    for i, (student, bundle) in enumerate(chosen_alloc.indices):
        if chosen_alloc.vector[i] == 1.0:
            for item in bundle:
                bundle_item_alloc[item].append(student)

    # Save to database
    allocation_id = uuid.uuid4()
    allocations = []
    for item in bundle_item_alloc:
        item.distribute_to_courses(bundle_item_alloc[item])
        for student in item.members:
            a = Allocation(alloc_id=str(allocation_id),
                           student=student.student_id,
                           bundle_item=item.bundle_item_id,
                           course=item.members[student].course_id,
                           priority=student.rankings[item.ranking_group].index(item) + 1)
            db.session.add(a)
            allocations.append(a)
    db.session.commit()

    # Callback to remote server to announce that we're done!
    callback.queue(url=callback_url, data=AllocationSchema(many=True).dumps(allocations))
    return str(allocation_id)


# TODO: Move callback to another queue and add retries
@rq.job
def callback(url, data):
    r = requests.post(url=url, data=data)
    r.raise_for_status()
