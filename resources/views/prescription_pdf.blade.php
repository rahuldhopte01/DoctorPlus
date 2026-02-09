<html>
    <head>
        <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1a1a1a; margin: 0; }
        .page {
            background: #cfe3f4;
            padding: 18px 18px 20px 18px;
            border: 1px solid #4a76a8;
        }
        .title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 8px;
        }
        .title { font-size: 16px; font-weight: bold; }
        .date { font-size: 11px; }
        .row { display: flex; gap: 10px; margin-bottom: 8px; }
        .box {
            border: 1px solid #4a76a8;
            background: #d9e8f6;
            padding: 8px;
            box-sizing: border-box;
        }
        .box.patient { flex: 2; min-height: 90px; }
        .box.meta { flex: 1; min-height: 90px; }
        .label { font-size: 9px; color: #2e4f73; }
        .line { border-bottom: 1px solid #4a76a8; min-height: 14px; margin-bottom: 4px; }
        .small-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
        .rx { flex: 2; min-height: 220px; }
        .rx-header { display: flex; gap: 6px; align-items: baseline; margin-bottom: 6px; }
        .rx-label { font-weight: bold; }
        .rx-lines { border-top: 1px solid #4a76a8; padding-top: 6px; min-height: 170px; }
        .rx-item { margin-bottom: 8px; }
        .rx-item .name { font-weight: bold; }
        .sign { flex: 1; min-height: 220px; display: flex; flex-direction: column; justify-content: flex-end; }
        .sign-box { border-top: 1px solid #4a76a8; padding-top: 8px; }
        .sign-name { font-weight: bold; }
        </style>
    </head>
    <body>
        @php
            $items = $medicines ?? [];
            $createdDate = isset($prescription) && $prescription->created_at
                ? $prescription->created_at->format('d.m.Y')
                : now()->format('d.m.Y');
            $doctorName = $doctor_name ?? 'Doctor';
            $patientName = $patient_name ?? 'Patient';
        @endphp
        <div class="page">
            <div class="title-row">
                <div class="title">Privatrezept</div>
                <div class="date">{{ $createdDate }}</div>
            </div>

            <div class="row">
                <div class="box patient">
                    <div class="label">Name, Vorname des Versicherten</div>
                    <div class="line">{{ $patientName }}</div>
                    <div class="label">Adresse</div>
                    <div class="line">&nbsp;</div>
                    <div class="label">Versicherungsnummer / Personennummer</div>
                    <div class="line">&nbsp;</div>
                </div>
                <div class="box meta">
                    <div class="small-grid">
                        <div>
                            <div class="label">Bezugsdatum</div>
                            <div class="line">&nbsp;</div>
                        </div>
                        <div>
                            <div class="label">Apotheken-Nummer / IK</div>
                            <div class="line">&nbsp;</div>
                        </div>
                        <div>
                            <div class="label">Gesamt-Brutto</div>
                            <div class="line">&nbsp;</div>
                        </div>
                        <div>
                            <div class="label">Zuzahlung</div>
                            <div class="line">&nbsp;</div>
                        </div>
                        <div>
                            <div class="label">Arzt-Nr.</div>
                            <div class="line">&nbsp;</div>
                        </div>
                        <div>
                            <div class="label">Datum</div>
                            <div class="line">{{ $createdDate }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="box rx">
                    <div class="rx-header">
                        <div class="rx-label">Rp.</div>
                        <div class="label">(Bitte E-Rezept durchstreichen)</div>
                    </div>
                    <div class="rx-lines">
                        @forelse ($items as $item)
                            @php
                                $medicine = data_get($item, 'medicine', '-');
                                $strength = data_get($item, 'strength', '');
                                $days = data_get($item, 'days', '');
                                $morning = data_get($item, 'morning', null);
                                $afternoon = data_get($item, 'afternoon', null);
                                $night = data_get($item, 'night', null);
                                $hasFrequency = $morning !== null || $afternoon !== null || $night !== null;
                                $frequency = $hasFrequency
                                    ? (($morning ? '1' : '0') . ' / ' . ($afternoon ? '1' : '0') . ' / ' . ($night ? '1' : '0'))
                                    : '';
                            @endphp
                            <div class="rx-item">
                                <div class="name">{{ $medicine }}@if($strength) ({{ $strength }})@endif</div>
                                <div>Dosierung: {{ $frequency ?: '-' }}@if($days) | Tage: {{ $days }}@endif</div>
                            </div>
                        @empty
                            <div class="rx-item">Keine Medikamente hinterlegt.</div>
                        @endforelse
                    </div>
                </div>
                <div class="box sign">
                    <div class="sign-box">
                        <div class="sign-name">{{ $doctorName }}</div>
                        <div>Arzt/Arztin</div>
                        <div class="label">Arztstempel/Unterschrift des Arztes</div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
