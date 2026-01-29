
---

## 🗄️ Database Structure

### New Tables to Create

#### 1. api_providers
```sql
CREATE TABLE api_providers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Cannaleo, Pharmacy ABC',
    type ENUM('supplier', 'pharmacy') DEFAULT 'supplier',
    api_base_url VARCHAR(500) NOT NULL,
    api_key TEXT NOT NULL COMMENT 'Encrypted JWT token',
    api_version VARCHAR(50) DEFAULT 'v1',
    status ENUM('active', 'inactive') DEFAULT 'active',
    sync_frequency INT DEFAULT 60 COMMENT 'Minutes',
    last_sync_at TIMESTAMP NULL,
    next_sync_at TIMESTAMP NULL,
    config JSON COMMENT 'Provider settings',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_next_sync (next_sync_at, status)
);
```

#### 2. external_medicines
```sql
CREATE TABLE external_medicines (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    api_provider_id BIGINT UNSIGNED NOT NULL,
    external_id VARCHAR(255) NOT NULL COMMENT 'Cannaleo product ID',
    external_sku VARCHAR(255) NULL COMMENT 'ansayId',
    name VARCHAR(500) NOT NULL,
    strength VARCHAR(100) NULL COMMENT '20% THC / 0.5% CBD',
    form VARCHAR(100) NULL COMMENT 'flower, oil, capsule',
    category VARCHAR(100) NULL,
    price DECIMAL(10, 2) DEFAULT 0.00,
    description TEXT NULL,
    
    -- Cannabis-specific fields (optional but recommended)
    thc DECIMAL(5, 2) NULL COMMENT 'THC percentage',
    cbd DECIMAL(5, 2) NULL COMMENT 'CBD percentage',
    genetic VARCHAR(50) NULL COMMENT 'Indica/Sativa/Hybrid',
    strain VARCHAR(255) NULL,
    
    raw_data JSON COMMENT 'Complete API response',
    medicine_id BIGINT UNSIGNED NULL COMMENT 'Mapped internal medicine',
    mapping_status ENUM('unmapped', 'auto_mapped', 'manual_mapped', 'review_needed') DEFAULT 'unmapped',
    mapping_confidence DECIMAL(5, 2) DEFAULT 0.00,
    last_synced_at TIMESTAMP NULL,
    is_available BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (api_provider_id) REFERENCES api_providers(id) ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicine(id) ON DELETE SET NULL,
    UNIQUE KEY unique_provider_product (api_provider_id, external_id),
    INDEX idx_mapping (mapping_status, medicine_id),
    INDEX idx_available (is_available, is_active),
    INDEX idx_thc_cbd (thc, cbd)
);
```

#### 3. medicine_mappings
```sql
CREATE TABLE medicine_mappings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    medicine_id BIGINT UNSIGNED NOT NULL,
    external_medicine_id BIGINT UNSIGNED NOT NULL,
    api_provider_id BIGINT UNSIGNED NOT NULL,
    confidence_score DECIMAL(5, 2) DEFAULT 0.00,
    mapped_by BIGINT UNSIGNED NULL COMMENT 'User ID, NULL = system',
    mapping_type ENUM('auto', 'manual') DEFAULT 'manual',
    match_criteria JSON COMMENT 'How match was determined',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (medicine_id) REFERENCES medicine(id) ON DELETE CASCADE,
    FOREIGN KEY (external_medicine_id) REFERENCES external_medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (api_provider_id) REFERENCES api_providers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_mapping (medicine_id, external_medicine_id)
);
```

#### 4. sync_logs
```sql
CREATE TABLE sync_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    api_provider_id BIGINT UNSIGNED NOT NULL,
    status ENUM('started', 'completed', 'failed') DEFAULT 'started',
    products_fetched INT DEFAULT 0,
    products_created INT DEFAULT 0,
    products_updated INT DEFAULT 0,
    products_mapped INT DEFAULT 0,
    errors_count INT DEFAULT 0,
    error_details JSON NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    duration_seconds INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (api_provider_id) REFERENCES api_providers(id) ON DELETE CASCADE,
    INDEX idx_provider_status (api_provider_id, status)
);
```

### Update Existing Tables

```sql
-- medicine_brands
ALTER TABLE medicine_brands
ADD COLUMN api_provider_id BIGINT UNSIGNED NULL,
ADD COLUMN is_external BOOLEAN DEFAULT FALSE,
ADD CONSTRAINT fk_brand_provider 
    FOREIGN KEY (api_provider_id) REFERENCES api_providers(id) ON DELETE SET NULL;

-- pharmacy_inventory
ALTER TABLE pharmacy_inventory
ADD COLUMN external_medicine_id BIGINT UNSIGNED NULL,
ADD COLUMN sync_with_external BOOLEAN DEFAULT FALSE,
ADD CONSTRAINT fk_inventory_external 
    FOREIGN KEY (external_medicine_id) REFERENCES external_medicines(id) ON DELETE SET NULL;
```

---

## 💻 Code Implementation

### Step 1: Environment Configuration

**.env:**
```env
CANNALEO_API_URL=https://api.cannaleo.com
CANNALEO_API_KEY=YOUR_JWT_TOKEN_HERE
CANNALEO_API_VERSION=v1
CANNALEO_SYNC_ENABLED=true
CANNALEO_SYNC_FREQUENCY=60
```

### Step 2: API Service

**app/Services/ExternalAPI/Cannaleo/CannaleoApiService.php:**
```php
<?php

namespace App\Services\ExternalAPI\Cannaleo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CannaleoApiService
{
    protected $baseUrl;
    protected $apiKey;
    protected $version;
    protected $timeout = 30;
    
    public function __construct()
    {
        $this->baseUrl = config('external_api.cannaleo.api_url');
        $this->apiKey = config('external_api.cannaleo.api_key');
        $this->version = config('external_api.cannaleo.api_version', 'v1');
    }
    
    /**
     * Fetch complete catalog
     */
    public function fetchCatalog(): array
    {
        try {
            $url = "{$this->baseUrl}/api/{$this->version}/catalog";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->retry(3, 1000)
            ->get($url);
            
            if (!$response->successful()) {
                throw new \Exception("API request failed: " . $response->body());
            }
            
            $data = $response->json();
            
            // Check for API error
            if ($data['error'] ?? false) {
                throw new \Exception("API error: " . ($data['message'] ?? 'Unknown'));
            }
            
            $catalog = $data['data']['catalog'] ?? [];
            $updatedAt = $data['data']['updated_at'] ?? null;
            
            // Cache updated timestamp
            if ($updatedAt) {
                Cache::put('cannaleo_last_updated', $updatedAt, now()->addDays(7));
            }
            
            Log::info('Cannaleo catalog fetched', [
                'products_count' => count($catalog),
                'updated_at' => $updatedAt
            ]);
            
            return $catalog;
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch Cannaleo catalog', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Transform catalog product to standard format
     */
    public function transformProduct(array $product): array
    {
        return [
            'external_id' => $product['id'] ?? null,
            'external_sku' => $product['ansayId'] ?? null,
            'name' => $product['name'] ?? '',
            'strength' => $this->buildStrength($product),
            'form' => $product['category'] ?? null,
            'category' => $product['category'] ?? null,
            'price' => (float) ($product['price'] ?? 0),
            'thc' => $product['thc'] ?? null,
            'cbd' => $product['cbd'] ?? null,
            'genetic' => $product['genetic'] ?? null,
            'strain' => $product['strain'] ?? null,
            'description' => $this->buildDescription($product),
            'raw_data' => $product,
            'is_available' => $this->checkAvailability($product),
        ];
    }
    
    private function buildStrength(array $product): ?string
    {
        $thc = $product['thc'] ?? 0;
        $cbd = $product['cbd'] ?? 0;
        
        if ($thc > 0 && $cbd > 0) {
            return "{$thc}% THC / {$cbd}% CBD";
        } elseif ($thc > 0) {
            return "{$thc}% THC";
        } elseif ($cbd > 0) {
            return "{$cbd}% CBD";
        }
        
        return null;
    }
    
    private function buildDescription(array $product): string
    {
        $parts = [];
        
        if ($product['genetic'] ?? null) {
            $parts[] = "Genetic: {$product['genetic']}";
        }
        if ($product['strain'] ?? null) {
            $parts[] = "Strain: {$product['strain']}";
        }
        if ($product['dominance'] ?? null) {
            $parts[] = "Dominance: {$product['dominance']}";
        }
        if ($product['country'] ?? null) {
            $parts[] = "Origin: {$product['country']}";
        }
        if ($product['manufacturer'] ?? null) {
            $parts[] = "Manufacturer: {$product['manufacturer']}";
        }
        
        return implode(" | ", $parts);
    }
    
    private function checkAvailability(array $product): bool
    {
        // Handle API typo
        $availability = $product['availibility'] 
                     ?? $product['availability'] 
                     ?? 'unavailable';
        
        return in_array(strtolower($availability), [
            'available', 
            'in_stock', 
            'in stock'
        ]);
    }
    
    /**
     * Test API connection
     */
    public function testConnection(): bool
    {
        try {
            $this->fetchCatalog();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
```

### Step 3: Sync Job

**app/Jobs/SyncCannaleoProducts.php:**
```php
<?php

namespace App\Jobs;

use App\Models\ApiProvider;
use App\Models\ExternalMedicine;
use App\Models\SyncLog;
use App\Services\ExternalAPI\Cannaleo\CannaleoApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncCannaleoProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;
    public $tries = 3;
    public $backoff = [60, 300, 900];

    public function handle()
    {
        $provider = ApiProvider::where('name', 'Cannaleo')->first();
        
        if (!$provider) {
            Log::warning('Cannaleo provider not found');
            return;
        }
        
        $syncLog = SyncLog::create([
            'api_provider_id' => $provider->id,
            'status' => 'started',
            'started_at' => now()
        ]);
        
        try {
            $apiService = new CannaleoApiService();
            $catalog = $apiService->fetchCatalog();
            
            $syncLog->update(['products_fetched' => count($catalog)]);
            
            $stats = [
                'created' => 0,
                'updated' => 0,
                'errors' => 0
            ];
            
            foreach ($catalog as $product) {
                try {
                    $transformed = $apiService->transformProduct($product);
                    
                    $external = ExternalMedicine::updateOrCreate(
                        [
                            'api_provider_id' => $provider->id,
                            'external_id' => $transformed['external_id']
                        ],
                        [
                            'external_sku' => $transformed['external_sku'],
                            'name' => $transformed['name'],
                            'strength' => $transformed['strength'],
                            'form' => $transformed['form'],
                            'category' => $transformed['category'],
                            'price' => $transformed['price'],
                            'thc' => $transformed['thc'],
                            'cbd' => $transformed['cbd'],
                            'genetic' => $transformed['genetic'],
                            'strain' => $transformed['strain'],
                            'description' => $transformed['description'],
                            'raw_data' => $transformed['raw_data'],
                            'is_available' => $transformed['is_available'],
                            'last_synced_at' => now()
                        ]
                    );
                    
                    $stats[$external->wasRecentlyCreated ? 'created' : 'updated']++;
                    
                } catch (\Exception $e) {
                    $stats['errors']++;
                    Log::error('Failed to process product', [
                        'product_id' => $product['id'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $syncLog->update([
                'status' => 'completed',
                'products_created' => $stats['created'],
                'products_updated' => $stats['updated'],
                'errors_count' => $stats['errors'],
                'completed_at' => now(),
                'duration_seconds' => now()->diffInSeconds($syncLog->started_at)
            ]);
            
            $provider->update([
                'last_sync_at' => now(),
                'next_sync_at' => now()->addMinutes($provider->sync_frequency)
            ]);
            
            Log::info('Cannaleo sync completed', $stats);
            
        } catch (\Exception $e) {
            $syncLog->update([
                'status' => 'failed',
                'error_details' => ['message' => $e->getMessage()],
                'completed_at' => now()
            ]);
            
            throw $e;
        }
    }
}
```

### Step 4: Artisan Command

**app/Console/Commands/SyncCannaleoCommand.php:**
```php
<?php

namespace App\Console\Commands;

use App\Jobs\SyncCannaleoProducts;
use Illuminate\Console\Command;

class SyncCannaleoCommand extends Command
{
    protected $signature = 'cannaleo:sync {--force}';
    protected $description = 'Sync products from Cannaleo API';

    public function handle()
    {
        $this->info('Starting Cannaleo sync...');
        
        SyncCannaleoProducts::dispatch();
        
        $this->info('Sync job dispatched to queue');
        
        return 0;
    }
}
```

### Step 5: Schedule

**app/Console/Kernel.php:**
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('cannaleo:sync')
             ->hourly()
             ->withoutOverlapping()
             ->runInBackground();
}
```

---

## 🧪 Testing & Deployment

### Testing Checklist

**Before Production:**

- [ ] Test API connection with real credentials
- [ ] Verify all products sync correctly
- [ ] Check data accuracy (THC, CBD, prices)
- [ ] Test with large catalog (performance)
- [ ] Test error scenarios (API down, timeout)
- [ ] Verify mapping logic
- [ ] Test sync job in queue
- [ ] Check scheduled task runs
- [ ] Review logs for errors
- [ ] Test admin interface

**Test Command:**
```bash
# Test API connection
php artisan tinker
>>> $service = new \App\Services\ExternalAPI\Cannaleo\CannaleoApiService();
>>> $catalog = $service->fetchCatalog();
>>> count($catalog)

# Run sync manually
php artisan cannaleo:sync --force

# Check logs
tail -f storage/logs/laravel.log

# Check database
php artisan tinker
>>> \App\Models\ExternalMedicine::count()
>>> \App\Models\SyncLog::latest()->first()
```

---

## 🚧 Obstacles & Risks

### High Risk Obstacles

| Obstacle | Risk Level | Impact | Mitigation |
|----------|------------|--------|------------|
| **No JWT token documentation** | 🔴 HIGH | Can't authenticate | Ask client for auth details |
| **Token expiration unknown** | 🔴 HIGH | Sync failures | Implement token refresh logic |
| **No test environment** | 🟡 MEDIUM | Test on production | Request sandbox access |
| **Unknown rate limits** | 🟡 MEDIUM | API blocking | Implement rate limiting |
| **Large catalog (>1000 items)** | 🟡 MEDIUM | Performance issues | Use chunking & caching |
| **Mapping accuracy** | 🟡 MEDIUM | Wrong products | Manual review first 100 |
| **API changes without notice** | 🟡 MEDIUM | Integration breaks | Version API responses |
| **Client expectations unclear** | 🟢 LOW | Rework needed | Clarify requirements |

### Technical Risks

1. **Database Performance**
   - Large JSON fields in raw_data
   - Solution: Index key fields separately

2. **Queue Worker Failure**
   - Job stuck or failed
   - Solution: Monitor queue, setup alerts

3. **Memory Issues**
   - Processing 10,000+ products
   - Solution: Chunk processing, increase PHP memory

4. **Timezone Issues**
   - updated_at timestamp parsing
   - Solution: Always use UTC, convert display

---

## 📝 Action Items Summary

### For You (Developer)

**Immediate:**
1. ✅ Request API credentials from client (use template above)
2. ✅ Create database migrations
3. ✅ Set up development environment
4. ✅ Create models (ApiProvider, ExternalMedicine, etc.)

**After Receiving Credentials:**
5. ✅ Test API connection
6. ✅ Implement CannaleoApiService
7. ✅ Create sync job
8. ✅ Test full sync flow
9. ✅ Build admin interface
10. ✅ Deploy to staging

### For Client

**Must Provide:**
1. 🔴 JWT Token for API authentication
2. 🔴 API Base URL
3. 🔴 Token refresh mechanism (if applicable)
4. 🔴 Telemedicine ID
5. 🟡 Rate limit information
6. 🟡 Support contact for API issues
7. 🟡 Test/sandbox environment (if available)
8. 🟢 Business requirements clarification

**Must Decide:**
1. Sync frequency (hourly recommended)
2. Which pharmacies to include
3. Auto-mapping vs manual review preference
4. Out-of-stock product handling
5. Customer-facing THC/CBD display

---

## 📞 Next Steps

### Step 1: Send Client Request
Use the email template in "What Client Must Provide" section

### Step 2: While Waiting
- Set up database structure
- Create model classes
- Build service skeleton
- Prepare admin interface

### Step 3: Upon Receiving Credentials
- Test API immediately
- Verify catalog structure
- Run first sync
- Review data quality

### Step 4: Development
- Follow implementation plan (Days 1-12)
- Regular testing
- Document any issues

### Step 5: Deployment
- Staging first
- Monitor closely
- Production deployment
- Hand off to client

---

## 🎯 Success Criteria

**Integration is successful when:**

✅ Hourly sync runs automatically  
✅ All products sync correctly  
✅ Data accuracy >95%  
✅ Sync completes in <5 minutes  
✅ Failed syncs retry automatically  
✅ Admin can view sync status  
✅ Admin can manually trigger sync  
✅ Products map to internal medicines  
✅ Zero data loss  
✅ Logs are comprehensive  

---

**Document End**

This is your complete guide. Start with getting credentials from client, then follow the implementation plan step by step.
