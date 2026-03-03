# Cannaleo / Curobo Catalog API – Separate-Flow Implementation Plan

**Goal:** Call the Curobo catalog API every 30 minutes and maintain **separate** Cannaleo pharmacy and Cannaleo medicine data, without changing the existing `pharmacy` / `medicine` / `pharmacy_inventory` flow.

**API:** `GET https://api.curobo.de/api/v1/catalog/`  
**Auth:** `API-KEY: <JWT>`  
**Schedule:** Every 30 minutes  

---

## 1. Why Separate Tables?

- **Current flow:** `pharmacy` (user-linked), `medicine` (global catalog), `pharmacy_inventory` (pharmacy × medicine × price/stock). Used for your own pharmacies and internal medicine catalog.
- **Curobo flow:** External catalog; one API returns both pharmacy and product data per item. No `user_id`, different lifecycle (sync-only).
- **Benefit of separation:** No risk to existing pharmacy/medicine logic; clear “this is API-sourced” data; simpler sync (update/create by API keys only).

So we introduce **only**:

- `cannaleo_pharmacy` – one row per distinct API pharmacy.
- `cannaleo_medicine` – one row per API product, with `cannaleo_pharmacy_id`, `name`, `category`, and an explicit “this is API medicine” flag.

No new columns on `pharmacy` or `medicine`, no changes to `pharmacy_inventory`.

---

## 2. Data Model (New Tables Only)

### 2.1 `cannaleo_pharmacy`

| Column            | Type           | Description |
|-------------------|----------------|-------------|
| `id`              | bigint PK      | Auto-increment |
| `external_id`     | int/varchar    | `pharmacy_id` from API (e.g. 4) – unique per source |
| `name`            | string         | `pharmacy_name` (e.g. "Demo Cannabis-Taxi-Stuttgart") |
| `domain`          | string nullable| `pharmacy_domain` (e.g. "cannabistaxi.curobo.de") |
| `last_synced_at`  | timestamp null | Last time we saw this pharmacy in API response |
| `created_at`      | timestamp      | |
| `updated_at`      | timestamp      | |

- **Unique key:** `external_id` (or `(external_id)` if you later support multiple API sources).
- **Sync rule:** On each run, for each distinct `pharmacy_id` in the response: **update** if exists (name, domain, last_synced_at), **create** if not.

### 2.2 `cannaleo_medicine`

| Column               | Type           | Description |
|----------------------|----------------|-------------|
| `id`                 | bigint PK      | Auto-increment |
| `cannaleo_pharmacy_id` | FK → cannaleo_pharmacy | Which Cannaleo pharmacy this product belongs to |
| `external_id`        | string         | API `id` (e.g. "bedrocan-afina") – unique per pharmacy |
| `ansay_id`           | string null    | API `ansayId` (e.g. "nlUP8hTtJ7k4pHmPb0Y4") |
| `name`               | string         | API `name` (e.g. "Bedrocan") |
| `category`           | string null    | API `category` (e.g. "flower") |
| `is_api_medicine`    | boolean        | Always `true` – “this row is from the API catalog” |
| `price`              | decimal null   | API `price` |
| `thc`                | decimal null   | API `thc` |
| `cbd`                | decimal null   | API `cbd` |
| `genetic`            | string null    | e.g. "Sativa", "Hybrid" |
| `strain`             | string null    | e.g. "Afina" |
| `country`            | string null    | e.g. "Niederlande" |
| `manufacturer`       | string null    | |
| `grower`             | string null    | |
| `availability`       | string null    | API `availibility` (note API typo) |
| `irradiated`         | tinyint null   | 0/1 |
| `terpenes`           | json null      | Array of terpene names |
| `raw_data`           | json null      | Full API object for debugging/audit |
| `last_synced_at`     | timestamp null | |
| `created_at`         | timestamp      | |
| `updated_at`         | timestamp      | |

- **Unique key:** `(cannaleo_pharmacy_id, external_id)` so the same API product id can exist per pharmacy if the API ever returns it that way; if the API guarantees global uniqueness of `id`, you can use `external_id` only and drop `cannaleo_pharmacy_id` from the unique key (but keeping the FK still links medicine to pharmacy).
- **Sync rule:** For each item in the catalog: resolve or create `cannaleo_pharmacy` by `pharmacy_id`, then **updateOrCreate** `cannaleo_medicine` by `(cannaleo_pharmacy_id, external_id)` and set all fields above (and `is_api_medicine = true`).

Optional: add a small `cannaleo_sync_log` table (e.g. `id`, `started_at`, `completed_at`, `status`, `items_fetched`, `pharmacies_created`, `pharmacies_updated`, `medicines_created`, `medicines_updated`, `error_message`) for visibility without touching existing tables.

### 2.3 `category_cannaleo_medicine` (pivot – link to our treatment/category)

So that **Cannaleo medicines are shown for the same questionnaire/treatment categories** as internal medicines, we need a mapping from **our** `category` to Cannaleo medicines.

| Column               | Type        | Description |
|----------------------|-------------|-------------|
| `id`                 | bigint PK   | Auto-increment (optional; can be pivot without PK) |
| `category_id`        | FK → category | Our app’s category (treatment type; has questionnaire) |
| `cannaleo_medicine_id` | FK → cannaleo_medicine | Cannaleo product to show for this category |
| `created_at`         | timestamp   | |
| `updated_at`         | timestamp   | |

- **Unique key:** `(category_id, cannaleo_medicine_id)` so the same Cannaleo medicine is not attached twice to the same category.
- **Usage:** When we show “medicines for this category” to the customer, we load both:
  - Internal medicines: `category_medicine` (existing) → `medicine`.
  - Cannaleo medicines: `category_cannaleo_medicine` → `cannaleo_medicine` (with `cannaleo_pharmacy`).
- **No change** to existing `category` or `category_medicine`; this is an additional, parallel mapping.

---

## 3. High-Level Flow (Every 30 Minutes)

1. **Trigger:** Laravel scheduler runs a command every 30 minutes (e.g. `cannaleo:catalog-sync`).
2. **HTTP:** Call `GET https://api.curobo.de/api/v1/catalog/` with header `API-KEY: <token>` and `accept: application/json`.
3. **Parse:** Expect a JSON array (or an object with a key like `data`/`catalog` that contains the array of items you showed).
4. **Deduplicate pharmacies:** From the array, collect unique `pharmacy_id` (and `pharmacy_name`, `pharmacy_domain`). For each:
   - **Update or create** `cannaleo_pharmacy` by `external_id = pharmacy_id`.
5. **Process products:** For each catalog item:
   - Resolve `cannaleo_pharmacy_id` from `pharmacy_id`.
   - **Update or create** `cannaleo_medicine` by `(cannaleo_pharmacy_id, external_id)` (or by `external_id` if you make it globally unique), set `name`, `category`, `is_api_medicine = true`, and all other fields from the response.
6. **Optional:** Write one row to `cannaleo_sync_log` (started_at, completed_at, counts, status, error if failed).
7. **No changes** to `pharmacy`, `medicine`, or `pharmacy_inventory`.

---

## 4. Assigning Cannaleo Pharmacy Medicines to Our Category / Treatment (Same Flow as Internal Medicines)

**Current flow (internal only):**

- A **category** = treatment type (e.g. a questionnaire category). It has a questionnaire; after the questionnaire, the customer sees **medicine selection**.
- Which medicines are shown is determined by **`category_medicine`**: only medicines linked to that category are shown.
- Customer selects up to 3 medicines; selection is stored in **`questionnaire_submission.selected_medicines`** as `[ { "medicine_id": 1 }, ... ]`.
- Doctor reviews and (later) orders use that selection.

**What we add (same flow, two sources):**

- **Assignment:** Super admin (or admin) assigns **Cannaleo medicines** to **our categories** via the new pivot **`category_cannaleo_medicine`** (same idea as assigning internal medicines to a category).
- **Presentation:** When showing the medicine selection page for a category, we load **both**:
  1. Internal medicines: `$category->medicines()` (existing, via `category_medicine`).
  2. Cannaleo medicines: `$category->cannaleoMedicines()` (new, via `category_cannaleo_medicine`, with `cannaleoPharmacy` for name/domain).
- **Single list (or two sections):** We present both in the same step – e.g. one list with a “source” badge (Internal / Partner pharmacy), or two sections (“Our pharmacy” / “Cannaleo partner”). The only difference is the **data source** (we create internal data manually; Cannaleo data we save from the API), then we **assign both** to categories and show them together.
- **Selection storage:** Submission must support both types:
  - **Option A (recommended):** Extend `selected_medicines` to allow either `medicine_id` (internal) or `cannaleo_medicine_id` (Cannaleo), e.g. `[ { "medicine_id": 1 }, { "cannaleo_medicine_id": 5 } ]`. Validation: only allow IDs that belong to the current category (both from `category_medicine` and `category_cannaleo_medicine`).
  - **Option B:** Keep `selected_medicines` for internal only and add `selected_cannaleo_medicines` (same structure but for Cannaleo). Slightly simpler validation but two fields to maintain everywhere.
- **Doctor review:** When the doctor sees the submission, resolve both:
  - `medicine_id` → `Medicine` (with brand).
  - `cannaleo_medicine_id` → `CannaleoMedicine` (with cannaleoPharmacy), and show name, category, THC/CBD, price, pharmacy name, etc.
- **Order/fulfilment:** Internal selections → existing `purchase_medicine` / `medicine_child` flow. Cannaleo selections → either a separate order type (e.g. `purchase_cannaleo_medicine` or a flag on the same order) or a separate fulfilment path (e.g. send to Curobo prescription API). This can be Phase 2 of the order side; the plan here focuses on **assignment + presentation + selection storage**.

**Summary:**

| Step in flow        | Internal (existing)                    | Cannaleo (new)                                              |
|---------------------|----------------------------------------|-------------------------------------------------------------|
| **Assignment**      | `category_medicine` (admin)            | `category_cannaleo_medicine` (admin)                        |
| **Show to customer** | `$category->medicines()`               | `$category->cannaleoMedicines()`                            |
| **Store selection** | `selected_medicines[].medicine_id`     | Same array: `selected_medicines[].cannaleo_medicine_id`    |
| **Doctor review**   | Resolve `Medicine`                     | Resolve `CannaleoMedicine` + pharmacy                      |
| **Order**           | `purchase_medicine` + `medicine_child` | Separate path or new table (later)                          |

---

## 5. Implementation Steps (Checklist)

### Phase 1 – Database and models

1. **Migration: create `cannaleo_pharmacy`**
   - Columns as in §2.1.
   - Unique index on `external_id`.

2. **Migration: create `cannaleo_medicine`**
   - Columns as in §2.2.
   - Foreign key `cannaleo_pharmacy_id` → `cannaleo_pharmacy.id` (on delete: cascade or set null, your choice).
   - Unique index on `(cannaleo_pharmacy_id, external_id)` (or `external_id` only if global).

3. **Migration: create `category_cannaleo_medicine`**
   - Columns as in §2.3. Foreign keys to `category` and `cannaleo_medicine`. Unique `(category_id, cannaleo_medicine_id)`.

4. **Optional: migration for `cannaleo_sync_log`**
   - Fields suggested in §2.2.

5. **Models**
   - `App\Models\CannaleoPharmacy`: fillable, `hasMany(CannaleoMedicine::class)`.
   - `App\Models\CannaleoMedicine`: fillable, `belongsTo(CannaleoPharmacy::class)`, cast `terpenes` and `raw_data` as array/json; `belongsToMany(Category::class, 'category_cannaleo_medicine')`.
   - `App\Models\Category`: add `cannaleoMedicines()` → `belongsToMany(CannaleoMedicine::class, 'category_cannaleo_medicine')` (with pivot timestamps if needed).

### Phase 2 – API and config

6. **Config**
   - e.g. `config/services.php` or a dedicated `config/cannaleo.php`: `curobo_api_url`, `curobo_api_key`, optionally `catalog_sync_enabled`.

7. **.env**
   - `CUROBO_CATALOG_API_URL=https://api.curobo.de`
   - `CUROBO_CATALOG_API_KEY=<JWT>`
   - Do not commit the real key; use env only.

8. **API client**
   - Class e.g. `App\Services\Curobo\CuroboCatalogApi` or `CannaleoCatalogApi`:
     - Method `getCatalog(): array`:
       - GET `{base_url}/api/v1/catalog/`
       - Headers: `accept: application/json`, `API-KEY: {key}`
       - Return decoded array of products (or throw on non-2xx / invalid JSON).

### Phase 3 – Sync logic

9. **Sync service**
   - Class e.g. `App\Services\Cannaleo\CannaleoCatalogSync`:
     - Uses API client to fetch catalog.
     - Builds unique list of pharmacies from catalog items; for each, updateOrCreate `CannaleoPharmacy` by `external_id`.
     - For each catalog item: resolve `CannaleoPharmacy`, then updateOrCreate `CannaleoMedicine` by `(cannaleo_pharmacy_id, external_id)`; set `is_api_medicine = true`, map all fields (name, category, price, thc, cbd, etc.), store `raw_data` if desired.
     - Optional: write `CannaleoSyncLog` at start (status started) and end (completed/failed + counts).

10. **Artisan command**
    - e.g. `php artisan cannaleo:catalog-sync`:
      - Calls the sync service (and optionally logs to `cannaleo_sync_log`).
      - Handles exceptions (log and optionally set sync_log status to failed).

11. **Scheduler**
    - In `App\Console\Kernel::schedule()`:
      - `$schedule->command('cannaleo:catalog-sync')->everyThirtyMinutes()->withoutOverlapping(35);`
    - Ensure cron runs: `* * * * * php /path/to/artisan schedule:run`.

### Phase 4 – Category assignment and customer presentation

12. **Medicine selection (customer):**
    - In `QuestionnaireMedicineController::showMedicineSelection()` (or equivalent): load both `$category->medicines()->...` and `$category->cannaleoMedicines()->with('cannaleoPharmacy')->...`; pass both to the view (e.g. `medicines`, `cannaleoMedicines`).
    - View: show one combined list or two sections (e.g. “Our pharmacy” / “Partner (Cannaleo)”), with name, price, THC/CBD for Cannaleo items, and pharmacy name.

13. **Save selection:**
    - Extend validation to allow either `medicine_id` or `cannaleo_medicine_id` in each item. Allowed IDs: internal from `$category->medicines()`, Cannaleo from `$category->cannaleoMedicines()`.
    - Store in `questionnaire_submission.selected_medicines` as e.g. `[ { "medicine_id": 1 }, { "cannaleo_medicine_id": 5 } ]`. Keep max 3 total (or define rule: e.g. max 3 internal + max 3 Cannaleo, or max 3 combined).

14. **Doctor review:**
    - When loading submission, resolve each entry: if `medicine_id` load `Medicine` (with brand); if `cannaleo_medicine_id` load `CannaleoMedicine` (with cannaleoPharmacy). Display both in the review UI (name, source, price, THC/CBD for Cannaleo).

15. **Admin: assign Cannaleo medicines to categories:**
    - Super admin (or category edit) UI: for each category, allow attaching/detaching Cannaleo medicines (e.g. multi-select or checkboxes), same idea as assigning internal medicines. Persist via `category_cannaleo_medicine` (attach/detach on `Category` or `CannaleoMedicine`).

### Phase 5 – Optional improvements

16. **Queue (optional)**  
    - Dispatch a job from the command that runs the same sync logic, so long runs don’t block the scheduler; keep timeout and retries in mind.

17. **Soft “stale” handling (optional)**  
    - If a product disappears from the API, you can either leave it as-is or add a `last_synced_at` and mark rows not seen in the last run as “stale” (e.g. a boolean or status column). Not required for the first version.

18. **Admin/UI (later)**  
    - Read-only list of `cannaleo_pharmacy` and `cannaleo_medicine`, last sync time / sync_log, and category assignment UI, without touching existing pharmacy/medicine admin.

19. **Order/fulfilment for Cannaleo selections (later)**  
    - When submission is approved, Cannaleo items may need a separate order path (e.g. send to Curobo prescription API or create `purchase_cannaleo_medicine` records). Define in a follow-up plan.

---

## 5. API Response → Table Mapping (Quick Reference)

From each catalog item:

- **CannaleoPharmacy:** `external_id` ← `pharmacy_id`, `name` ← `pharmacy_name`, `domain` ← `pharmacy_domain`.
- **CannaleoMedicine:**  
  - `external_id` ← `id`, `ansay_id` ← `ansayId`, `name` ← `name`, `category` ← `category`, `is_api_medicine` ← `true`,  
  - `price` ← `price`, `thc` ← `thc`, `cbd` ← `cbd`, `genetic` ← `genetic`, `strain` ← `strain`, `country` ← `country`,  
  - `manufacturer` ← `manufacturer`, `grower` ← `grower`, `availability` ← `availibility`, `irradiated` ← `irradiated`,  
  - `terpenes` ← `terpenes`, `raw_data` ← full object.

---

## 6. Summary

| Item | Approach |
|------|----------|
| **Tables** | New only: `cannaleo_pharmacy`, `cannaleo_medicine`, `category_cannaleo_medicine` (optional: `cannaleo_sync_log`) |
| **Existing tables** | No changes to `pharmacy`, `medicine`, `pharmacy_inventory`, `category`, `category_medicine` |
| **“API medicine” flag** | Column `is_api_medicine` on `cannaleo_medicine` (always true) |
| **API category** | Column `category` on `cannaleo_medicine` from API (e.g. "flower") |
| **Our category/treatment** | Pivot `category_cannaleo_medicine`: assign Cannaleo medicines to our questionnaire categories so they are shown with internal medicines |
| **Presentation** | Same flow as internal: show medicines for a category = internal (`category_medicine`) + Cannaleo (`category_cannaleo_medicine`); customer selects; store in `selected_medicines` with either `medicine_id` or `cannaleo_medicine_id` |
| **Sync frequency** | Every 30 minutes via Laravel schedule |
| **Idempotency** | updateOrCreate by `external_id` (pharmacy) and `(cannaleo_pharmacy_id, external_id)` (medicine) |

Once this plan is approved, the next step is to implement Phase 1 (migrations + models including category pivot), then Phase 2 (config + API client), then Phase 3 (sync), then Phase 4 (category assignment + medicine selection + doctor review).
