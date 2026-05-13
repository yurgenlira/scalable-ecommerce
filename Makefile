.PHONY: lint analyse test check help

RUN := cd app &&

lint: ## Check code style with Pint
	$(RUN) ./vendor/bin/pint --test

analyse: ## Run static analysis with Larastan
	$(RUN) ./vendor/bin/phpstan analyse

test: ## Run the test suite with Pest
	$(RUN) ./vendor/bin/pest

check: lint analyse test ## Run the full quality gate (lint + analyse + test)

help: ## Show available targets
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
	  awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}' | sort
