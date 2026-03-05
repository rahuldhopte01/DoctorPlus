<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privatrezept</title>
    <style>
        /* Compact prescription slip – fits A6 (105×148mm) */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 9px;
        }
        .prescription-container {
            width: 380px;
            min-height: 530px;
            background-color: #a4ceef;
            position: relative;
            box-sizing: border-box;
            border: 1px solid #5a87a8;
        }
        .box {
            border: 1px solid #336699;
            background-color: white;
            position: absolute;
            box-sizing: border-box;
        }
        .label {
            font-size: 6px;
            color: #336699;
            position: absolute;
        }
        .top-left-date {
            position: absolute;
            top: 5px;
            left: 8px;
            font-size: 8px;
            font-family: monospace;
            color: #000;
        }
        .patient-box {
            top: 21px;
            left: 10px;
            width: 180px;
            height: 57px;
        }
        .patient-box .privat-title {
            font-size: 10px;
            font-weight: bold;
            margin: 2px 0 0 5px;
        }
        .patient-box .patient-label {
            font-size: 6px;
            color: #336699;
            margin: 1px 0 0 5px;
        }
        .patient-box .patient-info {
            font-size: 8px;
            margin: 4px 0 0 5px;
            line-height: 1.15;
        }
        .patient-box .geb-am {
            position: absolute;
            top: 28px;
            right: 5px;
            text-align: right;
        }
        .patient-box .geb-am .label { position: static; font-size: 6px; }
        .patient-box .geb-am .date { font-size: 8px; font-weight: bold; }
        .ins-box {
            top: 78px;
            left: 10px;
            width: 180px;
            height: 19px;
            display: flex;
        }
        .ins-box > div {
            border-right: 1px solid #336699;
            flex: 1;
            position: relative;
        }
        .ins-box > div:last-child { border-right: none; }
        .ins-box .label { top: 1px; left: 3px; font-size: 6px; }
        .doc-box {
            top: 97px;
            left: 10px;
            width: 180px;
            height: 19px;
            display: flex;
        }
        .doc-box > div {
            border-right: 1px solid #336699;
            flex: 1;
            position: relative;
        }
        .doc-box > div:last-child { border-right: none; }
        .doc-box .label { top: 1px; left: 3px; font-size: 6px; }
        .doc-box .val { position: absolute; bottom: 2px; left: 3px; font-size: 8px; }
        .doc-box .right-val { position: absolute; bottom: 2px; right: 5px; font-size: 8px; font-weight: bold; }
        .unfall-box {
            top: 83px;
            left: 5px;
            font-size: 5px;
            border: 1px solid #336699;
            background: white;
            padding: 1px;
            position: absolute;
        }
        .top-right-group {
            position: absolute;
            top: 14px;
            right: 10px;
            width: 166px;
        }
        .bezugsdatum {
            position: absolute;
            top: 0;
            left: 0;
            width: 71px;
            height: 17px;
        }
        .apotheken-nummer {
            position: absolute;
            top: 0;
            right: 0;
            width: 85px;
            height: 17px;
        }
        .bezugsdatum .label, .apotheken-nummer .label { top: -8px; left: 3px; font-size: 5px; }
        .gesamt-brutto {
            position: absolute;
            top: 21px;
            right: 0;
            width: 104px;
            height: 19px;
            display: flex;
        }
        .gesamt-brutto .label { top: -8px; left: 0; font-size: 5px; }
        .gv-cell { flex: 1; border-right: 1px dashed #336699; height: 100%; position: relative; }
        .gv-cell:last-child { border-right: none; }
        .gv-cell:nth-child(4) { border-right: 1px solid #336699; }
        .gv-cell::after {
            content: ''; position: absolute; bottom: -2px; left: 50%; transform: translateX(-50%);
            border-left: 2px solid transparent; border-right: 2px solid transparent; border-bottom: 2px solid #336699;
        }
        .arzn-grid {
            position: absolute;
            top: 47px;
            left: 0;
            width: 166px;
            height: 57px;
            background: rgba(255,255,255,0.7);
            border: 1px solid #336699;
            display: grid;
            grid-template-columns: 95px 24px 47px;
            grid-template-rows: 10px 16px 16px 16px;
        }
        .arzn-grid > div {
            border-right: 1px solid #336699;
            border-bottom: 1px solid #336699;
            position: relative;
        }
        .arzn-grid > div:nth-child(3n) { border-right: none; }
        .arzn-grid > div:nth-last-child(-n+3) { border-bottom: none; }
        .arzn-header { font-size: 5px; color: #336699; padding: 1px 2px; }
        .arzn-item { display: flex; }
        .arzn-item .t-cell { flex: 1; border-right: 1px dashed #336699; position: relative; }
        .arzn-item .t-cell:last-child { border-right: none; }
        .arzn-item .t-cell::after {
            content: ''; position: absolute; bottom: -2px; left: 50%; transform: translateX(-50%);
            border-left: 2px solid transparent; border-right: 2px solid transparent; border-bottom: 2px solid #336699;
        }
        .rp-area {
            position: absolute;
            top: 123px;
            left: 10px;
            width: 360px;
            min-height: 118px;
        }
        .rp-title {
            font-size: 10px;
            font-weight: bold;
            color: #336699;
            margin-bottom: 4px;
        }
        .rp-title span { font-size: 5px; font-weight: normal; }
        .med-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 6px;
            font-size: 7px;
            font-family: Arial, sans-serif;
        }
        .aut-idem {
            width: 12px;
            height: 12px;
            border: 1px solid #336699;
            background: white;
            margin-right: 5px;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            align-items: center;
            font-size: 4px;
            color: #336699;
            padding: 0 0;
            box-sizing: border-box;
            border-radius: 1px;
            flex-shrink: 0;
        }
        .med-text { flex: 1; }
        .med-text div:first-child { margin-bottom: 1px; }
        .doctor-info {
            position: absolute;
            bottom: 18px;
            right: 10px;
            text-align: center;
            font-size: 6px;
            width: 118px;
        }
        .doctor-name { font-size: 8px; margin-bottom: 1px; }
        .doctor-title { margin-bottom: 6px; }
        .doctor-address { font-size: 5px; line-height: 1.25; }
        .signature-line {
            position: absolute;
            bottom: 4px;
            right: 10px;
            font-size: 5px;
            color: #336699;
            width: 118px;
            text-align: center;
        }
        .footer-info {
            position: absolute;
            bottom: 4px;
            left: 10px;
            font-size: 6px;
            display: flex;
            gap: 70px;
            align-items: center;
        }
        .pkv { font-size: 10px; letter-spacing: 1px; }
        .squiggle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 38px;
            height: 28px;
            opacity: 0.5;
            pointer-events: none;
        }
        .squiggle svg { width: 100%; height: 100%; }
    </style>
</head>
<body>

@php
    $items = $medicines ?? [];
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

<div class="prescription-container">
    <div class="top-left-date">{{ $createdDate }}</div>

    <div class="box patient-box">
        <div class="privat-title">Privat</div>
        <div class="patient-label">Name, Vorname des Versicherten</div>
        <div class="patient-info">
            <strong>{{ $patientName }}</strong><br>
            @if($patientAddress){{ $patientAddress }}<br>
            @endif
            @if($patientCity){{ $patientCity }}<br>
            @endif
            @if($patientCountry){{ $patientCountry }}
            @endif
        </div>
        @if($patientDob)
        <div class="geb-am">
            <div class="label">geb. am</div>
            <div class="date">{{ $patientDob }}</div>
        </div>
        @endif
    </div>

    <div class="unfall-box">Unfall</div>

    <div class="box ins-box">
        <div><div class="label">Versicherungsnummer</div></div>
        <div><div class="label">Personennummer</div></div>
    </div>

    <div class="box doc-box">
        <div><div class="label">Arzt-Nr.</div>{!! $doctorLanr ? '<div class="val">'.e($doctorLanr).'</div>' : '' !!}</div>
        <div>
            <div class="label">Datum</div>
            <div class="right-val">{{ $createdDate }}</div>
        </div>
    </div>

    <div class="top-right-group">
        <div class="box bezugsdatum">
            <div class="label">Bezugsdatum</div>
        </div>
        <div class="box apotheken-nummer">
            <div class="label">Apotheken-Nummer / IK</div>
        </div>
        <div class="box gesamt-brutto">
            <div class="label">Gesamt-Brutto</div>
            <div class="gv-cell"></div><div class="gv-cell"></div><div class="gv-cell"></div><div class="gv-cell"></div>
            <div class="gv-cell"></div><div class="gv-cell"></div><div class="gv-cell"></div>
        </div>
        <div class="arzn-grid">
            <div class="arzn-header">Arzneimittel-/Hilfsmittel-/Heilmittel-Nr.</div>
            <div class="arzn-header">Faktor</div>
            <div class="arzn-header">Taxe</div>
            @for ($i = 0; $i < 3; $i++)
            <div class="arzn-item">
                <div class="t-cell"></div><div class="t-cell"></div><div class="t-cell"></div><div class="t-cell"></div><div class="t-cell"></div><div class="t-cell"></div><div class="t-cell"></div>
            </div>
            <div></div><div></div>
            @endfor
        </div>
    </div>

    <div class="rp-area">
        <div class="rp-title">Rp. <span>(Bitte Leerräume durchstreichen)</span></div>

        @forelse ($items as $item)
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
                    ? 'Dosierung: ED: ' . $ed . ', TD: Bis zu ' . $tdFreq
                    : ($frequency ? 'Dosierung: ' . $frequency . ($days ? ' | Tage: ' . $days : '') : 'Dosierung: ' . ($days ? 'Tage: ' . $days : '-'));
            @endphp
            <div class="med-item">
                <div class="aut-idem"><span>aut</span><span>idem</span></div>
                <div class="med-text">
                    <div>{{ $medicine }}{{ $strength ? ' (' . $strength . ')' : '' }}{{ $qty ? ', ' . $qty : '' }}</div>
                    <div>{{ $dosageLine }}</div>
                </div>
            </div>
        @empty
            <div class="med-item">
                <div class="aut-idem"><span>aut</span><span>idem</span></div>
                <div class="med-text"><div>Keine Medikamente hinterlegt.</div></div>
            </div>
        @endforelse

        <div class="doctor-info">
            <div class="doctor-name">{{ $doctorName }}</div>
            <div class="doctor-title">{{ $doctorTitle }}</div>
            <div class="doctor-address">
                @if($doctorAddress){!! nl2br(e($doctorAddress)) !!}<br>
                @endif
                @if($doctorPhone)Telefon: {{ $doctorPhone }}<br>
                @endif
                @if($doctorLanr)LANR: {{ $doctorLanr }}
                @endif
            </div>
            <div class="squiggle">
                <svg viewBox="0 0 100 50">
                    <path d="M10,40 Q20,10 30,30 T50,20 T70,40 T90,20" fill="transparent" stroke="#336699" stroke-width="2"/>
                </svg>
            </div>
        </div>
        <div class="signature-line">Arztstempel/Unterschrift des Arztes</div>
    </div>

    <div class="footer-info">
        <div class="pkv">PKV H</div>
        @if($validUntil)
        <div>Gültig bis {{ $validUntil }}</div>
        @endif
        <div>RezeptNr: {{ $receiptNr }}</div>
    </div>
</div>

</body>
</html>
