# 🏥 BackupDoctor System - Complete Working Features Documentation

**Document Version:** 1.0  
**Date:** January 28, 2026  
**System:** Healthcare Management Platform (Laravel)  
**Purpose:** Comprehensive documentation of all working features and workflows

---

## 📋 Table of Contents

1. [System Overview](#system-overview)
2. [User Types & Roles](#user-types--roles)
3. [Core Features](#core-features)
4. [Detailed Workflows](#detailed-workflows)
5. [Database Architecture](#database-architecture)
6. [Integrations](#integrations)
7. [Admin Panel Features](#admin-panel-features)
8. [Security & Permissions](#security--permissions)

---

## 🎯 System Overview

**BackupDoctor** is a comprehensive healthcare management platform built with Laravel that connects patients, doctors, pharmacies, and laboratories. The system specializes in questionnaire-based patient assessment with automated workflow management.

### Key Capabilities
- ✅ Multi-role user management (Super Admin, Doctors, Patients, Pharmacies, Labs)
- ✅ Category-based medical questionnaires with doctor review
- ✅ Appointment booking and management
- ✅ Prescription management with Stripe payment integration
- ✅ Medicine catalog with pharmacy-specific inventory
- ✅ Multi-language support

---

## 👥 User Types & Roles

### 1. **Super Admin** 
**Access Level:** Full system control

**Capabilities:**
- ✅ Manage all entities (users, doctors, pharmacies, hospitals
- ✅ Create and manage questionnaires
- ✅ View all  prescriptions
- ✅ Manage medicine catalog and brands
- ✅ Configure categories, treatments
- ✅ Multi-language configuration
- ✅ Login as any user (impersonation)

### 2. **Doctors** 
**Two Sub-Types:**

#### A. **ADMIN_DOCTOR** (Hospital Administrator)
**Capabilities:**
- ✅ View all questionnaires in their hospital
- ✅ Monitor which SUB_DOCTOR is reviewing what
- ✅ Can review any questionnaire in their hospital
- ✅ Manage hospital-wide settings
- ✅ Access complete hospital analytics

#### B. **SUB_DOCTOR** (Specialist/Reviewer)
**Capabilities:**
- ✅ View pending questionnaires for assigned categories
- ✅ Lock questionnaire when opening (exclusive review)
- ✅ Review and approve/reject questionnaires
- ✅ Create prescriptions for approved questionnaires

**Common Doctor Features:**
- ✅ Personal profile management
- ✅ Prescription creation and management
- ✅ Patient review system

### 3. **Patients (Users)**
**Capabilities:**
- ✅ Browse categories and treatments
- ✅ Complete medical questionnaires
- ✅ Choose delivery method (home delivery or pharmacy pickup)
- ✅ Select pharmacy for pickup
- ✅ Choose medicines from category-specific lists
- ✅ Purchase medicines from pharmacies
- ✅ Manage multiple addresses
- ✅ Profile and password management
- ✅ Download prescription PDFs
- ✅ Account deletion

### 4. **Pharmacies**
**Capabilities:**
- ✅ Manage pharmacy inventory (medicine stock and pricing)
- ✅ View and fulfill medicine orders
- ✅ Track order history
- ✅ Priority pharmacy flagging

**Pharmacy Approval Workflow:**
- Pending → Approved/Rejected by Super Admin
- Only approved pharmacies visible to patients

## 🚀 Core Features

### 1. **Questionnaire System** ⭐ (Primary Feature)

**Category-Based Workflow:**

#### Patient Side:
1. ✅ Browse categories (e.g., Hairfall Treatment, Weight Management)
2. ✅ Select category and view questionnaire
3. ✅ Single-page or section-wise questionnaire completion
4. ✅ Multiple field types supported:
   - Text input
   - Textarea
   - Number input
   - Dropdown/Select
   - Radio buttons
   - Checkboxes
5. ✅ Auto-save answers (real-time saving)
6. ✅ Progress tracking
7. ✅ Submit questionnaire

#### Doctor Review Workflow:
1. ✅ **Status: Pending** → Visible to all category doctors in hospital
2. ✅ **Doctor opens questionnaire** → Status changes to `IN_REVIEW`
3. ✅ **Locking mechanism:**
   - Sets `reviewing_doctor_id` to current doctor
   - Other SUB_DOCTORs cannot access (hidden from their list)
   - ADMIN_DOCTORs can still view (monitoring)
4. ✅ **Doctor reviews answers:**
   - View all patient responses
   - Flag problematic answers
   - Add internal notes
5. ✅ **Approval/Rejection:**
   - Approve → Unlocks questionnaire, triggers post-submission flow
   - Reject → Patient notified, can resubmit
6. ✅ **Hospital scoping:**
   - Doctors only see questionnaires from their hospital
   - Complete isolation between hospitals

#### Post-Approval Patient Flow:
1. ✅ **Delivery Choice:**
   - Home Delivery OR Pharmacy Pickup
2. ✅ **If Home Delivery:**
   - Provide/select delivery address
   - Enter postcode, city, state, full address
3. ✅ **If Pharmacy Pickup:**
   - Select from approved pharmacies
   - View pharmacy locations
4. ✅ **Medicine Selection:**
   - Category-specific medicine list
   - Multi-select medicines
   - View medicine details (strength, form, brand)
5. ✅ **Prescription Creation:**
   - Doctor creates prescription with selected medicines
   - System generates prescription PDF (this is not completely finished)
6. ✅ **Payment:**
   - Stripe Checkout integration
   - Prescription fee payment
   - Automatic PDF generation after payment
7. ✅ **Success/Completion**

**Advanced Questionnaire Features:**
- ✅ Conditional logic for questions
- ✅ Answer validation rules
- ✅ Required field enforcement
- ✅ Section-wise navigation
- ✅ Answer editing before submission
- ✅ Submission tracking and history


### 3. **Medicine Management System**

#### Global Medicine Catalog:
- ✅ Centralized medicine database
- ✅ Medicine details:
  - Name
  - Strength (e.g., 500mg)
  - Form (tablet, capsule, syrup)
  - Brand association
  - Image
  - Description
- ✅ Category-medicine mapping (many-to-many)
- ✅ Medicine brands management

#### Pharmacy-Specific Inventory:
- ✅ Each pharmacy sets own pricing
- ✅ Stock quantity management
- ✅ Low stock threshold alerts
- ✅ Medicine availability status
- ✅ Inventory updates

#### Medicine Ordering:
- ✅ Patients add medicines to cart
- ✅ View cart and update quantities
- ✅ Apply delivery charges
- ✅ Choose delivery address
- ✅ Select delivery timeslot
- ✅ Make payment
- ✅ Order tracking
- ✅ Order history

---

### 4. **Prescription System**

#### Prescription Creation:
- ✅ Post-appointment prescription
- ✅ Post-questionnaire prescription
- ✅ Add multiple medicines
- ✅ Specify dosage and duration
- ✅ Add doctor notes/instructions

#### Prescription Payment:
- ✅ **Stripe Checkout integration:**
  - Create checkout session
  - Secure payment processing
  - Webhook handling for confirmation
- ✅ **Payment flow:**
  1. Prescription status: `approved_pending_payment`
  2. Patient pays via Stripe
  3. Webhook confirms payment
  4. Status: `approved_paid`
  5. PDF generated and available for download
- ✅ **Payment fields:**
  - Payment amount
  - Payment status
  - Payment token
  - Payment method
  - Payment date
  - Stripe session ID

#### Prescription Features:
- ✅ PDF generation with doctor signature
- ✅ Download prescription
- ✅ Email prescription to patient
- ✅ Prescription validity tracking
- ✅ Prescription history



### 8. **Hospital Management**

#### Hospital Features:
- ✅ Hospital profiles:
  - Name, address, contact
  - Image/logo
  - Description
  - Working hours
- ✅ Hospital gallery (multiple images)
- ✅ Hospital-doctor relationship (one-to-many)

#### Hospital Hierarchy:
- ✅ ADMIN_DOCTOR oversees hospital operations
- ✅ SUB_DOCTORs work under hospital
- ✅ Questionnaires automatically assigned to hospital
- ✅ Cross-hospital isolation



### 10. **Multi-Language Support**

#### Capabilities:
- ✅ Admin adds new languages
- ✅ Language JSON file upload
- ✅ Patient selects preferred language
- ✅ All interface text translatable
- ✅ Language-specific content
- ✅ RTL support for Arabic/Hebrew

