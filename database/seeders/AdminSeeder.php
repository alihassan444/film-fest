<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::where('email', 'admin@example.com')->delete();

        User::updateOrCreate(
            ['email' => 'alihassan07077@gmail.com'],
            [
                'name' => 'Ali Hassan',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created: alihassan07077@gmail.com / password');
    }
}

