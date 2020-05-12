# Digicampus Bundle Allocation

This repository contains code in order to allow for allocating students to multiple courses in a non-overlapping and efficient way. It is implemented as part of a bachelor's thesis at the Faculty of Applied Computer Science, University of Augsburg.

- `bundle_allocation` is a Python 3.7+ library, which can be used to calculate an allocation with respect to the students' priorities of courses.
- `microservice` is a REST service, implemented using Flask and aforementioned library. Deployed as an endpoint for Digicampus to submit collected priorities from students and then fetch calculated allocation results.
- `plugin` is a Stud.IP plugin, written in PHP 7. It shall be installed in order to allow the configuration of rankable courses by teachers, submission of priorities by students and communication to microservice in order to get an allocation. 