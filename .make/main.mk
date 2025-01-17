## —— Target the local dev environment ————————————————————————————————————————————————————————————————

# Project names for dev and test
PN_DISPLAY = VanScape

# Executables (local)
DOCKER_COMP = docker compose --env-file .env.local

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php
DB_CONT = $(DOCKER_COMP) exec database
FRONT_CONT= $(DOCKER_COMP) exec frontend

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console
NPM      = $(FRONT_CONT) npm

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

# Required makefiles
include .make/info.mk
include .make/docker.mk
include .make/back.mk
include .make/front.mk
include .make/db.mk