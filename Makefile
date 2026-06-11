.DEFAULT_GOAL := help
.PHONY: help lint analyse test check infra-up infra-down plan cost-guard destroy-all demo up down build logs shell db

RUN := cd app &&
TF  := terraform -chdir=extra/level-1/infra/envs/prod
COST_GUARD := terraform -chdir=extra/level-1/ephemeral/cost-guard

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
infra-up: ## Provision the ephemeral infrastructure
	$(TF) init && $(TF) apply -auto-approve

infra-down: ## Destroy the ephemeral infrastructure
	$(TF) destroy -auto-approve

##@ FinOps
cost-guard: ## Create budget + auto-stop (needs var ALERT_EMAIL=my_email@domain.com )
	$(COST_GUARD) init -input=false
	$(COST_GUARD) apply -auto-approve \
	  -var="instance_id=$$($(TF) output -raw instance_id)" \
	  -var="alert_email=$(ALERT_EMAIL)"

destroy-all: ## Destroy all project resources and verify $0 cost (keeps bootstrap S3 state)
	bash extra/level-1/ephemeral/teardown/destroy-all.sh

##@ Demo
demo: ## Seed demo data on the running instance
	bash extra/level-1/ephemeral/demo-data/seed.sh

##@ Development
up: ## Start the local stack (app + db)
	docker compose up -d

down: ## Stop and remove the stack (keeps data)
	docker compose down

build: ## Build the local dev image
	docker compose build

logs: ## Tail the stack logs
	docker compose logs -f

shell: ## Open a shell in the app container
	docker compose exec app sh

db: ## Open a psql session on the database
	docker compose exec db psql -U app -d app