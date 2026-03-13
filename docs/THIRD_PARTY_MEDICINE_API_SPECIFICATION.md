# Third-Party Medicine Catalog API Specification

This document describes the **data format and structure** that a third-party supplier must provide so we can integrate their medicine catalog into our **normal** questionnaire flow. By following this specification, medicines can be imported, linked to our categories, and shown to patients during medicine selection without breaking our existing flow.

---

## 1. Purpose and context

- **Flow**: Normal questionnaire flow (delivery or pharmacy pickup).
- **Use case**: Your system sends us **two things**: (1) a **category list** (your treatment categories with stable IDs), and (2) a **medicine list** where each medicine includes **category IDs from that category list**. We sync both: we create/update our categories from your list, then create/update medicines and link them to our categories using your category IDs. Patients then see these medicines when they select “Choose medicines” for the matching category.
- **Result**: Same UX as today: patient picks up to 3 medicines per category; doctor can review and modify; prescription and fulfillment continue as usual.

---

## 2. Our internal structure (what we store)

| Concept | Our table / pivot | Purpose |
|--------|--------------------|---------|
| **Medicine** | `medicine` | One row per product: name, strength, form, brand, status, description, image. |
| **Brand** | `medicine_brands` | Optional. Brand name; many medicines can share one brand. |
| **Category** | `category` | Treatment categories (e.g. “Migraine”, “Anxiety”). We sync these from **your category list** (you send the list; we create/update our rows and keep a mapping from your category ID to ours). |
| **Category–Medicine link** | `category_medicine` | Pivot: which medicines are available in which category. We fill this from the **category_ids** you send on each medicine (IDs from your category list). |

---

## 3. Required data format (payload we expect)

We expect a **single JSON payload** (e.g. from a GET catalog endpoint or a POST/PUT sync) that includes **both** your category list and your medicine list. We sync categories first, then medicines (using your category IDs to link them).

### 3.1 Top-level shape

Payload must include **`categories`** and **`medicines`**. Optional top-level fields: `provider_id`, `sync_timestamp`.

```json
{
  "provider_id": "your-company-id",
  "sync_timestamp": "2026-03-12T10:00:00Z",
  "categories": [ ... ],
  "medicines": [ ... ]
}
```

### 3.2 Category list (`categories`)

Each item in **`categories`** defines one of your treatment categories. We use **`id`** to match medicines to categories and to upsert on future syncs. Use a stable string or number (e.g. `"CAT-001"` or `1`).

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `id` | string or number | **Yes** | Your unique category identifier. Must be stable and unique. Medicines will reference this in `category_ids`. |
| `name` | string | **Yes** | Category name as we should display it (e.g. "Pain Relief", "Migraine"). |
| `description` | string | No | Optional description. |
| `status` | integer | No | `1` = active, `0` = inactive. Default: `1`. |

**Example category object:**

```json
{
  "id": "CAT-001",
  "name": "Pain Relief",
  "description": "Medicines for pain and fever.",
  "status": 1
}
```

### 3.3 Each item in `medicines` array

Every object in `medicines` must follow this structure. Fields marked **Required** are mandatory for the normal flow; the rest are optional but recommended.

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `external_id` | string | **Yes** | Your unique product identifier. We use it to match records on future syncs (create/update). Must be stable and unique per product. |
| `name` | string | **Yes** | Medicine/product name as shown to the patient. |
| `strength` | string | No | e.g. "10 mg", "500 mg". Shown on the medicine card. |
| `form` | string | No | e.g. "Tablet", "Capsule", "Solution". Shown on the medicine card. |
| `brand_name` | string | No | Brand name. We will create or reuse a brand in `medicine_brands` and link the medicine to it. |
| `description` | string | No | Short description or notes (stored for display/admin). |
| `image_url` | string | No | Full URL to product image. We may store the URL or download and store locally; implementation detail on our side. |
| `status` | integer | No | `1` = active (shown), `0` = inactive (hidden). Default: `1`. |
| `category_ids` | array of strings/numbers | **Yes** | List of **category IDs from your category list** (the `id` field of items in `categories`). We sync categories first, then link this medicine to our categories that match these IDs. A medicine can be in one or more categories. |

**Important:** Every value in `category_ids` must match the `id` of an item in the **same payload’s** `categories` array. We will create/update our categories from that list, then link the medicine to the corresponding categories.

### 3.4 Example medicine object

```json
{
  "external_id": "PROD-2026-001",
  "name": "Paracetamol 500mg",
  "strength": "500 mg",
  "form": "Tablet",
  "brand_name": "Generic Pharma",
  "description": "Pain relief and fever reduction.",
  "image_url": "https://your-cdn.com/images/paracetamol-500.png",
  "status": 1,
  "category_ids": ["CAT-001", "CAT-002"]
}
```

### 3.5 Example full payload

```json
{
  "provider_id": "acme-pharma",
  "sync_timestamp": "2026-03-12T10:00:00Z",
  "categories": [
    { "id": "CAT-001", "name": "Pain Relief", "description": "Pain and fever.", "status": 1 },
    { "id": "CAT-002", "name": "General Wellness", "status": 1 }
  ],
  "medicines": [
    {
      "external_id": "PROD-2026-001",
      "name": "Paracetamol 500mg",
      "strength": "500 mg",
      "form": "Tablet",
      "brand_name": "Generic Pharma",
      "description": "Pain relief and fever reduction.",
      "image_url": "https://your-cdn.com/images/paracetamol-500.png",
      "status": 1,
      "category_ids": ["CAT-001", "CAT-002"]
    },
    {
      "external_id": "PROD-2026-002",
      "name": "Ibuprofen 400mg",
      "strength": "400 mg",
      "form": "Tablet",
      "brand_name": "Generic Pharma",
      "status": 1,
      "category_ids": ["CAT-001"]
    }
  ]
}
```

---

## 4. How we will use this (our side)

1. **Categories**: For each item in `categories`, we create or update a row in our `category` table, storing a mapping from your category `id` to our internal category id. We use this mapping when linking medicines.
2. **Brands**: For each `brand_name` in medicines, we create or find a row in `medicine_brands` and set the medicine’s `brand_id`.
3. **Medicines**: For each item in `medicines`, we upsert by `external_id` (we store `external_id` on our medicine table). We set: name, strength, form, description, image (or image URL), status, brand_id.
4. **Category–medicine links**: For each medicine, we read `category_ids` (your category IDs). For each ID we look up our corresponding category (from the mapping built in step 1) and ensure a row exists in `category_medicine` linking that medicine to that category. So the medicine appears in the questionnaire for each of those categories.
5. **Idempotency**: Same payload sent again should result in the same state (no duplicates; updates in place).

So: **you send the category list and the medicine list (with category_ids from that list); we sync both and link medicines to categories so the normal flow keeps running.**

---

## 5. What we need from you

- **API or file**:  
  - Either a **GET** endpoint that returns the JSON above (we call it on a schedule or on-demand), or  
  - A **POST** endpoint to which we send a request and you respond with the same JSON, or  
  - A **file export** (e.g. JSON) delivered by SFTP/email that follows the same structure.

- **Authentication**:  
  If the API is protected, we need an agreed method (e.g. API key in header, Bearer token) and credentials.

- **Category list**:  
  Include **`categories`** in every payload (or in a dedicated endpoint we sync first). Each category must have a stable `id`; medicines reference these IDs in `category_ids`.

- **external_id** (medicines) and **id** (categories):  
  Stable, unique per product/category, never reused.

---

## 6. Summary (quick reference)

**Categories** (in `categories` array):

| Field | Required | We use it for |
|-------|----------|----------------|
| `id` | Yes | Your category ID; medicines reference this in `category_ids`; we map to our category |
| `name` | Yes | Our category name |
| `description` | No | Stored on category |
| `status` | No | 1 = active, 0 = inactive |

**Medicines** (in `medicines` array):

| Field | Required | We use it for |
|-------|----------|----------------|
| `external_id` | Yes | Upsert key, no duplicates |
| `name` | Yes | Display name in medicine selection |
| `strength` | No | Subtitle/display on card |
| `form` | No | Subtitle/display on card |
| `brand_name` | No | Link to `medicine_brands` |
| `description` | No | Stored on medicine |
| `image_url` | No | Medicine image |
| `status` | No | 1 = active, 0 = inactive |
| `category_ids` | Yes | IDs from your `categories` list; we link medicine to our categories via these |

---

## 7. Next steps

1. We will add `external_id` (and optionally `external_provider`) to our `medicine` table and store external category id mapping for `category` (so we can sync your categories and map your IDs to ours). We will implement the sync job that consumes the full payload (categories + medicines).
2. You implement the API or file export that returns both **`categories`** and **`medicines`**, with each medicine including **`category_ids`** from your category list.
3. We run a test sync and verify that categories and medicines appear correctly and that medicines show in the “Select medicines” step for the right categories.

If you need a different format (e.g. CSV with column names matching these fields), we can define an alternative mapping in an addendum to this document.
