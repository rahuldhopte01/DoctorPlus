<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Privatrezept</title>
    <style>
        @page {
            size: 148mm 105mm;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #000;
        }

        .page {
            position: relative;
            width: 148mm;
            height: 105mm;
            overflow: hidden;
            page-break-after: always;
            background: #fff;
        }

        .page.last {
            page-break-after: auto;
        }

        .bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 148mm;
            height: 105mm;
            z-index: 0;
        }

        .text,
        .stamp,
        .aut-idem,
        .mask {
            position: absolute;
            z-index: 1;
        }

        .mask-white {
            background: #fff;
        }

        .mask-blue {
            background: #b9dbf4;
        }

        .top-date-mask {
            top: 1.6mm;
            left: 1.8mm;
            width: 28mm;
            height: 7mm;
        }

        .patient-text-mask {
            top: 15.5mm;
            left: 9.2mm;
            width: 49mm;
            height: 19mm;
        }

        .dob-mask {
            top: 18.8mm;
            left: 59.3mm;
            width: 18mm;
            height: 9.5mm;
        }

        .issue-date-mask {
            top: 40.5mm;
            left: 70.0mm;
            width: 15mm;
            height: 8mm;
        }

        .rx-mask {
            left: 7.8mm;
            width: 66mm;
            height: 6.8mm;
        }

        .rx-mask.one { top: 57.7mm; }
        .rx-mask.two { top: 66.5mm; }
        .rx-mask.three { top: 75.1mm; }
        .rx-mask.four { top: 83.4mm; }
        .rx-mask.five { top: 91.2mm; }

        .doctor-mask {
            top: 57.2mm;
            left: 108.8mm;
            width: 28.5mm;
            height: 24.5mm;
        }

        .footer-left-mask {
            top: 83.8mm;
            left: 6.0mm;
            width: 26mm;
            height: 9mm;
        }

        .footer-mid-mask {
            top: 83.8mm;
            left: 19.0mm;
            width: 58mm;
            height: 9mm;
        }

        .aut-mask {
            position: absolute;
            top: 57mm;
            left: 3.9mm;
            width: 7.5mm;
            height: 31mm;
            background: #b9dbf4;
            z-index: 1;
        }

        .date-text {
            top: 2.7mm;
            left: 3.8mm;
            font-size: 8.1pt;
            font-weight: 700;
        }

        .patient-name {
            top: 16.8mm;
            left: 11.4mm;
            width: 48mm;
            font-size: 8.8pt;
            font-weight: 700;
            line-height: 1.1;
        }

        .patient-address {
            top: 21.7mm;
            left: 11.4mm;
            width: 48mm;
            font-size: 7.7pt;
            line-height: 1.18;
            white-space: pre-line;
        }

        .patient-dob {
            top: 22.5mm;
            left: 68.7mm;
            width: 12.5mm;
            font-size: 8.1pt;
            text-align: center;
        }

        .issued-date {
            top: 43.1mm;
            left: 67.6mm;
            width: 14mm;
            font-size: 8.7pt;
            font-weight: 700;
            text-align: center;
        }

        .rx-row {
            position: absolute;
            left: 8.8mm;
            width: 92mm;
            z-index: 2;
        }

        .rx-name {
            font-size: 5.7pt;
            font-weight: 700;
            line-height: 1.08;
            margin-left: 5.8mm;
        }

        .rx-dose {
            font-size: 5.1pt;
            line-height: 1.08;
            margin-left: 5.8mm;
        }

        .doctor-block {
            position: absolute;
            top: 57.1mm;
            left: 106.6mm;
            width: 30mm;
            z-index: 2;
            text-align: center;
        }

        .doctor-name {
            font-size: 7.5pt;
            line-height: 1.05;
        }

        .doctor-role {
            font-size: 5.4pt;
            margin-top: 0.8mm;
        }

        .doctor-address,
        .doctor-phone,
        .doctor-lanr {
            font-size: 5.1pt;
            line-height: 1.08;
            white-space: pre-line;
        }

        .stamp {
            top: 67.3mm;
            left: 114.3mm;
            width: 19mm;
            height: 19mm;
            border: 0.35mm solid rgba(93, 125, 170, 0.75);
            border-radius: 50%;
            z-index: 2;
        }

        .stamp span {
            display: block;
            text-align: center;
            line-height: 18.2mm;
            font-family: Georgia, 'Times New Roman', serif;
            font-style: italic;
            font-size: 15pt;
            color: rgba(93, 125, 170, 0.78);
        }

        .pkvh {
            top: 81.8mm;
            left: 8.5mm;
            font-size: 15pt;
            letter-spacing: 0.4pt;
        }

        .valid-until {
            top: 85.9mm;
            left: 19.8mm;
            font-size: 6.1pt;
        }

        .receipt {
            top: 85.9mm;
            left: 59.9mm;
            font-size: 6.1pt;
        }

        .aut-idem {
            width: 5mm;
            height: 5mm;
            border: 0.25mm solid #5f91b6;
            background: #fff;
            color: #5f91b6;
            font-size: 4.1pt;
            line-height: 1.05;
            text-align: center;
            padding-top: 0.3mm;
        }
    </style>
</head>
<body>
@php
    $items = collect($medicines ?? [])->take(5)->values();

    $createdDate = isset($prescription) && $prescription->created_at
        ? $prescription->created_at->format('d.m.Y')
        : now()->format('d.m.Y');

    $validUntilFormatted = isset($prescription) && $prescription->valid_until
        ? $prescription->valid_until->format('d.m.Y')
        : (isset($valid_until) && $valid_until ? \Carbon\Carbon::parse($valid_until)->format('d.m.Y') : '');

    $patientName = trim((string) ($patient_name ?? (isset($prescription->user) ? $prescription->user->name : 'Patient')));
    $patientAddress = trim((string) ($patient_address ?? ''));
    $patientCity = trim((string) ($patient_city ?? ''));
    $patientCountry = trim((string) ($patient_country ?? 'Deutschland'));
    $patientDob = trim((string) ($patient_dob ?? (isset($prescription->user) && $prescription->user->dob ? \Carbon\Carbon::parse($prescription->user->dob)->format('d.m.Y') : '')));

    $doctorName = trim((string) ($doctor_name ?? (isset($prescription->doctor) ? (isset($prescription->doctor->user) ? $prescription->doctor->user->name : $prescription->doctor->name) : 'Arzt')));
    $doctorTitle = trim((string) ($doctor_title ?? 'Arztin'));
    $doctorAddress = trim((string) ($doctor_address ?? ''));
    $doctorPhone = trim((string) ($doctor_phone ?? ''));
    $doctorLanr = trim((string) ($doctor_lanr ?? ''));
    $receiptNr = trim((string) ($receipt_nr ?? ('RP' . str_pad((string) ($prescription->id ?? 0), 12, '0', STR_PAD_LEFT))));

    $patientLines = collect([$patientAddress, $patientCity, $patientCountry])
        ->filter(fn ($line) => filled(trim((string) $line)))
        ->implode("\n");

    $count = max(1, $items->count());
    $rowLayouts = match (true) {
        $count >= 5 => ['tops' => [58.4, 64.4, 70.4, 76.4, 82.4], 'name' => 5.2, 'dose' => 4.6],
        $count === 4 => ['tops' => [58.4, 65.3, 72.2, 79.1], 'name' => 5.4, 'dose' => 4.8],
        default => ['tops' => [58.4, 67.1, 75.8], 'name' => 5.7, 'dose' => 5.1],
    };
@endphp
<div class="page last">
    <img class="bg" src="{{ public_path('assets/prescription/prescription-reference.png') }}" alt="Prescription form">
    <div class="mask mask-blue top-date-mask"></div>
    <div class="mask mask-white patient-text-mask"></div>
    <div class="mask mask-white dob-mask"></div>
    <div class="mask mask-white issue-date-mask"></div>
    <div class="mask mask-blue rx-mask one"></div>
    <div class="mask mask-blue rx-mask two"></div>
    <div class="mask mask-blue rx-mask three"></div>
    @if($count >= 4)
        <div class="mask mask-blue rx-mask four"></div>
    @endif
    @if($count >= 5)
        <div class="mask mask-blue rx-mask five"></div>
    @endif
    <div class="mask mask-blue doctor-mask"></div>
    <div class="mask mask-blue footer-left-mask"></div>
    <div class="mask mask-blue footer-mid-mask"></div>
    <div class="aut-mask"></div>

    <div class="text date-text">{{ $createdDate }}</div>

    @if($patientName !== '')
        <div class="text patient-name">{{ $patientName }}</div>
    @endif

    @if($patientLines !== '')
        <div class="text patient-address">{{ $patientLines }}</div>
    @endif

    @if($patientDob !== '')
        <div class="text patient-dob">{{ $patientDob }}</div>
    @endif

    <div class="text issued-date">{{ $createdDate }}</div>

    @foreach($items as $index => $item)
        @php
            $medicine = trim((string) data_get($item, 'medicine', ''));
            $strength = trim((string) data_get($item, 'strength', ''));
            $qty = trim((string) data_get($item, 'qty', ''));
            $ed = trim((string) data_get($item, 'ed', ''));
            $tdFreq = trim((string) data_get($item, 'td_freq', ''));

            $nameParts = array_values(array_filter([$medicine, $strength, $qty, 'Unzerkleinert']));
            $doseParts = [];
            if ($ed !== '') {
                $doseParts[] = 'ED: ' . $ed;
            }
            if ($tdFreq !== '') {
                $doseParts[] = 'TD: Bis zu ' . $tdFreq;
            }
            $doseLine = count($doseParts)
                ? 'Dosierung: ' . implode(', ', $doseParts) . ', verdampfen und inhalieren'
                : 'Dosierung: nach Anweisung, verdampfen und inhalieren';
            $top = $rowLayouts['tops'][$index] ?? end($rowLayouts['tops']);
        @endphp
        <div class="aut-idem" style="top: {{ $top + 0.1 }}mm; left: 4.9mm;">aut<br>idem</div>
        <div class="rx-row" style="top: {{ $top }}mm;">
            <div class="rx-name" style="font-size: {{ $rowLayouts['name'] }}pt;">Cannabisbluten: "{{ implode(', ', $nameParts) }}"</div>
            <div class="rx-dose" style="font-size: {{ $rowLayouts['dose'] }}pt;">{{ $doseLine }}</div>
        </div>
    @endforeach

    @if($items->isEmpty())
        <div class="rx-row" style="top: 58.4mm;">
            <div class="rx-name">Keine Medikamente hinterlegt.</div>
        </div>
    @endif

    <div class="doctor-block">
        <div class="doctor-name">{{ $doctorName }}</div>
        <div class="doctor-role">{{ $doctorTitle }}</div>
        @if($doctorAddress !== '')
            <div class="doctor-address">{{ $doctorAddress }}</div>
        @endif
        @if($doctorPhone !== '')
            <div class="doctor-phone">Telefon: {{ $doctorPhone }}</div>
        @endif
        @if($doctorLanr !== '')
            <div class="doctor-lanr">LANR: {{ $doctorLanr }}</div>
        @endif
    </div>

    <div class="stamp"><span>R</span></div>

    <div class="text pkvh">PKVH</div>
    @if($validUntilFormatted !== '')
        <div class="text valid-until">Gultig bis {{ $validUntilFormatted }}</div>
    @endif
    <div class="text receipt">RezeptNr: {{ $receiptNr }}</div>
</div>
</body>
</html>
