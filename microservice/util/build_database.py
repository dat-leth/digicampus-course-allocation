from config import db
from models import Allocation
import os


# Delete database file if it exists currently
if os.path.exists("people.db"):
    os.remove("people.db")
db.create_all()
db.session.commit()
