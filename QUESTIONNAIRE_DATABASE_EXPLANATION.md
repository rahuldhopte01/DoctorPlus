# Questionnaire Answers Database Storage - Explanation

## Current Architecture

The questionnaire system uses a **two-step process**:

1. **Questionnaire Submission** → Stores answers in **session**
2. **Appointment Creation** → Saves answers to **database**

## Why This Design?

Questionnaire answers are linked to **appointments** in the database schema:
- `questionnaire_answers.appointment_id` is a required foreign key
- Answers cannot exist without an appointment
- This ensures answers are only saved when a booking is actually made

## Flow Diagram

```
User fills questionnaire
        ↓
Submit questionnaire
        ↓
Answers stored in session (key: questionnaire_submitted_{categoryId})
        ↓
User books appointment
        ↓
Answers retrieved from session
        ↓
Answers saved to database (linked to appointment_id)
```

## Where Answers Are Stored

### After Questionnaire Submission:
- **Location**: Session storage
- **Session Key**: `questionnaire_submitted_{categoryId}`
- **Database**: ❌ NOT saved yet

### After Appointment Creation:
- **Location**: Database table `questionnaire_answers`
- **Linked to**: `appointment_id`
- **Database**: ✅ Saved

## Session Data Structure

```php
session()->put('questionnaire_submitted_' . $categoryId, [
    'questionnaire_id' => 1,
    'category_id' => 1,
    'treatment_id' => 1,
    'answers' => [
        6 => 'A+',        // question_id => answer
        7 => 'Male',
        9 => '25',
    ],
    'files' => [
        10 => 'questionnaire_uploads/temp/1/1/file.pdf'
    ],
    'flags' => [],
    'version' => 1,
    'submitted_at' => '2024-01-01 12:00:00',
    'user_id' => 1,
]);
```

## Database Schema

The `questionnaire_answers` table requires:
- `appointment_id` (required - foreign key)
- `question_id` (required - foreign key)
- `answer_value` (text/JSON)
- `file_path` (optional)

## Checking Submitted Answers

To verify answers are in session (before appointment):

```php
// In controller or tinker
$categoryId = 1;
$sessionData = session()->get('questionnaire_submitted_' . $categoryId);
dd($sessionData);
```

## When Answers Are Saved to Database

Answers are saved when:
1. User submits questionnaire ✅ (stored in session)
2. User books an appointment with a doctor in that category ✅ (saved to database)

The `bookAppointment()` method in `WebsiteController` retrieves the session data and calls `QuestionnaireService::saveAnswers()` to persist to database.

## If You Want Immediate Database Storage

If you want to save answers immediately (without appointment), you would need to:

1. Create a different table structure (not linked to appointments)
2. OR create a "pending" appointment record
3. OR change the schema to allow null `appointment_id` (not recommended)

**Current design is correct** - answers should only exist when there's an actual appointment booking.

## Troubleshooting

### Issue: "No entries in database after submission"
**Expected Behavior**: Answers are in session, not database yet
**Solution**: This is correct. Answers will be saved when an appointment is created.

### Issue: "Answers not saved when booking appointment"
**Check**:
1. Session data exists: `session()->get('questionnaire_submitted_' . $categoryId)`
2. Doctor's category_id matches the questionnaire category_id
3. `bookAppointment()` method is retrieving session data correctly

### Issue: "Session data cleared before booking"
**Solution**: Session data persists until appointment is created or session expires

## Code Locations

- **Submission**: `app/Http/Controllers/Website/QuestionnaireController.php::submitQuestionnaire()`
- **Database Save**: `app/Services/QuestionnaireService.php::saveAnswers()`
- **Appointment Creation**: `app/Http/Controllers/Website/WebsiteController.php::bookAppointment()`
