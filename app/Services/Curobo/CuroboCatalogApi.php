<?php

namespace App\Services\Curobo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CuroboCatalogApi
{
    protected string $baseUrl;

    protected ?string $apiKey;

    public function __construct(?string $baseUrl = null, ?string $apiKey = null)
    {
        $this->baseUrl = rtrim($baseUrl ?? config('cannaleo.curobo_api_url'), '/');
        $this->apiKey = $apiKey ?? config('cannaleo.curobo_api_key');
    }

    /**
     * Fetch catalog from GET {base_url}/api/v1/catalog/
     * Returns decoded array of catalog items (or from data/catalog key if wrapped).
     *
     * @return array<int, array<string, mixed>>
     * @throws \RuntimeException on non-2xx or invalid JSON
     */
    public function getCatalog(): array
    {
        $url = $this->baseUrl . '/api/v1/catalog/';

        $verifySsl = config('cannaleo.curobo_ssl_verify', true);
        $options = ['verify' => $verifySsl];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'API-KEY' => $this->apiKey ?? '',
        ])->withOptions($options)->get($url);

        if (! $response->successful()) {
            Log::warning('Curobo catalog API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException(
                'Curobo catalog API request failed: ' . $response->status() . ' ' . $response->body()
            );
        }

        $data = $response->json();
        if ($data === null) {
            throw new \RuntimeException('Curobo catalog API returned invalid JSON');
        }

        $items = $this->extractCatalogArray($data);
        if ($items !== null) {
            return $items;
        }

        throw new \RuntimeException(
            'Curobo catalog API response format unexpected. Top-level keys: ' . implode(', ', array_keys(is_array($data) ? $data : []))
        );
    }

    /**
     * Extract flat array of catalog items from various API response shapes.
     * Postman returns 153 items; response may be e.g. { "data": { "items": [...] } } or { "catalog": [...] }.
     *
     * @param mixed $data
     * @return array<int, array<string, mixed>>|null
     */
    protected function extractCatalogArray($data): ?array
    {
        if (! is_array($data)) {
            return null;
        }

        // Already a list of products (numeric keys, first element is product-shaped)
        if ($this->isCatalogList($data)) {
            return $data;
        }

        // { "data": [ ... ] }
        if (isset($data['data']) && is_array($data['data']) && $this->isCatalogList($data['data'])) {
            return $data['data'];
        }

        // { "data": { "items": [ ... ] } } or { "data": { "catalog": [ ... ] } }
        if (isset($data['data']) && is_array($data['data'])) {
            $inner = $data['data'];
            if (isset($inner['items']) && is_array($inner['items']) && $this->isCatalogList($inner['items'])) {
                return $inner['items'];
            }
            if (isset($inner['catalog']) && is_array($inner['catalog']) && $this->isCatalogList($inner['catalog'])) {
                return $inner['catalog'];
            }
        }

        // { "catalog": [ ... ] } or { "items": [ ... ] }
        if (isset($data['catalog']) && is_array($data['catalog']) && $this->isCatalogList($data['catalog'])) {
            return $data['catalog'];
        }
        if (isset($data['items']) && is_array($data['items']) && $this->isCatalogList($data['items'])) {
            return $data['items'];
        }

        return null;
    }

    /**
     * True if array looks like a list of catalog products (has numeric keys and product-like elements).
     */
    protected function isCatalogList(array $arr): bool
    {
        if (empty($arr)) {
            return true;
        }
        $first = reset($arr);
        return is_array($first) && (isset($first['id']) || isset($first['name']));
    }
}
