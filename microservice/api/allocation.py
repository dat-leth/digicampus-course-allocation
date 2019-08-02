from jobs.allocation import generate
from models import Allocation, AllocationSchema
from config import rq
from flask import jsonify, make_response, url_for, redirect, request
import connexion


def post_allocation():
    req = request.get_json()
    job = generate.queue(req['studentPreferences'], req['callbackUrl'], timeout=3600, result_ttl=86400)
    if job is None:
        return connexion.problem(500, 'Internal Server Error', 'Executing job could not be created.')

    data = {
        "detail": "Requested allocation was accepted.",
        "status": 202,
        "job": url_for(".api_allocation_get_job", job_id=job.id, _external=True),
    }

    resp = make_response(jsonify(data), 202)
    resp.headers["Location"] = f"/job/{job.id}"
    return resp


def get_allocation(allocation_id):
    allocs = Allocation.query.filter(Allocation.alloc_id == allocation_id).all()
    alloc_schema = AllocationSchema(many=True)
    data = alloc_schema.dump(allocs)
    if not data:
        return connexion.problem(404, 'Not Found', 'The requested allocation was not found on the server. '
                                                   'Allocation might have been already deleted.')
    return data


def get_job(job_id):
    job = rq.get_queue().fetch_job(job_id)
    if job is not None:
        if job.get_status() == "finished":
            return redirect(url_for(".api_allocation_get_allocation", allocation_id=job.result["allocation_id"]),
                            code=303)
        else:
            return job.get_status()
    return connexion.problem(
        404,
        "Not Found",
        "The requested job was not found on the server. Job might have been already deleted.",
    )
