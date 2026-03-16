<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Privatrezept</title>
    <style>
        @page {
            size: 105mm 148mm;
            margin: 3mm;
        }
        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 7pt;
            color: #000;
        }

        .page {
            position: relative;
            width: 99mm;
            height: 142mm;
            background: #9fc7e5;
            border: 1pt solid #3f7fb7;
            margin: 0 auto;
            page-break-after: always;
            overflow: hidden;
            box-sizing: border-box;
        }
        .page.last { page-break-after: auto; }

        .box {
            border: 1pt solid #4b86bd;
            background: #fff;
            box-sizing: border-box;
        }
        .label {
            color: #2f6fa5;
            font-size: 5.5pt;
        }

        /* Date top-left */
        .date-top {
            position: absolute;
            top: 2mm;
            left: 2mm;
            font-size: 7pt;
        }

        /* Patient info box — left column, top section */
        .patient {
            position: absolute;
            top: 7mm;
            left: 2mm;
            width: 56mm;
            height: 51mm;
            padding: 2mm 2mm;
            box-sizing: border-box;
        }
        .patient .title {
            font-size: 16pt;
            font-weight: 700;
            line-height: 1;
        }
        .patient .meta {
            margin-top: 0.5mm;
        }
        .patient .name {
            font-size: 7pt;
            line-height: 1.2;
            margin-top: 1mm;
            width: 34mm;
        }
        .birth {
            position: absolute;
            right: 2mm;
            top: 20mm;
            text-align: right;
        }
        .birth .val {
            font-size: 9pt;
            line-height: 1;
        }

        /* Unfall (accident) side tab */
        .unfall {
            position: absolute;
            top: 46mm;
            left: 0;
            width: 3.5mm;
            height: 14mm;
            border: 1pt solid #4b86bd;
            background: #fff;
            color: #2f6fa5;
            text-align: center;
            font-size: 4.5pt;
            line-height: 1.1;
            padding-top: 2mm;
            box-sizing: border-box;
        }

        /* Insurance and doctor info rows — left column, below patient */
        .ins,
        .doc {
            position: absolute;
            left: 2mm;
            width: 56mm;
            border: 1pt solid #4b86bd;
            border-top: 0;
            background: #f5f5f5;
            border-collapse: collapse;
        }
        .ins { top: 58mm; }
        .doc { top: 70mm; }
        .ins td,
        .doc td {
            border-right: 1pt solid #4b86bd;
            height: 10mm;
            padding: 1mm 2mm;
            vertical-align: top;
            position: relative;
            font-size: 5.5pt;
        }
        .ins td:last-child,
        .doc td:last-child { border-right: 0; }
        .tick {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0.5pt;
            height: 4mm;
            background: #4b86bd;
        }
        .doc-date {
            position: absolute;
            right: 2mm;
            bottom: 1mm;
            font-size: 8pt;
            line-height: 1;
        }
        .doc-number {
            position: absolute;
            left: 2mm;
            bottom: 1mm;
            font-size: 6pt;
            line-height: 1;
        }

        /* Right pharmacy/billing column */
        .right {
            position: absolute;
            top: 7mm;
            left: 60mm;
            width: 39mm;
        }
        .smallbox {
            display: inline-block;
            vertical-align: top;
            height: 11mm;
            padding: 1.5mm;
            border: 1pt solid #4b86bd;
            background: #fff;
            box-sizing: border-box;
            font-size: 5pt;
        }
        .bez { width: 15mm; }
        .apo { width: 23mm; margin-left: 1mm; }

        /* Zuzahlungsbeleg grid (8-box copayment receipt) */
        .gross {
            margin-top: 2mm;
            margin-left: 16mm;
            width: 23mm;
            height: 10mm;
            border: 1pt solid #4b86bd;
            background: #fff;
            border-collapse: collapse;
        }
        .gross td {
            border-right: 0.5pt dashed #4b86bd;
            width: 12.5%;
        }
        .gross td:last-child { border-right: 0; }

        /* Pharmacy billing grid */
        .grid {
            margin-top: 2mm;
            width: 39mm;
            border: 1pt solid #4b86bd;
            border-collapse: collapse;
            background: #fff;
        }
        .grid th,
        .grid td {
            border-right: 1pt solid #4b86bd;
            border-bottom: 1pt solid #4b86bd;
            padding: 1mm;
            color: #2f6fa5;
            font-size: 4.5pt;
            text-align: left;
            vertical-align: top;
            height: 8mm;
        }
        .grid th { height: 7mm; font-weight: normal; }
        .grid tr:last-child td { border-bottom: 0; }
        .grid th:last-child,
        .grid td:last-child { border-right: 0; }
        .dash {
            margin-top: 3mm;
            border-top: 0.5pt dashed #4b86bd;
            height: 0;
        }

        /* Prescription (Rp.) / medicines section */
        .rx {
            position: absolute;
            top: 82mm;
            left: 2mm;
            width: 64mm;
        }
        .rx-title {
            color: #2f6fa5;
            font-size: 11pt;
            line-height: 1;
            margin-bottom: 2mm;
        }
        .rx-title span {
            font-size: 5pt;
            font-weight: normal;
        }

        .med {
            margin-bottom: 2mm;
            font-size: 6pt;
            line-height: 1.3;
        }
        .aut {
            display: inline-block;
            width: 5.5mm;
            height: 8mm;
            border: 1pt solid #4b86bd;
            background: #fff;
            text-align: center;
            color: #2f6fa5;
            font-size: 4pt;
            line-height: 1.1;
            padding-top: 1mm;
            vertical-align: top;
            box-sizing: border-box;
        }
        .med-text {
            display: inline-block;
            width: 57mm;
            margin-left: 1.5mm;
            vertical-align: top;
        }

        /* Doctor stamp / signature */
        .doctor {
            position: absolute;
            right: 3mm;
            bottom: 14mm;
            width: 30mm;
            text-align: center;
            font-size: 6pt;
            line-height: 1.2;
        }
        .doctor .name {
            font-size: 9pt;
            line-height: 1;
        }
        .doctor .sign {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 18pt;
            line-height: 0.8;
            margin-top: -1mm;
            margin-bottom: -2mm;
        }
        .doctor-ring {
            position: absolute;
            left: 50%;
            top: 56%;
            width: 14mm;
            height: 14mm;
            margin-left: -7mm;
            margin-top: -7mm;
            border: 0.5pt solid rgba(51, 78, 130, 0.45);
            border-radius: 50%;
        }

        /* Footer */
        .footer {
            position: absolute;
            left: 2mm;
            right: 2mm;
            bottom: 2mm;
            font-size: 6pt;
        }
        .pkv {
            float: left;
            font-size: 14pt;
            line-height: 0.9;
        }
        .valid {
            float: left;
            margin-left: 3mm;
            margin-top: 3mm;
            font-size: 6.5pt;
        }
        .receipt {
            float: left;
            margin-left: 3mm;
            margin-top: 3mm;
            font-size: 6.5pt;
        }
        .footer-sign {
            float: right;
            margin-top: 4mm;
            color: #2f6fa5;
            font-size: 5pt;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
@php
    $items = collect($medicines ?? []);
    $chunks = $items->chunk(3);
    if ($chunks->isEmpty()) {
        $chunks = collect([collect()]);
    }

    $createdDate = isset($prescription) && $prescription->created_at
        ? $prescription->created_at->format('d.m.Y')
        : now()->format('d.m.Y');
    $validUntil = isset($prescription) && $prescription->valid_until
        ? $prescription->valid_until->format('d.m.Y')
        : (isset($valid_until) ? \Carbon\Carbon::parse($valid_until)->format('d.m.Y') : '');

    $patientName = $patient_name ?? (isset($prescription->user) && $prescription->user ? $prescription->user->name : 'Patient');
    $patientAddress = $patient_address ?? '';
    $patientCity = $patient_city ?? '';
    $patientCountry = $patient_country ?? 'Deutschland';
    $patientDob = $patient_dob ?? (isset($prescription->user) && $prescription->user && $prescription->user->dob ? \Carbon\Carbon::parse($prescription->user->dob)->format('d.m.Y') : '');

    $doctorName = $doctor_name ?? (isset($prescription->doctor) && $prescription->doctor && $prescription->doctor->user ? $prescription->doctor->user->name : (isset($prescription->doctor) && $prescription->doctor ? $prescription->doctor->name : 'Doctor'));
    $doctorTitle = $doctor_title ?? 'Arzt/Ärztin';
    $doctorAddress = $doctor_address ?? '';
    $doctorPhone = $doctor_phone ?? '';
    $doctorLanr = $doctor_lanr ?? '';
    $receiptNr = $receipt_nr ?? ('RP' . str_pad($prescription->id ?? 0, 12, '0', STR_PAD_LEFT));
@endphp

@foreach($chunks as $chunkIndex => $chunk)
<div class="page {{ $loop->last ? 'last' : '' }}">
    <div class="date-top">{{ $createdDate }}</div>

    <div class="box patient">
        <div class="title">Privat</div>
        <div class="meta label">Name, Vorname des Versicherten</div>
        <div class="name">
            {{ $patientName }}<br>
            @if($patientAddress){{ $patientAddress }}<br>@endif
            @if($patientCity){{ $patientCity }}<br>@endif
            @if($patientCountry){{ $patientCountry }}@endif
        </div>
        @if($patientDob)
        <div class="birth">
            <div class="label">geb. am</div>
            <div class="val">{{ $patientDob }}</div>
        </div>
        @endif
    </div>

    <div class="unfall">Unfall</div>

    <table class="ins">
        <tr>
            <td><span class="label">Versicherungsnummer</span><span class="tick"></span></td>
            <td><span class="label">Personennummer</span><span class="tick"></span></td>
        </tr>
    </table>

    <table class="doc">
        <tr>
            <td>
                <span class="label">Arzt-Nr.</span>
                <span class="tick"></span>
                @if($doctorLanr)
                    <div class="doc-number">{{ $doctorLanr }}</div>
                @endif
            </td>
            <td>
                <span class="label">Datum</span>
                <span class="tick"></span>
                <div class="doc-date">{{ $createdDate }}</div>
            </td>
        </tr>
    </table>

    <div class="right">
        <div class="smallbox bez"><span class="label">Bezugsdatum</span></div>
        <div class="smallbox apo"><span class="label">Apotheken-Nr. / IK</span></div>

        <table class="gross">
            <tr>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
        </table>

        <table class="grid">
            <tr>
                <th>AM-/HM-/HeilM-Nr.</th>
                <th>Faktor</th>
                <th>Taxe</th>
            </tr>
            <tr><td><div class="dash"></div></td><td><div class="dash"></div></td><td><div class="dash"></div></td></tr>
            <tr><td><div class="dash"></div></td><td><div class="dash"></div></td><td><div class="dash"></div></td></tr>
            <tr><td><div class="dash"></div></td><td><div class="dash"></div></td><td><div class="dash"></div></td></tr>
        </table>
    </div>

    <div class="rx">
        <div class="rx-title">Rp.<span> (Bitte Leerräume durchstreichen)</span></div>

        @forelse($chunk as $item)
            @php
                $medicine = data_get($item, 'medicine', '-');
                $strength = data_get($item, 'strength', '');
                $qty = data_get($item, 'qty', '');
                $ed = data_get($item, 'ed', '');
                $tdFreq = data_get($item, 'td_freq', '');
                $days = data_get($item, 'days', '');
                $morning = data_get($item, 'morning', null);
                $afternoon = data_get($item, 'afternoon', null);
                $night = data_get($item, 'night', null);
                $hasFrequency = $morning !== null || $afternoon !== null || $night !== null;
                $frequency = $hasFrequency
                    ? (($morning ? '1' : '0') . ' / ' . ($afternoon ? '1' : '0') . ' / ' . ($night ? '1' : '0'))
                    : '';
                $dosageLine = $ed && $tdFreq
                    ? ('Dosierung: ED: ' . $ed . ', TD: Bis zu ' . $tdFreq)
                    : ($frequency
                        ? ('Dosierung: ' . $frequency . ($days ? ' | Tage: ' . $days : ''))
                        : ('Dosierung: ' . ($days ? 'Tage: ' . $days : '-')));
            @endphp
            <div class="med">
                <span class="aut">aut<br>idem</span>
                <span class="med-text">
                    {{ $medicine }}{{ $strength ? ' (' . $strength . ')' : '' }}{{ $qty ? ', ' . $qty : '' }}<br>
                    {{ $dosageLine }}, verdampfen und inhalieren
                </span>
            </div>
        @empty
            <div class="med">
                <span class="aut">aut<br>idem</span>
                <span class="med-text">Keine Medikamente hinterlegt.</span>
            </div>
        @endforelse
    </div>

    <div class="doctor">
        <div class="name">{{ $doctorName }}</div>
        <div>{{ $doctorTitle }}</div>
        <div class="sign">R</div>
        <div class="doctor-ring"></div>
        @if($doctorAddress)<div>{!! nl2br(e($doctorAddress)) !!}</div>@endif
        @if($doctorPhone)<div>Tel: {{ $doctorPhone }}</div>@endif
        @if($doctorLanr)<div>LANR: {{ $doctorLanr }}</div>@endif
    </div>

    <div class="footer clearfix">
        <div class="pkv">PKVH</div>
        @if($validUntil)<div class="valid">Gültig bis {{ $validUntil }}</div>@endif
        <div class="receipt">RezeptNr: {{ $receiptNr }}</div>
        <div class="footer-sign">Arztstempel/Unterschrift</div>
    </div>
</div>
@endforeach
</body>
</html>
