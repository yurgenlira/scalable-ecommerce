.DEFAULT_GOAL := help
.PHONY: help lint analyse test check up down plan

RUN := cd app &&
TF  := terraform -chdir=extra/level-1/infra/envs/prod

help: ## Show this help
	@awk 'BEGIN {FS = ":.*##"} /^##@/ {printf "\n\033[1m%s\033[0m\n", substr($$0, 5)} /^[a-zA-Z_-]+:.*##/ {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

##@ Quality
lint: ## Check code style with Pint
	$(RUN) ./vendor/bin/pint --test

analyse: ## Run static analysis with Larastan
	$(RUN) ./vendor/bin/phpstan analyse

test: ## Run the test suite with Pest
	$(RUN) ./vendor/bin/pest

check: lint analyse test ## Run the full quality gate

##@ Infrastructure
up: ## Provision the ephemeral infrastructure
	$(TF) init && $(TF) apply -auto-approve

down: ## Destroy the ephemeral infrastructure
	$(TF) destroy -auto-approve

plan: ## Preview infrastructure changes
	$(TF) init && $(TF) plan