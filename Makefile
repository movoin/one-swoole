ARGS = $(filter-out $@,$(MAKECMDGOALS))
MAKEFLAGS += --silent

#############################
# Docker machine states
#############################

build:
	docker build -t onelab/one_swoole $$(pwd)/

up:	clean_runtime run

run:
	docker run -it -d --name one_swoole -p 9501:9501 -v $$(pwd):/app onelab/one_swoole

down:
	docker stop one_swoole && docker rm one_swoole

start:
	docker start one_swoole

stop:
	docker stop one_swoole

ssh:
	docker exec -it -u app one_swoole bash

root:
	docker exec -it one_swoole bash

tail:
	docker logs -f one_swoole

clean_ds:
	find . -name .DS_Store -print0 | xargs -0 rm -f

clean_runtime:
	rm -f $$(pwd)/example/runtime/**/*.log
	rm -f $$(pwd)/example/runtime/**/*.pid
	rm -f $$(pwd)/example/runtime/**/*.sock

#############################
# Argument fix workaround
#############################
%:
	@:
