## —— PostgreSQL Command ————————————————————————————————————————————————————————————————

# —— PostgreSQL
connection: ## Test your connection to the database
	@$(SYMFONY) dbal:run-sql -q "SELECT 1" && echo "OK" || echo "Connection is not working"

psql: ## Open psql terminal session
	@echo "Connexion à la base de données PostgreSQL..."
	@$(DB_CONT) psql -U ${POSTGRES_USER} -d ${POSTGRES_DB}