# Questionnaire Database Storage with Status - Implementation Summary

## ✅ Changes Completed

### 1. Database Migration
**File**: `database/migrations/2026_01_09_130559_modify_questionnaire_answers_table_add_status_and_user_fields.php`

**Changes:**
- ✅ Made `appointment_id` nullable (using DB::statement)
- ✅ Added `user_id` field (foreign key to users)
- ✅ Added `category_id` field (foreign key to category)
- ✅ Added `questionnaire_id` field (foreign key to questionnaires)
- ✅ Added `status` enum: `pending`, `under_review`, `approved`, `rejected` (default: `pending`)
- ✅ Added `submitted_at` timestamp
- ✅ Added indexes for efficient querying
- ✅ Added foreign key constraints

### 2. Model Updates
**File**: `app/Models/QuestionnaireAnswer.php`

**Updates:**
- ✅ Added new fields to `$fillable`
- ✅ Added `submitted_at` to `$casts`
- ✅ Added relationships: `user()`, `category()`, `questionnaire()`
- ✅ Added scopes: `scopeByStatus()`, `scopeForUserAndCategory()`

### 3. Service Updates
**File**: `app/Services/QuestionnaireService.php`

**New Method:**
- ✅ `saveAnswersImmediate()` - Saves answers immediately without appointment

**Updated Method:**
- ✅ `saveAnswers()` - Now also sets user_id, category_id, questionnaire_id, status

### 4. Controller Updates

#### Website\QuestionnaireController
**File**: `app/Http/Controllers/Website/QuestionnaireController.php`

**Updates:**
- ✅ `submitQuestionnaire()` - Now saves answers to database immediately
- ✅ Deletes existing pending answers before saving new ones
- ✅ Moves files to permanent location
- ✅ Sets status to 'pending'

#### Doctor\QuestionnaireReviewController
**File**: `app/Http/Controllers/Doctor/QuestionnaireReviewController.php`

**New Methods:**
- ✅ `index()` - Lists all pending/under_review submissions
- ✅ `showSubmission()` - Shows submission details
- ✅ `updateStatus()` - Updates submission status
- ✅ `groupAnswersBySection()` - Groups answers by section

### 5. Routes Added
**File**: `routes/web.php`

**New Routes:**
- ✅ `GET /doctor/questionnaires` - List submissions
- ✅ `GET /doctor/questionnaire/{userId}/{categoryId}/{questionnaireId}` - View submission
- ✅ `POST /doctor/questionnaire/{userId}/{categoryId}/{questionnaireId}/status` - Update status

### 6. Views Created
**Files:**
- ✅ `resources/views/doctor/questionnaire/index.blade.php` - List all submissions
- ✅ `resources/views/doctor/questionnaire/review_submission.blade.php` - Review submission details

## 📋 How to Start

### Step 1: Run Migration
```bash
php artisan migrate
```

This will:
- Make `appointment_id` nullable
- Add new fields (user_id, category_id, questionnaire_id, status, submitted_at)
- Add foreign keys and indexes

### Step 2: Test Questionnaire Submission
1. Go to a category with a questionnaire
2. Fill out the questionnaire
3. Submit
4. ✅ Answers should be saved to database immediately with status='pending'

### Step 3: Check Database
```sql
SELECT * FROM questionnaire_answers 
WHERE appointment_id IS NULL 
AND status = 'pending'
ORDER BY submitted_at DESC;
```

You should see entries with:
- `user_id` set
- `category_id` set
- `questionnaire_id` set
- `status` = 'pending'
- `appointment_id` = NULL

### Step 4: Doctor Review
1. Login as doctor
2. Go to `/doctor/questionnaires`
3. See list of pending submissions
4. Click "Review" on any submission
5. View all answers grouped by section
6. Update status (pending → under_review → approved/rejected)

## 🔄 New Flow

```
1. User submits questionnaire
   ↓
2. Answers saved to database (status: 'pending', appointment_id: NULL)
   ↓
3. Doctor reviews at /doctor/questionnaires
   ↓
4. Doctor updates status (approved/rejected)
   ↓
5. User pays (if approved)
   ↓
6. Appointment created
   ↓
7. Answers linked to appointment (appointment_id set, status remains)
```

## 📊 Status Values

- **pending** - Just submitted, waiting for review
- **under_review** - Doctor is currently reviewing
- **approved** - Doctor approved, ready for payment/appointment
- **rejected** - Doctor rejected

## 🎯 Key Features

1. ✅ Answers saved immediately (not just in session)
2. ✅ Status tracking (pending, under_review, approved, rejected)
3. ✅ Doctor can review submissions before appointments
4. ✅ Separate review page for submissions without appointments
5. ✅ Status update functionality
6. ✅ Files stored in user-specific folders
7. ✅ Backward compatible (answers with appointments still work)

## 🔍 Database Queries

### Get pending submissions:
```php
QuestionnaireAnswer::where('category_id', $categoryId)
    ->whereNull('appointment_id')
    ->where('status', 'pending')
    ->with(['user', 'questionnaire'])
    ->get();
```

### Update status:
```php
QuestionnaireAnswer::where('user_id', $userId)
    ->where('category_id', $categoryId)
    ->where('questionnaire_id', $questionnaireId)
    ->whereNull('appointment_id')
    ->update(['status' => 'approved']);
```

## 📝 Notes

- Files are stored in `questionnaire_uploads/user/{userId}/{categoryId}/`
- When appointment is created, files can be moved to `questionnaire_uploads/{appointmentId}/`
- Answers remain in database even after appointment is created
- Status field tracks the review state
- Doctors can review submissions even days after submission

## ⚠️ Important

After running migration, existing answers with appointments will continue to work normally. Only new submissions will have the new fields populated.
