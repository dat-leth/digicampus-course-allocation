from config import db
from models import Allocation
import os


# Delete database file if it exists currently
if os.path.exists("database.sqlite"):
    os.remove("database.sqlite")
db.create_all()
db.session.commit()
