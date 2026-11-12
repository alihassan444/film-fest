<?php

namespace App\Services;

use App\Mail\TicketRejectedMail;
use App\Mail\TicketVerifiedMail;
use App\Models\Ticket;
use Illuminate\Support\Facades\Mail;

class TicketDecisionMailer
{
    public function send(Ticket $ticket): void
    {
        $this->ensureDeliveryConfigured();

        $mail = match ($ticket->status) {
            'verified', 'checked_in' => new TicketVerifiedMail($ticket),
            'rejected' => new TicketRejectedMail($ticket),
            default => throw new \LogicException('Pending tickets cannot receive decision emails.'),
        };

        // Deliberately synchronous: no queue worker or polling interval involved.
        Mail::to($ticket->email)->send($mail);
    }

    public function ensureDeliveryConfigured(): void
    {
        $mailer = config('mail.default');
        $testArrayTransport = app()->environment('testing') && $mailer === 'array';
        if (in_array($mailer, ['log', 'array'], true) && ! Mail::isFake() && ! $testArrayTransport) {
            throw new \RuntimeException('Configure a delivery mailer before sending ticket notifications.');
        }

    }
}
