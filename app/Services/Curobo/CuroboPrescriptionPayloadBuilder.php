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

class CuroboPrescriptionPayloadBuilder
{
    /**
     * Temporary dummy signature used for Cannaleo API testing.
     */
    protected const TEST_DUMMY_DOCTOR_SIGNATURE = 'DUMMY_DOCTOR_SIGNATURE_FOR_TESTING';

    /**
     * Build the exact JSON body expected by the Curobo prescription API.
     *
     * @param  array<CannaleoMedicine>|Collection<int, CannaleoMedicine>  $products
     * @return array<string, mixed>
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

        $salutation = self::mapGenderToSalutation($customer->gender ?? null);
        $nameParts = self::splitName($customer->name ?? '');
        $dob = $customer->dob ? (is_string($customer->dob) ? $customer->dob : $customer->dob->format('Y-m-d')) : '';

        $firstAddress = UserAddress::where('user_id', $customer->id)->first();
        $homeStreet = $firstAddress && $firstAddress->address ? $firstAddress->address : '';
        // postal_code and city were added to user_address table; fall back to delivery address data
        $homePostalCode = ($firstAddress && $firstAddress->postal_code)
            ? (string) $firstAddress->postal_code
            : (string) ($submission->delivery_postcode ?? '');
        $homeCity = ($firstAddress && $firstAddress->city)
            ? (string) $firstAddress->city
            : (string) ($submission->delivery_city ?? '');
        // Curobo requires postalCode to match ^[0-9]{5}$ — keep only if valid, else empty
        $homePostalCode = preg_match('/^[0-9]{5}$/', $homePostalCode) ? $homePostalCode : '';
        $homeCity = mb_strlen($homeCity) >= 2 ? $homeCity : '';
        $homeAddress = [
            'streetName' => $homeStreet,
            'houseNr' => '',
            'addressAddition' => '',
            'postalCode' => $homePostalCode,
            'city' => $homeCity,
        ];

        $deliveryStreet = $submission->delivery_address ?? '';
        $deliveryPostalCode = (string) ($submission->delivery_postcode ?? '');
        $deliveryCity = (string) ($submission->delivery_city ?? '');
        // Curobo requires postalCode to match ^[0-9]{5}$ — keep only if valid, else empty
        $deliveryPostalCode = preg_match('/^[0-9]{5}$/', $deliveryPostalCode) ? $deliveryPostalCode : '';
        $deliveryCity = mb_strlen($deliveryCity) >= 2 ? $deliveryCity : '';
        $deliveryAddress = [
            'streetName' => $deliveryStreet,
            'houseNr' => '',
            'addressAddition' => (string) ($submission->delivery_state ?? ''),
            'postalCode' => $deliveryPostalCode,
            'city' => $deliveryCity,
            'salutation' => $salutation,
            'firstname' => $nameParts['firstname'],
            'lastname' => $nameParts['lastname'],
        ];

        $customerPayload = [
            'salutation' => $salutation,
            'firstname' => $nameParts['firstname'],
            'lastname' => $nameParts['lastname'],
            'dateOfBirth' => $dob,
            'email' => $customer->email ?? '',
            'phone' => $customer->phone ?? '',
            'homeAddress' => $homeAddress,
            'deliveryAddress' => $deliveryAddress,
        ];

        $apiProducts = [];
        $totalGrossFloat = 0;
        foreach ($products as $med) {
            $qty = 1;
            $priceFloat = (float) $med->price;
            $apiProducts[] = [
                'id' => (string) $med->external_id,
                'name' => $med->name ?? '',
                'price' => (int) round($priceFloat * 100), // API expects integer cents
                'category' => $med->category ?? 'flower',
                'quantity' => $qty,
            ];
            $totalGrossFloat += $priceFloat * $qty;
        }

        // Cannaleo: use customer's selected delivery option; otherwise fallback to pickup vs delivery
        // Curobo API accepts: 'standard', 'express', 'pickup'
        $shipping = 'standard';
        if (! empty($submission->cannaleo_delivery_option)) {
            $rawOption = $submission->cannaleo_delivery_option;
            // Map known aliases to valid Curobo API values
            $shippingMap = [
                'delivery' => 'standard',
                'standard' => 'standard',
                'express'  => 'express',
                'pickup'   => 'pickup',
            ];
            $shipping = $shippingMap[$rawOption] ?? 'standard';
        } elseif ($submission->delivery_type === 'pickup') {
            $shipping = 'pickup';
        }

        $pickupBranchId = null;
        if ($shipping === 'pickup' && $pharmacy->external_id !== null && $pharmacy->external_id !== '') {
            $pickupBranchId = is_numeric($pharmacy->external_id) ? (int) $pharmacy->external_id : null;
        }

        $doctorPayload = [
            'name' => $doctorName,
            'phone' => $doctorPhone,
            'email' => $doctorEmail,
            'cityOfSignature' => $cityOfSignature,
            'dateOfSignature' => $dateOfSignature,
        ];
        $doctorPayload['signature'] = self::resolveDoctorSignature($doctor);

        $payload = [
            'prescriptionURL' => $prescriptionUrl,
            'internalOrderId' => 'RX-' . $prescription->id,
            'internalPharmacyId' => (string) $pharmacy->external_id,
            'doctor' => $doctorPayload,
            'customer' => $customerPayload,
            'products' => $apiProducts,
            'prepaid' => 0,
            'shipping' => $shipping,
            'pickup_branch_id' => $pickupBranchId, // null for delivery, int for pickup
            'totalGross' => (int) round($totalGrossFloat * 100), // API expects integer cents
            'callbackUrl' => config('cannaleo.prescription_callback_url', ''),
        ];

        return $payload;
    }

    /**
     * Resolve the doctor's signature for the Curobo API.
     * Priority: uploaded signature file (as base64 data URL) → static config value → dummy.
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
        }
        // 2. Static signature from config (set CUROBO_STATIC_DOCTOR_SIGNATURE in .env)
        $staticSignature = (string) config('cannaleo.static_doctor_signature', '');
        if ($staticSignature !== '') {
            return $staticSignature;
        }
        // 3. Fallback dummy (API will likely reject this; doctor should upload a signature)
        return self::TEST_DUMMY_DOCTOR_SIGNATURE;
    }

    protected static function doctorCityOfSignature(Doctor $doctor): string
    {
        // 1. Use the dedicated city column added to the hospital table
        if ($doctor->hospital && ! empty($doctor->hospital->city)) {
            return (string) $doctor->hospital->city;
        }
        // 2. Fall back to config default (set CUROBO_DEFAULT_SIGNATURE_CITY in .env)
        $default = config('cannaleo.default_signature_city', '');
        if ($default !== '') {
            return $default;
        }
        // 3. Last resort: try to parse city from the end of the hospital address string
        if ($doctor->hospital && $doctor->hospital->address) {
            // Try to extract last word that is at least 2 chars (likely a city name)
            $parts = preg_split('/[\s,]+/', trim($doctor->hospital->address), -1, PREG_SPLIT_NO_EMPTY);
            $parts = array_filter($parts, fn ($p) => mb_strlen($p) >= 2);
            if ($parts) {
                return (string) end($parts);
            }
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
            'lastname' => trim(substr($name, $pos + 1)),
        ];
    }
}
