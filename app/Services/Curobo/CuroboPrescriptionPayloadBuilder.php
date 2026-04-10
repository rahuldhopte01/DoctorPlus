<?php

namespace App\Services\Curobo;

use App\Models\CannaleoMedicine;
use App\Models\CannaleoPharmacy;
use App\Models\Doctor;
use App\Models\Prescription;
use App\Models\QuestionnaireSubmission;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CuroboPrescriptionPayloadBuilder
{
    /**
     * Build the exact JSON body expected by the Curobo prescription API.
     *
     * @param  array<CannaleoMedicine>|Collection<int, CannaleoMedicine>  $products
     * @return array<string, mixed>
     * @throws \RuntimeException if required fields (signature, cityOfSignature, postalCode) are missing.
     */
    public static function build(
        Prescription $prescription,
        QuestionnaireSubmission $submission,
        User $customer,
        Doctor $doctor,
        $products,
        string $prescriptionUrl,
        CannaleoPharmacy $pharmacy
    ): array {
        $products = $products instanceof Collection ? $products->all() : $products;
        $products = array_values($products);

        $doctorName = $doctor->user && $doctor->user->name
            ? $doctor->user->name
            : ($doctor->name ?? '');
        $doctorPhone = $doctor->user && $doctor->user->phone ? $doctor->user->phone : '';
        $doctorEmail = $doctor->user && $doctor->user->email ? $doctor->user->email : '';
        $cityOfSignature = self::doctorCityOfSignature($doctor);
        $dateOfSignature = $prescription->created_at ? $prescription->created_at->format('Y-m-d') : now()->format('Y-m-d');

        if (mb_strlen($cityOfSignature) < 2) {
            throw new \RuntimeException(
                'Doctor (ID: ' . $doctor->id . ') cityOfSignature is empty. ' .
                'Set the hospital city for this doctor or set CUROBO_DEFAULT_SIGNATURE_CITY in .env.'
            );
        }

        $salutation = self::mapGenderToSalutation($customer->gender ?? null);
        $nameParts = self::splitName($customer->name ?? '');
        $dob = $customer->dob ? (is_string($customer->dob) ? $customer->dob : $customer->dob->format('Y-m-d')) : '';

        $firstAddress = UserAddress::where('user_id', $customer->id)->first();
        $homeStreet = $firstAddress && $firstAddress->address ? $firstAddress->address : '';

        // Postal code: DB column → extract from street string (handles full-address-in-one-field)
        $homePostalCode = ($firstAddress && $firstAddress->postal_code)
            ? (string) $firstAddress->postal_code
            : (string) ($submission->delivery_postcode ?? '');
        if (! preg_match('/^[0-9]{5}$/', $homePostalCode)) {
            $homePostalCode = self::extractPostalCode($homeStreet);
        }

        // City: DB column → extract from street string (text after postal code)
        $homeCity = ($firstAddress && $firstAddress->city)
            ? (string) $firstAddress->city
            : '';
        if (mb_strlen($homeCity) < 2) {
            $homeCity = self::extractCityFromAddress($homeStreet);
        }
        if (mb_strlen($homeCity) < 2) {
            $homeCity = (string) ($submission->delivery_city ?? '');
        }

        if (! preg_match('/^[0-9]{5}$/', $homePostalCode)) {
            throw new \RuntimeException(
                'Customer (ID: ' . $customer->id . ') home address has no valid 5-digit German postal code. ' .
                'Update the postal_code on user_address or enter it in the street field.'
            );
        }

        $homeAddress = [
            'streetName'      => $homeStreet,
            'houseNr'         => '',
            'addressAddition' => '',
            'postalCode'      => $homePostalCode,
            'city'            => $homeCity,
        ];

        $deliveryStreet = $submission->delivery_address ?? '';
        $deliveryPostalCode = (string) ($submission->delivery_postcode ?? '');
        $deliveryCity = (string) ($submission->delivery_city ?? '');

        // Try to extract postal code from the delivery street string
        if (! preg_match('/^[0-9]{5}$/', $deliveryPostalCode)) {
            $deliveryPostalCode = self::extractPostalCode($deliveryStreet);
        }
        // Fall back to home address values when delivery postal code / city are missing
        if (! preg_match('/^[0-9]{5}$/', $deliveryPostalCode)) {
            $deliveryPostalCode = $homePostalCode;
        }
        if (mb_strlen($deliveryCity) < 2) {
            $deliveryCity = self::extractCityFromAddress($deliveryStreet);
        }
        if (mb_strlen($deliveryCity) < 2) {
            $deliveryCity = $homeCity;
        }

        $deliveryAddress = [
            'streetName'      => $deliveryStreet ?: $homeStreet,
            'houseNr'         => '',
            'addressAddition' => (string) ($submission->delivery_state ?? ''),
            'postalCode'      => $deliveryPostalCode,
            'city'            => $deliveryCity,
            'salutation'      => $salutation,
            'firstname'       => $nameParts['firstname'],
            'lastname'        => $nameParts['lastname'],
        ];

        $customerPayload = [
            'salutation'      => $salutation,
            'firstname'       => $nameParts['firstname'],
            'lastname'        => $nameParts['lastname'],
            'dateOfBirth'     => $dob,
            'email'           => $customer->email ?? '',
            'phone'           => $customer->phone ?? '',
            'homeAddress'     => $homeAddress,
            'deliveryAddress' => $deliveryAddress,
        ];

        $apiProducts = [];
        $totalGrossFloat = 0;
        foreach ($products as $med) {
            $qty = 1;
            $priceFloat = (float) $med->price;
            $apiProducts[] = [
                'id'       => (string) $med->external_id,
                'name'     => $med->name ?? '',
                'price'    => (int) round($priceFloat * 100), // API expects integer cents
                'category' => $med->category ?? 'flower',
                'quantity' => $qty,
            ];
            $totalGrossFloat += $priceFloat * $qty;
        }

        $shipping = 'shipping';
        $pickupBranchId = null;

        $doctorPayload = [
            'name'            => $doctorName,
            'phone'           => $doctorPhone,
            'email'           => $doctorEmail,
            'cityOfSignature' => $cityOfSignature,
            'dateOfSignature' => $dateOfSignature,
        ];

        return [
            'prescriptionURL'   => $prescriptionUrl,
            'internalOrderId'   => 'RX-' . $prescription->id,
            'internalPharmacyId' => (string) $pharmacy->external_id,
            'doctor'            => $doctorPayload,
            'customer'          => $customerPayload,
            'products'          => $apiProducts,
            'prepaid'           => 0,
            'shipping'          => $shipping,
            'pickup_branch_id'  => $pickupBranchId,
            'totalGross'        => (int) round($totalGrossFloat * 100), // API expects integer cents
            'callbackUrl'       => config('cannaleo.prescription_callback_url', ''),
        ];
    }

    /**
     * Resolve the doctor's signature for the Curobo API.
     * Priority: uploaded signature file (as base64 data URL) → static config value.
     * Throws if no signature is available — submission is blocked until the doctor uploads one.
     *
     * @throws \RuntimeException
     */
    protected static function resolveDoctorSignature(Doctor $doctor): string
    {
        // 1. Use the doctor's uploaded scanned signature (image or PDF)
        if (! empty($doctor->signature)) {
            $path = storage_path('app/doctor-signatures/' . $doctor->signature);
            if (file_exists($path)) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mimeType = $ext === 'pdf'
                    ? 'application/pdf'
                    : (mime_content_type($path) ?: 'image/png');
                $base64 = base64_encode(file_get_contents($path));
                return 'data:' . $mimeType . ';base64,' . $base64;
            }
            Log::warning('Doctor signature file missing on disk', [
                'doctor_id'     => $doctor->id,
                'expected_path' => $path,
            ]);
        }

        // 2. Static signature from config (set CUROBO_STATIC_DOCTOR_SIGNATURE in .env)
        $staticSignature = (string) config('cannaleo.static_doctor_signature', '');
        if ($staticSignature !== '') {
            return $staticSignature;
        }

        throw new \RuntimeException(
            'Doctor (ID: ' . $doctor->id . ') has no signature uploaded. ' .
            'Please upload a signature before approving Cannaleo prescriptions.'
        );
    }

    protected static function doctorCityOfSignature(Doctor $doctor): string
    {
        // 1. Doctor's own city field (set directly on the doctor record)
        if (! empty($doctor->city)) {
            return (string) $doctor->city;
        }
        // 2. Hospital city column
        if ($doctor->hospital && ! empty($doctor->hospital->city)) {
            return (string) $doctor->hospital->city;
        }
        // 3. Config default (set CUROBO_DEFAULT_SIGNATURE_CITY in .env)
        $default = config('cannaleo.default_signature_city', '');
        if ($default !== '') {
            return $default;
        }
        // 4. Extract city from hospital address string
        if ($doctor->hospital && $doctor->hospital->address) {
            $city = self::extractCityFromAddress($doctor->hospital->address);
            if (mb_strlen($city) >= 2) {
                return $city;
            }
            // Fallback: last word(s) in the address
            $parts = preg_split('/[\s,]+/', trim($doctor->hospital->address), -1, PREG_SPLIT_NO_EMPTY);
            $parts = array_filter($parts, fn ($p) => mb_strlen($p) >= 2);
            if ($parts) {
                return (string) end($parts);
            }
        }
        // 5. Extract city from doctor's own street field
        if (! empty($doctor->street)) {
            $city = self::extractCityFromAddress($doctor->street);
            if (mb_strlen($city) >= 2) {
                return $city;
            }
        }
        return '';
    }

    /**
     * Extract a 5-digit German postal code from an address string.
     * e.g. "Wörthstr. 19 67059 Ludwigshafen am Rhein" → "67059"
     */
    protected static function extractPostalCode(string $str): string
    {
        if (preg_match('/\b([0-9]{5})\b/', $str, $m)) {
            return $m[1];
        }
        return '';
    }

    /**
     * Extract city name from an address string — text that follows a 5-digit postal code.
     * e.g. "Wörthstr. 19 67059 Ludwigshafen am Rhein" → "Ludwigshafen am Rhein"
     */
    protected static function extractCityFromAddress(string $str): string
    {
        if (preg_match('/\b[0-9]{5}\b\s+(.+)$/u', trim($str), $m)) {
            $city = trim($m[1]);
            return mb_strlen($city) >= 2 ? $city : '';
        }
        return '';
    }

    protected static function mapGenderToSalutation(?string $gender): string
    {
        if ($gender === null || $gender === '') {
            return 'other';
        }
        $g = strtolower($gender);
        if (in_array($g, ['male', 'm', 'man'], true)) {
            return 'male';
        }
        if (in_array($g, ['female', 'f', 'woman'], true)) {
            return 'female';
        }
        return 'other';
    }

    /**
     * @return array{firstname: string, lastname: string}
     */
    protected static function splitName(string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return ['firstname' => '', 'lastname' => ''];
        }
        $pos = strpos($name, ' ');
        if ($pos === false) {
            return ['firstname' => '', 'lastname' => $name];
        }
        return [
            'firstname' => substr($name, 0, $pos),
            'lastname'  => trim(substr($name, $pos + 1)),
        ];
    }
}
