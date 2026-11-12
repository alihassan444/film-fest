<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketDirectoryTest extends TestCase
{
    use RefreshDatabase;

    private function ticket(string $name, string $status): Ticket
    {
        return Ticket::create(['name' => $name, 'email' => str_replace(' ', '', strtolower($name)).'@example.com', 'status' => $status]);
    }

    public function test_dashboard_tracks_check_ins_without_double_counting(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->ticket('Pending Buyer', 'pending');
        $verified = $this->ticket('Verified Buyer', 'verified');
        $this->ticket('Rejected Buyer', 'rejected');
        $this->ticket('Admitted Buyer', 'checked_in');

        $this->get(route('admin.dashboard'))->assertOk()
            ->assertViewHas('totalTickets', 4)
            ->assertViewHas('verifiedTickets', 2)
            ->assertViewHas('awaitingEntryTickets', 1)
            ->assertViewHas('rejectedTickets', 1)
            ->assertViewHas('checkedInTickets', 1)
            ->assertViewHas('approvedTickets', 2)
            ->assertViewHas('checkInRate', 50.0);

        $this->post(route('admin.tickets.checkin', $verified->id))->assertRedirect();
        $this->get(route('admin.dashboard'))->assertOk()
            ->assertViewHas('verifiedTickets', 2)
            ->assertViewHas('totalTickets', 4)
            ->assertViewHas('awaitingEntryTickets', 0)
            ->assertViewHas('checkedInTickets', 2)
            ->assertViewHas('approvedTickets', 2)
            ->assertViewHas('checkInRate', 100.0);

        $this->get(route('admin.tickets.index', ['status' => 'verified']))
            ->assertOk()->assertViewHas('tickets', fn ($tickets) => $tickets->total() === 2);
    }

    public function test_search_and_filters_remain_scoped_to_the_selected_tab(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $pending = $this->ticket('Camera Student', 'pending');
        $rejected = $this->ticket('Camera Guest', 'rejected');
        $this->ticket('Another Student', 'pending');

        $this->get(route('admin.tickets.pending', ['q' => 'Camera', 'status' => 'rejected']))
            ->assertOk()->assertViewHas('tickets', fn ($tickets) => $tickets->total() === 1 && $tickets->first()->is($pending));
        $this->get(route('admin.tickets.rejected', ['q' => $rejected->ticket_id]))
            ->assertOk()->assertViewHas('tickets', fn ($tickets) => $tickets->total() === 1 && $tickets->first()->is($rejected));
        $this->get(route('admin.tickets.index', ['status' => 'verified']))
            ->assertOk()->assertSee('No tickets found');
    }

    public function test_sorting_pagination_and_empty_dashboard_are_supported(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->get(route('admin.dashboard'))->assertOk()->assertViewHas('checkInRate', 0);
        foreach (range(1, 16) as $number) {
            $this->ticket(sprintf('Buyer %02d', $number), 'pending');
        }
        $this->get(route('admin.tickets.pending', ['q' => 'Buyer', 'sort' => 'name', 'per_page' => 15, 'page' => 2]))
            ->assertOk()->assertViewHas('tickets', fn ($tickets) => $tickets->total() === 16 && $tickets->count() === 1 && $tickets->first()->name === 'Buyer 16');
        $this->get(route('admin.tickets.pending', ['per_page' => 30]))
            ->assertOk()->assertViewHas('tickets', fn ($tickets) => $tickets->count() === 16);
    }
}
