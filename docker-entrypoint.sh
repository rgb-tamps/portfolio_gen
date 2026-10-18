
#!/bin/sh
set -e

cd /var/www/html

# Force Apache to use only one MPM
for module in mpm_event mpm_worker mpm_prefork; do
    a2dismod "$module" 2>/dev/null || true
done

# Remove any remaining enabled MPM configuration links
rm -f /etc/apache2/mods-enabled/mpm_*.load
rm -f /etc/apache2/mods-enabled/mpm_*.conf

# Enable only prefork
a2enmod mpm_prefork

# Clear Laravel caches and run database migrations
php artisan optimize:clear
php artisan migrate --force

# Verify Apache configuration before starting
apache2ctl -t

exec apache2-foreground
