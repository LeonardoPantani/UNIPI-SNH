set dotenv-load

alias rs := restart
alias w  := watch

_default:
	@just --list --unsorted

[private]
run:
	@just --choose --unsorted

# run containers
[positional-arguments]
up *args='b d':
	#!/bin/bash
	if [ $(docker compose ps -q | wc -l) -eq 0 ]; then
		echo "Starting containers..."
		
		# convert 'b d' into 'b' 'd'
		set -- $@

		args=""
		while [ -n "$1" ]; do
			case "$1" in 
				b)
					args="$args --build"
					;;

				d)
					args="$args --detach"
					;;

				w)
					args="$args --watch"
					;;

				-)
					:
					;;

				*)
					echo "Error: unknown arg $1"
					exit 1
					;;

			esac
			shift
		done

		docker compose up $args
	else
		echo "Containers are already running..."
	fi

# kill containers
[positional-arguments]
down *args='':
	#!/bin/bash
	if [ $(docker compose ps -aq | wc -l) -ne 0 ]; then
		echo "Removing containers..."
		
		args=""
		while [ -n "$1" ]; do
			case "$1" in 
				v)
					args="$args --volumes"
					;;

				-)
					:
					;;

				*)
					echo "Error: unknown arg $1"
					exit 1
					;;

			esac
			shift
		done

		docker compose down $args
	else
		echo "There are no containers..."
	fi

# kill, re-build and run containers removing volumes
[positional-arguments]
restart *args='':
	#!/bin/bash

	up_args=""
	down_args=""
	while [ -n "$1" ]; do
		case "$1" in 
			b)
				up_args="$up_args b"
				;;

			d)
				up_args="$up_args d"
				;;

			w)
				up_args="$up_args w"
				;;

			v)
				down_args="$down_args v"
				;;

			-)
				up_args="-"
				down_args="-"
				;;

			*)
				echo "Error: unknown arg $1"
				exit 1
				;;

		esac
		shift
	done

	just down ${down_args} && just up ${up_args}

# resync container files automatically when they are updated
@watch:
	docker compose watch --no-up

# start containers (if they're stopped)
start:
	#!/bin/bash
	if [ $(docker compose ps -q --status exited | wc -l) -ne 0 ]; then
		echo "Starting containers..."
		docker compose start
	else
		echo "Containers are already running..."
	fi

# stop containers (if they're running)
stop:
	#!/bin/bash
	if [ $(docker compose ps -q | wc -l) -ne 0 ]; then
		echo "Stopping containers..."
		docker compose stop
	else
		echo "Containers are stopped..."
	fi

# show running/stopped containers info
[positional-arguments]
ps *args='':
	#!/bin/bash
	if $(systemctl --quiet is-active docker); then
		docker=False
		while [ -n "$1" ]; do
			case "$1" in 
				d)
					docker=True
					;;

				*)
					echo "Error: unknown arg $1"
					exit 1
					;;

			esac
			shift
		done

		[ $docker == True ] \
			&& docker ps -a  \
			|| docker compose ps -a
	else
		echo "Docker deamon is stopped."
	fi

# print service logs
logs: 
	#!/bin/bash
	if ! command -v jq &>/dev/null; then
	    echo "Error: jq not found"
		exit 0
	fi

	# NOTE: docker exec wants 'container name', whereas docker compose wants 'service name' 
	containers=$(docker compose ps -a --format json \
		| jq -rs 'map(.Service) | @sh // empty' \
		| tr -d \')
	
	if [ -z "$containers" ]; then
		echo "Error: no container available"
		exit 1
	fi

	if [ -z "${EXEC_RECIPE_CHOOSER}" ]; then
		select container_name in $containers;
		do
			if [ $REPLY -ge 1 ] && [ $REPLY -le $(echo $containers | wc -w) ]; then
				break
			fi
		done
	else
		container_name=$(echo -n "$containers" \
			| tr ' ' '\n' \
			| bash -c "${EXEC_RECIPE_CHOOSER}")
	fi
	
	if [ -z "${container_name}" ]; then
		echo "Error: no container selected"
		exit 1
	fi

	docker compose logs "${container_name}" | less

# run a command inside a container
[positional-arguments]
exec *args='': up
	#!/bin/bash

	if ! command -v jq &>/dev/null; then
	    echo "Error: jq not found"
		exit 0
	fi

	# NOTE: docker exec wants 'container name', whereas docker compose wants 'service name' 
	containers=$(docker compose ps --format json \
		| jq -rs 'map(.Name) | @sh // empty' \
		| tr -d \')
	
	if [ -z "$containers" ]; then
		echo "Error: no container available"
		exit 1
	fi

	if [ -z "${EXEC_RECIPE_CHOOSER}" ]; then
		select container_name in $containers;
		do
			if [ $REPLY -ge 1 ] && [ $REPLY -le $(echo $containers | wc -w) ]; then
				break
			fi
		done
	else
		container_name=$(echo -n "$containers" \
			| tr ' ' '\n' \
			| bash -c "${EXEC_RECIPE_CHOOSER}")
	fi
	
	if [ -z "${container_name}" ]; then
		echo "Error: no container selected"
		exit 1
	fi

	if [ -n "$1" ]; then 
		command="$@"
	else
		echo "Getting into ${container_name}..."

		case "${container_name#*_}" in
			app)
				docker exec -it "${container_name}" /bin/bash
				;;
			db)
				docker exec -it "${container_name}" /bin/bash -c 'mysql -u root -proot'
				;;
			*)
				docker exec -it "${container_name}" /bin/bash
				;;
		esac
	fi

# build images
build:
	docker compose build

# return compose.yml file in canonical form
config:
	docker compose config