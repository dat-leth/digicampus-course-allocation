import connexion
import os

from flask_sqlalchemy import SQLAlchemy
from flask_marshmallow import Marshmallow
from flask_rq2 import RQ

from dotenv import load_dotenv

load_dotenv()

basedir = os.path.abspath(os.path.dirname(__file__))
connex = connexion.FlaskApp(__name__, specification_dir=os.path.join(basedir, "specification/"))
app = connex.app

# Build the Sqlite ULR for SqlAlchemy
sqlite_url = "sqlite:////" + os.path.join(basedir, "database.sqlite")

# Configure the SqlAlchemy part of the app instance
app.config["SQLALCHEMY_DATABASE_URI"] = sqlite_url
app.config["SQLALCHEMY_TRACK_MODIFICATIONS"] = False

# Configure Redis
app.config['RQ_REDIS_URL'] = 'redis://localhost:6379/0'

rq = RQ(app)
db = SQLAlchemy(app)
ma = Marshmallow(app)

