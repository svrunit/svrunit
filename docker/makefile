# Makefile  Project

.PHONY: help
.DEFAULT_GOAL := help


#------------------------------------------------------------------------------------------------

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

run: ## Installs all prod dependencies
	docker-compose up -d
	sleep 8
	docker exec -it svrunit bash -c 'cd /app && make dev'
	docker exec -it svrunit bash -c 'cd /app && make pr'
	docker exec -it svrunit bash
