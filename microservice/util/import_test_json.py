from models import *
from config import db
from pprint import pprint
from datetime import datetime

import bundle_allocation
import requests
import numpy as np
import collections
import uuid

EPSILON = 1
GAMMA = 10

rs = RootSchema()

r = requests.get('http://localhost:5005/studentPrefs')
r.raise_for_status()
courses, bundle_items, students, overlaps = rs.load(r.json())

for s in students:
    s.generate_bundles(overlaps)

bps_alloc = bundle_allocation.generate_frac_alloc(students)
delta = np.min(np.ones(bps_alloc.vector.shape) - bps_alloc.vector)

if delta == 0.0:
    scale_factor = 1 - EPSILON / (GAMMA * np.linalg.norm(bps_alloc.vector))
    bps_alloc.vector *= scale_factor
    EPSILON = (EPSILON * (GAMMA - 1)) / GAMMA
    delta = np.min(np.ones(bps_alloc.vector.shape) - bps_alloc.vector)

convex_combination = bundle_allocation.lottery(bps_alloc, students, EPSILON, delta)

chosen_alloc = np.random.choice([alloc for alloc, coeff in convex_combination],
                                p=[coeff for alloc, coeff in convex_combination])

bundle_item_alloc = collections.defaultdict(list)
for i, (student, bundle) in enumerate(chosen_alloc.indices):
    if chosen_alloc.vector[i] == 1.0:
        for item in bundle:
            bundle_item_alloc[item].append(student)

allocation_id = uuid.uuid4()
allocations = []
for item in bundle_item_alloc:
    item.distribute_to_courses(bundle_item_alloc[item])
    for student in item.members:
        a = Allocation(alloc_id=str(allocation_id),
                       student=student.student_id,
                       bundle_item=item.bundle_item_id,
                       course=item.members[student].course_id,
                       priority=student.rankings[item.ranking_group].index(item) + 1,
                       timestamp=datetime.utcnow())
        db.session.add(a)
        allocations.append(a)
db.session.commit()
pprint(AllocationSchema(many=True).dump(allocations))