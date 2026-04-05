<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@upi.edu')],
            [
                'name'     => 'Admin Humas UPI',
                'email'    => env('ADMIN_EMAIL', 'admin@upi.edu'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
            ]
        );

        $this->command->info('✅ Admin user created: ' . env('ADMIN_EMAIL', 'admin@upi.edu'));
    }
}
