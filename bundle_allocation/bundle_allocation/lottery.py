from bundle_allocation.entities import *
from bundle_allocation.iterative_rounding import iterative_rounding
from gurobipy import *
from typing import Dict, Union

import numpy as np
import copy


def lottery(
    bps_alloc: Allocation, students: List[Student], epsilon: float, delta: float
) -> List[Tuple[Allocation, float]]:
    """
    Generates a probability distribution of integral solution around a fractional solution (bps_alloc)
    Finds integral solutions step by step and spans a convex hull.
    Distance of nearest point in the convex hull to the fractional solution is determinited.
    If distance is closer than epsilon, probability distribution is accepted.
    Delta is the distance with which a new fractional solution is found that is on the opposite side of
    bps_alloc from nearest point in convex hull
    This new fractional solution is used to determine a integral solution on the opposite side
    """

    # First integral allocation
    convex_combination = []
    int_alloc = iterative_rounding(bps_alloc, students)
    convex_combination.append((int_alloc, None))

    # Determine nearest point of convex hull with one integral point in set
    nearest_conv, convex_combination = nearest_point_on_conv(
        bps_alloc, convex_combination
    )

    distance = np.linalg.norm(bps_alloc.vector - nearest_conv.vector)
    while distance > epsilon:
        print(distance, len(convex_combination))

        # Find new fractional solution on the opposite side of bps_alloc
        new_alloc = copy.copy(bps_alloc)
        new_alloc.vector = bps_alloc.vector + delta * (
            (bps_alloc.vector - nearest_conv.vector) / distance
        )
        new_alloc.vector = np.clip(new_alloc.vector, 0, 1)

        # Try to find a integral solution that is in the direction of new_alloc
        try:
            new_int_alloc = iterative_rounding(
                new_alloc, students, nearest_conv, bps_alloc
            )

            # Add to solution set
            convex_combination.append((new_int_alloc, None))

            # Calculate nearest point on convex hull and its probability distribution
            nearest_conv, convex_combination = nearest_point_on_conv(
                bps_alloc, convex_combination
            )

            # If allocation does not contribute to getting closer, break
            prev_distance = distance
            distance = np.linalg.norm(bps_alloc.vector - nearest_conv.vector)
            if prev_distance - distance < 1.0e-5:
                break
        except InfeasibleException:
            print("BREAKING EARLY BECAUSE OF INFEASIBLE IRA")
            break

    return convex_combination


def nearest_point_on_conv(
    bps_alloc: Allocation,
    int_allocs: List[Tuple[Allocation, Union[None, float]]],
    threshold: float = 1.0e-5,
) -> Tuple[Allocation, List[Tuple[Allocation, float]]]:
    """
    Solving quadratic optimization problem to find a closest point in convex hull to a fractional solution
    """
    model = Model()
    model.setParam("LogToConsole", 0)

    # Coefficients of all integral solutions, bounded in intervall [0.0, 1.0]
    lambda_vars = [(alloc, model.addVar(lb=0.0, ub=1.0)) for alloc, coeff in int_allocs]

    # Transform linear combination of vectors in one matrix with variables
    nearest_conv = copy.copy(bps_alloc)
    nearest_conv.vector = np.zeros(bps_alloc.vector.shape, dtype=object)
    for alloc, var in lambda_vars:
        nearest_conv.vector += alloc.vector * var

    # Determine minimal euclidian distance
    objective = QuadExpr()
    for p, q in zip(nearest_conv.vector, bps_alloc.vector):
        objective.add((p - q) * (p - q))
    model.setObjective(objective, GRB.MINIMIZE)

    # sum of all coefficients has to be 1 to be a convex hull
    model.addConstr(quicksum([var for alloc, var in lambda_vars]) == 1)

    model.optimize()

    nearest_conv.vector = np.array([i.getValue() for i in nearest_conv.vector])

    # Convex combination as list of (integral allocation, coefficient)
    convex_combination = [
        (alloc, var.X) for alloc, var in lambda_vars if var.X > threshold
    ]

    # zip lambda var with point and 'just multiply' lambda with point
    return nearest_conv, convex_combination
