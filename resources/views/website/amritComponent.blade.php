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

<!-- Medical Content -->
<div class="med-content">
  <h2>Behandlungen bei {{ $category->name ?? 'Erektionsstörungen' }}</h2>

  <div class="toc">
    <h3>Themenliste</h3>
    <a href="#was-ist-ed">Was ist eine erektile Dysfunktion?</a>
    <a href="#wann-behandeln">Wann sollte ED behandelt werden?</a>
    <a href="#wie-behandeln">Wie kann ich eine erektile Dysfunktion behandeln?</a>
    <a href="#pde5">PDE5-Hemmer Medikation</a>
    <a href="#nebenwirkungen">K&ouml;nnen Nebenwirkungen auftreten?</a>
    <a href="#kontraindikation">Wann man PDE5-Hemmer nicht einnehmen sollte</a>
    <a href="#alternativen">Alternative Behandlungsm&ouml;glichkeiten</a>
    <a href="#psycho">Psychologische und Wellness Therapien</a>
    <a href="#andere">Andere Behandlungsm&ouml;glichkeiten</a>
    <a href="#warum-drfuxx">Warum dr.fuxx?</a>
  </div>

  <div class="med-article" id="was-ist-ed">
    <h2>Was ist eine erektile Dysfunktion?</h2>
    <p>Zwischen 3 und 5 Millionen M&auml;nner leiden alleine in Deutschland an einer erektilen Dysfunktion. Das entspricht in etwa jedem f&uuml;nften Mann und zeigt deutlich, dass es sich dabei um ein sehr h&auml;ufig auftretendes Krankheitsbild handelt. Unter einer erektilen Dysfunktion versteht man die anhaltende oder immer wiederkehrende Unf&auml;higkeit eine Erektion dauerhaft und stark genug aufrechtzuerhalten, um Geschlechtsverkehr zu haben.</p>
    <p>M&auml;nner jeden Alters sind davon betroffen und suchen nach Gr&uuml;nden und L&ouml;sungen f&uuml;r ihre Potenzprobleme. H&auml;ufig ist die erektile Dysfunktion aber gut behandelbar. Es gibt eine Bandbreite an organischen aber auch psychologischen Ursachen, die eine erektile Dysfunktion ausl&ouml;sen k&ouml;nnen.</p>
  </div>

  <div class="med-article" id="wann-behandeln">
    <h2>Wann sollte ED behandelt werden?</h2>
    <p>Wenn ein Mann &uuml;ber einen l&auml;ngeren Zeitraum hinweg oder immer wieder mit Erektionsst&ouml;rungen konfrontiert ist und unter diesem Zustand leidet, dann sollte die erektile Dysfunktion behandelt werden. Erektionsst&ouml;rungen und Potenzprobleme k&ouml;nnen unterschiedlicher Natur sein. Oft ist die Erektion entweder nicht stark genug oder aber sie dauert nicht lange genug f&uuml;r den Sexualakt an. Dieses Problem kann jedoch behandelt werden. Bei dr.fuxx haben Sie die M&ouml;glichkeit Online &Auml;rzte zu konsultieren und eine erektile Dysfunktion rasch und effektiv zu behandeln.</p>
  </div>

  <div class="med-article" id="wie-behandeln">
    <h2>Wie kann ich eine erektile Dysfunktion behandeln?</h2>
    <p>Unter dem Begriff erektile Dysfunktion (ED) versteht man das Unvermögen, eine Erektion zu bekommen oder diese lang genug f&uuml;r den Sexualakt aufrechtzuerhalten. F&uuml;r ED gibt es viele verschiedene Ursachen, doch letztendlich tritt eine erektile Dysfunktion dann auf, wenn zu wenig Blut in den Penis flie&szlig;t bzw. w&auml;hrend einer Erektion wieder herausflie&szlig;t. Dieser Umstand ist teilweise auf ein Enzym namens PDE5 zur&uuml;ckzuf&uuml;hren. Sogenannte PDE5-Hemmer, die oral eingenommen werden, sorgen daf&uuml;r, den Blutfluss in das Glied zu erh&ouml;hen und so eine Erektion &uuml;ber einen l&auml;ngeren Zeitraum aufrechtzuerhalten.</p>
    <p>Da die Blutgef&auml;&szlig;e des Glieds im nicht-erigierten Zustand verengt sind, enth&auml;lt der Schwellk&ouml;rper nur wenig Blut. Wenn es zu einer sexuellen Erregung kommt, wird das sogenannte zyklische Guanin-Monophosphat (cGMP), ausgesch&uuml;ttet. Dieses f&uuml;hrt dazu, dass sich die Gef&auml;&szlig;muskulatur entspannt und mehr Blut in die Schwellk&ouml;rper str&ouml;men kann &ndash; der Penis wird steif. PDE5-Hemmer wirken dadurch, dass sie das Enzym Phosphodiesterase-5 (kurz PDE-5) blockieren. Dieses ist f&uuml;r den Abbau von cGMP verantwortlich und beeinflusst so die Erektionsf&auml;higkeit.</p>
  </div>

  <div class="med-article" id="pde5">
    <h2>PDE5-Hemmer Medikation</h2>
    <p>Phosphodiesterase-5-Hemmer, kurz PDE-5-Hemmer, bezeichnen eine Wirkstoffgruppe, die in Medikamenten zur Behandlung der erektilen Dysfunktion zum Einsatz kommt. In Deutschland sind vier verschiedene PDE-5-Hemmer zugelassen.</p>
    <p>Erektile Dysfunktion, kurz ED kommt dadurch zustande, dass nicht gen&uuml;gend Blut in das Glied flie&szlig;t. Dieser Umstand l&auml;sst sich h&auml;ufig auf ein Enzym namens Phosphodiesterase-5, kurz PDE5 zur&uuml;ckf&uuml;hren. In den meisten F&auml;llen wird Ihnen Ihr Arzt deshalb zu einer Einnahme von PDE5-Hemmern raten.</p>

    <h3>Verf&uuml;gbare Potenztabletten</h3>
    <div class="med-table-wrapper">
    <table class="med-table">
      <tr><th>Medikation</th><th>Viagra&reg;</th><th>Cialis&reg;</th><th>Levitra&reg;</th><th>Spedra&reg;</th></tr>
      <tr><td>Wirkstoff</td><td>Sildenafil</td><td>Tadalafil</td><td>Vardenafil</td><td>Avanafil</td></tr>
      <tr><td>Wirksam</td><td>nach 30 Min f&uuml;r 4 Std.</td><td>nach 30 Min f&uuml;r 36 Std.</td><td>nach 30 Min f&uuml;r 6 Std.</td><td>nach 15 Min f&uuml;r 6 Std.</td></tr>
      <tr><td>Dosierung</td><td>25 mg, 50 mg, 100 mg</td><td>10 mg, 20 mg</td><td>5 mg, 10 mg, 20 mg</td><td>50 mg, 100 mg, 200 mg</td></tr>
      <tr><td>Beschreibung</td><td>Zuverl&auml;ssig, bekannt, bew&auml;hrt</td><td>Lange wirkend f&uuml;r Sex ohne Zeitdruck</td><td>Sehr gut vertr&auml;glich, auch f&uuml;r M&auml;nner &uuml;ber 50</td><td>Schnellste Wirkung, f&uuml;r die, die Sex nicht immer planen</td></tr>
    </table>
    </div>
    <p><strong>Diese Behandlungen k&ouml;nnen von den &Auml;rzten auf der Plattform verschrieben werden</strong></p>
    <p>Sildenafil und Tadalafil sind die am h&auml;ufigsten eingesetzten Wirkstoffe in der Behandlung von erektiler Dysfunktion. Beide fallen in die Kategorie der PDE5-Hemmer und entfalten kurz nach der Einnahme ihre erektionssteigernde Wirkung.</p>
    <p>Seit 2013 der Patentschutz f&uuml;r das Originalmedikament von Pfizer fiel, haben zahlreiche kosteng&uuml;nstigere Nachahmerprodukte (sogenannte Generika) den Markt erobert. Bei dr.fuxx k&ouml;nnen neben Viagra&reg; auch noch viele weitere PDE-5-Hemmer mit dem Wirkstoff Sildenafil von den behandelnden &Auml;rzten verschrieben werden.</p>
  </div>

  <div class="med-article" id="nebenwirkungen">
    <h2>K&ouml;nnen Nebenwirkungen auftreten?</h2>
    <p>Wie jedes andere verschreibungspflichtige Arzneimittel k&ouml;nnen auch PDE5-Hemmer mit m&ouml;glichen Nebenwirkungen einhergehen.</p>
    <p>Zu den h&auml;ufigsten Nebenwirkungen bei einer Behandlung mit PDE-5-Hemmern geh&ouml;ren Kopfschmerzen, Hautr&ouml;tungen im Gesicht und am Oberk&ouml;rper oder eine verstopfte Nase. Ihr Arzt wird Sie &uuml;ber m&ouml;gliche Nebenwirkungen in Kenntnis setzen.</p>
  </div>

  <div class="med-article" id="kontraindikation">
    <h2>Wann man PDE5-Hemmer nicht einnehmen sollte</h2>
    <p>In gewissen F&auml;llen sollten PDE 5-Hemmer nicht eingenommen werden. Sprechen Sie bitte immer mit Ihrem Arzt bevor Sie mit der Medikation beginnen.</p>
    <p>Wenn Sie unter schweren Herz-Kreislauf- und Lebererkrankungen leiden oder innerhalb der vergangenen sechs Monate einen Herzinfarkt oder einen Schlaganfall erlitten haben, sollte Sie keine PDE5-Hemmer nehmen. Auch bei bestimmten Augenkrankheiten ist die Anwendung von Phosphodiesterasehemmern kontraindiziert.</p>
    <p>Weiters gibt es eine Reihe an Medikamenten, die Stickstoffmonoxid absondern, Mediziner bezeichnen sie als NO-Donatoren. Werden PDE-5-Hemmer und Stickstoffmonoxid kombiniert so kann es im K&ouml;rper zu einem starken Blutdruckabfall kommen, der im schlimmsten Fall lebensbedrohlich sein kann.</p>
    <p>Generell ist es m&ouml;glich, dass sich Potenzmittel und andere Arzneistoffe gegenseitig in ihrer Wirksamkeit beeinflussen.</p>
    <div class="callout">
      <h4>Wie dr.fuxx helfen kann?</h4>
      <p>Bei dr.fuxx k&ouml;nnen M&auml;nner eine medizinische Konsultation beginnen oder sich ihr Rezept von einem der behandelnden &Auml;rzte ausstellen lassen. F&uuml;llen Sie einfach einen kurzen Fragebogen aus und wenn keine gesundheitlichen Einw&auml;nde aufkommen wird Ihnen der Arzt ein Rezept ausstellen und es weiterleiten. Unser Service k&uuml;mmert sich dann darum, dass Sie Ihre Bestellung so rasch wie m&ouml;glich erhalten.</p>
    </div>
  </div>

  <div class="med-article" id="alternativen">
    <h2>Alternative Behandlungsm&ouml;glichkeiten</h2>
    <h3>Nat&uuml;rliche Behandlungsm&ouml;glichkeiten</h3>
    <p>Neben einer medikament&ouml;sen Behandlung gibt es auch eine Vielzahl an nat&uuml;rlichen Alternativen, die zus&auml;tzlich Anwendung finden k&ouml;nnen. Einigen Pflanzen und nat&uuml;rlichen Substanzen wird eine potenzsteigernde Wirkung nachgesagt. Die Bekanntesten stellen wir Ihnen hier vor.</p>
    <ul>
      <li><strong>Ginkgo:</strong> Der Wirkstoff wird aus den Bl&auml;ttern des Ginkgobaumes gewonnen, der in China beheimatet, mittlerweile aber auf der ganzen Welt zuhause ist. Die Bl&auml;tter enthalten Flavonoide, Terpene, Ketone und S&auml;uren, die die arterielle Durchblutung verbessern.</li>
      <li><strong>Ginseng:</strong> Eine der beliebtesten pflanzlichen Substanzen. Sie wird in China bereits seit Jahrtausenden als Aphrodisiakum verwendet.</li>
    </ul>
    <p>Diese Substanzen werden zwar von Ihren Herstellern vermarktet, allerdings gibt es kaum aussagekr&auml;ftige Studien, die ihre Wirkung belegen. Au&szlig;erdem werden diese pflanzlichen Pr&auml;parate nicht von &Auml;rzten verschrieben.</p>
  </div>

  <div class="med-article" id="psycho">
    <h2>Psychologische und Wellness Therapien</h2>
    <p>Eine Psychotherapie als Erg&auml;nzung zur medikament&ouml;sen Behandlung kann ebenfalls einen Unterschied machen, wenn psychologische Faktoren bei der Entstehung von ED mitspielen.</p>
    <h4>Coaching oder Psychologe</h4>
    <p>F&uuml;r M&auml;nner, bei denen eindeutig psychologische Bedingungen zu einer Erektionsst&ouml;rung f&uuml;hren, ist anzunehmen, dass diese sich auch anderweitig bemerkbar machen. Coaching bietet sich bei niedrigem Selbstbewusstsein, Hemmungen oder Partnerkonflikten als Therapiemethode an.</p>
    <h4>K&ouml;rperliches Training</h4>
    <p>Auch dies ist Teil eines gesunden Lebensstils. Regelm&auml;&szlig;ige k&ouml;rperliche Aktivit&auml;t sorgt f&uuml;r gesunde K&ouml;rperfunktionen. Au&szlig;erdem kann ein Arzt gezielte &Uuml;bungen zur St&auml;rkung des Beckenbodens empfehlen, um die Potenz zu trainieren.</p>
    <h4>Gesunder Lebensstil</h4>
    <p>Dies ist weniger eine Behandlung, als eine allgemeine Empfehlung unabh&auml;ngig von der Diagnose der eigentlichen Ursache der Impotenz. Zu einem gesunden Lebensstil geh&ouml;ren eine ausgewogene, fett- und cholesterinarme Ern&auml;hrung, sowie ein gem&auml;&szlig;igter Alkoholkonsum und kein Nikotin oder andere Drogen.</p>
  </div>

  <div class="med-article" id="andere">
    <h2>Andere Behandlungsm&ouml;glichkeiten</h2>
    <h4>Intrakavern&ouml;se Injektionstherapie</h4>
    <p>Bei dieser Methode wird das Medikament direkt in den Schwellk&ouml;rper des Penis gespritzt. Der Penis wird nach etwa 20 Minuten steif und bleibt 30&ndash;60 Minuten erekt. Diese Methode kann nur ein Urologe nach gr&uuml;ndlicher Diagnose verschreiben. Sie ist geeignet f&uuml;r Patienten, die PDE-5 Hemmer nicht vertragen oder diese nicht einnehmen k&ouml;nnen.</p>
    <h4>Penispumpe</h4>
    <p>Wer keine Medikamente einnehmen kann oder darf oder allergisch auf bestimmte Inhaltsstoffe reagiert, kann Erektionsst&ouml;rung auch mit mechanischen Mitteln entgegenwirken. Die bekannteste Methode ist die sogenannte Penispumpe. Hierbei handelt es sich um eine Vakuumpumpe, mit deren Hilfe Blut regelrecht in das m&auml;nnliche Glied gepumpt wird.</p>
    <h4>Penisring</h4>
    <p>&Auml;hnlich angewendet, wie die Vakuumpumpe, wird der Penisring. Dieser Plastikring wird zu Beginn der sexuellen Aktivit&auml;t um das m&auml;nnliche Glied gelegt. Eine Erektion kommt zustande, wenn Blut verst&auml;rkt in die Schwellk&ouml;rper des Penis einflie&szlig;t.</p>
    <h4>Operative M&ouml;glichkeiten</h4>
    <p>Wenn andere konventionelle Methoden nicht angewendet werden k&ouml;nnen oder nicht erfolgreich sind, besteht die M&ouml;glichkeit eines Implantats in den Schwellk&ouml;rper. Diese Methode ist zuverl&auml;ssig und erfordert lediglich eine einmalige Operation um eine Erektion wieder m&ouml;glich zu machen.</p>
  </div>

  <div class="med-article" id="warum-drfuxx">
    <h2>Warum dr.fuxx?</h2>
    <p>Die medizinische Plattform von dr.fuxx hilft M&auml;nnern nicht nur bei der Diagnose, sondern auch bei der Behandlung von Erektionsst&ouml;rungen. Das Gespr&auml;ch mit dem behandelnden Arzt ist schnell und unkompliziert organisiert. Der Mediziner wird Ihnen online ein in Deutschland zugelassenes Rezept ausstellen und innerhalb weniger Tage erhalten Sie Ihre Bestellung. Sie sparen sich Zeit, Stress und unn&ouml;tiges Warten auf ein Folgerezept mit dem sicheren und diskreten Service.</p>
  </div>
</div>

<!-- Medical Review -->
<div class="med-review">
  <div class="med-review-img">
    <img src="https://images.unsplash.com/photo-1612349317150-e410f624c427?auto=format&fit=crop&w=800&q=80" alt="Dr. med. Experte" loading="lazy">
  </div>
  <div class="med-review-text">
    <div class="med-review-doc">Dr. med. Experte</div>
    <div class="med-review-role">Facharzt f&uuml;r Urologie</div>
    <h3>Medizinisch-fachlich gepr&uuml;ft</h3>
    <p>Die medizinischen Inhalte auf dieser Seite wurden in Zusammenarbeit mit einem unserer &Auml;rzte bzw. medizinischen Experten erstellt und von diesen &uuml;berpr&uuml;ft. Die Informationen stammen ausschlie&szlig;lich aus zuverl&auml;ssigen, vertrauensw&uuml;rdigen und &uuml;berpr&uuml;ften Quellen, Studien, Forschungen und Expertenmeinungen.</p>
    <p>Die medizinischen Inhalte werden regelm&auml;&szlig;ig &uuml;berpr&uuml;ft, um maximale Genauigkeit und Zuverl&auml;ssigkeit zu gew&auml;hrleisten. Weitere Informationen zum redaktionellen Vorgehen finden Sie in unserem <a href="#" style="color:var(--ed-primary);">Redaktionsprozess</a>.</p>
    <div class="update">Letzte Aktualisierung am {{ date('d/m/Y') }}</div>
  </div>
</div>

<!-- Main Content -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-5">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Overview -->
                <div class="mb-5">
                    <h2 class="display-6 fw-bold mb-4">Overview</h2>
                    <p class="lead text-muted">
                        {{ $category->description ?: 'This treatment provides professional medical consultation and care tailored to your specific needs. Our licensed doctors will review your information and provide appropriate medical guidance.' }}
                    </p>
                </div>

                <!-- How It Works -->
                <div class="mb-5">
                    <h2 class="display-6 fw-bold mb-4">How it works</h2>
                    <div class="d-flex flex-column gap-3">
                        @php
                            $howItWorks = [];
                            if (isset($category->how_it_works) && is_array($category->how_it_works)) {
                                $howItWorks = $category->how_it_works;
                            } elseif (isset($category->how_it_works) && is_string($category->how_it_works)) {
                                $howItWorks = json_decode($category->how_it_works, true) ?? [];
                            }
                            
                            // Default steps if not available
                            if (empty($howItWorks)) {
                                $howItWorks = [
                                    'Complete our secure medical questionnaire about your health and medical history',
                                    'Our certified doctors review your information and assess your suitability',
                                    'If appropriate, a prescription is issued and your medication is prepared',
                                    'Your treatment is delivered discreetly to your door within 24-48 hours'
                                ];
                            }
                        @endphp
                        
                        @foreach($howItWorks as $index => $step)
                            <div class="d-flex gap-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; flex-shrink: 0;">
                                    {{ $index + 1 }}
                                </div>
                                <p class="mb-0 pt-1">{{ is_array($step) ? ($step['step'] ?? $step['description'] ?? '') : $step }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Medications -->
                @if($medicines->count() > 0)
                <div class="mb-5">
                    <h2 class="display-6 fw-bold mb-4">Available medications</h2>
                    <div class="d-flex flex-column gap-3">
                        @foreach($medicines as $medicine)
                            <div class="card bg-light border-0 p-4">
                                <h5 class="fw-semibold mb-2">{{ $medicine->name }}</h5>
                                @if($medicine->description)
                                    <p class="text-muted mb-2">{{ $medicine->description }}</p>
                                @endif
                                @if($medicine->strength)
                                    <p class="small text-muted mb-0">
                                        <span class="fw-medium">Strength:</span> {{ $medicine->strength }}
                                        @if($medicine->form)
                                            ({{ $medicine->form }})
                                        @endif
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Suitable For / Not Suitable For -->
                @php
                    $suitableFor = [];
                    $notSuitableFor = [];
                    
                    if (isset($category->suitable_for)) {
                        if (is_array($category->suitable_for)) {
                            $suitableFor = $category->suitable_for;
                        } elseif (is_string($category->suitable_for)) {
                            $suitableFor = json_decode($category->suitable_for, true) ?? [];
                        }
                    }
                    
                    if (isset($category->not_suitable_for)) {
                        if (is_array($category->not_suitable_for)) {
                            $notSuitableFor = $category->not_suitable_for;
                        } elseif (is_string($category->not_suitable_for)) {
                            $notSuitableFor = json_decode($category->not_suitable_for, true) ?? [];
                        }
                    }
                    
                    // Default values if not available
                    if (empty($suitableFor)) {
                        $suitableFor = [
                            'Adults over 18 years of age',
                            'Individuals seeking professional medical consultation',
                            'Patients looking for discreet and convenient healthcare'
                        ];
                    }
                    
                    if (empty($notSuitableFor)) {
                        $notSuitableFor = [
                            'Children under 18 years of age',
                            'Pregnant or breastfeeding women (unless specifically approved)',
                            'Individuals with severe medical conditions requiring immediate attention'
                        ];
                    }
                @endphp
                
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="card border-success border-2 bg-light-green h-100 p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <h5 class="fw-semibold mb-0">Suitable for</h5>
                            </div>
                            <ul class="list-unstyled mb-0">
                                @foreach($suitableFor as $item)
                                    <li class="d-flex gap-2 mb-2">
                                        <i class="bi bi-check-circle-fill text-success" style="flex-shrink: 0;"></i>
                                        <span class="small">{{ is_array($item) ? ($item['item'] ?? $item['text'] ?? '') : $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-danger border-2 bg-light-red h-100 p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-exclamation-circle-fill text-danger fs-5"></i>
                                <h5 class="fw-semibold mb-0">Not suitable for</h5>
                            </div>
                            <ul class="list-unstyled mb-0">
                                @foreach($notSuitableFor as $item)
                                    <li class="d-flex gap-2 mb-2">
                                        <i class="bi bi-exclamation-circle-fill text-danger" style="flex-shrink: 0;"></i>
                                        <span class="small">{{ is_array($item) ? ($item['item'] ?? $item['text'] ?? '') : $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Side Effects -->
                @php
                    $sideEffects = [];
                    if (isset($category->side_effects)) {
                        if (is_array($category->side_effects)) {
                            $sideEffects = $category->side_effects;
                        } elseif (is_string($category->side_effects)) {
                            $sideEffects = json_decode($category->side_effects, true) ?? [];
                        }
                    }
                    
                    // Default side effects if not available
                    if (empty($sideEffects)) {
                        $sideEffects = [
                            'Mild nausea',
                            'Headache',
                            'Dizziness',
                            'Fatigue'
                        ];
                    }
                @endphp
                
                <div class="mb-5">
                    <h2 class="display-6 fw-bold mb-4">Possible side effects</h2>
                    <p class="text-muted mb-3">
                        Like all medications, this treatment may cause side effects, although not everyone gets them. 
                        Most side effects are mild and temporary.
                    </p>
                    <div class="card border-warning border-2 bg-light-yellow p-4 mb-3">
                        <div class="row g-3">
                            @foreach($sideEffects as $effect)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-warning rounded-circle" style="width: 8px; height: 8px;"></div>
                                        <span>{{ is_array($effect) ? ($effect['effect'] ?? $effect['name'] ?? '') : $effect }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <p class="small text-muted">
                        If you experience severe side effects, stop taking the medication and contact a doctor immediately.
                    </p>
                </div>

                <!-- FAQs -->
                @php
                    $faqs = [];
                    if (isset($category->faqs)) {
                        if (is_array($category->faqs)) {
                            $faqs = $category->faqs;
                        } elseif (is_string($category->faqs)) {
                            $faqs = json_decode($category->faqs, true) ?? [];
                        }
                    }
                    
                    // Default FAQs if not available
                    if (empty($faqs)) {
                        $faqs = [
                            [
                                'question' => 'How long does the consultation process take?',
                                'answer' => 'The entire process typically takes 24-48 hours from questionnaire submission to prescription approval and shipping.'
                            ],
                            [
                                'question' => 'Is this treatment suitable for me?',
                                'answer' => 'Our doctors will review your questionnaire and medical history to determine if this treatment is appropriate for your specific situation.'
                            ],
                            [
                                'question' => 'What if I have questions about my medication?',
                                'answer' => 'You can contact our medical team at any time with questions about your treatment. We provide ongoing support throughout your treatment period.'
                            ]
                        ];
                    }
                @endphp
                
                <div class="mb-5">
                    <h2 class="display-6 fw-bold mb-4">Frequently asked questions</h2>
                    <div class="accordion" id="faqAccordion">
                        @foreach($faqs as $index => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">
                                        {{ is_array($faq) ? ($faq['question'] ?? '') : (isset($faq->question) ? $faq->question : '') }}
                                    </button>
                                </h2>
                                <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        {{ is_array($faq) ? ($faq['answer'] ?? '') : (isset($faq->answer) ? $faq->answer : '') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column - Sticky CTA -->
            <div class="col-lg-4">
                <div class="sticky-cta">
                    <div class="card border-2 shadow p-4">
                        <h5 class="fw-bold mb-3">Ready to get started?</h5>
                        <p class="text-muted mb-4">
                            Complete a quick medical questionnaire and get your prescription today.
                        </p>
                        
                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light-green rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-check text-success"></i>
                                </div>
                                <span class="small">Free consultation with doctor</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light-green rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-check text-success"></i>
                                </div>
                                <span class="small">Delivered in 24-48 hours</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light-green rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-check text-success"></i>
                                </div>
                                <span class="small">Cancel anytime</span>
                            </div>
                        </div>

                        <div class="card bg-light border-0 p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Starting from:</span>
                                <span class="h4 text-primary fw-bold mb-0">
                                    @if(isset($category->price) && $category->price)
                                        €{{ number_format($category->price, 2) }}
                                    @else
                                        Price not available
                                    @endif
                                </span>
                            </div>
                            <p class="small text-muted mb-0">Free shipping • Discreet packaging</p>
                        </div>

                        @if($hasQuestionnaire)
                            <a href="{{ auth()->check() ? url('/questionnaire/category/' . $category->id) : url('/patient-login?redirect_to=' . urlencode('/questionnaire/category/' . $category->id)) }}" 
                               class="btn btn-primary btn-lg w-100 mb-2">
                                Start consultation
                            </a>
                        @else
                            <a href="{{ route('categories') }}" class="btn btn-primary btn-lg w-100 mb-2">
                                Browse treatments
                            </a>
                        @endif
                        <p class="small text-center text-muted mb-0">
                            Takes 5 minutes • 100% confidential
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bottom CTA -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold mb-3">Still have questions?</h2>
                <p class="lead text-muted mb-4">
                    Our medical team is here to help. Contact us for personalized advice.
                </p>
                <a href="#" class="btn btn-outline-primary btn-lg border-2">Contact our team</a>
            </div>
        </div>
    </div>
</section>

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
