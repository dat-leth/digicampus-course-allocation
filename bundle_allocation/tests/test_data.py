from pprint import pprint

import json
import bundle_allocation
import numpy as np
import collections

with open('data_vv.json', 'r') as f:
    data = json.load(f)

EPSILON = 0.09
GAMMA = 100

courses = [bundle_allocation.Course(c['course_id'], c['course_name'], c['capacity']) for c in data['courses']]
bundle_items = [bundle_allocation.BundleItem(b['item_id'], b['sum_capacity'], [c2 for c1 in b['courses'] for c2 in courses if c1 == c2.id])
                for b in data['bundle_items']]
students = []
for s in data['students']:
    id = s['student_id']
    bundles = []
    for b in s['bundles']:
        bundle = tuple([i2 for i1 in b for i2 in bundle_items if i1 == i2.id])
        bundles.append(bundle)
    students.append(bundle_allocation.Student(id, bundles))

bps_alloc = bundle_allocation.generate_frac_alloc(students)
DELTA = np.min(np.ones(bps_alloc.vector.shape) - bps_alloc.vector)

print(bps_alloc.vector.shape)

# scale if needed
if np.min(np.ones(bps_alloc.vector.shape) - bps_alloc.vector) == 0.0:
    scale_factor = 1 - EPSILON / (GAMMA * np.linalg.norm(bps_alloc.vector))
    bps_alloc.vector *= scale_factor
    EPSILON = (EPSILON * (GAMMA - 1)) / GAMMA
    DELTA = np.min(np.ones(bps_alloc.vector.shape) - bps_alloc.vector)
    print(scale_factor, EPSILON, DELTA)

convex_combination = bundle_allocation.lottery(bps_alloc, students, EPSILON, DELTA)
for alloc, coeff in convex_combination:
    student_alloc = collections.defaultdict(int)
    for i, (student, bundle) in enumerate(alloc.indices):
        student_alloc[student] += alloc.vector[i]
    counts = collections.Counter((student_alloc[s] for s in student_alloc))
    print(alloc.vector, coeff, counts)

print()
print("CHOSEN")
chosen_alloc = np.random.choice([alloc for alloc, coeff in convex_combination], p=[coeff for alloc, coeff in convex_combination])
student_alloc = collections.defaultdict(int)
bundle_item_alloc = collections.defaultdict(set)
for i, (student, bundle) in enumerate(chosen_alloc.indices):
    student_alloc[student] += chosen_alloc.vector[i]
    if chosen_alloc.vector[i] == 1.0:
        for item in bundle:
            bundle_item_alloc[item].add(student)
counts = collections.Counter((student_alloc[s] for s in student_alloc))
print(chosen_alloc.vector, counts)
for item in bundle_item_alloc:
    print(item, len(bundle_item_alloc[item]))
print()
for s in student_alloc:
    if student_alloc[s] == 0.0:
        print(s, s.bundles)
