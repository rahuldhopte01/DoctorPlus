# Testing Guide: Hospital-Doctor Hierarchy & Questionnaire Review Flow

## Prerequisites

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Verify Migrations Ran Successfully**
   ```bash
   php artisan migrate:status
   ```

## Step 1: Setup Test Data

### 1.1 Create/Verify Hospitals
```sql
-- Check existing hospitals
SELECT * FROM hospital;

-- If needed, create test hospitals
INSERT INTO hospital (name, phone, address, lat, lng, facility, status, created_at, updated_at)
VALUES 
('Test Hospital A', '1234567890', '123 Main St', '40.7128', '-74.0060', 'General', 1, NOW(), NOW()),
('Test Hospital B', '0987654321', '456 Oak Ave', '40.7580', '-73.9855', 'Specialty', 1, NOW(), NOW());
```

### 1.2 Assign Doctors to Hospitals and Set Roles
```sql
-- Check current doctor assignments
SELECT id, name, hospital_id, doctor_role FROM doctor;

-- Update doctors to assign hospitals and roles
-- Example: Make doctor ID 1 an ADMIN_DOCTOR at hospital 1
UPDATE doctor SET hospital_id = 1, doctor_role = 'ADMIN_DOCTOR' WHERE id = 1;

-- Example: Make doctor ID 2 a SUB_DOCTOR at hospital 1
UPDATE doctor SET hospital_id = 1, doctor_role = 'SUB_DOCTOR' WHERE id = 2;

-- Example: Make doctor ID 3 a SUB_DOCTOR at hospital 2
UPDATE doctor SET hospital_id = 2, doctor_role = 'SUB_DOCTOR' WHERE id = 3;
```

### 1.3 Verify Doctor-Category Relationships
```sql
-- Check which categories doctors handle
SELECT d.id, d.name, d.hospital_id, d.doctor_role, dc.category_id, c.name as category_name
FROM doctor d
LEFT JOIN doctor_category dc ON d.id = dc.doctor_id
LEFT JOIN category c ON dc.category_id = c.id
WHERE d.hospital_id IS NOT NULL;
```

## Step 2: Test Questionnaire Submission

### 2.1 Submit a Questionnaire (as a patient/user)
1. Login as a patient/user
2. Navigate to a category that has a questionnaire
3. Fill out and submit the questionnaire
4. Verify it's saved with `hospital_id` set

**Check in Database:**
```sql
-- Verify questionnaire was saved with hospital_id
SELECT 
    qa.id,
    qa.user_id,
    qa.category_id,
    qa.questionnaire_id,
    qa.hospital_id,
    qa.status,
    qa.reviewing_doctor_id,
    u.name as user_name,
    c.name as category_name,
    h.name as hospital_name
FROM questionnaire_answers qa
LEFT JOIN users u ON qa.user_id = u.id
LEFT JOIN category c ON qa.category_id = c.id
LEFT JOIN hospital h ON qa.hospital_id = h.id
WHERE qa.appointment_id IS NULL
ORDER BY qa.submitted_at DESC
LIMIT 10;
```

## Step 3: Test SUB_DOCTOR Visibility

### 3.1 Login as SUB_DOCTOR
1. Login as a doctor with `doctor_role = 'SUB_DOCTOR'`
2. Navigate to: `/doctor/questionnaires`

**Expected Results:**
- ✅ Should only see questionnaires from their hospital
- ✅ Should only see questionnaires that are:
  - `status = 'pending'` AND `reviewing_doctor_id IS NULL` (unlocked), OR
  - `reviewing_doctor_id = their doctor_id` (assigned to them)
- ❌ Should NOT see questionnaires locked by other doctors

**Database Check:**
```sql
-- Check what SUB_DOCTOR should see
SELECT 
    qa.id,
    qa.status,
    qa.reviewing_doctor_id,
    qa.hospital_id,
    d.name as reviewing_doctor_name,
    h.name as hospital_name
FROM questionnaire_answers qa
LEFT JOIN doctor d ON qa.reviewing_doctor_id = d.id
LEFT JOIN hospital h ON qa.hospital_id = h.id
WHERE qa.hospital_id = 1  -- Replace with SUB_DOCTOR's hospital_id
AND qa.appointment_id IS NULL
AND (
    (qa.status = 'pending' AND qa.reviewing_doctor_id IS NULL)
    OR qa.reviewing_doctor_id = 2  -- Replace with SUB_DOCTOR's doctor_id
);
```

### 3.2 Test Locking Mechanism (SUB_DOCTOR)
1. As SUB_DOCTOR, click on an unlocked questionnaire (`status = 'pending'`)
2. Navigate to review page

**Expected Results:**
- ✅ Questionnaire status changes to `IN_REVIEW`
- ✅ `reviewing_doctor_id` is set to the SUB_DOCTOR's ID
- ✅ `hospital_id` is set (if not already set)

**Database Check:**
```sql
-- Before opening
SELECT id, status, reviewing_doctor_id, hospital_id 
FROM questionnaire_answers 
WHERE id = [QUESTIONNAIRE_ANSWER_ID];

-- After opening (should see changes)
SELECT id, status, reviewing_doctor_id, hospital_id 
FROM questionnaire_answers 
WHERE id = [QUESTIONNAIRE_ANSWER_ID];
```

### 3.3 Test SUB_DOCTOR Cannot Access Locked Questionnaires
1. As SUB_DOCTOR A, open and lock a questionnaire
2. Login as SUB_DOCTOR B (same hospital)
3. Try to access the locked questionnaire

**Expected Results:**
- ❌ SUB_DOCTOR B should get error: "This questionnaire is currently being reviewed by another doctor"
- ❌ Should not see the questionnaire in their list (unless it's assigned to them)

## Step 4: Test ADMIN_DOCTOR Visibility

### 4.1 Login as ADMIN_DOCTOR
1. Login as a doctor with `doctor_role = 'ADMIN_DOCTOR'`
2. Navigate to: `/doctor/questionnaires`

**Expected Results:**
- ✅ Should see ALL questionnaires from their hospital
- ✅ Should see which doctor is reviewing which questionnaire
- ✅ Should see review status and progress
- ✅ Can view locked questionnaires (read-only)

**Database Check:**
```sql
-- Check what ADMIN_DOCTOR should see
SELECT 
    qa.id,
    qa.status,
    qa.reviewing_doctor_id,
    d.name as reviewing_doctor_name,
    u.name as patient_name,
    c.name as category_name
FROM questionnaire_answers qa
LEFT JOIN doctor d ON qa.reviewing_doctor_id = d.id
LEFT JOIN users u ON qa.user_id = u.id
LEFT JOIN category c ON qa.category_id = c.id
WHERE qa.hospital_id = 1  -- Replace with ADMIN_DOCTOR's hospital_id
AND qa.appointment_id IS NULL;
```

### 4.2 Test ADMIN_DOCTOR Cannot Edit Locked Questionnaires
1. As SUB_DOCTOR, lock a questionnaire
2. Login as ADMIN_DOCTOR (same hospital)
3. Try to update the status of the locked questionnaire

**Expected Results:**
- ✅ ADMIN_DOCTOR can view the questionnaire
- ❌ ADMIN_DOCTOR cannot edit/update status (should get error or disabled form)

## Step 5: Test Review Completion

### 5.1 Complete Review (SUB_DOCTOR)
1. As SUB_DOCTOR, open a questionnaire (it gets locked)
2. Review the answers
3. Update status to `approved` or `rejected`

**Expected Results:**
- ✅ Status changes to `approved` or `rejected`
- ✅ `reviewing_doctor_id` is set to `NULL` (unlocked)
- ✅ Questionnaire becomes available for other doctors again

**Database Check:**
```sql
-- After completing review
SELECT id, status, reviewing_doctor_id, hospital_id 
FROM questionnaire_answers 
WHERE id = [QUESTIONNAIRE_ANSWER_ID];
-- Should show: status = 'approved'/'rejected', reviewing_doctor_id = NULL
```

## Step 6: Test Hospital Scoping

### 6.1 Cross-Hospital Access Test
1. Create questionnaire submission for Hospital A
2. Login as doctor from Hospital B
3. Try to access the questionnaire

**Expected Results:**
- ❌ Doctor from Hospital B should NOT see questionnaires from Hospital A
- ❌ Should get 403 error if trying to access directly

**Database Check:**
```sql
-- Verify hospital isolation
SELECT 
    qa.id,
    qa.hospital_id,
    h.name as hospital_name,
    d.id as doctor_id,
    d.name as doctor_name,
    d.hospital_id as doctor_hospital_id
FROM questionnaire_answers qa
LEFT JOIN hospital h ON qa.hospital_id = h.id
LEFT JOIN doctor d ON qa.reviewing_doctor_id = d.id
WHERE qa.appointment_id IS NULL;
```

## Step 7: Test Edge Cases

### 7.1 Doctor Without Hospital
```sql
-- Set a doctor's hospital_id to NULL
UPDATE doctor SET hospital_id = NULL WHERE id = [DOCTOR_ID];

-- Try to access questionnaires
-- Expected: Should get error "Doctor is not assigned to a hospital"
```

### 7.2 Questionnaire Without Hospital
```sql
-- Check questionnaires without hospital_id
SELECT * FROM questionnaire_answers 
WHERE hospital_id IS NULL AND appointment_id IS NULL;

-- When doctor locks it, hospital_id should be set automatically
```

### 7.3 Multiple Submissions Same Category
- Submit multiple questionnaires for the same category
- Verify each gets its own hospital_id
- Verify doctors can see all of them (based on role)

## Step 8: Manual Testing Checklist

### Database Verification Queries

```sql
-- 1. Check all doctors and their hospitals/roles
SELECT id, name, hospital_id, doctor_role, 
       (SELECT name FROM hospital WHERE id = doctor.hospital_id) as hospital_name
FROM doctor
WHERE hospital_id IS NOT NULL;

-- 2. Check questionnaire submissions with hospital info
SELECT 
    qa.id,
    qa.user_id,
    qa.category_id,
    qa.status,
    qa.reviewing_doctor_id,
    qa.hospital_id,
    u.name as patient_name,
    c.name as category_name,
    h.name as hospital_name,
    d.name as reviewing_doctor_name
FROM questionnaire_answers qa
LEFT JOIN users u ON qa.user_id = u.id
LEFT JOIN category c ON qa.category_id = c.id
LEFT JOIN hospital h ON qa.hospital_id = h.id
LEFT JOIN doctor d ON qa.reviewing_doctor_id = d.id
WHERE qa.appointment_id IS NULL
ORDER BY qa.submitted_at DESC;

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

-- 4. Check unlocked questionnaires (available for review)
SELECT 
    qa.id,
    qa.status,
    qa.hospital_id,
    h.name as hospital_name
FROM questionnaire_answers qa
LEFT JOIN hospital h ON qa.hospital_id = h.id
WHERE qa.status = 'pending'
AND qa.reviewing_doctor_id IS NULL
AND qa.appointment_id IS NULL;
```

## Step 9: Browser Testing

### 9.1 Test as SUB_DOCTOR
1. Login: `/login` (as doctor user)
2. Navigate: `/doctor/questionnaires`
3. **Verify:**
   - Only see questionnaires from your hospital
   - Only see unlocked or assigned to you
   - Can click to open and lock
   - Cannot access locked by others

### 9.2 Test as ADMIN_DOCTOR
1. Login: `/login` (as admin doctor user)
2. Navigate: `/doctor/questionnaires`
3. **Verify:**
   - See all questionnaires from your hospital
   - See which doctor is reviewing each
   - Can view locked questionnaires (read-only)
   - Cannot edit locked questionnaires

### 9.3 Test Review Flow
1. Open questionnaire → Should lock
2. Review answers → Should show all sections
3. Update status → Should unlock when completed
4. Verify unlock → Other doctors can now see it

## Step 10: API/Route Testing (if applicable)

```bash
# Test routes (if using API)
# List questionnaires (should be hospital-scoped)
GET /doctor/questionnaires

# View submission (should lock if SUB_DOCTOR)
GET /doctor/questionnaire/{userId}/{categoryId}/{questionnaireId}

# Update status (should unlock if approved/rejected)
POST /doctor/questionnaire/{userId}/{categoryId}/{questionnaireId}/status
```

## Common Issues & Solutions

### Issue 1: Migration Fails
**Error:** "Column 'hospital_id' cannot be null"
**Solution:** Update existing doctors to have hospital_id:
```sql
UPDATE doctor SET hospital_id = 1 WHERE hospital_id IS NULL;
```

### Issue 2: No Questionnaires Visible
**Check:**
1. Doctor has `hospital_id` set?
2. Questionnaires have `hospital_id` set?
3. Doctor's role is correct?
4. Questionnaire status is correct?

### Issue 3: Cannot Lock Questionnaire
**Check:**
1. Doctor is SUB_DOCTOR?
2. Questionnaire is not already locked?
3. Questionnaire is from same hospital?

### Issue 4: Hospital ID Not Set on Submission
**Check:**
1. Are there doctors assigned to the category?
2. Do those doctors have hospital_id?
3. Check QuestionnaireService logic

## Quick Test Script

Run this SQL to set up a quick test scenario:

```sql
-- 1. Create test hospitals (if needed)
INSERT INTO hospital (name, phone, address, lat, lng, facility, status, created_at, updated_at)
VALUES 
('Test Hospital A', '1234567890', '123 Main St', '40.7128', '-74.0060', 'General', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE name=name;

-- 2. Assign first 3 doctors to hospital 1 with different roles
UPDATE doctor SET hospital_id = 1, doctor_role = 'ADMIN_DOCTOR' WHERE id = 1 LIMIT 1;
UPDATE doctor SET hospital_id = 1, doctor_role = 'SUB_DOCTOR' WHERE id = 2 LIMIT 1;
UPDATE doctor SET hospital_id = 1, doctor_role = 'SUB_DOCTOR' WHERE id = 3 LIMIT 1;

-- 3. Verify setup
SELECT id, name, hospital_id, doctor_role FROM doctor WHERE id IN (1,2,3);
```

## Success Criteria

✅ **All tests pass if:**
1. SUB_DOCTOR only sees assigned/unlocked questionnaires from their hospital
2. ADMIN_DOCTOR sees all questionnaires from their hospital
3. Questionnaire locks when SUB_DOCTOR opens it
4. Other SUB_DOCTORs cannot access locked questionnaires
5. ADMIN_DOCTOR can view but not edit locked questionnaires
6. Questionnaire unlocks when review is completed
7. Hospital scoping prevents cross-hospital access
8. All questionnaire queries filter by hospital_id
