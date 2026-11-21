.PHONY: help install build dev watch clean docker-up docker-down docker-logs test

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install PHP and Node dependencies
	composer install
	npm install

build: ## Build frontend assets for production
	npm run build

dev: ## Build frontend assets for development
	npm run dev

watch: ## Watch and rebuild frontend assets on changes
	npm run watch

clean: ## Clean generated files
	rm -rf vendor node_modules js/

docker-up: ## Start Docker containers
	docker-compose up -d

docker-down: ## Stop Docker containers
	docker-compose down

docker-logs: ## Show Docker container logs
	docker-compose logs -f nextcloud

docker-rebuild: ## Rebuild and restart containers
	docker-compose down
	docker-compose up -d --build

docker-shell: ## Open shell in Nextcloud container
	docker exec -it nextcloud-git-integration /bin/bash

enable-app: ## Enable the app in Nextcloud (requires running container)
	docker exec -u www-data nextcloud-git-integration php occ app:enable gitintegration

disable-app: ## Disable the app in Nextcloud
	docker exec -u www-data nextcloud-git-integration php occ app:disable gitintegration

test: ## Run PHP tests
	vendor/bin/phpunit --configuration phpunit.xml

lint: ## Run PHP linter
	composer lint

setup: install build docker-up ## Complete setup: install dependencies, build, and start Docker
	@echo "Waiting for Nextcloud to be ready..."
	@sleep 30
	@echo "Enabling Git Integration app..."
	@$(MAKE) enable-app
	@echo "Setup complete! Access Nextcloud at http://localhost:8080"
	@echo "Admin credentials: admin / admin"
	@echo "Adminer available at http://localhost:8081"
