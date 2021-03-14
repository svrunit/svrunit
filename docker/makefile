# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#------------------------------------------------------------------------------------------------

build: ## Builds SVRUNIT and creates svrunit.phar
	@docker exec -it dev bash -c "cd /app && make build -B"

test: ## Tests SVRUNIT
	@docker exec -it dev bash -c "cd /app && make install -B && make test -B"
