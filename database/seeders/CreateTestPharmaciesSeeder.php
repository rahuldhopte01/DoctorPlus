<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pharmacy;
use App\Models\PharmacyWorkingHour;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateTestPharmaciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = Setting::first();
        
        // Ensure pharmacy role exists
        $pharmacyRole = Role::firstOrCreate(['name' => 'pharmacy', 'guard_name' => 'web']);
        
        $testPharmacies = [
            [
                'name' => 'Test Pharmacy 1 - Shipping Enabled',
                'email' => 'pharmacy1@test.com',
                'phone' => '1234567890',
                'phone_code' => '+1',
                'password' => 'pharmacy123',
                'address' => '123 Main Street, Test City',
                'postcode' => '12345',
                'lat' => '40.7128',
                'lang' => '-74.0060',
                'is_shipping' => 1, // Shipping enabled
                'is_priority' => 1,
                'status' => 'approved',
            ],
            [
                'name' => 'Test Pharmacy 2 - No Shipping',
                'email' => 'pharmacy2@test.com',
                'phone' => '1234567891',
                'phone_code' => '+1',
                'password' => 'pharmacy123',
                'address' => '456 Oak Avenue, Test City',
                'postcode' => '12346',
                'lat' => '40.7130',
                'lang' => '-74.0062',
                'is_shipping' => 0, // No shipping
                'is_priority' => 0,
                'status' => 'approved',
            ],
            [
                'name' => 'Test Pharmacy 3 - Shipping Enabled',
                'email' => 'pharmacy3@test.com',
                'phone' => '1234567892',
                'phone_code' => '+1',
                'password' => 'pharmacy123',
                'address' => '789 Pine Road, Test City',
                'postcode' => '12347',
                'lat' => '40.7132',
                'lang' => '-74.0064',
                'is_shipping' => 1, // Shipping enabled
                'is_priority' => 0,
                'status' => 'approved',
            ],
            [
                'name' => 'Test Pharmacy 4 - Priority Shipping',
                'email' => 'pharmacy4@test.com',
                'phone' => '1234567893',
                'phone_code' => '+1',
                'password' => 'pharmacy123',
                'address' => '321 Elm Street, Test City',
                'postcode' => '12348',
                'lat' => '40.7134',
                'lang' => '-74.0066',
                'is_shipping' => 1, // Shipping enabled
                'is_priority' => 1,
                'status' => 'approved',
            ],
        ];
        
        $createdPharmacies = [];
        
        foreach ($testPharmacies as $pharmacyData) {
            // Check if user already exists
            $existingUser = User::where('email', $pharmacyData['email'])->first();
            
            if ($existingUser) {
                $this->command->warn("Pharmacy with email {$pharmacyData['email']} already exists. Skipping...");
                continue;
            }
            
            // Create user
            $user = User::create([
                'name' => $pharmacyData['name'],
                'email' => $pharmacyData['email'],
                'password' => Hash::make($pharmacyData['password']),
                'verify' => 1,
                'phone' => $pharmacyData['phone'],
                'phone_code' => $pharmacyData['phone_code'],
            ]);
            
            // Assign pharmacy role
            $user->assignRole('pharmacy');
            
            // Create pharmacy record
            $pharmacy = Pharmacy::create([
                'user_id' => $user->id,
                'name' => $pharmacyData['name'],
                'email' => $pharmacyData['email'],
                'phone' => $pharmacyData['phone'],
                'address' => $pharmacyData['address'],
                'postcode' => $pharmacyData['postcode'] ?? null,
                'lat' => $pharmacyData['lat'] ?? ($setting->lat ?? ''),
                'lang' => $pharmacyData['lang'] ?? ($setting->lang ?? ''),
                'start_time' => '08:00 am',
                'end_time' => '08:00 pm',
                'description' => 'Test pharmacy for development and testing purposes',
                'commission_amount' => $setting->pharmacy_commission ?? 10,
                'status' => $pharmacyData['status'],
                'is_priority' => $pharmacyData['is_priority'],
                'is_shipping' => $pharmacyData['is_shipping'],
                'image' => 'defaultUser.png',
                'delivery_charges' => $pharmacyData['is_shipping'] ? 50 : null,
            ]);
            
            // Create working hours for all days
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            foreach ($days as $day) {
                $master = [];
                $temp2['start_time'] = $pharmacy->start_time;
                $temp2['end_time'] = $pharmacy->end_time;
                array_push($master, $temp2);
                
                PharmacyWorkingHour::create([
                    'pharmacy_id' => $pharmacy->id,
                    'period_list' => json_encode($master),
                    'day_index' => $day,
                    'status' => 1,
                ]);
            }
            
            $createdPharmacies[] = [
                'name' => $pharmacyData['name'],
                'email' => $pharmacyData['email'],
                'password' => $pharmacyData['password'],
                'pharmacy_id' => $pharmacy->id,
                'shipping_enabled' => $pharmacyData['is_shipping'] ? 'Yes' : 'No',
            ];
            
            $this->command->info("Created pharmacy: {$pharmacyData['name']}");
        }
        
        // Display summary
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('TEST PHARMACIES CREATED SUCCESSFULLY!');
        $this->command->info('========================================');
        $this->command->newLine();
        
        foreach ($createdPharmacies as $index => $pharmacy) {
            $this->command->line("Pharmacy " . ($index + 1) . ": {$pharmacy['name']}");
            $this->command->line("  Email: {$pharmacy['email']}");
            $this->command->line("  Password: {$pharmacy['password']}");
            $this->command->line("  Shipping Enabled: {$pharmacy['shipping_enabled']}");
            $this->command->line("  Pharmacy ID: {$pharmacy['pharmacy_id']}");
            $this->command->newLine();
        }
        
        $this->command->info('Login URL: ' . url('pharmacy_login'));
        $this->command->newLine();
    }
}
