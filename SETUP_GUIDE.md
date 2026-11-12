# Quick Setup Guide

## Prerequisites

1. PHP 8.2+ with extensions:
   - GD (for QR code generation)
   - PDO
   - OpenSSL
   - Mbstring
   - Tokenizer
   - XML
   - Ctype
   - JSON

2. Composer
3. Node.js & NPM (for frontend assets)

## Installation Steps

### 1. Install PHP Dependencies

```bash
composer install
```

**Note**: If you get an error about missing `ext-gd`, install it:
- **Ubuntu/Debian**: `sudo apt-get install php-gd`
- **Windows**: Uncomment `extension=gd` in `php.ini`
- **macOS**: `brew install php-gd` or `pecl install gd`

Then run `composer install` again.

### 2. Configure Environment

Copy `.env.example` to `.env` (if not exists) and update:

```bash
cp .env.example .env
php artisan key:generate
```

Update these values in `.env`:
```env
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticket_system
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Ticket System"
```

### 3. Run Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed
```

This creates:
- Database tables
- Admin user: `admin@example.com` / `password`
- Sample event

### 4. Create Storage Link

```bash
php artisan storage:link
```

This allows public access to uploaded files and QR codes.

### 5. Install Frontend Dependencies (if needed)

```bash
npm install
npm run build
```

### 6. Start Development Server

```bash
php artisan serve
```

Visit: `http://localhost:8000`

### 7. Start Queue Worker (for emails)

In a separate terminal:

```bash
php artisan queue:work
```

Or use Laravel Horizon/Supervisor for production.

## Testing the System

### 1. Register a Ticket

1. Visit `/form`
2. Fill in the form:
   - Name: John Doe
   - Email: john@example.com
   - Phone: 0300-1234567
   - CNIC: 12345-1234567-1
   - Event: Select from dropdown
   - Upload transaction screenshot
3. Submit

### 2. Login as Admin

1. Visit `/login`
2. Email: `admin@example.com`
3. Password: `password`

### 3. Verify Ticket

1. Go to `/admin/tickets/pending`
2. Click "Verify" on a pending ticket
3. Check student's email for verification email with QR code

### 4. Test QR Verification

1. Open the verification URL from email: `/verify-ticket/{token}`
2. Or scan the QR code
3. Should return JSON with ticket details
4. Ticket status changes to "checked_in"

## API Testing with cURL

### Register Ticket (API)
```bash
curl -X POST http://localhost:8000/api/tickets \
  -F "name=John Doe" \
  -F "email=john@example.com" \
  -F "phone=0300-1234567" \
  -F "cnic=12345-1234567-1" \
  -F "event_id=1" \
  -F "transaction_screenshot=@/path/to/screenshot.jpg"
```

### Get Pending Tickets (Admin API)
```bash
curl -X GET http://localhost:8000/api/admin/tickets/pending \
  -H "Authorization: Bearer YOUR_SANCTUM_TOKEN" \
  -H "Accept: application/json"
```

### Verify Ticket (Admin API)
```bash
curl -X POST http://localhost:8000/api/admin/tickets/1/verify \
  -H "Authorization: Bearer YOUR_SANCTUM_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"ticket_id": 1}'
```

### Verify QR Code
```bash
curl -X GET http://localhost:8000/api/verify-ticket/YOUR_TOKEN
```

## Common Issues

### QR Code Not Generating
- Check GD extension: `php -m | grep gd`
- Check storage permissions: `chmod -R 775 storage`
- Check storage link: `ls -la public/storage`

### Emails Not Sending
- Check `.env` mail settings
- Check queue worker is running
- Check logs: `tail -f storage/logs/laravel.log`
- Test mail config: `php artisan tinker` → `Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });`

### Storage Files 404
- Run: `php artisan storage:link`
- Check `public/storage` exists and is symlinked
- Check file permissions

### Admin Access Denied
- Check user has `is_admin = 1` in database
- Update user: `php artisan tinker` → `User::where('email', 'admin@example.com')->update(['is_admin' => 1]);`

## Production Deployment

1. Set `APP_DEBUG=false` in `.env`
2. Set `APP_ENV=production` in `.env`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Set up queue worker (Supervisor)
7. Set up cron for scheduler (if needed)
8. Configure web server (Nginx/Apache)
9. Enable HTTPS
10. Set up database backups

## Next Steps

- Customize email template in `resources/views/emails/ticket-verified.blade.php`
- Add more events via admin panel or seeder
- Customize admin dashboard
- Add more validation rules if needed
- Set up monitoring and logging

