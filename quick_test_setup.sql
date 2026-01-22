-- =====================================================
-- QUICK TEST SETUP SCRIPT
-- Run this after migrations to set up test data
-- =====================================================

-- 1. Check existing hospitals
SELECT '=== EXISTING HOSPITALS ===' as info;
SELECT id, name, status FROM hospital LIMIT 5;

-- 2. Check existing doctors
SELECT '=== EXISTING DOCTORS (BEFORE UPDATE) ===' as info;
SELECT id, name, hospital_id, doctor_role FROM doctor LIMIT 10;

-- 3. Update doctors to have hospital_id and role (if not set)
-- Get first hospital ID
SET @first_hospital_id = (SELECT id FROM hospital LIMIT 1);
SET @second_hospital_id = (SELECT id FROM hospital LIMIT 1 OFFSET 1);

-- If only one hospital exists, use it for all
SET @second_hospital_id = IFNULL(@second_hospital_id, @first_hospital_id);

-- Update first 3 doctors (adjust IDs as needed)
UPDATE doctor 
SET hospital_id = @first_hospital_id, 
    doctor_role = 'ADMIN_DOCTOR' 
WHERE id = (SELECT id FROM (SELECT id FROM doctor ORDER BY id LIMIT 1) as t)
LIMIT 1;

UPDATE doctor 
SET hospital_id = @first_hospital_id, 
    doctor_role = 'SUB_DOCTOR' 
WHERE id = (SELECT id FROM (SELECT id FROM doctor ORDER BY id LIMIT 1 OFFSET 1) as t)
LIMIT 1;

UPDATE doctor 
SET hospital_id = @first_hospital_id, 
    doctor_role = 'SUB_DOCTOR' 
WHERE id = (SELECT id FROM (SELECT id FROM doctor ORDER BY id LIMIT 1 OFFSET 2) as t)
LIMIT 1;

-- 4. Verify updates
SELECT '=== DOCTORS AFTER UPDATE ===' as info;
SELECT 
    d.id,
    d.name,
    d.hospital_id,
    d.doctor_role,
    h.name as hospital_name
FROM doctor d
LEFT JOIN hospital h ON d.hospital_id = h.id
WHERE d.hospital_id IS NOT NULL
LIMIT 10;

-- 5. Check questionnaire submissions
SELECT '=== QUESTIONNAIRE SUBMISSIONS ===' as info;
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
ORDER BY qa.submitted_at DESC
LIMIT 10;

-- 6. Check doctor-category relationships
SELECT '=== DOCTOR-CATEGORY RELATIONSHIPS ===' as info;
SELECT 
    d.id as doctor_id,
    d.name as doctor_name,
    d.hospital_id,
    d.doctor_role,
    dc.category_id,
    c.name as category_name
FROM doctor d
LEFT JOIN doctor_category dc ON d.id = dc.doctor_id
LEFT JOIN category c ON dc.category_id = c.id
WHERE d.hospital_id IS NOT NULL
LIMIT 20;
