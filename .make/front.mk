## —— Frontend Command ————————————————————————————————————————————————————————————————

# —— General
front-sh: ## Connect to the frontend container
	@$(FRONT_CONT) sh

front-bash: ## Connect to the frontend container via bash so up and down arrows go to previous commands
	@$(FRONT_CONT) bash

lint: ## Run ESLint
	@$(NPM) run lint

type-check: ## Run TypeScript Compiler
	@$(NPM) run type-check

prettier: ## Start formatting with prettier
	@$(NPX) prettier . --write
