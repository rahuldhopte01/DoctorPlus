# Pharmacy Login Information

## Important: Two Pharmacy Systems

There are **two separate pharmacy systems** in this application:

### 1. OLD System (for `/pharmacy_login`)
- Table: `pharmacy` (singular)
- Model: `App\Models\Pharmacy`
- Used by: Existing pharmacy login at `/pharmacy_login`
- Authentication: Users with "pharmacy" role + entry in `pharmacy` table

### 2. NEW System (Module 1 - Pharmacy Registration)
- Table: `pharmacies` (plural)
- Model: `App\Models\PharmacyRegistration`
- Used by: New pharmacy registration and management system
- Authentication: Will need separate integration (not yet implemented)

## Pharmacy Login Credentials

### For OLD System (`/pharmacy_login`):

**Email:** `pharmacy1@test.com`  
**Password:** `password`

This user has:
- ✅ "pharmacy" role assigned
- ✅ Entry in `pharmacy` table
- ✅ Status: Active
- ✅ Verify: 1 (verified)

### Creating Additional Test Pharmacies for OLD System

If you need more test pharmacies for the old system, you can use the existing pharmacy registration form:

1. Go to: `http://127.0.0.1:8000/pharmacy_signUp`
2. Fill in the registration form
3. The system will create both:
   - User account with "pharmacy" role
   - Entry in `pharmacy` table

Or use Tinker:

```php
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\Hash;

// Create user
$user = User::create([
    'name' => 'Pharmacy Name',
    'email' => 'pharmacy@example.com',
    'password' => Hash::make('password'),
    'phone' => '1234567890',
    'phone_code' => '+1',
    'verify' => 1,
    'status' => 1,
]);

// Assign pharmacy role
$user->assignRole('pharmacy');

// Create pharmacy entry
Pharmacy::create([
    'user_id' => $user->id,
    'name' => 'Pharmacy Name',
    'email' => 'pharmacy@example.com',
    'phone' => '1234567890',
    'address' => 'Address here',
    'image' => 'defaultUser.png',
    'status' => 1,
    'start_time' => '08:00 am',
    'end_time' => '08:00 pm',
    'commission_amount' => 10,
]);
```

## Testing NEW System (Module 1)

The NEW pharmacy registration system (`pharmacies` table) is managed by SuperAdmin:

1. **SuperAdmin Login:** `/login`
2. **View Registrations:** `/pharmacy_registrations`
3. **Approve/Reject:** From the pharmacy registration list

The NEW system doesn't have its own login yet - it's managed through the SuperAdmin panel.

## Summary

- **OLD System Login:** `/pharmacy_login`
  - Email: `pharmacy1@test.com`
  - Password: `password`
  - Uses `pharmacy` table

- **NEW System Management:** `/pharmacy_registrations` (SuperAdmin)
  - View and manage pharmacy registrations
  - Approve/reject pharmacies
  - Uses `pharmacies` table

## Next Steps for Integration

To fully integrate the NEW system, you would need to:

1. Create a login system that uses `PharmacyRegistration` table
2. Update pharmacy routes to use `PharmacyRegistration` instead of `Pharmacy`
3. Or migrate existing pharmacies from OLD to NEW system

For now, use the OLD system for pharmacy login testing.
