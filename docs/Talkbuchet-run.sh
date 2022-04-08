#!/usr/bin/env bash

# @copyright Copyright (c) 2022, Daniel Calviño Sánchez (danxuliu@gmail.com)
#
# @license GNU AGPL version 3 or any later version
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

# TODO
#
# To perform its job, the script requires the "docker" command to be available.
#
# The Docker Command Line Interface (the "docker" command) requires special
# permissions to talk to the Docker daemon, and those permissions are typically
# available only to the root user. Please see the Docker documentation to find
# out how to give access to a regular user to the Docker daemon:
# https://docs.docker.com/engine/installation/linux/linux-postinstall/
#
# Note, however, that being able to communicate with the Docker daemon is the
# same as being able to get root privileges for the system. Therefore, you must
# give access to the Docker daemon (and thus run this script as) ONLY to trusted
# and secure users:
# https://docs.docker.com/engine/security/security/#docker-daemon-attack-surface

# Sets the variables that abstract the differences in command names and options
# between operating systems.
#
# Switches between timeout on GNU/Linux and gtimeout on macOS (same for mktemp
# and gmktemp).
function setOperatingSystemAbstractionVariables() {
	case "$OSTYPE" in
		darwin*)
			if [ "$(which gtimeout)" == "" ]; then
				echo "Please install coreutils (brew install coreutils)"
				exit 1
			fi

			MKTEMP=gmktemp
			TIMEOUT=gtimeout
			DOCKER_OPTIONS="-e no_proxy=localhost "
			;;
		linux*)
			MKTEMP=mktemp
			TIMEOUT=timeout
			DOCKER_OPTIONS=" "
			;;
		*)
			echo "Operating system ($OSTYPE) not supported"
			exit 1
			;;
	esac
}

# Launches the Selenium server in a Docker container.
#
# TODO
function prepareSelenium() {
	echo "Starting Selenium server"
	docker run --detach --name=$CONTAINER --publish $VNC_PORT:7900 $DOCKER_OPTIONS selenium/standalone-chrome

	echo "Waiting for Selenium server to be ready"
	if ! $TIMEOUT 10s bash -c "while ! curl 127.0.0.1:$VNC_PORT >/dev/null 2>&1; do sleep 1; done"; then
		echo "Could not start Selenium server; running" \
		     "\"docker run --rm --publish $VNC_PORT:7900 $DOCKER_OPTIONS selenium/standalone-chrome\"" \
		     "could give you a hint of the problem"

		exit 1
	fi
}

# Installs the required Python modules and Talkbuchet in the Selenium container.
function prepareTalkbuchet() {
	echo "Installing python3-selenium"
	docker exec --user root $CONTAINER bash -c "apt-get update && apt-get install --assume-yes python3-pip && pip install selenium"

	echo "Copying Talkbuchet to the container"
	docker cp Talkbuchet.js $CONTAINER:/tmp/
	docker cp Talkbuchet-cli.py $CONTAINER:/tmp/
}

# Exit immediately on errors.
set -o errexit

# Ensure working directory is script directory, as some actions (like copying
# Talkbuchet to the container) expect that.
cd "$(dirname $0)"

CONTAINER="talkbuchet-selenium"
if [ "$1" = "--container" ]; then
	CONTAINER="$2"

	shift 2
fi

VNC_PORT="7900"
if [ "$1" = "--vnc-port" ]; then
	VNC_PORT="$2"

	shift 2
fi

setOperatingSystemAbstractionVariables

# If the container is not found a new one is prepared. Otherwise the existing
# container is used.
#
# The name filter must be specified as "^/XXX$" to get an exact match; using
# just "XXX" would match every name that contained "XXX".
if [ -z "$(docker ps --all --quiet --filter name="^/$CONTAINER$")" ]; then
	prepareSelenium
	prepareTalkbuchet
fi

echo "Starting Talkbuchet CLI"
docker exec --tty --interactive --workdir /tmp $CONTAINER python3 -i /tmp/Talkbuchet-cli.py
