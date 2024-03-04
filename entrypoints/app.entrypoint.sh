#!/usr/bin/env bash

set -e

# Run our defined exec if args empty
if [ -z "$1" ]; then
    role=${CONTAINER_ROLE:-app}
    env=${APP_ENV:-production}

    if [ "$env" != "local" ]; then
        echo "Caching configuration..."
        (cd /var/www/html && php artisan config:cache && php artisan event:cache && php artisan route:cache && php artisan view:cache)
    fi

    if [ "$role" = "app" ]; then

        echo "Running PHP-FPM..."
        exec php-fpm

    elif [ "$role" = "queue" ]; then

        echo "Running the queue..."
        exec php /var/www/html/artisan queue:work -vv --no-interaction --tries=3 --sleep=5 --timeout=300 --delay=10

    elif [ "$role" = "cron" ]; then

        echo "Running the cron..."
        while [ true ]
        do
          exec php /var/www/html/artisan schedule:run -vv --no-interaction
          sleep 60
        done

    else
        echo "Could not match the container role \"$role\""
        exit 1
    fi

else
    exec "$@"
fi
