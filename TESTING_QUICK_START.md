# Quick Start Testing Guide

## 1. Prepare Test Environment

```bash
# Navigate to project directory
cd c:\wamp64\www\backupdoctor

# Clear all caches
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear

# Verify migrations are run
php artisan migrate:status
```

## 2. Seed Test Data

```bash
# Run the test data seeder
php artisan db:seed --class=Module1TestDataSeeder
```

This will create:
- 3 test pharmacies (1 approved, 1 pending, 1 priority)
- 4 medicines with brands
- Inventory items with different stock statuses
- Delivery settings and methods

## 3. Quick Database Verification

```bash
# Open Tinker
php artisan tinker
```

Then run:

```php
// Check pharmacies
\App\Models\PharmacyRegistration::count(); // Should be 3 or more

// Check medicines
\App\Models\MedicineMaster::count(); // Should be 4 or more

// Check inventory
\App\Models\PharmacyInventory::count(); // Should be 4 or more

// Check a pharmacy with relationships
$pharmacy = \App\Models\PharmacyRegistration::with(['owner', 'deliverySettings', 'inventory'])->first();
$pharmacy->name;
$pharmacy->owner->name;
$pharmacy->deliverySettings;
$pharmacy->inventory->count();

// Exit tinker
exit
```

## 4. Test Routes (Must be logged in)

### As SuperAdmin:

1. **Test Pharmacy Registrations List**
   ```
   URL: http://your-domain/pharmacy_registrations
   Expected: Page shows list of pharmacies with status badges
   ```

2. **Test Approve/Reject Pharmacy**
   ```
   - Click on a pending pharmacy
   - Click approve button
   - Verify status changes to "Approved"
   ```

3. **Test Medicine Master**
   ```
   URL: http://your-domain/medicine_master
   - Click "Add New"
   - Fill form: Name="Test Medicine", Strength="100mg", Form="Tablet"
   - Submit
   - Verify medicine appears in list
   ```

### As Pharmacy Owner:

1. **Test Inventory (if logged in as pharmacy owner)**
   ```
   URL: http://your-domain/pharmacy_new/inventory
   Note: You need to log in with pharmacy1@test.com / password
   ```

## 5. Test Low Stock Notification

```bash
php artisan tinker
```

```php
// Get an inventory item
$inventory = \App\Models\PharmacyInventory::where('quantity', '>', 0)->first();

// Set quantity below threshold to trigger notification
$inventory->quantity = 5;
$inventory->low_stock_threshold = 10;
$inventory->save();

// Check notifications
\App\Models\Notification::where('notification_type', 'low_stock')
    ->where('pharmacy_inventory_id', $inventory->id)
    ->count(); // Should be > 0

// Check stock status was updated
$inventory->refresh();
$inventory->stock_status; // Should be 'low_stock'
```

## 6. Manual Checklist

- [ ] Log in as SuperAdmin
- [ ] Navigate to `/pharmacy_registrations`
- [ ] Verify pharmacy list displays
- [ ] Click on a pharmacy to view details
- [ ] Test approve button
- [ ] Test reject button (on pending pharmacy)
- [ ] Test toggle priority button
- [ ] Navigate to `/medicine_master`
- [ ] Create a new medicine
- [ ] Edit the medicine
- [ ] View medicine details
- [ ] Verify brands list shows (if any)

## 7. Common Test Scenarios

### Scenario A: Approve Pharmacy
1. Go to `/pharmacy_registrations`
2. Find pharmacy with status "Pending"
3. Click on it
4. Click "Approve" button
5. Verify status changes to "Approved"
6. Verify success message appears

### Scenario B: Create Medicine
1. Go to `/medicine_master`
2. Click "Add New"
3. Fill in: Name, Strength, Form
4. Check "Status" checkbox
5. Click "Submit"
6. Verify medicine appears in list
7. Verify it shows as "Active"

### Scenario C: Low Stock Trigger
1. Open Tinker: `php artisan tinker`
2. Run code from section 5 above
3. Check notification table in database
4. Verify notifications created for pharmacy owner and admins

## 8. Verify Database Integrity

Run these SQL queries in phpMyAdmin or MySQL client:

```sql
-- Check all pharmacies
SELECT id, name, status, is_priority FROM pharmacies;

-- Check medicines and their brands
SELECT m.name as medicine, mb.brand_name as brand 
FROM medicines m 
LEFT JOIN medicine_brands mb ON m.id = mb.medicine_id;

-- Check inventory with stock status
SELECT 
    p.name as pharmacy,
    m.name as medicine,
    mb.brand_name as brand,
    pi.quantity,
    pi.low_stock_threshold,
    pi.stock_status
FROM pharmacy_inventory pi
JOIN pharmacies p ON pi.pharmacy_id = p.id
JOIN medicines m ON pi.medicine_id = m.id
JOIN medicine_brands mb ON pi.medicine_brand_id = mb.id;

-- Check low stock notifications
SELECT 
    n.id,
    n.title,
    n.notification_type,
    p.name as pharmacy_name,
    m.name as medicine_name
FROM notification n
JOIN pharmacies p ON n.pharmacy_id = p.id
JOIN pharmacy_inventory pi ON n.pharmacy_inventory_id = pi.id
JOIN medicines m ON pi.medicine_id = m.id
WHERE n.notification_type = 'low_stock';
```

## 9. Troubleshooting

### Routes not working?
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list | grep pharmacy_registrations
```

### Views not found?
```bash
php artisan view:clear
# Check files exist:
ls resources/views/superAdmin/pharmacy_registration/
ls resources/views/superAdmin/medicine_master/
```

### Models not working?
```bash
php artisan cache:clear
php artisan config:clear
composer dump-autoload
```

### Observer not triggering?
- Check `app/Providers/AppServiceProvider.php` boot method
- Verify observer is registered
- Clear cache and try again

## 10. Expected Results

After running the seeder, you should have:

- **3 Pharmacies**: 2 approved (1 priority), 1 pending
- **4 Medicines**: Paracetamol, Ibuprofen, Amoxicillin, Cough Syrup
- **4 Brands**: Tylenol, Panadol, Advil, Amoxil
- **4 Inventory Items**: Various stock statuses (in_stock, low_stock, out_of_stock)
- **2 Delivery Settings**: For approved pharmacies
- **2 Delivery Methods**: Standard and Express for pharmacy 1

All test user passwords: `password`
