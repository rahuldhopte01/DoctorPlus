# Integration Audit - Module 1 Pharmacy Features

## Current State Analysis

### Existing Pharmacy Routes (OLD System)
- `/pharmacy_login` - Pharmacy login
- `/pharmacy_home` - Pharmacy dashboard  
- `/medicines` - Pharmacy medicine management (OLD system - uses `medicine` table)
- `/purchased_medicines` - Medicine orders
- `/pharmacy_schedule` - Schedule management
- `/pharmacyCommission` - Commission details
- `/pharmacy_profile` - Profile management

**Model Used:** `App\Models\Pharmacy` (table: `pharmacy`)
**Medicine Model:** `App\Models\Medicine` (table: `medicine` - pharmacy-specific)

### New Pharmacy Routes (Module 1 - NEW System)
- `/pharmacy_registrations` (admin) - NEW - Pharmacy registration management
- `/medicine_master` (admin) - NEW - Global medicine master
- `/pharmacy_new/inventory` - NEW - Should be `/pharmacy/inventory`
- `/pharmacy_new/delivery-settings` - NEW - Should be `/pharmacy/delivery-settings`

**Model Used:** `App\Models\PharmacyRegistration` (table: `pharmacies`)
**Medicine Model:** `App\Models\MedicineMaster` (table: `medicines` - global)

### Key Differences

**OLD System:**
- Pharmacy-specific medicines (`medicine` table with `pharmacy_id`)
- Each pharmacy manages their own medicine list
- Direct medicine creation per pharmacy

**NEW System (Module 1):**
- Global medicine master (`medicines` table)
- Medicine brands (`medicine_brands` table)
- Pharmacy inventory (`pharmacy_inventory` table)
- Strict separation: Medicine → Brand → Inventory

## Integration Strategy

### Decision: Both Systems Can Coexist
- OLD system: Continue for existing pharmacies
- NEW system: Use for new pharmacy registration workflow
- Admin can manage both

### Integration Tasks

1. **Routes Integration:**
   - Move `/pharmacy_new/inventory` → `/pharmacy/inventory` (within pharmacy auth)
   - Move `/pharmacy_new/delivery-settings` → `/pharmacy/delivery-settings`
   - Keep `/pharmacy_registrations` in admin (already correct)
   - Keep `/medicine_master` in admin (already correct)

2. **Sidebar Integration:**
   - Add "Inventory Management" to pharmacy sidebar (NEW system)
   - Add "Pharmacies (New)" to admin sidebar (pharmacy_registrations)
   - Add "Medicine Master" to admin sidebar (medicine_master)

3. **Models:**
   - Keep both systems (they serve different purposes)
   - OLD: `Medicine` (pharmacy-specific)
   - NEW: `MedicineMaster` + `MedicineBrand` + `PharmacyInventory` (global marketplace)

4. **Controllers:**
   - Update InventoryController to use existing pharmacy auth
   - Keep PharmacyRegistrationController in admin
   - Keep MedicineMasterController in admin

## Implementation Plan

1. ✅ Audit complete
2. ⏳ Move routes to existing structure
3. ⏳ Update sidebar/navbar
4. ⏳ Update controllers for proper auth
5. ⏳ Test integration
