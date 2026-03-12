DOCKER_COMPOSE = docker compose

.PHONY: help build up down restart ps \
        logs logs-app logs-admin logs-api logs-db \
        composer init migrate wait-db permissions setup \
        shell-app shell-admin shell-api shell-db \
        clean

# ─────────────────────────────────────────────────────────────────────────────
# Default target
# ─────────────────────────────────────────────────────────────────────────────

help: ## Show available commands
	@printf "\n\033[1mMaisLusitânia – Docker commands\033[0m\n\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-18s\033[0m %s\n", $$1, $$2}'
	@printf "\n"

# ─────────────────────────────────────────────────────────────────────────────
# Docker lifecycle
# ─────────────────────────────────────────────────────────────────────────────

build: ## Build all Docker images
	$(DOCKER_COMPOSE) build

up: ## Start all containers in the background
	$(DOCKER_COMPOSE) up -d

down: ## Stop and remove containers (keeps volumes)
	$(DOCKER_COMPOSE) down

restart: ## Restart all running containers
	$(DOCKER_COMPOSE) restart

ps: ## Show the status of all containers
	$(DOCKER_COMPOSE) ps

# ─────────────────────────────────────────────────────────────────────────────
# Logs
# ─────────────────────────────────────────────────────────────────────────────

logs: ## Follow logs of all containers
	$(DOCKER_COMPOSE) logs -f

logs-app: ## Follow logs of maislusitania-app (frontoffice)
	$(DOCKER_COMPOSE) logs -f maislusitania-app

logs-admin: ## Follow logs of maislusitania-admin (backoffice)
	$(DOCKER_COMPOSE) logs -f maislusitania-admin

logs-api: ## Follow logs of maislusitania-api
	$(DOCKER_COMPOSE) logs -f maislusitania-api

logs-db: ## Follow logs of maislusitania-db (MySQL)
	$(DOCKER_COMPOSE) logs -f maislusitania-db

# ─────────────────────────────────────────────────────────────────────────────
# Application setup
# ─────────────────────────────────────────────────────────────────────────────

composer: ## Install Composer dependencies (runs inside maislusitania-admin)
	$(DOCKER_COMPOSE) exec maislusitania-admin composer install --no-interaction --prefer-dist

init: ## Run Yii2 init script for Development environment
	$(DOCKER_COMPOSE) exec maislusitania-admin php /app/init --env=Development --overwrite=n
	$(DOCKER_COMPOSE) exec maislusitania-admin sed -i \
		-e 's|mysql:host=localhost|mysql:host=maislusitania-db|g' \
		-e "s|'username' => 'root'|'username' => 'yii2advanced'|g" \
		-e "s|'password' => ''|'password' => 'secret'|g" \
		/app/common/config/main-local.php

migrate: ## Run all pending database migrations
	$(DOCKER_COMPOSE) exec maislusitania-admin php /app/yii migrate --interactive=0

permissions: ## Fix write permissions on uploads, assets and runtime directories
	$(DOCKER_COMPOSE) exec maislusitania-admin chmod -R 777 \
		/app/frontend/web/uploads \
		/app/frontend/web/assets \
		/app/frontend/runtime \
		/app/backend/web/assets \
		/app/backend/runtime \
		/app/console/runtime

wait-db: ## Wait until MySQL is ready to accept connections
	@printf "Waiting for MySQL"
	@until $(DOCKER_COMPOSE) exec maislusitania-admin php -r "try { new PDO('mysql:host=maislusitania-db;dbname=yii2advanced', 'yii2advanced', 'secret'); exit(0); } catch(Exception) { exit(1); }" 2>/dev/null; do \
		printf '.'; \
		sleep 2; \
	done
	@printf " ready!\n"

# ─────────────────────────────────────────────────────────────────────────────
# First-time full setup  →  make setup
# ─────────────────────────────────────────────────────────────────────────────

setup: ## Full first-time setup: build → up → wait-db → composer → init → migrate → permissions
	@$(MAKE) build
	@$(MAKE) up
	@$(MAKE) wait-db
	@$(MAKE) composer
	@$(MAKE) init
	@$(MAKE) migrate
	@$(MAKE) permissions
	@printf "\n\033[32m✔ Setup complete!\033[0m\n"
	@printf "  Frontoffice  →  http://localhost:8080\n"
	@printf "  Backoffice   →  http://localhost:8081\n"
	@printf "  API          →  http://localhost:8082\n"
	@printf "  phpMyAdmin   →  http://localhost:8083\n\n"

# ─────────────────────────────────────────────────────────────────────────────
# Container shells
# ─────────────────────────────────────────────────────────────────────────────

shell-app: ## Open a shell in maislusitania-app (frontoffice)
	$(DOCKER_COMPOSE) exec maislusitania-app bash

shell-admin: ## Open a shell in maislusitania-admin (backoffice)
	$(DOCKER_COMPOSE) exec maislusitania-admin bash

shell-api: ## Open a shell in maislusitania-api
	$(DOCKER_COMPOSE) exec maislusitania-api bash

shell-db: ## Open a shell in maislusitania-db (MySQL)
	$(DOCKER_COMPOSE) exec maislusitania-db bash

# ─────────────────────────────────────────────────────────────────────────────
# Cleanup
# ─────────────────────────────────────────────────────────────────────────────

clean: ## Stop containers, remove volumes and locally built images
	$(DOCKER_COMPOSE) down -v --rmi local
