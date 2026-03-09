# Curobo Prescription API – Implementation Plan

**Goal:** When a doctor approves a prescription for a Cannaleo medicine, call the Curobo prescription API with the correct payload (including our generated prescription PDF URL), and maintain an audit log of every call (when, which medicines, costs, etc.). Webhook handling for order status changes is out of scope for this phase.

---

## 0. What Is Present vs What We Build / Extend

### 0.1 Already present (use as-is)

| Area | What exists |
|------|-------------|
| **API config & catalog client** | `config/cannaleo.php` (curobo_api_url, curobo_api_key, curobo_ssl_verify). `CuroboCatalogApi` with `API-KEY` header – same key used for prescription API. |
| **Prescription create flow** | `QuestionnaireReviewController::storePrescription()` – creates prescription for Cannaleo with medicines JSON, status `approved_pending_payment`; no internal orders. |
| **Prescription model** | `Prescription` (id, user_id, doctor_id, medicines, pdf, payment_amount, etc.). |
| **PDF generation logic** | `WebsiteController::generatePrescriptionPdf()`, `PrescriptionPaymentController::generatePrescriptionPdf()` – can be reused/called from storePrescription. |
| **QuestionnaireSubmission** | delivery_type, selected_cannaleo_pharmacy_id, selected_medicines (cannaleo_medicine_id list), delivery_address, delivery_postcode, delivery_city, delivery_state. |
| **User (customer)** | name, email, phone, dob, gender. |
| **CannaleoMedicine** | external_id, name, price, category (sync from catalog). |
| **CannaleoPharmacy** | external_id, name, domain. |
| **Doctor** | name, user_id, hospital_id. |
| **Doctor’s User** | name, email, phone (so we have doctor name, email, phone). |
| **Hospital** | name, phone, address (single field). |
| **UserAddress** | address (single line), user_id. |

### 0.2 Build from scratch

| Item | Description |
|------|-------------|
| **Curobo Prescription API client** | New class (e.g. `CuroboPrescriptionApi`) – POST to `/api/v1/prescription/` with same API-KEY, JSON body, return response. |
| **Payload builder** | Build full API JSON from Prescription, Submission, User, Doctor, CannaleoMedicine list (including mapping addresses and handling gaps). |
| **Table `cannaleo_prescription_log`** | New migration + model – log every API call (when, prescription_id, products snapshot, costs, request/response, errors). |
| **Prescription URL** | Logic to produce public (or signed) URL for `prescription/upload/<pdf>` so Curobo can fetch the PDF. |
| **Call + log in storePrescription** | After creating Cannaleo prescription and generating PDF: build payload → call API → write log (success or failure). |
| **Optional: admin view** | Simple list/detail for `cannaleo_prescription_log`. |

### 0.3 Extend / adjust (we have less data than API needs)

| Area | API needs | What we have | Adjustment needed |
|------|-----------|--------------|--------------------|
| **Doctor – cityOfSignature** | City where prescription was signed | Hospital has only `address` (full text), no separate `city` | **Option A:** Add `hospital.city` (migration + form) and use it. **Option B:** Parse city from `hospital.address` or use a config default (e.g. `config('cannaleo.default_signature_city')`). |
| **Doctor – dateOfSignature** | Date of signature (e.g. 2011-05-18) | No stored “signature date” | **Option A:** Use prescription `created_at` date (same as “prescription date”). **Option B:** Add optional `doctor.date_of_signature` or hospital-level setting if they want a different date; otherwise use prescription date. |
| **Doctor – phone/email** | Present | From Doctor’s User (email, phone) | Use as-is; ensure doctor’s user has email/phone filled where possible. |
| **Customer – firstname / lastname** | Separate first and last name | User has single `name` | **Option A:** Split `User.name` on first space (e.g. “John Doe” → firstname “John”, lastname “Doe”). **Option B:** Add `User.firstname`, `User.lastname` (migration + profile/registration) for accurate data. |
| **Customer – salutation** | e.g. "male" / "female" | User has `gender` | Map gender to API salutation (male/female/other) in payload builder. |
| **Customer – homeAddress** | streetName, houseNr, addressAddition, postalCode, city | `user_address` has only `address` (one line) | **Short term:** Send single line in `streetName` (or one field), leave others empty or use placeholders; confirm with Curobo if acceptable. **Long term:** Add to `user_address`: streetName, houseNr, postalCode, city (migration + forms). |
| **Customer – deliveryAddress** | Same structure | QuestionnaireSubmission has delivery_address, delivery_postcode, delivery_city, delivery_state (no street/houseNr split) | **Short term:** Put `delivery_address` in streetName, postalCode/city from submission; houseNr/addressAddition empty or from address string. **Long term:** Add structured delivery fields if Curobo requires them. |
| **Prescription – Cannaleo flag** | — | Prescription has no “is_cannaleo” | Add `prescription.is_cannaleo` (boolean) in migration; set true when creating Cannaleo prescription. Helps logging and future queries. |

**Summary:** We have enough to call the API with small code-only mappings (split name, use prescription date, map gender). For **doctor**, the only real gaps are **cityOfSignature** (hospital has no city field) and **dateOfSignature** (use prescription date). If you want correct doctor data in Curobo, add **hospital.city** (and optionally a dedicated signature date field); otherwise use config/defaults and prescription created_at.

---

## 1. API Understanding

### 1.1 Endpoint and auth

- **URL:** `POST https://api.curobo.de/api/v1/prescription/`
- **Auth:** Same as catalog API: header `API-KEY: <token>` (from `config('cannaleo.curobo_api_key')`). Use the same base URL and SSL verify settings from `config/cannaleo.php` (`curobo_api_url`, `curobo_ssl_verify`).
- **Headers:** `Accept: application/json`, `Content-Type: application/json`.

### 1.2 Request body (summary)

| Field | Type | Purpose |
|-------|------|--------|
| `prescriptionURL` | string | **URL to our prescription PDF** (must be publicly reachable by Curobo). |
| `internalOrderId` | string | Our internal reference (e.g. prescription id or composite order id). |
| `internalPharmacyId` | string | Our reference for the partner pharmacy (e.g. Cannaleo pharmacy external_id or our cannaleo_pharmacy id). |
| `doctor` | object | name, phone, email, cityOfSignature, dateOfSignature. |
| `customer` | object | salutation, firstname, lastname, dateOfBirth, email, phone, homeAddress, deliveryAddress (each address: streetName, houseNr, addressAddition, postalCode, city; deliveryAddress also has salutation, firstname, lastname). |
| `products` | array | Items: id (API product id), name, price, category (e.g. "flower"), quantity. |
| `prepaid` | number | Prepaid amount. |
| `shipping` | string | Shipping method/code. |
| `pickup_branch_id` | number | Branch id if pickup (clarify with Curobo if this maps to their pharmacy id). |
| `totalGross` | number | Total gross amount. |
| `callbackUrl` | string | Webhook URL for order status updates (optional for now; can be empty or a placeholder). |

---

## 2. When to Call the API

- **Trigger:** When the doctor **creates/approves** a Cannaleo prescription in the “Create prescription” flow (i.e. when `storePrescription` runs with `cannaleo_prescription === true` and the prescription is successfully created).
- **Flow today:**  
  - Doctor selects Cannaleo medicines and submits the form → `QuestionnaireReviewController::storePrescription()` → validation → `Prescription::create()` with `medicines` JSON and status `approved_pending_payment` → no internal orders created.  
- **Change:** After the prescription is created (and we have a prescription id and, if needed, a PDF), call the Curobo prescription API once, then write the audit log. No change to when the doctor approves; only add the API call and logging after success.

---

## 3. Prescription PDF and `prescriptionURL`

- The API expects a **URL** (`prescriptionURL`) that points to the prescription PDF. Curobo must be able to GET this URL (e.g. from their server).
- **Current behaviour:** The prescription PDF is generated **later**: either on first user download (`WebsiteController::downloadPDF` → `generatePrescriptionPdf`) or after payment (`PrescriptionPaymentController` → `generatePrescriptionPdf`). So at the moment the doctor clicks “Approve & Generate Prescription”, the PDF may not exist yet.
- **Required:** Before calling the Curobo prescription API we must have a PDF and a **public URL** for it.
  - **Option A (recommended):** Generate the PDF **immediately** when storing a Cannaleo prescription (inside the same flow as `storePrescription`), save it to `prescription.pdf`, then build a public URL (e.g. `config('app.url') . '/prescription/upload/' . $prescription->pdf` or a dedicated route that serves the file with a token or signed URL if you want to avoid listing). Ensure the URL is reachable by Curobo (no login required for that request, or use a time-limited signed URL if the API supports it).
  - **Option B:** If Curobo supports file upload (e.g. multipart) instead of URL, we could send the PDF in the request; the plan below assumes URL only unless Curobo docs say otherwise.
- **Conclusion:** In the implementation phase, generate the prescription PDF for Cannaleo prescriptions at the time of `storePrescription`, then use that file to derive `prescriptionURL` for the API call.

---

## 4. Data Mapping (Our System → API)

### 4.1 Source of truth at call time

- **Prescription** (just created): id, user_id, doctor_id, medicines (JSON), pdf (after we generate it), payment_amount (prescription fee).
- **QuestionnaireSubmission** (same user/category/questionnaire): delivery_type, selected_cannaleo_pharmacy_id, selected_medicines (array of `cannaleo_medicine_id`), delivery_address_id, delivery_address, delivery_postcode, delivery_city, delivery_state.
- **User** (customer): name, email, phone, dob, gender (→ salutation).
- **UserAddress** (home): currently single `address` field; API wants streetName, houseNr, addressAddition, postalCode, city. Either: (a) add columns to `user_address` later, or (b) use submission delivery fields as fallback for “delivery” and for “home” use a single line or parsed address.
- **Doctor + User + Hospital:** doctor name (doctor.user.name or doctor.name), phone/email (doctor.user), cityOfSignature/dateOfSignature (could come from hospital or settings; if not present, use placeholders and add config later).
- **CannaleoMedicine** (per selected id): external_id (→ products[].id), name, price, category (→ products[].category; map to API enum e.g. "flower" if needed), quantity from request or default 1.
- **CannaleoPharmacy:** external_id (→ internalPharmacyId or pickup_branch_id; confirm with Curobo which field they use).

### 4.2 Mapping table

| API field | Source |
|-----------|--------|
| prescriptionURL | Public URL to `prescription/upload/<prescription.pdf>` or signed URL. |
| internalOrderId | `prescription.id` or string like `"RX-{prescription.id}"`. |
| internalPharmacyId | `CannaleoPharmacy.external_id` for selected_cannaleo_pharmacy_id. |
| doctor.name | Doctor’s user name or doctor name. |
| doctor.phone, email | From doctor’s user. |
| doctor.cityOfSignature, dateOfSignature | Hospital city / today’s date or from config. |
| customer.firstname, lastname | Split User.name or store separately; if not available, use full name in lastname. |
| customer.salutation | Map User.gender to "male"/"female"/other per API. |
| customer.dateOfBirth | User.dob (format Y-m-d or as API expects). |
| customer.email, phone | User. |
| customer.homeAddress | UserAddress (or first address); if only one line, put in streetName or split if you add columns. |
| customer.deliveryAddress | QuestionnaireSubmission delivery_* fields + User name/salutation. |
| products[].id | CannaleoMedicine.external_id. |
| products[].name | CannaleoMedicine.name. |
| products[].price | CannaleoMedicine.price. |
| products[].category | CannaleoMedicine.category (or map to API enum). |
| products[].quantity | 1 or from future UI. |
| totalGross | Sum of (price * quantity) for products + any prescription fee if included; clarify with Curobo. |
| prepaid | 0 unless we already collected payment. |
| shipping | Fixed string or from submission (e.g. "delivery"/"pickup"); confirm values with Curobo. |
| pickup_branch_id | If delivery_type is pickup, use Cannaleo pharmacy id (external or internal) per Curobo docs. |
| callbackUrl | Optional; can be empty or a route we add later for webhooks. |

### 4.3 Gaps to resolve before coding

- **Address:** Our `user_address` has only `address` (single line). For full API compliance we may need to add streetName, houseNr, postalCode, city (and optionally addressAddition), or a single field that we parse. For delivery we already have delivery_address, delivery_postcode, delivery_city, delivery_state.
- **prescriptionURL accessibility:** Ensure the URL is publicly GET-table by Curobo (no auth or use a time-limited signed URL if supported).
- **category enum:** Confirm with Curobo the allowed values for `products[].category` (e.g. "flower") and map `CannaleoMedicine.category` accordingly.
- **internalPharmacyId vs pickup_branch_id:** Confirm with Curobo whether internalPharmacyId is their pharmacy id or ours, and what pickup_branch_id means.

---

## 5. Audit Log (Cannaleo Prescription API Log)

- **Purpose:** For each call to the Curobo prescription API, record: when it was called, for which prescription, which medicines (and quantities), cost per medicine and total, request/response (or at least response status and id), and any error.
- **Suggested table:** `cannaleo_prescription_log` (or `curobo_prescription_log`).

Suggested columns:

| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| prescription_id | bigint FK nullable | Our prescription.id. |
| questionnaire_submission_id | bigint FK nullable | So we can trace back to submission. |
| called_at | timestamp | When the API was called. |
| request_payload | json nullable | Full body sent (or redact if needed). |
| response_status | int nullable | HTTP status. |
| response_body | text/json nullable | Response body (or summary). |
| external_order_id | string nullable | If Curobo returns an order id. |
| products_snapshot | json | List of { cannaleo_medicine_id, name, price, quantity, category } (and total line cost). |
| total_medicine_cost | decimal nullable | Sum of (price * quantity). |
| prescription_fee | decimal nullable | Our prescription fee for reference. |
| error_message | text nullable | If call failed. |
| created_at, updated_at | timestamps | |

- **Write log:** Once per API call (success or failure). On success, store response_status, response_body, external_order_id if present; on failure, store error_message and optionally response_body.

---

## 6. Implementation Steps (High Level)

1. **Config**  
   - Ensure `config/cannaleo.php` has base URL and API key (already used for catalog). Add if needed: `prescription_api_path` (e.g. `/api/v1/prescription/`), and optional `prescription_callback_url` for later.

2. **Prescription PDF at create time (Cannaleo only)**  
   - In `QuestionnaireReviewController::storePrescription()`, when `$isCannaleoPrescription` is true and after `Prescription::create()`:
     - Generate the prescription PDF (reuse logic from `WebsiteController::generatePrescriptionPdf()` or `PrescriptionPaymentController::generatePrescriptionPdf()`).
     - Save filename to `prescription.pdf` and persist.
   - Ensure the PDF is generated with the same medicines and data as the prescription (already the case if we use the same view and prescription model).

3. **Prescription URL**  
   - Define how Curobo will access the PDF (e.g. public path `https://yourdomain.com/prescription/upload/<filename>` or a dedicated route with optional signed URL). Implement that and build `prescriptionURL` in code.

4. **Curobo Prescription API client**  
   - New class (e.g. `App\Services\Curobo\CuroboPrescriptionApi` or extend existing service):  
     - Method `submitPrescription(array $payload): array` that POSTs to `{baseUrl}/api/v1/prescription/` with `API-KEY` and JSON body, returns decoded response and/or throws on failure.  
   - Reuse same base URL and API key as `CuroboCatalogApi` (and same SSL verify).

5. **Payload builder**  
   - New class or method (e.g. `CuroboPrescriptionPayloadBuilder` or method in the same service): given Prescription, QuestionnaireSubmission, User, Doctor, and list of CannaleoMedicine models, build the exact JSON body expected by the API (doctor, customer, products, prescriptionURL, internalOrderId, internalPharmacyId, totalGross, etc.).  
   - Resolve address mapping (delivery from submission; home from UserAddress or fallback).

6. **Call API and log**  
   - In `storePrescription()`, after creating the prescription and generating the PDF for Cannaleo:
     - Build payload.
     - Call `CuroboPrescriptionApi::submitPrescription($payload)`.
     - Insert into `cannaleo_prescription_log`: prescription_id, submission id, called_at, products_snapshot (names, prices, quantities, total_medicine_cost), prescription_fee, request_payload (or summary), response_status, response_body, external_order_id, error_message.
   - On exception (network, 4xx/5xx): log the error in the same table and optionally rethrow or show a warning to the doctor (“Prescription saved but partner API temporarily failed; we will retry or contact support.”).

7. **Prescription flag (optional but useful)**  
   - Add `prescription.is_cannaleo` (boolean) or similar so we can later query “all Cannaleo prescriptions” and so the log clearly relates to Cannaleo. Set it in `Prescription::create()` when `$isCannaleoPrescription` is true. Migration + model update.

8. **Admin / visibility**  
   - Optional: simple list or detail view for `cannaleo_prescription_log` (e.g. in SuperAdmin or Doctor panel) so staff can see when the API was called, for which prescription, and what was sent/received.

9. **Webhook**  
   - Not in scope for this phase. Later: add a route and controller for Curobo to call when order status changes; persist status in DB and optionally link to `cannaleo_prescription_log` or a new `cannaleo_orders` table.

---

## 7. Order of Work (Suggested)

1. Add migration for `cannaleo_prescription_log` (and optionally `prescription.is_cannaleo`).  
2. Add Curobo prescription API client (POST + auth) and payload builder; unit test payload shape if possible.  
3. Move or centralize PDF generation so it can be called from `storePrescription` for Cannaleo; generate PDF right after creating the prescription.  
4. Implement prescription URL (public or signed).  
5. In `storePrescription`, after PDF and payload: call API, then write log (success or failure).  
6. Test with a real Cannaleo prescription (sandbox if available).  
7. Optional: admin view for logs; webhook in a later phase.

---

## 8. Risks and Notes

- **PDF generation failure:** If PDF generation fails, we cannot send a valid prescriptionURL. Decide: block prescription creation and show error, or create prescription without calling Curobo and log “PDF generation failed” and retry later (e.g. when user downloads PDF).  
- **API failure:** Prescription is already created. Log the failure and decide whether to retry (job queue) or notify support.  
- **Idempotency:** If the doctor resubmits or we retry, Curobo might duplicate orders. Clarify with Curobo if they support idempotency key or if we must avoid duplicate calls per prescription_id.  
- **Address:** Until we have structured address fields, we may send partial or single-line address; confirm with Curobo if that is acceptable.

---

## 9. Summary

| Item | Action |
|------|--------|
| API auth | Same as catalog: `API-KEY` from `cannaleo.curobo_api_key`. |
| When to call | Right after creating a Cannaleo prescription (and generating its PDF). |
| prescriptionURL | Public (or signed) URL to our generated prescription PDF. |
| Payload | Map doctor, customer, products, addresses from existing models; internalOrderId = prescription id; internalPharmacyId = Cannaleo pharmacy external_id. |
| Log | New table `cannaleo_prescription_log`: when called, prescription_id, products snapshot, costs, request/response, errors. |
| Webhook | Deferred. |

This plan does not change any existing behaviour except: (1) generating the PDF earlier for Cannaleo prescriptions, and (2) adding one API call and one log write after that. All other logic (approval flow, validation, submission, delivery type) remains as is.
