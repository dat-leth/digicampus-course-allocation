from config import db, ma
from datetime import datetime
from marshmallow import Schema, fields, post_load

import bundle_allocation


class Allocation(db.Model):
    __tablename__ = "allocation"
    alloc_id = db.Column(db.String, primary_key=True)
    student = db.Column(db.String)
    course = db.Column(db.String)
    priority = db.Column(db.Integer)
    timestamp = db.Column(db.DateTime, default=datetime.utcnow(), onupdate=datetime.utcnow())


class AllocationSchema(ma.ModelSchema):
    class Meta:
        model = Allocation
        sqla_session = db.session
        fields = ["student", "course", "priority"]


class CourseSchema(Schema):
    course_id = fields.Str()
    course_name = fields.Str()
    capacity = fields.Integer()


class BundleItemSchema(Schema):
    bundle_item_id = fields.Str()
    sum_capacity = fields.Integer()
    ranking_group = fields.Str()
    courses = fields.Nested(CourseSchema, only=["course_id"], many=True)


class StudentSchema(Schema):
    student_id = fields.Str()
    rankings = fields.Dict(keys=fields.Str(),
                           values=fields.Nested(BundleItemSchema, only=["bundle_item_id"], many=True))


class OverlapSchema(Schema):
    bundle_item = fields.Nested(BundleItemSchema, only=["bundle_item_id"])
    overlapping_items = fields.Nested(BundleItemSchema, only=["bundle_item_id"], many=True)


class RootSchema(Schema):
    courses = fields.Nested(CourseSchema, many=True)
    bundle_items = fields.Nested(BundleItemSchema, many=True)
    students = fields.Nested(StudentSchema, many=True)
    overlaps = fields.Nested(OverlapSchema, many=True)

    @post_load
    def make_objects(self, data, **kwargs):
        courses = [bundle_allocation.Course(c['course_id'], c['course_name'], c['capacity']) for c in data['courses']]
        bundle_items = [bundle_allocation.BundleItem(b['bundle_item_id'],
                                                     b['sum_capacity'],
                                                     b['ranking_group'],
                                                     [c2 for c1 in b['courses'] for c2 in courses if
                                                      c1['course_id'] == c2.course_id])
                        for b in data['bundle_items']]
        students = []
        for s in data['students']:
            student_id = s['student_id']
            rankings = {group: [b2 for b1 in s['rankings'][group] for b2 in bundle_items
                                if b1['bundle_item_id'] == b2.bundle_item_id]
                        for group in s['rankings']}
            students.append(bundle_allocation.Student(student_id, rankings))
        overlaps = {}
        for o in data['overlaps']:
            key = next((b for b in bundle_items if b.bundle_item_id == o['bundle_item']['bundle_item_id']))
            value = [b2 for b1 in o['overlapping_items'] for b2 in bundle_items
                     if b1['bundle_item_id'] == b2.bundle_item_id]
            overlaps[key] = value
        return courses, bundle_items, students, overlaps


class TestSchema(Schema):
    bundle_items = fields.Nested(BundleItemSchema, many=True)
