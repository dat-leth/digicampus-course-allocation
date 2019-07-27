from dataclasses import dataclass, field
from typing import List, Tuple

import numpy as np


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
    bundles: List[Tuple[BundleItem]]

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