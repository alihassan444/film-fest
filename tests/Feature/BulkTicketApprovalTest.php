<?php

namespace Tests\Feature;

use App\Jobs\SendApprovedTicketEmail;
use App\Mail\TicketVerifiedMail;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketDecisionMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BulkTicketApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function ticket(int $number, string $status = 'pending'): Ticket
    {
        return Ticket::create(['name' => 'Student '.$number, 'email' => 'student'.$number.'@example.com', 'status' => $status]);
    }

    public function test_fifty_approvals_enqueue_fifty_individual_emails_without_duplicates(): void
    {
        Queue::fake();
        Mail::fake();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $tickets = collect(range(1, 50))->map(fn ($number) => $this->ticket($number));
        $payload = ['ticket_ids' => $tickets->pluck('id')->all()];
        $this->post(route('admin.tickets.bulkVerify'), $payload)->assertSessionHas('success');
        $this->assertSame(50, Ticket::where('status', 'verified')->count());
        Queue::assertPushed(SendApprovedTicketEmail::class, 50);
        Queue::assertPushedOn('ticket-emails', SendApprovedTicketEmail::class);
        Mail::assertNothingSent();
        $this->post(route('admin.tickets.bulkVerify'), $payload)->assertSessionHas('error');
        Queue::assertPushed(SendApprovedTicketEmail::class, 50);

        foreach ($tickets as $ticket) {
            $ticket->refresh();
            (new SendApprovedTicketEmail($ticket->id, $ticket->verification_token))->handle(app(TicketDecisionMailer::class));
        }
        Mail::assertSentCount(50);
        Mail::assertSent(TicketVerifiedMail::class, fn ($mail) => $mail->hasTo('student50@example.com'));
    }

    public function test_rejected_tickets_are_never_approved_or_sent_a_qr(): void
    {
        Queue::fake();
        Mail::fake();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $pending = $this->ticket(1);
        $rejected = $this->ticket(2, 'rejected');
        $this->post(route('admin.tickets.bulkVerify'), ['ticket_ids' => [$pending->id, $rejected->id]])->assertSessionHas('success');
        Queue::assertPushed(SendApprovedTicketEmail::class, 1);
        $this->assertSame('rejected', $rejected->fresh()->status);
        (new SendApprovedTicketEmail($rejected->id, 'obsolete-token'))->handle(app(TicketDecisionMailer::class));
        Mail::assertNothingSent();
    }

    public function test_bulk_approval_requires_a_real_mailer_and_no_tickets_change_on_failure(): void
    {
        Queue::fake();
        config(['mail.default' => 'log']);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $ticket = $this->ticket(1);
        $this->post(route('admin.tickets.bulkVerify'), ['ticket_ids' => [$ticket->id]])->assertSessionHas('error');
        $this->assertSame('pending', $ticket->fresh()->status);
        Queue::assertNothingPushed();
    }

    public function test_selection_is_required_and_only_admins_can_bulk_approve(): void
    {
        Queue::fake();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->post(route('admin.tickets.bulkVerify'), [])->assertSessionHasErrors('ticket_ids');
        $this->actingAs(User::factory()->create(['is_admin' => false]));
        $this->post(route('admin.tickets.bulkVerify'), [])->assertForbidden();
        Queue::assertNothingPushed();
    }
}
