USER_ID := $(shell id -u)
GROUP_ID := $(shell id -g)
CONTAINER_NAME := php
DOCKER_COMPOSE := docker-compose
DOCKER_COMPOSE_RUN := $(DOCKER_COMPOSE) run --user=$(USER_ID) --rm --no-deps $(CONTAINER_NAME)

help: ## Show this help.
	@grep -F -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | grep -v '###'

build: ## Build containers
	@DOCKER_BUILDKIT=1 docker-compose build --pull --build-arg USER_ID=$(USER_ID) --build-arg GROUP_ID=$(GROUP_ID)

ssh: ## Log into php container
	@$(DOCKER_COMPOSE_RUN) fish

install: copy-env destroy build install-vendor ## install project

copy-env:
	@[ -f .env.local ] || cp .env.local-dist .env.local

install-vendor: ## install vendor
	@$(DOCKER_COMPOSE_RUN) composer install

destroy: ## Destroy containers
	$(DOCKER_COMPOSE) down -v --remove-orphans --rmi local
	rm -rf vendor var output

cc: phpstan ecs ## Check code

phpstan: ## Run PHPStan
	@echo "Running PHPStan"
	@$(DOCKER_COMPOSE_RUN) ./vendor/bin/phpstan

ecs: ## Run ECS
	@echo "Running ECS"
	@$(DOCKER_COMPOSE_RUN) ./vendor/bin/ecs

merge-view: merge view-last ## Merge and view last

merge: ## Playground
	@$(DOCKER_COMPOSE_RUN) bin/console app:merge

view-last: ## View last generation in imv
	@if [ -z "$(shell ls -Art ./output/merge/ | tail -n 1)" ]; then echo "No file to view"; exit 1; fi
	@if [ -z "$(shell command -v imv 2> /dev/null)" ]; then echo "Please install imv see https://sr.ht/~exec64/imv/"; exit 1; fi
	@imv -f ./output/merge/$(shell ls -Art ./output/merge/ | tail -n 1)

generate: ## Generate from 1 to 9999
	@$(DOCKER_COMPOSE_RUN) bin/console app:generate

fix-cs: ## Fix code style
	@$(DOCKER_COMPOSE_RUN) ./vendor/bin/ecs --fix
