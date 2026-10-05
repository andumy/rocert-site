SHL:=$(shell uname -s)
IS_MAC:=$(filter Darwin,$(SHL))
U_ID:=$(if $(IS_MAC),1000,$(shell id -u))
G_ID:=$(if $(IS_MAC),1000,$(shell id -g))
COMPOSE_DEV=HOST_UID=$(U_ID) HOST_GID=$(G_ID) docker compose -f docker-compose.dev.yml
COMPOSE_PROD=HOST_UID=$(U_ID) HOST_GID=$(G_ID) docker compose -f docker-compose.prod.yml
WP=docker exec -u rocert rocert-site-app wp

.PHONY: start start-prod env init init-prod provision seed reseed stop down bash sql logs wp export start-runner

## Local: build env, boot containers, install WordPress + theme + plugins, seed content on first run
start: env init provision

## Production server: .env is written by the deploy workflow (deploy/.env.static + Infisical prod) before this runs
start-prod: init-prod provision

env:
	cat deploy/.env.static deploy/.env.local > .env

init:
	$(COMPOSE_DEV) up -d --build

init-prod:
	$(COMPOSE_PROD) up -d --build --force-recreate

provision:
	docker exec -u rocert rocert-site-app bash /opt/rocert/deploy/provision.sh

## Seed content (only runs once per site unless forced)
seed:
	$(WP) eval-file /opt/rocert/content/seed.php

## Re-apply seed content over existing pages (overwrites edits made in wp-admin!)
reseed:
	$(WP) eval-file /opt/rocert/content/seed.php force
	$(WP) eval-file /opt/rocert/content/post-seed.php

stop:
	$(COMPOSE_DEV) stop

## Destroys the local database volume and the downloaded WordPress tree
down:
	$(COMPOSE_DEV) down -v
	rm -rf wordpress

bash:
	docker exec -u rocert -it rocert-site-app bash

sql:
	docker exec -it rocert-site-db mysql -urocert -procert rocert_site

logs:
	docker logs -f rocert-site-app

wp:
	$(WP) $(filter-out $@,$(MAKECMDGOALS))

## Self-hosted GitHub runner for the deploy workflow (label rocert-site), like the other projects
start-runner:
	../gh-runners/start.sh andumy/rocert-site 1 rocert-site

## Hand-over package for an FTP-only host: search-replaced SQL + full WP tree (see deploy/README.md)
export:
	bash deploy/export.sh

%:
	@:
