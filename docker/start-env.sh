#!/bin/bash

docker-compose -f stack2.yml up

# To connect to image for debuging
#
# $ docker exec -it docker_joomladb_1 bash
# or
# $ docker exec -it docker_joomla_1 bash