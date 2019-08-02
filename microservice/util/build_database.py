from config import db
from models import Allocation
import os


# Delete database file if it exists currently
if os.path.exists("allocations.db"):
    os.remove("allocations.db")
db.create_all()
db.session.commit()
