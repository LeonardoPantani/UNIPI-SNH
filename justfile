app_container_name := "php_app"
db_container_name  := "php_db"

default: up

on:
	#!/bin/bash
	if $(systemctl --quiet is-active docker); then
		echo "Docker deamon is already active..."
	else
		echo "Starting docker deamon..."
		sudo systemctl start docker
	fi

off: down
	#!/bin/bash
	read -p "Turn off docker deamon? [Y/n] " res 
	if [ -z "$res" ] || [ "$res" == "Y" ]; then
		sudo systemctl stop docker docker.socket
	fi

up: on
	#!/bin/bash
	if [ $(docker compose ps | wc -l) -ne 3 ]; then
		echo "Starting containers..."
		docker compose up -d
	else
		echo "Containers are already running..."
	fi

down:
	#!/bin/bash
	if [ $(docker compose ps | wc -l) -le 3 ]; then
		echo "Stopping containers..."
		docker compose down
	else
		echo "Containers are stopped..."
	fi

start: on
	#!/bin/bash
	if [ $(docker compose ps | wc -l) -ne 3 ]; then
		echo "Starting containers..."
		docker compose start
	else
		echo "Containers are already running..."
	fi

stop:
	#!/bin/bash
	if [ $(docker compose ps | wc -l) -eq 3 ]; then
		echo "Stopping containers..."
		docker compose stop
	else
		echo "Containers are stopped..."
	fi

ps:
	#!/bin/bash
	if $(systemctl --quiet is-active docker); then
		docker compose ps -a
	else
		echo "Docker deamon is stopped."
	fi

@app: up
	echo "Get into {{app_container_name}}..."
	docker exec -it {{app_container_name}} /bin/bash

@db: up
	echo "Get into {{db_container_name}}..."
	docker exec -it {{db_container_name}} /bin/bash

build: on
	docker compose build

config: on
	docker compose config

rm:
	docker container rm {{app_container_name}} {{db_container_name}}
