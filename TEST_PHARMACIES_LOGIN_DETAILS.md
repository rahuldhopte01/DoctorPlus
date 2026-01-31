# Test Pharmacies Login Details

This document contains login credentials for test pharmacies created for development and testing purposes.

## Test Pharmacies

### Pharmacy 1 - Shipping Enabled
- **Name:** Test Pharmacy 1 - Shipping Enabled
- **Email:** pharmacy1@test.com
- **Password:** pharmacy123
- **Phone:** 1234567890
- **Address:** 123 Main Street, Test City
- **Shipping Enabled:** Yes
- **Priority:** Yes
- **Status:** Approved

### Pharmacy 2 - No Shipping
- **Name:** Test Pharmacy 2 - No Shipping
- **Email:** pharmacy2@test.com
- **Password:** pharmacy123
- **Phone:** 1234567891
- **Address:** 456 Oak Avenue, Test City
- **Shipping Enabled:** No
- **Priority:** No
- **Status:** Approved

### Pharmacy 3 - Shipping Enabled
- **Name:** Test Pharmacy 3 - Shipping Enabled
- **Email:** pharmacy3@test.com
- **Password:** pharmacy123
- **Phone:** 1234567892
- **Address:** 789 Pine Road, Test City
- **Shipping Enabled:** Yes
- **Priority:** No
- **Status:** Approved

### Pharmacy 4 - Priority Shipping
- **Name:** Test Pharmacy 4 - Priority Shipping
- **Email:** pharmacy4@test.com
- **Password:** pharmacy123
- **Phone:** 1234567893
- **Address:** 321 Elm Street, Test City
- **Shipping Enabled:** Yes
- **Priority:** Yes
- **Status:** Approved

## Login Information

**Login URL:** `http://127.0.0.1:8000/pharmacy_login` (or your local URL)

**Default Password for all test pharmacies:** `pharmacy123`

## Notes

- All pharmacies are pre-approved and ready to use
- Pharmacies 1, 3, and 4 have shipping enabled (useful for delivery orders)
- Pharmacy 2 does not have shipping enabled (useful for pickup-only testing)
- All pharmacies have working hours set from 8:00 AM to 8:00 PM for all days
- You can change passwords after logging in

## Usage

These test pharmacies can be used for:
- Testing prescription order creation
- Testing delivery vs pickup flows
- Testing pharmacy inventory management
- Testing pharmacy commission calculations
- Testing order fulfillment workflows

## Recreating Test Pharmacies

To recreate or add more test pharmacies, run:

```bash
php artisan db:seed --class=CreateTestPharmaciesSeeder
```

The seeder will skip pharmacies that already exist (based on email), so it's safe to run multiple times.
