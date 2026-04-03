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
        
        @media (max-width: 768px) {
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

<!-- Hero Section -->
<!-- Hero Section -->
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
            <a href="{{ auth()->check() ? url('/questionnaire/category/' . $category->id) : url('/patient-login?redirect_to=' . urlencode('/questionnaire/category/' . $category->id)) }}" class="hero-cta">
                Zu den medizinischen Fragen
            </a>
        @else
            <a href="{{ route('categories') }}" class="hero-cta">
                Browse treatments
            </a>
        @endif
        
        <div class="hero-pricing">
            Behandlungsgebühr 29 &euro; +<br>
            Medikament ab 
            @if(isset($category->price) && $category->price)
                {{ number_format($category->price, 2) }} &euro;
            @else
                41,58 &euro;
            @endif
        </div>
        <div class="hero-rating">
          <span class="stars">★★★★★</span>
          <strong>4,79</strong> Hervorragend
          <span style="color:var(--ed-text-muted)">14.082 Bewertungen</span>
        </div>
      </div>
    </div>
    <div class="ed-hero-badge">
      <div class="badge-big">85<span>%</span></div>
      <div class="badge-sub">der Männer<br>berichten von<br>einer Besserung</div>
    </div>
</section>

<!-- Features Bar -->
<section class="features-bar">
    <div class="features-bar-inner">
        <div class="fb-item">
            <div class="fb-icon">
                <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            </div>
            <div class="fb-text">
                <strong>Das Rezept wird online ausgestellt.</strong>
                <span>Ein Klinikbesuch ist nicht erforderlich.</span>
            </div>
        </div>
        <div class="fb-item">
            <div class="fb-icon">
                <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div class="fb-text">
                <strong>Lieferung innerhalb von 1&ndash;2 Werktagen.</strong>
                <span>Schnelle, zuverl&auml;ssige Lieferung.</span>
            </div>
        </div>
        <div class="fb-item">
            <div class="fb-icon">
                <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
            </div>
            <div class="fb-text">
                <strong>Originalmedizin und Generika.</strong>
                <span>Aus zertifizierten Apotheken.</span>
            </div>
        </div>
        <div class="fb-item">
            <div class="fb-icon">
                <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <div class="fb-text">
                <strong>Beratung &uuml;ber Online-Fragebogen.</strong>
                <span>Schnelle medizinische Beratung</span>
            </div>
        </div>
    </div>
</section>

<!-- 3 Steps -->
<section class="steps-section">
    <h2 class="steps-title">3 einfache Schritte<br><span>100 % online</span></h2>
    <div class="steps-grid">
      <div class="step-card">
        <div class="step-num">1</div>
        <div class="step-card-inner">
            <h3>Füllen Sie den <span>medizinischen Fragebogen aus</span></h3>
            <p>Starten Sie die Online-Konsultation und beantworten Sie die medizinischen Fragen.</p>
            <img src="https://images.unsplash.com/photo-1512428559087-560fa5ceab42?auto=format&fit=crop&w=400&q=80" alt="Fragebogen" loading="lazy">
        </div>
      </div>
      <div class="step-card">
        <div class="step-num">2</div>
        <div class="step-card-inner">
            <h3>Wählen Sie die <span>gewünschte Behandlung</span></h3>
            <p>Der behandelnde Arzt prüft Ihre Angaben und stellt Ihnen bei Bedarf ein Rezept aus.</p>
            <img src="https://images.unsplash.com/photo-1612349317150-e410f624c427?auto=format&fit=crop&w=400&q=80" alt="Arzt" loading="lazy">
        </div>
      </div>
      <div class="step-card">
        <div class="step-num">3</div>
        <div class="step-card-inner">
            <h3>Lieferung in <span>1&ndash;2 Werktagen</span></h3>
            <p>Sie erhalten Ihre Medikamente diskret und sicher.</p>
            <img src="https://images.unsplash.com/photo-1580674285054-bed31e145f59?auto=format&fit=crop&w=400&q=80" alt="Lieferung" loading="lazy">
        </div>
      </div>
    </div>
</section>

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
