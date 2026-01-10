# Module 1: Pharmacy Registration & Management - Testing Guide

## Overview
This guide covers comprehensive testing for Module 1 implementation, including database structure, models, controllers, views, and the low stock notification system.

---

## 1. Database Structure Testing

### 1.1 Verify Tables Exist
Run these SQL queries or use phpMyAdmin:

```sql
-- Check all tables exist
SHOW TABLES LIKE 'pharmacies';
SHOW TABLES LIKE 'pharmacy_delivery_settings';
SHOW TABLES LIKE 'pharmacy_delivery_methods';
SHOW TABLES LIKE 'medicines';
SHOW TABLES LIKE 'medicine_brands';
SHOW TABLES LIKE 'pharmacy_inventory';

-- Verify notification table has new columns
DESCRIBE notification;
-- Should show: notification_type, pharmacy_id, pharmacy_inventory_id
```

### 1.2 Test Table Structures
```sql
-- Check pharmacies table structure
DESCRIBE pharmacies;
-- Expected columns: id, name, owner_user_id, email, phone, address, postcode, latitude, longitude, is_priority, status, created_at, updated_at

-- Check pharmacy_inventory table structure
DESCRIBE pharmacy_inventory;
-- Expected columns: id, pharmacy_id, medicine_id, medicine_brand_id, price, quantity, low_stock_threshold, stock_status, created_at, updated_at
```

### 1.3 Test Foreign Key Relationships
```sql
-- Test if foreign keys work (should not error)
SELECT p.*, u.name as owner_name 
FROM pharmacies p 
LEFT JOIN users u ON p.owner_user_id = u.id 
LIMIT 1;

SELECT pi.*, m.name as medicine_name, mb.brand_name 
FROM pharmacy_inventory pi 
JOIN medicines m ON pi.medicine_id = m.id 
JOIN medicine_brands mb ON pi.medicine_brand_id = mb.id 
LIMIT 1;
```

---

## 2. Model Testing (Tinker/Artisan)

### 2.1 Test Model Creation
Open Laravel Tinker: `php artisan tinker`

```php
// Test PharmacyRegistration model
use App\Models\PharmacyRegistration;
use App\Models\User;

// Get or create a test user
$user = User::first();

// Create a pharmacy
$pharmacy = PharmacyRegistration::create([
    'name' => 'Test Pharmacy',
    'owner_user_id' => $user->id,
    'email' => 'test@pharmacy.com',
    'phone' => '1234567890',
    'address' => '123 Test Street',
    'postcode' => '12345',
    'latitude' => 40.7128,
    'longitude' => -74.0060,
    'status' => 'pending'
]);

// Test relationships
$pharmacy->owner; // Should return User
$pharmacy->deliverySettings; // Should return null or PharmacyDeliverySetting
$pharmacy->inventory; // Should return collection

// Test scopes
PharmacyRegistration::approved()->get(); // Only approved
PharmacyRegistration::priority()->get(); // Only priority
PharmacyRegistration::status('pending')->get(); // Only pending
```

### 2.2 Test MedicineMaster Model
```php
use App\Models\MedicineMaster;

// Create medicine
$medicine = MedicineMaster::create([
    'name' => 'Paracetamol',
    'strength' => '500mg',
    'form' => 'Tablet',
    'status' => true
]);

// Test relationships
$medicine->brands; // Should return collection
$medicine->activeBrands; // Only active brands
```

### 2.3 Test MedicineBrand Model
```php
use App\Models\MedicineBrand;

// Create brand
$brand = MedicineBrand::create([
    'medicine_id' => $medicine->id,
    'brand_name' => 'Tylenol',
    'strength' => '500mg',
    'status' => true
]);

// Test relationships
$brand->medicine; // Should return MedicineMaster
$brand->inventory; // Should return collection
```

### 2.4 Test PharmacyInventory Model
```php
use App\Models\PharmacyInventory;

// Create inventory
$inventory = PharmacyInventory::create([
    'pharmacy_id' => $pharmacy->id,
    'medicine_id' => $medicine->id,
    'medicine_brand_id' => $brand->id,
    'price' => 10.50,
    'quantity' => 100,
    'low_stock_threshold' => 20
]);

// Test stock status auto-calculation
$inventory->stock_status; // Should be 'in_stock' (quantity > threshold)

// Test scopes
PharmacyInventory::lowStock()->get();
PharmacyInventory::outOfStock()->get();
PharmacyInventory::inStock()->get();

// Test updateStockStatus
$inventory->quantity = 15; // Below threshold
$inventory->save(); // Should auto-update stock_status to 'low_stock'
$inventory->stock_status; // Should be 'low_stock'
```

---

## 3. Controller & Route Testing

### 3.1 Test SuperAdmin Routes

#### Test Pharmacy Registration Routes (Must be logged in as SuperAdmin)

1. **List Pharmacies**
   ```
   GET: /pharmacy_registrations
   Expected: View with list of pharmacies
   ```

2. **View Pharmacy Details**
   ```
   GET: /pharmacy_registrations/{id}
   Expected: View with pharmacy details
   ```

3. **Approve Pharmacy**
   ```
   POST: /pharmacy_registrations/{id}/approve
   Expected: Pharmacy status changes to 'approved', redirect back with success message
   ```

4. **Reject Pharmacy**
   ```
   POST: /pharmacy_registrations/{id}/reject
   Expected: Pharmacy status changes to 'rejected', redirect back with success message
   ```

5. **Toggle Priority**
   ```
   POST: /pharmacy_registrations/{id}/toggle-priority
   Expected: Pharmacy is_priority toggles, redirect back with success message
   ```

#### Test Medicine Master Routes

1. **List Medicines**
   ```
   GET: /medicine_master
   Expected: View with list of medicines
   ```

2. **Create Medicine (Form)**
   ```
   GET: /medicine_master/create
   Expected: View with create form
   ```

3. **Store Medicine**
   ```
   POST: /medicine_master
   Data: {name: 'Test Medicine', strength: '100mg', form: 'Tablet', status: 1}
   Expected: Medicine created, redirect to list with success message
   ```

4. **Edit Medicine (Form)**
   ```
   GET: /medicine_master/{id}/edit
   Expected: View with edit form pre-filled
   ```

5. **Update Medicine**
   ```
   PUT: /medicine_master/{id}
   Data: {name: 'Updated Medicine', ...}
   Expected: Medicine updated, redirect to list with success message
   ```

6. **View Medicine**
   ```
   GET: /medicine_master/{id}
   Expected: View with medicine details and brands
   ```

7. **Delete Medicine**
   ```
   DELETE: /medicine_master/{id}
   Expected: Medicine deleted, JSON response {success: true}
   ```

### 3.2 Test Pharmacy Routes (Must be logged in as Pharmacy)

1. **List Inventory**
   ```
   GET: /pharmacy_new/inventory
   Expected: View with pharmacy inventory list
   ```

2. **Create Inventory (Form)**
   ```
   GET: /pharmacy_new/inventory/create
   Expected: View with create form
   ```

3. **Store Inventory**
   ```
   POST: /pharmacy_new/inventory
   Data: {medicine_id: 1, medicine_brand_id: 1, price: 10.50, quantity: 100, low_stock_threshold: 20}
   Expected: Inventory item created, redirect to list with success message
   ```

---

## 4. Low Stock Notification Testing

### 4.1 Test Observer Trigger

```php
// In Tinker
use App\Models\PharmacyInventory;
use App\Models\Notification;

// Create inventory with quantity below threshold
$inventory = PharmacyInventory::create([
    'pharmacy_id' => 1,
    'medicine_id' => 1,
    'medicine_brand_id' => 1,
    'price' => 10.50,
    'quantity' => 5, // Below threshold
    'low_stock_threshold' => 10
]);

// Check notifications were created
$notifications = Notification::where('pharmacy_inventory_id', $inventory->id)
    ->where('notification_type', 'low_stock')
    ->get();

// Should have notifications for:
// - Pharmacy owner (if owner_user_id exists)
// - Super admins
```

### 4.2 Test Stock Status Update

```php
// Test automatic stock status update
$inventory = PharmacyInventory::find(1);

// Set quantity to trigger low stock
$inventory->quantity = 15;
$inventory->low_stock_threshold = 20;
$inventory->save(); // Should trigger observer

// Check stock_status was updated
$inventory->refresh();
$inventory->stock_status; // Should be 'low_stock'

// Test out of stock
$inventory->quantity = 0;
$inventory->save();
$inventory->refresh();
$inventory->stock_status; // Should be 'out_of_stock'

// Test in stock
$inventory->quantity = 100;
$inventory->save();
$inventory->refresh();
$inventory->stock_status; // Should be 'in_stock'
```

---

## 5. Manual Testing Checklist

### 5.1 SuperAdmin Panel

- [ ] Navigate to `/pharmacy_registrations`
  - [ ] Verify list displays all pharmacies
  - [ ] Verify status badges show correctly (pending/approved/rejected)
  - [ ] Verify priority badge shows for priority pharmacies
  - [ ] Click on a pharmacy to view details
  - [ ] Test approve button (changes status to approved)
  - [ ] Test reject button (changes status to rejected)
  - [ ] Test toggle priority button (toggles is_priority)
  - [ ] Test delete button (removes pharmacy)

- [ ] Navigate to `/medicine_master`
  - [ ] Verify list displays all medicines
  - [ ] Click "Add New"
  - [ ] Fill form and submit (verify medicine created)
  - [ ] Click edit icon on a medicine
  - [ ] Update medicine details and submit (verify updated)
  - [ ] Click view icon to see medicine details
  - [ ] Verify brands list shows on detail page
  - [ ] Test delete button

### 5.2 Pharmacy Panel (New System)

- [ ] Navigate to `/pharmacy_new/inventory`
  - [ ] Verify list shows inventory items
  - [ ] Verify stock status badges show correctly
  - [ ] Click "Add New"
  - [ ] Select medicine and brand from dropdowns
  - [ ] Fill price, quantity, threshold
  - [ ] Submit form (verify created)
  - [ ] Edit an inventory item
  - [ ] Update quantity to trigger low stock
  - [ ] Verify notification created

### 5.3 Low Stock Notification Testing

- [ ] Create inventory with quantity = 10, threshold = 20
  - [ ] Verify notification created for pharmacy owner
  - [ ] Verify notification created for super admins
  - [ ] Check notification table for entries

- [ ] Update inventory quantity to 5 (still below threshold)
  - [ ] Verify new notification created (if not within 1 hour)

- [ ] Update inventory quantity to 100 (above threshold)
  - [ ] Verify stock_status is 'in_stock'

---

## 6. Data Validation Testing

### 6.1 Pharmacy Registration Validation

Test with invalid data:
- [ ] Empty name (should fail validation)
- [ ] Invalid email format (should fail)
- [ ] Empty address (should fail)
- [ ] Invalid status value (should fail)

### 6.2 Medicine Master Validation

Test with invalid data:
- [ ] Empty name (should fail validation)
- [ ] Name too long (should fail if max length enforced)

### 6.3 Inventory Validation

Test with invalid data:
- [ ] Negative price (should fail)
- [ ] Negative quantity (should fail)
- [ ] Duplicate medicine+brand combination (should fail)
- [ ] Invalid medicine_id (should fail)
- [ ] Invalid brand_id (should fail)

---

## 7. Integration Testing Scenarios

### Scenario 1: Complete Pharmacy Registration Flow
1. Create pharmacy registration (status: pending)
2. Admin views pharmacy details
3. Admin approves pharmacy
4. Pharmacy owner can now access inventory
5. Pharmacy adds inventory items
6. Test low stock notification triggers

### Scenario 2: Medicine & Brand Management
1. Admin creates medicine
2. Admin creates brand for medicine
3. Pharmacy adds inventory with medicine + brand
4. Verify relationships work correctly

### Scenario 3: Low Stock Workflow
1. Pharmacy creates inventory (quantity: 100, threshold: 20)
2. Pharmacy updates quantity to 15 (below threshold)
3. Verify notifications sent
4. Verify stock_status updated to 'low_stock'
5. Pharmacy restocks (quantity: 50)
6. Verify stock_status updated to 'in_stock'

---

## 8. SQL Test Data Scripts

See `database/seeders/Module1TestDataSeeder.php` for automated test data creation.

---

## 9. Common Issues & Troubleshooting

### Issue: Routes not found
**Solution:** Run `php artisan route:clear && php artisan route:cache`

### Issue: Observer not triggering
**Solution:** 
- Check AppServiceProvider boot method registers observer
- Clear cache: `php artisan cache:clear`
- Check logs: `storage/logs/laravel.log`

### Issue: Foreign key constraint errors
**Solution:** 
- Verify all referenced tables exist
- Check migration order
- Verify data integrity

### Issue: Views not found
**Solution:**
- Verify view files exist in correct directories
- Check view paths in controllers
- Run `php artisan view:clear`

---

## 10. Performance Testing

- [ ] Test with 100+ pharmacies in list
- [ ] Test with 1000+ medicines in list
- [ ] Test with 5000+ inventory items
- [ ] Check query performance (use Laravel Debugbar)
- [ ] Verify indexes are used (EXPLAIN queries)

---

## Quick Test Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear

# Run migrations (fresh)
php artisan migrate:fresh

# Seed test data (if seeder created)
php artisan db:seed --class=Module1TestDataSeeder

# Check routes
php artisan route:list | grep pharmacy_registrations
php artisan route:list | grep medicine_master
php artisan route:list | grep inventory

# Test in Tinker
php artisan tinker
# Then use PHP code from section 2
```
