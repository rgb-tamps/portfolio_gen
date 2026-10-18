
#!/bin/sh
set -e

cd /var/www/html

# Use Railway's assigned port, with 8000 as the fallback.
PORT="${PORT:-8000}"

# Configure Apache to listen on the selected port.
sed -ri "s/^Listen [0-9]+$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri \
    "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" \
    /etc/apache2/sites-available/000-default.conf

# Clear cached Laravel configuration.
php artisan optimize:clear

# Apply database migrations.
php artisan migrate --force

# Validate Apache configuration before starting.
apache2ctl -t

exec apache2-foreground
