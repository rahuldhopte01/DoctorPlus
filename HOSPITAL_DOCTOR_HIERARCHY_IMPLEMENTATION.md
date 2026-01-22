# Hospital-Doctor Hierarchy & Questionnaire Review Flow - Implementation Summary

## ✅ Completed Implementation

### 1. Database Changes

#### Migration: `2026_01_22_045444_fix_doctor_hospital_id_and_add_role.php`
- ✅ Fixed `hospital_id` in `doctor` table from `varchar(255)` to `unsignedBigInteger`
- ✅ Added proper foreign key constraint to `hospital` table
- ✅ Handles existing comma-separated hospital_id values (takes first one)
- ✅ Added `doctor_role` enum field: `ADMIN_DOCTOR`, `SUB_DOCTOR` (default: `SUB_DOCTOR`)
- ✅ Added index on `doctor_role`

#### Migration: `2026_01_22_045501_add_reviewing_fields_to_questionnaire_answers.php`
- ✅ Added `reviewing_doctor_id` field (foreign key to `doctor` table)
- ✅ Added `hospital_id` field (foreign key to `hospital` table)
- ✅ Updated status enum to include `IN_REVIEW` and `REVIEW_COMPLETED`
- ✅ Added indexes for efficient querying:
  - `qa_hospital_status_idx` - (hospital_id, status)
  - `qa_reviewing_doctor_status_idx` - (reviewing_doctor_id, status)
  - `qa_hospital_doctor_idx` - (hospital_id, reviewing_doctor_id)

### 2. Model Updates

#### Doctor Model (`app/Models/Doctor.php`)
- ✅ Added `doctor_role` to `$fillable`
- ✅ Added `hospital()` relationship (belongsTo)
- ✅ Added `reviewingQuestionnaireAnswers()` relationship
- ✅ Added helper methods:
  - `isAdminDoctor()` - Check if doctor is admin
  - `isSubDoctor()` - Check if doctor is sub-doctor
- ✅ Added scopes:
  - `scopeAdminDoctors()` - Filter admin doctors
  - `scopeSubDoctors()` - Filter sub doctors
  - `scopeByHospital()` - Filter by hospital

#### QuestionnaireAnswer Model (`app/Models/QuestionnaireAnswer.php`)
- ✅ Added `reviewing_doctor_id` and `hospital_id` to `$fillable`
- ✅ Added `reviewingDoctor()` relationship (belongsTo)
- ✅ Added `hospital()` relationship (belongsTo)
- ✅ Added scopes:
  - `scopeByHospital()` - Filter by hospital
  - `scopeBeingReviewedBy()` - Filter by reviewing doctor
  - `scopeLocked()` - Get locked answers
  - `scopeUnlocked()` - Get unlocked answers
- ✅ Added helper methods:
  - `lockForReview($doctorId)` - Lock answer to doctor
  - `unlockFromReview()` - Unlock answer
  - `isLocked()` - Check if locked
  - `isLockedBy($doctorId)` - Check if locked by specific doctor

#### Hospital Model (`app/Models/Hospital.php`)
- ✅ Added `doctors()` relationship (alias for `doctor()`)
- ✅ Added `questionnaireAnswers()` relationship

### 3. Service Updates

#### QuestionnaireService (`app/Services/QuestionnaireService.php`)
- ✅ Updated `saveAnswersImmediate()` to automatically set `hospital_id`
- ✅ Logic: Finds first doctor handling the category with a hospital and uses that hospital_id
- ✅ If no doctor found, hospital_id remains null (will be set when doctor locks it)

### 4. Controller Updates

#### QuestionnaireReviewController (`app/Http/Controllers/Doctor/QuestionnaireReviewController.php`)

##### `index()` - List Submissions
- ✅ **Hospital-scoped**: Only shows questionnaires from doctor's hospital
- ✅ **Role-based visibility**:
  - **SUB_DOCTOR**: Only sees questionnaires assigned to them OR unlocked (pending)
  - **ADMIN_DOCTOR**: Sees all questionnaires in their hospital
- ✅ Shows lock status, reviewing doctor info, and edit permissions

##### `showSubmission()` - View/Review Submission
- ✅ **Hospital-scoped**: Only allows access to questionnaires from doctor's hospital
- ✅ **Locking mechanism**: 
  - When SUB_DOCTOR opens an unlocked questionnaire, it's automatically locked
  - Status changes to `IN_REVIEW`
  - `reviewing_doctor_id` is set
  - `hospital_id` is ensured to be set
- ✅ **Access control**:
  - SUB_DOCTOR cannot access questionnaires locked by others
  - ADMIN_DOCTOR can view locked questionnaires but cannot edit
- ✅ Returns `canEdit` flag for view logic

##### `updateStatus()` - Update Review Status
- ✅ **Hospital-scoped**: Only allows updates to questionnaires from doctor's hospital
- ✅ **Permission checks**:
  - SUB_DOCTOR can only update questionnaires they locked
  - ADMIN_DOCTOR cannot edit questionnaires locked by others
- ✅ **Unlocking logic**: When status is set to `approved`, `rejected`, or `REVIEW_COMPLETED`, the questionnaire is unlocked (`reviewing_doctor_id` set to null)

##### `createPrescription()` & `storePrescription()`
- ✅ Updated to be hospital-scoped
- ✅ Added hospital_id checks

## Business Logic Implementation

### Visibility Rules

1. **SUB_DOCTOR**:
   - Can see: Questionnaires assigned to them (`reviewing_doctor_id = their id`) OR unlocked questionnaires (`status = pending`, `reviewing_doctor_id = null`)
   - Cannot see: Questionnaires locked by other doctors
   - Can edit: Only questionnaires they locked

2. **ADMIN_DOCTOR**:
   - Can see: All questionnaires in their hospital
   - Can see: Which doctor is reviewing which questionnaire
   - Can see: Review status and progress (read-only)
   - Cannot edit: Questionnaires currently being reviewed by other doctors

### Locking Mechanism

1. **When doctor opens questionnaire**:
   - Status changes to `IN_REVIEW`
   - `reviewing_doctor_id` is set to the doctor's ID
   - `hospital_id` is ensured to be set

2. **When locked**:
   - Other SUB_DOCTORs cannot see or open it
   - ADMIN_DOCTOR can see it but not edit it

3. **When review completed**:
   - Status changes to `approved`, `rejected`, or `REVIEW_COMPLETED`
   - Lock is released (`reviewing_doctor_id` set to null)
   - Review data is saved with doctor ID and timestamp

### Hospital Scoping

- All questionnaire queries filter by `hospital_id`
- Doctors can only access questionnaires from their hospital
- When questionnaire is submitted, `hospital_id` is automatically determined from the first doctor handling that category
- If no hospital found initially, it's set when a doctor locks it

## Status Values

- `pending` - Just submitted, waiting for doctor review
- `under_review` - Legacy status (backward compatibility)
- `IN_REVIEW` - Doctor is currently reviewing (locked)
- `approved` - Doctor approved, ready for payment/appointment
- `rejected` - Doctor rejected
- `REVIEW_COMPLETED` - Review completed (explicit completion status)

## Next Steps (Optional Enhancements)

1. **Review History Table**: Track all review actions (who reviewed, when, what changed)
2. **Force Reassignment**: Allow admin to reassign locked questionnaires
3. **Notification System**: Notify doctors when questionnaires are assigned/locked
4. **Dashboard**: Show review statistics per hospital
5. **Bulk Actions**: Allow admin to bulk assign questionnaires to doctors

## Testing Checklist

- [ ] Doctor can be assigned to one hospital
- [ ] Doctor role (ADMIN_DOCTOR/SUB_DOCTOR) is set correctly
- [ ] SUB_DOCTOR only sees assigned/unlocked questionnaires
- [ ] ADMIN_DOCTOR sees all questionnaires in hospital
- [ ] Questionnaire locks when SUB_DOCTOR opens it
- [ ] Other SUB_DOCTORs cannot access locked questionnaires
- [ ] ADMIN_DOCTOR can view but not edit locked questionnaires
- [ ] Questionnaire unlocks when review is completed
- [ ] Hospital scoping works correctly
- [ ] Prescription creation respects hospital scoping
