from models import *
from pprint import pprint

import bundle_allocation

with open('../marshmallow_vv_complete.json', 'r') as f:
    rs = RootSchema()
    courses, bundle_items, students, overlaps = rs.loads(f.read())
    for s in students:
        s.generate_bundles(overlaps)
        pprint(s.bundles)
