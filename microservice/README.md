# Microservice for Bundle Allocation

Python microservice for generating an allocation of bundles of courses to students. Written in Python 3.7 using Connexion, Flask, SQLite and `bundle_allocation`.

Stud.IP<sup>[1](#myfootnote1)</sup> plugin ([Digicampus](https://digicampus.uni-augsburg.de) at University of Augsburg) interacts with this microservice in the following manner:

- Submitting a request by POSTing to /allocation with information about where to fetch student preferences, list of courses and overlaps as well as a callback URL. This job will be queued and a job ID will be returned.
- If a worker is free: worker will fetch relevant information from provided endpoint, calculate an allocation and transmit the resulting allocation to Stud.IP. The resulting allocation is also temporarily stored in the database for 48 hours.
- Job status can always be checked using provided ID.
- Within 48 hours result can be fetched from microservice given job ID or allocation ID.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

- Linux (untested on other operating systems)
- Gurobi 8.1 (or higher) including a license
- Python 3.7 (or higher given compatibility of Gurobi)
- Reverse proxy (nginx or Apache)
- TLS/SSL secured connection (important!)
- Poetry https://python-poetry.org/ (recommended!)

### Installing

- Setting up the virtualenv with necessary packages. Poetry will automatically setup a new virtual environment if not already running inside one as well as resolving dependencies and installing packages.
    ```
    $ poetry install
    ```
  
  Alternatively:
  ``` 
  $ virtualenv env -p python3
  $ source env/bin/activate
  $ pip install -r requirements.txt
  ```
- Manually install `gurobipy` since no PyPI package is provided.
    ```
    $ poetry shell # if using poetry otherwise skip
    $ python $GUROBI_HOME/setup.py install 
    ```
- Initialize database
    ``` 
    $ flask init-database
    ```


## Deployment

- Setup cronjob for automatically deleting old allocation data
    For example every 15 minutes:
    ``` 
    */15 * * * * env/bin/flask cleanup
    ```
- (Only for Digicampus) Set Private-Token in `.env` file for accessing Digicampus REST endpoints
    ``` 
    DC_PRIVATE_TOKEN=qwertyuiop123
    ```
- Create Bearer token (default expiry is 180 days)
    ```
    $ flask token create NAME_OF_TOKEN --expiry 180
    ```
- Serve WSGI applications via gunicorn
    ``` 
    gunicorn --reload --worker 4 app:application --bind 0.0.0.0:57887
    ```
- Configure reverse proxy with TLS/SSL!

## Footnotes
<a name="fn1">1</a>: Stud.IP is an open source student information system. https://www.studip.de