<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class PrescriptionPdfService
{
    /**
     * Generate prescription PDF and save to public_path('prescription/upload/...').
     * Sets prescription.pdf and saves the model on success.
     *
     * @return true on success, or error message string on failure.
     */
    public function generate(Prescription $prescription): bool|string
    {
        try {
            $prescription->load(['doctor.user', 'doctor.hospital', 'user']);

            $medicines = json_decode($prescription->medicines, true) ?? [];
            $doctor = $prescription->doctor;
            $doctorName = $doctor && $doctor->user && $doctor->user->name
                ? $doctor->user->name
                : ($doctor && $doctor->name ? $doctor->name : 'Doctor');
            $patientName = $prescription->user ? $prescription->user->name : 'Patient';

            $patientAddress = '';
            $patientCity = '';
            $patientDob = null;
            if ($prescription->user) {
                $firstAddress = UserAddress::where('user_id', $prescription->user_id)->first();
                if ($firstAddress) {
                    $patientAddress = $firstAddress->address ?? '';
                }
                if (! empty($prescription->user->dob)) {
                    try {
                        $patientDob = Carbon::parse($prescription->user->dob)->format('d.m.Y');
                    } catch (\Exception $e) {
                        $patientDob = null;
                    }
                }
            }

            $doctorPhone = ($doctor && $doctor->user && $doctor->user->phone) ? $doctor->user->phone : '';
            $doctorAddress = ($doctor && $doctor->hospital && $doctor->hospital->address) ? $doctor->hospital->address : '';
            $doctorLanr = '';
            $receiptNr = 'RP' . str_pad((string) $prescription->id, 12, '0', STR_PAD_LEFT);

            $validFrom = now()->format('d.m.Y');
            $validUntil = null;
            if ($prescription->valid_from) {
                try {
                    $validFrom = $prescription->valid_from->format('d.m.Y');
                } catch (\Exception $e) {
                    // keep default
                }
            }
            if ($prescription->valid_until) {
                try {
                    $validUntil = $prescription->valid_until->format('d.m.Y');
                } catch (\Exception $e) {
                    // keep null
                }
            }

            $directory = public_path('prescription/upload');
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $fileName = 'prescription_' . $prescription->id . '_' . time() . '.pdf';
            $path = $directory . DIRECTORY_SEPARATOR . $fileName;

            if (View::exists('prescription_pdf')) {
                $pdf = \PDF::loadView('prescription_pdf', [
                    'prescription' => $prescription,
                    'medicines' => $medicines,
                    'doctor_name' => $doctorName,
                    'patient_name' => $patientName,
                    'patient_address' => $patientAddress,
                    'patient_city' => $patientCity,
                    'patient_country' => 'Deutschland',
                    'patient_dob' => $patientDob,
                    'doctor_phone' => $doctorPhone,
                    'doctor_address' => $doctorAddress,
                    'doctor_lanr' => $doctorLanr,
                    'doctor_title' => 'Arzt/Ärztin',
                    'receipt_nr' => $receiptNr,
                    'valid_from' => $validFrom,
                    'valid_until' => $validUntil,
                ]);
                $pdf->setPaper([0, 0, 297.64, 419.53], 'portrait');
            } else {
                $medicineName = $this->buildTempMedicineJson($medicines);
                $pdf = \PDF::loadView('temp', ['medicineName' => $medicineName]);
            }

            $pdf->save($path);

            if (! file_exists($path)) {
                Log::error('Prescription PDF save reported success but file missing', ['path' => $path]);

                return __('File was not written to disk.');
            }

            $prescription->pdf = $fileName;
            $prescription->save();

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to generate prescription PDF', [
                'prescription_id' => $prescription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $e->getMessage();
        }
    }

    protected function buildTempMedicineJson(array $medicines): string
    {
        $rows = [];
        foreach ($medicines as $item) {
            $rows[] = [
                'medicine' => data_get($item, 'medicine', ''),
                'days' => data_get($item, 'days', ''),
                'morning' => (int) (data_get($item, 'morning', 0) ? 1 : 0),
                'afternoon' => (int) (data_get($item, 'afternoon', 0) ? 1 : 0),
                'night' => (int) (data_get($item, 'night', 0) ? 1 : 0),
            ];
        }

        return json_encode($rows);
    }
}
