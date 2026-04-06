<!DOCTYPE html>
<html lang="en">
<head>
    @php
    $setting = App\Models\Setting::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - {{ $setting->business_name }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link href="{{asset('css/new-design.css')}}" rel="stylesheet">
    
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

        /* ed-hero variables */
        :root {
            --ed-radius-lg: 20px;
            --ed-dark: #1a1a1a;
            --ed-text-light: #555;
            --ed-text-muted: #888;
            --ed-max-width: 1280px;
        }
        .ed-hero {
            position: relative;
            width: 100%;
            min-height: 520px;
            overflow: hidden;
            margin-bottom: 0;
        }
        .ed-hero-bg {
            position: absolute; inset: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            object-position: center top;
        }
        .ed-hero-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to right, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.7) 45%, transparent 70%);
        }
        .ed-hero-inner {
            position: relative; z-index: 2;
            max-width: var(--ed-max-width);
            margin: 0 auto;
            padding: 48px 24px;
            display: flex;
            align-items: center;
            min-height: 520px;
        }
        .ed-hero-text {
            max-width: 440px;
            background: rgba(255,255,255,0.3);
            backdrop-filter: blur(16px);
            border-radius: var(--ed-radius-lg);
            padding: 30px 28px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.06);
        }
        .ed-hero-text h1 {
            font-size: 2.2rem; font-weight: 800; line-height: 1.15;
            color: var(--ed-dark); margin-bottom: 14px;
            font-family: inherit;
        }
        .ed-hero-text > p {
            font-size: 0.85rem; color: var(--ed-text-light); line-height: 1.6; margin-bottom: 20px;
        }
        .hero-cta {
            display: inline-flex; align-items: center;
            padding: 16px 36px; background: #3b6fd4;
            color: #fff; border-radius: 50px;
            font-size: 1rem; font-weight: 700;
            box-shadow: 0 6px 20px rgba(59,111,212,0.35);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .hero-cta:hover { background: #2a52a8; color: #fff; transform: translateY(-2px); }
        .hero-pricing { margin-top: 18px; font-size: 0.82rem; color: var(--ed-text-light); line-height: 1.5; }
        .hero-rating { margin-top: 14px; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; }
        .hero-rating .stars { color: #f59e0b; font-size: 1rem; }
        .ed-hero-badge {
            position: absolute;
            bottom: 48px; right: 80px;
            background: linear-gradient(135deg, rgba(59,111,212,0.9), rgba(30,60,140,0.95));
            backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 28px 32px;
            color: #fff;
            text-align: center;
            box-shadow: 0 8px 30px rgba(59,111,212,0.3);
            z-index: 3;
        }
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
        @media (max-width: 480px) {
            .ed-hero-text h1 { font-size: 1.4rem; }
        }
        /* FEATURES BAR */
        .features-bar { background: #fafafa; border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 28px 0; }
        .features-bar-inner { max-width: var(--ed-max-width); margin: 0 auto; padding: 0 24px; display: flex; justify-content: space-between; gap: 24px; }
        .fb-item { display: flex; align-items: center; gap: 12px; flex: 1; }
        .fb-icon { width: 44px; height: 44px; border-radius: 50%; background: #eff3fb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .fb-icon svg { width: 20px; height: 20px; stroke: #3b6fd4; fill: none; stroke-width: 2; }
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
        .steps-section { padding: 72px 24px; background: linear-gradient(180deg, #eaf2ff 0%, #fff 100%); text-align: center; overflow: hidden; }
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

        @media (max-width: 768px) {
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
<!-- changes -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
    <div class="container"> 
        <a class="navbar-brand" href="{{ url('/') }}">
            @if($setting->company_logo && file_exists(public_path('images/upload/'.$setting->company_logo)))
                <img src="{{ $setting->logo }}" alt="{{ $setting->business_name }}">
            @else
                <img src="{{ url('/images/upload_empty/fuxxlogo.png') }}" alt="{{ $setting->business_name }}">
            @endif
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories') }}">Treatments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}#how-it-works">How it works</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/about-us') }}">About us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}#faq">Help</a>
                </li>
            </ul>
            
            <div class="d-flex gap-2">
                @if(auth()->check())
                    <a href="{{ url('user_profile') }}" class="btn btn-link text-dark text-decoration-none">
                        <i class="bi bi-person"></i> {{ auth()->user()->name }}
                    </a>
                @else
                    <a href="{{ url('patient-login') }}" class="btn btn-link text-dark text-decoration-none">
                        <i class="bi bi-person"></i> Sign in
                    </a>
                @endif
                <a href="{{ route('categories') }}" class="btn btn-primary">Start treatment</a>
            </div>
        </div>
    </div>
</nav>

<!-- Breadcrumb -->
<div class="bg-light border-bottom">
    <div class="container py-3">
        <a href="{{ route('categories') }}" class="breadcrumb-link text-decoration-none">
            <i class="bi bi-chevron-left"></i> Back to all treatments
        </a>
    </div>
</div>

@php
// --- CMS Section Data (with defaults) ---
$_cms     = $category->cms_sections ?? [];

$cmsHero  = array_merge([
    'enabled'              => true,
    'cta_text'             => 'Zu den medizinischen Fragen',
    'cta_color'            => '#3b6fd4',
    'consultation_fee'     => '29',
    'badge_enabled'        => true,
    'badge_percentage'     => '85',
    'badge_text'           => 'der Männer berichten von einer Besserung',
    'badge_bg_color_start' => '#3b6fd4',
    'badge_bg_color_end'   => '#1e3c8c',
    'rating_enabled'       => true,
    'rating_value'         => '4,79',
    'rating_count'         => '14.082',
], $_cms['hero'] ?? []);

$cmsFb = array_merge([
    'enabled'  => true,
    'bg_color' => '#fafafa',
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
    'enabled'          => true,
    'section_title'    => '3 einfache Schritte',
    'section_subtitle' => '100 % online',
    'subtitle_color'   => '#3b6fd4',
    'step_number_bg'   => '#3b6fd4',
    'steps' => [
        ['title_plain' => 'Füllen Sie den',  'title_highlighted' => 'medizinischen Fragebogen aus', 'highlight_color' => '#3b6fd4', 'description' => 'Starten Sie die Online-Konsultation und beantworten Sie die medizinischen Fragen.',    'image' => null],
        ['title_plain' => 'Wählen Sie die',  'title_highlighted' => 'gewünschte Behandlung',        'highlight_color' => '#3b6fd4', 'description' => 'Der behandelnde Arzt prüft Ihre Angaben und stellt Ihnen bei Bedarf ein Rezept aus.', 'image' => null],
        ['title_plain' => 'Lieferung in',    'title_highlighted' => '1–2 Werktagen',                'highlight_color' => '#3b6fd4', 'description' => 'Sie erhalten Ihre Medikamente diskret und sicher.',                                    'image' => null],
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
@endphp

<!-- Hero Section -->
@if($cmsHero['enabled'])
<section class="ed-hero">
    @if(isset($category->image) && file_exists(public_path('images/upload/'.$category->image)))
        <img class="ed-hero-bg" src="{{ asset('images/upload/'.$category->image) }}" alt="{{ $category->name }}">
    @else
        <img class="ed-hero-bg" src="https://images.unsplash.com/photo-1505751172876-fa1923c5c528?auto=format&fit=crop&w=1280&q=80" alt="{{ $category->name }}">
    @endif
    <div class="ed-hero-overlay"></div>
    <div class="ed-hero-inner">
      <div class="ed-hero-text">
        <h1>{{ $category->name }}</h1>
        <p>{{ $category->description ? Str::limit($category->description, 150) : 'Führen Sie einfach unsere Online-Beratung durch, um ein Rezept zu erhalten und das Potenzmittel wird Ihnen in 1-2 Werktage geliefert.' }}</p>

        @if($hasQuestionnaire)
            <a href="{{ auth()->check() ? url('/questionnaire/category/' . $category->id) : url('/patient-login?redirect_to=' . urlencode('/questionnaire/category/' . $category->id)) }}"
               class="hero-cta"
               style="background:{{ $cmsHero['cta_color'] }}; box-shadow:0 6px 20px {{ $cmsHero['cta_color'] }}55;">
                {{ $cmsHero['cta_text'] }}
            </a>
        @else
            <a href="{{ route('categories') }}" class="hero-cta"
               style="background:{{ $cmsHero['cta_color'] }}; box-shadow:0 6px 20px {{ $cmsHero['cta_color'] }}55;">
                Browse treatments
            </a>
        @endif

        <div class="hero-pricing">
            Behandlungsgebühr {{ $cmsHero['consultation_fee'] }} &euro; +<br>
            Medikament ab
            @if(isset($category->price) && $category->price)
                {{ number_format($category->price, 2) }} &euro;
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

<!-- Features Bar -->
@if($cmsFb['enabled'])
<section class="features-bar" style="background:{{ $cmsFb['bg_color'] }};">
    <div class="features-bar-inner">
        @foreach($cmsFb['features'] as $i => $feat)
        @if($feat['enabled'] ?? true)
        <div class="fb-item">
            <div class="fb-icon">{!! $_fbIcons[$i] !!}</div>
            <div class="fb-text">
                <strong>{{ $feat['title'] }}</strong>
                <span>{{ $feat['subtitle'] }}</span>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</section>
@endif

<!-- 3 Steps -->
@if($cmsSteps['enabled'])
<section class="steps-section">
    <h2 class="steps-title">
        {{ $cmsSteps['section_title'] }}<br>
        <span style="color:{{ $cmsSteps['subtitle_color'] }};">{{ $cmsSteps['section_subtitle'] }}</span>
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
            <h3>{{ $step['title_plain'] }} <span style="color:{{ $step['highlight_color'] ?? $cmsSteps['subtitle_color'] }};">{{ $step['title_highlighted'] }}</span></h3>
            <p>{{ $step['description'] }}</p>
            @if($stepImg)
            <img src="{{ $stepImg }}" alt="{{ $_stepAltTexts[$i] ?? '' }}" loading="lazy">
            @endif
        </div>
      </div>
      @endforeach
    </div>
</section>
@endif

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

@php
// --- CMS: Medical Content, Doctor Review, FAQ ---
$cmsMedical = array_merge([
    'enabled'       => true,
    'section_title' => 'Behandlungen bei',
    'toc_enabled'   => true,
    'toc_title'     => 'Themenliste',
    'toc_items'     => [],
    'articles'      => [],
], $_cms['medical_content'] ?? []);

$cmsDr = array_merge([
    'enabled'           => true,
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
    'enabled' => true,
    'title'   => 'Frequently asked questions',
    'items'   => [
        ['question' => 'How long does the consultation process take?',  'answer' => 'The entire process typically takes 24-48 hours from questionnaire submission to prescription approval and shipping.'],
        ['question' => 'Is this treatment suitable for me?',            'answer' => 'Our doctors will review your questionnaire and medical history to determine if this treatment is appropriate for your specific situation.'],
        ['question' => 'What if I have questions about my medication?', 'answer' => 'You can contact our medical team at any time with questions about your treatment. We provide ongoing support throughout your treatment period.'],
    ],
], $_cms['faq'] ?? []);
@endphp

<!-- Medical Content -->
@if($cmsMedical['enabled'])
<div class="med-content">
  <h2>{{ $cmsMedical['section_title'] }} {{ $category->name }}</h2>

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
          <p>{{ $block['content'] }}</p>
          @break
        @case('subheading')
          @php $lvl = in_array($block['level'] ?? '', ['h3','h4']) ? $block['level'] : 'h3'; @endphp
          <{{ $lvl }}>{{ $block['text'] }}</{{ $lvl }}>
          @break
        @case('table')
          @if(!empty($block['heading']))<h3>{{ $block['heading'] }}</h3>@endif
          <div class="med-table-wrapper">
          <table class="med-table" style="border-color:{{ $block['border_color'] ?? '#dee2e6' }};">
            <tr>
              @foreach($block['headers'] ?? [] as $th)
              <th style="background:{{ $block['header_bg'] ?? '#3b6fd4' }}; color:{{ $block['header_text_color'] ?? '#ffffff' }};">{{ $th }}</th>
              @endforeach
            </tr>
            @foreach($block['rows'] ?? [] as $ri => $row)
            <tr style="{{ ($ri % 2 === 1) ? 'background:' . ($block['alt_row_bg'] ?? '#f8f9fa') . ';' : '' }}">
              @foreach($row as $cell)
              <td style="border-color:{{ $block['border_color'] ?? '#dee2e6' }};">{{ $cell }}</td>
              @endforeach
            </tr>
            @endforeach
          </table>
          </div>
          @break
        @case('list')
          <ul>
            @foreach($block['items'] ?? [] as $item)
            <li>@if(!empty($item['label']))<strong>{{ $item['label'] }}</strong> @endif{{ $item['text'] }}</li>
            @endforeach
          </ul>
          @break
        @case('callout')
          <div class="callout" style="background:{{ $block['bg_color'] ?? '#eff3fb' }}; border-left:4px solid {{ $block['border_color'] ?? '#3b6fd4' }}; padding:16px 20px; border-radius:8px; margin:16px 0;">
            @if(!empty($block['heading']))<h4>{{ $block['heading'] }}</h4>@endif
            <p style="margin:0;">{{ $block['content'] }}</p>
          </div>
          @break
      @endswitch
    @endforeach
  </div>
  @endforeach
</div>
@endif

<!-- Medical Review -->
@if($cmsDr['enabled'])
@php
  $_drImg = (!empty($cmsDr['image']) && file_exists(public_path('images/upload/' . $cmsDr['image'])))
    ? asset('images/upload/' . $cmsDr['image'])
    : 'https://images.unsplash.com/photo-1612349317150-e410f624c427?auto=format&fit=crop&w=800&q=80';
@endphp
<div class="med-review">
  <div class="med-review-img">
    <img src="{{ $_drImg }}" alt="{{ $cmsDr['name'] }}" loading="lazy">
  </div>
  <div class="med-review-text">
    <div class="med-review-doc">{{ $cmsDr['name'] }}</div>
    <div class="med-review-role">{{ $cmsDr['role'] }}</div>
    <h3>{{ $cmsDr['title'] }}</h3>
    @foreach($cmsDr['paragraphs'] as $para)
    <p>{{ $para }}</p>
    @endforeach
    @if(!empty($cmsDr['link_text']))
    <p>Weitere Informationen finden Sie in unserem <a href="{{ $cmsDr['link_url'] }}" style="color:var(--ed-primary, #3b6fd4);">{{ $cmsDr['link_text'] }}</a>.</p>
    @endif
    @if($cmsDr['show_last_updated'])
    <div class="update">Letzte Aktualisierung am {{ date('d/m/Y') }}</div>
    @endif
  </div>
</div>
@endif

<!-- FAQ -->
@if($cmsFaq['enabled'] && !empty($cmsFaq['items']))
<section class="py-5 bg-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-5">
                    <h2 class="display-6 fw-bold mb-4">{{ $cmsFaq['title'] }}</h2>
                    <div class="accordion" id="faqAccordion">
                        @foreach($cmsFaq['items'] as $index => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">
                                        {{ $faq['question'] ?? '' }}
                                    </button>
                                </h2>
                                <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        {{ $faq['answer'] ?? '' }}
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

<!-- Footer -->
<footer class="footer-dark text-light py-5">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Company Info -->
            <div class="col-md-6 col-lg-3">
                @if($setting->company_logo && file_exists(public_path('images/upload/'.$setting->company_logo)))
                    <img src="{{ $setting->logo }}" alt="{{ $setting->business_name }}" class="footer-logo mb-3">
                @else
                    <img src="{{ url('/images/upload_empty/fuxxlogo.png') }}" alt="{{ $setting->business_name }}" class="footer-logo mb-3">
                @endif
                <p class="small mb-3">Your trusted online medical practice for discreet and secure medical consultation.</p>
                <div class="d-flex gap-3">
                    @if($setting->facebook_link)
                        <a href="{{ $setting->facebook_link }}" class="text-light" target="_blank"><i class="bi bi-facebook"></i></a>
                    @endif
                    @if($setting->twitter_link)
                        <a href="{{ $setting->twitter_link }}" class="text-light" target="_blank"><i class="bi bi-twitter"></i></a>
                    @endif
                    @if($setting->instagram_link)
                        <a href="{{ $setting->instagram_link }}" class="text-light" target="_blank"><i class="bi bi-instagram"></i></a>
                    @endif
                    @if($setting->linkedin_link)
                        <a href="{{ $setting->linkedin_link }}" class="text-light" target="_blank"><i class="bi bi-linkedin"></i></a>
                    @endif
                </div>
            </div>

            <!-- Treatments -->
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-3">Treatments</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('categories') }}" class="text-light text-decoration-none">All treatments</a></li>
                    @foreach($treatments->take(5) as $treatmentItem)
                        <li class="mb-2">
                            <a href="{{ route('categories', ['treatment' => $treatmentItem->id]) }}" class="text-light text-decoration-none">
                                {{ $treatmentItem->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Company -->
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-3">Company</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ url('/about-us') }}" class="text-light text-decoration-none">About us</a></li>
                    <li class="mb-2"><a href="{{ url('/') }}#how-it-works" class="text-light text-decoration-none">How it works</a></li>
                    <li class="mb-2"><a href="{{ url('show-doctors') }}" class="text-light text-decoration-none">Our doctors</a></li>
                    <li class="mb-2"><a href="{{ url('our_blogs') }}" class="text-light text-decoration-none">Blog</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-3">Support</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Help center</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Contact</a></li>
                    <li class="mb-2"><a href="{{ url('/') }}#faq" class="text-light text-decoration-none">FAQ</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Shipping & delivery</a></li>
                </ul>
            </div>
        </div>

        <div class="border-top border-secondary pt-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="small mb-0">© {{ date('Y') }} {{ $setting->business_name }} All rights reserved.</p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-3 small">
                        <a href="{{ url('/privacy-policy') }}" class="text-light text-decoration-none">Privacy</a>
                        <a href="#" class="text-light text-decoration-none">Terms</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
