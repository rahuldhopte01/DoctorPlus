# AGENT BRIEFING — BackupDoctor UI Integration

> **IMPORTANT**: Paste this entire document into your agent chat before starting any UI work.
> This is the source of truth for the current backend. Do NOT deviate from this flow without first suggesting the required backend change.

---

## Your Primary Directive

You are integrating a new UI/UX design into an **existing, fully functional Laravel backend**. Your job is to make the new UI work WITH the current backend — not around it.

**Hard Rule**: Do NOT make any page or feature static if the backend already serves it dynamically. If a dynamic integration seems difficult, suggest a specific backend change in a comment block like this:

```
// BACKEND CHANGE SUGGESTION:
// Route: GET /category/{id}
// Reason: [explain why the current route or data structure conflicts]
// Suggested fix: [describe what should change in the backend]
```

---

## Critical Issue — Category Pages

The biggest risk is this: The new UI has static category pages. **Do not keep them static.**

The backend already handles category pages dynamically:

- **Route**: `GET /categories` — paginated category listing with search & treatment filter
- **Route**: `GET /category/{id}` — individual category detail page

The controller (`WebsiteController@categories` and `WebsiteController@categoryDetail`) fetches:
- Category name, description, image
- Treatment it belongs to
- Associated medicines
- Whether a questionnaire is active (`$category->hasActiveQuestionnaire()`)

Every category page must be rendered via `GET /category/{id}` using the Blade template at `resources/views/website/category_detail.blade.php`. If the new design requires data that is not currently returned by this route, suggest what field to add to the controller — do NOT hardcode the content.

---

## Full Patient Flow (Do Not Break Any Step)

The patient journey is a connected chain. Every step feeds the next.

```
Homepage (/)
  └── Browse Categories (/categories)
        └── Category Detail (/category/{id})
              └── [If questionnaire active] Start Questionnaire
                    └── [If not logged in] → Login (/patient-login)
                          └── [After login] → Resume questionnaire
                    └── [If logged in] → Questionnaire Form
                          └── (/questionnaire/category/{categoryId})
                                └── Submit answers
                                      └── [Delivery-based] → Delivery flow → Payment
                                      └── [Appointment-based] → Doctor Search (/show-doctors)
                                            └── Doctor Profile (/doctor-profile/{id}/{name})
                                                  └── Booking Form (/booking/{doctor_id}/{name})
                                                        └── Submit (/bookAppointment POST)
                                                              └── Payment → Confirmation
                                                                    └── Patient Dashboard (/user_profile)
                                                                          └── View Prescription → Download PDF
```

**Never short-circuit this flow.** For example, do not link a category button directly to a static doctor list — it must go through the questionnaire check first.

---

## Route Reference (All Patient-Facing)

### Public Routes (No Login Required)
| URL | Purpose |
|-----|---------|
| `GET /` | Homepage |
| `GET /categories` | Category listing (supports `?search=` and `?treatment=`) |
| `GET /category/{id}` | Category detail page |
| `GET /show-doctors` | Doctor search/filter |
| `GET /doctor-profile/{id}/{name}` | Doctor profile + timeslots |
| `GET /patient-login` | Login page |
| `GET /signup` | Registration page |
| `GET /send_otp` | OTP entry page |
| `POST /verify_user` | OTP verification |

### Authenticated Routes (Login Required)
| URL | Purpose |
|-----|---------|
| `GET /questionnaire/category/{categoryId}` | Show questionnaire form |
| `POST /questionnaire/category/{categoryId}/save-section` | Save section answers (AJAX) |
| `POST /questionnaire/category/{categoryId}/submit` | Final questionnaire submission |
| `GET /questionnaire/category/{categoryId}/payment` | Questionnaire payment page |
| `GET /booking/{id}/{name}` | Appointment booking form |
| `POST /bookAppointment` | Submit booking |
| `GET /user_profile` | Patient dashboard |
| `GET /downloadPDF/{id}` | Download prescription PDF |
| `GET /prescription/pay/{id}` | Pay for prescription |
| `GET /patient-address` | Manage addresses |

---

## Data Models You Must Know

### Category
```
id, name, description, image, treatment_id, price, status, is_cannaleo_only
→ belongsTo(Treatment)
→ hasOne(Questionnaire)
→ belongsToMany(Medicine)
→ method: hasActiveQuestionnaire(): bool
```

### Doctor
```
id, name, gender, appointment_fees, experience, status, is_filled, subscription_status, is_popular
→ belongsToMany(Category) [via doctor_category pivot]
→ belongsToMany(Treatment) [via doctor_treatment pivot]
→ belongsTo(Hospital)
→ accessor: rate (average rating)
→ accessor: review (review count)
```

### Appointment
```
id, appointment_id (string), user_id, doctor_id, hospital_id,
date, time, appointment_status (pending|confirmed|completed|cancelled),
payment_status, amount, appointment_for, patient_name, age,
illness_information, drug_effect, note, is_insured,
questionnaire_id, questionnaire_completed_at
```

### Questionnaire (attached to Category)
```
id, category_id, name, description, status, version
→ hasMany(Section) → hasMany(Question)
→ Question field types: text | textarea | number | dropdown | radio | checkbox | file
→ Questions have: flagging_rules, conditional_logic, option_behaviors
```

---

## Critical Integration Rules

### 1. Category Pages Must Be Dynamic
- Do not hardcode category names, descriptions, images, or medicine lists.
- Use `GET /category/{id}` — data is served from the `category` DB table.
- The "Start Consultation" CTA must check `$category->hasActiveQuestionnaire()` before showing the questionnaire button.

### 2. Doctor-Category Relationship Is Many-to-Many
- A doctor can belong to multiple categories (via `doctor_category` pivot table).
- Do NOT use `doctor.category_id` — that column no longer represents the full relationship.
- Use `$doctor->categories` (Eloquent relationship) for filtering and display.

### 3. Questionnaire Flow Cannot Be Skipped
- If a category has an active questionnaire, the patient MUST complete it before booking an appointment.
- The questionnaire system uses session storage (`questionnaire_answers_{categoryId}`) to carry answers into the booking form.
- Skipping this step means questionnaire data will be lost.

### 4. Login Redirect Must Preserve Intent
- If a patient clicks "Start Questionnaire" without being logged in, the intent must be stored in session:
  ```php
  session()->put('questionnaire_intent', [
      'category_id' => $categoryId,
      'redirect_to' => url('/questionnaire/category/' . $categoryId),
  ]);
  ```
- After login, the user must be redirected back to the questionnaire — not the homepage.

### 5. Authentication Middleware
- Web routes use `auth` middleware (Laravel session auth).
- API routes use `auth:api` (Laravel Passport tokens).
- Do not mix these. The patient web UI always uses session-based auth.

### 6. Doctor Search Filters
`POST /show-doctors` supports these filters — all must remain functional in the new UI:
- `category[]` — array of category IDs
- `treatment_id`
- `doc_lat` + `doc_lang` — geo-location (hospital proximity search)
- `gender_type` — male / female
- `sort_by` — rating | latest | popular
- `search_doctor` — name search

### 7. Booking Form Required Fields
These fields are validated server-side and MUST be present in the booking form:
```
doctor_id, appointment_for, patient_name, age, patient_address,
phone_no, illness_information, drug_effect, date, time, hospital_id
Optional: is_insured, policy_insurer_name, policy_number, report_image[]
```

### 8. Payment Is Backend-Driven
- Appointment fee comes from `doctor.appointment_fees` — do not hardcode prices.
- Payment is handled via Stripe (primary), with Razor Pay, Flutterwave, PayStack, PayPal, COD as options.
- Stripe webhook is at `/stripe/webhook` — this is outside CSRF protection by design.
- Prescription payment is separate from appointment payment.

### 9. Working Hours & Timeslots
- Doctor timeslots are calculated via `CustomController::timeSlot($doctorId, $date)`.
- Working hours are stored as JSON in `working_hour.period_list`.
- The UI must fetch available timeslots dynamically (AJAX call) when the patient picks a date.

### 10. Multi-Language
- All UI text should use `{{ __('text') }}` — not hardcoded strings.
- Language files are at `resources/lang/{locale}/`.

---

## What to Do When the New Design Conflicts With the Backend

If the new UI design requires something the current backend does not support, do the following:

1. Integrate as much as you can with the existing flow.
2. Flag the conflicting part with a comment block:
   ```
   // BACKEND CHANGE SUGGESTION:
   // Area: [category detail / doctor search / booking / etc.]
   // Current backend behavior: [describe what it does now]
   // New UI requires: [describe what the design expects]
   // Suggested backend change: [describe the specific change — new field, new route, model change, etc.]
   ```
3. Do NOT make that section static as a workaround. Leave a placeholder and flag it.

---

## File Locations

| Type | Path |
|------|------|
| Web Routes | `routes/web.php` |
| API Routes | `routes/api.php` |
| Main Patient Controller | `app/Http/Controllers/Website/WebsiteController.php` |
| Questionnaire Controller | `app/Http/Controllers/Website/QuestionnaireController.php` |
| Questionnaire Payment | `app/Http/Controllers/Website/QuestionnairePaymentController.php` |
| Delivery Flow | `app/Http/Controllers/Website/QuestionnaireDeliveryController.php` |
| Patient-facing views | `resources/views/website/` |
| Models | `app/Models/` |
| Uploads | `public/images/upload/` |
| Prescription PDFs | `public/prescription/upload/` |

---

## Summary

| Principle | Rule |
|-----------|------|
| Category pages | Dynamic via `GET /category/{id}` — never static |
| Questionnaire | Must run before appointment if `hasActiveQuestionnaire()` is true |
| Doctor relationship | Many-to-many with category — use `$doctor->categories` |
| Login redirect | Preserve questionnaire intent in session |
| Doctor search | All 6 filters must remain functional |
| Booking form | All required fields must be present, server-validated |
| Prices | Always from DB — never hardcoded |
| Timeslots | Always fetched dynamically by date |
| Conflicts | Flag with BACKEND CHANGE SUGGESTION comment — do not go static |
