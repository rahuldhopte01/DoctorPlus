<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Privatrezept</title>
    <style>
        @page { margin: 10px; }
        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #000;
        }

        .page {
            position: relative;
            width: 700px;
            height: 430px;
            background: #9fc7e5;
            border: 2px solid #3f7fb7;
            margin: 0 auto 10px auto;
            page-break-after: always;
            overflow: hidden;
        }
        .page.last { page-break-after: auto; }

        .box {
            border: 2px solid #4b86bd;
            background: #fff;
            box-sizing: border-box;
        }
        .label {
            color: #2f6fa5;
            font-size: 10px;
        }

        .date-top {
            position: absolute;
            top: 8px;
            left: 10px;
            font-size: 16px;
        }

        .patient {
            position: absolute;
            top: 38px;
            left: 10px;
            width: 390px;
            height: 152px;
            padding: 8px 10px;
        }
        .patient .title {
            font-size: 39px;
            font-weight: 700;
            line-height: 1;
        }
        .patient .meta { margin-top: 2px; }
        .patient .name {
            font-size: 15px;
            line-height: 1.15;
            margin-top: 4px;
            width: 250px;
        }
        .birth {
            position: absolute;
            right: 10px;
            top: 72px;
            text-align: right;
        }
        .birth .val {
            font-size: 31px;
            line-height: 1;
        }

        .unfall {
            position: absolute;
            top: 155px;
            left: 2px;
            width: 24px;
            height: 42px;
            border: 2px solid #4b86bd;
            background: #fff;
            color: #2f6fa5;
            text-align: center;
            font-size: 8px;
            line-height: 1.05;
            padding-top: 8px;
        }

        .ins,
        .doc {
            position: absolute;
            left: 10px;
            width: 390px;
            border: 2px solid #4b86bd;
            border-top: 0;
            background: #f5f5f5;
            border-collapse: collapse;
        }
        .ins { top: 190px; }
        .doc { top: 228px; }
        .ins td,
        .doc td {
            border-right: 2px solid #4b86bd;
            height: 36px;
            padding: 4px 10px;
            vertical-align: top;
            position: relative;
        }
        .ins td:last-child,
        .doc td:last-child { border-right: 0; }
        .tick {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 1px;
            height: 18px;
            background: #4b86bd;
        }
        .doc-date {
            position: absolute;
            right: 10px;
            bottom: 4px;
            font-size: 32px;
            line-height: 1;
        }
        .doc-number {
            position: absolute;
            left: 10px;
            bottom: 4px;
            font-size: 16px;
            line-height: 1;
        }

        .right {
            position: absolute;
            top: 38px;
            left: 412px;
            width: 278px;
        }
        .smallbox {
            display: inline-block;
            vertical-align: top;
            height: 40px;
            padding: 3px 8px;
            border: 2px solid #4b86bd;
            background: #fff;
            box-sizing: border-box;
        }
        .bez { width: 104px; }
        .apo { width: 164px; margin-left: 6px; }

        .gross {
            margin-top: 8px;
            margin-left: 104px;
            width: 174px;
            height: 37px;
            border: 2px solid #4b86bd;
            background: #fff;
            border-collapse: collapse;
        }
        .gross td {
            border-right: 1px dashed #4b86bd;
            width: 12.5%;
        }
        .gross td:last-child { border-right: 0; }

        .grid {
            margin-top: 8px;
            width: 278px;
            border: 2px solid #4b86bd;
            border-collapse: collapse;
            background: #fff;
        }
        .grid th,
        .grid td {
            border-right: 2px solid #4b86bd;
            border-bottom: 2px solid #4b86bd;
            padding: 4px 8px;
            color: #2f6fa5;
            font-size: 10px;
            text-align: left;
            vertical-align: top;
            height: 38px;
        }
        .grid th { height: 28px; font-weight: normal; }
        .grid tr:last-child td { border-bottom: 0; }
        .grid th:last-child,
        .grid td:last-child { border-right: 0; }
        .dash {
            margin-top: 16px;
            border-top: 1px dashed #4b86bd;
            height: 0;
        }

        .rx {
            position: absolute;
            top: 268px;
            left: 10px;
            width: 470px;
        }
        .rx-title {
            color: #2f6fa5;
            font-size: 32px;
            line-height: 1;
            margin-bottom: 6px;
        }
        .rx-title span {
            font-size: 14px;
            font-weight: normal;
        }

        .med {
            margin-bottom: 10px;
            font-size: 11px;
            line-height: 1.25;
        }
        .aut {
            display: inline-block;
            width: 24px;
            height: 28px;
            border: 2px solid #4b86bd;
            background: #fff;
            text-align: center;
            color: #2f6fa5;
            font-size: 9px;
            line-height: 1.05;
            padding-top: 4px;
            vertical-align: top;
        }
        .med-text {
            display: inline-block;
            width: 436px;
            margin-left: 6px;
            vertical-align: top;
        }

        .doctor {
            position: absolute;
            right: 20px;
            bottom: 42px;
            width: 210px;
            text-align: center;
            font-size: 12px;
            line-height: 1.2;
        }
        .doctor .name {
            font-size: 42px;
            line-height: 1;
        }
        .doctor .sign {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 66px;
            line-height: 0.8;
            margin-top: -2px;
            margin-bottom: -8px;
        }
        .doctor-ring {
            position: absolute;
            left: 50%;
            top: 56%;
            width: 66px;
            height: 66px;
            margin-left: -33px;
            margin-top: -33px;
            border: 1px solid rgba(51, 78, 130, 0.45);
            border-radius: 50%;
        }

        .footer {
            position: absolute;
            left: 10px;
            right: 10px;
            bottom: 10px;
            font-size: 10px;
        }
        .pkv {
            float: left;
            font-size: 45px;
            line-height: 0.9;
        }
        .valid {
            float: left;
            margin-left: 24px;
            margin-top: 18px;
            font-size: 28px;
        }
        .receipt {
            float: left;
            margin-left: 28px;
            margin-top: 18px;
            font-size: 28px;
        }
        .footer-sign {
            float: right;
            margin-top: 24px;
            color: #2f6fa5;
            font-size: 10px;
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

    <div class="unfall">Unfal<br>l</div>

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
        <div class="smallbox apo"><span class="label">Apotheken-Nummer / IK</span></div>

        <table class="gross">
            <tr>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
        </table>

        <table class="grid">
            <tr>
                <th>Arzneimittel-/Hilfsmittel-/Heilmittel-Nr.</th>
                <th>Faktor</th>
                <th>Taxe</th>
            </tr>
            <tr><td><div class="dash"></div></td><td><div class="dash"></div></td><td><div class="dash"></div></td></tr>
            <tr><td><div class="dash"></div></td><td><div class="dash"></div></td><td><div class="dash"></div></td></tr>
            <tr><td><div class="dash"></div></td><td><div class="dash"></div></td><td><div class="dash"></div></td></tr>
        </table>
    </div>

    <div class="rx">
        <div class="rx-title">Rp.<span>(Bitte Leerräume durchstreichen)</span></div>

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
        @if($doctorPhone)<div>Telefon: {{ $doctorPhone }}</div>@endif
        @if($doctorLanr)<div>LANR: {{ $doctorLanr }}</div>@endif
    </div>

    <div class="footer clearfix">
        <div class="pkv">PKVH</div>
        @if($validUntil)<div class="valid">Gültig bis {{ $validUntil }}</div>@endif
        <div class="receipt">RezeptNr: {{ $receiptNr }}</div>
        <div class="footer-sign">Arztstempel/Unterschrift des Arztes</div>
    </div>
</div>
@endforeach
</body>
</html>
