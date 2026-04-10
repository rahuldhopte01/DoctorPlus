<?php

namespace App\Services\Curobo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CuroboPrescriptionApi
{
    protected string $baseUrl;

    protected ?string $apiKey;

    protected bool $verifySsl;

    public function __construct(?string $baseUrl = null, ?string $apiKey = null, ?bool $verifySsl = null)
    {
        $this->baseUrl = rtrim($baseUrl ?? config('cannaleo.curobo_api_url'), '/');
        $this->apiKey = $apiKey ?? config('cannaleo.curobo_api_key');
        $this->verifySsl = $verifySsl ?? filter_var(config('cannaleo.curobo_ssl_verify', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Submit prescription payload to Curobo prescription API.
     * POST to {baseUrl}{prescription_api_path} with API-KEY and JSON body.
     *
     * @param array<string, mixed> $payload Request body (will be JSON encoded).
     * @return array<string, mixed> Decoded response array on 2xx.
     * @throws \RuntimeException on non-2xx or invalid JSON.
     */
    public function submitPrescription(array $payload): array
    {
        $path = ltrim(config('cannaleo.prescription_api_path', '/api/v1/prescription/'), '/');
        $url = $this->baseUrl . '/' . $path;

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'API-KEY' => $this->apiKey ?? '',
            'domain' => config('cannaleo.curobo_domain', ''),
        ])->withOptions(['verify' => $this->verifySsl])
            ->post($url, $payload);

        if (! $response->successful()) {
            Log::warning('Curobo prescription API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException(
                'Curobo prescription API request failed: ' . $response->status() . ' ' . $response->body()
            );
        }

        $data = $response->json();
        if (! is_array($data)) {
            throw new \RuntimeException('Curobo prescription API returned invalid JSON');
        }

        return $data;
    }
}
