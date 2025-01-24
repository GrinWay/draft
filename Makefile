run:
	clear && symfony server:stop && docker compose stop && symfony server:start

stop:
	clear && symfony server:stop && docker compose stop

open:
	clear && symfony open:local

dump:
	clear && symfony console server:dump

cc:
	clear && symfony console cache:clear

build:
	clear && yarn encore production --progress

test:
	clear && php bin/phpunit --testsuite all

test_c:
	clear && php bin/phpunit --testsuite c
