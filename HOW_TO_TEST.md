# How to Test the Hospital-Doctor Hierarchy Implementation

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

#### Test 1: SUB_DOCTOR Visibility
1. Login as a SUB_DOCTOR
2. Go to: `/doctor/questionnaires`
3. **Expected:** Only see questionnaires from your hospital that are unlocked or assigned to you

#### Test 2: Locking Mechanism
1. As SUB_DOCTOR, click on an unlocked questionnaire
2. **Expected:** Questionnaire locks (status = IN_REVIEW, reviewing_doctor_id = your ID)
3. Check database:
   ```sql
   SELECT id, status, reviewing_doctor_id FROM questionnaire_answers 
   WHERE reviewing_doctor_id = [YOUR_DOCTOR_ID];
   ```

#### Test 3: ADMIN_DOCTOR Visibility
1. Login as ADMIN_DOCTOR
2. Go to: `/doctor/questionnaires`
3. **Expected:** See ALL questionnaires from your hospital (including locked ones)

#### Test 4: Review Completion
1. As SUB_DOCTOR, update status to `approved`
2. **Expected:** Questionnaire unlocks (reviewing_doctor_id = NULL)

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
- [ ] Only sees questionnaires from their hospital
- [ ] Only sees unlocked (pending) or assigned to them
- [ ] Cannot see questionnaires locked by other doctors
- [ ] Can lock questionnaire when opening it
- [ ] Can update status of questionnaires they locked
- [ ] Questionnaire unlocks when status is set to approved/rejected

### ✅ ADMIN_DOCTOR Functionality
- [ ] Sees ALL questionnaires from their hospital
- [ ] Can see which doctor is reviewing which questionnaire
- [ ] Can view locked questionnaires (read-only)
- [ ] Cannot edit questionnaires locked by other doctors

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

## 📚 Additional Resources

- **Full Testing Guide:** `TESTING_GUIDE_HOSPITAL_DOCTOR_HIERARCHY.md`
- **Quick Test Steps:** `QUICK_TEST_STEPS.md`
- **Implementation Summary:** `HOSPITAL_DOCTOR_HIERARCHY_IMPLEMENTATION.md`
- **SQL Setup Script:** `quick_test_setup.sql`
