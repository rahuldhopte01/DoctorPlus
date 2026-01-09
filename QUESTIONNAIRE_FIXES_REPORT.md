# Questionnaire Flow Fixes - Implementation Report

## Summary

All three critical issues have been fixed with proper architectural implementation.

---

## ✅ ISSUE 1: POST METHOD NOT SUPPORTED - FIXED

### Problem
Form was submitting POST requests to GET-only route `/questionnaire/category/{categoryId}`

### Solution
1. **Form Action Fixed**: Added `action="#"` to form to prevent default submission
2. **Correct Endpoints Used**:
   - `POST /questionnaire/category/{categoryId}/save-section` - For saving section answers
   - `POST /questionnaire/category/{categoryId}/submit` - For final submission
3. **JavaScript Handlers**: All form submissions now go through JavaScript handlers that POST to correct endpoints

### Files Modified
- `resources/views/website/questionnaire/category_section.blade.php` (NEW)
- `app/Http/Controllers/Website/QuestionnaireController.php`

### Endpoints Used
- ✅ `POST /questionnaire/category/{categoryId}/save-section` - Saves section answers and navigates
- ✅ `POST /questionnaire/category/{categoryId}/submit` - Final questionnaire submission
- ✅ `GET /questionnaire/category/{categoryId}/section/{sectionIndex}` - Views section (GET only)

---

## ✅ ISSUE 2: SECTION-WISE NAVIGATION - IMPLEMENTED

### Problem
All sections were displayed on a single long page

### Solution
**Implemented step-by-step navigation where each section is a separate page/step**

### Implementation Details

#### 1. New Routes Created
```php
Route::get('/questionnaire/category/{categoryId}/section/{sectionIndex}', [WebQuestionnaireController::class, 'showSection'])->name('questionnaire.section');
Route::post('/questionnaire/category/{categoryId}/save-section', [WebQuestionnaireController::class, 'saveSectionAnswers'])->name('questionnaire.save-section');
```

#### 2. Controller Methods

**`showSection($categoryId, $sectionIndex)`**:
- Shows one section at a time
- Validates section index
- Loads saved answers from session
- Displays progress indicator (Section X of Y)

**`saveSectionAnswers($categoryId)`**:
- Saves answers for current section
- Validates required fields before allowing "Next"
- Allows "Previous" without validation
- Merges with existing answers from other sections
- Returns next/previous section index for navigation

#### 3. Navigation Features
- ✅ **Next Button**: Validates required fields, saves, navigates to next section
- ✅ **Previous Button**: Saves current answers, navigates back without validation
- ✅ **Progress Indicator**: Shows "Section X of Y" and percentage
- ✅ **Step Indicator**: Visual progress bar
- ✅ **Section Persistence**: Answers persist when navigating back/forth
- ✅ **Final Section**: Shows "Submit Questionnaire" button instead of "Next"

#### 4. View Created
- `resources/views/website/questionnaire/category_section.blade.php` - Single section view with navigation

### Files Created/Modified
- ✅ `app/Http/Controllers/Website/QuestionnaireController.php` - Added `showSection()` and `saveSectionAnswers()`
- ✅ `routes/web.php` - Added section routes
- ✅ `resources/views/website/questionnaire/category_section.blade.php` - NEW: Section view

---

## ✅ ISSUE 3: FILE UPLOAD (BIRTH CERTIFICATE) - IMPLEMENTED

### Problem
File upload functionality needed for birth certificate and other file fields

### Solution

#### 1. Backend File Upload Handling

**File Storage**:
- Files stored in: `public/questionnaire_uploads/temp/{userId}/{categoryId}/`
- Files are stored temporarily before appointment creation
- File paths stored in session with answers

**File Validation**:
- ✅ Allowed types: PDF, JPG, JPEG, PNG
- ✅ Max size: 5MB (5242880 bytes)
- ✅ Client-side and server-side validation
- ✅ Secure filename sanitization

#### 2. Frontend Implementation

**File Input**:
- File picker with accept attribute: `.pdf,.jpg,.jpeg,.png`
- Client-side size validation (5MB limit)
- Shows uploaded file name after selection
- Displays "File uploaded" indicator if file already saved

**File Upload Flow**:
1. User selects file
2. Client validates file type and size
3. File uploaded when section is saved (via `saveSectionAnswers`)
4. File stored temporarily with user_id + category_id
5. File path stored in session
6. On final submit, files remain in session for appointment creation

#### 3. Backend Methods Updated

**`saveAnswers()`**:
- Handles file uploads
- Validates file type and size
- Stores files in temp directory
- Stores file paths in session

**`saveSectionAnswers()`**:
- Handles file uploads per section
- Validates file type and size
- Merges file paths with existing files

**`submitQuestionnaire()`**:
- Collects all files from session
- Stores file paths in final submission data

### Files Modified
- ✅ `app/Http/Controllers/Website/QuestionnaireController.php` - File upload handling in all save methods
- ✅ `resources/views/website/questionnaire/category_section.blade.php` - File input field with validation

### File Upload Flow
```
User selects file → Client validation → POST to save-section → 
Backend validates → File stored in temp directory → 
File path stored in session → On submit, files ready for appointment creation
```

---

## 🔧 TECHNICAL IMPLEMENTATION DETAILS

### Routes Added
```php
// Section-wise navigation
GET  /questionnaire/category/{categoryId}/section/{sectionIndex}
POST /questionnaire/category/{categoryId}/save-section

// Existing routes (still used)
GET  /questionnaire/category/{categoryId} → redirects to section 0
POST /questionnaire/category/{categoryId}/save
POST /questionnaire/category/{categoryId}/submit
GET  /questionnaire/category/{categoryId}/saved-answers
GET  /questionnaire/category/{categoryId}/success
```

### Session Storage Structure
```php
'questionnaire_answers_' . $categoryId => [
    'questionnaire_id' => int,
    'category_id' => int,
    'answers' => [questionId => answerValue],
    'files' => [questionId => filePath],
    'user_id' => int,
    'updated_at' => timestamp,
]
```

### File Storage Structure
```
public/
  questionnaire_uploads/
    temp/
      {userId}/
        {categoryId}/
          {timestamp}_{filename}.{ext}
```

---

## 📋 BACKEND ASSUMPTIONS & LIMITATIONS

### File Upload Limitations
1. **Temporary Storage**: Files are stored in `temp/` directory before appointment creation
2. **Appointment Requirement**: Final file storage requires appointment_id (as per existing backend design)
3. **File Cleanup**: Temporary files should be cleaned up periodically (not implemented - consider cron job)
4. **File Movement**: When appointment is created, files should be moved from `temp/` to `{appointmentId}/` directory

### Session-Based Storage
- Answers are stored in session (not database) until appointment creation
- Session-based storage works within the same session
- Full persistence across logout/login would require backend changes to support `user_id + category_id` in `questionnaire_answers` table

### Backend Integration Points
1. **Appointment Creation**: When appointment is created, questionnaire files should be moved from temp directory to appointment directory
2. **File Reference**: File paths stored in session need to be used when creating `QuestionnaireAnswer` records with `file_path`

---

## ✅ VERIFICATION CHECKLIST

### Issue 1: POST Method
- ✅ Form has `action="#"` to prevent default submission
- ✅ All POST requests go to `/save-section` or `/submit` endpoints
- ✅ No POST requests sent to GET routes
- ✅ JavaScript handlers prevent form submission
- ✅ HTTP methods match backend definitions

### Issue 2: Section Navigation
- ✅ Each section displayed on separate page/step
- ✅ Route pattern: `/questionnaire/category/{categoryId}/section/{sectionIndex}`
- ✅ Next/Previous buttons implemented
- ✅ Required field validation prevents moving forward
- ✅ Navigation back allowed without validation
- ✅ Progress indicator shows "Section X of Y"
- ✅ Section index persisted in URL
- ✅ Answers saved on section change
- ✅ Answers restored when navigating back

### Issue 3: File Upload
- ✅ File upload field implemented
- ✅ File type validation: PDF, JPG, PNG
- ✅ File size validation: 5MB max
- ✅ Client-side and server-side validation
- ✅ Files uploaded to backend
- ✅ File paths stored in session
- ✅ File reference stored in answers
- ✅ Works for birth certificate and other file fields

---

## 🎯 DELIVERABLES

### 1. Correct Submission Endpoint Used
- **Endpoint**: `POST /questionnaire/category/{categoryId}/submit`
- **Used for**: Final questionnaire submission
- **Returns**: Success response with redirect URL to success page

### 2. Routes Created for Section Navigation
- **Route**: `GET /questionnaire/category/{categoryId}/section/{sectionIndex}`
  - Displays single section
  - Validates section index
  - Loads saved answers
  
- **Route**: `POST /questionnaire/category/{categoryId}/save-section`
  - Saves section answers
  - Validates required fields
  - Returns next section index
  - Handles navigation

### 3. File Upload Handling Flow

**Frontend → Backend Flow**:
1. User selects file in file input field
2. Client validates file type and size (5MB, PDF/JPG/PNG)
3. Form submitted to `POST /questionnaire/category/{categoryId}/save-section`
4. Backend validates file type and size
5. File stored in `public/questionnaire_uploads/temp/{userId}/{categoryId}/`
6. File path stored in session: `questionnaire_answers_{categoryId}.files.{questionId}`
7. File path also stored in answers: `questionnaire_answers_{categoryId}.answers.{questionId}`
8. On final submit, all files remain in session for appointment creation
9. When appointment is created, files should be moved to `{appointmentId}/` directory

**Backend Storage**:
- Temporary: `questionnaire_uploads/temp/{userId}/{categoryId}/{timestamp}_{filename}`
- Final (when appointment created): `questionnaire_uploads/{appointmentId}/{timestamp}_{filename}`

### 4. Backend Assumptions/Limitations

**Current Implementation**:
- Files stored temporarily before appointment creation
- File paths stored in session
- No database records created until appointment exists

**Future Enhancement Needed**:
- Move files from temp to appointment directory when appointment is created
- Update `QuestionnaireService::saveAnswers()` to handle file movement
- Consider cleanup job for old temporary files

**Backend Limitation**:
- `questionnaire_answers` table requires `appointment_id` (foreign key constraint)
- Cannot save answers to database before appointment creation
- Session-based storage is used as workaround
- Full persistence across logout/login would require schema changes

---

## 📝 FILES MODIFIED/CREATED

### New Files
1. `resources/views/website/questionnaire/category_section.blade.php` - Section view with navigation

### Modified Files
1. `app/Http/Controllers/Website/QuestionnaireController.php`
   - Added `showSection()` method
   - Added `saveSectionAnswers()` method
   - Updated `showByCategory()` to redirect to section 0
   - Updated `saveAnswers()` with file upload handling
   - Updated `submitQuestionnaire()` to use session answers

2. `routes/web.php`
   - Added section navigation routes

3. `resources/views/website/questionnaire/category_form.blade.php`
   - Updated form action to `#` (Issue 1 fix)

---

## ✨ SUMMARY

All three issues have been fixed:
1. ✅ POST method now uses correct endpoints
2. ✅ Section-wise navigation fully implemented
3. ✅ File upload (birth certificate) fully implemented

The implementation follows Laravel best practices, maintains backward compatibility, and integrates with existing backend architecture.
