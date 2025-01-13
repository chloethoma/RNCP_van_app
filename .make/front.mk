## —— Frontend Command ————————————————————————————————————————————————————————————————

# —— General
front-sh: ## Connect to the FrankenPHP container
	@$(FRONT_CONT) sh

front-bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(FRONT_CONT) bash