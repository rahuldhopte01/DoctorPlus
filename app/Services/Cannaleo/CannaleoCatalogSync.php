<?php

namespace App\Services\Cannaleo;

use App\Models\CannaleoMedicine;
use App\Models\CannaleoPharmacy;
use App\Models\CannaleoSyncLog;
use App\Services\Curobo\CuroboCatalogApi;
use Illuminate\Support\Carbon;

class CannaleoCatalogSync
{
    protected CuroboCatalogApi $api;

    protected ?CannaleoSyncLog $log = null;

    protected bool $writeLog = true;

    public function __construct(?CuroboCatalogApi $api = null)
    {
        $this->api = $api ?? new CuroboCatalogApi();
    }

    public function setWriteLog(bool $write): self
    {
        $this->writeLog = $write;
        return $this;
    }

    /**
     * Run full sync: fetch pharmacies API first, create/update pharmacies; then fetch catalog
     * and save only medicines whose pharmacy_id exists in the synced pharmacies.
     */
    public function sync(): array
    {
        $startedAt = Carbon::now();
        $stats = [
            'pharmacies_fetched' => 0,
            'items_fetched' => 0,
            'pharmacies_created' => 0,
            'pharmacies_updated' => 0,
            'medicines_created' => 0,
            'medicines_updated' => 0,
        ];

        if ($this->writeLog && class_exists(CannaleoSyncLog::class)) {
            $this->log = CannaleoSyncLog::create([
                'started_at' => $startedAt,
                'status' => 'started',
            ]);
        }

        try {
            // 1. Fetch pharmacies from GET /api/v1/pharmacies/ and create/update
            $pharmaciesFromApi = $this->api->getPharmacies();
            $stats['pharmacies_fetched'] = count($pharmaciesFromApi);
            $pharmaciesByExternalId = $this->syncPharmaciesFromApi($pharmaciesFromApi, $stats);

            // 2. Fetch catalog and save only medicines for pharmacies we just synced
            $items = $this->api->getCatalog();
            $stats['items_fetched'] = count($items);
            $this->syncMedicines($items, $pharmaciesByExternalId, $stats);
            unset($items);

            if ($this->log) {
                $this->log->update([
                    'completed_at' => Carbon::now(),
                    'status' => 'completed',
                    'items_fetched' => $stats['items_fetched'],
                    'pharmacies_created' => $stats['pharmacies_created'],
                    'pharmacies_updated' => $stats['pharmacies_updated'],
                    'medicines_created' => $stats['medicines_created'],
                    'medicines_updated' => $stats['medicines_updated'],
                ]);
            }

            return $stats;
        } catch (\Throwable $e) {
            if ($this->log) {
                $this->log->update([
                    'completed_at' => Carbon::now(),
                    'status' => 'failed',
                    'items_fetched' => $stats['items_fetched'],
                    'pharmacies_created' => $stats['pharmacies_created'],
                    'pharmacies_updated' => $stats['pharmacies_updated'],
                    'medicines_created' => $stats['medicines_created'],
                    'medicines_updated' => $stats['medicines_updated'],
                    'error_message' => $e->getMessage(),
                ]);
            }
            throw $e;
        }
    }

    /**
     * Create/update Cannaleo pharmacies from GET /api/v1/pharmacies/ response.
     * Returns map external_id (pharmacy id) => CannaleoPharmacy for use when syncing medicines.
     *
     * @param array<int, array<string, mixed>> $pharmaciesFromApi
     * @param array<string, int> $stats
     * @return array<string|int, CannaleoPharmacy>
     */
    protected function syncPharmaciesFromApi(array $pharmaciesFromApi, array &$stats): array
    {
        $now = Carbon::now();
        $result = [];

        foreach ($pharmaciesFromApi as $p) {
            $externalId = (string) ($p['id'] ?? '');
            if ($externalId === '') {
                continue;
            }

            $name = $p['cannabis_pharmacy_name'] ?? $p['official_name'] ?? '';
            $domain = $p['domain'] ?? null;

            // Contact
            $email = $p['email'] ?? null;
            $phoneNumber = $p['phone_number'] ?? null;

            // Address
            $street = $p['street'] ?? null;
            $plz = $p['plz'] ?? null;
            $city = $p['city'] ?? null;

            // Shipping
            $shipping = $p['shipping'] ?? null;
            $shippingCostStandard = isset($p['shipping_cost_standard']) ? (float) $p['shipping_cost_standard'] : null;
            $shippingCostReduced = $this->normalizeCostReduced($p['shipping_cost_reduced'] ?? null);

            // Express
            $express = $p['express'] ?? null;
            $expressCostStandard = isset($p['express_cost_standard']) ? (float) $p['express_cost_standard'] : null;
            $expressCostReduced = $this->normalizeCostReduced($p['express_cost_reduced'] ?? null);

            // Local courier (API typo: local_coure_*)
            $localCourier = $p['local_courier'] ?? null;
            $localCourierCostStandard = isset($p['local_coure_cost_standard']) ? (float) $p['local_coure_cost_standard'] : null;
            $localCourierCostReduced = $this->normalizeCostReduced($p['local_coure_cost_reduced'] ?? null);

            // Pickup
            $pickup = $p['pickup'] ?? null;
            $pickupBranches = isset($p['pickup_branches']) && is_array($p['pickup_branches']) ? $p['pickup_branches'] : null;

            $pharmacy = CannaleoPharmacy::updateOrCreate(
                ['external_id' => $externalId],
                [
                    'name' => $name,
                    'domain' => $domain,
                    'email' => $email,
                    'phone_number' => $phoneNumber,
                    'street' => $street,
                    'plz' => $plz,
                    'city' => $city,
                    'shipping' => $shipping,
                    'shipping_cost_standard' => $shippingCostStandard,
                    'shipping_cost_reduced' => $shippingCostReduced,
                    'express' => $express,
                    'express_cost_standard' => $expressCostStandard,
                    'express_cost_reduced' => $expressCostReduced,
                    'local_courier' => $localCourier,
                    'local_courier_cost_standard' => $localCourierCostStandard,
                    'local_courier_cost_reduced' => $localCourierCostReduced,
                    'pickup' => $pickup,
                    'pickup_branches' => $pickupBranches,
                    'last_synced_at' => $now,
                ]
            );
            $result[$externalId] = $pharmacy;
            if ($pharmacy->wasRecentlyCreated) {
                $stats['pharmacies_created']++;
            } else {
                $stats['pharmacies_updated']++;
            }
        }

        return $result;
    }

    /**
     * Normalize cost_reduced object from API for JSON storage.
     * API shape: { "type": "TOTALCOSTS", "amount": 0, "price": 0, "rule": "string" }
     *
     * @param mixed $value
     * @return array<string, mixed>|null
     */
    protected function normalizeCostReduced($value): ?array
    {
        if (! is_array($value)) {
            return null;
        }
        return $value;
    }

    /**
     * Upsert cannaleo_medicine for each catalog item.
     * Processes in chunks to avoid exhausting PHP memory on large catalogs.
     *
     * @param array<int, array<string, mixed>> $items  (passed by reference so spliced items are freed)
     * @param array<string|int, CannaleoPharmacy> $pharmaciesByExternalId
     * @param array<string, int> $stats
     */
    protected function syncMedicines(array &$items, array $pharmaciesByExternalId, array &$stats): void
    {
        $now = Carbon::now();

        // Process in chunks of 200; array_splice removes each chunk from $items as we go,
        // so the bulk of the catalog array is freed from memory progressively.
        while ($chunk = array_splice($items, 0, 200)) {
            foreach ($chunk as $item) {
                $pharmacyId = $item['pharmacy_id'] ?? null;
                if ($pharmacyId === null) {
                    continue;
                }
                $pharmacy = $pharmaciesByExternalId[(string) $pharmacyId] ?? null;
                if (! $pharmacy) {
                    continue;
                }

                $externalId = (string) ($item['id'] ?? '');
                if ($externalId === '') {
                    continue;
                }

                $payload = [
                    'ansay_id' => $item['ansayId'] ?? null,
                    'name' => $item['name'] ?? '',
                    'category' => $item['category'] ?? null,
                    'is_api_medicine' => true,
                    'price' => isset($item['price']) ? (float) $item['price'] : null,
                    'thc' => isset($item['thc']) ? (float) $item['thc'] : null,
                    'cbd' => isset($item['cbd']) ? (float) $item['cbd'] : null,
                    'genetic' => $item['genetic'] ?? null,
                    'strain' => $item['strain'] ?? null,
                    'country' => $item['country'] ?? null,
                    'manufacturer' => $item['manufacturer'] ?? null,
                    'grower' => $item['grower'] ?? null,
                    'availability' => $item['availibility'] ?? $item['availability'] ?? null,
                    'irradiated' => isset($item['irradiated']) ? (int) $item['irradiated'] : null,
                    'terpenes' => isset($item['terpenes']) && is_array($item['terpenes']) ? $item['terpenes'] : null,
                    'raw_data' => $item,
                    'last_synced_at' => $now,
                ];

                $medicine = CannaleoMedicine::updateOrCreate(
                    [
                        'cannaleo_pharmacy_id' => $pharmacy->id,
                        'external_id' => $externalId,
                    ],
                    $payload
                );

                if ($medicine->wasRecentlyCreated) {
                    $stats['medicines_created']++;
                } else {
                    $stats['medicines_updated']++;
                }
            }

            unset($chunk);
            gc_collect_cycles();
        }
    }
}
