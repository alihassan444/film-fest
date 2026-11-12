# Film Fest: Hostinger production deployment guide

Target: https://film-fest.devoasis.io
Repository: https://github.com/alihassan444/film-fest

This guide configures the workflow already committed in `.github/workflows/deploy.yml`.
Run Windows commands in PowerShell on your computer. Run server commands only after
connecting through SSH. Values marked REPLACE_ME must be replaced before use.

## 1. Deployment details

| Setting | Value |
| --- | --- |
| Production hostname | film-fest.devoasis.io |
| SSH host | 145.223.89.102 |
| SSH port | 65002 |
| SSH username | u469521153 |
| Deployment branch | main |
| Proposed application folder | /home/u469521153/apps/film-fest |
| Confirmed document root | /home/u469521153/domains/devoasis.io/public_html/film-fest |
| Health endpoint | https://film-fest.devoasis.io/up |

The document root has been confirmed over SSH. The private application folder
will be created in section 3. No server login, database
setup, secret configuration, or live deployment has been performed by this guide.

## 2. Prepare hosting, DNS and HTTPS

1. Open hPanel and select the website that owns `film-fest.devoasis.io`.
2. Confirm the subdomain exists. Check its DNS records in the authoritative DNS
   provider. Use the website IP shown in hPanel; do not assume the SSH IP is also
   the website IP. Resolve conflicting records if present.
3. Enable SSL for the subdomain and verify that HTTPS has a valid certificate.
4. Select PHP 8.3 for this website/subdomain. Confirm the parent website is not
   unintentionally affected if the PHP setting is shared.
5. Enable Laravel's required extensions, including PDO MySQL, mbstring, OpenSSL,
   tokenizer, XML, ctype, fileinfo, curl, DOM and session. Enable GD for QR images.
6. Enable SSH access in hPanel. Record the server's host-key fingerprints.
7. Back up existing subdomain files and databases if this replaces an existing site.

Check DNS from Windows:

```powershell
Resolve-DnsName film-fest.devoasis.io
```

Connect from Windows:

```powershell
ssh -p 65002 u469521153@145.223.89.102
```

Compare the fingerprint shown on first connection with hPanel before accepting it.
Enter the hosting SSH password when prompted; it will not be displayed.

On the server, check:

```bash
pwd
php -v
php -m
command -v bash
command -v tar
command -v flock
```

The workflow uses PHP 8.3. The CLI and website PHP versions can differ. If `php`
uses another version, ask Hostinger for the PHP 8.3 CLI executable and put its
absolute path in `HOSTINGER_PHP_BIN`. Use that same executable for all PHP commands
below. Do not enable deployment until Bash, tar and flock are available.

## 3. Prepare the confirmed folder layout

Your existing subdomain document root is:

```text
/home/u469521153/domains/devoasis.io/public_html/film-fest
```

Keep that document root unchanged. On the server, create the separate private
application folder:

```bash
mkdir -p /home/u469521153/apps/film-fest
```

The deployment places Laravel source, vendor, storage and `.env` in this private
folder. It copies public assets and generates an `index.php` in your existing
subdomain folder that loads the private application. A server-only
`bootstrap/public-path.php` tells both web requests and Artisan where the real
public directory is, so uploads, QR codes and storage links use the right folder.

Back up any existing subdomain files. The workflow replaces matching public assets
and index.php, while preserving uploaded tickets, QR codes and storage links.
Do not upload the whole application or `.env` into the subdomain folder. If a
previous upload exposed private application files there, remove those private
copies deliberately after backing up; deployment does not delete stale files.

Avoid enabling Hostinger Git auto-deployment for the same directory: use the
GitHub Actions workflow as the single deployment mechanism.

## 4. Create the production database

In hPanel's MySQL database management:

1. Create a dedicated database and database user.
2. Assign the user access to that database.
3. Record the full database name and username, including Hostinger prefixes.
4. Record the database host shown by Hostinger and a strong password.

For a fresh installation, leave the database empty. The workflow runs migrations.
For existing local data, plan a database export/import and upload transfer before
cutover. Git deployment does not copy your local database or uploaded ticket files.
Back up any destination data before importing; never use `migrate:fresh` in production.

## 5. Create the production .env

Using hPanel File Manager, create this file (including the leading dot):

```text
/home/u469521153/apps/film-fest/.env
```

Use the template below. Replace every REPLACE_ME value. Quote passwords containing
spaces or special characters; follow dotenv quoting rules for literal quotes.
SMTP host, port and scheme must match your mail provider's instructions.

```dotenv
APP_NAME="Film Fest"
APP_ENV=production
APP_KEY=REPLACE_ME_WITH_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://film-fest.devoasis.io

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=REPLACE_ME_DATABASE_HOST
DB_PORT=3306
DB_DATABASE=REPLACE_ME_FULL_DATABASE_NAME
DB_USERNAME=REPLACE_ME_FULL_DATABASE_USERNAME
DB_PASSWORD="REPLACE_ME_DATABASE_PASSWORD"

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
BROADCAST_CONNECTION=log

MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=REPLACE_ME_SMTP_HOST
MAIL_PORT=587
MAIL_USERNAME=REPLACE_ME_SMTP_USERNAME
MAIL_PASSWORD="REPLACE_ME_SMTP_PASSWORD"
MAIL_FROM_ADDRESS=REPLACE_ME_VERIFIED_SENDER_EMAIL
MAIL_FROM_NAME="Film Fest"
MAIL_TIMEOUT=20

VITE_APP_NAME="Film Fest"
```

The SMTP example uses port 587 with `smtp`; a provider using implicit TLS typically
requires `MAIL_SCHEME=smtps` and port 465. Do not assume a mailbox or SMTP account
already exists. Configure the provider's SPF/DKIM DNS records and test delivery.

Generate APP_KEY once on the server:

```bash
php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'
```

Paste the complete generated value into APP_KEY. Keep this key stable on future
deployments. Keep a secure backup of the production `.env`; do not commit it.

On the server:

```bash
chmod 600 /home/u469521153/apps/film-fest/.env
```

`QUEUE_CONNECTION=sync` runs jobs during requests and needs no worker. If you later
use `database` queues, configure a working worker or cron job first. The workflow's
`queue:restart` command does not start a worker.

## 6. Create a dedicated deployment SSH key

On Windows PowerShell, outside the project, run:

```powershell
New-Item -ItemType Directory -Force -Path "$env:USERPROFILE\.ssh" | Out-Null
ssh-keygen -t ed25519 -C "film-fest-github-actions" -f "$env:USERPROFILE\.ssh\film-fest-hostinger"
```

Use an empty passphrase for this dedicated automation key: the current workflow
has no passphrase handling. If the named file already exists, choose a new name
rather than overwriting it. The resulting files are:

- `film-fest-hostinger.pub`: public key, added to Hostinger.
- `film-fest-hostinger`: private key, added only to GitHub Secrets.

Display the public key:

```powershell
Get-Content "$env:USERPROFILE\.ssh\film-fest-hostinger.pub"
```

Add that entire single line using hPanel's SSH public-key management. Alternatively,
on the server, preserve existing authorized keys and append the new public key:

```bash
mkdir -p ~/.ssh
chmod 700 ~/.ssh
touch ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
nano ~/.ssh/authorized_keys
```

Paste the public key as a new single line, then save and exit. Never paste the
private key into authorized_keys.

Test from Windows; this must succeed without the hosting account password:

```powershell
ssh -i "$env:USERPROFILE\.ssh\film-fest-hostinger" -p 65002 -o BatchMode=yes u469521153@145.223.89.102 "whoami"
```

Expected output: `u469521153`.

## 7. Verify the server host key

On Windows, collect an ED25519 candidate host-key entry:

```powershell
ssh-keyscan -t ed25519 -p 65002 145.223.89.102 | Set-Content -Encoding ascii "$env:TEMP\film-fest-known-hosts"
ssh-keygen -lf "$env:TEMP\film-fest-known-hosts"
Get-Content "$env:TEMP\film-fest-known-hosts"
```

Compare the fingerprint with the ED25519 fingerprint in hPanel. If it differs,
do not use the entry; resolve the mismatch with Hostinger. The scan alone does
not verify the server. After verification, save the actual host-key line in the
GitHub secret below. It begins with `[145.223.89.102]:65002 ssh-ed25519`.

## 8. Add GitHub Actions secrets and variables

Open https://github.com/alihassan444/film-fest/settings/secrets/actions

Select Settings > Secrets and variables > Actions. If these settings are not
available to your account, have the repository owner configure them.

Under Secrets, select New repository secret for each:

| Name | Value |
| --- | --- |
| HOSTINGER_SSH_HOST | 145.223.89.102 |
| HOSTINGER_SSH_PORT | 65002 |
| HOSTINGER_SSH_USER | u469521153 |
| HOSTINGER_SSH_KEY | Complete contents of the private deployment key |
| HOSTINGER_KNOWN_HOSTS | Verified host-key entry from section 7 |

Copy the private key to the Windows clipboard without printing it:

```powershell
Get-Content -Raw "$env:USERPROFILE\.ssh\film-fest-hostinger" | Set-Clipboard
```

Paste into HOSTINGER_SSH_KEY, including BEGIN/END lines and all newlines. Do not
paste it into chat, source files, issue comments, or workflow YAML.

Under Variables, select New repository variable for each:

| Name | Value |
| --- | --- |
| HOSTINGER_APP_PATH | /home/u469521153/apps/film-fest |
| HOSTINGER_PUBLIC_PATH | /home/u469521153/domains/devoasis.io/public_html/film-fest |
| HOSTINGER_PHP_BIN | php, if verified as PHP 8.3; otherwise the verified executable path |
| HOSTINGER_HEALTH_URL | https://film-fest.devoasis.io/up |
| HOSTINGER_DEPLOY_ENABLED | false initially |

Use repository variables/secrets, as named above. Do not configure them only in a
GitHub environment: this workflow does not select an environment.

## 9. Run the first deployment

Before enabling, confirm sections 2-8 are complete, the production database and
.env are ready, the document root is correct, and any existing production data
has been backed up.

1. Change HOSTINGER_DEPLOY_ENABLED to `true` in GitHub repository Variables.
2. Open https://github.com/alihassan444/film-fest/actions
3. Select **Test and deploy to Hostinger**.
4. Select **Run workflow**, choose **main**, and run it.
5. Open the run and watch both the `test` and `deploy` jobs.

The workflow installs development dependencies, runs tests against an isolated
SQLite database, builds frontend assets, and packages production dependencies.
It uploads the build over verified SSH, enters maintenance mode, extracts the
application, runs production migrations, links storage, caches configuration and
views, restarts existing queue workers, leaves maintenance mode, and checks `/up`.
The production host does not need Node or Composer for this workflow.

Deployments preserve the production `.env`, storage, uploaded tickets and QR
codes. The workflow never runs database seeders or creates an administrator.
Do not run `php artisan db:seed`: the current seeders include an example admin
password and an EventSeeder referencing a model absent from this repository.

## 10. Create your production administrator

After the first deployment, open https://film-fest.devoasis.io/register and create
your own account with a strong unique password. This creates a normal user.
Then connect to the server and open Tinker:

```bash
cd /home/u469521153/apps/film-fest
php artisan tinker
```

Inside Tinker, replace the email with the account you just registered:

```php
$user = App\Models\User::where('email', 'REPLACE_ME_YOUR_REGISTERED_EMAIL')->firstOrFail();
$user->is_admin = true;
$user->email_verified_at = now();
$user->save();
exit
```

Sign in at https://film-fest.devoasis.io/login and check the admin dashboard.
Use this one-time procedure instead of the example administrator seeder.

## 11. Verify the live application

- Open https://film-fest.devoasis.io/up and confirm HTTP 200.
- Open the homepage and confirm CSS, JavaScript, images and video load.
- Confirm login and admin authorization work.
- Submit a test ticket with a sample receipt, approve it, and check email delivery.
- Open the QR attachment/link and check it uses the production hostname.
- Test rejection email and attendance management.
- Confirm `/.env` and `/composer.json` return 403 or 404, never their contents.
- Confirm HTTP redirects to HTTPS and the certificate is valid.

The `/up` endpoint confirms Laravel boots; it does not fully validate database,
SMTP or ticket operations. Do not announce the site until these checks pass.

## 12. Future deployments

From Windows in your project folder:

```powershell
cd C:\wamp64\www\film-fest
git status
git add .
git commit -m "Describe your change"
git push origin main
```

Every push to main runs checks and then deploys when enabled. Pull requests run
checks without deploying. To pause future deployments, set
HOSTINGER_DEPLOY_ENABLED to `false`; this does not stop an already running job.
Changes to production `.env` require refreshing cached configuration on the server:

```bash
cd /home/u469521153/apps/film-fest
php artisan config:cache
php artisan queue:restart
```

## 13. Troubleshooting

| Symptom | What to check |
| --- | --- |
| Deploy job skipped | Branch must be main; HOSTINGER_DEPLOY_ENABLED must be the repository variable `true` |
| Permission denied (publickey) | Correct private key in GitHub, matching public key on Hostinger, key permissions, correct SSH user |
| Host key verification failed | Compare fingerprints with hPanel and update the verified known_hosts entry; keep strict checking enabled |
| Configure SSH step fails | All secrets present, numeric port, absolute /home/ app path, HTTPS health URL |
| SSH connection times out | SSH enabled, host/port correct, hosting firewall/restrictions checked with Hostinger |
| PHP/platform check fails | CLI PHP 8.3 and required extensions enabled; set HOSTINGER_PHP_BIN to the verified executable |
| flock not found | Ask Hostinger about flock availability; the current deployment script requires it |
| Database connection error | Full prefixed database name/user, assigned privileges, host and password in production .env |
| 403/404 or default page | Correct subdomain document root, DNS, public/index.php and public/.htaccess |
| 500 error | Inspect storage/logs/laravel.log privately and check PHP extensions, database and writable directories |
| 503 after deployment failure | Maintenance mode remains enabled; fix the failed step and redeploy before bringing the site up |
| CSS/JS missing | Build job passed, public/build/manifest.json exists, APP_URL and public document root correct |
| Emails missing | Real SMTP mailer, correct port/scheme, sender authentication and provider logs; log/array mailers do not deliver |
| Upload permission errors | Hosting user can write storage, bootstrap/cache, public/tickets and public/qr-codes |
| Health check fails after deploy | Check HTTPS, DNS, /up response, redirects and web PHP runtime; deployment has already changed files |

Useful server diagnostics (do not share logs containing personal data/secrets):

```bash
cd /home/u469521153/apps/film-fest
php artisan migrate:status
php artisan about
tail -n 80 storage/logs/laravel.log
```

After repairing a failed deployment, and only when the application is consistent:

```bash
php artisan config:cache
php artisan view:cache
php artisan up
```

Do not use chmod 777, enable public debug mode, regenerate a live APP_KEY, or run
migrate:fresh as a troubleshooting shortcut.

## 14. Backups and recovery

Enable Hostinger backups appropriate to your plan and take a database export
before schema-changing releases. Back up `.env`, the database, storage, uploaded
tickets and generated QR codes securely. Confirm how to restore them.

This is an in-place deployment with brief maintenance downtime. It does not
provide atomic releases or automatic rollback. Files removed from Git remain on
the server and need deliberate cleanup; never delete upload/storage directories.
A failure during extraction/migrations keeps maintenance mode enabled. A failure
of the later HTTP health check marks the run failed but does not automatically
return the application to maintenance or undo changes.

For an application-code regression, revert the offending commit in Git and push
the revert to main. This does not reverse database migrations. Assess schema
compatibility before deploying older code. For data/schema recovery, restore a
verified backup with a planned maintenance window rather than blindly running
migrate:rollback. Keep the production APP_KEY unchanged during recovery.

## References

- [Hostinger SSH access and server fingerprints](https://www.hostinger.com/support/1583645-how-to-enable-ssh-access-in-hostinger/)
- [Hostinger SSH key setup](https://www.hostinger.com/support/5634532-how-to-generate-ssh-keys-and-add-them-to-hostinger-dashboard/)
- [Laravel 12 deployment requirements and public web root](https://laravel.com/docs/12.x/deployment)
- [GitHub Actions repository secrets](https://docs.github.com/en/actions/how-tos/write-workflows/choose-what-workflows-do/use-secrets)
