from dataclasses import dataclass, field
from typing import List, Tuple, Dict

import numpy as np
import itertools
import random

@dataclass
class Course:
    course_id: str
    course_name: str
    capacity: int
    members: List = field(default_factory=list)

    def __repr__(self) -> str:
        return f"{self.course_name}"


@dataclass
class BundleItem:
    bundle_item_id: str
    sum_capacity: int
    ranking_group: str
    courses: List[Course]
    members: Dict['Student', Course] = field(default_factory=dict)

    def distribute_to_courses(self, members: List['Student']):
        random.shuffle(members)
        amount_per_course = [round(c.capacity / self.sum_capacity * len(members)) for c in self.courses]
        prev_index = 0
        for i, c in enumerate(self.courses):
            c.members = members[prev_index:prev_index + amount_per_course[i]]
            prev_index = prev_index + amount_per_course[i]
            self.members.update({student: c for student in c.members})

    def __hash__(self) -> int:
        return hash(self.bundle_item_id)

    def __eq__(self, o: object) -> bool:
        if isinstance(o, BundleItem) and self.bundle_item_id == o.bundle_item_id:
            return True
        return False

    def __repr__(self) -> str:
        return f"{[c for c in self.courses]}, {self.sum_capacity}"


@dataclass
class Student:
    student_id: str
    rankings: Dict[str, List[BundleItem]]
    bundles: List[Tuple[BundleItem]] = None

    def generate_bundles(self, overlaps):
        product = [bundle for bundle in itertools.product(*self.rankings.values())]
        overlapping_bundles = []
        for bundle in product:
            for item in bundle:
                remainings = list(bundle)
                remainings.remove(item)
                if any(
                    remaining_item in overlaps[item] for remaining_item in remainings
                ):
                    overlapping_bundles.append(bundle)
                    break
            continue
        non_overlapping_bundles = [b for b in product if b not in overlapping_bundles]
        self.bundles = sorted(
            non_overlapping_bundles, key=lambda bundle: _sort_score(bundle, self)
        )

    def __hash__(self) -> int:
        return hash(self.student_id)

    def __eq__(self, o: object) -> bool:
        if isinstance(o, Student) and self.student_id == o.student_id:
            return True
        return False

    def __repr__(self) -> str:
        return f"Student {self.student_id}"


@dataclass
class Allocation:
    indices: List[Tuple[Student, Tuple[BundleItem]]]
    vector: np.ndarray

    def __hash__(self) -> int:
        return hash(repr(self))


class InfeasibleException(Exception):
    pass


def _sort_score(bundle, student):
    sum_ranks = 0
    sum_capacity = 0
    for item in bundle:
        sum_ranks += student.rankings[item.ranking_group].index(item) + 1
        sum_capacity += item.sum_capacity
    score = sum_ranks + (1 - (1 / sum_capacity))
    return score
