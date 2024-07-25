import yaml


class Client:
    def __init__(self, config):
        self.config = config

    def run(self):
        if self.config.get("run_localhost"):
            print("Running in localhost mode")
        else:
            print("Running in production mode")


# Load configuration from a YAML file
with open("../fullstack-repo/config.yaml", "r") as file:
    config = yaml.safe_load(file)

client = Client(config)
client.run()
