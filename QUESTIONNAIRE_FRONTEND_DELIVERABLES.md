# Questionnaire Frontend Flow - Deliverables

This document outlines all components, pages, routes, and APIs created for the complete questionnaire frontend flow.

## Overview

The frontend flow allows users to discover treatment categories, view category details, take questionnaires, with proper authentication handling and answer persistence.

---

## 1. Pages Created

### 1.1 Categories Landing Page
**File:** `resources/views/website/categories.blade.php`
**Route:** `/categories` (named: `categories`)
**Purpose:** Display all treatment categories with their treatment information
**Features:**
- Lists all active categories
- Shows category name, image, and associated treatment
- Clickable cards that navigate to category detail page
- Responsive grid layout

### 1.2 Category Detail Page
**File:** `resources/views/website/category_detail.blade.php`
**Route:** `/category/{id}` (named: `category.detail`)
**Purpose:** Show category details, treatment information, and questionnaire CTA
**Features:**
- Category information display
- Treatment details
- "Do you want to take questionnaire?" CTA button
- Authentication gate (redirects to login if not authenticated)
- Intent storage in localStorage and session

### 1.3 Category-Based Questionnaire Form
**File:** `resources/views/website/questionnaire/category_form.blade.php`
**Route:** `/questionnaire/category/{categoryId}` (named: `questionnaire.category`)
**Purpose:** Render and handle questionnaire completion
**Features:**
- Dynamic questionnaire rendering based on backend schema
- Section-wise navigation
- Progress indicator
- Auto-save functionality (progressive saving)
- Resume questionnaire with saved answers
- Client-side validation
- Conditional logic support
- Multiple field types (text, textarea, number, dropdown, radio, checkbox, file)
- Final submit with validation

### 1.4 Questionnaire Success Page
**File:** `resources/views/website/questionnaire/success.blade.php`
**Route:** `/questionnaire/category/{categoryId}/success` (named: `questionnaire.success`)
**Purpose:** Confirm successful questionnaire submission
**Features:**
- Success confirmation message
- Navigation options (browse more categories, go home)

---

## 2. Controller Methods Added

### 2.1 WebsiteController
**File:** `app/Http/Controllers/Website/WebsiteController.php`

**New Methods:**
- `categories()` - Display categories landing page
- `categoryDetail($id)` - Display category detail page
- `patientLogin()` - Updated to handle questionnaire intent redirect

### 2.2 QuestionnaireController
**File:** `app/Http/Controllers/Website/QuestionnaireController.php`

**New Methods:**
- `showByCategory($categoryId)` - Show questionnaire form for a category (with auth check)
- `saveAnswers(Request $request, $categoryId)` - Save answers (autosave/progressive save)
- `getSavedAnswers($categoryId)` - Get saved answers for resume
- `submitQuestionnaire(Request $request, $categoryId)` - Final submit with validation

---

## 3. Routes Added

**File:** `routes/web.php`

### Public Routes (No Authentication Required)
```php
Route::get('/categories', [WebsiteController::class, 'categories'])->name('categories');
Route::get('/category/{id}', [WebsiteController::class, 'categoryDetail'])->name('category.detail');
```

### Authenticated Routes (Require Authentication)
```php
Route::get('/questionnaire/category/{categoryId}', [WebQuestionnaireController::class, 'showByCategory'])->name('questionnaire.category');
Route::post('/questionnaire/category/{categoryId}/save', [WebQuestionnaireController::class, 'saveAnswers'])->name('questionnaire.save');
Route::post('/questionnaire/category/{categoryId}/submit', [WebQuestionnaireController::class, 'submitQuestionnaire'])->name('questionnaire.submit');
Route::get('/questionnaire/category/{categoryId}/saved-answers', [WebQuestionnaireController::class, 'getSavedAnswers'])->name('questionnaire.saved-answers');
Route::get('/questionnaire/category/{categoryId}/success', function($categoryId) {
    return view('website.questionnaire.success', compact('categoryId'));
})->name('questionnaire.success');
```

---

## 4. APIs Consumed

### 4.1 Backend Endpoints Used

1. **Get Questionnaire for Category**
   - Endpoint: `GET /api/questionnaire/{categoryId}` (existing)
   - Used by: `QuestionnaireController::getQuestionnaire()`
   - Purpose: Fetch questionnaire structure

2. **Save Answers (Progressive Save)**
   - Endpoint: `POST /questionnaire/category/{categoryId}/save`
   - Used by: Auto-save functionality in category_form.blade.php
   - Purpose: Save answers progressively as user fills form

3. **Final Submit**
   - Endpoint: `POST /questionnaire/category/{categoryId}/submit`
   - Used by: Final submit button in category_form.blade.php
   - Purpose: Validate and submit final answers

4. **Get Saved Answers**
   - Endpoint: `GET /questionnaire/category/{categoryId}/saved-answers`
   - Used by: Resume functionality
   - Purpose: Load previously saved answers

### 4.2 Backend Services Used

- `QuestionnaireService::getQuestionnaireForCategory()` - Get questionnaire structure
- `QuestionnaireService::validateAnswers()` - Validate answers
- `QuestionnaireService::checkForBlockingFlags()` - Check for blocking/warning flags

---

## 5. Frontend Components & Logic

### 5.1 Authentication Gate (Phase 3)

**Implementation:**
- Client-side: localStorage for persistence across page reloads
- Server-side: Session storage for redirect handling
- Logic: When user clicks "Do you want to take questionnaire?":
  1. Check if user is authenticated
  2. If NOT authenticated:
     - Store intent in localStorage (categoryId, treatmentId, redirectUrl)
     - Store intent in session (via redirect_to query parameter)
     - Redirect to login page
  3. After login:
     - Check session for questionnaire_intent
     - Redirect to questionnaire page
     - Clear intent from session

**Files:**
- `resources/views/website/category_detail.blade.php` (CTA button logic)
- `app/Http/Controllers/Website/WebsiteController.php` (login redirect logic)

### 5.2 Answer Persistence (Phase 5)

**Implementation:**
- **Client-side (localStorage):** Saves answers automatically on input change
- **Server-side (Session):** Saves answers via AJAX auto-save endpoint
- **Resume Logic:**
  - On page load, check localStorage for saved answers
  - Load answers into form fields
  - Also fetch from server session as backup

**Storage Keys:**
- localStorage: `questionnaire_answers_{categoryId}`
- Session: `questionnaire_answers_{categoryId}`

**Note:** Full persistence across logout/login would require backend changes to support user_id + category_id in questionnaire_answers table (currently requires appointment_id).

---

## 6. User Journey (End-to-End Flow)

### Step 1: Discovery
1. User visits `/categories` page
2. Views all available treatment categories
3. Clicks on a category card

### Step 2: Category Detail
1. User lands on `/category/{id}` page
2. Views category and treatment information
3. Sees "Do you want to take questionnaire?" button

### Step 3: Authentication Gate
1. User clicks questionnaire CTA
2. **If NOT logged in:**
   - Intent stored in localStorage and session
   - Redirected to `/patient-login?redirect_to=/questionnaire/category/{categoryId}`
3. **If logged in:**
   - Directly proceed to questionnaire page

### Step 4: Questionnaire Completion
1. User lands on `/questionnaire/category/{categoryId}` page
2. Questionnaire loads with saved answers (if any)
3. User fills form:
   - Answers auto-save to localStorage and server (every 2 seconds after typing stops)
   - Progress bar updates in real-time
   - Conditional logic shows/hides questions based on answers
4. User can navigate back or continue filling

### Step 5: Final Submit
1. User clicks "Submit Questionnaire" button
2. Client-side validation runs
3. Form data sent to backend for validation
4. Backend validates and checks for blocking flags
5. If valid:
   - Answers stored in session (for future appointment creation)
   - User redirected to success page
6. If blocked:
   - Error message shown
   - User cannot proceed

### Step 6: Success & Next Steps
1. User sees success confirmation
2. Options to browse more categories or go home
3. Answers remain in session for use during appointment booking (if applicable)

---

## 7. Key Features Implemented

### ✅ Phase 1: Landing Page
- Dynamic category listing from backend
- Category cards with images and treatment info
- Navigation to category detail

### ✅ Phase 2: Category Detail Page
- Category and treatment information display
- Questionnaire CTA button
- Authentication evaluation

### ✅ Phase 3: Authentication Gate
- Intent storage (localStorage + session)
- Login redirect with intent preservation
- Post-login redirect to questionnaire
- Clean intent restoration

### ✅ Phase 4: Questionnaire Page
- Dynamic rendering from backend schema
- Sections and questions
- All field types supported
- Conditional logic
- Validation rules
- Progress indicator
- Section-wise navigation

### ✅ Phase 5: Answer Persistence
- Auto-save (progressive saving)
- localStorage for client-side persistence
- Session storage for server-side persistence
- Resume functionality
- Answers persist within session

### ✅ Phase 6: Final Submit Flow
- Validation (client + server)
- Blocking flag checks
- Success confirmation
- Redirect to success page
- Answers stored for future use

### ✅ Phase 7: Code Organization
- Separated pages, components, and logic
- Reusable questionnaire rendering logic
- Clear authentication flow
- Inline comments explaining logic

---

## 8. Technical Notes

### 8.1 Answer Persistence Limitation

**Current Implementation:**
- Answers are stored in session (server-side) and localStorage (client-side)
- Session storage persists within the same session
- localStorage persists across page reloads but not across logout/login

**Backend Limitation:**
- The `questionnaire_answers` table requires `appointment_id` (foreign key constraint)
- To achieve true persistence across logout/login, backend would need:
  - Either: Add `user_id` and `category_id` columns to `questionnaire_answers` table
  - Or: Create a separate `user_questionnaire_drafts` table

**Current Workaround:**
- Answers are stored in session and localStorage
- When user logs back in, they can resume from localStorage if available
- Final submission stores answers in session for use during appointment creation

### 8.2 Authentication Flow

The authentication gate uses a hybrid approach:
1. **Client-side (localStorage):** Persists intent across page reloads
2. **Server-side (Session):** Handles redirect after login
3. **URL Parameter:** `redirect_to` query parameter passed to login page

### 8.3 Field Types Supported

- Text input
- Textarea
- Number
- Dropdown select
- Radio buttons
- Checkboxes (multiple selection)
- File upload

### 8.4 Conditional Logic

Questions can have conditional logic that shows/hides them based on other question answers. Supported operators:
- equals
- not_equals
- contains
- greater_than
- less_than

---

## 9. Files Modified

1. `app/Http/Controllers/Website/WebsiteController.php` - Added categories() and categoryDetail() methods, updated patientLogin()
2. `app/Http/Controllers/Website/QuestionnaireController.php` - Added category-based questionnaire methods
3. `routes/web.php` - Added new routes for categories and questionnaire flow
4. `resources/views/website/categories.blade.php` - NEW: Categories landing page
5. `resources/views/website/category_detail.blade.php` - NEW: Category detail page
6. `resources/views/website/questionnaire/category_form.blade.php` - NEW: Category-based questionnaire form
7. `resources/views/website/questionnaire/success.blade.php` - NEW: Success confirmation page

---

## 10. Testing Recommendations

1. **Category Listing:**
   - Verify all active categories are displayed
   - Test category card navigation

2. **Category Detail:**
   - Verify category and treatment information displays correctly
   - Test questionnaire CTA button (both logged in and logged out states)

3. **Authentication Gate:**
   - Test as logged-out user (should redirect to login)
   - Test as logged-in user (should go directly to questionnaire)
   - Test post-login redirect (should go to questionnaire)

4. **Questionnaire Form:**
   - Test all field types
   - Test conditional logic
   - Test auto-save functionality
   - Test resume with saved answers
   - Test validation (required fields, format validation)
   - Test file uploads

5. **Final Submit:**
   - Test successful submission
   - Test blocking flags
   - Test warning flags
   - Test validation errors

---

## 11. Future Enhancements (Optional)

1. **True Cross-Session Persistence:**
   - Modify backend to support user_id + category_id in questionnaire_answers
   - Implement draft saving to database

2. **Reusable Blade Components:**
   - Create Blade components for QuestionRenderer and SectionRenderer
   - Extract common questionnaire rendering logic

3. **Enhanced UX:**
   - Add section navigation (jump to section)
   - Add "Save and Continue Later" explicit button
   - Add questionnaire completion percentage in sidebar

4. **Analytics:**
   - Track questionnaire completion rates
   - Track drop-off points
   - Track time to complete

---

## Summary

✅ **All 8 phases completed:**
- Phase 1: Categories Landing Page
- Phase 2: Category Detail Page  
- Phase 3: Authentication Gate
- Phase 4: Questionnaire Page
- Phase 5: Answer Persistence
- Phase 6: Final Submit Flow
- Phase 7: Code Organization
- Phase 8: Documentation

The complete frontend flow is implemented and ready for testing. All backend APIs are integrated, and the system follows existing project architecture and styling patterns.
