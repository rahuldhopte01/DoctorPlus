@extends('layout.mainlayout', ['activePage' => 'agb'])

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
            text-align: justify;
        }

        .legal-meta {
            font-size: 0.9rem;
            color: #888;
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="legal-container">
        <div class="legal-header">
            <h1>Allgemeine Geschäftsbedingungen (AGB)</h1>
            <p>Fuxx Teleclinic GmbH – Telemedizin-Plattform dr. fuxx</p>
        </div>

        <div class="legal-content">
            <h3>1. Allgemeines</h3>

            <h4>1.1 Anbieterkennzeichnung; Geltungsbereich</h4>
            <p>
                Diese Allgemeinen Geschäftsbedingungen (nachfolgend „AGB“ genannt) gelten für alle Verträge im Fernabsatz,
                die zwischen Ihnen, unseren Kunden (nachfolgend „Sie“ oder „Kunde(n)“ genannt) und uns, der Fuxx Teleclinic
                GmbH (handelnd unter der Marke dr. fuxx), Pflügersgrundstraße 43, 68169 Mannheim, Telefon: [Telefonnummer],
                E-Mail: support@drfuxx.de, Handelsregister: HRB 757958 beim Amtsgericht Mannheim, Umsatzsteuer-ID:
                [USt-IdNr.], über den Erwerb von Telemedizin-Leistungen und Fernbehandlungsgutscheinen sowie die Vermittlung
                ärztlicher Fernbehandlungen über die unter www.drfuxx.de betriebene Onlineplattform „dr. fuxx“ (nachfolgend
                „Plattform“) geschlossen werden. Die AGB gelten in ihrer zum Zeitpunkt der Bestellung gültigen Fassung.
            </p>

            <h4>1.2 Verbraucher</h4>
            <p>
                „Verbraucher“ im Sinne des § 13 BGB ist jede natürliche Person, die ein Rechtsgeschäft zu Zwecken
                abschließt, die überwiegend weder ihrer gewerblichen noch ihrer selbstständigen beruflichen Tätigkeit
                zugerechnet werden können. Diese AGB gelten ausschließlich gegenüber Verbrauchern.
            </p>

            <h4>1.3 Mindestalter; Lieferbeschränkung</h4>
            <p>
                Voraussetzung für die Inanspruchnahme der auf unserer Plattform angebotenen Leistungen ist, dass Sie
                mindestens das 18. Lebensjahr vollendet haben, die Leistungen ausschließlich für sich selbst in Anspruch
                nehmen, über eine gültige Lieferadresse in der Bundesrepublik Deutschland verfügen und Selbstzahler sind.
                Eine Abrechnung mit Krankenversicherungen erfolgt nicht.
            </p>

            <h3>2. Vertragsgegenstand</h3>
            <p>
                Wir erbringen selbst keine medizinischen oder pharmazeutischen Leistungen. Wir führen keine Behandlungen
                durch, verschreiben keine Medikamente und geben diese nicht ab. Wir vermitteln ausschließlich Leistungen von
                zugelassenen Kooperationsärzten und -apotheken.
            </p>

            <h4>2.1 Vermittlung von Fernbehandlungen</h4>
            <p>
                Über die Plattform vermitteln wir ärztliche Leistungen im Bereich der Telemedizin. Die ärztlichen Leistungen
                werden von unabhängigen, zugelassenen Ärzten erbracht, mit denen wir kooperieren. Ein Behandlungsvertrag
                kommt ausschließlich zwischen Ihnen und dem jeweiligen Arzt zustande.
            </p>
        </div>
    </div>
@endsection