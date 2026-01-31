# 🌿 Cannaleo API Integration - Complete Implementation Guide

**Document Purpose:** Complete guide to integrate Cannaleo API for hourly medicine sync  
**Version:** 1.0  
**Date:** January 27, 2026  
**Status:** Ready for Implementation

---

## 📋 Table of Contents

1. [What We Have](#what-we-have)
2. [What Client Must Provide](#what-client-must-provide)
3. [Implementation Plan](#implementation-plan)
4. [Potential Problems & Solutions](#potential-problems--solutions)
5. [Database Structure](#database-structure)
6. [Code Implementation](#code-implementation)
7. [Testing & Deployment](#testing--deployment)
8. [Obstacles & Risks](#obstacles--risks)

---

## 🎯 What We Have

### ✅ Confirmed API Endpoint

**Endpoint:** `GET /api/v1/catalog`

**Response Structure:**
```json
{
  "error": false,
  "message": "Success",
  "data": {
    "catalog": [
      {
        "id": "CNL-001",
        "ansayId": "ANSAY-12345",
        "name": "Northern Lights 20% THC",
        "genetic": "Indica",
        "country": "Germany",
        "thc": 20,
        "cbd": 0.5,
        "irradiated": 0,
        "strain": "Northern Lights",
        "price": 25.00,
        "pharmacy_name": "Cannaleo Pharmacy",
        "pharmacy_domain": "cannaleo.com",
        "pharmacy_id": 1,
        "availibility": "available",
        "category": "flower",
        "manufacturer": "ABC Pharma",
        "grower": "Premium Growers",
        "dominance": "Indica Dominant",
        "terpenes": ["Myrcene", "Limonene"]
      }
    ],
    "updated_at": "2026-01-27T10:30:00Z"
  }
}
```

### 📊 What This Gives Us

1. **Complete Product Catalog** - All available medicines in one call
2. **Rich Product Data** - THC, CBD, genetics, terpenes, manufacturer
3. **Pharmacy Info** - Which pharmacy supplies each product
4. **Availability Status** - Real-time stock information
5. **Change Tracking** - `updated_at` timestamp for efficient sync

---

## ❗ What Client Must Provide

### 🔴 CRITICAL - Required Before Starting

#### 1. **Cannaleo API Credentials**

**What Exactly You Need:**

| Item | Description | Format | Example |
|------|-------------|--------|---------|
| **API Base URL** | Cannaleo API endpoint | URL | `https://api.cannaleo.com` |
| **JWT Token** | Authentication token | JWT string | `eyJhbGciOiJIUzI1NiIs...` |
| **API Version** | Version to use | String | `v1` |
| **Telemedicine ID** | Your unique ID (from JWT) | String | `TELEMED-12345` |

**⚠️ CRITICAL ISSUE:** The prescription document doesn't mention how to get the JWT token for catalog access!

**What to Ask Client:**

```
Subject: Cannaleo API Credentials Required

Dear Client,

To integrate the Cannaleo catalog API, we need the following:

1. JWT Token for authentication
   - How do we obtain this token?
   - Is it a static token or does it expire?
   - If it expires, what's the refresh mechanism?

2. API Access Details
   - What's the exact base URL? (e.g., https://api.cannaleo.com)
   - Are there any rate limits?
   - What's our API quota?

3. Telemedicine ID
   - What's your Cannaleo telemedicine ID?
   - Is this embedded in the JWT or separate?

4. Documentation Access
   - Do you have complete API documentation?
   - Are there any endpoints beyond /catalog and /prescription?
   - Is there a sandbox/test environment?

5. Support Contact
   - Who do we contact for API issues?
   - Is there a developer portal?

Please provide these details to proceed with integration.
```

#### 2. **Business Requirements Clarification**

**Questions for Client:**

| Question | Why Important | Impact |
|----------|---------------|--------|
| How often should we sync? | Determines server load | Hourly vs every 15 min |
| Which pharmacies to include? | Some products have pharmacy_id | Data filtering |
| Auto-map products or manual review? | Accuracy vs speed | User workflow |
| What happens to out-of-stock items? | Inventory management | Customer experience |
| Should we show THC/CBD to customers? | Legal/compliance | UI design |
| Multi-pharmacy support priority? | Database design | Architecture decisions |

#### 3. **Testing Environment**

**Required from Client:**

- [ ] Test API credentials (if available)
- [ ] Sample products for testing
- [ ] Expected behavior documentation
- [ ] Contact person for questions

---

**Tasks:**
1. ✅ Obtain API credentials from client
2. ✅ Test API connection manually
3. ✅ Verify response structure
4. ✅ Document any differences

**Manual Test:**
```bash
curl -X GET "https://api.cannaleo.com/api/v1/catalog" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

**Validation Checklist:**
- [ ] API returns 200 status
- [ ] Response has "error", "message", "data" fields
- [ ] Catalog array has products
- [ ] All expected fields present
- [ ] JWT token works

#### Day 2-3: Database Structure

**Create 4 New Tables:**

1. **api_providers** - Store API provider info (Cannaleo, others)
2. **external_medicines** - Store products from external APIs
3. **medicine_mappings** - Map external to internal medicines
4. **sync_logs** - Track all sync operations

**Also Update:**
- `medicine_brands` - Link to API providers
- `medicine` - Track source type
- `pharmacy_inventory` - Link to external medicines

### Phase 2: Core Development (Days 4-10)

#### Day 4-5: Service Layer

**Create Services:**
- `CannaleoApiService` - Handle API communication
- `MedicineSyncService` - Orchestrate sync process
- `MedicineMappingService` - Auto-map products

#### Day 6-7: Sync Job & Command

**Create:**
- `SyncExternalMedicines` Job - Queue job for async processing
- `SyncExternalMedicinesCommand` - Artisan command for manual sync
- Scheduler configuration - Hourly automatic sync

#### Day 8-9: Admin Interface

**Create:**
- API Provider management (add/edit Cannaleo)
- External medicine viewer
- Mapping interface (manual mapping)
- Sync dashboard (logs, status)

#### Day 10: Testing

**Test:**
- Full sync flow
- Error handling
- Data accuracy
- Performance

### Phase 3: Deployment (Days 11-12)

#### Day 11: Staging Deployment

- Deploy to staging
- Run test sync
- Verify data
- Check logs

#### Day 12: Production Deployment

- Deploy to production
- Monitor first sync
- Document any issues
- Hand off to client

---

## ⚠️ Potential Problems & Solutions

### Problem 1: JWT Token Management

**Issue:** Document doesn't explain token lifecycle

**Possible Scenarios:**

**Scenario A: Static Token**
```
Token never expires, always use same one
✅ Solution: Store encrypted in database
✅ No refresh logic needed
```

**Scenario B: Expiring Token**
```
Token expires after X hours/days
⚠️ Solution: Need refresh mechanism
⚠️ Must ask client: How to refresh?
```

**Scenario C: OAuth Flow**
```
Need to authenticate to get token
⚠️ Solution: Implement OAuth client
⚠️ Must ask client: OAuth credentials?
```

### Problem 2: Price Format Confusion

**Issue:** Catalog uses EUR, Prescription uses cents

**In Catalog Response:**
```json
"price": 25.00  // This is €25.00 (NOT cents)
```

**In Prescription Request:**
```json
"totalGross": 2500  // This IS in cents (€25.00)
```

**Solution:**
```php
// When fetching from catalog (NO conversion)
$price = (float) $product['price']; // 25.00

// When creating prescription (convert to cents)
$priceInCents = (int) ($price * 100); // 2500
```

### Problem 3: API Typo - "availibility"

**Issue:** API has spelling error

**Current API Field:**
```json
"availibility": "available"  // Wrong spelling
```

**Solution:** Handle both spellings
```php
private function checkAvailability(array $product): bool
{
    // Handle both correct and incorrect spelling
    $availability = $product['availibility'] 
                 ?? $product['availability'] 
                 ?? 'unavailable';
    
    return in_array(strtolower($availability), [
        'available', 
        'in_stock', 
        'in stock'
    ]);
}
```

### Problem 4: Multiple Pharmacies in Catalog

**Issue:** Products have different pharmacy_id values

**Question:** Should we:
- A) Import all products from all pharmacies?
- B) Filter by specific pharmacy_id?
- C) Let client choose which pharmacies?

**Solution:**
```php
// Add filtering option
public function fetchProducts(array $filters = []): array
{
    $response = $this->makeRequest('GET', '/catalog', $filters);
    $catalog = $response['data']['catalog'] ?? [];
    
    // Filter by pharmacy_id if specified
    if (isset($filters['pharmacy_id'])) {
        $catalog = array_filter($catalog, function($product) use ($filters) {
            return $product['pharmacy_id'] == $filters['pharmacy_id'];
        });
    }
    
    return $catalog;
}
```

**Ask Client:** Which pharmacies should we import?

### Problem 5: Mapping Accuracy

**Issue:** Auto-mapping might create wrong matches

**Example:**
- External: "Northern Lights 20% THC Flower"
- Internal: "Northern Lights" (no THC info)
- Match Score: 70% - Too low for auto-map

**Solution: Confidence Thresholds**
```php
public function autoMap(ExternalMedicine $external): bool
{
    $result = $this->findBestMatch($external);
    
    // Only auto-map if very confident
    if ($result['confidence'] >= 90) {
        return $this->createMapping($external, $result['medicine'], 'auto');
    }
    
    // Moderate confidence - flag for review
    if ($result['confidence'] >= 70) {
        $external->update([
            'mapping_status' => 'review_needed',
            'mapping_confidence' => $result['confidence']
        ]);
    }
    
    return false;
}
```

**Recommendation:** Manual review for first 50-100 products

### Problem 6: Large Catalog Performance

**Issue:** What if catalog has 10,000+ products?

**Solutions:**

**A) Chunking**
```php
collect($catalog)->chunk(100)->each(function($chunk) {
    foreach ($chunk as $product) {
        $this->processProduct($product);
    }
});
```

**B) Queue Dispatch**
```php
foreach ($catalog as $product) {
    ProcessSingleProduct::dispatch($product);
}
```

**C) Incremental Sync**
```php
$lastUpdated = $provider->last_sync_at;
$currentUpdated = $response['data']['updated_at'];

// Only process if catalog changed
if ($lastUpdated == $currentUpdated) {
    return; // Skip processing
}
```

### Problem 7: API Rate Limiting

**Issue:** Don't know Cannaleo's rate limits

**Questions for Client:**
- How many requests per minute/hour?
- What happens when limit exceeded?
- How long until limit resets?

**Preventive Solution:**
```php
use Illuminate\Support\Facades\RateLimiter;

public function fetchProducts(): array
{
    $key = 'cannaleo-api';
    
    if (RateLimiter::tooManyAttempts($key, 60)) {
        $seconds = RateLimiter::availableIn($key);
        throw new RateLimitException("Rate limit exceeded. Try in {$seconds}s");
    }
    
    RateLimiter::hit($key, 60); // 60 seconds
    
    return $this->makeApiCall();
}
```

### Problem 8: Sync Failures

**Issue:** What if API is down during hourly sync?

**Solution: Retry Logic**
```php
class SyncExternalMedicines implements ShouldQueue
{
    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min
    
    public function handle()
    {
        try {
            $this->syncService->syncProvider($this->provider);
        } catch (ApiConnectionException $e) {
            // Will retry automatically
            throw $e;
        }
    }
    
    public function failed(\Throwable $exception)
    {
        // After 3 tries, notify admin
        \Log::critical('Cannaleo sync failed after retries', [
            'error' => $exception->getMessage()
        ]);
        
        // Send notification
        Notification::send(
            User::superAdmins()->get(), 
            new SyncFailedNotification($this->provider, $exception)
        );
    }
}
```
