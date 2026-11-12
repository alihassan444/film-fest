<?php

namespace App\Listeners;

use App\Events\TicketVerified;
use App\Services\TicketDecisionMailer;

class SendTicketVerificationEmail
{
    public function handle(TicketVerified $event): void
    {
        app(TicketDecisionMailer::class)->send($event->ticket->refresh());
    }
}
