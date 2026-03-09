# Curobo Prescription API – Step-by-Step Agent Commands

Use this document to give one step at a time to another agent. Each step is self-contained. Reference: `CUROBO_PRESCRIPTION_API_PLAN.md`.

---

## Step 1: Database migrations

**Task:** Create migrations for the log table and the prescription Cannaleo flag.

**Commands / actions:**

1. Create migration for `cannaleo_prescription_log`:
   - Table: `cannaleo_prescription_log`
   - Columns: `id` (bigint PK), `prescription_id` (unsignedBigInteger nullable, FK to prescription.id), `questionnaire_submission_id` (unsignedBigInteger nullable, FK to questionnaire_submissions.id), `called_at` (timestamp), `request_payload` (json nullable), `response_status` (unsignedInteger nullable), `response_body` (text nullable), `external_order_id` (string nullable), `products_snapshot` (json), `total_medicine_cost` (decimal 10,2 nullable), `prescription_fee` (decimal 10,2 nullable), `error_message` (text nullable), `timestamps`.
   - Run: `php artisan make:migration create_cannaleo_prescription_log_table` then implement the schema.

2. Create migration for `prescription.is_cannaleo`:
   - Add column: `is_cannaleo` (boolean, default false) to table `prescription`.
   - Run: `php artisan make:migration add_is_cannaleo_to_prescription_table --table=prescription`.

3. Run migrations: `php artisan migrate`.

**Deliverable:** Both migrations exist and have been run. Table `cannaleo_prescription_log` exists; `prescription` has `is_cannaleo`.

---

## Step 2: Config

**Task:** Add prescription-related config so the prescription API URL and optional callback are configurable.

**Commands / actions:**

1. In `config/cannaleo.php`, add:
   - `'prescription_api_path' => env('CUROBO_PRESCRIPTION_API_PATH', '/api/v1/prescription/')`
   - `'prescription_callback_url' => env('CUROBO_PRESCRIPTION_CALLBACK_URL', '')`
2. In `.env.example`, add (optional) lines:
   - `CUROBO_PRESCRIPTION_API_PATH=/api/v1/prescription/`
   - `CUROBO_PRESCRIPTION_CALLBACK_URL=`
3. Optionally add `'default_signature_city' => env('CUROBO_DEFAULT_SIGNATURE_CITY', '')` for doctor.cityOfSignature fallback.

**Deliverable:** `config/cannaleo.php` returns prescription path and callback URL; .env.example documents them.

---

## Step 3: CannaleoPrescriptionLog model

**Task:** Create the Eloquent model for the log table and add the relationship on Prescription.

**Commands / actions:**

1. Create model: `php artisan make:model CannaleoPrescriptionLog`
2. Set table `cannaleo_prescription_log`, fillable: prescription_id, questionnaire_submission_id, called_at, request_payload, response_status, response_body, external_order_id, products_snapshot, total_medicine_cost, prescription_fee, error_message. Cast: request_payload, products_snapshot as array; called_at as datetime; total_medicine_cost, prescription_fee as decimal.
3. Add relationships: belongsTo Prescription, belongsTo QuestionnaireSubmission.
4. In `app/Models/Prescription.php`, add `is_cannaleo` to fillable and casts (`'is_cannaleo' => 'boolean'`), and a relationship `cannaleoPrescriptionLogs()` hasMany CannaleoPrescriptionLog.

**Deliverable:** Model `CannaleoPrescriptionLog` exists; Prescription has `is_cannaleo` and relation to logs.

---

## Step 4: Curobo Prescription API client

**Task:** Create a service class that POSTs to the Curobo prescription endpoint with the same auth as the catalog API.

**Commands / actions:**

1. Create `app/Services/Curobo/CuroboPrescriptionApi.php`.
2. Constructor: read base URL and API key from `config('cannaleo.curobo_api_url')` and `config('cannaleo.curobo_api_key')`, and SSL verify from `config('cannaleo.curobo_ssl_verify', true)` (same as CuroboCatalogApi).
3. Method `submitPrescription(array $payload): array`: POST to `{baseUrl}{prescription_api_path}` (from config, default `/api/v1/prescription/`) with headers `Accept: application/json`, `Content-Type: application/json`, `API-KEY: {apiKey}`. Body: JSON encode $payload. Use Laravel HTTP client with verify option. On success (2xx), return response JSON decoded array. On failure, log and throw a RuntimeException (or return a structure with success=false and error message—document which you choose).
4. Do not build the payload in this class; it only sends a given array.

**Deliverable:** Class `CuroboPrescriptionApi` with `submitPrescription(array $payload): array` (or equivalent error handling). Same base URL and API key as catalog.

---

## Step 5: Payload builder

**Task:** Build the exact JSON body expected by the Curobo prescription API from our models.

**Commands / actions:**

1. Create a dedicated class or a static method, e.g. `App\Services\Curobo\CuroboPrescriptionPayloadBuilder::build(...)` or a method on CuroboPrescriptionApi. Inputs: Prescription $prescription, QuestionnaireSubmission $submission, User $customer, Doctor $doctor, Collection|array of CannaleoMedicine $products, string $prescriptionUrl, CannaleoPharmacy $pharmacy.
2. Map to API shape:
   - prescriptionURL: the passed $prescriptionUrl.
   - internalOrderId: e.g. (string) $prescription->id or "RX-{$prescription->id}".
   - internalPharmacyId: $pharmacy->external_id (string).
   - doctor: name (doctor.user->name ?? doctor.name), phone (doctor.user->phone ?? ''), email (doctor.user->email ?? ''), cityOfSignature (hospital->address parsed for city, or config('cannaleo.default_signature_city', '') or last word of address), dateOfSignature (prescription->created_at->format('Y-m-d')).
   - customer: salutation from User.gender (male/female/other), firstname/lastname by splitting User.name on first space (if one part, put in lastname), dateOfBirth (User.dob Y-m-d), email, phone. homeAddress: use first UserAddress or empty; if single line put in streetName, leave houseNr/addressAddition empty, postalCode/city from submission delivery if needed or empty. deliveryAddress: delivery_address -> streetName, delivery_postcode -> postalCode, delivery_city -> city, delivery_state can go to addressAddition or leave empty; salutation/firstname/lastname same as customer.
   - products: for each CannaleoMedicine: id => external_id, name, price (float), category => category ?? 'flower', quantity => 1.
   - prepaid: 0. shipping: e.g. 'delivery' or 'pickup' from submission delivery_type. pickup_branch_id: pharmacy id if pickup (integer from external_id if numeric else 0). totalGross: sum of (price * quantity) for products. callbackUrl: config('cannaleo.prescription_callback_url', '').
3. Return a single associative array ready for json_encode (no extra keys). Handle nulls (empty string or omit as per API docs).

**Deliverable:** A payload builder that, given the listed inputs, returns the exact API request body array. No HTTP call inside this builder.

---

## Step 6: Centralize PDF generation (callable from controller)

**Task:** Ensure prescription PDF can be generated from the doctor flow (storePrescription) without duplicating logic.

**Commands / actions:**

1. Identify where PDF is generated: `WebsiteController::generatePrescriptionPdf()` and/or `PrescriptionPaymentController::generatePrescriptionPdf()`. If the logic is identical, extract to a single place (e.g. a service `PrescriptionPdfService::generate(Prescription $prescription): bool` or a static method that returns true on success or error string on failure).
2. Preferred: create `app/Services/PrescriptionPdfService.php` with method `generate(Prescription $prescription): bool|string` that contains the logic (load prescription with relations, build view data, generate PDF to public_path('prescription/upload/...'), set prescription.pdf and save). Return true on success, error message string on failure.
3. Replace calls in WebsiteController and PrescriptionPaymentController to use this service so behaviour is unchanged.
4. Ensure the prescription upload directory exists and is writable (mkdir in the service if needed).

**Deliverable:** One place (e.g. PrescriptionPdfService) that generates the PDF and updates prescription.pdf. Existing download/payment flows still work.

---

## Step 7: Generate PDF and set prescription URL in storePrescription (Cannaleo)

**Task:** For Cannaleo prescriptions, generate the PDF immediately after creating the prescription and build the public URL for the API.

**Commands / actions:**

1. In `app/Http/Controllers/Doctor/QuestionnaireReviewController.php`, in `storePrescription()`, inside the DB::transaction (or right after it), when `$isCannaleoPrescription` is true:
   - After Prescription::create(), you have the prescription model (assign the created prescription to a variable; currently create() is used without assigning—refactor so you have `$prescription = Prescription::create([...])` and set `is_cannaleo => true` in the create array).
2. After the transaction (so prescription is committed), if Cannaleo: call your PDF service to generate the PDF for this prescription. If generation fails, log and optionally show a warning; still proceed to call Curobo with the URL if you later add retry with PDF (or leave prescriptionURL empty and document—plan says we must have PDF; so if fail, either abort or set a flag and don’t call API until PDF exists). Recommended: generate PDF after transaction, then build URL; if PDF fails, do not call Curobo and log to cannaleo_prescription_log with error_message "PDF generation failed".
3. Prescription URL: `rtrim(config('app.url'), '/') . '/prescription/upload/' . $prescription->pdf` (ensure prescription.pdf is set after generation). If you use a signed route instead, build that URL here.
4. Ensure when creating the prescription you set `'is_cannaleo' => true` in the create array (migration from Step 1 must have been run).

**Deliverable:** For Cannaleo flow, prescription is created with is_cannaleo=true; PDF is generated right after; prescription URL is buildable for the payload.

---

## Step 8: Call Curobo API and write log in storePrescription

**Task:** After generating the PDF (and having the prescription URL), build the payload, call the Curobo prescription API, and write to cannaleo_prescription_log.

**Commands / actions:**

1. In `storePrescription()`, after the Cannaleo PDF generation step (Step 7), load: prescription (with relations), submission, customer (User), doctor (with user and hospital), selected CannaleoMedicine models, CannaleoPharmacy (submission->selectedCannaleoPharmacy).
2. Build payload using the payload builder from Step 5 (pass prescription, submission, customer, doctor, products list, prescription URL, pharmacy).
3. Build products_snapshot for logging: array of [cannaleo_medicine_id, name, price, quantity, category] and compute total_medicine_cost (sum price*quantity) and prescription_fee (prescription.payment_amount).
4. Call CuroboPrescriptionApi::submitPrescription($payload). Use try/catch. On success: insert CannaleoPrescriptionLog with prescription_id, questionnaire_submission_id, called_at=now(), request_payload=$payload (or redacted), response_status, response_body, external_order_id (if present in response), products_snapshot, total_medicine_cost, prescription_fee, error_message=null. On exception or non-2xx: insert log with error_message and optionally response_status/response_body; do not rethrow so the doctor still sees "Prescription and orders created successfully" (optionally add a warning flash message: "Prescription saved but partner API could not be notified; our team will follow up.").
5. Use the same prescription and submission ids for the log. If you use a new CuroboPrescriptionApi instance, instantiate it or resolve from container (no need to register in container if you new it).

**Deliverable:** Every Cannaleo prescription creation triggers one API call and one log row (success or failure). No change to non-Cannaleo flow.

---

## Step 9: (Optional) Admin list view for cannaleo_prescription_log

**Task:** Add a simple list (and optional detail) view in the SuperAdmin area to see when the API was called and what was sent/received.

**Commands / actions:**

1. Create a controller (e.g. SuperAdmin\CannaleoPrescriptionLogController) with index() (list logs, optionally filter by prescription_id or date) and show($id) (single log with request_payload and response_body).
2. Add routes under the super-admin prefix (e.g. Route::get('cannaleo-prescription-logs', ...), Route::get('cannaleo-prescription-logs/{id}', ...)).
3. Create a simple Blade list view (table: id, prescription_id, called_at, response_status, total_medicine_cost, error_message, link to show). Detail view: show full request_payload and response_body (consider hiding sensitive data if needed).
4. Add a link in the SuperAdmin menu/sidebar to "Cannaleo prescription logs" if such a menu exists.

**Deliverable:** SuperAdmin can open a page that lists Cannaleo prescription API logs and view details. Optional; can be skipped.

---

## Step 10: (Optional) Hospital city for doctor.cityOfSignature

**Task:** If you want to send a real city for the signing doctor, add a city field to hospitals and use it in the payload.

**Commands / actions:**

1. Migration: add `city` (string nullable) to table `hospital`. Run migrate.
2. Add `city` to Hospital model fillable.
3. In the payload builder (Step 5), for doctor.cityOfSignature use `$doctor->hospital->city ?? config('cannaleo.default_signature_city', '')` or fallback to parsing hospital.address. If you add a hospital edit form, add the city field there.

**Deliverable:** Hospital can store city; payload builder uses it for doctor.cityOfSignature when present. Optional.

---

## Order of execution

- **Must be in order:** 1 → 2 → 3 → 4 → 5 → 6 → 7 → 8.  
- **Optional:** 9 (after 8), 10 (can be done after 5; then update payload builder to use hospital.city).

**Suggested single command you can give to the agent:**

"Implement the Curobo prescription API integration by following the steps in CUROBO_PRESCRIPTION_AGENT_STEPS.md. Do steps 1 through 8 in order. Steps 9 and 10 are optional; do them if the document says to or if I ask. After each step, ensure migrations run and code is consistent with CUROBO_PRESCRIPTION_API_PLAN.md."

Or give one step at a time, e.g.:

"Do Step 1 from CUROBO_PRESCRIPTION_AGENT_STEPS.md: create the database migrations for cannaleo_prescription_log and prescription.is_cannaleo, then run migrations."
