#!/usr/bin/env bash
set -euo pipefail

app_path="${1:?Application directory is required}"
php_bin="${2:?PHP executable is required}"
archive="${3:?Build archive is required}"

[[ "$app_path" =~ ^/home/[a-zA-Z0-9_./-]+$ ]] && [[ "$app_path" != *..* ]]
[[ "$archive" =~ ^/tmp/film-fest-[0-9]+-[0-9]+\.tar\.gz$ ]]
cd "$app_path"
test -f .env || { echo 'Create the production .env before deploying.' >&2; exit 1; }
"$php_bin" -r 'exit(PHP_VERSION_ID >= 80300 ? 0 : 1);'
command -v flock >/dev/null
exec 9>.deploy.lock
flock -w 120 9

mkdir -p storage/app/private storage/app/public storage/logs \
  storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
  public/tickets public/qr-codes
chmod -R u+rwX storage bootstrap/cache public/tickets public/qr-codes

# Keep maintenance mode on after a failed update; do not serve a partial release.
if [[ -f artisan ]]; then
  "$php_bin" artisan down --retry=60
else
  printf '%s\n' '{"retry":60}' > storage/framework/down
fi
trap 'echo "Deployment failed. Maintenance mode remains enabled; inspect the error before running artisan up." >&2' ERR

# No deletion: production .env, uploaded tickets, QR codes and storage stay intact.
tar -xzf "$archive" -C "$app_path"
"$php_bin" artisan config:clear
"$php_bin" artisan package:discover --ansi
"$php_bin" vendor/composer/platform_check.php
"$php_bin" artisan migrate --force
"$php_bin" artisan storage:link
"$php_bin" artisan config:cache
"$php_bin" artisan view:cache
"$php_bin" artisan queue:restart
"$php_bin" artisan up
rm -f -- "$archive"
