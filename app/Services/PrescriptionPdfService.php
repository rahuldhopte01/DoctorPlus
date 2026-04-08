<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;

class PrescriptionPdfService
{
    /**
     * Generate prescription PDF and save to storage_path('prescription-upload/...').
     * Sets prescription.pdf and saves the model on success.
     *
     * @return true on success, or error message string on failure.
     */
    public function generate(Prescription $prescription): bool|string
    {
        try {
            $prescription->load(['doctor.user', 'doctor.hospital', 'user']);

            $medicines = array_slice(json_decode($prescription->medicines, true) ?? [], 0, 5);
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
                    $patientAddress = trim((string) ($firstAddress->address ?? ''));
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
            $doctorAddressParts = array_filter([
                $doctor->street ?? '',
                trim(($doctor->postcode ?? '') . ' ' . ($doctor->city ?? '')),
                $doctor->state ?? '',
                $doctor->country ?? '',
            ], fn ($p) => trim((string) $p) !== '');
            $doctorAddress = implode(', ', $doctorAddressParts);
            $doctorLanr = '';
            $receiptNr = 'RP' . str_pad((string) $prescription->id, 12, '0', STR_PAD_LEFT);

            $validUntil = null;
            if ($prescription->valid_until) {
                try {
                    $validUntil = $prescription->valid_until->format('d.m.Y');
                } catch (\Exception $e) {
                    $validUntil = null;
                }
            }

            $directory = storage_path('prescription-upload');
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $fileName = 'prescription_' . $prescription->id . '_' . time() . '.pdf';
            $path = $directory . DIRECTORY_SEPARATOR . $fileName;

            $signaturePath = ($doctor && $doctor->signature)
                ? storage_path('app/doctor-signatures/' . $doctor->signature)
                : null;

            $this->generateCustomTemplate(
                outputPath: $path,
                createdDate: $prescription->created_at ? $prescription->created_at->format('d.m.Y') : now()->format('d.m.Y'),
                patientName: $patientName,
                patientAddress: $patientAddress,
                patientCity: $patientCity,
                patientCountry: 'Deutschland',
                patientDob: $patientDob,
                doctorName: $doctorName,
                doctorTitle: 'Arztin',
                doctorAddress: $doctorAddress,
                doctorPhone: $doctorPhone,
                doctorLanr: $doctorLanr,
                medicines: $medicines,
                validUntil: $validUntil,
                receiptNr: $receiptNr,
                signaturePath: $signaturePath
            );

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

    protected function generateCustomTemplate(
        string $outputPath,
        string $createdDate,
        string $patientName,
        string $patientAddress,
        string $patientCity,
        string $patientCountry,
        ?string $patientDob,
        string $doctorName,
        string $doctorTitle,
        string $doctorAddress,
        string $doctorPhone,
        string $doctorLanr,
        array $medicines,
        ?string $validUntil,
        string $receiptNr,
        ?string $signaturePath = null
    ): void {
        $templatePath = base_path('prescription-pdf.pdf');
        if (! file_exists($templatePath)) {
            throw new \RuntimeException('Prescription template PDF not found: ' . $templatePath);
        }

        $pdf = new Fpdi('L', 'mm', [148, 105]);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $pageCount = $pdf->setSourceFile($templatePath);
        if ($pageCount < 1) {
            throw new \RuntimeException('Prescription template PDF is empty.');
        }

        $tpl = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($tpl);
        $pdf->useTemplate($tpl, 0, 0, $size['width'], $size['height']);

        $line = [78, 128, 170];
        $text = [0, 0, 0];

        $pdf->SetTextColor($text[0], $text[1], $text[2]);
        $this->drawText($pdf, $this->pxX(20), $this->pxY(16), $createdDate, 8.4, '');
        $this->drawText($pdf, $this->pxX(64), $this->pxY(74), 'Privat', 8.8, '');
        $this->drawText($pdf, $this->pxX(64), $this->pxY(116), $patientName, 9.2, '');

        $patientLines = array_values(array_filter([
            $patientAddress,
            $patientCity,
            $patientCountry,
        ], fn ($lineText) => trim((string) $lineText) !== ''));
        $this->drawMultiline(
            $pdf,
            $this->pxX(64),
            $this->pxY(146),
            $patientLines,
            8.2,
            '',
            $this->pxH(28),
            $this->pxW(278),
            'L'
        );

        if ($patientDob) {
            $this->drawCenteredText($pdf, $this->pxX(392), $this->pxY(182), $this->pxW(120), $patientDob, 9.0, '');
        }

        if ($doctorLanr !== '') {
            $this->drawCenteredText($pdf, $this->pxX(230), $this->pxY(250), $this->pxW(110), $doctorLanr, 6.8, '');
        }

        $this->drawCenteredText($pdf, $this->pxX(372), $this->pxY(284), $this->pxW(128), $createdDate, 9.0, '');

        $medicineLayout = $this->getMedicineLayout(count($medicines));
        foreach ($medicines as $index => $item) {
            $rowY = $medicineLayout['startY'] + ($index * $medicineLayout['rowHeight']);

            $name = trim((string) data_get($item, 'medicine', ''));
            $strength = trim((string) data_get($item, 'strength', ''));
            $qty = trim((string) data_get($item, 'qty', ''));
            $ed = trim((string) data_get($item, 'ed', ''));
            $td = trim((string) data_get($item, 'td_freq', ''));

            $nameParts = array_values(array_filter([$name, $strength, $qty]));
            $title = 'Cannabisbluten: "' . implode(', ', $nameParts) . '", Unzerkleinert';

            $doseParts = [];
            if ($ed !== '') {
                $doseParts[] = 'ED: ' . $ed;
            }
            if ($td !== '') {
                $doseParts[] = 'TD: Bis zu ' . $td;
            }
            $dose = count($doseParts)
                ? 'Dosierung: ' . implode(', ', $doseParts) . ', verdampfen und inhalieren'
                : 'Dosierung: nach Anweisung, verdampfen und inhalieren';

            $title = $this->fitTextToWidth($pdf, $title, $medicineLayout['titleFont'], '', $this->pxW(505));
            $dose = $this->fitTextToWidth($pdf, $dose, $medicineLayout['doseFont'], '', $this->pxW(505));

            $this->drawAutIdemBox(
                $pdf,
                $this->pxX(19),
                $rowY - $this->pxH(2),
                $this->pxW(29),
                $medicineLayout['boxHeight'],
                $line
            );
            $this->drawText($pdf, $this->pxX(53), $rowY, $title, $medicineLayout['titleFont'], '');
            $this->drawText($pdf, $this->pxX(53), $rowY + $medicineLayout['doseOffset'], $dose, $medicineLayout['doseFont'], '');
        }

        $this->drawCenteredText($pdf, $this->pxX(635), $this->pxY(359), $this->pxW(185), $doctorName, 8.6, '');
        $this->drawCenteredText($pdf, $this->pxX(680), $this->pxY(387), $this->pxW(90), $doctorTitle, 5.8, '');
        if ($signaturePath && file_exists($signaturePath)) {
            $pdf->Image($signaturePath, $this->pxX(675), $this->pxY(378), $this->pxW(95));
        } else {
            $this->drawDoctorStamp($pdf, $this->pxX(713), $this->pxY(421), $this->pxW(32), $line);
        }
        $this->drawMultiline($pdf, $this->pxX(655), $this->pxY(425), array_values(array_filter([
            $doctorAddress,
            $doctorPhone !== '' ? 'Telefon: ' . $doctorPhone : '',
            $doctorLanr !== '' ? 'LANR: ' . $doctorLanr : '',
        ])), 5.4, '', $this->pxH(20), $this->pxW(150), 'C');

        $this->drawText($pdf, $this->pxX(46), $this->pxY(500), 'PKVH', 12.8, '');
        if ($validUntil) {
            $this->drawText($pdf, $this->pxX(127), $this->pxY(503), 'Gultig bis ' . $validUntil, 6.3, '');
        }
        $this->drawText($pdf, $this->pxX(356), $this->pxY(503), 'RezeptNr: ' . $receiptNr, 6.3, '');

        $pdf->Output('F', $outputPath);
    }

    protected function drawAutIdemBox(Fpdi $pdf, float $x, float $y, float $w, float $h, array $line): void
    {
        $pdf->SetDrawColor($line[0], $line[1], $line[2]);
        $pdf->Rect($x, $y, $w, $h);
    }

    protected function drawDoctorStamp(Fpdi $pdf, float $x, float $y, float $r, array $rgb): void
    {
        $pdf->SetDrawColor($rgb[0], $rgb[1], $rgb[2]);
        $this->drawEllipse($pdf, $x, $y, $r, $r);
        $pdf->SetTextColor($rgb[0], $rgb[1], $rgb[2]);
        $this->drawCenteredText($pdf, $x - 3.0, $y - 1.6, 6.0, 'R', 14.0, 'I', 'Times');
        $pdf->SetTextColor(0, 0, 0);
    }

    protected function drawEllipse(Fpdi $pdf, float $cx, float $cy, float $rx, float $ry): void
    {
        $segments = 24;
        $step = (2 * M_PI) / $segments;
        $points = [];
        for ($i = 0; $i <= $segments; $i++) {
            $angle = $i * $step;
            $points[] = [
                $cx + ($rx * cos($angle)),
                $cy + ($ry * sin($angle)),
            ];
        }

        for ($i = 1; $i < count($points); $i++) {
            $pdf->Line($points[$i - 1][0], $points[$i - 1][1], $points[$i][0], $points[$i][1]);
        }
    }

    protected function drawTriangle(Fpdi $pdf, float $x, float $y, array $line): void
    {
        $w = $this->pxW(9);
        $h = $this->pxH(8);
        $pdf->Line($x, $y, $x + ($w / 2), $y - $h);
        $pdf->Line($x + ($w / 2), $y - $h, $x + $w, $y);
        $pdf->Line($x, $y, $x + $w, $y);
    }

    protected function drawText(
        Fpdi $pdf,
        float $x,
        float $y,
        string $text,
        float $size,
        string $style = '',
        string $font = 'Arial'
    ): void {
        $pdf->SetFont($font, $style, $size);
        $pdf->SetXY($x, $y);
        $pdf->Cell(0, 0, $this->encode($text), 0, 0, 'L');
    }

    protected function drawCenteredText(
        Fpdi $pdf,
        float $x,
        float $y,
        float $w,
        string $text,
        float $size,
        string $style = '',
        string $font = 'Arial'
    ): void {
        $pdf->SetFont($font, $style, $size);
        $pdf->SetXY($x, $y);
        $pdf->Cell($w, 0, $this->encode($text), 0, 0, 'C');
    }

    protected function drawMultiline(
        Fpdi $pdf,
        float $x,
        float $y,
        array $lines,
        float $size,
        string $style = '',
        float $lineHeight = 3.2,
        float $width = 40.0,
        string $align = 'L'
    ): void {
        $pdf->SetFont('Arial', $style, $size);
        $currentY = $y;
        foreach ($lines as $line) {
            $pdf->SetXY($x, $currentY);
            $pdf->Cell($width, 0, $this->encode((string) $line), 0, 0, $align);
            $currentY += $lineHeight;
        }
    }

    protected function getMedicineLayout(int $count): array
    {
        if ($count >= 5) {
            return [
                'startY' => $this->pxY(354),
                'rowHeight' => $this->pxH(49),
                'titleFont' => 5.55,
                'doseFont' => 4.95,
                'doseOffset' => $this->pxH(19),
                'boxHeight' => $this->pxH(30),
            ];
        }

        if ($count === 4) {
            return [
                'startY' => $this->pxY(354),
                'rowHeight' => $this->pxH(55),
                'titleFont' => 5.8,
                'doseFont' => 5.1,
                'doseOffset' => $this->pxH(20),
                'boxHeight' => $this->pxH(30),
            ];
        }

        return [
            'startY' => $this->pxY(354),
            'rowHeight' => $this->pxH(50),
            'titleFont' => 6.0,
            'doseFont' => 5.25,
            'doseOffset' => $this->pxH(20),
            'boxHeight' => $this->pxH(30),
        ];
    }

    protected function pxX(float $x): float
    {
        return ($x * 148) / 875;
    }

    protected function pxY(float $y): float
    {
        return ($y * 105) / 621;
    }

    protected function pxW(float $w): float
    {
        return ($w * 148) / 875;
    }

    protected function pxH(float $h): float
    {
        return ($h * 105) / 621;
    }

    protected function fitTextToWidth(
        Fpdi $pdf,
        string $text,
        float $size,
        string $style,
        float $width,
        string $font = 'Arial'
    ): string {
        $pdf->SetFont($font, $style, $size);
        $encoded = $this->encode($text);
        if ($pdf->GetStringWidth($encoded) <= $width) {
            return $text;
        }

        $ellipsis = '...';
        $plain = $text;
        while ($plain !== '') {
            $plain = rtrim(substr($plain, 0, -1));
            $candidate = $plain . $ellipsis;
            if ($pdf->GetStringWidth($this->encode($candidate)) <= $width) {
                return $candidate;
            }
        }

        return $ellipsis;
    }

    protected function encode(string $text): string
    {
        $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT', $text);
        return $converted !== false ? $converted : $text;
    }
}
