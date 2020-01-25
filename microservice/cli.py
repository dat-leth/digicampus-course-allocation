import click
import datetime
import base64
import os

from flask import Blueprint
from models import AuthToken, Allocation
from config import db
from sqlalchemy import exc

bp_token = Blueprint('token', __name__)


@bp_token.cli.command('list')
def list_tokens():
    print('{:20}\t{:20}'.format('NAME', 'EXPIRATION'))
    for token in AuthToken.query.all():
        print(f'{token.name:20}\t{token.expiration}')
    print()


@bp_token.cli.command('create')
@click.argument('name')
@click.option('--expiry', default=180, show_default=True)
def create(name, expiry):
    """Create a token for authentication to REST API."""
    try:
        token = AuthToken(name=name,
                          token=str(base64.urlsafe_b64encode(os.urandom(18)), "utf-8"),
                          expiration=datetime.datetime.utcnow() + datetime.timedelta(days=expiry))
        db.session.add(token)
        db.session.commit()
        print(f'NAME:\t {token.name:}')
        print(f'TOKEN:\t {token.token}')
        print(f'EXPIRY:\t {token.expiration}')
    except exc.IntegrityError:
        print(f'ERROR: Couldn\'t create token. Token with name {name!r} already exists.')


@bp_token.cli.command('delete')
@click.argument('name')
def remove(name):
    AuthToken.query.filter_by(name=name).delete()
    db.session.commit()


@bp_token.cli.command('renew')
@click.argument('name')
@click.option('--expiry', default=180, show_default=True)
def renew(name, expiry):
    token = AuthToken.query.filter(AuthToken.name == name).first()
    if token is not None:
        token.expiration = datetime.datetime.utcnow() + datetime.timedelta(days=expiry)
        db.session.commit()
        print(f'Token {name!r} is renewed until {token.expiration}')
    else:
        print(f'Token {name!r} not found.')


bp_cleanup = Blueprint('cleanup', __name__, cli_group=None)


@bp_cleanup.cli.command('cleanup')
def cleanup():
    """Removing old allocation data from SQLite database."""
    Allocation.query.filter(Allocation.timestamp > datetime.datetime.utcnow() + datetime.timedelta(hours=48)).delete()
    db.session.commit()


bp_db = Blueprint('init-database', __name__, cli_group=None)


@bp_db.cli.command('init-database')
def init():
    # Delete database file if it exists currently
    if os.path.exists("database.sqlite"):
        os.remove("database.sqlite")
    db.create_all()
    db.session.commit()
