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
        $homeAddress = [
            'streetName' => $homeStreet,
            'houseNr' => '',
            'addressAddition' => '',
            'postalCode' => '',
            'city' => '',
        ];

        $deliveryStreet = $submission->delivery_address ?? '';
        $deliveryAddress = [
            'streetName' => $deliveryStreet,
            'houseNr' => '',
            'addressAddition' => (string) ($submission->delivery_state ?? ''),
            'postalCode' => (string) ($submission->delivery_postcode ?? ''),
            'city' => (string) ($submission->delivery_city ?? ''),
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
        $totalGross = 0;
        foreach ($products as $med) {
            $qty = 1;
            $price = (float) $med->price;
            $apiProducts[] = [
                'id' => (string) $med->external_id,
                'name' => $med->name ?? '',
                'price' => $price,
                'category' => $med->category ?? 'flower',
                'quantity' => $qty,
            ];
            $totalGross += $price * $qty;
        }

        // Cannaleo: use customer's selected delivery option; otherwise fallback to pickup vs delivery
        $shipping = 'delivery';
        if (! empty($submission->cannaleo_delivery_option)) {
            $shipping = $submission->cannaleo_delivery_option;
        } elseif ($submission->delivery_type === 'pickup') {
            $shipping = 'pickup';
        }

        $pickupBranchId = 0;
        if ($shipping === 'pickup' && $pharmacy->external_id !== null && $pharmacy->external_id !== '') {
            $pickupBranchId = is_numeric($pharmacy->external_id) ? (int) $pharmacy->external_id : 0;
        }

        $doctorPayload = [
            'name' => $doctorName,
            'phone' => $doctorPhone,
            'email' => $doctorEmail,
            'cityOfSignature' => $cityOfSignature,
            'dateOfSignature' => $dateOfSignature,
        ];
        $staticSignature = (string) config('cannaleo.static_doctor_signature', '');
        $doctorPayload['signature'] = $staticSignature !== ''
            ? $staticSignature
            : self::TEST_DUMMY_DOCTOR_SIGNATURE;

        $payload = [
            'prescriptionURL' => $prescriptionUrl,
            'internalOrderId' => 'RX-' . $prescription->id,
            'internalPharmacyId' => (string) $pharmacy->external_id,
            'doctor' => $doctorPayload,
            'customer' => $customerPayload,
            'products' => $apiProducts,
            'prepaid' => 0,
            'shipping' => $shipping,
            'pickup_branch_id' => $pickupBranchId,
            'totalGross' => round($totalGross, 2),
            'callbackUrl' => config('cannaleo.prescription_callback_url', ''),
        ];

        return $payload;
    }

    protected static function doctorCityOfSignature(Doctor $doctor): string
    {
        if ($doctor->hospital && $doctor->hospital->city) {
            return (string) $doctor->hospital->city;
        }
        $default = config('cannaleo.default_signature_city', '');
        if ($default !== '') {
            return $default;
        }
        if ($doctor->hospital && $doctor->hospital->address) {
            $parts = preg_split('/\s+/', trim($doctor->hospital->address), -1, PREG_SPLIT_NO_EMPTY);
            return $parts ? (string) end($parts) : '';
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
