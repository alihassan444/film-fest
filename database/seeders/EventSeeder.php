<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::firstOrCreate(
            ['name' => 'Film & TV Society Festival'],
            [
                'description' => 'Annual Film & TV Society Festival featuring screenings, workshops, and Qawali Night.',
                'start_date' => '2025-12-10 10:00:00',
                'end_date' => '2025-12-11 23:00:00',
                'venue' => 'IAC Amphitheatre',
                'price' => 2000.00,
                'is_active' => true,
            ]
        );

        $this->command->info('Sample event created.');
    }
}

