# Makefile for dev and test environments
include .env
include .make/.env
export $(shell sed 's/=.*//' .make/.env)
include .make/main.mk

