#!/bin/sh

# Run database migrations in production
echo "Running database migrations..."
php artisan migrate --force

# Start Supervisor to run Nginx and PHP-FPM
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
