## —— Backend Command ————————————————————————————————————————————————————————————————

# —— General
php-sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

php-bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

phpunit: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--testsuite unit --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)

load-fixtures-test: ## Load new fixtures in the test database
	@$(SYMFONY) --env=test doctrine:fixtures:load

load-fixtures: ## Load new fixtures in the dev database
	@$(SYMFONY) doctrine:fixtures:load

cs-fix: ## Runs php-cs to fix Symfony Code Standards issues
	${PHP_CONT} vendor/bin/php-cs-fixer fix src --rules=@Symfony
	${PHP_CONT} vendor/bin/php-cs-fixer fix tests --rules=@Symfony

phpstan: ## Runs PHPStan Analysis
	${PHP_CONT} vendor/bin/phpstan analyse src

openapi: ## Generates OpenAPI documentation
	mkdir -p backend/docs/api
	${SYMFONY} nelmio:apidoc:dump --format=yaml > backend/docs/api/openapi.yaml

# logs: ## Display backend logs
# 	${PHP_CONT} tail -f /app/var/log/dev.log

# —— Composer
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

# —— Symfony
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf