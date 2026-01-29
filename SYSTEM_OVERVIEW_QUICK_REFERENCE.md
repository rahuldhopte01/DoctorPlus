# 🏥 BackupDoctor - Quick Reference Guide

**Last Updated:** January 28, 2026

---

## 🎯 What is BackupDoctor?

A **healthcare management platform** connecting patients, doctors, pharmacies, and laboratories through:
- 📝 Medical questionnaires with automated review workflows
- 📅 Doctor appointment booking with video consultations
- 💊 Medicine ordering from pharmacies
- 🔬 Lab test booking and report management
- 💳 Integrated payment processing (Stripe)
- 📊 Commission tracking for all stakeholders

---

## 👥 User Types

| Role | Description | Key Features |
|------|-------------|--------------|
| **Super Admin** | System administrator | Full control, all management, reports |
| **ADMIN_DOCTOR** | Hospital manager | See all hospital questionnaires, monitor sub-doctors |
| **SUB_DOCTOR** | Specialist reviewer | Review assigned questionnaires, create prescriptions |
| **Patient** | End user | Complete questionnaires, book appointments, buy medicines |
| **Pharmacy** | Medicine provider | Manage inventory, fulfill orders |
| **Laboratory** | Test provider | Manage tests, upload reports |

---

## 🔥 Top 5 Core Features

### 1. 📝 Questionnaire System (★ PRIMARY FEATURE)
**Complete workflow:**
```
Patient → Select Category → Complete Questionnaire → Submit
       ↓
Doctor Reviews (locks questionnaire) → Approve/Reject
       ↓
Approved → Patient chooses delivery method
       ↓
Home Delivery → Enter address
OR
Pharmacy Pickup → Select pharmacy
       ↓
Select Medicines (category-specific)
       ↓
Doctor creates prescription
       ↓
Patient pays via Stripe
       ↓
PDF generated → Complete
```

**Key Features:**
- ✅ Hospital-based scoping (doctors only see their hospital's questionnaires)
- ✅ Locking mechanism (one doctor reviews at a time)
- ✅ ADMIN_DOCTOR sees all, SUB_DOCTOR sees assigned categories
- ✅ Auto-save answers
- ✅ Multiple field types (text, dropdown, file upload, etc.)
- ✅ Post-approval patient flow (delivery/pickup → medicine selection)

### 2. 📅 Appointment Booking
**Workflow:**
```
Patient → Browse Doctors → Select → Choose Time → Pay → Confirmed
       ↓
Doctor Accepts → Video Consultation (Zoom) → Complete → Create Prescription
```

**Features:**
- ✅ Doctor search by treatment/category/location
- ✅ Timeslot management
- ✅ Payment integration
- ✅ Video consultation (Zoom OAuth)
- ✅ Commission tracking
- ✅ Google Calendar integration

### 3. 💊 Medicine Management
**Two Systems:**

**A. Global Medicine Catalog:**
- ✅ Centralized medicine database
- ✅ Assigned to categories
- ✅ Brand management

**B. Pharmacy Inventory:**
- ✅ Each pharmacy sets own prices
- ✅ Stock management
- ✅ Low stock alerts

**Ordering:**
```
Patient → Browse Pharmacy → Add to Cart → Checkout → Pay → Order Placed
       ↓
Pharmacy fulfills → Delivery
```

### 4. 💳 Prescription Payment (Stripe)
**Flow:**
```
Prescription Created (status: approved_pending_payment)
       ↓
Patient clicks "Pay"
       ↓
Stripe Checkout Session
       ↓
Payment Processed
       ↓
Webhook Confirms Payment
       ↓
Status: approved_paid
       ↓
PDF Generated Automatically
```

**Fields Tracked:**
- Payment amount, status, token, method, date
- Stripe session ID
- Prescription validity (valid_from, valid_until)

### 5. 🔬 Lab Tests
**Flow:**
```
Patient → Browse Labs → Select Test → Choose Collection Method → Pay
       ↓
Sample Collected (lab visit or home collection)
       ↓
Lab Processes → Uploads Report (PDF)
       ↓
Patient Downloads Report
```

**Types:**
- Pathology (blood, urine, biopsy)
- Radiology (X-ray, MRI, CT scan, ultrasound)

---

## 🗄️ Database Key Tables

### Core Structure
```
treatments
    ↓
category (linked to treatments)
    ↓
questionnaires (one per category)
    ↓
sections (multiple per questionnaire)
    ↓
questions (multiple per section)
```

### Important Tables
- `users` - All users (patients, admins)
- `doctor` - Doctor profiles (hospital_id, doctor_role)
- `hospital` - Hospital information
- `pharmacy` - Pharmacy details (status: pending/approved/rejected)
- `medicine` - Global medicine catalog
- `pharmacy_inventory` - Pharmacy-specific pricing/stock
- `questionnaire_answers` - Patient responses (with status, reviewing_doctor_id)
- `questionnaire_submissions` - Post-submission data (delivery, pharmacy, medicines)
- `appointment` - Appointments
- `prescription` - Prescriptions (with payment fields)
- `purchase_medicine` - Medicine orders
- `lab`, `pathology`, `radiology` - Lab test management

---

## 🔒 Security & Permissions

### Hospital Isolation
- ✅ Doctors see only their hospital's questionnaires
- ✅ Automatic hospital assignment on questionnaire submission
- ✅ Cross-hospital access prevented

### Locking Mechanism
- ✅ SUB_DOCTOR opens questionnaire → locks to them
- ✅ Other SUB_DOCTORs cannot access locked questionnaires
- ✅ ADMIN_DOCTOR can view all (monitoring)
- ✅ Auto-unlock on approve/reject

### Role-Based Access
- ✅ Spatie Laravel Permission
- ✅ Role hierarchy (Super Admin > ADMIN_DOCTOR > SUB_DOCTOR)
- ✅ Permission-based routes

### Data Security
- ✅ CSRF protection (except webhooks)
- ✅ XSS sanitization middleware
- ✅ SQL injection prevention (Eloquent)
- ✅ Password hashing (bcrypt)
- ✅ Secure file uploads

---

## 🔌 Integrations

| Integration | Status | Purpose |
|-------------|--------|---------|
| **Stripe** | ✅ Working | Prescription payments, webhook handling |
| **Zoom** | ✅ Working | Video consultations (OAuth 2.0) |
| **Email (SMTP)** | ✅ Working | Notifications, OTP, password reset |
| **SMS/OTP** | ✅ Working | Phone verification |
| **Google Calendar** | ✅ Working | Add appointment to calendar |
| **Cannaleo API** | 📋 Documented | Ready for implementation (awaiting credentials) |

---

## 📊 Commission System

### How It Works

**Doctors:**
- Option A: Subscription-based (pay monthly, keep 100% of fees)
- Option B: Commission-based (platform takes %)

**Pharmacies:**
- Platform takes % of each medicine sale

**Labs:**
- Platform takes % of each test booking

**Features:**
- ✅ Configurable commission rates per entity
- ✅ Settlement period management
- ✅ Payment status tracking (pending/paid)
- ✅ Admin marks settlements as paid
- ✅ Detailed commission reports

---

## 🎨 Admin Panel Capabilities

### Full Management
- ✅ Users (patients)
- ✅ Doctors (assign hospital, role, categories)
- ✅ Hospitals
- ✅ Pharmacies (approve/reject, priority flag)
- ✅ Laboratories
- ✅ Medicines (global catalog + pharmacy inventory)
- ✅ Categories & Treatments
- ✅ Questionnaires (create, edit sections/questions)
- ✅ Appointments (view, create, cancel)
- ✅ Prescriptions
- ✅ Commission & Settlements

### Settings
- ✅ General (site name, logo, contact)
- ✅ Payment (Stripe configuration)
- ✅ Notifications (email/SMS templates)
- ✅ Video Call (Zoom configuration)
- ✅ Verification (email/phone OTP)
- ✅ Multi-language

### Content Management
- ✅ Banners (homepage sliders)
- ✅ Blogs
- ✅ Offers/Coupons
- ✅ Static pages (About, Privacy Policy)

### Reports
- ✅ User report (filter, export)
- ✅ Doctor report (appointments, revenue, commission)
- ✅ Revenue report
- ✅ Appointment report
- ✅ Commission report

### Utilities
- ✅ Cache clear
- ✅ User impersonation (login as any user)
- ✅ Bulk delete operations
- ✅ Test email functionality

---

## 🚀 Quick Test Scenarios

### Test 1: Complete Patient Journey (Questionnaire)
1. Login as patient
2. Browse categories → Select one
3. Complete questionnaire → Submit
4. Login as SUB_DOCTOR (assigned to that category)
5. See questionnaire in list → Open it (locks automatically)
6. Review answers → Approve
7. Back as patient → Choose delivery method
8. Select pharmacy OR enter address
9. Select medicines
10. Doctor creates prescription
11. Patient pays via Stripe
12. Download PDF ✅

### Test 2: Appointment Booking
1. Login as patient
2. Browse doctors → Select one
3. Choose date/time → Book
4. Pay for appointment
5. Login as doctor → Accept appointment
6. Create Zoom meeting
7. Patient joins video call
8. Doctor marks complete → Create prescription ✅

### Test 3: Medicine Purchase
1. Login as patient
2. Browse pharmacies → Select one
3. Add medicines to cart
4. Checkout → Enter address
5. Pay
6. Pharmacy sees order → Fulfills ✅

### Test 4: Lab Test
1. Login as patient
2. Browse labs → Select one
3. Choose test (pathology/radiology)
4. Select collection method (lab visit or home)
5. Pay
6. Lab uploads report
7. Patient downloads report ✅

---

## 📈 System Stats

- **Total Tables:** 50+
- **Models:** 40+
- **Controllers:** 35+
- **Routes:** 300+
- **Roles:** 6 (Super Admin, ADMIN_DOCTOR, SUB_DOCTOR, Patient, Pharmacy, Lab)
- **Recent Migrations:** 31 (major 2026 updates)

---

## 🎯 What's Working (Summary)

✅ **100% Functional:**
- User management (all roles)
- Questionnaire system (complete workflow)
- Appointment booking (with video)
- Medicine management (global catalog + pharmacy inventory)
- Prescription payment (Stripe integration)
- Lab test management
- Commission tracking
- Admin panel (full CRUD)
- Multi-language support
- Video consultation (Zoom)
- Email/SMS notifications
- Security & permissions

📋 **Documented, Ready to Implement:**
- Cannaleo API integration (awaiting client credentials)

---

## 📞 Technical Stack

| Layer | Technology |
|-------|------------|
| **Framework** | Laravel 10+ |
| **Database** | MySQL |
| **Frontend** | Blade Templates, Bootstrap, jQuery |
| **Authentication** | Laravel Sanctum |
| **Authorization** | Spatie Laravel Permission |
| **Payment** | Stripe (Checkout + Webhooks) |
| **Video** | Zoom OAuth 2.0 |
| **Email** | SMTP |
| **Queue** | Database/Redis |
| **Cache** | Redis/File |

---

## 🗂️ Key Routes

### Public
- `/` - Homepage
- `/categories` - Browse categories
- `/show-doctors` - Browse doctors
- `/all-pharmacies` - Browse pharmacies
- `/all-labs` - Browse labs
- `/patient-login` - Patient login

### Patient (Auth Required)
- `/questionnaire/category/{id}` - Start questionnaire
- `/booking/{id}/{name}` - Book appointment
- `/prescription/pay/{id}` - Pay for prescription
- `/user_profile` - User profile

### Doctor (Auth Required)
- `/doctor_home` - Doctor dashboard
- `/doctor/questionnaires` - View questionnaires
- `/doctor/questionnaire/{userId}/{categoryId}/{questionnaireId}` - Review submission
- `/prescription/{appointment_id}` - Create prescription
- `/create_zoom_meeting/{appointment_id}` - Create Zoom meeting

### Pharmacy (Auth Required)
- `/pharmacy_home` - Pharmacy dashboard
- `/pharmacy_inventory` - Manage inventory
- `/purchased_medicines` - View orders

### Lab (Auth Required)
- `/pathologist_home` - Lab dashboard
- `/pathology` - Manage pathology tests
- `/radiology` - Manage radiology tests
- `/upload_report` - Upload test report

### Admin (Auth Required)
- `/home` - Admin dashboard
- `/doctor` - Manage doctors
- `/pharmacy` - Manage pharmacies
- `/medicine` - Manage medicines
- `/questionnaire` - Manage questionnaires
- `/setting` - System settings

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| `SYSTEM_WORKING_FEATURES_DOCUMENTATION.md` | Complete detailed documentation (this file's companion) |
| `SYSTEM_OVERVIEW_QUICK_REFERENCE.md` | This quick reference guide |
| `CANNALEO_INTEGRATION_COMPLETE_GUIDE.md` | Cannaleo API integration guide |
| `HOW_TO_TEST.md` | Testing guide for hospital-doctor hierarchy |
| `TESTING_GUIDE_HOSPITAL_DOCTOR_HIERARCHY.md` | Detailed testing guide |
| `TEST_PHARMACIES_LOGIN_DETAILS.md` | Test pharmacy credentials |
| `PROJECT_SETUP_GUIDE.md` | Project setup instructions |

---

## ✅ Quick Checklist: "Is My System Working?"

- [ ] Can patients complete questionnaires? ✅ YES
- [ ] Do doctors see and review questionnaires? ✅ YES
- [ ] Does locking work (one doctor at a time)? ✅ YES
- [ ] Can patients pay for prescriptions? ✅ YES (Stripe)
- [ ] Does video consultation work? ✅ YES (Zoom)
- [ ] Can pharmacies manage inventory? ✅ YES
- [ ] Can labs upload reports? ✅ YES
- [ ] Does admin panel have full control? ✅ YES
- [ ] Are commissions tracked? ✅ YES
- [ ] Is hospital isolation working? ✅ YES
- [ ] Are payments secure? ✅ YES
- [ ] Can I create questionnaires? ✅ YES
- [ ] Does multi-language work? ✅ YES

**All systems operational! 🎉**

---

**For complete details, see:** `SYSTEM_WORKING_FEATURES_DOCUMENTATION.md`

