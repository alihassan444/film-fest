<?php

namespace Tests\Feature;

use App\Mail\TicketRejectedMail;
use App\Mail\TicketVerifiedMail;
use App\Models\Ticket;
use App\Models\User;
use App\Services\ApprovedTicketQrCode;
use App\Services\TicketDecisionMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TicketDecisionEmailTest extends TestCase
{
    use RefreshDatabase;

    private function pendingTicket(): Ticket
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        return Ticket::create(['name' => 'Test Attendee', 'email' => 'attendee@example.com', 'status' => 'pending']);
    }

    public function test_approval_sends_once_immediately_and_does_not_queue(): void
    {
        Mail::fake();
        Queue::fake();
        $ticket = $this->pendingTicket();
        $this->post(route('admin.tickets.verify', $ticket->id))->assertSessionHas('success');
        $this->post(route('admin.tickets.verify', $ticket->id))->assertSessionHas('error');

        Mail::assertSent(TicketVerifiedMail::class, fn ($mail) => $mail->hasTo($ticket->email) && $mail->ticket->status === 'verified' && count($mail->attachments()) === 1);
        Mail::assertSentCount(1);
        Mail::assertQueuedCount(0);
        Queue::assertNothingPushed();
        $this->assertNotNull($ticket->fresh()->verification_token);
    }

    public function test_rejection_sends_only_the_reason_without_qr_or_attachments(): void
    {
        Mail::fake();
        $ticket = $this->pendingTicket();
        $this->post(route('admin.tickets.reject', $ticket->id), ['rejection_reason' => 'Payment reference does not match.'])->assertSessionHas('success');
        Mail::assertSent(TicketRejectedMail::class, function ($mail) use ($ticket) {
            $this->assertSame([], $mail->attachments());
            $html = $mail->render();
            $this->assertStringContainsString('Payment reference does not match.', $html);
            $this->assertStringNotContainsString('<img', $html);
            $this->assertStringNotContainsString('ticket-qr-code', $html);
            return $mail->hasTo($ticket->email);
        });
        Mail::assertNotSent(TicketVerifiedMail::class);
        $this->post(route('admin.tickets.resendEmail', $ticket->id))->assertSessionHas('success');
        Mail::assertSentCount(2);
        Mail::assertNotSent(TicketVerifiedMail::class);
    }

    public function test_approval_email_renders_inline_qr_and_attachment_without_existing_files(): void
    {
        $ticket = $this->pendingTicket();
        $ticket->update(['status' => 'verified', 'verification_token' => 'test-verification-token']);
        $mail = new TicketVerifiedMail($ticket);
        $this->assertCount(1, $mail->attachments());
        $html = $mail->render();
        $this->assertStringContainsString($ticket->ticket_id, $html);
        if (extension_loaded('gd')) {
            $qr = app(ApprovedTicketQrCode::class)->generate($ticket);
            $this->assertSame('image/png', $qr['mime']);
            $this->assertSame("\x89PNG\r\n\x1a\n", substr($qr['data'], 0, 8));
            $this->assertStringContainsString('data:image/png;base64,', $html);
            $sent = Mail::mailer('array')->to($ticket->email)->send(new TicketVerifiedMail($ticket));
            $message = $sent->getSymfonySentMessage()->getOriginalMessage();
            $this->assertStringContainsString('cid:', $message->getHtmlBody());
            $this->assertCount(2, $message->getAttachments()); // One inline image and one downloadable QR.
        }
    }

    public function test_failed_delivery_is_visible_and_can_be_retried_without_reapproving(): void
    {
        $ticket = $this->pendingTicket();
        $this->mock(TicketDecisionMailer::class, function ($mock) {
            $mock->shouldReceive('send')->once()->andThrow(new \RuntimeException('Mail transport unavailable'));
        });
        $this->post(route('admin.tickets.verify', $ticket->id))->assertSessionHas('error');
        $this->assertSame('verified', $ticket->fresh()->status);
        $token = $ticket->fresh()->verification_token;

        $this->app->instance(TicketDecisionMailer::class, new TicketDecisionMailer());
        Mail::fake();
        $this->post(route('admin.tickets.resendEmail', $ticket->id))->assertSessionHas('success');
        Mail::assertSentCount(1);
        $this->assertSame($token, $ticket->fresh()->verification_token);
    }

    public function test_pending_and_rejected_tickets_cannot_receive_approval_qr(): void
    {
        Mail::fake();
        $ticket = $this->pendingTicket();
        $this->post(route('admin.tickets.resendEmail', $ticket->id))->assertSessionHas('error');
        Mail::assertNothingSent();
        $ticket->update(['status' => 'rejected']);
        $this->expectException(\LogicException::class);
        new TicketVerifiedMail($ticket);
    }

    public function test_log_mailer_does_not_claim_delivery(): void
    {
        config(['mail.default' => 'log']);
        $ticket = $this->pendingTicket();
        $this->post(route('admin.tickets.verify', $ticket->id))->assertSessionHas('error');
        $this->assertSame('verified', $ticket->fresh()->status);
    }
}
