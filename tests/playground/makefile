# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

test: ## Runs all tests
	php ../../build/svrunit.phar test --configuration=svrunit.xml --report-junit --report-html --exclude-group=group1,group2

report: ## Shows the last report
	@npm install -g junit-cli-report-viewer
	@junit-cli-report-viewer .reports/report.xml