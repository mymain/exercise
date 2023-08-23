docker-container = exercise-php-fpm
default: help

help:
	@echo 'Please provide proper option for make: start|stop|install|bash|phpcs|phpcfb|cc|migrate'

start:
	docker-compose up -d --build

stop:
	docker-compose down

install:
	docker exec $(docker-container) php composer.phar install

bash:
	docker exec -it $(docker-container) bash

phpcs:
	docker exec -it $(docker-container) php vendor/bin/phpcs

test:
	docker exec -it $(docker-container) php bin/phpunit

phpcfb:
	docker exec -it $(docker-container) php vendor/bin/phpcbf

cc:
	docker exec -it $(docker-container) php bin/console c:c

migrate:
	docker exec -it $(docker-container) php bin/console doctrine:migrations:migrate --no-interaction