# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@echo ""
	@echo "PROJECT COMMANDS"
	@echo "--------------------------------------------------------------------------------------------"
	@printf "\033[33mInstallation:%-30s\033[0m %s\n"
	@grep -E '^[a-zA-Z_-]+:.*?##1 .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?##1 "}; {printf "\033[33m  - %-30s\033[0m %s\n", $$1, $$2}'
	@echo "--------------------------------------------------------------------------------------------"
	@printf "\033[36mDevelopment:%-30s\033[0m %s\n"
	@grep -E '^[a-zA-Z_-]+:.*?##2 .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?##2 "}; {printf "\033[36m  - %-30s\033[0m %s\n", $$1, $$2}'
	@echo "--------------------------------------------------------------------------------------------"
	@printf "\033[32mTests:%-30s\033[0m %s\n"
	@grep -E '^[a-zA-Z_-]+:.*?##3 .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?##3 "}; {printf "\033[32m  - %-30s\033[0m %s\n", $$1, $$2}'
	@echo "---------------------------------------------------------------------------------------------------------"
	@printf "\033[35mDevOps:%-30s\033[0m %s\n"
	@grep -E '^[a-zA-Z_-]+:.*?##4 .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?##4 "}; {printf "\033[35m  - %-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------
#// Install

install: ##1 Installs all prod dependencies
	composer install --no-dev

dev: ##1 Installs all dev dependencies
	composer install

#------------------------------------------------------------------------------------------------
#// Dev

build: ##2 Builds SVRUNIT and creates svrunit.phar
	@make install -B
	@echo "===================================================================="
	@echo "verifying if phar files can be created....phar.readonly has to be OFF"
	@php -i | grep phar.readonly
	@php -i | grep "Loaded Configuration"
	@cd scripts && php build.php

pr: ##2 Runs and prepares everything for a pull request
	php ./vendor/bin/php-cs-fixer fix --config=./.php_cs.php
	@make phpunit -B
	@make stan -B

#------------------------------------------------------------------------------------------------
#// Tests

csfix: ##3 Starts the PHP CS Fixer
	php ./vendor/bin/php-cs-fixer fix --config=./.php_cs.php --dry-run

stan: ##3 Starts the PHPStan Analyser
	@php ./vendor/bin/phpstan analyse -c ./.phpstan.neon

phpunit: ##3 Runs all tests
	@php ./vendor/bin/phpunit --configuration=./phpunit.xml

#------------------------------------------------------------------------------------------------
#// DevOps

release: ##4 Create a ZIP file in the build folder
	cd build && zip svrunit.zip svrunit.phar
	cd build && rm -rf svrunit.phar
