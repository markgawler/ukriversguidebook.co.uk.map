#!/bin/bash

docker-compose -f stack.yml up

# To connect to image for debuging
#
# $ docker exec -it docker_joomladb_1 bash
# or
# $ docker exec -it docker_joomla_1 bash

# Debug:
# # Build the Image including xdebug
# $ docker build -t joomla-debug:3.10.11 .
#
# $ docker-compose -f stack-debug.yml up
#
