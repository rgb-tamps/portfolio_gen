#!/bin/sh
set -eu

cd /var/www/html

port="${PORT:-8000}"
mods_enabled=/etc/apache2/mods-enabled
mods_available=/etc/apache2/mods-available

rm -f "$mods_enabled"/mpm_*.load "$mods_enabled"/mpm_*.conf
ln -s "$mods_available"/mpm_prefork.load "$mods_enabled"/mpm_prefork.load

if [ -f "$mods_available"/mpm_prefork.conf ]; then
    ln -s "$mods_available"/mpm_prefork.conf "$mods_enabled"/mpm_prefork.conf
fi

sed -i -E "s/^Listen [0-9]+$/Listen ${port}/" /etc/apache2/ports.conf
sed -i -E "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${port}>/" /etc/apache2/sites-available/000-default.conf

apache_modules="$(apache2ctl -M 2>&1)"
mpm_count="$(printf '%s\n' "$apache_modules" | grep -Ec '^[[:space:]]*mpm_[[:alnum:]_]+_module[[:space:]]')"

if [ "$mpm_count" -ne 1 ] || ! printf '%s\n' "$apache_modules" | grep -q 'mpm_prefork_module'; then
    printf '%s\n' "$apache_modules" >&2
    echo "Apache must load exactly one MPM: mpm_prefork_module." >&2
    exit 1
fi

apache2ctl -t

php artisan optimize:clear
su -s /bin/sh www-data -c 'php artisan migrate --force'

exec apache2-foreground
