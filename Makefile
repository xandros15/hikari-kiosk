DOCKER_IMAGE_NAME=ghcr.io/xandros15/hikari-kiosk
UID := $(shell id -u)
GID := $(shell id -g)
include .env

default:
	docker compose exec php sh
start:
	docker compose up -d --remove-orphans
start-prod:
	docker compose up -d --remove-orphans --pull always
build:
	docker build -t ${DOCKER_IMAGE_NAME}-php:latest --target php .
	docker build -t ${DOCKER_IMAGE_NAME}-www-frontend:latest --target www-frontend .
	docker build -t ${DOCKER_IMAGE_NAME}-www-backend:latest --target www-backend .
	docker build -t ${DOCKER_IMAGE_NAME}-www-proxy:latest --target www-proxy .
	docker build -t ${DOCKER_IMAGE_NAME}-cron:latest --target cron .

push:
	docker push ${DOCKER_IMAGE_NAME}-php:latest
	docker push ${DOCKER_IMAGE_NAME}-www-frontend:latest
	docker push ${DOCKER_IMAGE_NAME}-www-backend:latest
	docker push ${DOCKER_IMAGE_NAME}-www-proxy:latest
	docker push ${DOCKER_IMAGE_NAME}-cron:latest

pull:
	docker pull ${DOCKER_IMAGE_NAME}-php:latest
	docker pull ${DOCKER_IMAGE_NAME}-www-frontend:latest
	docker pull ${DOCKER_IMAGE_NAME}-www-backend:latest
	docker pull ${DOCKER_IMAGE_NAME}-www-proxy:latest
	docker pull ${DOCKER_IMAGE_NAME}-cron:latest

stop:
	docker compose down
log:
	docker compose logs -f php
fetch:
	docker compose exec -e "SOURCE_API_ENDPOINT=${SOURCE_API_ENDPOINT}" php /app/bin/fetch.sh
import:
	docker compose exec php php /app/bin/import.php
