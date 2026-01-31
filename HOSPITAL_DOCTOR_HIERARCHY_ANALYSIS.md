# Hospital-Doctor Hierarchy & Questionnaire Review Flow - Analysis

## Current State Analysis

### 1. Hospital Module
- **Table**: `hospital`
- **Fields**: id, name, phone, address, lat, lng, facility, status
- **Relationships**: 
  - `hasMany(Doctor)` - One hospital can have many doctors
- **Status**: ✅ Basic structure exists

### 2. Doctor Module
- **Table**: `doctor`
- **Current Fields**: 
  - `hospital_id` - **ISSUE**: Stored as `varchar(255)` instead of proper foreign key
  - No `doctor_role` field (ADMIN_DOCTOR/SUB_DOCTOR)
- **Relationships**:
  - `belongsToMany(Category)` via `doctor_category` table
  - `belongsToMany(Treatment)` via `doctor_treatment` table
  - `belongsTo(Hospital)` - relationship exists but hospital_id is varchar
- **Status**: ⚠️ Needs fixes

### 3. Questionnaire Module
- **Table**: `questionnaires`
- **Relationships**: `belongsTo(Category)`
- **Status**: ✅ Structure exists

### 4. QuestionnaireAnswer Module
- **Table**: `questionnaire_answers`
- **Current Status Values**: `pending`, `under_review`, `approved`, `rejected`
- **Missing Fields**:
  - `reviewing_doctor_id` - to track which doctor is reviewing
  - `hospital_id` - to scope questionnaires by hospital
- **Status**: ⚠️ Needs enhancements

### 5. Current Review Logic
- **Location**: `app/Http/Controllers/Doctor/QuestionnaireReviewController.php`
- **Current Filtering**: By `doctor->category_id`
- **Issue**: Not hospital-scoped, no role-based visibility, no locking mechanism
- **Status**: ⚠️ Needs complete rewrite

## Required Changes

### Database Changes

1. **Fix doctor.hospital_id**
   - Change from `varchar(255)` to `unsignedBigInteger`
   - Add proper foreign key constraint
   - Ensure one doctor = one hospital

2. **Add doctor_role to doctor table**
   - Type: `enum('ADMIN_DOCTOR', 'SUB_DOCTOR')`
   - Default: `SUB_DOCTOR`
   - Nullable: No

3. **Add fields to questionnaire_answers**
   - `reviewing_doctor_id` (unsignedBigInteger, nullable, foreign key to doctor)
   - `hospital_id` (unsignedBigInteger, nullable, foreign key to hospital)
   - Update status enum to include `IN_REVIEW` (or use `under_review` as `IN_REVIEW`)

4. **Add review tracking table (optional)**
   - `questionnaire_review_history` - track review actions
   - Fields: id, questionnaire_answer_group_id, doctor_id, action, notes, created_at

### Model Changes

1. **Doctor Model**
   - Add `hospital()` relationship (belongsTo)
   - Add `doctorRole` accessor/scope
   - Add `isAdminDoctor()` and `isSubDoctor()` helper methods

2. **QuestionnaireAnswer Model**
   - Add `reviewingDoctor()` relationship
   - Add `hospital()` relationship
   - Add scopes for hospital filtering
   - Add locking/unlocking methods

3. **Hospital Model**
   - Add `doctors()` relationship (already exists)
   - Add `questionnaireAnswers()` relationship

### Controller Changes

1. **QuestionnaireReviewController**
   - Rewrite `index()` to filter by hospital and role
   - Add locking mechanism in `showSubmission()`
   - Update visibility rules based on role
   - Add review completion logic

### Business Logic

#### Visibility Rules:
- **SUB_DOCTOR**: Only sees questionnaires assigned to them (reviewing_doctor_id = their id)
- **ADMIN_DOCTOR**: Sees all questionnaires in their hospital (hospital_id = their hospital_id)

#### Locking Mechanism:
- When doctor opens questionnaire: Set status to `IN_REVIEW`, set `reviewing_doctor_id`
- When locked: Other sub-doctors cannot see it, admin can see but not edit
- When completed: Set status to `REVIEW_COMPLETED`, clear `reviewing_doctor_id`

#### Hospital Scoping:
- All questionnaire queries must filter by `hospital_id`
- Doctors can only access questionnaires from their hospital
