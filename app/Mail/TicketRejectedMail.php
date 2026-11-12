<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TicketRejectedMail extends Mailable
{
    public function __construct(public Ticket $ticket)
    {
        if ($ticket->status !== 'rejected') {
            throw new \LogicException('A rejection email requires a rejected ticket.');
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Film Fest-26 — Update on your ticket request');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.ticket-rejected');
    }

    public function attachments(): array
    {
        return [];
    }
}
