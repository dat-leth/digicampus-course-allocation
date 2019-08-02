from gurobipy import *
from typing import List
from bundle_allocation.entities import Student, Allocation, InfeasibleException
from pprint import pprint

import collections
import copy
import numpy as np
import random


def iterative_rounding(
    frac_alloc: Allocation,
    students: List[Student],
    nearest_on_conv: Allocation = None,
    bps_alloc: Allocation = None,
) -> Allocation:
    if np.array_equal(
        frac_alloc.vector, frac_alloc.vector.astype(bool)
    ):  # all components are either 0 or 1
        return frac_alloc

    max_len_bundle = max([len(b) for s, b in frac_alloc.indices])  # known as k in paper

    model = Model()
    model.setParam("LogToConsole", 0)
    model.modelSense = GRB.MAXIMIZE

    lower_bounds = np.where(frac_alloc.vector < 1, 0, 1)
    upper_bounds = np.where(frac_alloc.vector > 0, 1, 0)

    if nearest_on_conv is None and bps_alloc is None:
        obj_coeffs = np.zeros(frac_alloc.vector.shape)
        # objective coefficent: most wanted bundle = max_amount_bundles, then count down (indicating preference)
        max_amount_bundles = max(
            collections.Counter(s for s, b in frac_alloc.indices).values()
        )
        student_coeffs = {s: max_amount_bundles for s in students}
        for i, (student, bundle) in enumerate(frac_alloc.indices):
            obj_coeffs[i] = student_coeffs[student]
            student_coeffs[student] -= 1
    else:
        obj_coeffs = bps_alloc.vector - nearest_on_conv.vector
        max_amount_bundles = max(
            collections.Counter(s for s, b in frac_alloc.indices).values()
        )
        student_coeffs = {s: max_amount_bundles for s in students}
        for i, (student, bundle) in enumerate(frac_alloc.indices):
            obj_coeffs[i] += student_coeffs[student]
            student_coeffs[student] -= 1

    x = model.addVars(
        frac_alloc.indices, lb=lower_bounds, ub=upper_bounds, obj=obj_coeffs
    )

    x_per_item = collections.defaultdict(list)
    indices_per_item = collections.defaultdict(list)
    for i, (student, bundle) in enumerate(x):
        for item in bundle:
            x_per_item[item].append(x[student, bundle])
            indices_per_item[item].append(i)

    demand_constrs = model.addConstrs(
        (x.sum(s, "*") <= 1.0 for s in students), "demand"
    )

    supply_constrs = model.addConstrs(
        (quicksum(x_per_item[i]) <= i.sum_capacity for i in x_per_item),
        "supply",  # if quicksum > sum_capapcity
    )
    model.update()

    # Update supply constraints
    # Adjust RHS (remaining capacity) of bundle items
    for item in supply_constrs:
        item_vector = np.take(frac_alloc.vector, indices_per_item[item])
        new_rhs = supply_constrs[item].getAttr("RHS") - np.sum(
            item_vector[item_vector == 1]
        )
        supply_constrs[item].setAttr("RHS", new_rhs)
    model.update()

    # Remove relaxable constraints, let overallocate by at most k - 1
    ceiled_frac_vector = np.ceil(frac_alloc.vector)
    non_fixed_components = np.not_equal(frac_alloc.vector, ceiled_frac_vector)
    relaxable_supply_constrs = []
    for item in supply_constrs:
        # new shorter vectors where components are taken from original,
        # so that only components of item are considered
        non_fixed_item = np.take(non_fixed_components, indices_per_item[item])
        ceiled_result_item = np.take(ceiled_frac_vector, indices_per_item[item])

        # np.array([1,2,3])[True, False, True] => [1, 3]
        if (
            np.sum(ceiled_result_item[non_fixed_item])
            <= supply_constrs[item].getAttr("RHS") + max_len_bundle - 1
        ):
            relaxable_supply_constrs.append(item)

    for item in relaxable_supply_constrs:
        model.remove(supply_constrs[item])
        supply_constrs.pop(item)
    model.update()
    model.optimize()
    if model.getAttr("STATUS") == GRB.OPTIMAL:
        result = np.zeros(frac_alloc.vector.shape)
        for s, b in x:
            i = frac_alloc.indices.index((s, b))
            result[i] = x[s, b].X
    else:
        raise InfeasibleException()

    while not np.array_equal(result, result.astype(bool)):
        # Fix values of variables where 0 or 1
        lower_bounds = np.where(result < 1, 0, 1)
        upper_bounds = np.where(result > 0, 1, 0)
        model.setAttr("LB", model.getVars(), lower_bounds)
        model.setAttr("UB", model.getVars(), upper_bounds)

        # Update supply constraints
        # Adjust RHS (remaining capacity) of bundle items
        fixed_per_item = collections.Counter(
            (i for s, b in x for i in b if x[s, b].X == 1.0)
        )
        for item in supply_constrs:
            new_rhs = supply_constrs[item].getAttr("RHS") - fixed_per_item[item]
            supply_constrs[item].setAttr("RHS", new_rhs)
        model.update()

        # Remove relaxable constraints, let overallocate by at most k - 1
        ceiled_result = np.ceil(result)
        non_fixed_components = np.not_equal(result, ceiled_result)
        relaxable_supply_constrs = []
        for item in supply_constrs:
            # new shorter vectors where components are taken from original,
            # so that only components of item are considered
            non_fixed_item = np.take(non_fixed_components, indices_per_item[item])
            ceiled_result_item = np.take(ceiled_result, indices_per_item[item])

            # np.array([1,2,3])[True, False, True] => [1, 3]
            if (
                np.sum(ceiled_result_item[non_fixed_item])
                <= supply_constrs[item].getAttr("RHS") + max_len_bundle - 1
            ):
                relaxable_supply_constrs.append(item)
            else:
                print(
                    item,
                    np.take(result, indices_per_item[item])[non_fixed_item],
                    [
                        frac_alloc.indices[i]
                        for i, b in zip(indices_per_item[item], non_fixed_item)
                        if b == True
                    ],
                )

        for item in relaxable_supply_constrs:
            model.remove(supply_constrs[item])
            supply_constrs.pop(item)
        model.update()
        model.optimize()
        if model.getAttr("Status") == GRB.OPTIMAL:
            result = np.zeros(frac_alloc.vector.shape)
            for s, b in x:
                i = frac_alloc.indices.index((s, b))
                result[i] = x[s, b].X
        else:
            raise InfeasibleException()

    int_alloc = copy.copy(frac_alloc)
    int_alloc.vector = result
    return int_alloc
