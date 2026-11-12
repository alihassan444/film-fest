# Laravel Ticketing System - Backend Documentation

## Overview

This is a complete Laravel-based ticketing system backend with the following features:
- Student ticket registration with transaction screenshot upload
- Admin panel for ticket verification
- QR code generation for verified tickets
- Email notifications with QR codes
- QR code scanning for event day verification
- RESTful API endpoints

## Folder Structure

```
app/
├── Events/
│   └── TicketVerified.php
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── TicketController.php
│   │   └── VerificationController.php
│   ├── Middleware/
│   │   └── EnsureUserIsAdmin.php
│   └── Requests/
│       ├── StoreTicketRequest.php
│       └── VerifyTicketRequest.php
├── Listeners/
│   └── SendTicketVerificationEmail.php
├── Mail/
│   └── TicketVerifiedMail.php
├── Models/
│   ├── Event.php
│   ├── Ticket.php
│   └── User.php (updated)
├── Providers/
│   ├── AppServiceProvider.php
│   └── EventServiceProvider.php
└── Services/
    ├── QrCodeService.php
    └── TicketService.php

database/
├── migrations/
│   ├── 2024_01_01_000001_create_events_table.php
│   ├── 2024_01_01_000002_create_tickets_table.php
│   └── 2024_01_01_000003_add_is_admin_to_users_table.php
└── seeders/
    ├── AdminSeeder.php
    ├── EventSeeder.php
    └── DatabaseSeeder.php

resources/
└── views/
    └── emails/
        └── ticket-verified.blade.php

routes/
├── api.php
└── web.php
```

## Installation & Setup

### 1. Install Dependencies

```bash
# Install PHP dependencies (requires GD extension for QR codes)
composer install

# Note: If GD extension is missing, install it:
# Ubuntu/Debian: sudo apt-get install php-gd
# Windows: Enable extension=gd in php.ini
# macOS: brew install php-gd
```

### 2. Environment Configuration

Update your `.env` file:

```env
APP_URL=https://mydomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticket_system
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Run Migrations

```bash
php artisan migrate
php artisan db:seed
```

This will:
- Create all necessary tables
- Create an admin user (email: `admin@example.com`, password: `password`)
- Create a sample event

### 4. Create Storage Link

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` for accessing uploaded files.

### 5. Publish Sanctum Configuration (if using API authentication)

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

## API Endpoints

### Public Endpoints

#### 1. Register Ticket
**POST** `/api/tickets` or `/tickets`

**Request:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "0300-1234567",
    "cnic": "12345-1234567-1",
    "event_id": 1,
    "transaction_screenshot": "<file>"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Ticket registration submitted successfully. Your ticket is pending verification.",
    "data": {
        "ticket_id": "TKT-ABC123XYZ",
        "status": "pending",
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

#### 2. Check Ticket Status
**GET** `/api/tickets/{ticketId}/status`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "ticket_id": "TKT-ABC123XYZ",
        "status": "verified",
        "name": "John Doe",
        "email": "john@example.com",
        "event": {
            "name": "Film & TV Society Festival",
            "venue": "IAC Amphitheatre"
        },
        "verified_at": "2024-01-15 10:30:00",
        "checked_in_at": null
    }
}
```

#### 3. Verify Ticket (QR Scan)
**GET** `/api/verify-ticket/{token}` or `/verify-ticket/{token}`

**Response - Verified (200):**
```json
{
    "success": true,
    "status": "VERIFIED",
    "message": "Ticket verified successfully.",
    "data": {
        "id": 1,
        "ticket_id": "TKT-ABC123XYZ",
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "0300-1234567",
        "event": {
            "id": 1,
            "name": "Film & TV Society Festival",
            "venue": "IAC Amphitheatre",
            "start_date": "2025-12-10 10:00:00"
        },
        "checked_in_at": "2024-01-15 14:30:00"
    }
}
```

**Response - Already Used (409):**
```json
{
    "success": false,
    "status": "ALREADY_USED",
    "message": "This ticket has already been used.",
    "data": null
}
```

**Response - Invalid (404):**
```json
{
    "success": false,
    "status": "INVALID",
    "message": "Invalid ticket token.",
    "data": null
}
```

#### 4. Get Ticket Details (without check-in)
**GET** `/api/ticket/{token}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "ticket_id": "TKT-ABC123XYZ",
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "0300-1234567",
        "status": "verified",
        "event": {
            "id": 1,
            "name": "Film & TV Society Festival",
            "venue": "IAC Amphitheatre",
            "start_date": "2025-12-10 10:00:00"
        },
        "verified_at": "2024-01-15 10:30:00",
        "checked_in_at": null
    }
}
```

### Admin Endpoints (Protected)

All admin endpoints require authentication. Use either:
- Session-based auth (for web admin panel)
- Sanctum token (for API calls)

**Authentication Header for API:**
```
Authorization: Bearer {sanctum_token}
```

#### 1. Dashboard Statistics
**GET** `/api/admin/dashboard`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "total_tickets": 150,
        "pending_tickets": 25,
        "verified_tickets": 100,
        "checked_in_tickets": 25,
        "total_events": 1
    }
}
```

#### 2. Get Pending Tickets
**GET** `/api/admin/tickets/pending?event_id=1`

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "ticket_id": "TKT-ABC123XYZ",
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "0300-1234567",
            "cnic": "12345-1234567-1",
            "event": {
                "id": 1,
                "name": "Film & TV Society Festival"
            },
            "transaction_screenshot": "http://example.com/storage/tickets/screenshot.jpg",
            "created_at": "2024-01-15 09:00:00"
        }
    ]
}
```

#### 3. Verify Ticket
**POST** `/api/admin/tickets/{id}/verify`

**Request:**
```json
{
    "ticket_id": 1
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Ticket verified successfully. Email sent to student.",
    "data": {
        "ticket_id": "TKT-ABC123XYZ",
        "status": "verified",
        "verification_token": "550e8400-e29b-41d4-a716-446655440000",
        "verified_at": "2024-01-15 10:30:00"
    }
}
```

#### 4. Reject Ticket
**POST** `/api/admin/tickets/{id}/reject`

**Request:**
```json
{
    "reason": "Invalid transaction screenshot"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Ticket rejected successfully.",
    "data": {
        "ticket_id": "TKT-ABC123XYZ",
        "status": "rejected"
    }
}
```

#### 5. Get All Tickets
**GET** `/api/admin/tickets?status=verified&event_id=1`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [...],
        "per_page": 20,
        "total": 100
    }
}
```

#### 6. Get Events
**GET** `/api/admin/events`

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Film & TV Society Festival",
            "description": "Annual festival...",
            "start_date": "2025-12-10 10:00:00",
            "end_date": "2025-12-11 23:00:00",
            "venue": "IAC Amphitheatre",
            "price": "2000.00",
            "is_active": true
        }
    ]
}
```

## Web Routes

### Public Routes
- `GET /` - Home page
- `GET /form` - Ticket registration form
- `POST /tickets` - Submit ticket registration
- `GET /verify-ticket/{token}` - QR verification page

### Admin Routes (Protected)
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/tickets/pending` - Pending tickets list
- `GET /admin/tickets` - All tickets list
- `POST /admin/tickets/{id}/verify` - Verify a ticket
- `POST /admin/tickets/{id}/reject` - Reject a ticket
- `GET /admin/events` - Events list

## Database Schema

### Events Table
- `id` - Primary key
- `name` - Event name
- `description` - Event description
- `start_date` - Event start date/time
- `end_date` - Event end date/time
- `venue` - Event venue
- `price` - Ticket price
- `max_tickets` - Maximum tickets (nullable)
- `is_active` - Active status
- `timestamps`

### Tickets Table
- `id` - Primary key
- `event_id` - Foreign key to events
- `name` - Student name
- `email` - Student email
- `phone` - Student phone
- `cnic` - CNIC (unique)
- `transaction_screenshot` - Screenshot file path
- `ticket_id` - Unique ticket identifier (e.g., TKT-ABC123XYZ)
- `verification_token` - UUID token for QR code
- `status` - Enum: pending, verified, checked_in, rejected
- `verified_at` - Verification timestamp
- `checked_in_at` - Check-in timestamp
- `qr_code_path` - QR code file path
- `rejection_reason` - Rejection reason (if rejected)
- `timestamps`

### Users Table (Extended)
- `is_admin` - Boolean flag for admin users

## Features

### 1. Student Registration
- Students submit: name, email, phone, CNIC, event_id, transaction_screenshot
- Data saved with status "pending"
- Screenshot stored in `storage/app/public/tickets`
- Unique ticket ID generated automatically

### 2. Admin Verification
- Admin views pending tickets
- Admin verifies ticket → generates UUID token
- QR code generated with verification URL
- Email sent automatically with ticket details and QR code
- Status updated to "verified"

### 3. QR Code Verification
- Security/admin scans QR code or opens verification URL
- System checks:
  - Token exists
  - Ticket is verified
  - Ticket not already checked in
- Returns JSON response with status
- Marks ticket as "checked_in" with timestamp

### 4. Email Notifications
- Beautiful HTML email template
- Includes:
  - Student name
  - Event details
  - Unique Ticket ID
  - QR code (inline and attachment)
  - Status badge
  - Instructions

## Security Features

1. **Authentication**: Session-based for web, Sanctum for API
2. **Authorization**: Admin middleware for protected routes
3. **Rate Limiting**: 60 requests/minute for QR verification endpoint
4. **File Validation**: Image files only, max 5MB
5. **Unique Constraints**: CNIC must be unique per ticket
6. **UUID Tokens**: Secure verification tokens

## Queue Configuration

Email sending is queued for better performance. Make sure to run:

```bash
php artisan queue:work
```

Or use a process manager like Supervisor for production.

## Testing

### Create Admin User
```bash
php artisan db:seed --class=AdminSeeder
```

### Create Sample Event
```bash
php artisan db:seed --class=EventSeeder
```

### Test QR Verification
1. Register a ticket
2. Verify it as admin
3. Use the verification token in URL: `/verify-ticket/{token}`
4. Check response and ticket status

## Troubleshooting

### QR Code Not Generating
- Ensure GD extension is installed: `php -m | grep gd`
- Check storage permissions: `chmod -R 775 storage`

### Emails Not Sending
- Check `.env` mail configuration
- Verify queue worker is running
- Check `storage/logs/laravel.log` for errors

### Storage Files Not Accessible
- Run `php artisan storage:link`
- Check `public/storage` symlink exists

### Admin Access Denied
- Ensure user has `is_admin = 1` in database
- Check middleware is registered in `bootstrap/app.php`

## Production Checklist

- [ ] Update `APP_URL` in `.env`
- [ ] Configure production mail settings
- [ ] Set up queue worker (Supervisor)
- [ ] Enable HTTPS
- [ ] Set `APP_DEBUG=false`
- [ ] Configure rate limiting
- [ ] Set up database backups
- [ ] Test email delivery
- [ ] Test QR code generation
- [ ] Test file uploads
- [ ] Review security settings

## Support

For issues or questions, check:
- Laravel Documentation: https://laravel.com/docs
- SimpleSoftwareIO QrCode: https://www.simplesoftware.io/docs/simple-qrcode
- Laravel Sanctum: https://laravel.com/docs/sanctum

