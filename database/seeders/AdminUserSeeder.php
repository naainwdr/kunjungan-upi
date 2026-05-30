<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin utama
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@upi.edu')],
            [
                'name'     => 'Admin Humas UPI',
                'email'    => env('ADMIN_EMAIL', 'admin@upi.edu'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
                'role'     => 'admin',
            ]
        );

        $this->command->info('✅ Admin user created: ' . env('ADMIN_EMAIL', 'admin@upi.edu'));

        // Petugas Presensi
        User::updateOrCreate(
            ['email' => env('PETUGAS_EMAIL', 'petugas@upi.edu')],
            [
                'name'     => 'Petugas Presensi',
                'email'    => env('PETUGAS_EMAIL', 'petugas@upi.edu'),
                'password' => Hash::make(env('PETUGAS_PASSWORD', 'petugas123')),
                'role'     => 'petugas',
            ]
        );

        $this->command->info('✅ Petugas user created: ' . env('PETUGAS_EMAIL', 'petugas@upi.edu'));
    }
}
