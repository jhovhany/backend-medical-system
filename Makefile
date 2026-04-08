.PHONY: up down build restart shell migrate seed fresh logs status

## Start all containers
up:
	docker-compose up -d

## Stop all containers
down:
	docker-compose down

## Build images
build:
	docker-compose build --no-cache

## Restart containers
restart:
	docker-compose down && docker-compose up -d

## Open shell in app container
shell:
	docker-compose exec app bash

## Run migrations
migrate:
	docker-compose exec app php artisan migrate

## Run seeders
seed:
	docker-compose exec app php artisan db:seed

## Fresh migration + seed
fresh:
	docker-compose exec app php artisan migrate:fresh --seed

## Show container logs
logs:
	docker-compose logs -f

## Show container status
status:
	docker-compose ps

## Generate app key
key:
	docker-compose exec app php artisan key:generate

## Generate JWT secret
jwt:
	docker-compose exec app php artisan jwt:secret

## Publish vendor configs
publish:
	docker-compose exec app php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
	docker-compose exec app php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

## Clear all caches
clear:
	docker-compose exec app php artisan optimize:clear

## Optimize for production
optimize:
	docker-compose exec app php artisan optimize
