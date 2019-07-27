from bundle_allocation.entities import *
from bundle_allocation.iterative_rounding import iterative_rounding
from gurobipy import *
from typing import Dict, Union

import numpy as np
import copy


def lottery(bps_alloc: Allocation,
            students: List[Student],
            epsilon: float,
            delta: float) -> List[Tuple[Allocation, float]]:

    convex_combination = []
    int_alloc = iterative_rounding(bps_alloc, students)
    convex_combination.append((int_alloc, None))

    nearest_conv, convex_combination = nearest_point_on_conv(bps_alloc, convex_combination)

    distance = np.linalg.norm(bps_alloc.vector - nearest_conv.vector)
    while distance > epsilon:
        print(distance, len(convex_combination))
        new_alloc = copy.copy(bps_alloc)
        new_alloc.vector = bps_alloc.vector + delta * ((bps_alloc.vector - nearest_conv.vector) / distance)
        new_alloc.vector = np.clip(new_alloc.vector, 0, 1)

        try:
            new_int_alloc = iterative_rounding(new_alloc, students, nearest_conv, bps_alloc)
            convex_combination.append((new_int_alloc, None))

            nearest_conv, convex_combination = nearest_point_on_conv(bps_alloc, convex_combination)
            distance = np.linalg.norm(bps_alloc.vector - nearest_conv.vector)
        except InfeasibleException:
            print("BREAKING EARLY BECAUSE OF INFEASIBLE IRA")
            break

    return convex_combination


def nearest_point_on_conv(bps_alloc: Allocation,
                          int_allocs: List[Tuple[Allocation, Union[None, float]]],
                          threshold: float = 1.0e-10) -> Tuple[Allocation, List[Tuple[Allocation, float]]]:
    model = Model()
    model.setParam("LogToConsole", 0)
    lambda_vars = [(alloc, model.addVar(lb=0.0, ub=1.0)) for alloc, coeff in int_allocs]

    nearest_conv = copy.copy(bps_alloc)
    nearest_conv.vector = np.zeros(bps_alloc.vector.shape, dtype=object)
    for alloc, var in lambda_vars:
        nearest_conv.vector += alloc.vector * var

    objective = QuadExpr()
    for p, q in zip(nearest_conv.vector, bps_alloc.vector):
        objective.add((p - q) * (p - q))
    model.setObjective(objective, GRB.MINIMIZE)

    model.addConstr(quicksum([var for alloc, var in lambda_vars]) == 1)

    model.optimize()

    nearest_conv.vector = np.array([i.getValue() for i in nearest_conv.vector])

    convex_combination = [(alloc, var.X) for alloc, var in lambda_vars if var.X > threshold]

    # zip lambda var with point and 'just multiply' lambda with point
    return nearest_conv, convex_combination
