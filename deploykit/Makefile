hosts=../settings/hosts

env ?= dev

settings = ../settings/$(env).yml

ROLES_PATH=$(shell pwd)/roles

flags_default=
flags=$(flags_default)

env_set=export ANSIBLE_ROLES_PATH=$(ROLES_PATH)

include ../settings/deploykit.conf

setup:
	$(env_set) && \
		ansible-playbook $(flags) -i $(hosts) $(sudo) -t setup $(settings)

deploy:
	$(env_set) && \
		ansible-playbook $(flags) -i $(hosts) $(sudo) -t "deploy,release" $(settings)

reprov:
	$(env_set) && \
		ansible-playbook $(flags) -i $(hosts) $(sudo) -t "settings,deploy,release" $(settings)

launch:
	$(env_set) && \
		ansible-playbook $(flags) -i $(hosts) $(sudo) -t launch $(settings)

revert:
	$(env_set) && \
		ansible-playbook $(flags) -i $(hosts) $(sudo) -t revert $(settings)

itall:
	$(env_set) && \
		ansible-playbook $(flags) -i $(hosts) $(sudo) -t "setup,deploy,release,launch" $(settings)
