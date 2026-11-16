<?php

namespace App\Support;

class TicketPricing
{
    public static function options(): array
    {
        $now = now('Asia/Karachi');

        return [
            'early' => ['label' => 'Early Bird', 'price' => 1200, 'available' => $now->gte('2026-11-16 00:00:00') && $now->lt('2026-12-05 00:00:00')],
            'advance' => ['label' => 'Advance Ticket', 'price' => 1500, 'available' => $now->lt('2026-12-11 00:00:00')],
            'window' => ['label' => 'Ticket Window', 'price' => 2000, 'available' => true],
            'social' => ['label' => 'Social Night', 'price' => 2500, 'available' => true],
        ];
    }

    public static function defaultTier(): string
    {
        foreach (self::options() as $key => $option) {
            if ($option['available']) {
                return $key;
            }
        }
        return 'window';
    }
}
