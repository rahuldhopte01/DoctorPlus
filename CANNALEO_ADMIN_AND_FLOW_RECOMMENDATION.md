# Cannaleo Admin Portal & Customer Flow – Recommendation

## Goal

- **Admin:** See Cannaleo medicine listing, Cannaleo pharmacy listing, and assign categories to the Cannaleo flow (so questionnaire → pharmacy → medicine works for Cannaleo).
- **Flow – Cannaleo-only category:** When a category is **Cannaleo-only** (`is_cannaleo_only`), **no delivery option is shown** (Cannaleo API handles delivery). After questionnaire submit → partner pharmacy listing → medicine selection for that pharmacy (customer must buy; no prescription-only). Doctor review etc. unchanged.
- **Flow – Other categories:** Keep the same high-level flow: **Category → Questionnaire → [Delivery/Pharmacy] → Medicine selection → Doctor review.** Cannaleo is just another “source” attached to the same category: after the category questionnaire is answered, the customer can choose **Cannaleo partner** → select **Cannaleo pharmacy** → select **Cannaleo medicines** for that category. The rest (submission, doctor review, prescription) stays the same.

---

## 1. Best approach: Cannaleo assigned to category (same flow)

- **Categories** already have questionnaires and **internal** medicines (`category_medicine`).
- **Cannaleo** is assigned to the **same category** via `category_cannaleo_medicine` (Cannaleo medicines per category). No separate “Cannaleo category”; one category can offer both internal and Cannaleo.
- **Which Cannaleo pharmacies appear** for a category: those that have **at least one Cannaleo medicine** assigned to that category (derived from `category_cannaleo_medicine` → `cannaleo_medicine` → `cannaleo_pharmacy`). No extra “assign pharmacy to questionnaire” table needed: assign **Cannaleo medicines** to the category; pharmacies are implied.
- **Optional (if you want to restrict which pharmacies show):** Add a pivot `category_cannaleo_pharmacy` so admin can explicitly “assign that pharmacy the questionnaire” (i.e. which Cannaleo pharmacies are allowed for that category). For most cases, deriving from assigned medicines is enough.

**Recommendation:** Start **without** `category_cannaleo_pharmacy`. Admin assigns **Cannaleo medicines** to categories; any Cannaleo pharmacy that has at least one of those medicines assigned to the category is shown in the customer flow. If later you need to hide some pharmacies per category, add `category_cannaleo_pharmacy` then.

---

## 2. Admin portal (backend)

| Need | Solution |
|------|----------|
| **Cannaleo pharmacy listing** | New admin page: list `cannaleo_pharmacy` (read-only from sync). Show name, domain, last_synced_at. Optional: link to “Medicines” per pharmacy. |
| **Cannaleo medicine listing** | New admin page: list `cannaleo_medicine` with pharmacy name, name, category (API), price, THC/CBD. Filter by pharmacy/category. Show which **app categories** each medicine is assigned to (from `category_cannaleo_medicine`). |
| **Assign Cannaleo to category / “assign pharmacy to questionnaire”** | **Option A:** In **Category edit**: add a section “Cannaleo medicines for this category” (multi-select Cannaleo medicines). Same idea as internal medicines (which are assigned from Medicine create/edit). Pharmacies are then implied. **Option B:** Separate “Category Cannaleo assignment” page: pick category, then attach/detach Cannaleo medicines (and optionally Cannaleo pharmacies if you add the pivot). |

**Recommendation:** **Option A** – Category edit page gets a “Cannaleo medicines” multi-select (and optionally a read-only list of “Cannaleo pharmacies that will appear for this category” derived from those medicines). Plus two menu items: **Cannaleo Pharmacies** (list) and **Cannaleo Medicines** (list + which categories they’re in).

---

## 3. Customer flow (same as now, with Cannaleo branch)

Current flow:

1. Choose **category** → **Questionnaire** (by category).
2. **Delivery choice:** Delivery | Pickup (our pharmacy).
3. If Delivery → **Address** → **Medicine selection** (internal).
4. If Pickup → **Pharmacy selection** (internal) → **Medicine selection** (internal).
5. **Medicine selection** → store `selected_medicines` (internal `medicine_id`).
6. Doctor review → accept prescription.

**With Cannaleo (recommended):**

1. Same: **Category** → **Questionnaire**.
2. **Delivery choice:** Delivery | Pickup (our pharmacy) | **Cannaleo partner** (only if this category has Cannaleo medicines assigned).
3. If **Cannaleo partner:**
   - **Cannaleo pharmacy selection:** Show Cannaleo pharmacies that have at least one medicine in `category_cannaleo_medicine` for this category. Save `selected_cannaleo_pharmacy_id` on `questionnaire_submissions`.
   - **Cannaleo medicine selection:** Show only Cannaleo medicines for this category **and** for the selected Cannaleo pharmacy. Save in `selected_medicines` as `{ "cannaleo_medicine_id": 5 }` (and keep existing `medicine_id` for internal).
4. If Delivery or Pickup: unchanged (address or internal pharmacy → internal medicine selection).
5. **Doctor review:** Resolve both `medicine_id` (internal) and `cannaleo_medicine_id` (Cannaleo + pharmacy name). Rest of flow (accept prescription, etc.) same.

So: **after the Cannaleo category questionnaire is answered** → customer chooses Cannaleo → selects **pharmacy** → selects **medicine**; the rest is the same until the doctor accepts the prescription.

---

## 4. Data model changes

| Item | Change |
|------|--------|
| `questionnaire_submissions` | Add `selected_cannaleo_pharmacy_id` (nullable, FK to `cannaleo_pharmacy`). Extend `delivery_type` to allow `'cannaleo'` (or add a separate enum column if you prefer to keep `delivery_type` strictly delivery/pickup and use e.g. `partner_source` = internal/cannaleo). |
| `selected_medicines` (JSON) | Allow entries with either `medicine_id` (internal) or `cannaleo_medicine_id` (Cannaleo). Validation: only IDs that belong to the current category (and for Cannaleo, to the selected Cannaleo pharmacy). |

---

## 5. Summary table

| Step | Internal (existing) | Cannaleo (new) |
|------|---------------------|----------------|
| **Category** | Same category | Same category (Cannaleo medicines assigned via `category_cannaleo_medicine`) |
| **Questionnaire** | Same questionnaire | Same questionnaire |
| **After submit** | Delivery \| Pickup | Delivery \| Pickup \| **Cannaleo partner** |
| **Next step** | Address or internal pharmacy | **Cannaleo pharmacy selection** |
| **Then** | Medicine selection (internal) | **Cannaleo medicine selection** (from selected pharmacy + category) |
| **Store** | `selected_pharmacy_id`, `selected_medicines` with `medicine_id` | `selected_cannaleo_pharmacy_id`, `selected_medicines` with `cannaleo_medicine_id` |
| **Doctor review** | Show internal medicine | Show Cannaleo medicine + pharmacy name; same acceptance flow |

---

## 6. Admin implementation checklist

- [ ] **Cannaleo Pharmacies** – List view (read-only), from sync.
- [ ] **Cannaleo Medicines** – List view with pharmacy, API category, price, THC/CBD; show assigned app categories.
- [ ] **Category edit** – Section to assign Cannaleo medicines to the category (multi-select); optionally show “Cannaleo pharmacies that will appear” (derived).
- [ ] Sidebar: add “Cannaleo Pharmacies” and “Cannaleo Medicines” under a “Cannaleo” group (or under Category/Medicine area).

---

## 7. Customer flow implementation checklist

- [ ] Migration: `selected_cannaleo_pharmacy_id`, extend `delivery_type` to `cannaleo`.
- [ ] Delivery choice: if category has Cannaleo medicines, show third option “Cannaleo partner”; set `delivery_type = 'cannaleo'`.
- [ ] Cannaleo pharmacy selection: new route + view; save `selected_cannaleo_pharmacy_id`; redirect to Cannaleo medicine selection.
- [ ] Cannaleo medicine selection: show Cannaleo medicines for category + selected pharmacy; save `selected_medicines` with `cannaleo_medicine_id`; max 3 total (or per-type rules); redirect to success.
- [ ] Doctor review: resolve and display both internal and Cannaleo selections (Cannaleo: name, pharmacy, price, THC/CBD).

This keeps one flow (category → questionnaire → source → pharmacy if needed → medicine → doctor), with Cannaleo as an additional source tied to the same category and questionnaire.
