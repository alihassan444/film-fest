# Ticket email delivery

Individual approval and rejection emails are sent synchronously during the admin action. Approval includes a QR attachment and an inline PNG when GD is available. Rejection uses a separate template with the reason and no QR or attachments.

## Bulk approval (up to 50 students)

On pending or all tickets, choose **50 rows**, select the pending tickets on that page, and press **Approve selected & email**. Only pending tickets change state; already processed tickets are skipped. Each approval creates a separate job on the `ticket-emails` queue with no scheduled delay. Transient failures retry up to three times without holding up the other students.

Use `QUEUE_CONNECTION=database` (or another persistent queue) and run the migrations before starting workers. A worker must remain running continuously, rather than being started by an occasional cron task:

```sh
php artisan queue:work --queue=ticket-emails --sleep=1 --tries=3 --timeout=30
```

For parallel delivery, run multiple copies of this worker (for example, four), managed by the hosting platform's process supervisor. Keep SMTP timeout below the worker timeout, and worker timeout below the queue's retry-after setting. Respect your provider's sending limits. Fifty approvals mean fifty individual emails, not one email exposing every student's address.

The approval response confirms enqueueing, not inbox delivery. A live worker begins processing immediately; four workers can send four emails at once. Monitor `php artisan queue:failed` and retry failures with `php artisan queue:retry <job-id>` after fixing the provider issue, or use the individual ticket's Resend email button. Avoid retrying the same notification through both methods.

## Configure actual delivery

The local `.env` currently selects `MAIL_MAILER=log`, which records messages rather than delivering them. Set your real mail provider's values locally:

```dotenv
APP_URL=https://your-public-event-domain.example
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=your-provider-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-provider-username
MAIL_PASSWORD=your-provider-password-or-app-password
MAIL_FROM_ADDRESS=tickets@your-verified-domain.example
MAIL_FROM_NAME="Film Fest-26"
```

Use the host, port and transport scheme required by the provider; implicit TLS commonly uses `smtps` and port 465. Do not commit passwords. Set `APP_URL` to the reachable event website so QR links work on attendees' phones; localhost URLs only work on the hosting computer.

After changing configuration:

```sh
php artisan config:clear
php artisan event:clear
```

If delivery fails, the decision remains saved and the administrator sees an error. Correct the provider configuration, open ticket details, and use **Resend email**. Rejected tickets always resend the rejection email without a QR.

A successful send means the mail provider accepted the message. Inbox arrival time remains controlled by the provider and recipient mail service.
