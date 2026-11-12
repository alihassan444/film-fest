<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketConfirmationTest extends TestCase
{
    public function test_submission_confirmation_displays_ticket_and_pending_status(): void
    {
        $this->withSession([
            'success' => 'Your ticket request has been submitted successfully.',
            'submitted_ticket_id' => 'TKT-TEST123456',
        ])->get(route('index'))
            ->assertOk()
            ->assertSee('TKT-TEST123456')
            ->assertSee('Awaiting verification')
            ->assertSee('Your ticket confirmation will be emailed after approval.');
    }

    public function test_confirmation_is_absent_without_a_submission(): void
    {
        $this->get(route('index'))->assertOk()->assertDontSee('id="ticket-confirmation"', false);
    }
}
