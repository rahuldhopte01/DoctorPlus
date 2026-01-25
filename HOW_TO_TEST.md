# How to Test the Hospital-Doctor Hierarchy Implementation

- **Task Group 1:** Hospital–doctor hierarchy, questionnaire locking, SUB_DOCTOR / ADMIN_DOCTOR (Quick Start below).
- **Task Group 2:** [Patient flow after questionnaire submission](#task-group-2-patient-flow-after-questionnaire-submission) (delivery → address/pharmacy → medicine selection → success).

## 🚀 Quick Start (5 Minutes)

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Set Up Test Data (Run in your database)

```sql
-- Get your first hospital ID
SELECT id, name FROM hospital LIMIT 1;

-- Update doctors (replace [HOSPITAL_ID] and doctor IDs with your actual IDs)
UPDATE doctor SET hospital_id = [HOSPITAL_ID], doctor_role = 'ADMIN_DOCTOR' WHERE id = [DOCTOR_ID_1];
UPDATE doctor SET hospital_id = [HOSPITAL_ID], doctor_role = 'SUB_DOCTOR' WHERE id = [DOCTOR_ID_2];
UPDATE doctor SET hospital_id = [HOSPITAL_ID], doctor_role = 'SUB_DOCTOR' WHERE id = [DOCTOR_ID_3];
```

### 3. Test in Browser

#### Test 1: Pending – visible to all category doctors
1. Ensure a questionnaire is **pending** (not yet opened by any doctor).
2. Login as a SUB_DOCTOR or ADMIN_DOCTOR assigned to that **category**.
3. Go to: `/doctor/questionnaires`
4. **Expected:** You see the pending questionnaire (all doctors of that category see it).

#### Test 2: Open pending → under review, only opener + admin see it
1. As a SUB_DOCTOR, click a **pending** questionnaire.
2. **Expected:** Status changes to IN_REVIEW, `reviewing_doctor_id` = your ID (it locks to you).
3. Login as **another SUB_DOCTOR** (same category, same hospital). Go to `/doctor/questionnaires`.
4. **Expected:** That questionnaire does **not** appear in their list.
5. Login as **ADMIN_DOCTOR**. Go to `/doctor/questionnaires`.
6. **Expected:** The questionnaire **does** appear (admin sees all, including under-review).
7. If the other SUB_DOCTOR tries to open it via direct URL, **expected:** Redirect with error “under review by another doctor”.

#### Test 3: ADMIN_DOCTOR visibility and opening
1. Login as ADMIN_DOCTOR, go to `/doctor/questionnaires`.
2. **Expected:** See all questionnaires in your hospital (pending + under-review).
3. Open a **pending** one.
4. **Expected:** It locks (status = IN_REVIEW, `reviewing_doctor_id` = admin). Only that admin and sub-doctors (when applicable) see it per rules.

#### Test 4: Review completion
1. As the SUB_DOCTOR who is reviewing, set status to `approved` (or `rejected`).
2. **Expected:** Questionnaire unlocks (`reviewing_doctor_id` = NULL).

## 📋 Detailed Testing Checklist

### ✅ Database Structure
- [ ] `doctor` table has `hospital_id` as BIGINT
- [ ] `doctor` table has `doctor_role` as ENUM('ADMIN_DOCTOR', 'SUB_DOCTOR')
- [ ] `questionnaire_answers` table has `reviewing_doctor_id` column
- [ ] `questionnaire_answers` table has `hospital_id` column
- [ ] Status enum includes 'IN_REVIEW' and 'REVIEW_COMPLETED'

### ✅ Doctor Setup
- [ ] Doctors have `hospital_id` assigned
- [ ] Doctors have `doctor_role` set (ADMIN_DOCTOR or SUB_DOCTOR)
- [ ] Doctors are linked to categories via `doctor_category` table

### ✅ Questionnaire Submission
- [ ] When questionnaire is submitted, `hospital_id` is automatically set
- [ ] `hospital_id` is determined from doctors handling that category

### ✅ SUB_DOCTOR Functionality
- [ ] Sees **pending** questionnaires for categories they are assigned to (same hospital).
- [ ] Sees **under-review** questionnaires only if they are the reviewer (`reviewing_doctor_id` = them).
- [ ] Does **not** see questionnaires under review by another sub-doctor (hidden from list, no URL access).
- [ ] Opening a pending questionnaire locks it (status → IN_REVIEW, `reviewing_doctor_id` set).
- [ ] Can update status only for questionnaires they are reviewing; questionnaire unlocks on approve/reject.

### ✅ ADMIN_DOCTOR Functionality
- [ ] Sees **all** questionnaires in their hospital (pending + under-review).
- [ ] Opening a pending questionnaire locks it to the admin (status → IN_REVIEW, `reviewing_doctor_id` = admin).
- [ ] Can **change status** for any questionnaire in their hospital (approved, rejected, etc.); can view any in hospital.

### ✅ Hospital Scoping
- [ ] Doctors from Hospital A cannot see questionnaires from Hospital B
- [ ] All queries filter by `hospital_id`

## 🔍 Verification Queries

Run these SQL queries to verify everything:

```sql
-- 1. Check doctors setup
SELECT id, name, hospital_id, doctor_role, 
       (SELECT name FROM hospital WHERE id = doctor.hospital_id) as hospital_name
FROM doctor 
WHERE hospital_id IS NOT NULL;

-- 2. Check questionnaire submissions
SELECT 
    qa.id,
    qa.status,
    qa.reviewing_doctor_id,
    qa.hospital_id,
    u.name as patient_name,
    h.name as hospital_name,
    d.name as reviewing_doctor_name
FROM questionnaire_answers qa
LEFT JOIN users u ON qa.user_id = u.id
LEFT JOIN hospital h ON qa.hospital_id = h.id
LEFT JOIN doctor d ON qa.reviewing_doctor_id = d.id
WHERE qa.appointment_id IS NULL
ORDER BY qa.submitted_at DESC
LIMIT 10;

-- 3. Check locked questionnaires
SELECT 
    qa.id,
    qa.status,
    qa.reviewing_doctor_id,
    d.name as reviewing_doctor,
    h.name as hospital_name
FROM questionnaire_answers qa
LEFT JOIN doctor d ON qa.reviewing_doctor_id = d.id
LEFT JOIN hospital h ON qa.hospital_id = h.id
WHERE qa.status IN ('IN_REVIEW', 'under_review')
AND qa.reviewing_doctor_id IS NOT NULL
AND qa.appointment_id IS NULL;
```

## 🐛 Troubleshooting

### Problem: "Doctor is not assigned to a hospital"
**Solution:**
```sql
UPDATE doctor SET hospital_id = [HOSPITAL_ID] WHERE id = [DOCTOR_ID];
```

### Problem: No questionnaires visible
**Check:**
1. Doctor has hospital_id? → `SELECT hospital_id FROM doctor WHERE id = [ID];`
2. Questionnaires have hospital_id? → `SELECT hospital_id FROM questionnaire_answers WHERE appointment_id IS NULL;`
3. Doctor's role? → `SELECT doctor_role FROM doctor WHERE id = [ID];`

### Problem: Cannot lock questionnaire
**Check:**
1. Are you a SUB_DOCTOR? → `SELECT doctor_role FROM doctor WHERE id = [ID];`
2. Is questionnaire already locked? → `SELECT reviewing_doctor_id FROM questionnaire_answers WHERE id = [ID];`
3. Same hospital? → Compare doctor's hospital_id with questionnaire's hospital_id

## 📝 Test Scenarios

### Scenario 1: SUB_DOCTOR Workflow
1. SUB_DOCTOR logs in
2. Sees list of unlocked questionnaires from their hospital
3. Clicks on one → It locks automatically
4. Reviews and approves → Questionnaire unlocks
5. Other SUB_DOCTORs can now see it

### Scenario 2: ADMIN_DOCTOR Monitoring
1. ADMIN_DOCTOR logs in
2. Sees all questionnaires from their hospital
3. Sees which SUB_DOCTOR is reviewing which questionnaire
4. Can view locked questionnaires but cannot edit them

### Scenario 3: Hospital Isolation
1. Doctor from Hospital A logs in
2. Cannot see questionnaires from Hospital B
3. Cannot access Hospital B questionnaires even with direct URL

## ✅ Success Criteria

You'll know everything works when:
- ✅ SUB_DOCTORs only see their assigned/unlocked questionnaires
- ✅ ADMIN_DOCTORs see all questionnaires from their hospital
- ✅ Questionnaires lock when SUB_DOCTOR opens them
- ✅ Other SUB_DOCTORs cannot access locked questionnaires
- ✅ Questionnaires unlock when review is completed
- ✅ Hospital scoping prevents cross-hospital access

---

## TASK GROUP 2: PATIENT FLOW AFTER QUESTIONNAIRE SUBMISSION

This section covers testing the **post-questionnaire patient flow**: delivery choice → address/pharmacy → medicine selection → success.

### 1. Seed Medicine Test Data

Medicine selection shows **category-specific** medicines (no Generic/Branded/Premium). Seed data with:

```bash
php artisan db:seed --class=QuestionnairePatientFlowTestDataSeeder
```

This creates:

- **Brands:** Generic, Bayer, Pfizer  
- **Medicines (9):** Paracetamol, Ibuprofen, Amoxicillin, Panadol, Nurofen, Amoxil, Omeprazole, Loratadine, Cetirizine  
- **category_medicine links:** Each category gets a rotating subset of these medicines (different categories → different options).

Ensure **categories** exist before seeding. The seeder assigns medicines to all existing categories.

**Admin: Assign categories to medicine**  
When creating or editing a medicine (Super Admin → Medicine → Add / Edit), use the **Categories** multi-select to assign that medicine to one or more categories. Only medicines assigned to a category appear on the patient’s medicine selection page when they submit that category’s questionnaire.

### 2. Prerequisites

Before testing the flow, ensure:

- [ ] **Patient user** – You can log in as a patient (e.g. `/patient-login`).
- [ ] **Category with questionnaire** – A category has an associated questionnaire; you complete it and submit.
- [ ] **Delivery:** At least one **user address** (in `user_address`) for the patient, or you’ll add one on the delivery-address form.
- [ ] **Pickup:** At least one **pharmacy** with `status = 'approved'` (for pharmacy selection).

### 3. Patient Flow (Step-by-Step)

| Step | Action | URL / Route |
|------|--------|-------------|
| 1 | Log in as **patient** | `/patient-login` |
| 2 | Open a **category** that has a questionnaire | e.g. `/category/1` (use your category ID) |
| 3 | **Complete and submit** the questionnaire | Submit from questionnaire form |
| 4 | **Delivery choice** | `/questionnaire/category/{id}/delivery-choice` |
| 5a | **Delivery** → Enter or choose address | `/questionnaire/category/{id}/delivery-address` |
| 5b | **Pickup** → Choose pharmacy | `/questionnaire/category/{id}/pharmacy-selection` |
| 6 | **Select medicines** (category-specific list; multi-select) | `/questionnaire/category/{id}/medicine-selection` |
| 7 | **Success** | `/questionnaire/category/{id}/success` |

### 4. What to Verify

- **Delivery choice:** Choosing “Home Delivery” or “Pharmacy Pickup” saves `delivery_type` and redirects correctly.
- **Delivery:** Address form saves postcode, city, state, address; redirects to medicine selection.
- **Pickup:** Pharmacy list loads; selecting one saves `selected_pharmacy_id`; redirects to medicine selection.
- **Medicine selection:** A flat list of medicines for that category (from `category_medicine`). Select one or more; submitting saves `selected_medicines` (JSON) and redirects to success.
- **Success:** Success page displays; links to “Browse More Categories” and “Go to Home” work.

### 5. Verification Queries

```sql
-- Questionnaire submissions with delivery/medicine info
SELECT 
    qs.id,
    qs.user_id,
    qs.category_id,
    qs.delivery_type,
    qs.status,
    qs.selected_pharmacy_id,
    qs.delivery_address,
    qs.delivery_postcode,
    qs.delivery_city,
    qs.selected_medicines,
    qs.created_at
FROM questionnaire_submissions qs
ORDER BY qs.created_at DESC
LIMIT 10;

-- Medicines per category (category_medicine)
SELECT c.id AS category_id, c.name AS category_name, m.id AS medicine_id, m.name AS medicine_name
FROM category c
JOIN category_medicine cm ON cm.category_id = c.id
JOIN medicine m ON m.id = cm.medicine_id
WHERE m.status = 1
ORDER BY c.id, m.name;

-- All active medicines
SELECT m.id, m.name, m.strength, m.form, b.name AS brand_name FROM medicine m
LEFT JOIN medicine_brands b ON m.brand_id = b.id WHERE m.status = 1 ORDER BY m.name;
```

### 6. Troubleshooting (Task Group 2)

| Issue | What to check |
|-------|----------------|
| **“Please select delivery method first”** | Open delivery choice before medicine selection. Ensure `delivery_type` is set on the submission. |
| **“Please provide delivery address”** | For delivery, fill address form completely (address, postcode, city, state). |
| **“Please select a pharmacy”** | For pickup, select a pharmacy. Ensure at least one pharmacy has `status = 'approved'`. |
| **No medicines on selection page** | Run `QuestionnairePatientFlowTestDataSeeder`. Ensure `category_medicine` has rows for your category, and `medicine.status = 1`. |
| **Redirect to login** | Patient flow requires auth. Log in as patient before starting. |
| **Category / questionnaire not found** | Use a category ID that has a questionnaire. Check `category` and `questionnaires` tables. |

### 7. Quick Test Checklist (Task Group 2)

- [ ] Medicine seeder runs without errors.
- [ ] Submit questionnaire → redirect to delivery choice.
- [ ] **Delivery path:** Choose delivery → address → medicine selection → success.
- [ ] **Pickup path:** Choose pickup → pharmacy → medicine selection → success.
- [ ] Medicine selection shows category-specific medicines (multi-select).
- [ ] Submitting medicine selection redirects to success page.
- [ ] `questionnaire_submissions` row has `delivery_type`, address/pharmacy, and `selected_medicines` populated.

---

## 📚 Additional Resources

- **Full Testing Guide:** `TESTING_GUIDE_HOSPITAL_DOCTOR_HIERARCHY.md`
- **Quick Test Steps:** `QUICK_TEST_STEPS.md`
- **Implementation Summary:** `HOSPITAL_DOCTOR_HIERARCHY_IMPLEMENTATION.md`
- **SQL Setup Script:** `quick_test_setup.sql`
