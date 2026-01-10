<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PharmacyRegistration;
use App\Models\PharmacyDeliverySetting;
use App\Models\PharmacyDeliveryMethod;
use App\Models\MedicineMaster;
use App\Models\MedicineBrand;
use App\Models\PharmacyInventory;
use Illuminate\Support\Facades\Hash;

class Module1TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users for pharmacy owners
        $pharmacyOwner1 = User::firstOrCreate(
            ['email' => 'pharmacy1@test.com'],
            [
                'name' => 'Pharmacy Owner 1',
                'password' => Hash::make('password'),
                'phone' => '1234567890',
                'phone_code' => '+1',
                'verify' => 1,
                'status' => 1,
            ]
        );

        $pharmacyOwner2 = User::firstOrCreate(
            ['email' => 'pharmacy2@test.com'],
            [
                'name' => 'Pharmacy Owner 2',
                'password' => Hash::make('password'),
                'phone' => '0987654321',
                'phone_code' => '+1',
                'verify' => 1,
                'status' => 1,
            ]
        );

        // Create pharmacy registrations
        $pharmacy1 = PharmacyRegistration::firstOrCreate(
            ['name' => 'Test Pharmacy 1', 'email' => 'pharmacy1@test.com'],
            [
                'owner_user_id' => $pharmacyOwner1->id,
                'phone' => '1234567890',
                'address' => '123 Main Street, Test City',
                'postcode' => '12345',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'status' => 'approved',
                'is_priority' => false,
            ]
        );

        $pharmacy2 = PharmacyRegistration::firstOrCreate(
            ['name' => 'Test Pharmacy 2', 'email' => 'pharmacy2@test.com'],
            [
                'owner_user_id' => $pharmacyOwner2->id,
                'phone' => '0987654321',
                'address' => '456 Oak Avenue, Test City',
                'postcode' => '54321',
                'latitude' => 40.7589,
                'longitude' => -73.9851,
                'status' => 'pending',
                'is_priority' => false,
            ]
        );

        $pharmacy3 = PharmacyRegistration::firstOrCreate(
            ['name' => 'Priority Pharmacy', 'email' => 'priority@test.com'],
            [
                'owner_user_id' => $pharmacyOwner1->id,
                'phone' => '5555555555',
                'address' => '789 Priority Lane, Test City',
                'postcode' => '99999',
                'latitude' => 40.7614,
                'longitude' => -73.9776,
                'status' => 'approved',
                'is_priority' => true,
            ]
        );

        // Create delivery settings
        PharmacyDeliverySetting::firstOrCreate(
            ['pharmacy_id' => $pharmacy1->id],
            [
                'delivery_type' => 'pickup_delivery',
                'delivery_radius' => 10.50,
            ]
        );

        PharmacyDeliverySetting::firstOrCreate(
            ['pharmacy_id' => $pharmacy3->id],
            [
                'delivery_type' => 'delivery_only',
                'delivery_radius' => 15.00,
            ]
        );

        // Create delivery methods
        PharmacyDeliveryMethod::firstOrCreate(
            ['pharmacy_id' => $pharmacy1->id, 'delivery_method' => 'standard'],
            ['is_active' => true]
        );

        PharmacyDeliveryMethod::firstOrCreate(
            ['pharmacy_id' => $pharmacy1->id, 'delivery_method' => 'express'],
            ['is_active' => true]
        );

        // Create medicines
        $medicine1 = MedicineMaster::firstOrCreate(
            ['name' => 'Paracetamol'],
            [
                'strength' => '500mg',
                'form' => 'Tablet',
                'status' => true,
            ]
        );

        $medicine2 = MedicineMaster::firstOrCreate(
            ['name' => 'Ibuprofen'],
            [
                'strength' => '400mg',
                'form' => 'Tablet',
                'status' => true,
            ]
        );

        $medicine3 = MedicineMaster::firstOrCreate(
            ['name' => 'Amoxicillin'],
            [
                'strength' => '250mg',
                'form' => 'Capsule',
                'status' => true,
            ]
        );

        $medicine4 = MedicineMaster::firstOrCreate(
            ['name' => 'Cough Syrup'],
            [
                'strength' => '100mg/5ml',
                'form' => 'Syrup',
                'status' => true,
            ]
        );

        // Create medicine brands
        $brand1 = MedicineBrand::firstOrCreate(
            ['medicine_id' => $medicine1->id, 'brand_name' => 'Tylenol'],
            [
                'strength' => '500mg',
                'status' => true,
            ]
        );

        $brand2 = MedicineBrand::firstOrCreate(
            ['medicine_id' => $medicine1->id, 'brand_name' => 'Panadol'],
            [
                'strength' => '500mg',
                'status' => true,
            ]
        );

        $brand3 = MedicineBrand::firstOrCreate(
            ['medicine_id' => $medicine2->id, 'brand_name' => 'Advil'],
            [
                'strength' => '400mg',
                'status' => true,
            ]
        );

        $brand4 = MedicineBrand::firstOrCreate(
            ['medicine_id' => $medicine3->id, 'brand_name' => 'Amoxil'],
            [
                'strength' => '250mg',
                'status' => true,
            ]
        );

        // Create pharmacy inventory
        PharmacyInventory::firstOrCreate(
            [
                'pharmacy_id' => $pharmacy1->id,
                'medicine_id' => $medicine1->id,
                'medicine_brand_id' => $brand1->id,
            ],
            [
                'price' => 10.50,
                'quantity' => 100,
                'low_stock_threshold' => 20,
                'stock_status' => 'in_stock',
            ]
        );

        PharmacyInventory::firstOrCreate(
            [
                'pharmacy_id' => $pharmacy1->id,
                'medicine_id' => $medicine1->id,
                'medicine_brand_id' => $brand2->id,
            ],
            [
                'price' => 9.75,
                'quantity' => 15, // Low stock
                'low_stock_threshold' => 20,
                'stock_status' => 'low_stock',
            ]
        );

        PharmacyInventory::firstOrCreate(
            [
                'pharmacy_id' => $pharmacy1->id,
                'medicine_id' => $medicine2->id,
                'medicine_brand_id' => $brand3->id,
            ],
            [
                'price' => 12.00,
                'quantity' => 0, // Out of stock
                'low_stock_threshold' => 15,
                'stock_status' => 'out_of_stock',
            ]
        );

        PharmacyInventory::firstOrCreate(
            [
                'pharmacy_id' => $pharmacy3->id,
                'medicine_id' => $medicine3->id,
                'medicine_brand_id' => $brand4->id,
            ],
            [
                'price' => 25.00,
                'quantity' => 50,
                'low_stock_threshold' => 20,
                'stock_status' => 'in_stock',
            ]
        );

        $this->command->info('Module 1 test data seeded successfully!');
        $this->command->info('Test pharmacies created:');
        $this->command->info('- Pharmacy 1 (Approved): pharmacy1@test.com');
        $this->command->info('- Pharmacy 2 (Pending): pharmacy2@test.com');
        $this->command->info('- Priority Pharmacy (Approved, Priority): priority@test.com');
        $this->command->info('All passwords: password');
    }
}
