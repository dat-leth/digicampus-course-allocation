from typing import List
from bundle_allocation.entities import Student, Allocation

import collections
import copy
import numpy as np


def generate_frac_alloc(students: List[Student]) -> Allocation:
    """
    Generates a fractional allocation (probability of student receiving a bundle) based
    on a given list of Students which each have a sorted list of wanted bundles (Student.bundles)

    Returns a allocation object where Allocation.indices is a list of (Student, Bundle) tuples and Allocation.vector is a numpy array
    List item and vector component corresponds based on index
    """

    # Deep copy student objects as modifications are made to bundles list
    # (removing bundles that cannot be claimed anymore)
    students_copy = copy.deepcopy(students)

    # Starting probability
    t = np.float_(0.0)
    frac_dict = collections.defaultdict(float)

    while t < 1.0:
        # All students can request its most wanted bundle
        demanded_bundles = []
        for student in students_copy:
            try:
                demanded_bundles.append((student, student.bundles[0]))
            except IndexError:
                pass

        # Demand is counted per bundle item (course)
        demand_per_bundle_item = collections.defaultdict(int)
        for student, bundle in demanded_bundles:
            for item in bundle:
                demand_per_bundle_item[item] += 1

        # Determine minimal allocatable fraction
        delta = np.min(
            [
                item.sum_capacity / demand_per_bundle_item[item]
                for item in demand_per_bundle_item
            ]
        )
        t += delta

        # Assign minimal fraction to requested bundle
        for student, bundle in demanded_bundles:
            frac_dict[student, bundle] += delta

        # Update supply (capacity) of each bundle item (course)
        for item in demand_per_bundle_item:
            item.sum_capacity -= delta * demand_per_bundle_item[item]
            if item.sum_capacity <= 0:
                for student in students_copy:
                    student.bundles = [
                        bundle for bundle in student.bundles if item not in bundle
                    ]

    # Create allocation object with (student, bundle) list and fractional vector
    # Refers to original student object
    frac_alloc = Allocation([], np.array([]))
    for (student, bundle), prob in frac_dict.items():
        student = students[students.index(student)]
        bundle = student.bundles[student.bundles.index(bundle)]
        frac_alloc.indices.append((student, bundle))
        frac_alloc.vector = np.append(frac_alloc.vector, prob)

    # Clipping vector since probability cannot be higher than 1
    frac_alloc.vector = frac_alloc.vector.clip(0, 1)
    assert len(frac_alloc.indices) == len(frac_alloc.vector) == len(frac_dict)

    return frac_alloc
