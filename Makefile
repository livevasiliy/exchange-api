.PHONY: help start stop down install update migrate seed test logs lint

help:
	@echo "Available commands:"
	@echo "  make start       - Start Docker containers"
	@echo "  make stop        - Stop Docker containers"
	@echo "  make down        - Stop and remove Docker containers, networks, and volumes"
	@echo "  make install     - Install project dependencies"
	@echo "  make update      - Update project dependencies"
	@echo "  make migrate     - Run database migrations"
	@echo "  make seed        - Seed the database with sample data"
	@echo "  make test        - Run PHPUnit tests"
	@echo "  make logs        - View Docker container logs"
	@echo "  make lint        - Run Laravel lint Pint"

start:
	docker-compose up -d

stop:
	docker-compose stop

down:
	docker-compose down

install:
	docker-compose exec app composer install
	docker-compose exec app cp .env.example .env
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan generate:api-token

update:
	docker-compose exec app composer update

migrate:
	docker-compose exec app php artisan migrate

seed:
	docker-compose exec app php artisan db:seed

test:
	docker-compose exec app php artisan test

logs:
	docker-compose logs -f

lint:
	docker-compose exec app php vendor/bin/pint
