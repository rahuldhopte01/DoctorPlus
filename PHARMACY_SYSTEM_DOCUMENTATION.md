# Pharmacy System Documentation

## Table of Contents
1. [Overview](#overview)
2. [Pharmacy Model & Structure](#pharmacy-model--structure)
3. [Registration & Authentication](#registration--authentication)
4. [Inventory Management](#inventory-management)
5. [Customer Purchase Flow](#customer-purchase-flow)
6. [Commission & Settlement System](#commission--settlement-system)
7. [Working Hours & Schedule](#working-hours--schedule)
8. [Pharmacy Admin Panel](#pharmacy-admin-panel)
9. [Super Admin Capabilities](#super-admin-capabilities)
10. [Key Features](#key-features)
11. [Database Schema](#database-schema)
12. [Technical Implementation](#technical-implementation)

---

## Overview

The pharmacy system is a complete e-commerce solution for managing multiple pharmacies, their inventories, orders, and commission-based settlements. It follows a marketplace model where:

- **Super Admin** manages the platform and approves pharmacies
- **Pharmacy Admins** manage their inventory and view orders
- **Customers** browse pharmacies, purchase medicines, and track orders

---

## Pharmacy Model & Structure

### Core Entity: Pharmacy Model
**Location:** `app/Models/Pharmacy.php`

### Key Fields

| Field | Type | Description |
|-------|------|-------------|
| `user_id` | Foreign Key | Links to User model for authentication |
| `name` | String | Pharmacy name |
| `email` | String | Contact email |
| `phone` | String | Contact phone |
| `address` | String | Physical address |
| `postcode` | String | Postal code |
| `lat` / `lang` | Decimal | Geographic coordinates for distance-based search |
| `start_time` / `end_time` | Time | Default working hours |
| `commission_amount` | Decimal | Commission percentage (e.g., 15%) |
| `status` | ENUM | `pending`, `approved`, `rejected` |
| `is_priority` | Boolean | Priority/featured pharmacy flag |
| `is_shipping` | Boolean | Delivery availability |
| `delivery_charges` | JSON | Delivery charge configuration |
| `image` | String | Pharmacy logo/image path |
| `description` | Text | Pharmacy description |
| `language` | String | Preferred language |

### Relationships

```php
// Pharmacy Model Relationships
belongsTo(User::class)              // Authentication
hasMany(PharmacyInventory::class)   // Inventory items
hasMany(PharmacyWorkingHour::class) // Working hours
hasMany(PurchaseMedicine::class)    // Orders
hasMany(PharmacySettle::class)      // Commission settlements
```

### Key Methods

**Distance-Based Search:**
```php
public function scopeGetByDistance($query, $lat, $lng, $radius)
{
    // Uses Haversine formula to calculate distance
    // Filters pharmacies within specified radius
}
```

---

## Registration & Authentication

### Registration Flow

#### Step 1: Pharmacy Registration Form
**Route:** `pharmacy_signUp()`  
**Controller:** `PharmacyController@pharmacy_register()`

**Process:**
1. Pharmacy fills registration form with:
   - Name, email, phone, address
   - Location coordinates (lat/lng)
   - Image upload
   - Description
2. System creates:
   - `User` account with `pharmacy` role
   - `Pharmacy` record with `status = 'pending'`
   - Default working hours for all 7 days (8 AM - 8 PM)
3. Redirects to login with "pending approval" message

#### Step 2: Admin Approval
**Route:** `approve_pharmacy()`  
**Controller:** `SuperAdmin\PharmacyController@approve_pharmacy()`

**Process:**
1. Admin reviews pending pharmacies
2. Approves or rejects registration
3. System sends email notification
4. Status changes to `approved` or `rejected`

### Login Flow

**Route:** `verify_pharmacy()`  
**Controller:** `PharmacyController@verify_pharmacy()`

**Validation Steps:**
1. ✅ Validate credentials
2. ✅ Check user has `pharmacy` role
3. ✅ Check email verification status
4. ✅ Check pharmacy approval status:
   - `approved` → Login successful → Redirect to dashboard
   - `pending` → Error: "Registration pending approval"
   - `rejected` → Error: "Pharmacy rejected"

### OTP Verification

If email not verified (`verify = 0`):
1. System sends OTP via email/SMS
2. Pharmacy enters OTP
3. System verifies and sets `verify = 1`
4. Proceeds to status check

---

## Inventory Management

### Two-Tier Architecture

#### Tier 1: Global Medicine Catalog (Admin Managed)
**Model:** `app/Models/Medicine.php`

**Purpose:** Master list of all available medicines

**Fields:**
- `name` - Medicine name
- `strength` - Dosage strength (e.g., "500mg")
- `form` - Form type (tablet, syrup, injection, etc.)
- `brand_id` - Foreign key to `medicine_brands`
- `status` - Active/inactive
- `description` - Medicine details

**Relationships:**
```php
belongsTo(MedicineBrand::class)
hasMany(PharmacyInventory::class)
belongsToMany(Category::class)
```

#### Tier 2: Pharmacy Inventory (Pharmacy Managed)
**Model:** `app/Models/PharmacyInventory.php`  
**Controller:** `Pharmacy\PharmacyInventoryController.php`

**Purpose:** Pharmacy-specific pricing and stock management

**Fields:**
- `pharmacy_id` - Which pharmacy
- `medicine_id` - Which medicine (from global catalog)
- `brand_id` - Medicine brand
- `price` - Pharmacy sets their own price
- `quantity` - Current stock level
- `low_stock_threshold` - Alert threshold

**Stock Status Methods:**
```php
public function getStockStatusAttribute()
{
    if ($this->quantity <= 0) return 'out_of_stock';
    if ($this->quantity <= $this->low_stock_threshold) return 'low_stock';
    return 'in_stock';
}

public function isLowStock() { ... }
public function isOutOfStock() { ... }
```

### Inventory Management Workflow

```
┌──────────────────────────────────────────────────────┐
│ STEP 1: Super Admin Creates Global Medicine         │
├──────────────────────────────────────────────────────┤
│ - Add medicine name, strength, form                 │
│ - Select brand                                       │
│ - Add description                                    │
│ - Medicine available to all pharmacies              │
└──────────────┬───────────────────────────────────────┘
               │
               ↓
┌──────────────────────────────────────────────────────┐
│ STEP 2: Pharmacy Adds to Their Inventory            │
├──────────────────────────────────────────────────────┤
│ - Browse global medicine catalog                    │
│ - Select medicines to add                           │
│ - Set price (pharmacy decides)                      │
│ - Set initial stock quantity                        │
│ - Set low stock threshold                           │
└──────────────┬───────────────────────────────────────┘
               │
               ↓
┌──────────────────────────────────────────────────────┐
│ STEP 3: Customers See Pharmacy-Specific Inventory   │
├──────────────────────────────────────────────────────┤
│ - Each pharmacy has different prices               │
│ - Each pharmacy has different stock                 │
│ - Customers compare across pharmacies               │
└──────────────┬───────────────────────────────────────┘
               │
               ↓
┌──────────────────────────────────────────────────────┐
│ STEP 4: Stock Updates Automatically                 │
├──────────────────────────────────────────────────────┤
│ - Order placed → Stock decreases                    │
│ - Low stock alert triggered                         │
│ - Pharmacy restocks manually                        │
└──────────────────────────────────────────────────────┘
```

### Pharmacy Inventory CRUD Operations

**List Inventory:** `GET /pharmacy/inventory`
- View all medicines in pharmacy's inventory
- See stock status, prices, quantities

**Add Medicine:** `POST /pharmacy/inventory`
- Select from global medicine catalog
- Set price and quantity
- Set low stock threshold

**Update Medicine:** `PUT /pharmacy/inventory/{id}`
- Update price
- Update quantity
- Update low stock threshold

**Remove Medicine:** `DELETE /pharmacy/inventory/{id}`
- Remove from pharmacy's inventory
- Does not delete from global catalog

---

## Customer Purchase Flow

### Complete Order Journey

#### Step 1: Browse Pharmacies
**Route:** `WebsiteController@pharmacy()`

**Features:**
- **Distance Filter:** Find pharmacies within X km radius
- **Name Search:** Search by pharmacy name
- **Currently Open Filter:** Show only open pharmacies based on schedule
- **Priority Display:** Priority pharmacies shown first

**Distance Calculation:**
```php
// Uses Haversine formula
$distance = Pharmacy::getByDistance($userLat, $userLng, $radius)->get();
```

#### Step 2: View Pharmacy Products
**Route:** `WebsiteController@pharmacyProduct($pharmacy_id)`

**Features:**
- List all medicines in pharmacy's inventory
- Filter by category
- Show prices (pharmacy-specific)
- Show availability status
- Display medicine details (strength, form, brand)

#### Step 3: Add to Cart
**Route:** `WebsiteController@addCart()`

**Validation:**
```php
// 1. Check if medicine exists in pharmacy inventory
// 2. Validate stock availability
// 3. Check if cart has items from different pharmacy (not allowed)
// 4. Update quantity if item already in cart
// 5. Track reserved stock (use_stock)
```

**Cart Storage:**
- Session-based cart
- Format: `session()->get('cart')`
- Stores: medicine_id, pharmacy_id, quantity, price

#### Step 4: View/Modify Cart
**Route:** `WebsiteController@viewCart()`

**Features:**
- View all cart items
- Update quantities (with stock validation)
- Remove items
- Calculate subtotal
- Show pharmacy info

#### Step 5: Checkout
**Route:** `WebsiteController@checkout()`

**Process:**
1. Select/add delivery address
2. Calculate delivery charges (if pharmacy has `is_shipping = 1`)
3. Upload prescription (if required)
4. Review order summary
5. Select payment method (COD/Online)

**Delivery Charge Calculation:**
```php
// Based on distance and pharmacy's delivery_charges JSON
// Format: [{"min": 0, "max": 5, "charges": 50}, ...]
```

#### Step 6: Place Order
**Route:** `WebsiteController@bookMedicine()`

**Complete Order Creation Process:**

```php
// 1. Create PurchaseMedicine (Order Header)
$order = PurchaseMedicine::create([
    'medicine_id' => generateOrderId(),
    'user_id' => auth()->user()->id,
    'pharmacy_id' => $pharmacy_id,
    'amount' => $total_amount,
    'payment_type' => $payment_type, // COD or Online
    'payment_status' => $payment_status,
    'admin_commission' => $admin_commission,
    'pharmacy_commission' => $pharmacy_commission,
    'address_id' => $address_id,
    'delivery_charge' => $delivery_charge,
]);

// 2. Create MedicineChild (Line Items)
foreach ($cart_items as $item) {
    MedicineChild::create([
        'purchase_medicine_id' => $order->id,
        'medicine_id' => $item['medicine_id'],
        'price' => $item['price'],
        'qty' => $item['quantity'],
    ]);
    
    // 3. Update Inventory Stock
    $inventory = PharmacyInventory::find($item['inventory_id']);
    $inventory->quantity -= $item['quantity'];
    $inventory->save();
}

// 4. Create Commission Settlement Record
PharmacySettle::create([
    'purchase_medicine_id' => $order->id,
    'pharmacy_id' => $pharmacy_id,
    'admin_amount' => $admin_commission,
    'pharmacy_amount' => $pharmacy_commission,
    'payment' => $payment_type, // 0=COD, 1=Online
    'pharmacy_status' => 0, // Unpaid
]);

// 5. Clear Cart Session
session()->forget('cart');

// 6. Send Notifications
// - Email to customer
// - Notification to pharmacy
```

### Order Models

#### PurchaseMedicine Model
**Location:** `app/Models/PurchaseMedicine.php`

**Fields:**
- `medicine_id` - Order reference ID (not FK, just order number)
- `user_id` - Customer
- `pharmacy_id` - Which pharmacy
- `amount` - Total order amount
- `payment_type` - Payment method
- `payment_status` - Payment status
- `admin_commission` - Platform's share
- `pharmacy_commission` - Pharmacy's share
- `address_id` - Delivery address
- `delivery_charge` - Shipping cost
- `shipping_at` - Delivery date/time
- `pdf` - Prescription upload

**Relationships:**
```php
belongsTo(User::class)
belongsTo(Pharmacy::class)
belongsTo(UserAddress::class)
hasMany(MedicineChild::class)
```

#### MedicineChild Model
**Location:** `app/Models/MedicineChild.php`

**Purpose:** Order line items (each medicine in the order)

**Fields:**
- `purchase_medicine_id` - Parent order
- `medicine_id` - Which medicine
- `price` - Price at time of purchase
- `qty` - Quantity ordered

---

## Commission & Settlement System

### Commission Calculation Logic

**Formula:**
```php
// Example: Order Total = $100, Commission = 15%

$admin_commission = ($amount * $commission_amount) / 100;
// $admin_commission = ($100 * 15) / 100 = $15

$pharmacy_commission = $amount - $admin_commission;
// $pharmacy_commission = $100 - $15 = $85
```

**Where `commission_amount` is:**
- Stored in `pharmacy.commission_amount` field
- Set per pharmacy (can vary)
- Typically between 10-20%

### Settlement Records

#### PharmacySettle Model
**Location:** `app/Models/PharmacySettle.php`

**Purpose:** Track commission for each order

**Fields:**
- `purchase_medicine_id` - Links to order
- `pharmacy_id` - Which pharmacy
- `admin_amount` - Platform's commission
- `pharmacy_amount` - Pharmacy's earnings
- `payment` - 0 = COD, 1 = Online Payment
- `pharmacy_status` - 0 = Unpaid, 1 = Paid

**Relationships:**
```php
belongsTo(PurchaseMedicine::class, 'purchase_medicine_id')
belongsTo(Pharmacy::class)
```

### Balance Calculation Logic

**Two Payment Scenarios:**

#### Scenario A: Cash on Delivery (COD)
```
Customer pays pharmacy directly: $100
Pharmacy owes admin commission: $15
Pharmacy keeps: $85

Settlement Balance: +$15 (pharmacy owes admin)
```

#### Scenario B: Online Payment
```
Customer pays platform: $100
Admin keeps commission: $15
Admin owes pharmacy: $85

Settlement Balance: -$85 (admin owes pharmacy)
```

#### Net Balance Calculation
```php
// For a settlement period:
$cod_orders_admin_amount = PharmacySettle::where('payment', 0)
    ->sum('admin_amount');
    
$online_orders_pharmacy_amount = PharmacySettle::where('payment', 1)
    ->sum('pharmacy_amount');

$net_balance = $cod_orders_admin_amount - $online_orders_pharmacy_amount;

// If positive: Pharmacy owes admin
// If negative: Admin owes pharmacy
```

### Commission Dashboard

**Route:** `PharmacyController@pharmacyCommission()`

**Features:**

1. **7-Day Sales Summary**
   - Total sales in last 7 days
   - Commission breakdown
   - Payment method distribution

2. **Settlement Periods (35 days in 10-day chunks)**
   ```
   Period 1: Day 0-10
   Period 2: Day 11-20
   Period 3: Day 21-30
   Period 4: Day 31-35
   ```

3. **Balance Tracking**
   - COD balance (pharmacy owes)
   - Online balance (admin owes)
   - Net balance
   - Payment history

4. **Settlement Status**
   - Unpaid settlements
   - Paid settlements
   - Payment dates

### Admin Settlement Processing

**Route:** `SuperAdmin\PharmacyController@show_pharmacy_settalement()`

**Features:**
- View settlement details for date range
- See all transactions
- Calculate net balance
- Mark settlements as paid
- Generate settlement reports

---

## Working Hours & Schedule

### PharmacyWorkingHour Model
**Location:** `app/Models/PharmacyWorkingHour.php`

### Structure

**One record per day per pharmacy (7 records total)**

**Fields:**
- `pharmacy_id` - Which pharmacy
- `day_index` - 0=Sunday, 1=Monday, ..., 6=Saturday
- `period_list` - JSON array of time periods
- `status` - 1=Active, 0=Inactive

### Period List Format

**JSON Structure:**
```json
[
    {
        "start_time": "08:00 am",
        "end_time": "12:00 pm"
    },
    {
        "start_time": "02:00 pm",
        "end_time": "08:00 pm"
    }
]
```

**Supports:**
- Multiple time periods per day (split shifts)
- Different hours for each day
- Enable/disable specific days

### Default Setup

When pharmacy registers, system creates:
```php
// For each day (Sunday to Saturday)
PharmacyWorkingHour::create([
    'pharmacy_id' => $pharmacy->id,
    'day_index' => $i, // 0-6
    'period_list' => json_encode([
        ['start_time' => '08:00 am', 'end_time' => '08:00 pm']
    ]),
    'status' => 1,
]);
```

### Schedule Management

**Routes:**
- `GET /pharmacy/schedule` - View schedule
- `POST /pharmacy/edit-timeslot` - Edit specific day
- `POST /pharmacy/update-timeslot` - Save changes

**Features:**
1. **View All Days:** See complete weekly schedule
2. **Edit Per Day:** Modify hours for specific days
3. **Multiple Shifts:** Add multiple time periods per day
4. **Toggle Days:** Enable/disable specific days
5. **Visual Calendar:** Calendar view of schedule

### "Currently Open" Filter

**Logic:**
```php
// Get current day and time
$current_day = date('w'); // 0-6
$current_time = date('H:i'); // 24-hour format

// Find pharmacies open now
$open_pharmacies = Pharmacy::whereHas('workingHours', function($q) use ($current_day, $current_time) {
    $q->where('day_index', $current_day)
      ->where('status', 1)
      ->whereRaw("JSON_SEARCH(period_list, 'one', ?) IS NOT NULL", [$current_time]);
})->get();
```

---

## Pharmacy Admin Panel

### Dashboard
**Route:** `PharmacyController@pharmacy_home()`

**Key Metrics:**
- 📊 Today's Sales Total
- 💊 Total Medicines in Inventory
- 📈 Revenue Charts (7-day trend)
- 📦 Recent Orders
- ⚠️ Low Stock Alerts

### Inventory Management
**Controller:** `PharmacyInventoryController`

**Features:**

| Action | Route | Description |
|--------|-------|-------------|
| List All | `GET /pharmacy/inventory` | View all medicines |
| Add Medicine | `GET/POST /pharmacy/inventory/create` | Add from catalog |
| Edit Medicine | `GET/PUT /pharmacy/inventory/{id}/edit` | Update price/stock |
| Delete Medicine | `DELETE /pharmacy/inventory/{id}` | Remove from inventory |
| Stock Status | Auto | In Stock / Low Stock / Out of Stock |

**Inventory Table Columns:**
- Medicine Name
- Brand
- Strength & Form
- Current Price
- Stock Quantity
- Stock Status (badge)
- Actions (Edit/Delete)

### Order Management
**Route:** `PharmacyController@purchased_medicines()`

**Features:**
- View all orders received
- Filter by date range
- Filter by status
- View order details:
  - Customer information
  - Order items
  - Payment method
  - Delivery address
  - Prescription (if uploaded)
- Order status tracking

**Order Details View:**
**Route:** `PharmacyController@display_purchase_medicine($id)`

Shows:
- Order ID and date
- Customer name, email, phone
- Delivery address
- Order items table (medicine, qty, price)
- Subtotal, delivery charge, total
- Payment method and status
- Prescription download

### Commission Dashboard
**Route:** `PharmacyController@pharmacyCommission()`

**Sections:**

1. **Quick Stats**
   - Total Earnings
   - Pending Settlements
   - Paid Settlements

2. **7-Day Sales Graph**
   - Daily sales visualization
   - Commission breakdown per day

3. **Settlement Periods**
   - 35 days divided into periods
   - Each period shows:
     - Date range
     - Total sales
     - Admin commission
     - Pharmacy earnings
     - Payment status

4. **Balance Summary**
   - COD Balance (pharmacy owes)
   - Online Balance (admin owes)
   - Net Balance

### Schedule Management
**Route:** `PharmacyController@pharmacy_schedule()`

**Interface:**
- Weekly calendar view
- Each day editable
- Add/remove time periods
- Toggle day on/off
- Save changes per day

**Edit Timeslot Modal:**
- Select day
- Add multiple periods
- Set start and end times
- Enable/disable day
- Save button

### Profile Management
**Route:** `PharmacyController@pharmacy_profile()`

**Editable Fields:**

**Basic Information:**
- Pharmacy Name
- Email
- Phone Number
- Address
- Postcode
- Location (Lat/Lng)

**Operating Details:**
- Default Start Time
- Default End Time
- Commission Percentage (view only)

**Delivery Settings:**
- Enable/Disable Shipping (`is_shipping`)
- Delivery Charges Configuration:
  ```json
  [
    {"min": 0, "max": 5, "charges": 50},
    {"min": 5, "max": 10, "charges": 100}
  ]
  ```

**Branding:**
- Upload Pharmacy Image/Logo
- Pharmacy Description
- Language Preference

---

## Super Admin Capabilities

### Pharmacy Management
**Controller:** `SuperAdmin\PharmacyController`

#### View All Pharmacies
**Route:** `GET /admin/pharmacy`

**Features:**
- List all pharmacies
- Filter by status (pending/approved/rejected)
- Search by name
- Sortable columns
- Pagination

**Table Columns:**
- Pharmacy Name
- Email & Phone
- Location
- Status Badge
- Priority Flag
- Commission %
- Actions (View/Edit/Delete/Approve)

#### Create Pharmacy (Pre-Approved)
**Route:** `POST /admin/pharmacy`

**Differences from Pharmacy Registration:**
- Created with `status = 'approved'` (no approval needed)
- Admin can set commission percentage
- Can set priority flag
- Full access to all settings

#### Approve/Reject Pharmacy
**Routes:**
- `POST /admin/pharmacy/approve` - Approve pharmacy
- `POST /admin/pharmacy/reject` - Reject pharmacy

**Approval Process:**
1. Admin reviews pending pharmacy
2. Clicks "Approve" or "Reject"
3. System updates status
4. Sends email notification to pharmacy
5. If approved, pharmacy can login

#### Toggle Priority Status
**Route:** `POST /admin/pharmacy/toggle-priority`

**Purpose:**
- Featured pharmacies shown first
- Marketing/partnership feature
- Quick toggle on/off

#### View Pharmacy Details
**Route:** `GET /admin/pharmacy/{id}`

**Shows:**
- Complete pharmacy information
- All orders received
- Total revenue
- Commission breakdown
- Working hours
- Inventory summary

### Commission & Settlement Management

#### Pharmacy Commission View
**Route:** `GET /admin/pharmacy/{id}/commission`

**Features:**
- View pharmacy's commission structure
- See all settlements
- Calculate net balance
- Filter by date range
- Export reports

#### Settlement Details
**Route:** `GET /admin/pharmacy/{id}/settlement`

**Shows:**
- Settlement period details
- All orders in period
- COD vs Online breakdown
- Balance calculation
- Payment status
- Mark as paid functionality

**Settlement Actions:**
- Mark settlement as paid
- Add payment notes
- Export settlement report
- Send settlement email

### Pharmacy Schedule View
**Route:** `GET /admin/pharmacy/{id}/schedule`

**Purpose:**
- View pharmacy working hours
- Monitor pharmacy availability
- Verify schedule accuracy

---

## Key Features

### 🗺️ Distance-Based Search

**Implementation:**
```php
// Haversine Formula in MySQL
public function scopeGetByDistance($query, $lat, $lng, $radius)
{
    return $query->selectRaw("
        *,
        (
            6371 * acos(
                cos(radians(?)) * 
                cos(radians(lat)) * 
                cos(radians(lang) - radians(?)) + 
                sin(radians(?)) * 
                sin(radians(lat))
            )
        ) AS distance
    ", [$lat, $lng, $lat])
    ->having('distance', '<', $radius)
    ->orderBy('distance', 'asc');
}
```

**Usage:**
- User enters location or uses GPS
- System finds pharmacies within X km
- Results sorted by distance (nearest first)

### ⭐ Priority Pharmacies

**Purpose:**
- Featured/promoted pharmacies
- Partnership/premium pharmacies
- Advertising feature

**Implementation:**
- `is_priority` boolean flag in `pharmacy` table
- Admin can toggle via dashboard
- Priority pharmacies shown first in search results
- Visual indicator (star/badge) in listings

**Business Logic:**
```php
// In search query
$pharmacies = Pharmacy::where('status', 'approved')
    ->orderBy('is_priority', 'desc')  // Priority first
    ->orderBy('distance', 'asc')      // Then by distance
    ->get();
```

### 🚚 Delivery System

**Configuration:**
Each pharmacy can configure:
1. **Enable/Disable Shipping:** `is_shipping` flag
2. **Delivery Charges:** JSON-based distance tiers

**Delivery Charges Format:**
```json
[
    {
        "min": 0,
        "max": 5,
        "charges": 50,
        "currency": "SAR"
    },
    {
        "min": 5,
        "max": 10,
        "charges": 100,
        "currency": "SAR"
    },
    {
        "min": 10,
        "max": 999,
        "charges": 150,
        "currency": "SAR"
    }
]
```

**Charge Calculation:**
```php
// Calculate distance between pharmacy and delivery address
$distance = calculateDistance(
    $pharmacy->lat, $pharmacy->lang,
    $address->lat, $address->lang
);

// Find matching tier
foreach ($delivery_charges as $tier) {
    if ($distance >= $tier['min'] && $distance < $tier['max']) {
        $charge = $tier['charges'];
        break;
    }
}
```

**Pickup Option:**
- If `is_shipping = 0`, only pickup available
- Customer must collect from pharmacy
- No delivery charge applied

### 📦 Stock Management

**Real-Time Tracking:**
- Stock decreases automatically on order
- Reserved stock prevents overselling
- Real-time availability display

**Stock Status System:**
```php
public function getStockStatusAttribute()
{
    if ($this->quantity <= 0) {
        return 'out_of_stock';
    } elseif ($this->quantity <= $this->low_stock_threshold) {
        return 'low_stock';
    } else {
        return 'in_stock';
    }
}
```

**Low Stock Alerts:**
- Pharmacy sets threshold per medicine
- Visual indicators in admin panel
- Email alerts (optional)
- Dashboard warning count

**Overselling Prevention:**
```php
// When adding to cart
if ($cart_quantity > $available_stock) {
    return error('Insufficient stock');
}

// Reserve stock in session
session()->put("reserved_stock.{$medicine_id}", $cart_quantity);
```

### 💰 Commission Tracking

**Automatic Calculation:**
- Commission calculated on every order
- Based on pharmacy's commission percentage
- Split recorded in `PharmacySettle` table

**Settlement Periods:**
- 35-day rolling window
- Divided into 10-day periods
- Automatic period generation
- Balance tracking per period

**Payment Flow:**

**For COD Orders:**
```
1. Customer pays pharmacy: $100
2. Pharmacy keeps: $85
3. Pharmacy owes admin: $15
4. Admin invoices pharmacy
5. Pharmacy pays admin
6. Settlement marked as paid
```

**For Online Orders:**
```
1. Customer pays platform: $100
2. Platform keeps: $15
3. Platform owes pharmacy: $85
4. Platform pays pharmacy
5. Settlement marked as paid
```

### 🌍 Multi-Language Support

**Implementation:**
- Each pharmacy sets preferred language
- Affects pharmacy admin panel UI
- Uses Laravel localization
- Supports multiple language files

**Available Languages:**
- English (`en`)
- Arabic (`ar`)
- Configurable in pharmacy profile

---

## Database Schema

### Core Tables

#### 1. `pharmacy` Table
```sql
CREATE TABLE `pharmacy` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(20) NOT NULL,
    `address` text NOT NULL,
    `postcode` varchar(10) DEFAULT NULL,
    `lat` decimal(10, 7) NOT NULL,
    `lang` decimal(10, 7) NOT NULL,
    `start_time` time NOT NULL,
    `end_time` time NOT NULL,
    `commission_amount` decimal(8, 2) NOT NULL DEFAULT 15.00,
    `status` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `is_priority` tinyint(1) NOT NULL DEFAULT 0,
    `is_shipping` tinyint(1) NOT NULL DEFAULT 0,
    `delivery_charges` json DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `language` varchar(10) DEFAULT 'en',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `pharmacy_user_id_foreign` (`user_id`),
    CONSTRAINT `pharmacy_user_id_foreign` 
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

#### 2. `pharmacy_inventory` Table
```sql
CREATE TABLE `pharmacy_inventory` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `pharmacy_id` bigint(20) UNSIGNED NOT NULL,
    `medicine_id` bigint(20) UNSIGNED NOT NULL,
    `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
    `price` decimal(10, 2) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 0,
    `low_stock_threshold` int(11) NOT NULL DEFAULT 10,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `pharmacy_inventory_pharmacy_id_foreign` (`pharmacy_id`),
    KEY `pharmacy_inventory_medicine_id_foreign` (`medicine_id`),
    KEY `pharmacy_inventory_brand_id_foreign` (`brand_id`),
    CONSTRAINT `pharmacy_inventory_pharmacy_id_foreign` 
        FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacy` (`id`) ON DELETE CASCADE,
    CONSTRAINT `pharmacy_inventory_medicine_id_foreign` 
        FOREIGN KEY (`medicine_id`) REFERENCES `medicine` (`id`) ON DELETE CASCADE,
    CONSTRAINT `pharmacy_inventory_brand_id_foreign` 
        FOREIGN KEY (`brand_id`) REFERENCES `medicine_brands` (`id`) ON DELETE SET NULL
);
```

#### 3. `pharmacy_working_hour` Table
```sql
CREATE TABLE `pharmacy_working_hour` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `pharmacy_id` bigint(20) UNSIGNED NOT NULL,
    `day_index` tinyint(4) NOT NULL COMMENT '0=Sunday, 6=Saturday',
    `period_list` json NOT NULL,
    `status` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `pharmacy_working_hour_pharmacy_id_foreign` (`pharmacy_id`),
    CONSTRAINT `pharmacy_working_hour_pharmacy_id_foreign` 
        FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacy` (`id`) ON DELETE CASCADE
);
```

#### 4. `purchase_medicine` Table
```sql
CREATE TABLE `purchase_medicine` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `medicine_id` varchar(255) NOT NULL COMMENT 'Order Reference Number',
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `pharmacy_id` bigint(20) UNSIGNED NOT NULL,
    `amount` decimal(10, 2) NOT NULL,
    `payment_type` varchar(50) NOT NULL,
    `payment_status` varchar(50) NOT NULL,
    `admin_commission` decimal(10, 2) NOT NULL,
    `pharmacy_commission` decimal(10, 2) NOT NULL,
    `address_id` bigint(20) UNSIGNED DEFAULT NULL,
    `delivery_charge` decimal(10, 2) DEFAULT 0.00,
    `shipping_at` datetime DEFAULT NULL,
    `pdf` varchar(255) DEFAULT NULL COMMENT 'Prescription',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `purchase_medicine_user_id_foreign` (`user_id`),
    KEY `purchase_medicine_pharmacy_id_foreign` (`pharmacy_id`),
    KEY `purchase_medicine_address_id_foreign` (`address_id`),
    CONSTRAINT `purchase_medicine_user_id_foreign` 
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `purchase_medicine_pharmacy_id_foreign` 
        FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacy` (`id`) ON DELETE CASCADE,
    CONSTRAINT `purchase_medicine_address_id_foreign` 
        FOREIGN KEY (`address_id`) REFERENCES `user_address` (`id`) ON DELETE SET NULL
);
```

#### 5. `medicine_child` Table
```sql
CREATE TABLE `medicine_child` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `purchase_medicine_id` bigint(20) UNSIGNED NOT NULL,
    `medicine_id` bigint(20) UNSIGNED NOT NULL,
    `price` decimal(10, 2) NOT NULL,
    `qty` int(11) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `medicine_child_purchase_medicine_id_foreign` (`purchase_medicine_id`),
    KEY `medicine_child_medicine_id_foreign` (`medicine_id`),
    CONSTRAINT `medicine_child_purchase_medicine_id_foreign` 
        FOREIGN KEY (`purchase_medicine_id`) REFERENCES `purchase_medicine` (`id`) ON DELETE CASCADE,
    CONSTRAINT `medicine_child_medicine_id_foreign` 
        FOREIGN KEY (`medicine_id`) REFERENCES `medicine` (`id`) ON DELETE CASCADE
);
```

#### 6. `pharmacy_settle` Table
```sql
CREATE TABLE `pharmacy_settle` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `purchase_medicine_id` bigint(20) UNSIGNED NOT NULL,
    `pharmacy_id` bigint(20) UNSIGNED NOT NULL,
    `admin_amount` decimal(10, 2) NOT NULL,
    `pharmacy_amount` decimal(10, 2) NOT NULL,
    `payment` tinyint(1) NOT NULL COMMENT '0=COD, 1=Online',
    `pharmacy_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Unpaid, 1=Paid',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `pharmacy_settle_purchase_medicine_id_foreign` (`purchase_medicine_id`),
    KEY `pharmacy_settle_pharmacy_id_foreign` (`pharmacy_id`),
    CONSTRAINT `pharmacy_settle_purchase_medicine_id_foreign` 
        FOREIGN KEY (`purchase_medicine_id`) REFERENCES `purchase_medicine` (`id`) ON DELETE CASCADE,
    CONSTRAINT `pharmacy_settle_pharmacy_id_foreign` 
        FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacy` (`id`) ON DELETE CASCADE
);
```

#### 7. `medicine` Table
```sql
CREATE TABLE `medicine` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `strength` varchar(100) DEFAULT NULL,
    `form` varchar(100) DEFAULT NULL COMMENT 'Tablet, Syrup, Injection, etc.',
    `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
    `status` tinyint(1) NOT NULL DEFAULT 1,
    `description` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `medicine_brand_id_foreign` (`brand_id`),
    CONSTRAINT `medicine_brand_id_foreign` 
        FOREIGN KEY (`brand_id`) REFERENCES `medicine_brands` (`id`) ON DELETE SET NULL
);
```

#### 8. `medicine_brands` Table
```sql
CREATE TABLE `medicine_brands` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `image` varchar(255) DEFAULT NULL,
    `status` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
);
```

### Relationship Diagram

```
┌─────────────┐
│    users    │
└──────┬──────┘
       │ 1:1
       ↓
┌─────────────┐          ┌──────────────────┐
│  pharmacy   │──────────│ pharmacy_working │
│             │ 1:many   │      _hour       │
└──────┬──────┘          └──────────────────┘
       │ 1:many
       ↓
┌──────────────────┐     ┌─────────────┐
│ pharmacy_        │─────│  medicine   │
│   inventory      │many:1│             │
└──────┬───────────┘     └──────┬──────┘
       │                        │
       │                        │ many:1
       │                        ↓
       │                 ┌─────────────────┐
       │                 │ medicine_brands │
       │                 └─────────────────┘
       │
┌──────┴──────┐
│  pharmacy   │
└──────┬──────┘
       │ 1:many
       ↓
┌──────────────────┐     ┌─────────────┐
│ purchase_        │─────│    users    │
│   medicine       │many:1│ (customers) │
└──────┬───────────┘     └─────────────┘
       │ 1:many
       ↓
┌──────────────────┐
│ medicine_child   │
└──────────────────┘
       │
       │ 1:1
       ↓
┌──────────────────┐
│ pharmacy_settle  │
└──────────────────┘
```

---

## Technical Implementation

### Controllers

#### 1. Pharmacy Panel Controllers
**Namespace:** `App\Http\Controllers\Pharmacy`

**PharmacyController.php:**
- Authentication (login, register, OTP)
- Dashboard
- Profile management
- Commission dashboard
- Order management
- Schedule management

**PharmacyInventoryController.php:**
- Inventory CRUD operations
- Stock management
- Price updates

#### 2. Super Admin Controllers
**Namespace:** `App\Http\Controllers\SuperAdmin`

**PharmacyController.php:**
- Pharmacy CRUD
- Approval/rejection
- Commission management
- Settlement processing

#### 3. Website Controllers
**Namespace:** `App\Http\Controllers`

**WebsiteController.php:**
- Browse pharmacies
- View pharmacy products
- Cart management
- Checkout
- Order placement

### Routes

#### Pharmacy Panel Routes
```php
// Authentication
Route::get('/pharmacy/login', 'PharmacyController@pharmacyLogin');
Route::post('/pharmacy/verify', 'PharmacyController@verify_pharmacy');
Route::get('/pharmacy/signup', 'PharmacyController@pharmacy_signUp');
Route::post('/pharmacy/register', 'PharmacyController@pharmacy_register');

// Dashboard (auth:pharmacy middleware)
Route::get('/pharmacy/home', 'PharmacyController@pharmacy_home');

// Inventory
Route::resource('/pharmacy/inventory', 'PharmacyInventoryController');

// Orders
Route::get('/pharmacy/orders', 'PharmacyController@purchased_medicines');
Route::get('/pharmacy/orders/{id}', 'PharmacyController@display_purchase_medicine');

// Commission
Route::get('/pharmacy/commission', 'PharmacyController@pharmacyCommission');

// Schedule
Route::get('/pharmacy/schedule', 'PharmacyController@pharmacy_schedule');
Route::post('/pharmacy/update-schedule', 'PharmacyController@update_pharmacy_timeslot');

// Profile
Route::get('/pharmacy/profile', 'PharmacyController@pharmacy_profile');
Route::post('/pharmacy/profile', 'PharmacyController@update_pharmacy_profile');
```

#### Super Admin Routes
```php
// Pharmacy Management
Route::resource('/admin/pharmacy', 'SuperAdmin\PharmacyController');
Route::post('/admin/pharmacy/approve', 'SuperAdmin\PharmacyController@approve_pharmacy');
Route::post('/admin/pharmacy/reject', 'SuperAdmin\PharmacyController@reject_pharmacy');
Route::post('/admin/pharmacy/toggle-priority', 'SuperAdmin\PharmacyController@toggle_priority');

// Commission & Settlement
Route::get('/admin/pharmacy/{id}/commission', 'SuperAdmin\PharmacyController@pharmacy_commission');
Route::get('/admin/pharmacy/{id}/settlement', 'SuperAdmin\PharmacyController@show_pharmacy_settalement');
```

#### Website Routes (Customer Facing)
```php
// Browse
Route::get('/pharmacies', 'WebsiteController@pharmacy');
Route::get('/pharmacy/{id}/products', 'WebsiteController@pharmacyProduct');

// Cart
Route::post('/cart/add', 'WebsiteController@addCart');
Route::get('/cart', 'WebsiteController@viewCart');
Route::post('/cart/update', 'WebsiteController@updateCart');
Route::post('/cart/remove', 'WebsiteController@removeCart');

// Checkout
Route::get('/checkout', 'WebsiteController@checkout');
Route::post('/order/place', 'WebsiteController@bookMedicine');
```

### Middleware

**Pharmacy Authentication:**
```php
// In Kernel.php
'pharmacy' => \App\Http\Middleware\PharmacyMiddleware::class,

// Middleware checks:
// 1. User is authenticated
// 2. User has 'pharmacy' role
// 3. Pharmacy is approved
```

**Super Admin:**
```php
'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
```

### Views

**Pharmacy Panel Views:**
```
resources/views/pharmacy/
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   └── otp.blade.php
├── dashboard.blade.php
├── inventory/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── orders/
│   ├── index.blade.php
│   └── show.blade.php
├── commission/
│   └── index.blade.php
├── schedule/
│   └── index.blade.php
└── profile/
    └── edit.blade.php
```

**Admin Views:**
```
resources/views/superadmin/pharmacy/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php
├── commission.blade.php
└── settlement.blade.php
```

**Website Views:**
```
resources/views/website/
├── pharmacies.blade.php
├── pharmacy_products.blade.php
├── cart.blade.php
└── checkout.blade.php
```

### API Endpoints (if applicable)

```php
// Mobile/Web API
Route::prefix('api')->group(function () {
    // Pharmacy List
    Route::get('/pharmacies', 'Api\PharmacyApiController@index');
    
    // Pharmacy Products
    Route::get('/pharmacy/{id}/products', 'Api\PharmacyApiController@products');
    
    // Cart
    Route::post('/cart', 'Api\CartApiController@add');
    Route::get('/cart', 'Api\CartApiController@index');
    Route::put('/cart/{id}', 'Api\CartApiController@update');
    Route::delete('/cart/{id}', 'Api\CartApiController@remove');
    
    // Checkout
    Route::post('/order', 'Api\OrderApiController@create');
    Route::get('/orders', 'Api\OrderApiController@index');
    Route::get('/order/{id}', 'Api\OrderApiController@show');
});
```

---

## Summary

This pharmacy system provides:

✅ **Multi-Pharmacy Marketplace** - Multiple independent pharmacies  
✅ **Commission-Based Model** - Automated commission calculation and tracking  
✅ **Inventory Management** - Pharmacy-specific pricing and stock  
✅ **Distance-Based Search** - Location-aware pharmacy discovery  
✅ **Flexible Working Hours** - Custom schedules with multiple shifts  
✅ **Delivery Management** - Configurable delivery zones and charges  
✅ **Settlement System** - Automatic commission settlements  
✅ **Admin Approval** - Pharmacy registration approval workflow  
✅ **Stock Tracking** - Real-time inventory with low stock alerts  
✅ **Multi-Payment** - COD and online payment support  

**Key Workflows:**
1. Pharmacy registers → Admin approves → Pharmacy adds inventory → Customers order → Commission calculated → Settlement processed
2. Two-tier medicine catalog: Global catalog (admin) + Pharmacy inventory (pharmacy-specific)
3. Automatic stock management and commission splits on every order

---

**Document Version:** 1.0  
**Last Updated:** January 30, 2026  
**Platform:** Laravel (PHP Framework)