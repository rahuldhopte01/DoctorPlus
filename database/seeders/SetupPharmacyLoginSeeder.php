<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\Hash;

class SetupPharmacyLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get user for pharmacy login (OLD system)
        $user = User::firstOrCreate(
            ['email' => 'pharmacy1@test.com'],
            [
                'name' => 'Test Pharmacy User',
                'password' => Hash::make('password'),
                'phone' => '1234567890',
                'phone_code' => '+1',
                'verify' => 1,
                'status' => 1,
            ]
        );

        // Assign pharmacy role if not already assigned
        if (!$user->hasRole('pharmacy')) {
            $user->assignRole('pharmacy');
        }

        // Create pharmacy entry in OLD system (pharmacy table)
        Pharmacy::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Test Pharmacy',
                'email' => 'pharmacy1@test.com',
                'phone' => '1234567890',
                'address' => '123 Test Street',
                'lat' => '40.7128',
                'lang' => '-74.0060',
                'image' => 'defaultUser.png',
                'status' => 1,
                'start_time' => '08:00 am',
                'end_time' => '08:00 pm',
                'commission_amount' => 10,
                'is_shipping' => 1,
            ]
        );

        $this->command->info('Pharmacy login setup complete!');
        $this->command->info('Login credentials:');
        $this->command->info('Email: pharmacy1@test.com');
        $this->command->info('Password: password');
        $this->command->info('URL: http://127.0.0.1:8000/pharmacy_login');
    }
}
