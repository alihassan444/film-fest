# Hostinger automatic deployment

Pushes to `main` and manual workflow runs test the application, build Vite assets,
and deploy over SSH only when `HOSTINGER_DEPLOY_ENABLED` is `true`. Pull requests
run checks without deploying. Deployment preserves `.env`, storage, uploaded
tickets and QR codes. Builds include production Composer dependencies, so the
hosting server does not need Node or Composer. Use PHP 8.3 for both web and SSH.

## One-time hosting setup

1. Enable SSH in hPanel and record its host, port and username. Enable PHP 8.3
   and Laravel's required extensions (including PDO MySQL, mbstring and GD).
2. Choose a dedicated application folder outside the public web root, for example
   `/home/u123456789/apps/film-fest`. Create it using SSH or File Manager.
3. Point the production subdomain's document root to that folder's `public`
   directory. Only `public` may be web accessible. If hPanel cannot set that
   document root, resolve the layout with Hostinger support before enabling CI/CD.
4. Create the MySQL database and user. Put a production `.env` in the application
   folder, starting from `.env.example`, with `APP_ENV=production`,
   `APP_DEBUG=false`, `APP_URL=https://your-subdomain`, `DB_CONNECTION=mysql`,
   `DB_HOST`, `DB_PORT=3306`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and real
   SMTP settings. Generate an APP_KEY once and keep it stable across deployments.
   For example, locally: `php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"`.
   Do not upload your local `.env` or commit production credentials.
5. Use `QUEUE_CONNECTION=sync` initially, or configure a supervised queue worker /
   Hostinger cron job for database queues. Restarting queues alone does not start
   workers. Configure HTTPS for the subdomain.
6. Generate a dedicated deploy key with `ssh-keygen -t ed25519 -f hostinger-deploy`
   on a trusted machine. Add its public `.pub` key to the hosting user's
   `~/.ssh/authorized_keys`. Put its private key only in GitHub Actions secrets.
   Keep both generated key files outside the project folder.

## GitHub configuration

Open repository **Settings → Secrets and variables → Actions**.

| Repository secret | Value |
| --- | --- |
| `HOSTINGER_SSH_HOST` | SSH host/IP from hPanel |
| `HOSTINGER_SSH_PORT` | Actual SSH port from hPanel |
| `HOSTINGER_SSH_USER` | Hosting SSH username |
| `HOSTINGER_SSH_KEY` | Entire dedicated private key, including header/footer |
| `HOSTINGER_KNOWN_HOSTS` | Verified SSH known_hosts entry |

Obtain a candidate known_hosts entry with
`ssh-keyscan -p PORT HOST`. Verify its fingerprint against hPanel's SSH Access
fingerprints before saving it. Do not accept unverified host keys in CI.

| Repository variable | Value |
| --- | --- |
| `HOSTINGER_APP_PATH` | Absolute dedicated application folder under `/home/` |
| `HOSTINGER_PHP_BIN` | `php`, or the absolute PHP 8.3 executable path |
| `HOSTINGER_HEALTH_URL` | `https://your-subdomain/up` |
| `HOSTINGER_DEPLOY_ENABLED` | Set `true` only after hosting and secrets are ready |

Then open **Actions → Test and deploy to Hostinger → Run workflow → main**.
Subsequent pushes to `main` deploy automatically after checks pass.

## Operations

Back up the production database and files before the first deployment and before
schema changes. Migrations run with `--force`; the workflow never seeds the
database or creates the example administrator. Create your production admin
separately with a strong password.

Updates use maintenance mode during extraction and migrations. This is an
in-place deployment with brief downtime, not an atomic release or automatic
rollback. Removed source files are retained on the server and require deliberate
cleanup. A failed update keeps maintenance mode enabled: inspect the workflow,
repair/redeploy the code, then run `php artisan up` in the application folder
only when healthy. Restore database backups if a migration needs recovery.
The `/up` check confirms Laravel boots; also check login, database access,
ticket uploads and real email delivery after the first deployment.
