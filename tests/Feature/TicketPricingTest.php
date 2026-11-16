<?php

namespace Tests\Feature;

use App\Support\TicketPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_cutoffs_use_pakistan_midnight(): void
    {
        foreach ([
            ['2026-12-04 23:59:59+05:00', true, true],
            ['2026-12-05 00:00:00+05:00', false, true],
            ['2026-12-10 23:59:59+05:00', false, true],
            ['2026-12-11 00:00:00+05:00', false, false],
        ] as [$date, $early, $advance]) {
            $this->travelTo(new \DateTimeImmutable($date));
            $prices = TicketPricing::options();
            $this->assertSame($early, $prices['early']['available']);
            $this->assertSame($advance, $prices['advance']['available']);
            $this->assertTrue($prices['window']['available']);
        }
    }

    public function test_expired_links_and_stale_submissions_are_rejected(): void
    {
        foreach ([['2026-12-05', 'early'], ['2026-12-11', 'advance']] as [$date, $tier]) {
            $this->travelTo(new \DateTimeImmutable($date.' 00:00:00+05:00'));
            $this->get('/form?tier='.$tier)->assertRedirect(route('index').'#pricing');
            $this->post('/ticket', [
                'ticket_tier' => $tier, 'name' => 'Guest', 'email' => 'guest@example.com',
                'phone' => '03001234567', 'payment_method' => 'cash', 'cash_amount' => 1200,
            ])->assertSessionHasErrors('ticket_tier');
        }
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_advance_and_window_remain_available_before_early_cutoff(): void
    {
        $this->travelTo(new \DateTimeImmutable('2026-11-16 12:00:00+05:00'));
        foreach (['early', 'advance', 'window'] as $tier) {
            $this->get('/form?tier='.$tier)->assertOk();
        }
    }

    public function test_only_window_remains_after_advance_cutoff_for_festival_pricing(): void
    {
        $this->travelTo(new \DateTimeImmutable('2026-12-11 00:00:00+05:00'));
        $this->get('/')->assertOk()->assertSee('Booking closed')
            ->assertDontSee('href="'.route('form', ['tier' => 'early']).'"', false)
            ->assertDontSee('href="'.route('form', ['tier' => 'advance']).'"', false)
            ->assertSee(route('form', ['tier' => 'window']), false);
        $this->get('/form')->assertOk()->assertSee('name="ticket_tier" value="window"', false)->assertDontSee('id="ticket-tier"', false);
    }
}
