from models import AuthToken
from werkzeug.exceptions import Unauthorized
from datetime import datetime


def verify_token(token):
    token = AuthToken.query.filter(AuthToken.token == token, AuthToken.expiration > datetime.utcnow()).first()
    if token is not None:
        return {'user': token.name, 'token_exp': token.expiration}
    else:
        raise Unauthorized