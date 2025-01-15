## —— Info Command (help, logs, ...) ————————————————————————————————————————————————————————————————

# Prints the help summary for each .mk file
define print_summary
	echo "\n\033[1m$(1)\033[0m"
    egrep '^[a-zA-Z_-]+:.*?## .*$$' $(2) | awk 'BEGIN {FS = ":.*?## "}; {printf " \033[36m%-20s\033[0m %s\n", $$1, $$2}'
endef

help: ## Shows this help message
	@echo "\n  \033[4m${PN_DISPLAY} Makefile\033[0m\n\n\033[1mUsage:\033[0m\n\n  make \033[36mtarget\033[0m"
	@$(call print_summary,Info,.make/info.mk)
	@$(call print_summary,Global Docker command,.make/docker.mk)
	@$(call print_summary,Backend command,.make/back.mk)
	@$(call print_summary,Frontend command,.make/front.mk)
	@$(call print_summary,PostgreSQL command,.make/db.mk)

list: ## Shows the list of all running images (not only the docker-compose ones)
	@docker container ls --format 'table {{.ID}}\t{{.Image}}\t{{.Size}}\t{{.Status}}\t{{.Ports}}\t{{.Names}}'
	@echo