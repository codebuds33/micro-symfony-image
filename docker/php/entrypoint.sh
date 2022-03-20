#!/bin/sh
set -e

if [ "$1" = 'start' ];then

  	until bin/console doctrine:query:sql "select 1" >/dev/null 2>&1; do
  				(>&2 echo "Waiting for MySQL to be ready...")
  			sleep 1
  	done

  bin/console doctrine:database:create --if-not-exists -n
  bin/console doctrine:migration:migrate -n

  symfony serve --allow-http --no-tls --port=8000
else
    /usr/local/bin/docker-php-entrypoint "$@"
fi
