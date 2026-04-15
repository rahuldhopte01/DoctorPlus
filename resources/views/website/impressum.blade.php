@extends('layout.mainlayout', ['activePage' => 'impressum'])

@section('css')
    <style>
        .legal-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 60px 20px;
            color: #333;
            line-height: 1.6;
        }

        .legal-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .legal-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1a1a1a;
        }

        .legal-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .legal-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 40px;
            margin-bottom: 20px;
            color: #1a1a1a;
            border-bottom: 2px solid #7b42f633;
            padding-bottom: 10px;
        }

        .legal-content h4 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 15px;
            color: #1a1a1a;
        }

        .legal-content p {
            margin-bottom: 15px;
        }

        .legal-meta {
            font-size: 0.9rem;
            color: #888;
            margin-top: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 10px 20px;
            margin-bottom: 30px;
        }

        .info-label {
            font-weight: 700;
            color: #1a1a1a;
        }

        .info-value {
            color: #444;
        }

        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 5px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="legal-container">
        <div class="legal-header">
            <h1>Impressum</h1>
            <p>Telemedizin-Plattform dr. fuxx – www.drfuxx.de</p>
            <div class="legal-meta">Stand: 08.01.2026</div>
        </div>

        <div class="legal-content">
            <h3>Angaben gemäß § 5 TMG</h3>

            <h4>Anbieter</h4>
            <div class="info-grid">
                <div class="info-label">Firmenname:</div>
                <div class="info-value">Fuxx Teleclinic GmbH</div>

                <div class="info-label">Marke / Plattform:</div>
                <div class="info-value">dr. fuxx (www.drfuxx.de)</div>

                <div class="info-label">Rechtsform:</div>
                <div class="info-value">Gesellschaft mit beschränkter Haftung (GmbH)</div>

                <div class="info-label">Gründungsdatum:</div>
                <div class="info-value">08.01.2026</div>

                <div class="info-label">Stammkapital:</div>
                <div class="info-value">25.000,00 EUR</div>
            </div>

            <h4>Anschrift</h4>
            <div class="info-grid">
                <div class="info-label">Straße:</div>
                <div class="info-value">Pflügersgrundstraße 43</div>

                <div class="info-label">Stadt:</div>
                <div class="info-value">68169 Mannheim</div>

                <div class="info-label">Land:</div>
                <div class="info-value">Deutschland</div>
            </div>

            <h4>Kontakt</h4>
            <div class="info-grid">
                <div class="info-label">E-Mail:</div>
                <div class="info-value">support@drfuxx.de</div>

                <div class="info-label">Telefon:</div>
                <div class="info-value">[Telefonnummer]</div>

                <div class="info-label">Geschäftszeiten:</div>
                <div class="info-value">Mo–Fr, 09:00–18:00 Uhr</div>

                <div class="info-label">Website:</div>
                <div class="info-value">www.drfuxx.de</div>
            </div>
        </div>
    </div>
@endsection
