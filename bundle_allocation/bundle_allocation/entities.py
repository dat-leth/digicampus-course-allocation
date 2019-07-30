from dataclasses import dataclass, field
from typing import List, Tuple, Dict

import numpy as np
import itertools


@dataclass
class Course:
    id: str
    name: str
    capacity: int
    members: List = field(default_factory=list)

    def __repr__(self) -> str:
        return f"{self.name}"


@dataclass
class BundleItem:
    id: str
    sum_capacity: int
    ranking_group: str
    courses: List[Course]

    def __hash__(self) -> int:
        return hash(self.id)

    def __eq__(self, o: object) -> bool:
        if isinstance(o, BundleItem) and self.id == o.id:
            return True
        return False

    def __repr__(self) -> str:
        return f"{[c for c in self.courses]}, {self.sum_capacity}"


@dataclass
class Student:
    id: str
    rankings: Dict[str, List[BundleItem]]
    bundles: List[Tuple[BundleItem]] = None

    def generate_bundles(self, overlaps):
        product = [bundle for bundle in itertools.product(*self.rankings.values())]
        overlapping_bundles = []
        for bundle in product:
            for item in bundle:
                remainings = list(bundle)
                remainings.remove(item)
                if any(remaining_item in overlaps[item] for remaining_item in remainings):
                    overlapping_bundles.append(bundle)
                    break
            continue
        non_overlapping_bundles = [b for b in product if b not in overlapping_bundles]
        self.bundles = sorted(non_overlapping_bundles, key=lambda bundle: _sort_score(bundle, self))

    def __hash__(self) -> int:
        return hash(self.id)

    def __eq__(self, o: object) -> bool:
        if isinstance(o, Student) and self.id == o.id:
            return True
        return False

    def __repr__(self) -> str:
        return f"Student {self.id}"


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
