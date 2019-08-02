from flask import redirect
import config

connex = config.connex
connex.add_api('bundle-allocation_openapi.yaml')

# Create a URL route in our application for "/"
@connex.route("/")
def home():
    return redirect("/ui")


if __name__ == "__main__":
    connex.run()