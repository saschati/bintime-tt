# Makefile

# make commands be run with `bash` instead of the default `sh`
SHELL='/bin/bash'

# Setup —————————————————————————————————————————————————————————————————————————————————
.DEFAULT_GOAL := help

# .DEFAULT: If command does not exist in this makefile
# default:  If no command was specified:
.DEFAULT default:
	$(EXECUTOR)
	if [ "$@" != "" ]; then echo "Command '$@' not found."; fi;
	make help

## —— Project Make file  ————————————————————————————————————————————————————————————————

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Init project ——————————————————————————————————————————————————————————————————————
init: ## Project init
init: docker-down-clear docker-pull docker-build docker-up app-init

## —— Manage project ————————————————————————————————————————————————————————————————————
up: docker-up ## Project up
down: docker-down ## Project down
restart: down docker-build up ## Project restart

## —— Audit project —————————————————————————————————————————————————————————————————————
check: lint ## Project restart
lint: api-lint ## Run linters

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-stop:
	docker-compose stop

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

app-init: api-init

## —— API ———————————————————————————————————————————————————————————————————————————————
api-init: ## Init API
api-init: api-permissions api-composer-install api-wait-db api-migrations

api-permissions:
	-docker run --rm -v ${PWD}/api:/app -w /app alpine chmod -R 777 runtime

api-wait-db:
	docker-compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 30

api-composer-install: ## Composer install
	docker-compose run --rm api-php-cli composer install

api-migrations:  ## Run migrations
	docker-compose run --rm api-php-cli php yii migrate --interactive=0

api-docs: ## Build api docs
	docker-compose run --rm api-php-cli composer docs

api-logs: ## Show logs
	docker-compose logs --follow api

api-lint: ## Run lints
	docker-compose run --rm api-php-cli composer validate
	docker-compose run --rm api-php-cli composer php-cs-fixer fix -- --dry-run --diff

api-fix: ## Run lint fixer
	docker-compose run --rm api-php-cli composer php-cs-fixer fix
