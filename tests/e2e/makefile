# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

bin: ## Runs all tests via bin/svrunit
	cd ../.. && php bin/svrunit test --configuration=./tests/e2e/svrunit.xml --list-suites
	cd ../.. && php bin/svrunit test --configuration=./tests/e2e/svrunit.xml --list-groups
	cd ../.. && php bin/svrunit test --configuration=./tests/e2e/svrunit.xml --report-junit --report-html

phar: ## Runs all tests with the PHAR
	php ../../build/svrunit.phar test --configuration=svrunit.xml --list-suites
	php ../../build/svrunit.phar test --configuration=svrunit.xml --list-groups
	php ../../build/svrunit.phar test --configuration=svrunit.xml --report-junit --report-html
