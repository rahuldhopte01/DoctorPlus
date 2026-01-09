# Dr. Fuxx App – Payment & Operational Logic Checklist
## Comparison: Proposal Requirements vs. Current Implementation

**Analysis Date:** 2026-01-04  
**Project:** Doctro - Medical Appointment Booking System  
**Version:** 10.0.0

---

## 1. PAYMENT SYSTEM

### 1.1 Payment Processing
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **In-app payment by user** | ✅ **PRESENT** | Payment integrated in booking flow (`WebsiteController::bookAppointment()`, `UserApiController::apiBooking()`) | Users can pay during appointment booking |
| **Payment authorized before treatment begins** | ❌ **MISSING** | Payment is charged immediately on booking | Payment happens at booking time, not held/authorized |
| **Amount held in escrow by platform** | ❌ **MISSING** | No escrow system found | Payment is captured immediately (Stripe charges.create) |
| **Release only after successful service delivery** | ❌ **MISSING** | No escrow release mechanism | Commission tracked in `settle` table after completion, but payment already captured |

**Implementation Location:**
- `app/Http/Controllers/UserApiController.php` (lines 357-367) - Stripe charge created immediately
- `app/Http/Controllers/Website/WebsiteController.php` - Payment processing in booking flow

---

### 1.2 Payment Gateways
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Stripe (recommended)** | ✅ **PRESENT** | Stripe integrated via `stripe/stripe-php` v16.1 | Found in `UserApiController::apiBooking()` |
| **PayPal (optional)** | ✅ **PRESENT** | PayPal integrated | Found in payment views and JS files |
| **Apple Pay, Google Pay, SEPA (via Stripe)** | ⚠️ **PARTIAL** | Stripe SDK supports these, but no explicit implementation found | May work if Stripe account configured |
| **Split payments** | ❌ **MISSING** | No split payment logic | Single charge to platform |
| **Automated refunds** | ❌ **MISSING** | No refund functionality found | Only status changes on cancellation |

**Additional Gateways Found:**
- ✅ Razorpay
- ✅ Paystack
- ✅ Flutterwave
- ✅ COD (Cash on Delivery)

**Implementation Location:**
- `public/assets/js/appointment.js` - Payment gateway selection
- `resources/views/website/appointment_booking.blade.php` - Payment method UI
- `app/Http/Controllers/UserApiController.php` - Stripe payment processing

---

### 1.3 Payment Timing
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Payment in advance** | ✅ **PRESENT** | Payment captured at booking time | Confirmed: payment happens before appointment |
| **Reduces no-shows** | ✅ **PRESENT** | Payment required to create appointment | Functionally achieves goal |
| **Legally permissible** | ✅ **N/A** | Implementation exists | Legal compliance is business decision |
| **Standard in telemedicine** | ✅ **N/A** | Implementation exists | Industry standard followed |

---

## 2. PHYSICIAN COMPENSATION

### 2.1 Compensation Model
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Fixed amount per consultation (€19-29)** | ❌ **MISSING** | Only commission-based model exists | `doctor.based_on` = 'commission' only |
| **Independent of final price** | ❌ **MISSING** | Commission is percentage-based | `doctor.commission_amount` is percentage |
| **Differentiated fees by service type** | ❌ **MISSING** | No service type differentiation | All consultations use same commission model |

**Current Implementation:**
- Commission-based model: `doctor.based_on = 'commission'`
- Commission percentage: `doctor.commission_amount` (stored as string/percentage)
- Calculation: `admin_commission = amount * commission_amount / 100`, `doctor_commission = amount - admin_commission`
- Also supports subscription model: `doctor.based_on = 'subscription'` (not commission-based)

**Implementation Location:**
- `app/Http/Controllers/UserApiController.php` (lines 368-373) - Commission calculation on booking
- `app/Models/Doctor.php` - `based_on`, `commission_amount` fields

---

### 2.2 Payout Frequency
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Monthly (standard)** | ⚠️ **PARTIAL** | Settlement tracking exists, but payout mechanism unclear | `settle` table tracks amounts, `doctor_status = 0` means unpaid |
| **Bi-weekly (optional)** | ❌ **MISSING** | No bi-weekly payout option | Only settlement tracking exists |

**Current Implementation:**
- `settle` table tracks doctor payouts
- Fields: `appointment_id`, `doctor_id`, `doctor_amount`, `admin_amount`, `payment` (0=COD, 1=Online), `doctor_status` (0=unpaid, paid when updated)
- Settlement created when appointment completed: `AppointmentController::completeAppointment()`
- Finance reporting exists: `DoctorController::finance()` shows earnings

**Implementation Location:**
- `app/Models/Settle.php`
- `app/Http/Controllers/SuperAdmin/AppointmentController.php` (lines 148-156) - Settlement creation
- `app/Http/Controllers/SuperAdmin/DoctorController.php` (lines 259-278) - Finance tracking

---

### 2.3 Payment Hold Period
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Withheld until service completed** | ✅ **PRESENT** | Settlement created only after appointment completion | `completeAppointment()` creates settlement |
| **Withheld until no complaint (24-48h)** | ❌ **MISSING** | No complaint/refund period logic | Settlement created immediately on completion |
| **Automatic release thereafter** | ❌ **MISSING** | No automatic release mechanism | Manual payout process (doctor_status update) |

**Current Implementation:**
- Settlement record created when `appointment_status = 'complete'`
- `doctor_status = 0` indicates unpaid
- No automatic release or complaint period

---

## 3. PHARMACY COMPENSATION

### 3.1 Payment Model
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Model A: User pays pharmacy directly (recommended)** | ❌ **MISSING** | User pays platform, pharmacy gets commission | Current: Platform collects total, splits commission |
| **Model B: Platform collects total** | ✅ **PRESENT** | Platform collects payment, pharmacy receives payout | Implemented as Model B |
| **Direct billing to user** | ❌ **MISSING** | No direct billing | All payments through platform |

**Current Implementation:**
- Platform collects total payment from user
- Commission split: `pharmacy_commission = amount - admin_commission`
- `admin_commission = amount * commission_amount / 100`
- Stored in `purchase_medicine` table
- Settlement in `pharmacy_settle` table

**Implementation Location:**
- `app/Http/Controllers/Website/WebsiteController.php` (lines 1372-1375) - Pharmacy commission calculation
- `app/Http/Controllers/UserApiController.php` (lines 649-653) - API pharmacy commission
- `app/Models/PharmacySettle.php`

---

### 3.2 Payout Method
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Direct to pharmacy** | ⚠️ **PARTIAL** | Settlement tracking exists, but payout mechanism unclear | `pharmacy_settle` table tracks, `pharmacy_status = 0` means unpaid |
| **Through platform** | ✅ **PRESENT** | Payments processed through platform | All payments go through platform first |

**Current Implementation:**
- `pharmacy_settle` table tracks payouts
- Fields: `purchase_medicine_id`, `pharmacy_id`, `pharmacy_amount`, `admin_amount`, `payment`, `pharmacy_status`
- Settlement created immediately on medicine purchase
- Finance tracking: `PharmacyController::commission()` shows earnings

---

### 3.3 Commissions/Service Fees
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Fixed referral fee** | ❌ **MISSING** | Only percentage-based commission | No fixed fee option |
| **Small commission (5-10%)** | ✅ **PRESENT** | Commission percentage stored in `pharmacy.commission_amount` | Configurable per pharmacy |
| **Dependent on pharmacy agreement** | ✅ **PRESENT** | Each pharmacy has own `commission_amount` | Flexible per-pharmacy rates |

**Current Implementation:**
- `pharmacy.commission_amount` - percentage commission per pharmacy
- Commission calculated: `admin_commission = amount * commission_amount / 100`
- Pharmacy receives: `pharmacy_commission = amount - admin_commission`

---

## 4. DELIVERY PARTNERS

### 4.1 Delivery Partner Integration
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **DHL integration** | ❌ **MISSING** | No delivery partner integration | No external delivery service APIs |
| **GO! Express integration** | ❌ **MISSING** | No delivery partner integration | No external delivery service APIs |
| **Local pharmacy courier services** | ❌ **MISSING** | No delivery partner integration | No external delivery service APIs |
| **External delivery (recommended)** | ⚠️ **PARTIAL** | Pharmacy shipping enabled, but no partner integration | `pharmacy.is_shipping` field exists |
| **Platform only integrates tracking/status** | ❌ **MISSING** | No tracking integration | No delivery tracking system |

**Current Implementation:**
- `pharmacy.is_shipping` - Boolean field (pharmacy offers shipping)
- `pharmacy.delivery_charges` - JSON field storing delivery charges
- `purchase_medicine.delivery_charge` - Delivery charge amount
- `purchase_medicine.shipping_at` - Timestamp field (unused?)
- `purchase_medicine.address_id` - Delivery address
- Delivery charge calculation: `WebsiteController::getDeliveryCharge()` - calculates based on distance/charges JSON

**Implementation Location:**
- `app/Models/Pharmacy.php` - `is_shipping`, `delivery_charges` fields
- `app/Http/Controllers/Website/WebsiteController.php` (lines 543-561) - Delivery charge calculation

---

### 4.2 Delivery Costs
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **User covers delivery costs** | ✅ **PRESENT** | Delivery charge added to order total | Implemented |
| **Delivery costs displayed transparently** | ⚠️ **PARTIAL** | Delivery charge calculated, but transparency unclear | Charge calculated, but display location needs verification |

---

## 5. FEES & COSTS

### 5.1 Fee Structure
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Doctor consultation fee (fixed price)** | ✅ **PRESENT** | `doctor.appointment_fees` field exists | Fee stored per doctor |
| **Prescription fee (if applicable)** | ❌ **MISSING** | No separate prescription fee | Included in consultation fee |
| **Medication (pharmacy)** | ✅ **PRESENT** | Medicine prices from pharmacy | Medicine pricing system exists |
| **Delivery costs** | ✅ **PRESENT** | `delivery_charge` field in purchase | Implemented |
| **Payment fees (included in price)** | ⚠️ **UNKNOWN** | Payment gateway fees not explicitly tracked | May be absorbed by platform margin |
| **No fees for physicians** | ✅ **PRESENT** | Doctors receive commission/payout | No fees deducted |
| **Platform retains margin** | ✅ **PRESENT** | `admin_commission` tracks platform margin | Commission split tracks platform earnings |
| **Payment provider fees (Stripe)** | ❌ **MISSING** | Not tracked separately | May be absorbed in margin |

**Current Implementation:**
- Consultation fee: `doctor.appointment_fees` (varchar field)
- Medicine pricing: `medicine.price_pr_strip` (per pharmacy)
- Delivery charge: Calculated and stored
- Commission split tracks platform earnings

---

### 5.2 Fee Transparency
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Fees clearly itemized before payment** | ⚠️ **PARTIAL** | Fees shown in booking form, but itemization unclear | Booking form shows total, breakdown needs verification |
| **Dedicated overview pages for users** | ❌ **MISSING** | No dedicated fee overview page | Fees shown in booking flow only |
| **Dedicated overview pages for physicians** | ⚠️ **PARTIAL** | Finance/commission view exists | `DoctorController::finance()` shows earnings |
| **Dedicated overview pages for pharmacies** | ⚠️ **PARTIAL** | Commission view exists | `PharmacyController::commission()` shows earnings |

---

### 5.3 Situational Fees
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Express delivery** | ❌ **MISSING** | No express delivery option | Standard delivery only |
| **Night/weekend processing** | ❌ **MISSING** | No time-based fee differentiation | Standard fees only |
| **Special services** | ❌ **MISSING** | No special service fees | Standard consultation fee only |

---

## 6. REFUNDS (REFUND PROCESS)

### 6.1 Refund Scenarios
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Doctor not available** | ❌ **MISSING** | Appointment can be cancelled, but no refund | Only status change to 'cancel' |
| **Request medically declined** | ❌ **MISSING** | Doctor can decline, but no refund | Status changes, no refund logic |
| **Technical error** | ❌ **MISSING** | No refund for technical errors | No refund system |
| **Delivery failed** | ❌ **MISSING** | No refund for delivery failure | No refund system |
| **Cancellation before physician review** | ❌ **MISSING** | User can cancel, but no refund | Only status change |

**Current Implementation:**
- Cancellation exists: `AppointmentController::cancelAppointment()`, `WebsiteController::cancelAppointment()`
- Status changes: `appointment_status = 'cancel'`
- No refund processing found anywhere in codebase
- Payment already captured, no reversal mechanism

**Implementation Location:**
- `app/Http/Controllers/SuperAdmin/AppointmentController.php` (lines 133-141) - Admin cancellation
- `app/Http/Controllers/Website/WebsiteController.php` (lines 1648-1657) - User cancellation

---

### 6.2 Refund Processing
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Automated refunds (service not started)** | ❌ **MISSING** | No automated refund system | No refund functionality |
| **Manual refunds (partial service)** | ❌ **MISSING** | No manual refund system | No refund functionality |
| **Timeline: 3-5 business days** | ❌ **MISSING** | No refund system | N/A |
| **Platform bears payment fees** | ❌ **MISSING** | No refund system | N/A |
| **Physician receives compensation only for successful service** | ✅ **PRESENT** | Settlement only created on completion | Compensation tied to completion |
| **Partial refunds (delivery failed, medication unavailable)** | ❌ **MISSING** | No partial refund logic | No refund system |

---

## 7. SPECIAL CASES & USE CASES

### 7.1 Special Scenarios
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **Multiple pharmacies decline → reassignment** | ❌ **MISSING** | No pharmacy assignment/reassignment logic | User selects pharmacy manually |
| **Physician declines medically → full refund** | ❌ **MISSING** | Doctor can cancel, but no refund | Status change only |
| **User cancels process → status-dependent logic** | ⚠️ **PARTIAL** | Cancellation exists, but no refund/status-dependent logic | Simple status change |
| **Prescription issued but not redeemed** | ❌ **MISSING** | No prescription redemption tracking | Prescriptions exist, but no redemption logic |
| **Support override for manual decisions** | ❌ **MISSING** | No support/admin override system | Admin can change status, but no override workflow |

---

## 8. COMPLETE BUSINESS LOGIC (PAYMENT FLOW)

### 8.1 Proposed Payment Flow vs. Current Flow

**Proposed Flow:**
1. User selects service
2. Payment authorized (Escrow)
3. Physician reviews & treats
4. Decision: Declined → Refund | Accepted → Release
5. Prescription → Pharmacy
6. Pharmacy delivers medication
7. Payouts: Physician → monthly | Platform → margin | Pharmacy → direct

**Current Flow:**
1. ✅ User selects service/doctor
2. ❌ Payment captured immediately (no escrow)
3. ✅ Physician reviews (accept/cancel/complete)
4. ❌ No refund on decline, payment already captured
5. ✅ Prescription can be created (separate flow)
6. ⚠️ Pharmacy delivery exists (no partner integration)
7. ⚠️ Payouts tracked (Settle/PharmacySettle tables), but manual process

**Gap Analysis:**
- ❌ No escrow/authorization system
- ❌ No refund mechanism
- ❌ No automatic payout system
- ⚠️ Payment flow is: Book → Pay Immediately → Service → Track Commission
- ⚠️ Proposed flow: Book → Authorize/Hold → Service → Release/Refund → Payout

---

## 9. ADDITIONAL RELEVANT RULES

### 9.1 Compliance & Security
| Requirement | Status | Current Implementation | Notes |
|------------|--------|----------------------|-------|
| **GDPR-compliant data storage** | ⚠️ **UNKNOWN** | Standard Laravel data storage | Compliance depends on implementation details |
| **Separation of medical data and payment data** | ⚠️ **PARTIAL** | Separate tables, but no explicit separation logic | Data in different tables, but no enforced separation |
| **Roles & permissions: User, Physician, Pharmacy, Admin** | ✅ **PRESENT** | Spatie Laravel Permission package | Roles: `super admin`, `doctor`, `pharmacy`, `laboratory`, `patient` |
| **Audit logs for payments & decisions** | ❌ **MISSING** | No audit log system | Laravel logging exists, but no payment/decision audit trail |

**Current Implementation:**
- Roles: Spatie Permission package (`spatie/laravel-permission` v6.9)
- Permissions: Gate-based authorization (`Gate::denies()`)
- No dedicated audit log tables
- Laravel's default logging exists, but no structured payment audit

**Implementation Location:**
- `app/Http/Middleware/` - Authorization middleware
- `config/logging.php` - Laravel logging configuration
- No payment audit log model found

---

## SUMMARY

### ✅ PRESENT (Fully Implemented)
1. Payment gateways (Stripe, PayPal, multiple others)
2. Payment in advance (captured at booking)
3. Commission-based physician compensation
4. Commission-based pharmacy compensation
5. Settlement tracking (Settle, PharmacySettle tables)
6. Delivery charge calculation
7. Role-based access control
8. Finance tracking for doctors/pharmacies

### ⚠️ PARTIAL (Partially Implemented)
1. Payment processing (captured immediately, not escrow)
2. Payout frequency (tracking exists, automated payout unclear)
3. Payment hold period (settlement created on completion, but no complaint period)
4. Pharmacy payout (tracking exists, mechanism unclear)
5. Fee transparency (fees shown, but itemization unclear)
6. Delivery partners (shipping enabled, but no integration)
7. Payment flow (basic flow exists, but missing escrow/refund)

### ❌ MISSING (Not Implemented)
1. **Escrow system** (payment authorization/hold)
2. **Refund system** (automated and manual)
3. **Fixed fee physician compensation** (only commission exists)
4. **Delivery partner integration** (DHL, GO! Express, etc.)
5. **Automatic payout system** (monthly/bi-weekly)
6. **Complaint/refund period** (24-48 hour hold)
7. **Payment audit logs** (structured audit trail)
8. **Prescription redemption tracking**
9. **Pharmacy reassignment logic**
10. **Support override workflow**
11. **Situational fees** (express delivery, night/weekend)
12. **Fee overview pages** (dedicated pages for transparency)

---

## PRIORITY RECOMMENDATIONS

### 🔴 HIGH PRIORITY (Critical for Operations)
1. **Implement Escrow System** - Payment authorization/hold before service
2. **Implement Refund System** - Automated and manual refunds
3. **Add Fixed Fee Compensation Model** - Support €19-29 fixed fees for physicians
4. **Implement Payment Audit Logs** - Track all payment decisions

### 🟡 MEDIUM PRIORITY (Important for Compliance)
5. **Add Complaint/Refund Period** - 24-48 hour hold before payout
6. **Implement Automatic Payouts** - Monthly/bi-weekly automated payouts
7. **Improve Fee Transparency** - Dedicated fee overview pages
8. **Add Delivery Partner Integration** - DHL/GO! Express integration

### 🟢 LOW PRIORITY (Nice to Have)
9. **Add Situational Fees** - Express delivery, night/weekend processing
10. **Add Prescription Redemption Tracking**
11. **Add Pharmacy Reassignment Logic**
12. **Add Support Override Workflow**

---

## TECHNICAL IMPLEMENTATION NOTES

### Current Payment Flow
```
User → Select Doctor → Book Appointment → Payment Captured Immediately (Stripe/PayPal) 
→ Appointment Created → Doctor Accepts/Declines/Completes 
→ If Completed: Settlement Record Created → Manual Payout (doctor_status = 0 → 1)
```

### Required Payment Flow (Per Proposal)
```
User → Select Service → Payment Authorized (Escrow/Hold) 
→ Physician Reviews → Decision:
  - Declined → Automated Refund
  - Accepted → Payment Released from Escrow
→ Prescription → Pharmacy → Delivery
→ Automatic Payouts (Monthly):
  - Physician → Commission
  - Platform → Margin
  - Pharmacy → Direct/Through Platform
```

### Key Code Locations
- **Payment Processing:** `app/Http/Controllers/UserApiController.php` (lines 357-375)
- **Commission Calculation:** `app/Http/Controllers/UserApiController.php` (lines 368-373)
- **Settlement Creation:** `app/Http/Controllers/SuperAdmin/AppointmentController.php` (lines 148-156)
- **Pharmacy Commission:** `app/Http/Controllers/Website/WebsiteController.php` (lines 1372-1375)
- **Payment Models:** `app/Models/Settle.php`, `app/Models/PharmacySettle.php`

---

**End of Checklist**
