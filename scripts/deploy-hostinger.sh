#!/usr/bin/env bash
set -euo pipefail

app_path="${1:?Application directory is required}"
php_bin="${2:?PHP executable is required}"
archive="${3:?Build archive is required}"
public_path="${4:?Public document root is required}"

[[ "$app_path" =~ ^/home/[a-zA-Z0-9_./-]+$ ]] && [[ "$app_path" != *..* ]]
[[ "$archive" =~ ^/tmp/film-fest-[0-9]+-[0-9]+\.tar\.gz$ ]]
[[ "$public_path" =~ ^/home/[a-zA-Z0-9_./-]+$ ]] && [[ "$public_path" != *..* ]]
[[ "$public_path" == /home/*/domains/*/public_html/* ]]
[[ "$app_path" != *'/public_html'* ]]
[[ "$app_path" != "$public_path" && "$public_path" != "$app_path/"* ]]
cd "$app_path"
test -f .env || { echo 'Create the production .env before deploying.' >&2; exit 1; }
"$php_bin" -r 'exit(PHP_VERSION_ID >= 80300 ? 0 : 1);'
command -v flock >/dev/null
exec 9>.deploy.lock
flock -w 120 9

mkdir -p storage/app/private storage/app/public storage/logs \
  storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
  "$public_path/tickets" "$public_path/qr-codes"
chmod -R u+rwX storage bootstrap/cache "$public_path/tickets" "$public_path/qr-codes"

# Keep maintenance mode on after a failed update; do not serve a partial release.
if [[ -f artisan ]]; then
  "$php_bin" artisan down --retry=60
else
  printf '%s\n' '{"retry":60}' > storage/framework/down
fi
trap 'echo "Deployment failed. Maintenance mode remains enabled; inspect the error before running artisan up." >&2' ERR

# No deletion: production .env, uploaded tickets, QR codes and storage stay intact.
tar -xzf "$archive" -C "$app_path"
printf "<?php\nreturn '%s';\n" "$public_path" > bootstrap/public-path.php
# Copy only public assets, preserving production uploads and storage links.
tar -C "$app_path/public" --exclude=./index.php --exclude=./tickets --exclude=./qr-codes --exclude=./storage -cf - . | tar -C "$public_path" -xf -
# Use a deployment-specific entry point; the repository entry point stays portable.
cat > "$public_path/index.php" <<PHP
<?php
use Illuminate\\Http\\Request;
define('LARAVEL_START', microtime(true));
if (file_exists(\$maintenance = '$app_path/storage/framework/maintenance.php')) {
    require \$maintenance;
}
require '$app_path/vendor/autoload.php';
\$app = require_once '$app_path/bootstrap/app.php';
\$app->handleRequest(Request::capture());
PHP
"$php_bin" -l "$public_path/index.php"
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
