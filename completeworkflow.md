---

## 📊 Detailed Workflows

### Workflow 1: Patient Questionnaire Journey (Complete)

```
1. Patient Login
   ↓
2. Browse Categories
   ↓
3. Select Category (e.g., Hairfall Treatment)
   ↓
4. View Questionnaire Details
   ↓
5. Start Questionnaire
   ↓
6. Answer Questions (auto-saved)
   ↓
7. Submit Questionnaire
   ├─ Status: PENDING
   ├─ Assigned to hospital (based on category doctors)
   ↓
8. Doctor Reviews
   ├─ Doctor opens → Status: IN_REVIEW
   ├─ Locks to reviewing doctor
   ├─ Doctor reviews answers
   ├─ Doctor approves/rejects
   ↓
9. If APPROVED → Patient chooses delivery method
   ├─ Option A: Home Delivery
   │   ├─ Select/add delivery address
   │   ├─ Enter postcode, city, state
   │   └─ Confirm address
   │
   ├─ Option B: Pharmacy Pickup
   │   ├─ View approved pharmacies
   │   ├─ Select pharmacy
   │   └─ Confirm selection
   ↓
10. Medicine Selection
    ├─ View category-specific medicines
    ├─ Multi-select medicines
    ├─ View medicine details
    └─ Submit selection
    ↓
11. Doctor Creates Prescription
    ├─ Uses patient's medicine selection
    ├─ Adds dosage and instructions
    ├─ Sets validity period
    └─ Generates prescription
    ↓
12. Prescription Payment
    ├─ Status: approved_pending_payment
    ├─ Patient sees payment page
    ├─ Stripe Checkout
    ├─ Payment processed
    ├─ Webhook confirms payment
    ├─ Status: approved_paid
    └─ PDF generated
    ↓
13. Patient Downloads Prescription
    ├─ View prescription PDF
    ├─ Download for records
    └─ Prescription sent to selected pharmacy (if pickup)
    ↓
14. Pharmacy Fulfills Order (if applicable)
    ↓
15. Patient Receives Medicines
    └─ COMPLETE
```

### Workflow 2: Direct Appointment Booking

```
1. Patient Login
   ↓
2. Browse Doctors
   ├─ Filter by treatment
   ├─ Filter by category
   ├─ Filter by location
   └─ View doctor profile
   ↓
3. Select Doctor
   ↓
4. Choose Date & Timeslot
   ↓
5. Select Hospital (if doctor at multiple)
   ↓
6. Apply Coupon (optional)
   ↓
7. Make Payment
   ↓
8. Booking Confirmed
   ├─ Status: PENDING
   ├─ Notification to doctor
   └─ Add to calendar option
   ↓
9. Doctor Accepts
   ├─ Status: ACCEPTED
   └─ Notification to patient
   ↓
10. Video Consultation (optional)
    ├─ Doctor creates Zoom meeting
    ├─ Patient receives join link
    └─ Consultation happens
    ↓
11. Doctor Marks Complete
    ├─ Status: COMPLETED
    └─ Creates prescription (if needed)
    ↓
12. Patient Reviews Doctor
    └─ COMPLETE
```

### Workflow 3: Medicine Purchase (Direct)

```
1. Patient Login
   ↓
2. Browse Pharmacies
   ↓
3. Select Pharmacy
   ↓
4. Browse Pharmacy Products
   ↓
5. Add Medicines to Cart
   ↓
6. View Cart
   ├─ Update quantities
   └─ Remove items
   ↓
7. Proceed to Checkout
   ↓
8. Select Delivery Address
   ↓
9. Calculate Delivery Charges
   ↓
10. Select Delivery Timeslot
    ↓
11. Apply Coupon (optional)
    ↓
12. Make Payment
    ↓
13. Order Confirmed
    ├─ Pharmacy receives order
    └─ Patient receives confirmation
    ↓
14. Pharmacy Processes Order
    ├─ Packs medicines
    └─ Arranges delivery
    ↓
15. Patient Receives Order
    └─ COMPLETE
```

### Workflow 4: Lab Test Booking

```
1. Patient Login
   ↓
2. Browse Labs
   ↓
3. Select Lab
   ↓
4. Choose Test Category (Pathology/Radiology)
   ↓
5. View Tests in Category
   ↓
6. Select Test(s)
   ↓
7. View Test Details & Price
   ↓
8. Choose Collection Method
   ├─ Lab Visit → Select timeslot
   └─ Home Collection → Provide address
   ↓
9. Make Payment
   ↓
10. Booking Confirmed
    ├─ Lab receives booking
    └─ Patient receives confirmation
    ↓
11. Sample Collection
    ├─ Patient visits lab OR
    └─ Lab staff visits patient
    ↓
12. Lab Processes Sample
    ↓
13. Lab Uploads Report
    ├─ Report available in patient portal
    └─ Patient notified
    ↓
14. Patient Views/Downloads Report
    └─ COMPLETE
```

---

## 🗄️ Database Architecture

### Core Tables Structure

#### **Users & Authentication**
- ✅ `users` - Unified user table (patients, admins)
- ✅ `roles` - Spatie roles
- ✅ `permissions` - Spatie permissions
- ✅ `model_has_roles` - User-role assignments
- ✅ `model_has_permissions` - User-permission assignments

#### **Medical Hierarchy**
```
treatments (top level)
    ↓
category (belongs to treatment)
    ↓
questionnaires (belongs to category)
    ↓
expertise (belongs to category)
```

**Relationships:**
- ✅ `treatments` → one-to-many → `category`
- ✅ `category` → one-to-many → `questionnaires`
- ✅ `category` → one-to-many → `expertise`
- ✅ `category` ↔ many-to-many ↔ `medicine` (via `category_medicine`)
- ✅ `category` ↔ many-to-many ↔ `doctor` (via `doctor_category`)

#### **Hospital & Doctor Structure**
- ✅ `hospital` - Hospital information
- ✅ `doctor` - Doctor profiles
  - `hospital_id` (BIGINT) - Belongs to hospital
  - `doctor_role` (ENUM) - ADMIN_DOCTOR or SUB_DOCTOR
- ✅ `doctor_treatment` - Doctor-treatment assignments (many-to-many)
- ✅ `doctor_category` - Doctor-category assignments (many-to-many)
- ✅ `working_hour` - Doctor working hours

#### **Questionnaire System**
- ✅ `questionnaires` - Questionnaire definitions
  - `category_id` - Belongs to category
  - `name`, `description`
  - `status` (active/inactive)
- ✅ `questionnaire_sections` - Questionnaire sections
  - `questionnaire_id` - Belongs to questionnaire
  - `title`, `description`
  - `order_index` - Section order
- ✅ `questionnaire_questions` - Individual questions
  - `section_id` - Belongs to section
  - `question_text`
  - `field_type` (text, textarea, number, dropdown, radio, checkbox, file)
  - `options` (JSON) - For dropdown/radio/checkbox
  - `is_required`
  - `order_index` - Question order
- ✅ `questionnaire_answers` - Patient responses
  - `user_id` - Patient who answered
  - `questionnaire_id` - Which questionnaire
  - `category_id` - Which category
  - `question_id` - Which question
  - `answer` (TEXT/JSON) - Answer content
  - `status` (pending, IN_REVIEW, approved, rejected)
  - `reviewing_doctor_id` - Doctor currently reviewing
  - `hospital_id` - Assigned hospital
  - `appointment_id` (nullable) - If related to appointment
  - `submitted_at` - Submission timestamp
- ✅ `questionnaire_submissions` - Post-submission data
  - `user_id`, `category_id`, `questionnaire_id`
  - `delivery_type` (delivery, pickup)
  - `delivery_address`, `delivery_postcode`, `delivery_city`, `delivery_state`
  - `selected_pharmacy_id`
  - `selected_medicines` (JSON)
  - `status`

#### **Appointments**
- ✅ `appointment`
  - `user_id` - Patient
  - `doctor_id` - Doctor
  - `hospital_id` - Hospital
  - `appointment_date`, `appointment_time`
  - `amount`, `discount`, `final_amount`
  - `status` (pending, accepted, cancelled, completed)
  - `payment_status`
  - `payment_token`
  - `commission_amount`, `admin_commission`

#### **Prescriptions**
- ✅ `prescription`
  - `user_id` - Patient
  - `doctor_id` - Doctor
  - `appointment_id` (nullable) - Related appointment
  - `medicines` (JSON) - Prescribed medicines with dosage
  - `note` - Doctor instructions
  - `status` (pending, approved_pending_payment, approved_paid, rejected)
  - `valid_from`, `valid_until` - Validity period
  - **Payment fields (Stripe):**
    - `payment_amount`
    - `payment_status` (pending, paid, failed)
    - `payment_token`
    - `payment_method`
    - `payment_date`
    - `stripe_session_id`

#### **Medicine System**
- ✅ `medicine_brands` - Medicine brands
  - `name`, `image`
  - `api_provider_id` (for external APIs like Cannaleo)
- ✅ `medicine` - Global medicine catalog
  - `name`, `strength`, `form`
  - `brand_id` - Belongs to medicine brand
  - `image`, `description`
  - `status` (active/inactive)
  - `source_type` (internal, external)
- ✅ `pharmacy_inventory` - Pharmacy-specific inventory
  - `pharmacy_id` - Which pharmacy
  - `medicine_id` - Which medicine
  - `price` - Pharmacy's price
  - `stock_quantity` - Available stock
  - `low_stock_threshold`
- ✅ `category_medicine` - Category-medicine mapping
  - `category_id`
  - `medicine_id`
- ✅ `purchase_medicine` - Medicine orders
  - `user_id` - Patient
  - `pharmacy_id` - Pharmacy
  - `address_id` - Delivery address
  - `total_amount`, `discount_amount`, `delivery_charge`
  - `status` (pending, confirmed, shipped, delivered, cancelled)
  - `payment_status`, `payment_token`
- ✅ `medicine_child` - Order line items
  - `purchase_medicine_id` - Order
  - `medicine_id` - Medicine
  - `quantity`, `price`

#### **Pharmacy**
- ✅ `pharmacy`
  - `user_id` - Pharmacy user account
  - `name`, `address`, `phone`
  - `postcode` - For location-based search
  - `status` (ENUM: pending, approved, rejected)
  - `is_priority` (boolean) - Priority pharmacy flag
  - `commission_percentage`
- ✅ `pharmacy_working_hour` - Working hours
- ✅ `pharmacy_settle` - Commission settlements

#### **Laboratory**
- ✅ `lab` - Laboratory information
  - `user_id` - Lab user account
  - `name`, `address`, `phone`
  - `commission_percentage`
- ✅ `pathology_category` - Pathology test categories
- ✅ `pathology` - Pathology tests
  - `pathology_category_id`
  - `name`, `price`
  - `method`, `duration`
- ✅ `radiology_category` - Radiology test categories
- ✅ `radiology` - Radiology tests
  - `radiology_category_id`
  - `name`, `price`
- ✅ `report` - Test reports
  - `user_id` - Patient
  - `lab_id` - Laboratory
  - `pathology_id` or `radiology_id`
  - `report_pdf` - Uploaded report file
  - `status`
- ✅ `lab_work_hours` - Lab working hours
- ✅ `lab_settle` - Lab commission settlements

#### **Other Important Tables**
- ✅ `user_address` - Patient addresses
- ✅ `review` - Doctor reviews
- ✅ `faviroute` - Bookmarked doctors (typo in table name)
- ✅ `banner` - Homepage banners
- ✅ `blog` - Blog posts
- ✅ `offer` - Special offers/coupons
- ✅ `subscription` - Doctor subscription plans
- ✅ `doctor_subscription` - Doctor subscription purchases
- ✅ `settle` - Doctor commission settlements
- ✅ `notification_template` - Email/SMS templates
- ✅ `language` - Multi-language support
- ✅ `settings` - System-wide settings
- ✅ `video_call_history` - Video call records
- ✅ `zoom_oauth` - Zoom OAuth tokens

---

## 🔌 Integrations

### 1. **Stripe Payment Integration** ✅

**What's Integrated:**
- ✅ Stripe Checkout for prescription payments
- ✅ Webhook handling for payment confirmation
- ✅ Secure payment token storage
- ✅ Payment status tracking
- ✅ Automatic PDF generation post-payment

**Implementation Details:**
- Controller: `App\Http\Controllers\Website\PrescriptionPaymentController`
- Routes:
  - `GET /prescription/pay/{id}` - Payment page
  - `POST /prescription/create-checkout-session/{id}` - Create Stripe session
  - `POST /stripe/webhook` - Stripe webhook handler
  - `GET /prescription/payment/success/{id}` - Success page
  - `GET /prescription/payment/cancel/{id}` - Cancel page

**Settings:**
- Stripe publishable key
- Stripe secret key
- Stripe webhook secret

### 2. **Zoom Video Integration** ✅

**What's Integrated:**
- ✅ Zoom OAuth 2.0 authentication
- ✅ Automatic meeting creation for appointments
- ✅ Meeting details storage (meeting ID, passcode, URLs)
- ✅ Join links for patients

**Implementation Details:**
- Controller: `App\Http\Controllers\Doctor\ZoomOAuthController`
- Routes:
  - `GET /create_zoom_meeting/{appointment_id}` - Create meeting
  - `GET /zoom-oauth-callback` - OAuth callback
- Database: `zoom_oauth`, `video_call_history` tables

**Settings:**
- Zoom Client ID
- Zoom Client Secret
- Zoom Account ID

### 3. **Email Integration** ✅

**What's Integrated:**
- ✅ SMTP email sending
- ✅ Customizable email templates
- ✅ OTP verification emails
- ✅ Appointment confirmation emails
- ✅ Password reset emails
- ✅ Prescription notification emails
- ✅ Test mail functionality

**Settings:**
- SMTP host, port, username, password
- From email and name

### 4. **SMS/OTP Integration** ✅

**What's Integrated:**
- ✅ Phone number verification
- ✅ OTP generation and sending
- ✅ OTP verification

**Use Cases:**
- Doctor registration verification
- Pharmacy registration verification
- Lab registration verification
- User phone verification

### 5. **Google Calendar Integration** ✅

**What's Integrated:**
- ✅ "Add to Calendar" links for appointments
- ✅ Automatic calendar event generation with:
  - Event title
  - Start/end time
  - Location
  - Description

**Implementation:**
- Controller: `App\Http\Controllers\Website\CalenderController`
- Route: `GET /add-to-calendar/{appointment_id}`

### 6. **Cannaleo API (In Documentation - Ready for Implementation)** 📋

**Planned Integration:**
- ✅ Documentation complete
- ⏳ Awaiting client API credentials
- ⏳ Hourly medicine sync from Cannaleo catalog
- ⏳ Prescription submission to Cannaleo
- ⏳ Real-time stock updates

**Reference:** `CANNALEO_INTEGRATION_COMPLETE_GUIDE.md`

---

## ⚙️ Admin Panel Features

### Super Admin Dashboard

#### 1. **Dashboard Overview**
- ✅ Total users count
- ✅ Total doctors count
- ✅ Total appointments (today, this week, this month)
- ✅ Revenue statistics
- ✅ Recent appointments
- ✅ Pending pharmacy approvals
- ✅ Pending questionnaires

#### 2. **User Management**
- ✅ View all patients
- ✅ Add new patient
- ✅ Edit patient details
- ✅ Change patient status (active/inactive)
- ✅ Delete patient
- ✅ Login as patient (impersonation)
- ✅ Bulk delete patients
- ✅ Export patient list

#### 3. **Doctor Management**
- ✅ View all doctors
- ✅ Add new doctor
- ✅ Edit doctor details:
  - Personal info
  - Hospital assignment
  - Doctor role (ADMIN_DOCTOR/SUB_DOCTOR)
  - Treatment assignments
  - Category assignments
  - Commission settings
- ✅ Manage doctor schedule/timeslots
- ✅ Change doctor status
- ✅ Login as doctor
- ✅ View doctor appointments
- ✅ View doctor earnings/commission
- ✅ Bulk delete doctors

#### 4. **Hospital Management**
- ✅ View all hospitals
- ✅ Add new hospital
- ✅ Edit hospital details
- ✅ Manage hospital gallery
- ✅ Change hospital status
- ✅ Delete hospital
- ✅ View hospital doctors
- ✅ View hospital appointments

#### 5. **Pharmacy Management**
- ✅ View all pharmacies
- ✅ Add new pharmacy
- ✅ Edit pharmacy details
- ✅ Approve/Reject pharmacy
- ✅ Set priority pharmacy
- ✅ Manage pharmacy schedule
- ✅ View pharmacy inventory
- ✅ View pharmacy orders
- ✅ Manage commission settings
- ✅ View settlement history
- ✅ Mark settlements as paid
- ✅ Change pharmacy status
- ✅ Bulk delete pharmacies

#### 6. **Laboratory Management**
- ✅ View all labs
- ✅ Add new lab
- ✅ Edit lab details
- ✅ Manage pathology categories
- ✅ Manage pathology tests
- ✅ Manage radiology categories
- ✅ Manage radiology tests
- ✅ View test bookings
- ✅ View test reports
- ✅ Upload reports
- ✅ Manage commission settings
- ✅ View settlement history
- ✅ Change lab status

#### 7. **Medicine Management**
- ✅ View all medicines
- ✅ Add new medicine (global catalog)
- ✅ Edit medicine details
- ✅ Manage medicine brands
- ✅ Assign medicines to categories
- ✅ View pharmacy-specific pricing
- ✅ Manage stock levels (per pharmacy)
- ✅ Change medicine status
- ✅ Bulk delete medicines
- ✅ Medicine image upload

#### 8. **Category & Treatment Management**
- ✅ View all treatments
- ✅ Add new treatment
- ✅ Edit treatment details
- ✅ View all categories
- ✅ Add new category
- ✅ Edit category details
- ✅ Assign categories to treatments
- ✅ Manage expertise within categories
- ✅ Change status
- ✅ Bulk delete

#### 9. **Questionnaire Management**
- ✅ View all questionnaires
- ✅ Add new questionnaire
- ✅ Edit questionnaire details
- ✅ Manage sections:
  - Add section
  - Edit section
  - Reorder sections
  - Delete section
- ✅ Manage questions:
  - Add question
  - Edit question
  - Set field type
  - Set validation rules
  - Reorder questions
  - Delete question
- ✅ Assign questionnaire to category
- ✅ Change questionnaire status
- ✅ View questionnaire submissions
- ✅ View submission details
- ✅ Export submissions

#### 10. **Appointment Management**
- ✅ View all appointments
- ✅ Calendar view
- ✅ Filter by:
  - Status
  - Date range
  - Doctor
  - Patient
  - Hospital
- ✅ Create appointment manually
- ✅ Edit appointment
- ✅ Cancel appointment
- ✅ View appointment details
- ✅ View prescription (if created)
- ✅ View commission breakdown
- ✅ Bulk delete appointments

#### 11. **Settings Management**

**General Settings:**
- ✅ Site name
- ✅ Logo
- ✅ Favicon
- ✅ Contact details
- ✅ Social media links
- ✅ Currency settings

**Payment Settings:**
- ✅ Stripe configuration
- ✅ Payment gateway selection
- ✅ Commission rates (default)

**Notification Settings:**
- ✅ Email templates
- ✅ SMS templates
- ✅ Notification triggers
- ✅ Test email functionality

**Video Call Settings:**
- ✅ Zoom configuration
- ✅ OAuth credentials

**Verification Settings:**
- ✅ Email verification on/off
- ✅ Phone verification on/off
- ✅ OTP settings

**License Settings:**
- ✅ Purchase code
- ✅ License verification

#### 12. **Reports**
- ✅ User report:
  - Filter by date range
  - Export to Excel/PDF
- ✅ Doctor report:
  - Appointments count
  - Revenue generated
  - Commission breakdown
  - Filter by date range
  - Export to Excel/PDF
- ✅ Revenue report
- ✅ Appointment report
- ✅ Commission report

#### 13. **Content Management**
- ✅ Banner management (homepage sliders)
- ✅ Blog management:
  - Create blog post
  - Edit blog post
  - Featured image
  - Categories
  - Publish/unpublish
- ✅ Offer management:
  - Create offer/coupon
  - Set discount percentage/amount
  - Set validity period
  - Coupon code generation
  - Usage limits
- ✅ Static pages:
  - About Us
  - Privacy Policy
  - Terms & Conditions

#### 14. **Role & Permission Management**
- ✅ View all roles
- ✅ Create custom roles
- ✅ Edit role permissions
- ✅ Assign roles to users
- ✅ Permission matrix
- ✅ Admin user management

#### 15. **Subscription Management**
- ✅ View all subscription plans
- ✅ Create subscription plan
- ✅ Edit plan details:
  - Name
  - Price
  - Duration
  - Features
- ✅ View subscription purchases
- ✅ View subscription history
- ✅ Change payment status

#### 16. **Language Management**
- ✅ View all languages
- ✅ Add new language
- ✅ Edit language
- ✅ Upload language JSON file
- ✅ Download sample language file
- ✅ Set default language
- ✅ Change language status

#### 17. **Notification Template Management**
- ✅ View all templates
- ✅ Edit template content
- ✅ Use variables in templates
- ✅ Test templates

#### 18. **System Utilities**
- ✅ Cache clear
- ✅ Route clear
- ✅ View clear
- ✅ Config clear
- ✅ Permission cache clear
- ✅ System installer

---

## 🔐 Security & Permissions

### Authentication
- ✅ Laravel Sanctum for API authentication
- ✅ Session-based web authentication
- ✅ Password hashing (bcrypt)
- ✅ Remember me functionality
- ✅ Password reset via email
- ✅ Email verification
- ✅ Phone OTP verification

### Authorization
- ✅ **Spatie Laravel Permission:**
  - Role-based access control (RBAC)
  - Permission-based access control
  - Role hierarchy
- ✅ **Custom Middleware:**
  - XSS Sanitizer middleware
  - CSRF protection (with exceptions for webhooks)
  - Authentication middleware
  - Role-based route protection

### Data Security
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS prevention (input sanitization)
- ✅ CSRF protection
- ✅ Encrypted sensitive data
- ✅ Secure file uploads with validation
- ✅ Payment token security

### Hospital-Based Data Isolation
- ✅ **Questionnaire Scoping:**
  - Doctors only see questionnaires from their hospital
  - Automatic hospital assignment on submission
  - Cross-hospital access prevention
- ✅ **Appointment Scoping:**
  - Doctors only see appointments for their hospital
  - Hospital-based filtering

### Locking Mechanism
- ✅ **Questionnaire Review Locking:**
  - Prevents concurrent reviews by multiple doctors
  - `reviewing_doctor_id` field for lock tracking
  - Automatic unlock on approval/rejection
  - ADMIN_DOCTOR can view locked questionnaires

### Role-Specific Permissions

**Super Admin:**
- Full system access
- All CRUD operations
- Settings management
- User impersonation

**ADMIN_DOCTOR:**
- View all hospital questionnaires
- Monitor sub-doctor activities
- Access hospital-wide reports
- Manage hospital settings

**SUB_DOCTOR:**
- View assigned category questionnaires
- Lock and review questionnaires
- Create prescriptions
- Manage personal appointments
- View personal commission

**Pharmacy:**
- Manage own inventory
- View own orders
- Update order status
- View own commission

**Laboratory:**
- Manage own tests
- View own bookings
- Upload reports
- View own commission

**Patient:**
- Complete questionnaires
- Book appointments
- Purchase medicines
- Book lab tests
- View personal data only

---

## 📈 System Statistics

### Current Implementation Status

| Feature Category | Status | Percentage |
|-----------------|--------|------------|
| User Management | ✅ Complete | 100% |
| Questionnaire System | ✅ Complete | 100% |
| Appointment System | ✅ Complete | 100% |
| Medicine System | ✅ Complete | 100% |
| Prescription System | ✅ Complete | 100% |
| Laboratory System | ✅ Complete | 100% |
| Payment Integration | ✅ Complete | 100% |
| Video Consultation | ✅ Complete | 100% |
| Commission System | ✅ Complete | 100% |
| Admin Panel | ✅ Complete | 100% |
| Multi-language | ✅ Complete | 100% |
| Cannaleo Integration | 📋 Documented | 0% (Ready) |

### Database
- **Total Tables:** 50+
- **Models:** 40+
- **Migrations:** 31 (recent major updates)
- **Seeders:** 6

### Controllers
- **Total Controllers:** 35+
- **API Controllers:** 2 (UserApiController, DoctorApiController)
- **Web Controllers:** 30+

### Routes
- **Total Routes:** 300+
- **Public Routes:** 30+
- **Authenticated Routes:** 270+
- **Admin Routes:** 150+
- **Doctor Routes:** 40+
- **Pharmacy Routes:** 20+
- **Lab Routes:** 15+

---

## 🎯 Key Strengths

1. **✅ Comprehensive Questionnaire System**
   - Category-based organization
   - Doctor review workflow with locking
   - Hospital-based scoping
   - Post-submission patient flow
   - Medicine selection integration

2. **✅ Role-Based Architecture**
   - Clear separation of concerns
   - Proper permission management
   - Hospital hierarchy (ADMIN_DOCTOR/SUB_DOCTOR)
   - Multi-role support

3. **✅ Complete Payment Integration**
   - Stripe Checkout
   - Webhook handling
   - Secure payment processing
   - Automatic PDF generation

4. **✅ Medicine Management**
   - Global catalog
   - Pharmacy-specific inventory
   - Category-based assignment
   - Flexible pricing

5. **✅ Commission System**
   - Doctor commission tracking
   - Pharmacy commission tracking
   - Lab commission tracking
   - Settlement management

6. **✅ Video Consultation**
   - Zoom integration
   - OAuth authentication
   - Automatic meeting creation

7. **✅ Multi-Tenant Support**
   - Hospital isolation
   - Pharmacy multi-tenancy
   - Lab multi-tenancy

8. **✅ Comprehensive Admin Panel**
   - Full CRUD for all entities
   - Reports and analytics
   - Settings management
   - User impersonation

---

## 📝 Recent Major Updates (2026)

### January 2026 Updates:

1. **✅ Questionnaire System Overhaul**
   - Changed from treatment-based to category-based
   - Added status tracking (pending, IN_REVIEW, approved, rejected)
   - Implemented locking mechanism
   - Added hospital-based scoping

2. **✅ Doctor-Hospital Hierarchy**
   - Fixed hospital_id relationship (proper foreign key)
   - Added doctor_role (ADMIN_DOCTOR/SUB_DOCTOR)
   - Moved treatment/category assignments to pivot tables
   - Implemented proper access control

3. **✅ Medicine System Restructuring**
   - Made medicines global (removed pharmacy_id)
   - Created pharmacy_inventory table
   - Added medicine_brands table
   - Created category_medicine pivot table

4. **✅ Prescription Payment Integration**
   - Added Stripe payment fields
   - Implemented webhook handling
   - Added payment status tracking
   - Automatic PDF generation

5. **✅ Pharmacy Enhancements**
   - Added postcode for location-based search
   - Added is_priority flag
   - Changed status to enum (pending, approved, rejected)
   - Approval workflow

6. **✅ Post-Questionnaire Patient Flow**
   - Delivery choice (home delivery vs pharmacy pickup)
   - Address management
   - Pharmacy selection
   - Medicine selection
   - Complete submission tracking

---

## 🚀 System Capabilities Summary

### What Patients Can Do:
✅ Browse categories and complete questionnaires  
✅ Book doctor appointments with video consultation  
✅ Purchase medicines from pharmacies  
✅ Book lab tests and download reports  
✅ Pay for prescriptions securely  
✅ Manage multiple addresses  
✅ Review doctors  
✅ Track order history  

### What Doctors Can Do:
✅ Review and approve patient questionnaires  
✅ Lock questionnaires for exclusive review  
✅ Create prescriptions  
✅ Manage appointments  
✅ Conduct video consultations  
✅ Track commission earnings  
✅ Set availability schedule  

### What Pharmacies Can Do:
✅ Manage inventory (add medicines, set prices, track stock)  
✅ View and fulfill orders  
✅ Set working hours  
✅ Track commission  
✅ Manage profile  

### What Labs Can Do:
✅ Manage tests (pathology and radiology)  
✅ View test bookings  
✅ Upload test reports  
✅ Track commission  
✅ Manage profile  

### What Super Admin Can Do:
✅ **Everything** - Full system control  
✅ Manage all users, doctors, pharmacies, labs  
✅ Configure system settings  
✅ Create questionnaires  
✅ Generate reports  
✅ Manage content (blogs, banners, offers)  
✅ Handle commissions and settlements  
✅ User impersonation for testing/support  

---

## 📞 Technical Stack

- **Framework:** Laravel 10+
- **Database:** MySQL
- **Frontend:** Blade Templates, Bootstrap, jQuery
- **Authentication:** Laravel Sanctum
- **Authorization:** Spatie Laravel Permission
- **Payment:** Stripe
- **Video:** Zoom OAuth
- **Email:** SMTP
- **File Storage:** Local/S3 (configurable)
- **Queue:** Database/Redis (configurable)
- **Cache:** Redis/File (configurable)

---

## ✅ Conclusion

The **BackupDoctor** system is a **fully functional, production-ready healthcare management platform** with:

- ✅ Complete patient-doctor-pharmacy-lab workflow
- ✅ Advanced questionnaire system with hospital-based scoping
- ✅ Payment integration (Stripe)
- ✅ Video consultation (Zoom)
- ✅ Commission tracking and settlement
- ✅ Comprehensive admin panel
- ✅ Multi-language support
- ✅ Role-based access control
- ✅ Hospital hierarchy with ADMIN_DOCTOR/SUB_DOCTOR roles
- ✅ Pharmacy inventory management
- ✅ Lab test management
- ✅ Medicine ordering system

**All core features are working and tested.**

---

**Document Created:** January 28, 2026  
**Last Updated:** January 28, 2026  
**Status:** ✅ Complete and Accurate

