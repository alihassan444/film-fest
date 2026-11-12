<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketRejectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_rejection_form_and_reject_a_pending_ticket(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $ticket = Ticket::create([
            'name' => 'Ticket Buyer',
            'email' => 'buyer@example.com',
            'status' => 'pending',
            'transaction_screenshot' => 'tickets/receipt.png',
        ]);

        $this->actingAs($admin)->get(route('admin.tickets.pending'))
            ->assertOk()
            ->assertSee('frontend/js/bootstrap.min.js')
            ->assertSee('data-bs-target="#rejectModal'.$ticket->id.'"', false)
            ->assertSee(route('admin.tickets.reject', $ticket->id));

        $this->get(route('admin.tickets.show', $ticket->ticket_id))
            ->assertOk()
            ->assertSee('Attendee information')
            ->assertSee('Awaiting verification')
            ->assertSee('Reject request');

        $this->from(route('admin.tickets.pending'))
            ->post(route('admin.tickets.reject', $ticket->id), [
                'rejection_reason' => 'Payment receipt could not be verified.',
            ])
            ->assertRedirect(route('admin.tickets.pending'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'rejected',
            'rejection_reason' => 'Payment receipt could not be verified.',
        ]);

        $this->get(route('admin.tickets.pending'))->assertDontSee($ticket->ticket_id);
        $this->get(route('admin.tickets.rejected'))->assertSee($ticket->ticket_id);
        $this->get(route('admin.tickets.show', $ticket->ticket_id))
            ->assertOk()
            ->assertSee('Payment receipt could not be verified.')
            ->assertDontSee('id="detailRejectModal"', false);
    }
}
