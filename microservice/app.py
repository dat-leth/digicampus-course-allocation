from flask import redirect
import config
import cli

connex = config.connex
connex.add_api('bundle-allocation_openapi.yaml')

application = connex.app
application.register_blueprint(cli.bp_cleanup)
application.register_blueprint(cli.bp_token)


# Create a URL route in our application for "/"
@connex.route("/")
def home():
    return redirect("/ui")


if __name__ == "__main__":
    connex.run()
