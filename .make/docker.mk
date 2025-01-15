## —— Global Docker Command ————————————————————————————————————————————————————————————————

all: build up ## Build fresh images, create and start the containers

build: ## Builds the Docker images, pass the parameter "c=" to precise service to build, example: make build c="frontend"
	@$(eval c ?=)
	@$(DOCKER_COMP) build $(c) --pull --no-cache

up: ## Create and start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach
# @$(DOCKER_COMP) up --pull always -d --wait

start: ## Start containers
	@$(DOCKER_COMP) start

stop: ## Stop Dockers containers
	@$(DOCKER_COMP) stop

down: ## Stop ans remove the docker containers
	@$(DOCKER_COMP) down --remove-orphans