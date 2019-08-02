from config import rq
from time import sleep
from models import RootSchema

import requests
import bundle_allocation


@rq.job
def generate(student_preferences, callback_url):
    # fetch prefs resource from remote destination
    req = requests.get(student_preferences)
    print(req.status_code)
    req.raise_for_status()

    # deserialize
    rs = RootSchema()
    courses, bundle_items, students = rs.loads(req.json())

    # do stuff
    print(courses, bundle_items, students)
    # TODO
    return {"allocation_id": 'alloc_id123'}


@rq.job
def callback(url, data):
    r = requests.post(url, data)
    r.raise_for_status()
    # TODO
