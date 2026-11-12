<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TicketDecisionMailer;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendApprovedTicketEmail implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 30;
    public int $uniqueFor = 600;

    public function __construct(public int $ticketId, public string $verificationToken)
    {
        $this->onQueue('ticket-emails');
    }

    public function uniqueId(): string
    {
        return $this->ticketId.':'.$this->verificationToken;
    }

    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function handle(TicketDecisionMailer $mailer): void
    {
        $ticket = Ticket::find($this->ticketId);
        // Never deliver a QR if the decision changed while this job was waiting.
        if (! $ticket || ! in_array($ticket->status, ['verified', 'checked_in'], true)
            || $ticket->verification_token !== $this->verificationToken) {
            return;
        }
        $mailer->send($ticket);
    }

    public function failed(?\Throwable $exception): void
    {
        Log::error('Bulk approval email failed after retries. Resend from ticket details.', [
            'ticket_id' => $this->ticketId,
            'error' => $exception?->getMessage(),
        ]);
    }
}
