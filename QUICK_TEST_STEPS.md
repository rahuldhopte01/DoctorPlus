# Quick Testing Steps

## Step 1: Run Migrations

```bash
php artisan migrate
```

**Expected Output:**
- ✅ Migration `2026_01_22_045444_fix_doctor_hospital_id_and_add_role` runs successfully
- ✅ Migration `2026_01_22_045501_add_reviewing_fields_to_questionnaire_answers` runs successfully

**If you get errors:**
- Check if `hospital_id` in doctor table has invalid data (non-numeric values)
- Check if there are existing foreign key constraints that need to be dropped first

## Step 2: Verify Database Structure

Run these SQL queries in your database:

```sql
-- Check doctor table structure
DESCRIBE doctor;
-- Should show: hospital_id as BIGINT, doctor_role as ENUM

-- Check questionnaire_answers table structure  
DESCRIBE questionnaire_answers;
-- Should show: reviewing_doctor_id and hospital_id columns
```

## Step 3: Set Up Test Data

### Option A: Use SQL Script
Run the `quick_test_setup.sql` file in your database.

### Option B: Manual Setup

**3.1 Assign Doctors to Hospitals:**
```sql
-- Get hospital IDs
SELECT id, name FROM hospital;

-- Update doctors (replace IDs with your actual doctor IDs)
UPDATE doctor SET hospital_id = 1, doctor_role = 'ADMIN_DOCTOR' WHERE id = 1;
UPDATE doctor SET hospital_id = 1, doctor_role = 'SUB_DOCTOR' WHERE id = 2;
UPDATE doctor SET hospital_id = 1, doctor_role = 'SUB_DOCTOR' WHERE id = 3;
```

**3.2 Verify Setup:**
```sql
SELECT id, name, hospital_id, doctor_role, 
       (SELECT name FROM hospital WHERE id = doctor.hospital_id) as hospital_name
FROM doctor 
WHERE hospital_id IS NOT NULL;
```

## Step 4: Test in Browser

### 4.1 Test as SUB_DOCTOR

1. **Login as SUB_DOCTOR:**
   - Go to `/login`
   - Login with a doctor account that has `doctor_role = 'SUB_DOCTOR'`

2. **Navigate to Questionnaire List:**
   - Go to `/doctor/questionnaires`
   - **Expected:** Should only see questionnaires from their hospital that are:
     - Unlocked (`status = 'pending'`, `reviewing_doctor_id IS NULL`), OR
     - Assigned to them (`reviewing_doctor_id = their doctor_id`)

3. **Test Locking:**
   - Click on an unlocked questionnaire
   - **Expected:** 
     - Page loads successfully
     - Status changes to `IN_REVIEW`
     - `reviewing_doctor_id` is set to your doctor ID

4. **Verify Lock:**
   - Go back to list
   - **Expected:** The questionnaire should still be visible (because it's assigned to you)
   - Check database:
     ```sql
     SELECT id, status, reviewing_doctor_id, hospital_id 
     FROM questionnaire_answers 
     WHERE reviewing_doctor_id = [YOUR_DOCTOR_ID];
     ```

### 4.2 Test as Another SUB_DOCTOR (Same Hospital)

1. **Login as Different SUB_DOCTOR:**
   - Login with another doctor from the same hospital

2. **Check Visibility:**
   - Go to `/doctor/questionnaires`
   - **Expected:** Should NOT see the questionnaire locked by the first doctor

3. **Try to Access Directly:**
   - Try to access the locked questionnaire URL directly
   - **Expected:** Should get error message or redirect

### 4.3 Test as ADMIN_DOCTOR

1. **Login as ADMIN_DOCTOR:**
   - Login with a doctor that has `doctor_role = 'ADMIN_DOCTOR'`

2. **Check Visibility:**
   - Go to `/doctor/questionnaires`
   - **Expected:** Should see ALL questionnaires from their hospital, including locked ones

3. **View Locked Questionnaire:**
   - Click on a questionnaire locked by a SUB_DOCTOR
   - **Expected:** 
     - Can view the questionnaire
     - Can see which doctor is reviewing it
     - Cannot edit/update status (form should be disabled or show error)

### 4.4 Test Review Completion

1. **As SUB_DOCTOR who locked it:**
   - Open the locked questionnaire
   - Update status to `approved` or `rejected`
   - **Expected:**
     - Status updates successfully
     - `reviewing_doctor_id` becomes `NULL` (unlocked)

2. **Verify Unlock:**
   ```sql
   SELECT id, status, reviewing_doctor_id 
   FROM questionnaire_answers 
   WHERE id = [QUESTIONNAIRE_ID];
   -- Should show: reviewing_doctor_id = NULL
   ```

3. **Check Other Doctors Can See It:**
   - Login as another SUB_DOCTOR
   - **Expected:** Should now see the unlocked questionnaire

## Step 5: Database Verification Queries

Run these to verify everything is working:

```sql
-- 1. Check all doctors with their hospitals and roles
SELECT 
    d.id,
    d.name,
    d.hospital_id,
    d.doctor_role,
    h.name as hospital_name
FROM doctor d
LEFT JOIN hospital h ON d.hospital_id = h.id
WHERE d.hospital_id IS NOT NULL;

-- 2. Check questionnaire submissions with hospital info
SELECT 
    qa.id,
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

-- 4. Check unlocked questionnaires (available for review)
SELECT 
    qa.id,
    qa.status,
    qa.hospital_id,
    h.name as hospital_name,
    COUNT(*) as answer_count
FROM questionnaire_answers qa
LEFT JOIN hospital h ON qa.hospital_id = h.id
WHERE qa.status = 'pending'
AND qa.reviewing_doctor_id IS NULL
AND qa.appointment_id IS NULL
GROUP BY qa.user_id, qa.category_id, qa.questionnaire_id, qa.submitted_at;
```

## Step 6: Test Hospital Scoping

1. **Create Test Scenario:**
   - Ensure you have questionnaires from different hospitals
   - Login as doctor from Hospital A

2. **Verify Isolation:**
   - Should only see questionnaires from Hospital A
   - Should NOT see questionnaires from Hospital B

3. **Database Check:**
   ```sql
   -- Check hospital distribution
   SELECT 
       h.id,
       h.name,
       COUNT(DISTINCT qa.id) as questionnaire_count
   FROM hospital h
   LEFT JOIN questionnaire_answers qa ON h.id = qa.hospital_id
   WHERE qa.appointment_id IS NULL
   GROUP BY h.id, h.name;
   ```

## Common Issues & Quick Fixes

### Issue: "Doctor is not assigned to a hospital"
**Fix:**
```sql
UPDATE doctor SET hospital_id = 1 WHERE id = [DOCTOR_ID];
```

### Issue: "Questionnaire submission not found"
**Check:**
- Does the questionnaire have `hospital_id` set?
- Is the doctor from the same hospital?
- Run: `SELECT * FROM questionnaire_answers WHERE id = [ID];`

### Issue: Migration fails on hospital_id conversion
**Fix:**
```sql
-- Clean up invalid hospital_id values first
UPDATE doctor SET hospital_id = NULL WHERE hospital_id NOT REGEXP '^[0-9]+$';
-- Then run migration again
```

### Issue: No questionnaires visible
**Check:**
1. Doctor has `hospital_id`? → `SELECT hospital_id FROM doctor WHERE id = [ID];`
2. Questionnaires have `hospital_id`? → `SELECT hospital_id FROM questionnaire_answers WHERE appointment_id IS NULL;`
3. Doctor's role is correct? → `SELECT doctor_role FROM doctor WHERE id = [ID];`

## Success Indicators

✅ **Everything works if:**
- Migrations run without errors
- Doctors can be assigned to hospitals
- SUB_DOCTORs only see their assigned/unlocked questionnaires
- ADMIN_DOCTORs see all questionnaires from their hospital
- Questionnaires lock when SUB_DOCTOR opens them
- Locked questionnaires are not accessible by other SUB_DOCTORs
- Questionnaires unlock when review is completed
- Hospital scoping prevents cross-hospital access

## Need Help?

If something doesn't work:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check database directly with SQL queries above
3. Verify migrations ran: `php artisan migrate:status`
4. Check browser console for JavaScript errors
5. Check network tab for API errors
