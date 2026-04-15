<?php

namespace App\Console\Commands;

use App\Services\Cannaleo\CannaleoCatalogSync;
use Illuminate\Console\Command;

class CannaleoCatalogSyncCommand extends Command
{
    protected $signature = 'cannaleo:catalog-sync';

    protected $description = 'Sync Cannaleo catalog (pharmacies and medicines) from cannaleo.de API. Run every 15 minutes via scheduler.';

    public function handle(): int
    {
        if (! config('cannaleo.catalog_sync_enabled', true)) {
            $this->warn('Cannaleo catalog sync is disabled (CUROBO_CATALOG_SYNC_ENABLED=false).');
            return 0;
        }

        if (empty(config('cannaleo.curobo_api_key'))) {
            $this->error('CUROBO_CATALOG_API_KEY is not set in .env');
            return 1;
        }

        try {
            $sync = new CannaleoCatalogSync();
            $stats = $sync->sync();

            $this->info('Cannaleo catalog sync completed.');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Pharmacies fetched (API)', $stats['pharmacies_fetched']],
                    ['Catalog items fetched', $stats['items_fetched']],
                    ['Pharmacies created', $stats['pharmacies_created']],
                    ['Pharmacies updated', $stats['pharmacies_updated']],
                    ['Medicines created', $stats['medicines_created']],
                    ['Medicines updated', $stats['medicines_updated']],
                ]
            );
            return 0;
        } catch (\Throwable $e) {
            $this->error('Cannaleo catalog sync failed: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Cannaleo catalog sync failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }
    }
}
