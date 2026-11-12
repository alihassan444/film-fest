<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Services\ApprovedTicketQrCode;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TicketVerifiedMail extends Mailable
{
    private ?array $qrPayload = null;

    public function __construct(public Ticket $ticket)
    {
        if (! in_array($ticket->status, ['verified', 'checked_in'], true) || ! $ticket->verification_token) {
            throw new \LogicException('Approval emails require a verified ticket.');
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Film Fest-26 — Your verified ticket and QR code');
    }

    public function content(): Content
    {
        $qr = $this->qr();
        return new Content(view: 'emails.ticket-verified', with: ['qrData' => $qr['data'], 'qrMime' => $qr['mime']]);
    }

    public function attachments(): array
    {
        $qr = $this->qr();
        return [Attachment::fromData(fn () => $qr['data'], 'ticket-qr-code.'.$qr['extension'])->withMime($qr['mime'])];
    }

    private function qr(): array
    {
        return $this->qrPayload ??= app(ApprovedTicketQrCode::class)->generate($this->ticket);
    }
}
