<!DOCTYPE html>
<html lang="en">
<head>
    @php
    $setting = App\Models\Setting::first();
    // --- CMS Section Data (with defaults) ---
    $_cms     = $category->cms_sections ?? [];

    $cmsHero  = array_merge([
        'type'                 => 'type1',
        'enabled'              => true,
        'background_image'     => null,
        'bg_color'             => '#f0fdf4',
        'cta_text'             => 'Zu den medizinischen Fragen',
        'cta_color'            => '#3b6fd4',
        'cta_text_color'       => '#ffffff',
        'consultation_fee'     => '29',
        'badge_enabled'        => true,
        'badge_percentage'     => '85',
        'badge_text'           => 'der Männer berichten von einer Besserung',
        'badge_bg_color_start' => '#3b6fd4',
        'badge_bg_color_end'   => '#1e3c8c',
        'rating_enabled'       => true,
        'rating_value'         => '4,79',
        'rating_count'         => '14.082',

        // Type 2 defaults
        't2_heading'           => 'Therapie mit medizinischem Cannabis',
        't2_description'       => 'Füllen Sie einen Online-Fragebogen aus und lassen Sie Ihre Angaben von einem zugelassenen Arzt überprüfen...',
        't2_subtext'           => 'Ärztliche Beurteilung und Verordnung 14,9 € + Cannabis-Therapeutikum ab 3 €',
        't2_main_image'        => null,
        't2_info_1_val'        => '700+',
        't2_info_1_lbl'        => 'ANGESCHLOSSENE APOTHEKEN',
        't2_info_2_val'        => '1,5K+',
        't2_info_2_lbl'        => 'CANNABIS BLÜTEN',
        't2_heading_highlight_color' => '#2d7a45',
        't2_blob_color'        => '#dcfce7',

        // Type 3 defaults
        't3_heading'           => 'Testosteron-Injektion — fertig zur Direktnutzung',
        't3_subheading'        => 'Ärztlich geprüft, sofort einsatzbereit. Kein Mischen, keine Vorbereitung — einfach anwenden.',
        't3_cta_1_url'         => '#',
        't3_cta_1_color'       => '#ef4444',
        't3_cta_1_text_color'  => '#ffffff',
        't3_cta_2_text'        => 'Mehr erfahren',
        't3_cta_2_url'         => '#',
        't3_cta_2_color'       => '#ef4444',
        't3_cta_2_text_color'  => '#ef4444',
        't2_rating_line'       => '<strong>4,79</strong>/5 <span style="color:#666">(14.082 Bewertungen)</span>',
        't3_bottom_items'      => [
            ['icon' => 'bx bx-user', 'text' => 'Deutsche Ärzte'],
            ['icon' => 'bx bx-shield-check', 'text' => '100% DSGVO-konform'],
            ['icon' => 'bx bx-truck', 'text' => 'Expressversand'],
        ],
    ], $_cms['hero'] ?? []);

    $cmsFb = array_merge([
        'enabled'    => true,
        'bg_color'   => '#fafafa',
        'icon_color' => '#3b6fd4',
        'features' => [
            ['enabled' => true, 'title' => 'Das Rezept wird online ausgestellt.',      'subtitle' => 'Ein Klinikbesuch ist nicht erforderlich.'],
            ['enabled' => true, 'title' => 'Lieferung innerhalb von 1–2 Werktagen.',   'subtitle' => 'Schnelle, zuverlässige Lieferung.'],
            ['enabled' => true, 'title' => 'Originalmedizin und Generika.',            'subtitle' => 'Aus zertifizierten Apotheken.'],
            ['enabled' => true, 'title' => 'Beratung über Online-Fragebogen.',         'subtitle' => 'Schnelle medizinische Beratung'],
        ],
    ], $_cms['features_bar'] ?? []);

    $_fbIcons = [
        '<svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>',
        '<svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        '<svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>',
        '<svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>',
    ];

    $cmsSteps = array_merge([
        'enabled'           => true,
        'type'              => 'type1',
        'section_title'     => 'Der dr.fuxx Weg:',
        'section_subtitle'  => 'So schnell & einfach geht es',
        'subtitle_color'    => '#3b6fd4',
        'bg_color'          => '#ffffff',
        'step_number_bg'    => '#3b6fd4',
        't2_title'          => 'In nur 3 Schritten zu Ihrer Behandlung',
        't2_subtitle'       => 'Diskret, sicher und professionell.',
        't2_desc'           => '',
        'icon_color'        => '#ef4444',
        'icon_bg_color'     => '#fef2f2',
        'steps'             => [
            ['title_plain' => 'Füllen Sie den',  'title_highlighted' => 'medizinischen Fragebogen aus', 'highlight_color' => '#3b6fd4', 'description' => 'Starten Sie die Online-Konsultation und beantworten Sie die medizinischen Fragen.',    'image' => null, 'icon' => 'bx bx-file', 't2_title' => 'Anamnese-Bogen ausfüllen'],
            ['title_plain' => 'Wählen Sie die',  'title_highlighted' => 'gewünschte Behandlung',        'highlight_color' => '#3b6fd4', 'description' => 'Der behandelnde Arzt prüft Ihre Angaben und stellt Ihnen bei Bedarf ein Rezept aus.', 'image' => null, 'icon' => 'bx bx-user', 't2_title' => 'Ärztliche Prüfung'],
            ['title_plain' => 'Lieferung in',    'title_highlighted' => '1–2 Werktagen',                'highlight_color' => '#3b6fd4', 'description' => 'Sie erhalten Ihre Medikamente diskret und sicher.',                                    'image' => null, 'icon' => 'bx bx-truck', 't2_title' => 'Express-Zustellung'],
        ],
    ], $_cms['steps'] ?? []);

    $_stepFallbackImgs = [
        'https://images.unsplash.com/photo-1512428559087-560fa5ceab42?auto=format&fit=crop&w=400&q=80',
        'https://images.unsplash.com/photo-1612349317150-e410f624c427?auto=format&fit=crop&w=400&q=80',
        'https://images.unsplash.com/photo-1580674285054-bed31e145f59?auto=format&fit=crop&w=400&q=80',
    ];
    $_stepAltTexts = ['Fragebogen', 'Arzt', 'Lieferung'];

    $cmsPay = array_merge([
        'enabled'  => true,
        'label'    => 'Akzeptierte Zahlungsmethoden:',
        'bg_color' => '#1a1a1a',
        'methods'  => ['klarna' => true, 'visa' => true, 'maestro' => true, 'gpay' => true, 'apple_pay' => true, 'paypal' => true],
    ], $_cms['payment_bar'] ?? []);

    $cmsMedical = array_merge([
        'enabled'       => true,
        'bg_color'      => '#ffffff',
        'section_title' => 'Behandlungen bei',
        'toc_enabled'   => true,
        'toc_title'     => 'Themenliste',
        'toc_items'     => [],
        'articles'      => [],
    ], $_cms['medical_content'] ?? []);

    $cmsDr = array_merge([
        'enabled'           => true,
        'bg_color'          => '#ffffff',
        'image'             => null,
        'name'              => 'Dr. med. Experte',
        'role'              => 'Facharzt für Urologie',
        'title'             => 'Medizinisch-fachlich geprüft',
        'paragraphs'        => [
            'Die medizinischen Inhalte auf dieser Seite wurden in Zusammenarbeit mit einem unserer Ärzte bzw. medizinischen Experten erstellt und von diesen überprüft.',
            'Die medizinischen Inhalte werden regelmäßig überprüft, um maximale Genauigkeit und Zuverlässigkeit zu gewährleisten.',
        ],
        'link_text'         => 'Redaktionsprozess',
        'link_url'          => '#',
        'show_last_updated' => true,
    ], $_cms['doctor_review'] ?? []);

    $cmsFaq = array_merge([
        'enabled'              => true,
        'bg_color'             => '#ffffff',
        'title'                => 'Frequently asked questions',
        'subtitle'             => '',
        'subtitle_color'       => '#e63946',
        'question_color'       => '#1a1a1a',
        'question_hover_color' => '#3b6fd4',
        'question_bg_color'       => '#ffffff',
        'question_bg_hover_color' => '#f8f9fa',
        'question_bg_active_color' => '#ffffff',
        'question_active_color'    => '#ffffff',
        'answer_bg_color'          => '#ffffff',
        'answer_text_color'        => '#6c757d',
        'items'                => [
            ['question' => 'How long does the consultation process take?',  'answer' => 'The entire process typically takes 24-48 hours from questionnaire submission to prescription approval and shipping.'],
            ['question' => 'Is this treatment suitable for me?',            'answer' => 'Our doctors will review your questionnaire and medical history to determine if this treatment is appropriate for your specific situation.'],
            ['question' => 'What if I have questions about my medication?', 'answer' => 'You can contact our medical team at any time with questions about your treatment. We provide ongoing support throughout your treatment period.'],
        ],
    ], $_cms['faq'] ?? []);

    $cmsTestoInfo = array_merge([
        'enabled'       => true,
        'bg_color'      => '#ffffff',
        'card_bg_color' => '#fdf3f3',
        'icon_color'    => '#f05050',
        'icon_bg_color' => '#ffffff',
        'heading'       => 'Was ist eine Testosteron-Injektion?',
        'paragraph_1'   => 'Testosteron ist das wichtigste männliche Sexualhormon und spielt eine zentrale Rolle für Energie, Muskelaufbau, Stimmung und Libido. Mit zunehmendem Alter oder durch bestimmte Erkrankungen kann der Testosteronspiegel sinken — oft mit spürbaren Auswirkungen auf Körper und Wohlbefinden.',
        'paragraph_2'   => 'Unsere fertige Testosteron-Injektion wurde speziell für die einfache Anwendung entwickelt: kein Mischen, kein Vorbereiten. Sie ist ärztlich dosiert, qualitätsgeprüft und sofort einsatzbereit. Ideal für Männer, die ihren Testosteronspiegel effektiv und unkompliziert anheben möchten.',
        'paragraph_3'   => 'Die Behandlung erfolgt unter ärztlicher Aufsicht: Ein zugelassener Arzt prüft Ihre Angaben, stellt das Rezept aus und die fertige Injektion wird diskret zu Ihnen nach Hause geliefert.',
        'cards' => [
            ['icon' => 'bi-activity',     'title' => 'Fertige Injektion',        'subtitle' => 'Sofort einsatzbereit, keine Vorbereitung'],
            ['icon' => 'bi-check-circle', 'title' => 'Keine Vorbereitung nötig', 'subtitle' => 'Kein Mischen, kein Dosieren'],
            ['icon' => 'bi-person',       'title' => 'Ärztlich dosiert',         'subtitle' => 'Individuell geprüft und verschrieben'],
            ['icon' => 'bi-truck',        'title' => 'Express-Lieferung',        'subtitle' => 'Diskret in 1-2 Werktagen bei Ihnen'],
        ],
    ], $_cms['testo_info'] ?? []);

    $cmsTestoTreatments = array_merge([
        'enabled'           => true,
        'bg_color'          => '#fdf5f5',
        'button_color'      => '#8b5cf6',
        'button_text_color' => '#ffffff',
        'heading'           => 'Unsere Testosteron-Behandlungen',
        'subheading'        => 'Wählen Sie die passende Behandlung — ärztlich geprüft und fertig zur Anwendung.',
        'cards' => [
            ['image' => null, 'title' => 'Energie und Antrieb zurückgewinnen',      'description' => 'Spüren Sie wieder mehr Vitalität, Leistungsfähigkeit und Lebensfreude. Unsere Testosteron-Injektion unterstützt Sie dabei, Ihren Alltag mit neuer Energie zu meistern.', 'button_text' => 'Behandlung starten', 'button_url' => '#'],
            ['image' => null, 'title' => 'Fertige Injektion — einfach und sicher',  'description' => 'Keine komplizierte Vorbereitung, kein Mischen. Die Injektion ist ärztlich dosiert und sofort anwendbar — für maximale Sicherheit und Komfort.',                         'button_text' => 'Jetzt anfragen',     'button_url' => '#'],
        ],
    ], $_cms['testo_treatments'] ?? []);

    $_testoTreatFallbackImgs = [
        'https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?auto=format&fit=crop&w=600&q=80',
    ];

    $cmsSecurity = array_merge([
        'enabled'       => true,
        'bg_color'      => '#fdf5f5',
        'card_bg_color' => '#ffffff',
        'icon_color'    => '#f05050',
        'icon_bg_color' => '#fdf0f0',
        'heading'       => 'Ihre Sicherheit ist unsere Priorität',
        'subheading'    => 'Vertrauen, Datenschutz und medizinische Qualität — darauf können Sie sich bei dr.fuxx verlassen.',
        'cards' => [
            ['icon' => 'bi-shield', 'title' => '100% DSGVO-konform',   'description' => 'Ihre persönlichen und medizinischen Daten werden nach höchsten deutschen Datenschutzstandards verschlüsselt und geschützt.'],
            ['icon' => 'bi-person', 'title' => 'Deutsche Ärzte',        'description' => 'Alle Rezepte werden von in Deutschland zugelassenen Ärzten ausgestellt. Qualität und Sicherheit stehen bei uns an erster Stelle.'],
            ['icon' => 'bi-lock',   'title' => 'Diskret & vertraulich', 'description' => 'Neutrale Verpackung, verschlüsselte Kommunikation und keine Weitergabe Ihrer Daten an Dritte.'],
        ],
    ], $_cms['security'] ?? []);

    // --- Section Order ---
    $_validSections  = ['hero','features_bar','steps','payment_bar','medical_content','doctor_review','faq','testo_info','testo_treatments','security'];
    $_sectionOrder   = $_cms['section_order'] ?? $_validSections;
    foreach ($_validSections as $_sk) {
        if (!in_array($_sk, $_sectionOrder)) $_sectionOrder[] = $_sk;
    }
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - {{ $setting->business_name }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link href="{{asset('css/new-design.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/website_header.css') }}">
    <link href="{{asset('styles.css')}}?v={{ time() }}" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Global typography: Inter (body) + Clash Display (headings) -->
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6, .display-4, .display-5 { font-family: 'Clash Display', sans-serif; }
        h1 span, h2 span, h3 span, h4 span, h5 span, h6 span, .display-4 span, .display-5 span { font-family: inherit; }
    </style>
    <style>
        .bg-light-green {
            background-color: #f0fdf4;
        }
        .bg-light-red {
            background-color: #fef2f2;
        }
        .bg-light-yellow {
            background-color: #fefce8;
        }

        /* --- GENERAL HERO VARS --- */
        :root {
            --ed-radius-lg: 20px;
            --ed-dark: #1a1a1a;
            --ed-text-light: #555;
            --ed-text-muted: #888;
            --ed-max-width: 1280px;
        }

        /* --- HERO TYPE 1 (CLASSIC) --- */
        .ed-hero { position: relative; width: 100%; min-height: 520px; overflow: hidden; margin-bottom: 0; }
        .ed-hero-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center top; }
        .ed-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to right, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.7) 45%, transparent 70%); }
        .ed-hero-inner { position: relative; z-index: 2; max-width: var(--ed-max-width); margin: 0 auto; padding: 48px 24px; display: flex; align-items: center; min-height: 520px; }
        .ed-hero-text { max-width: 440px; background: rgba(255,255,255,0.3); backdrop-filter: blur(16px); border-radius: var(--ed-radius-lg); padding: 30px 28px; box-shadow: 0 8px 40px rgba(0,0,0,0.06); }
        .ed-hero-text h1 { font-size: 2.2rem; font-weight: 800; line-height: 1.15; color: var(--ed-dark); margin-bottom: 14px; }
        .ed-hero-text > p { font-size: 0.85rem; color: var(--ed-text-light); line-height: 1.6; margin-bottom: 20px; }
        
        .hero-cta { display: inline-flex; align-items: center; padding: 16px 36px; background: #3b6fd4; color: #fff; border-radius: 50px; font-size: 1rem; font-weight: 700; box-shadow: 0 6px 20px rgba(59,111,212,0.35); text-decoration: none; transition: all 0.3s ease; border:none; cursor: pointer; }
        .hero-cta:hover { background: #2a52a8; color: #fff; transform: translateY(-2px); }
        
        .hero-pricing { margin-top: 18px; font-size: 0.82rem; color: var(--ed-text-light); line-height: 1.5; }
        .hero-rating { margin-top: 14px; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; }
        .hero-rating .stars { color: #f59e0b; font-size: 1rem; }
        
        .ed-hero-badge { position: absolute; bottom: 48px; right: 80px; background: linear-gradient(135deg, rgba(59,111,212,0.9), rgba(30,60,140,0.95)); backdrop-filter: blur(8px); border-radius: 20px; padding: 28px 32px; color: #fff; text-align: center; box-shadow: 0 8px 30px rgba(59,111,212,0.3); z-index: 3; }
        .ed-hero-badge .badge-big { font-size: 3.5rem; font-weight: 900; line-height: 1; }
        .ed-hero-badge .badge-big span { font-size: 2rem; }
        .ed-hero-badge .badge-sub { font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 6px; }

        @media (max-width: 1024px) {
            .ed-hero-inner { padding: 32px 20px; }
            .ed-hero-text h1 { font-size: 2rem; }
            .ed-hero-badge { right: 40px; bottom: 32px; padding: 20px 24px; }
            .ed-hero-badge .badge-big { font-size: 2.5rem; }
        }
        @media (max-width: 768px) {
            .ed-hero { min-height: 600px; }
            .ed-hero-bg { object-position: 65% top; }
            .ed-hero-inner { min-height: 600px; padding: 24px 16px; flex-direction: column; justify-content: flex-end; }
            .ed-hero-overlay { background: linear-gradient(to top, rgba(255,255,255,0.97) 0%, rgba(255,255,255,0.85) 50%, rgba(255,255,255,0.2) 75%, transparent 100%); }
            .ed-hero-text { max-width: 100%; padding: 20px 18px; background: rgba(255,255,255,0.5); backdrop-filter: blur(14px); }
            .ed-hero-text h1 { font-size: clamp(1.5rem, 5vw, 2rem); }
            .hero-cta { width: 100%; justify-content: center; }
            .ed-hero-badge { position: absolute; top: 16px; right: 16px; bottom: auto; left: auto; padding: 16px 18px; }
            .ed-hero-badge .badge-big { font-size: 1.8rem; }
            .ed-hero-badge .badge-sub { font-size: 0.72rem; }
        }

        /* --- HERO TYPE 2 (CANNABIS) --- */
        .ed-hero-t2 {
            background-color: {{ $cmsHero['bg_color'] ?? '#f0fdf4' }};
            padding: 80px 24px;
            overflow: hidden;
            position: relative;
        }
        .ed-hero-t2-inner {
            max-width: var(--ed-max-width);
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 40px;
            align-items: center;
        }
        .ed-hero-t2-text h1 {
            font-size: 3.5rem; font-weight: 800; color: #1a1a1a; line-height: 1.1; margin-bottom: 24px;
        }
        .ed-hero-t2-text h1 span.text-success { color: {{ $cmsHero['t2_heading_highlight_color'] ?? '#2d7a45' }} !important; }
        .ed-hero-t2-text p { font-size: 1.05rem; color: #555; line-height: 1.6; margin-bottom: 32px; max-width: 520px; }
        .ed-hero-t2-cta-wrap { margin-bottom: 24px; }
        .hero-t2-subtext { font-size: 0.9rem; color: #666; margin-top: 14px; font-weight: 500; }
        .ed-hero-t2-rating { display: flex; align-items: center; gap: 8px; font-size: 0.95rem; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 20px; width: fit-content; }
        .ed-hero-t2-rating .stars { color: #f59e0b; font-size: 1.2rem; }
        
        .ed-hero-t2-img-wrap { position: relative; display: flex; justify-content: center; height: 100%; min-height: 400px; }
        .ed-hero-t2-main-img { width: 100%; max-width: 500px; height: auto; object-fit: contain; z-index: 2; position: relative; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.1)); }
        .ed-hero-t2-bg-blob {
            position: absolute; top: 10%; left: 10%; width: 80%; height: 80%;
            background: {{ $cmsHero['t2_blob_color'] ?? '#dcfce7' }}; border-radius: 40px; transform: rotate(-5deg); z-index: 1;
        }
        .ed-hero-t2-info-box {
            position: absolute; z-index: 3; background: #fff; padding: 14px 22px; border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 14px;
        }
        .ed-hero-t2-info-box .val { font-size: 1.6rem; font-weight: 900; color: #dc2626; line-height: 1; }
        .ed-hero-t2-info-box .lbl { font-size: 0.7rem; font-weight: 800; color: #1a1a1a; text-transform: uppercase; line-height: 1.2; letter-spacing: 0.05em; }
        .box-1 { bottom: 60px; left: -30px; }
        .box-2 { top: 60px; right: -30px; }

        @media (max-width: 1200px) {
            .box-1 { left: 0; } .box-2 { right: 0; }
        }
        @media (max-width: 992px) {
            .ed-hero-t2-inner { grid-template-columns: 1fr; text-align: center; }
            .ed-hero-t2-text h1 { font-size: 2.8rem; }
            .ed-hero-t2-text p { margin-left: auto; margin-right: auto; }
            .ed-hero-t2-cta-wrap { display: flex; flex-direction: column; align-items: center; }
            .ed-hero-t2-rating { margin-left: auto; margin-right: auto; }
            .ed-hero-t2-img-wrap { margin-top: 60px; min-height: 350px; }
            .box-1 { left: 10%; } .box-2 { right: 10%; top: -20px; }
        }
        @media (max-width: 480px) {
            .ed-hero-t2-text h1 { font-size: 2.2rem; }
            .ed-hero-t2-info-box { padding: 10px 16px; gap: 10px; }
            .ed-hero-t2-info-box .val { font-size: 1.3rem; }
            .ed-hero-t2-info-box .lbl { font-size: 0.6rem; }
            .box-1 { left: 0; bottom: 20px; } .box-2 { right: 0; top: -10px; }
        }

        /* --- HERO TYPE 3 (TESTOSTERONE) --- */
        .ed-hero-t3 {
            position: relative; width: 100%; min-height: 650px;
            display: flex; align-items: center; padding: 80px 24px;
            background-size: cover; background-position: center; border-bottom: 1px solid #eee;
        }
        .ed-hero-t3-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.15); z-index: 1; }
        .ed-hero-t3-card {
            position: relative; z-index: 2; width: 100%; max-width: 500px;
            background: rgba(255,255,255,0.92); backdrop-filter: blur(12px);
            padding: 50px; border-radius: 40px; margin-left: 5%;
            box-shadow: 0 25px 60px rgba(0,0,0,0.18);
        }
        .ed-hero-t3-card h1 { font-size: 2.5rem; font-weight: 800; color: #1a1a1a; margin-bottom: 24px; line-height: 1.15; }
        .ed-hero-t3-card p { font-size: 1rem; color: #4b5563; line-height: 1.6; margin-bottom: 35px; }
        .ed-hero-t3-btns { display: flex; flex-direction: column; gap: 16px; margin-bottom: 35px; }
        .t3-btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 16px 30px; border-radius: 60px; font-weight: 700; text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); text-align: center; border: none; font-size: 1.05rem;
        }
        .t3-btn-solid { background: #ef4444; color: #fff; box-shadow: 0 12px 24px rgba(239,68,68,0.3); }
        .t3-btn-solid:hover { background: #dc2626; color: #fff; transform: translateY(-3px); box-shadow: 0 15px 30px rgba(239,68,68,0.4); }
        .t3-btn-outline { border: 2.5px solid #ef4444; color: #ef4444; background: transparent; }
        .t3-btn-outline:hover { background: #ef4444; color: #fff; transform: translateY(-3px); }

        .ed-hero-t3-bottom { display: flex; flex-wrap: wrap; gap: 24px; border-top: 1px solid #e5e7eb; padding-top: 30px; }
        .t3-bottom-item { display: flex; align-items: center; gap: 10px; font-size: 0.88rem; color: #374151; font-weight: 600; }
        .t3-bottom-item i { color: #ef4444; font-size: 1.25rem; }

        @media (max-width: 992px) {
            .ed-hero-t3 { min-height: 550px; padding: 60px 24px; }
            .ed-hero-t3-card { margin-left: 0; max-width: 480px; }
        }
        @media (max-width: 768px) {
            .ed-hero-t3 { justify-content: center; padding: 40px 16px; background-attachment: scroll; }
            .ed-hero-t3-card { margin-left: 0; padding: 40px 24px; border-radius: 30px; width: 100%; }
            .ed-hero-t3-card h1 { font-size: 2rem; }
            .ed-hero-t3-bottom { gap: 16px; justify-content: center; }
            .t3-bottom-item { font-size: 0.8rem; }
        }

        /* FEATURES BAR */
        .features-bar { background: #fafafa; border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 28px 0; }
        .features-bar-inner { max-width: var(--ed-max-width); margin: 0 auto; padding: 0 24px; display: flex; justify-content: space-between; gap: 24px; }
        .fb-item { display: flex; align-items: center; gap: 12px; flex: 1; }
        .fb-icon { width: 44px; height: 44px; border-radius: 50%; background: #eff3fb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .fb-icon svg { width: 20px; height: 20px; stroke: var(--fb-icon-color, #3b6fd4); fill: none; stroke-width: 2; }
        .fb-text strong { display: block; font-size: 0.82rem; font-weight: 700; color: var(--ed-dark); line-height: 1.3; }
        .fb-text span { font-size: 0.78rem; color: var(--ed-text-muted); }

        @media (max-width: 1024px) {
            .features-bar-inner { flex-wrap: wrap; }
            .fb-item { flex: 0 0 calc(50% - 12px); }
        }
        @media (max-width: 768px) {
            .features-bar-inner { overflow-x: auto; flex-wrap: nowrap; gap: 20px; padding: 0 16px; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
            .features-bar-inner::-webkit-scrollbar { display: none; }
            .fb-item { flex: 0 0 auto; min-width: 200px; }
        }
        /* 3 STEPS */
        .steps-section { 
            padding: 72px 24px; 
            background: {{ $cmsSteps['bg_color'] ?? '#ffffff' }} !important;
            text-align: center; 
            overflow: hidden; 
        }
        .steps-title { font-size: 2.4rem; font-weight: 800; color: var(--ed-dark); margin-bottom: 8px; font-family: inherit; }
        .steps-title span { color: #3b6fd4; font-style: italic; font-family: inherit; }
        .steps-grid { display: flex; justify-content: center; gap: 24px; max-width: 960px; margin: 48px auto 0; position: relative; }
        .step-card { background: #fff; border-radius: var(--ed-radius-lg); box-shadow: 0 4px 20px rgba(0,0,0,0.08); max-width: 280px; width: 100%; text-align: center; position: relative; z-index: 1; transition: transform 0.4s ease, box-shadow 0.4s ease; overflow: visible; }
        .step-card-inner { padding: 28px 20px 20px; overflow: hidden; border-radius: var(--ed-radius-lg); display: flex; flex-direction: column; align-items: center; justify-content: flex-start; height: 100%; }
        .step-card:hover { transform: translateY(-8px); box-shadow: 0 8px 32px rgba(0,0,0,0.1); z-index: 3; }
        .step-num { position: absolute; top: -16px; left: 50%; transform: translateX(-50%); width: 38px; height: 38px; border-radius: 50%; background: #3b6fd4; color: #fff; font-size: 1rem; font-weight: 800; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(59,111,212,0.4); z-index: 5; }
        .step-card h3 { font-size: 1.1rem; font-weight: 800; color: var(--ed-dark); margin: 12px 0 6px; line-height: 1.25; font-family: inherit; }
        .step-card h3 span { color: #3b6fd4; }
        .step-card p { font-size: 0.78rem; color: var(--ed-text-light); line-height: 1.5; margin-bottom: 14px; }
        .step-card img { width: 100%; height: 180px; object-fit: contain; border-radius: 12px; transition: transform 0.5s ease; margin-top: auto; }
        .step-card:hover img { transform: scale(1.06); }

        /* 3 STEPS TYPE 2 (Testosterone) */
        .steps-section-t2 { padding: 80px 24px; background: #fffafb; text-align: center; }
        .steps-section-t2 h2 { font-size: 2rem; font-weight: 700; color: #1a1a1a; margin-bottom: 12px; }
        .steps-section-t2 .subtitle { font-size: 2.2rem; font-weight: 800; color: #ef4444; font-style: italic; margin-bottom: 24px; }
        .steps-section-t2-desc { max-width: 600px; margin: 0 auto 56px; color: #666; line-height: 1.6; font-size: 1rem; }
        .steps-grid-t2 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; max-width: 1140px; margin: 0 auto; }
        .step-card-t2 { background: #fff; border-radius: 24px; padding: 48px 32px 40px; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s ease; display: flex; flex-direction: column; align-items: center; }
        .step-card-t2:hover { transform: translateY(-10px); }
        .step-num-t2 { position: absolute; top: -20px; left: 50%; transform: translateX(-50%); width: 40px; height: 40px; background: #ef4444; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4); }
        .step-icon-t2 { width: 72px; height: 72px; background: {{ $cmsSteps['icon_bg_color'] }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; }
        .step-icon-t2 i { font-size: 1.8rem; color: {{ $cmsSteps['icon_color'] }}; }
        .step-card-t2 h3 { font-size: 1.35rem; font-weight: 800; color: #ef4444; margin-bottom: 12px; }
        .step-card-t2 p { font-size: 0.9rem; color: #555; line-height: 1.6; margin: 0; }

        @media (max-width: 992px) {
            .steps-grid-t2 { grid-template-columns: 1fr; max-width: 400px; gap: 48px; }
            .steps-section-t2 .subtitle { font-size: 1.8rem; }
        }
        
        /* PAYMENT BAR */
        .payment-bar { background: var(--ed-dark); padding: 24px 0; }
        .payment-bar-inner { max-width: 960px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; gap: 32px; flex-wrap: wrap; justify-content: center; }
        .payment-bar-inner > span { color: rgba(255,255,255,0.8); font-size: 0.85rem; font-weight: 600; }
        .payment-logos { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
        .payment-logos .pay-logo { background: #fff; border-radius: 8px; padding: 8px 16px; font-size: 0.85rem; font-weight: 800; color: var(--ed-dark); display: inline-flex; align-items: center; }

        /* MEDICAL CONTENT */
        .med-content { max-width: 800px; margin: 0 auto; padding: 72px 24px; }
        .med-content > h2:first-child { font-size: 2.4rem; font-weight: 800; color: var(--ed-dark); text-align: center; margin-bottom: 40px; }
        .toc { margin-bottom: 48px; }
        .toc h3 { font-size: 1rem; font-weight: 700; margin-bottom: 12px; }
        .toc a { display: block; color: var(--ed-primary); font-size: 0.9rem; line-height: 1.8; text-decoration: underline; text-underline-offset: 3px; }
        .toc a:hover { color: #2a52a8; }
        .med-article { margin-bottom: 48px; }
        .med-article h2 { font-size: 2.2rem; font-weight: 800; color: var(--ed-dark); margin-bottom: 18px; line-height: 1.15; }
        .med-article h3 { font-size: 1.6rem; font-weight: 800; color: var(--ed-dark); margin: 32px 0 14px; line-height: 1.2; }
        .med-article h4 { font-size: 1.25rem; font-weight: 700; color: var(--ed-dark); margin: 28px 0 10px; }
        .med-article p { font-size: 0.92rem; color: #444; line-height: 1.75; margin-bottom: 16px; }
        .med-article ul { margin: 12px 0 20px 20px; list-style: disc; }
        .med-article ul li { font-size: 0.92rem; color: #444; line-height: 1.75; margin-bottom: 8px; }
        .med-article a { color: var(--ed-primary); text-decoration: underline; }

        /* TABLE */
        .med-table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; margin: 24px 0 20px; }
        .med-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; min-width: 500px; }
        .med-table th { background: #3b6fd4; color: #fff; padding: 12px 14px; text-align: center; font-weight: 700; }
        .med-table td { padding: 12px 14px; text-align: center; border: 1px solid #e5e5e5; color: #444; }
        .med-table tr:nth-child(even) { background: #fafafa; }
        .med-table td:first-child, .med-table th:first-child { text-align: left; font-weight: 600; background: #f5f5f5; color: var(--ed-dark); }
        .med-table th:first-child { background: #2a52a8; color: #fff; }

        /* CALLOUT BOX */
        .callout { background: #f8f8f8; border-radius: 12px; padding: 24px 28px; margin: 28px 0; }
        .callout h4 { font-size: 0.95rem; font-weight: 700; margin-bottom: 10px; }
        .callout p { font-size: 0.88rem; color: #555; line-height: 1.7; margin: 0; }

        /* MEDICAL REVIEW */
        .med-review { max-width: 800px; margin: 0 auto; padding: 0 24px 72px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; }
        .med-review-img { border-radius: var(--ed-radius-lg); overflow: hidden; background: #f0f0f0; }
        .med-review-img img { width: 100%; height: 400px; object-fit: cover; object-position: center top; display: block; }
        .med-review-doc { font-size: 1rem; font-weight: 700; margin-bottom: 4px; }
        .med-review-role { font-size: 0.82rem; color: var(--ed-primary); margin-bottom: 16px; }
        .med-review-text h3 { font-size: 2rem; font-weight: 800; color: var(--ed-primary); margin-bottom: 16px; line-height: 1.2; }
        .med-review-text p { font-size: 0.88rem; color: #555; line-height: 1.7; margin-bottom: 12px; }
        .med-review-text .update { font-size: 0.78rem; color: var(--ed-primary); margin-top: 16px; }

        /* TESTOSTERON SECTION */
        .testo-section { padding: 72px 24px; max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; }
        .testo-content h2 { font-size: 2.2rem; font-weight: 800; color: var(--ed-dark); margin-bottom: 24px; line-height: 1.25; }
        .testo-content p { font-size: 0.95rem; color: #555; line-height: 1.7; margin-bottom: 20px; }
        .testo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .testo-card { background-color: {{ $cmsTestoInfo['card_bg_color'] }} !important; border-radius: 12px; padding: 28px 24px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 180px; }
        .testo-icon-wrap { width: 44px; height: 44px; background: {{ $cmsTestoInfo['icon_bg_color'] }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.04); }
        .testo-icon-wrap i { color: {{ $cmsTestoInfo['icon_color'] }}; font-size: 1.3rem; }
        .testo-card h4 { font-size: 0.95rem; font-weight: 800; color: var(--ed-dark); margin: 0 0 8px; line-height: 1.3; }
        .testo-card p { font-size: 0.8rem; color: #666; margin: 0; line-height: 1.45; }

        /* TESTOSTERON TREATMENTS SECTION */
        .testo-treat-section { background-color: {{ $cmsTestoTreatments['bg_color'] }} !important; padding: 80px 24px; text-align: center; }
        .testo-treat-header h2 { font-size: 2.2rem; font-weight: 800; color: var(--ed-dark); margin-bottom: 12px; line-height: 1.2; }
        .testo-treat-header p { font-size: 0.95rem; color: #666; max-width: 600px; margin: 0 auto 48px; line-height: 1.6; }
        .testo-treat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; max-width: 1000px; margin: 0 auto; text-align: left; }
        .testo-treat-card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.04); display: flex; flex-direction: column; }
        .testo-treat-img { width: 100%; height: 280px; background: #fafafa; object-fit: contain; }
        .testo-treat-content { padding: 32px; display: flex; flex-direction: column; flex-grow: 1; justify-content: flex-start; }
        .testo-treat-content h3 { font-size: 1.25rem; font-weight: 800; color: var(--ed-dark); margin-bottom: 12px; }
        .testo-treat-content p { font-size: 0.85rem; color: #555; line-height: 1.6; margin-bottom: 24px; }
        .testo-treat-btn { background: {{ $cmsTestoTreatments['button_color'] }}; color: {{ $cmsTestoTreatments['button_text_color'] }}; text-decoration: none; padding: 12px 28px; border-radius: 30px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; align-self: flex-start; transition: background 0.3s; margin-top: auto; }
        .testo-treat-btn:hover { opacity: 0.9; color: {{ $cmsTestoTreatments['button_text_color'] }}; }

        /* SECURITY SECTION */
        .security-section { background-color: {{ $cmsSecurity['bg_color'] }} !important; padding: 0 24px 80px; text-align: center; } 
        .security-header h2 { font-size: 2.2rem; font-weight: 800; color: var(--ed-dark); margin-bottom: 12px; line-height: 1.25; }
        .security-header p { font-size: 0.95rem; color: #555; max-width: 600px; margin: 0 auto 48px; line-height: 1.6; }
        .security-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; max-width: 1040px; margin: 0 auto; text-align: center; }
        .security-card { background: {{ $cmsSecurity['card_bg_color'] }} !important; border-radius: 16px; padding: 40px 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); display: flex; flex-direction: column; align-items: center; }
        .security-icon { width: 64px; height: 64px; background: {{ $cmsSecurity['icon_bg_color'] }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; }
        .security-icon i { color: {{ $cmsSecurity['icon_color'] }}; font-size: 1.6rem; }
        .security-card h3 { font-size: 1.05rem; font-weight: 800; color: var(--ed-dark); margin-bottom: 16px; line-height: 1.3; }
        .security-card p { font-size: 0.85rem; color: #666; line-height: 1.6; margin: 0; }

        @media (max-width: 992px) {
            .security-grid { grid-template-columns: 1fr 1fr; }
            .security-grid .security-card:nth-child(3) { grid-column: span 2; max-width: 50%; margin: 0 auto; }
        }

        @media (max-width: 768px) {
            .testo-treat-grid { grid-template-columns: 1fr; max-width: 500px; }
            .testo-treat-img { height: 240px; }
            .testo-treat-section { padding: 48px 24px; }
            .testo-section { grid-template-columns: 1fr; gap: 40px; padding: 48px 24px; }
            .testo-grid { grid-template-columns: 1fr; }
            .payment-bar-inner { gap: 16px; }
            .payment-logos { gap: 10px; }
            .pay-logo { padding: 6px 10px; font-size: 0.75rem; }
            .steps-section { padding: 48px 16px; }
            .steps-title { font-size: 1.5rem; }
            .steps-grid { flex-direction: column; align-items: center; gap: 32px; }
            .step-card { width: 100%; max-width: 320px; }
            .step-card:nth-child(1), .step-card:nth-child(2), .step-card:nth-child(3) { transform: none; }
        }
        @media (max-width: 480px) {
            .steps-title { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
    @include('layout.partials.skeleton_loader')
<!-- Navigation -->
@include('layout.partials.navbar_website')

<!-- Breadcrumb -->
<div class="bg-light border-bottom">
    <div class="container py-3">
        <a href="{{ route('categories') }}" class="breadcrumb-link text-decoration-none">
            <i class="bi bi-chevron-left"></i> Back to all treatments
        </a>
    </div>
</div>

@foreach($_sectionOrder as $_sectionKey)
@switch($_sectionKey)

{{-- ============================  HERO  ============================ --}}
@case('hero')
<!-- Hero Section -->
@if($cmsHero['enabled'])
    @php
        $_heroType = $cmsHero['type'] ?? 'type1';
        $_heroBannerFile = $cmsHero['background_image'] ?? null;
        $_heroFallbackFile = $category->image ?? null;
        $_heroImgPath = '';
        if ($_heroBannerFile && file_exists(public_path('images/upload/'.$_heroBannerFile))) {
            $_heroImgPath = asset('images/upload/'.$_heroBannerFile);
        } elseif ($_heroFallbackFile && file_exists(public_path('images/upload/'.$_heroFallbackFile))) {
            $_heroImgPath = asset('images/upload/'.$_heroFallbackFile);
        } else {
            $_heroImgPath = "https://images.unsplash.com/photo-1505751172876-fa1923c5c528?auto=format&fit=crop&w=1280&q=80";
        }
    @endphp

    @if($_heroType == 'type3')
        {{-- TYPE 3: TESTOSTERONE --}}
        <section class="ed-hero-t3" style="background-image: url('{{ $_heroImgPath }}'); background-color: {{ $cmsHero['bg_color'] }} !important;">
            <div class="ed-hero-t3-overlay"></div>
            <div class="ed-hero-t3-card">
                <h1>{!! $cmsHero['t3_heading'] ?? $category->name !!}</h1>
                <p>{!! $cmsHero['t3_subheading'] ?? ($category->description ? Str::limit($category->description, 150) : '') !!}</p>

                <div class="ed-hero-t3-btns">
                    <a href="{{ url('/questionnaire/category/' . $category->id) }}"
                       class="t3-btn t3-btn-solid"
                       style="background:{{ $cmsHero['t3_cta_1_color'] }}; color:{{ $cmsHero['t3_cta_1_text_color'] }}; box-shadow:0 12px 24px {{ $cmsHero['t3_cta_1_color'] }}4d;">
                        {{ $cmsHero['t3_cta_1_text'] ?? 'Jetzt Beratung starten' }}
                    </a>
                    <a href="{{ $cmsHero['t3_cta_2_url'] ?? '#' }}" class="t3-btn t3-btn-outline"
                       style="border-color:{{ $cmsHero['t3_cta_2_color'] }}; color:{{ $cmsHero['t3_cta_2_text_color'] }};">
                        {{ $cmsHero['t3_cta_2_text'] ?? 'Mehr erfahren' }}
                    </a>
                </div>

                <div class="ed-hero-t3-bottom">
                    @foreach(($cmsHero['t3_bottom_items'] ?? []) as $item)
                        <div class="t3-bottom-item">
                            <i class="{{ $item['icon'] ?? 'bx bx-check-circle' }}"></i>
                            <span>{{ $item['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    @elseif($_heroType == 'type2')
        {{-- TYPE 2: CANNABIS --}}
        <section class="ed-hero-t2" style="background-color: {{ $cmsHero['bg_color'] }} !important;">
            <div class="ed-hero-t2-inner">
                <div class="ed-hero-t2-text">
                    <h1>{!! $cmsHero['t2_heading'] ?? $category->name !!}</h1>
                    <p>{!! $cmsHero['t2_description'] ?? ($category->description ? Str::limit($category->description, 180) : '') !!}</p>

                    <div class="ed-hero-t2-cta-wrap">
                        <a href="{{ url('/questionnaire/category/' . $category->id) }}"
                           class="hero-cta"
                           style="background:{{ $cmsHero['cta_color'] }}; color:{{ $cmsHero['cta_text_color'] }}; box-shadow:0 10px 25px {{ $cmsHero['cta_color'] }}4d;">
                            {{ $cmsHero['cta_text'] }}
                        </a>
                        <div class="hero-t2-subtext">
                            {!! $cmsHero['t2_subtext'] !!}
                        </div>
                    </div>

                    @if($cmsHero['rating_enabled'])
                    <div class="ed-hero-t2-rating">
                        <span class="stars">★★★★★</span>
                        <div class="ml-1">
                            {!! $cmsHero['t2_rating_line'] !!}
                        </div>
                    </div>
                    @endif
                </div>

                <div class="ed-hero-t2-img-wrap">
                    <div class="ed-hero-t2-bg-blob"></div>
                    @if(!empty($cmsHero['t2_main_image']))
                        <img src="{{ asset('images/upload/'.$cmsHero['t2_main_image']) }}" alt="Cannabis" class="ed-hero-t2-main-img">
                    @else
                        <img src="https://images.unsplash.com/photo-1627582522437-db7b3c20ca47?auto=format&fit=crop&w=800" alt="Cannabis" class="ed-hero-t2-main-img">
                    @endif

                    <div class="ed-hero-t2-info-box box-1">
                        <div class="val">{{ $cmsHero['t2_info_1_val'] }}</div>
                        <div class="lbl">{!! str_replace(' ', '<br>', $cmsHero['t2_info_1_lbl']) !!}</div>
                    </div>
                    <div class="ed-hero-t2-info-box box-2">
                        <div class="val">{{ $cmsHero['t2_info_2_val'] }}</div>
                        <div class="lbl">{!! str_replace(' ', '<br>', $cmsHero['t2_info_2_lbl']) !!}</div>
                    </div>
                </div>
            </div>
        </section>

    @else
        {{-- TYPE 1: DEFAULT --}}
        <section class="ed-hero" style="background-color: {{ $cmsHero['bg_color'] }} !important;">
            <img class="ed-hero-bg" src="{{ $_heroImgPath }}" alt="{{ $category->name }}">
            <div class="ed-hero-overlay"></div>
            <div class="ed-hero-inner">
              <div class="ed-hero-text">
                <h1>{!! $cmsHero['t1_heading'] ?? $category->name !!}</h1>
                <p>{!! $cmsHero['t1_description'] ?? ($category->description ? Str::limit($category->description, 150) : 'Führen Sie einfach unsere Online-Beratung durch, um ein Rezept zu erhalten und das Potenzmittel wird Ihnen in 1-2 Werktage geliefert.') !!}</p>

                @if($hasQuestionnaire)
                    <a href="{{ url('/questionnaire/category/' . $category->id) }}"
                       class="hero-cta"
                       style="background:{{ $cmsHero['cta_color'] }}; color:{{ $cmsHero['cta_text_color'] }}; box-shadow:0 6px 20px {{ $cmsHero['cta_color'] }}55;">
                        {{ $cmsHero['cta_text'] }}
                    </a>
                @else
                    <a href="{{ route('categories') }}" class="hero-cta"
                       style="background:{{ $cmsHero['cta_color'] }}; color:{{ $cmsHero['cta_text_color'] }}; box-shadow:0 6px 20px {{ $cmsHero['cta_color'] }}55;">
                        Browse treatments
                    </a>
                @endif

                <div class="hero-pricing">
                    Behandlungsgebühr {{ $cmsHero['consultation_fee'] }} &euro; +<br>
                    Medikament ab
                    @if(isset($category->price) && $category->price)
                        {{ number_format($category->price, 2, ',', '.') }} &euro;
                    @else
                        41,58 &euro;
                    @endif
                </div>
                @if($cmsHero['rating_enabled'])
                <div class="hero-rating">
                  <span class="stars">★★★★★</span>
                  <strong>{{ $cmsHero['rating_value'] }}</strong> Hervorragend
                  <span style="color:var(--ed-text-muted)">{{ $cmsHero['rating_count'] }} Bewertungen</span>
                </div>
                @endif
              </div>
            </div>
            @if($cmsHero['badge_enabled'])
            <div class="ed-hero-badge"
                 style="background:linear-gradient(135deg, {{ $cmsHero['badge_bg_color_start'] }}e6, {{ $cmsHero['badge_bg_color_end'] }}f2); box-shadow:0 8px 30px {{ $cmsHero['badge_bg_color_start'] }}4d;">
              <div class="badge-big">{{ $cmsHero['badge_percentage'] }}<span>%</span></div>
              <div class="badge-sub">{{ $cmsHero['badge_text'] }}</div>
            </div>
            @endif
        </section>
    @endif
@endif
@break

{{-- ============================  FEATURES BAR  ============================ --}}
@case('features_bar')
<!-- Features Bar -->
@if($cmsFb['enabled'])
<section class="features-bar" style="background:{{ $cmsFb['bg_color'] }};">
    <div class="features-bar-inner">
        @foreach($cmsFb['features'] as $i => $feat)
        @if($feat['enabled'] ?? true)
        <div class="fb-item">
            @php $_fbIconColor = $cmsFb['icon_color'] ?? '#3b6fd4'; $_fbIconBg = $_fbIconColor . '22'; @endphp
            <div class="fb-icon" style="background:{{ $_fbIconBg }}; --fb-icon-color:{{ $_fbIconColor }};">{!! $_fbIcons[$i] !!}</div>
            <div class="fb-text">
                <strong>{!! $feat['title'] !!}</strong>
                <span>{!! $feat['subtitle'] !!}</span>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</section>
@endif
@break

{{-- ============================  STEPS  ============================ --}}
@case('steps')
<!-- 3 Steps -->
@if($cmsSteps['enabled'])
    @php $_stepsType = $cmsSteps['type'] ?? 'type1'; @endphp
    
    @if($_stepsType == 'type2')
        {{-- TYPE 2: TESTOSTERONE --}}
        <section class="steps-section-t2" style="background-color: {{ $cmsSteps['bg_color'] ?? '#fffafb' }};">
            <h2>{!! $cmsSteps['t2_title'] !!}</h2>
            <div class="subtitle">{!! $cmsSteps['t2_subtitle'] !!}</div>
            @if(!empty($cmsSteps['t2_desc']))
                <p class="steps-section-t2-desc">{!! $cmsSteps['t2_desc'] !!}</p>
            @endif

            <div class="steps-grid-t2">
                @foreach($cmsSteps['steps'] as $i => $step)
                <div class="step-card-t2">
                    <div class="step-num-t2">{{ $i + 1 }}</div>
                    <div class="step-icon-t2">
                        <i class="{{ $step['icon'] ?? 'bx bx-check' }}"></i>
                    </div>
                    <h3>{!! $step['t2_title'] !!}</h3>
                    <p>{!! $step['description'] !!}</p>
                </div>
                @endforeach
            </div>
        </section>
    @else
        {{-- TYPE 1: DEFAULT --}}
        <section class="steps-section" style="background-color: {{ $cmsSteps['bg_color'] }};">
            <h2 class="steps-title">
                {!! $cmsSteps['section_title'] !!}<br>
                <span style="color:{{ $cmsSteps['subtitle_color'] }};">{!! $cmsSteps['section_subtitle'] !!}</span>
            </h2>
            <div class="steps-grid">
              @foreach($cmsSteps['steps'] as $i => $step)
              @php
                  $stepImg = !empty($step['image']) && file_exists(public_path('images/upload/'.$step['image']))
                      ? asset('images/upload/'.$step['image'])
                      : ($_stepFallbackImgs[$i] ?? null);
              @endphp
              <div class="step-card">
                <div class="step-num" style="background:{{ $cmsSteps['step_number_bg'] }}; box-shadow:0 4px 12px {{ $cmsSteps['step_number_bg'] }}66;">{{ $i + 1 }}</div>
                <div class="step-card-inner">
                    <h3>{!! $step['title_plain'] !!} <span style="color:{{ $step['highlight_color'] ?? $cmsSteps['subtitle_color'] }};">{!! $step['title_highlighted'] !!}</span></h3>
                    <p>{!! $step['description'] !!}</p>
                    @if($stepImg)
                    <img src="{{ $stepImg }}" alt="{{ $_stepAltTexts[$i] ?? '' }}" loading="lazy">
                    @endif
                </div>
              </div>
              @endforeach
            </div>
        </section>
    @endif
@endif
@break

{{-- ============================  PAYMENT BAR  ============================ --}}
@case('payment_bar')
<!-- Payment Methods -->
@if($cmsPay['enabled'])
<section class="payment-bar" style="background:{{ $cmsPay['bg_color'] }};">
  <div class="payment-bar-inner">
    <span>{{ $cmsPay['label'] }}</span>
    <div class="payment-logos">
      @if($cmsPay['methods']['klarna'] ?? true)<span class="pay-logo">Klarna.</span>@endif
      @if($cmsPay['methods']['visa'] ?? true)<span class="pay-logo">VISA</span>@endif
      @if($cmsPay['methods']['maestro'] ?? true)<span class="pay-logo">Maestro</span>@endif
      @if($cmsPay['methods']['gpay'] ?? true)<span class="pay-logo">G Pay</span>@endif
      @if($cmsPay['methods']['apple_pay'] ?? true)<span class="pay-logo">Apple Pay</span>@endif
      @if($cmsPay['methods']['paypal'] ?? true)<span class="pay-logo">PayPal</span>@endif
    </div>
  </div>
</section>
@endif
@break



{{-- ============================  MEDICAL CONTENT  ============================ --}}
@case('medical_content')
<!-- Medical Content -->
@if($cmsMedical['enabled'])
<div style="background-color: {{ $cmsMedical['bg_color'] }} !important; padding: 40px 0;">
  <div class="med-content" style="padding-top: 0; padding-bottom: 0;">
  <h2>{!! $cmsMedical['section_title'] !!} {{ $category->name }}</h2>

  @if($cmsMedical['toc_enabled'] && !empty($cmsMedical['toc_items']))
  <div class="toc">
    <h3>{{ $cmsMedical['toc_title'] }}</h3>
    @foreach($cmsMedical['toc_items'] as $tocItem)
    <a href="{{ $tocItem['url'] }}">{{ $tocItem['label'] }}</a>
    @endforeach
  </div>
  @endif

  @foreach($cmsMedical['articles'] as $article)
  <div class="med-article" id="{{ $article['anchor_id'] ?? '' }}">
    <h2>{{ $article['heading'] }}</h2>
    @foreach($article['blocks'] ?? [] as $block)
      @switch($block['type'] ?? '')
        @case('text')
          <p>{!! $block['content'] !!}</p>
          @break
        @case('subheading')
          @php $lvl = in_array($block['level'] ?? '', ['h3','h4']) ? $block['level'] : 'h3'; @endphp
          <{!! $lvl !!}>{!! $block['text'] !!}</{!! $lvl !!}>
          @break
        @case('table')
          @if(!empty($block['heading']))<h3>{!! $block['heading'] !!}</h3>@endif
          <div class="med-table-wrapper">
          <table class="med-table" style="border-color:{{ $block['border_color'] ?? '#dee2e6' }};">
            <tr>
              @foreach($block['headers'] ?? [] as $th)
              <th style="background:{{ $block['header_bg'] ?? '#3b6fd4' }}; color:{{ $block['header_text_color'] ?? '#ffffff' }};">{!! $th !!}</th>
              @endforeach
            </tr>
            @foreach($block['rows'] ?? [] as $ri => $row)
            <tr style="{{ ($ri % 2 === 1) ? 'background:' . ($block['alt_row_bg'] ?? '#f8f9fa') . ';' : '' }}">
              @foreach($row as $cell)
              <td style="border-color:{{ $block['border_color'] ?? '#dee2e6' }};">{!! $cell !!}</td>
              @endforeach
            </tr>
            @endforeach
          </table>
          </div>
          @break
        @case('list')
          <ul>
            @foreach($block['items'] ?? [] as $item)
            <li>@if(!empty($item['label']))<strong>{!! $item['label'] !!}</strong> @endif{!! $item['text'] !!}</li>
            @endforeach
          </ul>
          @break
        @case('callout')
          <div class="callout" style="background:{{ $block['bg_color'] ?? '#eff3fb' }}; border-left:4px solid {{ $block['border_color'] ?? '#3b6fd4' }}; padding:16px 20px; border-radius:8px; margin:16px 0;">
            @if(!empty($block['heading']))<h4>{!! $block['heading'] !!}</h4>@endif
            <p style="margin:0;">{!! $block['content'] !!}</p>
          </div>
          @break
      @endswitch
    @endforeach
    @endforeach
  </div>
</div>
@endif
@break

{{-- ============================  DOCTOR REVIEW  ============================ --}}
@case('doctor_review')
<!-- Medical Review -->
@if($cmsDr['enabled'])
@php
  $_drImg = (!empty($cmsDr['image']) && file_exists(public_path('images/upload/' . $cmsDr['image'])))
    ? asset('images/upload/' . $cmsDr['image'])
    : 'https://images.unsplash.com/photo-1612349317150-e410f624c427?auto=format&fit=crop&w=800&q=80';
@endphp
<div style="background-color: {{ $cmsDr['bg_color'] }} !important; padding: 40px 0;">
  <div class="med-review" style="padding-top: 0; padding-bottom: 0;">
    <div class="med-review-img">
      <img src="{{ $_drImg }}" alt="{{ $cmsDr['name'] }}" loading="lazy">
    </div>
  <div class="med-review-text">
    <div class="med-review-doc">{!! $cmsDr['name'] !!}</div>
    <div class="med-review-role">{!! $cmsDr['role'] !!}</div>
    <h3>{!! $cmsDr['title'] !!}</h3>
    @foreach($cmsDr['paragraphs'] as $para)
    <p>{!! $para !!}</p>
    @endforeach
    @if(!empty($cmsDr['link_text']))
    <p>Weitere Informationen finden Sie in unserem <a href="{{ $cmsDr['link_url'] }}" style="color:var(--ed-primary, #3b6fd4);">{{ $cmsDr['link_text'] }}</a>.</p>
    @endif
    @if($cmsDr['show_last_updated'])
    <div class="update">Letzte Aktualisierung am {{ date('d/m/Y') }}</div>
    @endif
  </div>
  </div>
</div>
@endif
@break

{{-- ============================  FAQ  ============================ --}}
@case('faq')
<!-- FAQ -->
@if($cmsFaq['enabled'] && !empty($cmsFaq['items']))
<section class="py-5" style="background-color: {{ $cmsFaq['bg_color'] }} !important;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-5 text-center">
                    <h2 class="display-6 fw-bold mb-2">{{ $cmsFaq['title'] }}</h2>
                    @if(!empty($cmsFaq['subtitle']))
                        <div class="faq-subtitle-extra" style="color:{{ $cmsFaq['subtitle_color'] ?? '#e63946' }};">
                           <span>{!! $cmsFaq['subtitle'] !!}</span>
                        </div>
                    @endif
                </div>

                <style>
                    .faq-subtitle-extra {
                        font-size: 1.75rem;
                        font-weight: 800;
                        margin-bottom: 30px;
                        display: inline-block;
                        position: relative;
                        padding-bottom: 8px;
                    }
                    .faq-subtitle-extra span {
                        border-bottom: none;
                    }
                    #faqAccordion .accordion-button {
                        background-color: {{ $cmsFaq['question_bg_color'] ?? '#ffffff' }} !important;
                        color: {{ $cmsFaq['question_color'] ?? '#1a1a1a' }} !important;
                    }
                    #faqAccordion .accordion-button:not(.collapsed) {
                        background-color: {{ $cmsFaq['question_bg_active_color'] ?? '#ffffff' }} !important;
                        color: {{ $cmsFaq['question_active_color'] ?? '#1a1a1a' }} !important;
                    }
                    #faqAccordion .accordion-button:hover {
                        background-color: {{ $cmsFaq['question_bg_hover_color'] ?? '#f8f9fa' }} !important;
                        color: {{ $cmsFaq['question_hover_color'] ?? '#3b6fd4' }} !important;
                    }
                    #faqAccordion .accordion-collapse {
                        background-color: {{ $cmsFaq['answer_bg_color'] ?? '#ffffff' }} !important;
                    }
                    #faqAccordion .accordion-body {
                        color: {{ $cmsFaq['answer_text_color'] ?? '#6c757d' }} !important;
                    }
                    #faqAccordion .accordion-button:focus {
                        box-shadow: none;
                        border-color: rgba(0,0,0,0.125);
                    }
                </style>
                    <div class="accordion" id="faqAccordion">
                        @foreach($cmsFaq['items'] as $index => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">
                                        {!! $faq['question'] ?? '' !!}
                                    </button>
                                </h2>
                                <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {!! $faq['answer'] ?? '' !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@break

{{-- ============================  TESTO INFO  ============================ --}}
@case('testo_info')
<!-- Section 8: Testosterone Info -->
@if($cmsTestoInfo['enabled'])
<section class="testo-section-wrap" style="background-color: {{ $cmsTestoInfo['bg_color'] }} !important;">
  <div class="testo-section">
    <div class="testo-content">
      <h2>{!! $cmsTestoInfo['heading'] !!}</h2>
      <p>{!! $cmsTestoInfo['paragraph_1'] !!}</p>
      <p>{!! $cmsTestoInfo['paragraph_2'] !!}</p>
      <p>{!! $cmsTestoInfo['paragraph_3'] !!}</p>
    </div>
    <div class="testo-grid">
      @foreach($cmsTestoInfo['cards'] as $card)
      <div class="testo-card">
        <div class="testo-icon-wrap"><i class="bi {{ $card['icon'] }}"></i></div>
        <h4>{!! $card['title'] !!}</h4>
        <p>{!! $card['subtitle'] !!}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif
@break

{{-- ============================  TESTO TREATMENTS  ============================ --}}
@case('testo_treatments')
<!-- Section 9: Testosterone Treatments -->
@if($cmsTestoTreatments['enabled'])
<section class="testo-treat-section" style="background-color: {{ $cmsTestoTreatments['bg_color'] }} !important;">
  <div class="testo-treat-header">
    <h2>{!! $cmsTestoTreatments['heading'] !!}</h2>
    <p>{!! $cmsTestoTreatments['subheading'] !!}</p>
  </div>
  <div class="testo-treat-grid">
    @foreach($cmsTestoTreatments['cards'] as $i => $card)
    @php
      $_treatImg = (!empty($card['image']) && file_exists(public_path('images/upload/'.$card['image'])))
          ? asset('images/upload/'.$card['image'])
          : ($_testoTreatFallbackImgs[$i] ?? null);
    @endphp
    <div class="testo-treat-card">
      @if($_treatImg)
      <img src="{{ $_treatImg }}" alt="{{ $card['title'] }}" class="testo-treat-img" loading="lazy">
      @endif
      <div class="testo-treat-content">
        <h3>{!! $card['title'] !!}</h3>
        <p>{!! $card['description'] !!}</p>
        <a href="{{ $card['button_url'] }}" class="testo-treat-btn">{{ $card['button_text'] }}</a>
      </div>
    </div>
    @endforeach
  </div>
</section>
@endif
@break

{{-- ============================  SECURITY  ============================ --}}
@case('security')
<!-- Section 10: Security / Trust -->
@if($cmsSecurity['enabled'])
<section class="security-section" style="background-color: {{ $cmsSecurity['bg_color'] }};">
  <div class="security-header">
    <h2>{!! $cmsSecurity['heading'] !!}</h2>
    <p>{!! $cmsSecurity['subheading'] !!}</p>
  </div>
  <div class="security-grid">
    @foreach($cmsSecurity['cards'] as $card)
    <div class="security-card">
      <div class="security-icon"><i class="bi {{ $card['icon'] }}"></i></div>
      <h3>{!! $card['title'] !!}</h3>
      <p>{!! $card['description'] !!}</p>
    </div>
    @endforeach
  </div>
</section>
@endif
@break

@endswitch
@endforeach

<!-- Footer -->
@include('layout.partials.footer')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
