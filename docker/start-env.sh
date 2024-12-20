#!/bin/bash

docker-compose -f stack-mac.yml up

# To connect to image for debugging
#
# $ docker exec -it docker-joomladb-1 bash
# or
# $ docker exec -it docker-joomla-1 bash

# Debug:
# # Build the Image including xdebug
# $ docker build -t joomla-debug:3.10.11 .
#
# $ docker-compose -f stack-debug.yml up
#
