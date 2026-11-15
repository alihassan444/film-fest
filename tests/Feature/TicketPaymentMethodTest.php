<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Tests\TestCase;

class TicketPaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_booking_saves_amount_without_receipt_and_stays_pending(): void
    {
        $qr = \Mockery::mock(\SimpleSoftwareIO\QrCode\Generator::class);
        $qr->shouldReceive('format')->once()->andReturnSelf();
        $qr->shouldReceive('size')->once()->andReturnSelf();
        $qr->shouldReceive('margin')->once()->andReturnSelf();
        $qr->shouldReceive('generate')->once();
        QrCode::swap($qr);

        $this->post('/ticket', [
            'name' => 'Cash Guest', 'email' => 'cash@example.com', 'phone' => '03001234567',
            'payment_method' => 'cash', 'cash_amount' => '1500.50',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'email' => 'cash@example.com', 'payment_method' => 'cash',
            'cash_amount' => 1500.50, 'transaction_screenshot' => null, 'status' => 'pending',
        ]);
    }

    public function test_cash_requires_a_positive_amount_with_at_most_two_decimal_places(): void
    {
        foreach ([null, 0, -1, 'abc', '1.001'] as $amount) {
            $this->post('/ticket', [
                'name' => 'Guest', 'email' => 'guest@example.com',
                'payment_method' => 'cash', 'cash_amount' => $amount,
            ])->assertSessionHasErrors('cash_amount');
        }
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_bank_and_legacy_bookings_require_a_receipt(): void
    {
        foreach ([[], ['payment_method' => 'bank']] as $payment) {
            $this->post('/ticket', array_merge([
                'name' => 'Guest', 'email' => 'guest@example.com',
            ], $payment))->assertSessionHasErrors('transaction_screenshot');
        }
    }

    public function test_unknown_payment_methods_are_rejected(): void
    {
        $this->post('/ticket', [
            'name' => 'Guest', 'email' => 'guest@example.com', 'payment_method' => 'other',
        ])->assertSessionHasErrors('payment_method');
    }
}
