# Questionnaire Database Storage with Status - Implementation

## Changes Made

The questionnaire system has been updated to save answers immediately to the database with status tracking, allowing doctors to review submissions before appointments are created.

## Database Schema Changes

### Migration: `2026_01_09_130559_modify_questionnaire_answers_table_add_status_and_user_fields.php`

**Changes:**
- `appointment_id` → Made **nullable** (answers can exist without appointment)
- Added `user_id` (required - links to user who submitted)
- Added `category_id` (required - links to category)
- Added `questionnaire_id` (required - links to questionnaire)
- Added `status` enum: `pending`, `under_review`, `approved`, `rejected` (default: `pending`)
- Added `submitted_at` timestamp
- Added indexes for efficient querying

**Status Values:**
- `pending` - Just submitted, waiting for doctor review
- `under_review` - Doctor is reviewing
- `approved` - Doctor approved, ready for payment/appointment
- `rejected` - Doctor rejected

## Flow After Changes

```
1. User submits questionnaire
        ↓
2. Answers saved to database immediately (status: 'pending')
        ↓
3. Doctor reviews in separate page
        ↓
4. Doctor updates status (approved/rejected)
        ↓
5. User pays (if approved)
        ↓
6. Appointment created
        ↓
7. Answers linked to appointment_id (status remains)
```

## Model Updates

### QuestionnaireAnswer Model

**New Fields:**
- `user_id`, `category_id`, `questionnaire_id`, `status`, `submitted_at`

**New Relationships:**
- `user()` - BelongsTo User
- `category()` - BelongsTo Category  
- `questionnaire()` - BelongsTo Questionnaire

**New Scopes:**
- `scopeByStatus($query, $status)` - Filter by status
- `scopeForUserAndCategory($query, $userId, $categoryId)` - Filter by user and category

## Service Updates

### QuestionnaireService

**New Method:**
- `saveAnswersImmediate($userId, $categoryId, Questionnaire $questionnaire, array $answers, array $files = [], $status = 'pending')`
  - Saves answers to database immediately
  - Sets status to 'pending' by default
  - Stores files in user-specific folder
  - No appointment_id required

**Updated Method:**
- `saveAnswers()` - Still used when appointment is created, now also sets user_id, category_id, questionnaire_id, status='approved'

## Controller Updates

### Website\QuestionnaireController

**submitQuestionnaire() method:**
- Now saves answers immediately to database using `saveAnswersImmediate()`
- Deletes any existing pending answers for same user/category/questionnaire
- Moves files to permanent location
- Still stores in session for backward compatibility

### Doctor\QuestionnaireReviewController

**New Methods:**
1. `index()` - Lists all pending/under_review submissions for doctor's category
2. `showSubmission($userId, $categoryId, $questionnaireId)` - Shows submission details
3. `updateStatus()` - Updates status of submission
4. `groupAnswersBySection()` - Helper to group answers by section

## Routes Added

```php
// Doctor questionnaire review routes
GET  /doctor/questionnaires                                    - List all submissions
GET  /doctor/questionnaire/{userId}/{categoryId}/{questionnaireId}  - View submission
POST /doctor/questionnaire/{userId}/{categoryId}/{questionnaireId}/status  - Update status
```

## Views Created

1. **`resources/views/doctor/questionnaire/index.blade.php`**
   - Lists all pending/under_review submissions
   - Shows patient, category, questionnaire, status, flagged count
   - Link to review each submission

2. **`resources/views/doctor/questionnaire/review_submission.blade.php`**
   - Shows submission details
   - Patient information
   - Status update form
   - Answers grouped by section
   - Flagged answers highlighted

## File Storage

Files are now stored in:
- `public/questionnaire_uploads/user/{userId}/{categoryId}/filename`

When appointment is created, files can be moved to:
- `public/questionnaire_uploads/{appointmentId}/filename`

## How to Use

### For Patients:
1. Fill questionnaire
2. Submit → Answers saved to database with status 'pending'
3. Wait for doctor review
4. After doctor approves → Pay
5. Book appointment

### For Doctors:
1. Go to `/doctor/questionnaires` 
2. See list of pending submissions
3. Click "Review" to view details
4. Update status (pending → under_review → approved/rejected)
5. Answers remain in database for future reference

## Database Queries

### Get pending submissions for a category:
```php
QuestionnaireAnswer::where('category_id', $categoryId)
    ->whereNull('appointment_id')
    ->where('status', 'pending')
    ->with(['user', 'questionnaire'])
    ->get()
    ->groupBy(['user_id', 'category_id', 'questionnaire_id', 'submitted_at']);
```

### Get answers for a specific submission:
```php
QuestionnaireAnswer::where('user_id', $userId)
    ->where('category_id', $categoryId)
    ->where('questionnaire_id', $questionnaireId)
    ->whereNull('appointment_id')
    ->with(['question.section'])
    ->get();
```

## Migration Instructions

Run the migration:
```bash
php artisan migrate
```

This will:
1. Make `appointment_id` nullable
2. Add new fields (user_id, category_id, questionnaire_id, status, submitted_at)
3. Add foreign keys and indexes

## Backward Compatibility

- Existing `saveAnswers()` method still works (for appointments)
- Session storage still works (for appointment booking)
- Answers linked to appointments still function correctly

## Next Steps

1. Run migration: `php artisan migrate`
2. Test questionnaire submission
3. Check database for answers with status='pending'
4. Access `/doctor/questionnaires` to review submissions
5. Test status updates
