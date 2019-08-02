import collections
import pandas as pd

from bundle_allocation import *
from models import *


def import_csv(path, name_prefix):
    df = pd.read_csv(path)
    df.columns = df.columns.str.split('.').str[0]
    header = df.iloc[0:1, 9:]
    courses = []
    parallels = collections.defaultdict(list)
    for i, j in enumerate(header):
        c = Course(course_id=f"{name_prefix}_T{i + 1}",
                   course_name=f"{name_prefix} {j}",
                   capacity=int(header.iloc[0, i]))
        courses.append(c)
        parallels[j].append(c)

    bundle_items = []
    for p in parallels:
        bundle_items.append(BundleItem(bundle_item_id=f"b{name_prefix} {p}",
                                       sum_capacity=sum([c.capacity for c in parallels[p]]),
                                       ranking_group=f"{name_prefix}",
                                       courses=parallels[p]))

    df_prios = df.set_index('UserId').drop(df.columns[range(1, 9)], axis=1).drop(np.nan).replace(0, np.nan)
    rankings = collections.defaultdict(list)
    for vv_id in df_prios.index:
        ranked_timeslots = df_prios.loc[vv_id].dropna().sort_values().index.to_list()
        rankings[int(vv_id)] = [b for t in ranked_timeslots for b in bundle_items if b.bundle_item_id == f"b{name_prefix} {t}"]

    return courses, bundle_items, rankings


if __name__ == "__main__":
    prios = {'Info2': '../../data/vv_ss18_info2.csv', 'SI': '../../data/vv_ss18_si.csv', 'TI': '../../data/vv_ss18_ti.csv'}
    results = {}
    students = {}
    for p in prios:
        results[p] = import_csv(prios[p], p)
        for s in results[p][2]:
            if results[p][2][s]:
                if s not in students:
                    students[s] = Student(s, {p: results[p][2][s]}, [])
                else:
                    students[s].rankings[p] = results[p][2][s]

    overlaps = collections.defaultdict(list)
    for p in prios:
        for b in results[p][1]:
            overlaps[b].extend([b2 for p2 in prios for b2 in results[p2][1]
                                if b.bundle_item_id.split(' ', 1)[1] == b2.bundle_item_id.split(' ', 1)[1]])

    data = {
        "courses": [c for p in prios for c in results[p][0]],
        "bundle_items": [b for p in prios for b in results[p][1]],
        "students": [students[s] for s in students],
        "overlaps": [{
            "bundle_item": b,
            "overlapping_items": overlaps[b]
        } for b in overlaps]
    }

    rs = RootSchema()
    with open('../marshmallow_vv_complete.json', 'w') as f:
        f.write(rs.dumps(data))

