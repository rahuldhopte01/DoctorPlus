<!DOCTYPE html>
<html lang="en">
<head>
    @php
    $setting = App\Models\Setting::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->business_name }} - {{ __('landing.meta.online_medical_consultation') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts: DM Serif Display -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{asset('css/new-design.css')}}?v={{ time() }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/website_header.css') }}">
    <link href="{{asset('css/landing_styles.css')}}?v={{ time() }}" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bold Differentiation typography -->
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6, .display-4, .display-5 { font-family: 'Clash Display', sans-serif; }
        h1 span, h2 span, h3 span, h4 span, h5 span, h6 span, .display-4 span, .display-5 span { font-family: inherit; }
    </style>
</head>
<body>
    @include('layout.partials.skeleton_loader')
<!-- Navigation -->
@include('layout.partials.navbar_website')

<!-- Hero Section -->
@php
    $homeSettings = json_decode($setting->website_home_settings, true) ?: [];
    $hero = $homeSettings['hero'] ?? [];
@endphp

@php
    $heroBgColor = $hero['bg_color'] ?? '#f3ecff';
@endphp
<style>
    /* Hero Background & Blend */
    .hero-fuxx {
        background: linear-gradient(135deg, {{ $heroBgColor }} 0%, #ffffff 100%) !important;
    }
    .hero-bg-wrapper {
        opacity: 0.6;
        mix-blend-mode: multiply;
    }
    
    /* Premium Button Interactive Styles */
    .btn-hero-premium {
        background-color: #8a48ff !important;
        border-color: #8a48ff !important;
        color: #ffffff !important;
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
        position: relative;
        overflow: hidden;
    }
    
    .btn-hero-premium:hover {
        transform: translateY(-4px) !important;
        background-color: #7a35fa !important;
        border-color: #7a35fa !important;
    }

    /* Quick Link Cards Interactive Styles */
    .quick-link-card .card {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        cursor: pointer;
    }
    
    .quick-link-card:hover .card {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important;
    }
    
    /* Trust Icons */
    .trust-icon-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 6px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        font-weight: 700;
        font-size: 0.75rem;
        background: #fff;
    }
</style>

<section class="hero-fuxx position-relative overflow-hidden" style="min-height: 80vh; padding-top: 50px; padding-bottom: 80px;">
    
    <!-- Center Foreground Product Image -->
    @if(!empty($hero['image']))
        <div class="hero-bg-wrapper position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1; pointer-events: none; width: 100%; max-width: 800px;">
            <img src="{{ url('images/upload/'.$hero['image']) }}" alt="Background" class="img-fluid w-100" style="mask-image: linear-gradient(to top, transparent 0%, black 50%); -webkit-mask-image: linear-gradient(to top, transparent 0%, black 50%);">
        </div>
    @endif

    <!-- Small Decorative Hero Image (like the plant/product on drfuxx) centered with overlay -->
    @if(!empty($hero['bg_image']))
        <!-- Image sits at z-index:1 (lowest) -->
        <div class="position-absolute" style="top:50%;left:50%;transform:translate(-50%,-50%);z-index:1;pointer-events:none;max-width:380px;width:100%;">
            <img src="{{ url('images/upload/'.$hero['bg_image']) }}" alt="" class="img-fluid" style="mask-image: linear-gradient(to top, transparent 0%, black 40%); -webkit-mask-image: linear-gradient(to top, transparent 0%, black 40%); opacity: 0.9;">
        </div>
        <!-- Color overlay #f3ecfe sits at z-index:2, above image -->
        <div class="position-absolute" style="top:0;left:0;right:0;bottom:0;background-color:#f3ecfe;opacity:0.78;z-index:2;pointer-events:none;"></div>
    @endif

    <div class="container position-relative" style="z-index: 3;">
        
        <!-- Top Quick Link Cards -->
        @if(!empty($hero['quick_links']) && count($hero['quick_links']) > 0)
        <style>
            .hero-quick-links-container {
                max-width: 1100px;
                margin: 0 auto;
            }
            .quick-link-card {
                flex: 1 1 300px;
                max-width: 350px;
                perspective: 1000px;
            }
            .quick-link-card .card {
                border-radius: 20px !important;
                background-color: #ffffff;
                border: none !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
                min-height: 140px;
                padding: 24px;
                overflow: hidden;
                position: relative;
                transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
            }
            .quick-link-card:hover .card {
                transform: translateY(-5px);
                box-shadow: 0 15px 35px rgba(0,0,0,0.12) !important;
            }
            
            /* Dark Tint Overlay */
            .quick-link-card .card-overlay {
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background-color: rgba(60, 60, 60, 0.85); /* Dark grey filter */
                opacity: 0;
                transition: opacity 0.4s ease;
                z-index: 3;
            }
            .quick-link-card:hover .card-overlay {
                opacity: 1;
            }
            
            /* Typography Wrapper - needs high z-index to stay above overlay */
            .quick-link-card .qlink-title-wrapper {
                position: relative;
                z-index: 10;
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            /* Title */
            .quick-link-card .qlink-title {
                font-size: 1.15rem;
                font-weight: 800;
                color: #111;
                margin-bottom: 4px;
                letter-spacing: -0.3px;
                transition: color 0.4s ease;
                text-transform: uppercase;
            }
            .quick-link-card:hover .qlink-title {
                color: #8a48ff !important;
            }

            /* Subtitle Reveal */
            .quick-link-card .qlink-subtitle {
                font-size: 0.85rem;
                color: #ffffff;
                font-weight: 500;
                opacity: 0;
                max-height: 0;
                transform: translateY(10px);
                transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
                margin: 0;
            }
            .quick-link-card:hover .qlink-subtitle {
                opacity: 0.9;
                max-height: 100px;
                transform: translateY(0);
                margin-top: 6px;
            }

            /* Image setup */
            .quick-link-card .qlink-img {
                position: absolute;
                bottom: 15px;
                left: 50%;
                transform: translateX(-50%) scale(1);
                height: 55px; /* Fixed height for clean proportion scaling */
                width: auto;
                object-fit: contain;
                transition: transform 0.6s cubic-bezier(0.2, 0.8, 0.2, 1), opacity 0.4s ease;
                z-index: 2; /* Below the overlay */
            }
            
            /* Hover Image Expansion */
            .quick-link-card:hover .qlink-img {
                transform: translateX(-50%) scale(8); /* Massive scale to fill background */
                opacity: 0.3; /* Fade into the background under the overlay */
            }

            /* Badge */
            .qlink-badge {
                background-color: #f79d00;
                color: #fff;
                border-radius: 4px;
                font-size: 0.65rem;
                padding: 3px 6px;
                font-weight: 700;
                vertical-align: middle;
            }
        </style>
        <div class="hero-quick-links-container d-flex flex-wrap justify-content-center gap-4 mb-5 px-3">
            @foreach($hero['quick_links'] as $qlink)
            <a href="{{ $qlink['url'] ?? '#' }}" class="quick-link-card text-decoration-none">
                <div class="card position-relative">
                    <div class="card-overlay"></div>
                    <div class="qlink-title-wrapper">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="qlink-title mb-0">{{ $qlink['title'] ?? '' }}</h5>
                            @if(!empty($qlink['badge']))
                                <span class="qlink-badge">{{ $qlink['badge'] }}</span>
                            @endif
                        </div>
                        @if(!empty($qlink['subtitle']))
                            <p class="qlink-subtitle">{{ $qlink['subtitle'] }}</p>
                        @endif
                    </div>
                    
                    @if(!empty($qlink['image']))
                        <img src="{{ url('images/upload/'.$qlink['image']) }}" class="qlink-img" alt="">
                    @elseif(!empty($qlink['icon_class']))
                        <i class="{{ $qlink['icon_class'] }} qlink-img" style="font-size: 2.5rem; color: #7b42f6; bottom: 5px;"></i>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        @endif

        <!-- Main Content -->
        <div class="hero-main-content text-center mx-auto" style="max-width: 800px; margin-top: 60px;">
            <style>
                .hero-ticker-badge {
                    display: inline-block;
                    position: relative;
                }
                .hero-ticker-badge::after {
                    content: '|';
                    color: #7b42f6;
                    animation: blink 1s step-end infinite;
                }
                @keyframes blink {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0; }
                }
            </style>

            <div class="text-uppercase fw-bold mb-3 d-inline-block px-3 py-1" style="color: #8a48ff; background-color: #f4effe; border-radius: 20px; letter-spacing: 1.5px; font-size: 0.85rem;">
                <span class="hero-ticker-badge" id="heroTicker"></span>
            </div>

            <h1 class="display-4 fw-bold mb-4" style="color: #1a1a1a; letter-spacing: -1px;">
                {{ $hero['title'] ?? 'Ganz einfach mit dr.fuxx' }}
            </h1>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    @php
                        $typingKeywordsStr = $hero['typing_keywords'] ?? 'MED. CANNABIS, EREKTIONSSTÖRUNGEN, TESTOSTERON, HAARAUSFALL, ÜBERGEWICHT';
                        $typingKeywordsArray = array_map('trim', explode(',', $typingKeywordsStr));
                        $typingKeywordsArray = array_filter($typingKeywordsArray);
                        if(empty($typingKeywordsArray)) {
                            $typingKeywordsArray = ['MED. CANNABIS'];
                        }
                    @endphp
                    const keywords = {!! json_encode(array_values($typingKeywordsArray)) !!};
                    const tickerEl = document.getElementById('heroTicker');
                    let keywordIndex = 0;
                    let charIndex = 0;
                    let isDeleting = false;
                    let typingSpeed = 100;

                    function typeEffect() {
                        const currentKeyword = keywords[keywordIndex];
                        
                        if (isDeleting) {
                            tickerEl.textContent = currentKeyword.substring(0, charIndex - 1);
                            charIndex--;
                            typingSpeed = 50; // Faster when deleting
                        } else {
                            tickerEl.textContent = currentKeyword.substring(0, charIndex + 1);
                            charIndex++;
                            typingSpeed = 120; // Normal typing speed
                        }

                        if (!isDeleting && charIndex === currentKeyword.length) {
                            typingSpeed = 2000; // Pause at the end of word
                            isDeleting = true;
                        } else if (isDeleting && charIndex === 0) {
                            isDeleting = false;
                            keywordIndex = (keywordIndex + 1) % keywords.length;
                            typingSpeed = 400; // Pause before typing next word
                        }

                        setTimeout(typeEffect, typingSpeed);
                    }

                    if(tickerEl) {
                        setTimeout(typeEffect, 500);
                    }
                });
            </script>

            <p class="lead mb-5 mx-auto" style="color: #4a4a4a; max-width: 650px; font-size: 1.15rem; line-height: 1.7;">
                {{ $hero['description'] ?? 'Original deutsche Medikamente, Online-Rezepte und medizinische Produkte – Lieferung in 24-48 Stunden oder per Express in 2 Stunden / Selbstabholung möglich.' }}
            </p>

            @if(!empty($hero['btn_text']))
                <style>
                    .btn-cta-pulse {
                        animation: ctaPulse 2.5s ease-in-out infinite;
                        position: relative;
                        border-radius: 50px !important;
                    }
                    @keyframes ctaPulse {
                        0%, 100% { box-shadow: 0 0 0 0 rgba(124, 58, 237, 0.4); }
                        50%       { box-shadow: 0 0 0 12px rgba(124, 58, 237, 0); }
                    }
                </style>
                <a href="{{ $hero['btn_url'] ?? '#' }}" class="btn btn-hero-premium btn-cta-pulse rounded-pill px-5 py-3 fs-5 fw-bold mb-5">
                    {{ $hero['btn_text'] }}
                </a>
            @endif

            <!-- Trust Items -->
            @if(!empty($hero['trust_items']) && count($hero['trust_items']) > 0)
            <div class="d-flex flex-wrap justify-content-center gap-4 mb-4">
                @foreach($hero['trust_items'] as $trust)
                    <div class="d-flex align-items-center text-dark" style="font-size: 0.95rem;">
                        @if(strpos($trust['icon_class'], 'bi-') !== false || strpos($trust['icon_class'], 'fa-') !== false)
                            <i class="{{ $trust['icon_class'] }} fs-5 me-2" style="color: #7b42f6;"></i>
                        @elseif(!empty($trust['icon_class']))
                            <span class="trust-icon-badge me-2">{{ $trust['icon_class'] }}</span>
                        @endif
                        <span class="fw-medium text-secondary">{{ $trust['text'] }}</span>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Ratings -->
            <div class="rating-section mb-3 d-flex flex-wrap align-items-center justify-content-center gap-2 mt-4">
                <div class="stars text-warning d-flex fs-5">
                    @php 
                        $stars = floatval($hero['rating_stars'] ?? 5);
                        $fullStars = floor($stars);
                        $halfStar = $stars - $fullStars >= 0.5;
                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                    @endphp
                    @for($i=0; $i<$fullStars; $i++) <i class="bi bi-star-fill mx-1"></i> @endfor
                    @if($halfStar) <i class="bi bi-star-half mx-1"></i> @endif
                    @for($i=0; $i<$emptyStars; $i++) <i class="bi bi-star mx-1"></i> @endfor
                </div>
                <div class="fw-bold fs-6">{{ $hero['rating_score'] ?? '4,79' }}</div>
                <div class="text-muted small">{{ $hero['rating_text'] ?? 'Hervorragend aus 13.764 Bewertungen' }}</div>
            </div>

            <!-- Live Viewers -->
            @if(!empty($hero['live_viewers']))
            <div class="live-viewers d-flex align-items-center justify-content-center text-muted small mt-3">
                <div class="spinner-grow spinner-grow-sm me-2" role="status" style="width: 8px; height: 8px; background-color: #7b42f6;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="fw-bold text-dark me-1" id="live-viewer-count">{{ rand(100, 250) }}</span> {{ $hero['live_viewers'] }}
            </div>
            <script>
                // Make the viewer count dynamic
                document.addEventListener("DOMContentLoaded", function() {
                    let countElement = document.getElementById('live-viewer-count');
                    if (countElement) {
                        setInterval(() => {
                            let current = parseInt(countElement.innerText);
                            let change = Math.floor(Math.random() * 5) - 2; // -2 to +2
                            let newCount = current + change;
                            if(newCount < 50) newCount = 50; 
                            countElement.innerText = newCount;
                        }, 5000);
                    }
                });
            </script>
            @endif
        </div>
    </div>
</section>

@php
    $how = $homeSettings['how_it_works'] ?? [];
    // Provide default steps so section shows even if admin not yet configured
    if (empty($how['steps'])) {
        $how['title']    = $how['title']    ?? '3 einfache Schritte';
        $how['subtitle'] = $how['subtitle'] ?? '100 % online';
        $how['badge']    = $how['badge']    ?? '5 Ärzte online | täglich 8–18 Uhr';
        $how['steps'] = [
            ['title' => 'Füll den|medizinischen Fragebogen aus', 'text' => '', 'icon' => ''],
            ['title' => 'Wähle die|gewünschte Behandlung',        'text' => '', 'icon' => ''],
            ['title' => 'Lieferung|flexibel wählen',
             'text' => ">Online Express: Lieferung in 1–2 Werktagen\n>Apotheke vor Ort: Selbstabholung möglich",
             'icon' => ''],
        ];
    }
@endphp

<!-- How It Works Section -->
<section class="py-5 position-relative overflow-hidden" style="background: linear-gradient(175deg, #ddd6ff 0%, #e9e4ff 40%, #f3f0ff 100%); min-height: 560px;">

    <!-- Wavy background line SVG -->
    <svg class="position-absolute w-100" style="bottom: 60px; left: 0; opacity: 0.18; pointer-events:none;" viewBox="0 0 1440 120" preserveAspectRatio="none">
        <path d="M0,60 C180,20 360,100 540,60 C720,20 900,100 1080,60 C1260,20 1380,80 1440,60" stroke="#7b42f6" stroke-width="3" fill="none"/>
    </svg>

    <style>
        .hiw-title  { font-size: 2.3rem; font-weight: 800; color: #111; letter-spacing: -0.5px; font-family: 'Clash Display', sans-serif; }
        .hiw-sub    { font-size: 2.1rem; font-weight: 800; color: #7b42f6; letter-spacing: -0.5px; font-style: italic; font-family: 'Clash Display', sans-serif; }
        .hiw-badge  { display: inline-flex; align-items: center; gap: 8px; background: #fff; border-radius: 50px; padding: 6px 16px; font-size: 0.82rem; font-weight: 600; color: #333; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
        .hiw-dot    { width: 9px; height: 9px; background: #22c55e; border-radius: 50%; flex-shrink: 0; animation: hiwDotPulse 2s ease-in-out infinite; }
        @keyframes hiwDotPulse { 0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,0.5)} 50%{box-shadow:0 0 0 6px rgba(34,197,94,0)} }

        /* Deck layout — exact reference values */
        .hiw-deck   { display: flex; justify-content: center; align-items: flex-end; gap: 0; padding: 40px 0 20px; }
        .step-card-tilted {
            background: #faf8ff;
            border-radius: 20px;
            padding: 28px 24px 20px;
            text-align: left;
            position: relative;
            overflow: hidden;
            width: 280px;
            flex-shrink: 0;
            min-height: 360px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease, z-index 0s;
        }
        .step-card-tilted:hover {
            box-shadow: 0 16px 45px rgba(80,40,180,0.22);
        }
        /* Exact transforms from reference */
        .tilt-left   { transform: rotate(-6deg) translateY(10px); z-index: 1; margin-right: -30px; }
        .tilt-center { transform: rotate(0deg) translateY(-20px); z-index: 2; }
        .tilt-right  { transform: rotate(6deg) translateY(10px);  z-index: 1; margin-left: -30px; }
        
        /* JS adds this class to raise the hovered card */
        .step-card-tilted.hiw-active { z-index: 10 !important; }
        .tilt-left.hiw-active   { transform: rotate(-4deg) translateY(0px) scale(1.02); }
        .tilt-center.hiw-active { transform: rotate(0deg)  translateY(-30px) scale(1.02); }
        .tilt-right.hiw-active  { transform: rotate(4deg)  translateY(0px) scale(1.02); }

        .step-num-tilted { width: 38px; height: 38px; background: #7b42f6; color: #fff; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1rem; margin-bottom: 16px; }
        .step-card-tilted h3 { font-size: 1.2rem; font-weight: 600; color: #1a1a1a; line-height: 1.35; margin-bottom: 6px; }
        .step-card-tilted h3 span { color: #7b42f6; display: block; }
        .step-card-tilted > p { color: #666; font-size: 0.82rem; line-height: 1.5; margin-bottom: 16px; }
        .hiw-sub-items { margin-top: 12px; }
        .hiw-sub-item  { display: flex; align-items: flex-start; gap: 10px; padding: 9px 10px;
            border: 1px solid #ebe8f8; border-radius: 12px; margin-bottom: 8px; background: #fff; }
        .hiw-sub-item i { color: #7b42f6; font-size: 1rem; margin-top: 2px; flex-shrink: 0; }
        .hiw-sub-item-label { font-weight: 700; font-size: 0.82rem; color: #111; }
        .hiw-sub-item-desc  { font-size: 0.74rem; color: #777; }
        .hiw-card-photo { display: block; width: 100%; margin-top: 14px; object-fit: contain; max-height: 190px; }

        /* Mobile: stack cards vertically instead of overflowing */
        @media (max-width: 767px) {
            .hiw-deck { flex-direction: column !important; align-items: center !important; gap: 16px !important; padding: 24px 16px 20px !important; }
            .step-card-tilted { transform: none !important; margin: 0 !important; width: 100% !important; max-width: 360px !important; min-height: auto !important; }
            .step-card-tilted.hiw-active { transform: none !important; }
        }
    </style>

    <div class="container text-center position-relative" style="z-index:2;">
        <p class="hiw-title mb-1">{{ $how['title'] ?? '3 einfache Schritte' }}</p>
        <p class="hiw-sub mb-3">{{ $how['subtitle'] ?? '100 % online' }}</p>

        @if(!empty($how['badge']))
        <div class="hiw-badge mb-5 mx-auto" style="width:fit-content;">
            <span class="hiw-dot"></span>{{ $how['badge'] }}
        </div>
        @endif

        <div class="hiw-deck mx-auto" style="max-width: 950px;">
            @foreach($how['steps'] as $i => $step)
            @php
                $tiltClass = ['tilt-left','tilt-center','tilt-right'][$i] ?? 'tilt-center';
                // Support "Normal text|Purple text" split in the title field
                $titleParts = explode('|', $step['title'] ?? '', 2);
                $titleNormal = trim($titleParts[0]);
                $titlePurple = isset($titleParts[1]) ? trim($titleParts[1]) : '';
                // Parse sub-items from 'text' if they contain lines starting with ">"
                $subItems = [];
                $plainText = '';
                foreach(explode("\n", $step['text'] ?? '') as $line) {
                    $line = trim($line);
                    if(str_starts_with($line, '>')) {
                        $parts = explode(':', ltrim($line, '>'), 2);
                        $subItems[] = ['label' => trim($parts[0]), 'desc' => isset($parts[1]) ? trim($parts[1]) : ''];
                    } elseif($line) {
                        $plainText .= $line . ' ';
                    }
                }
            @endphp
            <div class="step-card-tilted {{ $tiltClass }}">
                <div class="step-num-tilted">{{ $i + 1 }}</div>
                <h3>{{ $titleNormal }}@if($titlePurple)<span>{{ $titlePurple }}</span>@endif
                </h3>
                @if($subItems)
                <div class="hiw-sub-items">
                    @foreach($subItems as $sub)
                    <div class="hiw-sub-item">
                        <i class="bi bi-check2-circle"></i>
                        <div>
                            <div class="hiw-sub-item-label">{{ $sub['label'] }}</div>
                            @if($sub['desc'])<div class="hiw-sub-item-desc">{{ $sub['desc'] }}</div>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @elseif(trim($plainText))
                    <p class="mt-2" style="font-size:0.83rem;color:#666;">{{ trim($plainText) }}</p>
                @endif
                @if(!empty($step['icon']))
                    <img src="{{ url('images/upload/'.$step['icon']) }}" class="hiw-card-photo" alt="">
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <script>
    document.querySelectorAll('.step-card-tilted').forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            document.querySelectorAll('.step-card-tilted').forEach(function(c) { c.classList.remove('hiw-active'); });
            card.classList.add('hiw-active');
        });
        card.addEventListener('mouseleave', function() {
            card.classList.remove('hiw-active');
        });
    });
    </script>
</section>

@php
    $relief = $homeSettings['natural_relief'] ?? [];
    if (empty($relief['cards'])) {
        $relief['title'] = $relief['title'] ?? 'Finden Sie natürliche und|sichere Linderung';
        $relief['badge'] = $relief['badge'] ?? 'REZEPT WIRD ONLINE AUSGESTELLT';
        $relief['btn1_text'] = $relief['btn1_text'] ?? 'Berechtigung prüfen';
        $relief['btn1_url'] = $relief['btn1_url'] ?? '#';
        $relief['btn2_text'] = $relief['btn2_text'] ?? 'Gratis Beratung starten';
        $relief['btn2_url'] = $relief['btn2_url'] ?? '#';
        $relief['cards'] = [
            [
                'title' => 'Wenn Schmerzen nicht aufhören...',
                'btn_text' => 'Mehr erfahren',
                'btn_url' => '#',
                'icon' => '' 
            ],
            [
                'title' => 'Sind Sie es leid, alles andere auszuprobieren?',
                'btn_text' => 'Behandlungen entdecken',
                'btn_url' => '#',
                'icon' => ''
            ]
        ];
    }
@endphp

<!-- Natural Relief Section -->
<section class="cannabis-banner-section">
    <div class="cbs-inner">
        @if(!empty($relief['badge']))
            <span class="cbs-pill">{{ $relief['badge'] }}</span>
        @endif

        @php
            $rTitleParts = explode('|', $relief['title'] ?? '', 2);
            $rTitleNormal = trim($rTitleParts[0]);
            $rTitleGreen = isset($rTitleParts[1]) ? trim($rTitleParts[1]) : '';
        @endphp
        
        <h2 class="cbs-heading">{{ $rTitleNormal }} @if($rTitleGreen)<span class="cbs-green">{{ $rTitleGreen }}</span>@endif</h2>

        <!-- Center hero image with buttons overlaid -->
        <div class="cbs-hero-img-wrap">
            <img src="{{ !empty($relief['image']) ? url('images/upload/'.$relief['image']) : 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=800&q=80' }}" alt="" class="cbs-hero-img">

            <div class="cbs-btns-overlay">
                @if(!empty($relief['btn1_text']))
                    <a href="{{ $relief['btn1_url'] ?? '#' }}" class="cbs-btn cbs-btn-outline">{{ $relief['btn1_text'] }}</a>
                @endif
                @if(!empty($relief['btn2_text']))
                    <a href="{{ $relief['btn2_url'] ?? '#' }}" class="cbs-btn cbs-btn-filled">{{ $relief['btn2_text'] }}</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Two info cards -->
    <div class="cbs-cards">
        @foreach($relief['cards'] as $card)
        @if(!empty($card['title']))
        <div class="cbs-card">
            <div class="cbs-card-text" @if(empty($card['icon'])) style="width:100%;padding-right:0;" @endif>
                <h3>{{ $card['title'] }}</h3>
                @if(!empty($card['btn_text']))
                    <a href="{{ $card['btn_url'] ?? '#' }}" class="cbs-card-btn">{{ $card['btn_text'] }}</a>
                @endif
            </div>
            @if(!empty($card['icon']))
            <div class="cbs-card-img-wrap">
                <img src="{{ url('images/upload/'.$card['icon']) }}" alt="" class="cbs-card-img" />
            </div>
            @endif
        </div>
        @endif
        @endforeach
    </div>
</section>

<!-- ED Banner Section -->
@php
    $ed = $homeSettings['ed_banner'] ?? [];
    $edPill = $ed['pill'] ?? 'LÖSUNG FÜR EREKTILE DYSFUNKTION';
    
    // Process title: wrap text after | in a span with .ed-blue
    $rawTitle = $ed['title'] ?? 'Gewinnen Sie Ihr | Selbstvertrauen | und Ihre Intimität zurück';
    $parts = explode('|', $rawTitle);
    if (count($parts) > 1) {
        $processedTitle = $parts[0];
        for ($i = 1; $i < count($parts); $i++) {
            if ($i % 2 != 0) {
                $processedTitle .= '<span class="ed-blue">' . $parts[$i] . '</span>';
            } else {
                $processedTitle .= $parts[$i];
            }
        }
    } else {
        $processedTitle = $rawTitle;
    }
    $edHeading = $processedTitle;

    $edHeroImage = !empty($ed['hero_image']) ? url('images/upload/'.$ed['hero_image']) : 'https://images.unsplash.com/photo-1511130558040-bb3396b42b79?q=80&w=1000&auto=format&fit=crop';
    
    $edBtn1Text = !empty($ed['btn1_text']) ? $ed['btn1_text'] : 'Meine Behandlung finden';
    $edBtn1Url = !empty($ed['btn1_url']) ? $ed['btn1_url'] : route('categories');
    $edBtn2Text = !empty($ed['btn2_text']) ? $ed['btn2_text'] : 'Meine kostenlose Beratung starten';
    $edBtn2Url = !empty($ed['btn2_url']) ? $ed['btn2_url'] : route('categories');

    $largeCard = $ed['large_card'] ?? [];
    $largeTitle = !empty($largeCard['title']) ? str_replace('|', '<br>', $largeCard['title']) : 'Es kommt häufiger vor, als Sie denken.';
    $largeBtnText = !empty($largeCard['btn_text']) ? $largeCard['btn_text'] : 'Mehr über Ursachen erfahren';
    $largeBtnUrl = !empty($largeCard['btn_url']) ? $largeCard['btn_url'] : route('categories');
    $largeImage = !empty($largeCard['image']) ? url('images/upload/'.$largeCard['image']) : 'https://images.unsplash.com/photo-1621348123733-47a824707db9?q=80&w=1000&auto=format&fit=crop';

    $r1 = $ed['right_card_1'] ?? [];
    $r1Title = !empty($r1['title']) ? str_replace('|', '<br>', $r1['title']) : 'Wenn Leistung zu Druck wird';
    $r1BtnText = !empty($r1['btn_text']) ? $r1['btn_text'] : 'Verstehen, wie ED funktioniert';
    $r1BtnUrl = !empty($r1['btn_url']) ? $r1['btn_url'] : route('categories');
    $r1Image = !empty($r1['image']) ? url('images/upload/'.$r1['image']) : 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?q=80&w=1000&auto=format&fit=crop';

    $r2 = $ed['right_card_2'] ?? [];
    $r2Title = !empty($r2['title']) ? str_replace('|', '<br>', $r2['title']) : 'Professionelle Hilfe, die diskret wirkt.';
    $r2BtnText = !empty($r2['btn_text']) ? $r2['btn_text'] : 'Mein Rezept erhalten';
    $r2BtnUrl = !empty($r2['btn_url']) ? $r2['btn_url'] : route('categories');
    $r2Image = !empty($r2['image']) ? url('images/upload/'.$r2['image']) : 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?q=80&w=1000&auto=format&fit=crop';
@endphp
<section class="ed-banner-section">
    <div class="ed-inner">
        <span class="ed-pill">{{ $edPill }}</span>
        <h2 class="ed-heading">{!! $edHeading !!}</h2>
        <div class="ed-hero-img-wrap">
            <img src="{{ $edHeroImage }}" alt="Paar" class="ed-hero-img" />
            <div class="ed-btns-overlay">
                <a href="{{ $edBtn1Url }}" class="ed-btn ed-btn-outline">{{ $edBtn1Text }}</a>
                <a href="{{ $edBtn2Url }}" class="ed-btn ed-btn-filled">{{ $edBtn2Text }}</a>
            </div>
        </div>
    </div>
    <div class="ed-cards">
        <div class="ed-card ed-card-large">
            <div class="ed-card-text">
                <h3>{!! $largeTitle !!}</h3>
                <a href="{{ $largeBtnUrl }}" class="ed-card-btn">{{ $largeBtnText }}</a>
            </div>
            <div class="ed-card-img-wrap">
                <img src="{{ $largeImage }}" alt="" class="ed-card-img" />
            </div>
        </div>
        <div class="ed-cards-right">
            <div class="ed-card ed-card-small">
                <div class="ed-card-text">
                    <h3>{!! $r1Title !!}</h3>
                    <a href="{{ $r1BtnUrl }}" class="ed-card-btn">{{ $r1BtnText }}</a>
                </div>
                <div class="ed-card-img-wrap">
                    <img src="{{ $r1Image }}" alt="" class="ed-card-img" />
                </div>
            </div>
            <div class="ed-card ed-card-small">
                <div class="ed-card-text">
                    <h3>{!! $r2Title !!}</h3>
                    <a href="{{ $r2BtnUrl }}" class="ed-card-btn">{{ $r2BtnText }}</a>
                </div>
                <div class="ed-card-img-wrap">
                    <img src="{{ $r2Image }}" alt="" class="ed-card-img" />
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testosterone Banner Section -->
@php
    $testo = $homeSettings['testosterone_banner'] ?? [];
    $testoPill = $testo['pill'] ?? 'TESTOSTERON-INJEKTION';
    
    // Process title: wrap text after | in a span with .testo-red
    $rawTestoTitle = $testo['title'] ?? 'Testosteron-Injektion — | fertig zur Direktnutzung';
    $tParts = explode('|', $rawTestoTitle);
    if (count($tParts) > 1) {
        $processedTestoTitle = $tParts[0];
        for ($i = 1; $i < count($tParts); $i++) {
            if ($i % 2 != 0) {
                $processedTestoTitle .= '<span class="testo-red">' . $tParts[$i] . '</span>';
            } else {
                $processedTestoTitle .= $tParts[$i];
            }
        }
    } else {
        $processedTestoTitle = $rawTestoTitle;
    }
    $testoHeading = $processedTestoTitle;

    $testoHeroImage = !empty($testo['hero_image']) ? url('images/upload/'.$testo['hero_image']) : 'https://images.unsplash.com/photo-1579722820308-d74e571900a9?q=80&w=1000&auto=format&fit=crop';
    
    $testoBtn1Text = !empty($testo['btn1_text']) ? $testo['btn1_text'] : 'Mehr erfahren';
    $testoBtn1Url = !empty($testo['btn1_url']) ? $testo['btn1_url'] : route('categories');
    $testoBtn2Text = !empty($testo['btn2_text']) ? $testo['btn2_text'] : 'Jetzt Beratung starten';
    $testoBtn2Url = !empty($testo['btn2_url']) ? $testo['btn2_url'] : route('categories');

    $tLeft = $testo['left_card'] ?? [];
    $tLeftTitle = !empty($tLeft['title']) ? str_replace('|', '<br>', $tLeft['title']) : 'Energie und Antrieb zurückgewinnen';
    $tLeftBtnText = !empty($tLeft['btn_text']) ? $tLeft['btn_text'] : 'Mehr erfahren';
    $tLeftBtnUrl = !empty($tLeft['btn_url']) ? $tLeft['btn_url'] : route('categories');
    $tLeftImage = !empty($tLeft['image']) ? url('images/upload/'.$tLeft['image']) : 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?q=80&w=1000&auto=format&fit=crop';

    $tRight = $testo['right_card'] ?? [];
    $tRightTitle = !empty($tRight['title']) ? str_replace('|', '<br>', $tRight['title']) : 'Fertige Injektion — einfach und sicher';
    $tRightBtnText = !empty($tRight['btn_text']) ? $tRight['btn_text'] : 'Behandlung starten';
    $tRightBtnUrl = !empty($tRight['btn_url']) ? $tRight['btn_url'] : route('categories');
    $tRightImage = !empty($tRight['image']) ? url('images/upload/'.$tRight['image']) : 'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?q=80&w=1000&auto=format&fit=crop';
@endphp
<section class="testo-banner-section">
    <div class="testo-inner">
        <span class="testo-pill">{{ $testoPill }}</span>
        <h2 class="testo-heading">{!! $testoHeading !!}</h2>
        <div class="testo-hero-img-wrap">
            <img src="{{ $testoHeroImage }}" alt="Testosterone" class="testo-hero-img" />
            <div class="testo-btns-overlay">
                <a href="{{ $testoBtn1Url }}" class="testo-btn testo-btn-outline">{{ $testoBtn1Text }}</a>
                <a href="{{ $testoBtn2Url }}" class="testo-btn testo-btn-filled">{{ $testoBtn2Text }}</a>
            </div>
        </div>
    </div>
    <div class="testo-cards">
        <div class="testo-card">
            <div class="testo-card-text">
                <h3>{!! $tLeftTitle !!}</h3>
                <a href="{{ $tLeftBtnUrl }}" class="testo-card-btn">{{ $tLeftBtnText }}</a>
            </div>
            <div class="testo-card-img-wrap">
                <img src="{{ $tLeftImage }}" alt="" class="testo-card-img" />
            </div>
        </div>
        <div class="testo-card">
            <div class="testo-card-text">
                <h3>{!! $tRightTitle !!}</h3>
                <a href="{{ $tRightBtnUrl }}" class="testo-card-btn">{{ $tRightBtnText }}</a>
            </div>
            <div class="testo-card-img-wrap">
                <img src="{{ $tRightImage }}" alt="" class="testo-card-img" />
            </div>
        </div>
    </div>
</section>

<!-- Weight Management Banner Section -->
@php
    $wl = $homeSettings['weight_loss_banner'] ?? [];
    $wlPill = $wl['pill'] ?? 'GEWICHTSMANAGEMENT';
    
    // Process title: wrap text after | in a span with .wl-tint (dark teal)
    $rawWlTitle = $wl['title'] ?? 'Gesund abnehmen — mit | ärztlicher Begleitung';
    $wParts = explode('|', $rawWlTitle);
    if (count($wParts) > 1) {
        $processedWlTitle = $wParts[0];
        for ($i = 1; $i < count($wParts); $i++) {
            if ($i % 2 != 0) {
                $processedWlTitle .= '<span class="wl-tint">' . $wParts[$i] . '</span>';
            } else {
                $processedWlTitle .= $wParts[$i];
            }
        }
    } else {
        $processedWlTitle = $rawWlTitle;
    }
    $wlHeading = $processedWlTitle;
    
    $wlSubtext = $wl['subtext'] ?? 'Abnehmspritze, Ernährungsberatung und medikamentöse Therapie – alles aus einer Hand, 100% online.';

    $wlHeroImage = !empty($wl['hero_image']) ? url('images/upload/'.$wl['hero_image']) : 'https://images.unsplash.com/photo-1518310383802-640c2de311b2?w=800&q=80';
    
    $wlBtn1Text = !empty($wl['btn1_text']) ? $wl['btn1_text'] : 'Mehr erfahren';
    $wlBtn1Url = !empty($wl['btn1_url']) ? $wl['btn1_url'] : route('categories');
    $wlBtn2Text = !empty($wl['btn2_text']) ? $wl['btn2_text'] : 'Beratung starten';
    $wlBtn2Url = !empty($wl['btn2_url']) ? $wl['btn2_url'] : route('categories');

    $wlLeft = $wl['left_card'] ?? [];
    $wlLeftTitle = !empty($wlLeft['title']) ? str_replace('|', '<br>', $wlLeft['title']) : 'Abnehmspritze — einfach und effektiv';
    $wlLeftBtnText = !empty($wlLeft['btn_text']) ? $wlLeft['btn_text'] : 'Jetzt informieren';
    $wlLeftBtnUrl = !empty($wlLeft['btn_url']) ? $wlLeft['btn_url'] : route('categories');
    $wlLeftImage = !empty($wlLeft['image']) ? url('images/upload/'.$wlLeft['image']) : 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?w=800&q=80';

    $wlRight = $wl['right_card'] ?? [];
    $wlRightTitle = !empty($wlRight['title']) ? str_replace('|', '<br>', $wlRight['title']) : 'Ärztlich begleitet — sicher zum Wunschgewicht';
    $wlRightBtnText = !empty($wlRight['btn_text']) ? $wlRight['btn_text'] : 'Behandlung starten';
    $wlRightBtnUrl = !empty($wlRight['btn_url']) ? $wlRight['btn_url'] : route('categories');
    $wlRightImage = !empty($wlRight['image']) ? url('images/upload/'.$wlRight['image']) : 'https://images.unsplash.com/photo-1576091160550-2173ff9e5ee5?w=800&q=80';
@endphp
<section class="wl-banner-section">
    <div class="wl-inner">
        <span class="wl-pill">{{ $wlPill }}</span>
        <h2 class="wl-heading">{!! $wlHeading !!}</h2>
        <p class="wl-sub">{{ $wlSubtext }}</p>
        <div class="wl-hero-img-wrap">
            <img src="{{ $wlHeroImage }}" alt="Weight Management" class="wl-hero-img" />
            <div class="wl-btns-overlay">
                <a href="{{ $wlBtn1Url }}" class="wl-btn wl-btn-outline">{{ $wlBtn1Text }}</a>
                <a href="{{ $wlBtn2Url }}" class="wl-btn wl-btn-filled">{{ $wlBtn2Text }}</a>
            </div>
        </div>
    </div>
    <div class="wl-cards">
        <div class="wl-card">
            <div class="wl-card-text">
                <h3>{!! $wlLeftTitle !!}</h3>
                <a href="{{ $wlLeftBtnUrl }}" class="wl-card-btn">{{ $wlLeftBtnText }}</a>
            </div>
            <div class="wl-card-img-wrap">
                <img src="{{ $wlLeftImage }}" alt="" class="wl-card-img" />
            </div>
        </div>
        <div class="wl-card">
            <div class="wl-card-text">
                <h3>{!! $wlRightTitle !!}</h3>
                <a href="{{ $wlRightBtnUrl }}" class="wl-card-btn">{{ $wlRightBtnText }}</a>
            </div>
            <div class="wl-card-img-wrap">
                <img src="{{ $wlRightImage }}" alt="" class="wl-card-img" />
            </div>
        </div>
    </div>
</section>

<!-- Medical Advisory Board Section -->
@php
    $advData = $homeSettings['medical_advisors'] ?? [];
    $advHeading = $advData['heading'] ?? 'Unser medizinischer Beirat';
    $advSlots = $advData['slots'] ?? [];
    
    // Provide some default dummy data if nothing is configured yet
    if (empty($advSlots)) {
        $advSlots = [
            ['name' => 'Dr. med. Roland M. Ruiken', 'image' => ''],
            ['name' => 'Dr. med. Salomé Apitz', 'image' => ''],
            ['name' => 'Dr. med. Viktor Simunovic', 'image' => ''],
            ['name' => 'Dr. med. Expert', 'image' => ''],
            ['name' => 'Dr. med. Senior Berater', 'image' => '']
        ];
    }
@endphp
<section class="advisors-section">
    <div class="advisors-container">
        <h2 class="advisors-heading">{{ $advHeading }}</h2>
        <div class="advisors-grid">
            @foreach($advSlots as $slot)
                @if(!empty($slot['name']) || !empty($slot['image']))
                    <div class="advisor-card">
                        <div class="advisor-img-wrap">
                            @if(!empty($slot['image']))
                                <img src="{{ url('images/upload/'.$slot['image']) }}" alt="{{ $slot['name'] ?? 'Advisor' }}" class="advisor-img" />
                            @else
                                <div class="advisor-img advisor-img-placeholder"></div>
                            @endif
                        </div>
                        <div class="advisor-name">
                            {!! !empty($slot['name']) ? str_replace('|', '<br>', $slot['name']) : '' !!}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>

<!-- Statistics Showcase Section -->
@php
    $statsData = $homeSettings['stats_section'] ?? [];
    $statsHeading = $statsData['heading'] ?? 'Rund um die Uhr Hilfe von deutschen Ärzten';
    
    $statsLeft = $statsData['left_card'] ?? [];
    $sLeftTop = $statsLeft['top_text'] ?? 'ÜBER';
    $sLeftNum = $statsLeft['number'] ?? '8.000';
    $sLeftBot = $statsLeft['bottom_text'] ?? 'Stammkunden';

    $statsRight = $statsData['right_card'] ?? [];
    $sRightTop = $statsRight['top_text'] ?? 'ÜBER';
    $sRightNum = $statsRight['number'] ?? '12';
    $sRightBot = $statsRight['bottom_text'] ?? 'Jahre Expertise';
@endphp
<section class="stats-section">
    <div class="stats-container">
        <h3 class="stats-heading">{{ $statsHeading }}</h3>
        <div class="stats-cards">
            <div class="stats-card">
                <span class="stats-top">{{ $sLeftTop }}</span>
                <span class="stats-num">{{ $sLeftNum }}</span>
                <span class="stats-bot">{{ $sLeftBot }}</span>
            </div>
            <div class="stats-card">
                <span class="stats-top">{{ $sRightTop }}</span>
                <span class="stats-num">{{ $sRightNum }}</span>
                <span class="stats-bot">{{ $sRightBot }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Comparison Section -->
@php
    $compData = $homeSettings['comparison_section'] ?? [];
    $compPill = $compData['pill'] ?? 'Natürlich. Sicher. Deutsch.';
    
    // Process title: wrap text within | in a span with .comp-tint (vibrant purple)
    $rawCompTitle = $compData['heading'] ?? 'Warum |dr.fuxx|?';
    $cParts = explode('|', $rawCompTitle);
    if (count($cParts) > 1) {
        $processedCompTitle = $cParts[0];
        for ($i = 1; $i < count($cParts); $i++) {
            if ($i % 2 != 0) {
                $processedCompTitle .= '<span class="comp-tint">' . $cParts[$i] . '</span>';
            } else {
                $processedCompTitle .= $cParts[$i];
            }
        }
    } else {
        $processedCompTitle = $rawCompTitle;
    }
    
    $compSub = $compData['subheading'] ?? 'Der Unterschied, der zählt';
    $compCenterImage = !empty($compData['center_image']) ? url('images/upload/'.$compData['center_image']) : 'https://images.unsplash.com/photo-1512428559087-560fa5ceab42?w=800&q=80'; /* default phone placeholder */

    $compLeftTitle = $compData['left_col_title'] ?? 'ANDERE ANBIETER';
    $compRightTitle = $compData['right_col_title'] ?? 'DR. FUXX';
    $compRows = $compData['rows'] ?? [
        ['left' => 'Keine deutschen Ärzte', 'right' => 'Deutsche Ärzte'],
        ['left' => 'Daten im Ausland', 'right' => 'Daten sicher in DE'],
        ['left' => 'Keine DSGVO', 'right' => '100% DSGVO-konform'],
        ['left' => 'Support eingeschränkt', 'right' => 'Immer erreichbar'],
        ['left' => 'Ausländische Tech', 'right' => 'Made in Germany']
    ];

    $compBtnText = !empty($compData['btn_text']) ? $compData['btn_text'] : 'Jetzt Rezept anfragen &rarr;';
    $compBtnUrl = !empty($compData['btn_url']) ? $compData['btn_url'] : route('categories');
    $compBtnSub = $compData['btn_subtext'] ?? 'Kostenlose Erstberatung &bull; Rezept in 24h';
@endphp
<section class="comparison-section">
    <div class="comp-container">
        <div class="comp-header">
            <span class="comp-pill"><i class="bi bi-shield-check"></i> {{ $compPill }}</span>
            <h2 class="comp-heading">{!! $processedCompTitle !!}</h2>
            <p class="comp-sub">{{ $compSub }}</p>
        </div>
        
        <div class="comp-board">
            <div class="comp-floating-img-wrap">
                <img src="{{ $compCenterImage }}" alt="App Demo" class="comp-img" />
            </div>
            
            <div class="comp-table-card">
                <div class="comp-table-head">
                    <div class="comp-col-h comp-col-left">{{ $compLeftTitle }}</div>
                    <div class="comp-col-h comp-col-right"><i class="bi bi-shield-check"></i> {{ $compRightTitle }}</div>
                </div>
                <div class="comp-table-body">
                    @foreach($compRows as $row)
                        @if(!empty($row['left']) || !empty($row['right']))
                        <div class="comp-row">
                            <div class="comp-col comp-left-item">
                                <span class="comp-dot-left"></span> {{ $row['left'] }}
                            </div>
                            <div class="comp-col comp-right-item">
                                <span class="comp-dot-right"></span> {{ $row['right'] }}
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="comp-cta-wrap">
            <a href="{{ $compBtnUrl }}" class="comp-btn">{!! $compBtnText !!}</a>
            <p class="comp-btn-sub">{!! $compBtnSub !!}</p>
        </div>
    </div>
</section>

<!-- FAQ Section -->
@php
    $faqData = $homeSettings['faq_section'] ?? [];
    $faqItems = $faqData['items'] ?? [];
    
    // Provide demo defaults if empty so it's always visible for the user to see the design
    if (empty($faqItems)) {
        $faqItems = [
            ['question' => 'Was ist dr.fuxx?', 'answer' => 'dr.fuxx ist eine digitale Gesundheits- und Apothekenplattform mit Sitz in Deutschland. Wir verbinden Patienten mit zugelassenen Ärzten.'],
            ['question' => 'Wie funktioniert dr.fuxx?', 'answer' => 'Sie wählen eine Behandlung, füllen den medizinischen Fragebogen aus und erhalten nach ärztlicher Prüfung Ihr Rezept.'],
            ['question' => 'Wer sind die Anbieter bei dr.fuxx?', 'answer' => 'Wir arbeiten ausschließlich mit zertifizierten deutschen Ärzten und Partnerapotheken zusammen.'],
        ];
    }
@endphp
@if(!empty($faqItems))
<section class="faq-section">
    <div class="faq-container">
        <div class="faq-header">
            <h2 class="faq-heading">{{ $faqData['heading'] ?? 'Sie haben Fragen?' }}</h2>
            <p class="faq-sub">{{ $faqData['subheading'] ?? 'Hier gibt es Antworten!' }}</p>
        </div>

        <div class="faq-accordion" id="homeFaqAccordion">
            @foreach($faqItems as $index => $item)
            <div class="faq-item">
                <div class="faq-question" 
                     data-bs-toggle="collapse" 
                     data-bs-target="#faqCollapse{{ $index }}" 
                     aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                     aria-controls="faqCollapse{{ $index }}">
                    <span class="faq-q-text">{{ $item['question'] }}</span>
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </div>
                <div id="faqCollapse{{ $index }}" 
                     class="collapse {{ $index === 0 ? 'show' : '' }}" 
                     data-bs-parent="#homeFaqAccordion">
                    <div class="faq-answer">
                        {!! nl2br(e($item['answer'])) !!}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@php
    $mediaData = $homeSettings['media_section'] ?? [];
    $mediaItems = $mediaData['items'] ?? [];
    
    // Provide demo defaults if empty
    if (empty($mediaItems)) {
        $mediaItems = ["BILD", "TAGESSPIEGEL", "FOCUS", "news.de", "OK!", "WESTFALEN", "nordbayern", "Rhönische Nachrichten"];
    }
    
    $ctaData = $homeSettings['cta_section'] ?? [];
    
    // Provide demo CTA if empty
    if (is_array($ctaData) && empty($ctaData)) {
        $ctaData = [
            'heading' => 'Bereit? In 3 Minuten zu deinem Rezept.',
            'btn_text' => 'Jetzt kostenlos starten',
            'btn_url' => '#',
            'subtext' => 'Keine Kosten bis zur Rezeptausstellung – unverbindlich testen'
        ];
    }
@endphp

<section class="press-logos">
    <div class="press-logos-inner">
        <span class="press-label">{{ $mediaData['heading'] ?? 'Bekannt aus' }}</span>
        <div class="press-track">
            @foreach($mediaItems as $item)
                <span class="press-logo-item">{{ $item }}</span>
            @endforeach
        </div>
    </div>
</section>

<section class="mid-cta">
    <div class="mid-cta-inner">
        <h2>{{ $ctaData['heading'] ?? 'Bereit? In 3 Minuten zu deinem Rezept.' }}</h2>
        <a href="{{ $ctaData['btn_url'] ?? '#' }}" class="btn-cta-lg">{{ $ctaData['btn_text'] ?? 'Jetzt kostenlos starten' }}</a>
        @if(!empty($ctaData['subtext']))
            <p class="mid-cta-note">{{ $ctaData['subtext'] }}</p>
        @endif
    </div>
</section>

@php
    $privData = $homeSettings['privacy_section'] ?? [];
    $privFeatures = $privData['features'] ?? [];
    $privPills = $privData['pills'] ?? [];
    
    // Provide demo defaults if empty
    if (empty($privFeatures)) {
        $privFeatures = ["Deutsche Ärzte", "DSGVO-konform", "Sitz in DE", "Immer erreichbar"];
    }
    if (empty($privPills)) {
        $privPills = ["100% DSGVO", "Deutsche Server", "Kein Ausland"];
    }
@endphp

<section class="privacy-v2">
    <div class="privacy-container container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <span class="privacy-label">{{ $privData['label'] ?? 'DATENSCHUTZ' }}</span>
                <h2 class="privacy-heading">
                    {{ $privData['heading_1'] ?? 'Ihre Privatsphäre.' }}
                    <span class="khaki-italic">{{ $privData['heading_2'] ?? 'Unsere Priorität.' }}</span>
                </h2>
                <div class="privacy-intro">
                    {{ $privData['subtext'] ?? 'Ihre Daten bleiben sicher in Deutschland — geschützt durch deutsche Ärzte, deutsche Server und volle DSGVO-Konformität.' }}
                </div>
            </div>
            <div class="col-lg-7">
                <div class="privacy-card">
                    <div class="germany-badge">
                        <div class="badge-icon">
                            <img src="https://drfuxx.stratolution.de/sample-a-klassisch/img/made-in-germany.svg" width="30" alt="Shield" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2092/2092663.png'">
                        </div>
                        <div class="badge-text">
                            <h4>Made in Germany</h4>
                            <p>Entwickelt, gehostet und betrieben in Deutschland</p>
                        </div>
                    </div>
                    
                    <div class="flags-row">
                        <div class="flag-pill">
                            <img src="https://flagcdn.com/w20/de.png" width="18" alt="DE"> DE
                        </div>
                        <span class="text-muted small">— Daten hier</span>
                        <div class="flag-pill">
                            <img src="https://flagcdn.com/w20/eu.png" width="18" alt="EU"> EU
                        </div>
                        <span class="text-muted small">Kein Transfer</span>
                    </div>

                    <div class="privacy-feature-grid">
                        @foreach($privFeatures as $feature)
                        <div class="privacy-feature-item">
                            <span class="purple-dot"></span>
                            {{ $feature }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <div class="privacy-bottom-pills text-center">
            @foreach($privPills as $pill)
            <span class="priv-pill">{{ $pill }}</span>
            @endforeach
        </div>
    </div>
</section>

@php
    $nlData = $homeSettings['newsletter_section'] ?? [];
    $nlHeading = $nlData['heading'] ?? "Bleib auf dem\nLaufenden";
    $nlBg = $nlData['bg_image'] ?? 'https://drfuxx.stratolution.de/WhatsApp%20Image%202026-03-17%20at%2009.45.39%20%281%29.jpeg';
@endphp

<section class="newsletter-hero">
    <div class="nl-hero-bg" style="background-image: url('{{ $nlBg }}');"></div>
    <div class="nl-hero-content">
        <h2>{!! nl2br(e($nlHeading)) !!}</h2>
        <p>{{ $nlData['subtext'] ?? 'Meld dich für unseren Newsletter an und erhalte Updates, Tipps und Sonderangebote per E-Mail.' }}</p>
        
        <form class="nl-hero-form">
            <input type="email" placeholder="E-Mail" required>
            <button type="submit">{{ $nlData['btn_text'] ?? $nlData['label'] ?? 'Abonnieren' }}</button>
        </form>
        
        <div class="nl-hero-legal">
            {!! $nlData['legal'] ?? 'Mit der Erstellung eines Kontos per E-Mail stimme ich den <a href="#">AGB</a> zu und erkenne die <a href="#">Datenschutzerklärung</a> an.' !!}
        </div>
    </div>
</section>

<section class="treatment-areas-section py-5" style="background-color: #f2efea !important;" id="services">

<!-- Our Treatment Areas – Carousel Section -->
    <div class="container py-4">
        <div class="treatment-areas-header mb-4">
            <span class="treatment-areas-label">{{ __('landing.treatments.label') }}</span>
            <h2 class="display-5 fw-bold mb-2">{{ __('landing.treatments.title') }}</h2>
            <p class="lead text-muted mb-0">{{ __('landing.treatments.subtitle') }}</p>
        </div>

        <div class="treatment-areas-viewport" id="treatment-viewport">
            <div class="treatment-areas-track" id="treatment-track">
                @php
                    $iconMap = [
                        "Men's Health" => ['icon' => 'bi-heart-pulse', 'badge' => 'Popular'],
                        "Women's Health" => ['icon' => 'bi-person', 'badge' => null],
                        "General Medicine" => ['icon' => 'bi-capsule', 'badge' => null],
                        "Weight Management" => ['icon' => 'bi-activity', 'badge' => 'New'],
                        "Travel Medicine" => ['icon' => 'bi-shield-check', 'badge' => null],
                        "Skin Health" => ['icon' => 'bi-stars', 'badge' => null],
                    ];
                    $treatmentCards = $categories->take(12);
                @endphp
                @forelse($treatmentCards as $index => $category)
                    @php
                        $treatmentName = $category->treatment ? $category->treatment->name : 'General Medicine';
                        $iconData = $iconMap[$treatmentName] ?? ['icon' => 'bi-capsule', 'badge' => null];
                    @endphp
                    <div class="treatment-area-card">
                        <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-decoration-none text-dark">
                            <div class="treatment-card-body">
                                <h3 class="treatment-card-title">{{ $category->name }}</h3>
                                <div class="treatment-card-tags">
                                    <span class="treatment-tag treatment-tag-type">{{ $treatmentName }}</span>
                                    @if($category->price && $category->price > 0)
                                        <span class="treatment-tag treatment-tag-info">{{ __('landing.common.from') }} {{ number_format($category->price, 0) }} €</span>
                                    @endif
                                </div>
                                <p class="treatment-card-sub">{{ $category->description ? Str::limit($category->description, 60) : __('landing.treatments.default_description') }}</p>
                                <span class="treatment-card-cta">{{ __('landing.common.learn_more') }} <i class="bi bi-arrow-right"></i></span>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="treatment-area-card">
                        <a href="{{ route('categories') }}" class="text-decoration-none text-dark">
                            <div class="treatment-card-body">
                                <h3 class="treatment-card-title">{{ __('landing.treatments.browse_treatments') }}</h3>
                                <div class="treatment-card-tags">
                                    <span class="treatment-tag treatment-tag-type">General Medicine</span>
                                </div>
                                <p class="treatment-card-sub">{{ __('landing.treatments.default_description') }}</p>
                                <span class="treatment-card-cta">{{ __('landing.common.learn_more') }} <i class="bi bi-arrow-right"></i></span>
                            </div>
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="treatment-areas-controls">
            <a href="{{ route('categories') }}" class="btn btn-primary treatment-btn-discover" id="treatment-btn-discover">{{ __('landing.treatments.view_all') }}</a>
            <div class="treatment-controls-right">
                <div class="treatment-dots" id="treatment-dots"></div>
                <div class="treatment-arrow-group">
                    <button type="button" class="treatment-arrow-btn" id="treatment-prev-btn" aria-label="{{ __('landing.common.previous') }}">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="treatment-arrow-btn" id="treatment-next-btn" aria-label="{{ __('landing.common.next') }}">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-bloomwell-dark text-white" id="how-it-works">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('landing.how_it_works.title') }}</h2>
            <p class="lead" style="color: rgba(255,255,255,0.8);">{{ __('landing.how_it_works.subtitle') }}</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 position-relative shadow-bloomwell">
                    <div class="step-connector d-none d-lg-block"></div>
                    <div class="position-relative mb-3">
                        <div class="step-icon bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto">
                            <i class="bi bi-file-text text-white"></i>
                        </div>
                        <span class="step-number position-absolute bg-white text-primary border border-primary rounded-circle">01</span>
                    </div>
                    <h5 class="fw-semibold mb-2">{{ __('landing.how_it_works.step1_title') }}</h5>
                    <p class="text-muted small">{{ __('landing.how_it_works.step1_text') }}</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 position-relative shadow-bloomwell">
                    <div class="step-connector d-none d-lg-block"></div>
                    <div class="position-relative mb-3">
                        <div class="step-icon bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto">
                            <i class="bi bi-heart-pulse text-white"></i>
                        </div>
                        <span class="step-number position-absolute bg-white text-primary border border-primary rounded-circle">02</span>
                    </div>
                    <h5 class="fw-semibold mb-2">{{ __('landing.how_it_works.step2_title') }}</h5>
                    <p class="text-muted small">{{ __('landing.how_it_works.step2_text') }}</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 position-relative shadow-bloomwell">
                    <div class="step-connector d-none d-lg-block"></div>
                    <div class="position-relative mb-3">
                        <div class="step-icon bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto">
                            <i class="bi bi-box-seam text-white"></i>
                        </div>
                        <span class="step-number position-absolute bg-white text-primary border border-primary rounded-circle">03</span>
                    </div>
                    <h5 class="fw-semibold mb-2">{{ __('landing.how_it_works.step3_title') }}</h5>
                    <p class="text-muted small">{{ __('landing.how_it_works.step3_text') }}</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="position-relative mb-3">
                        <div class="step-icon bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto">
                            <i class="bi bi-check-circle text-white"></i>
                        </div>
                        <span class="step-number position-absolute bg-white text-primary border border-primary rounded-circle">04</span>
                    </div>
                    <h5 class="fw-semibold mb-2">{{ __('landing.how_it_works.step4_title') }}</h5>
                    <p class="text-muted small">{{ __('landing.how_it_works.step4_text') }}</p>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('categories') }}" class="btn btn-primary btn-lg btn-start-treatment">{{ __('landing.how_it_works.cta') }}</a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5 position-relative overflow-hidden" style="background-color: #f2efea !important;" id="about">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 order-2 order-lg-1 position-relative">
                <div class="position-relative z-index-1">
                    <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&h=600&fit=crop" 
                         alt="{{ __('landing.about.image_alt') }}" 
                         class="img-fluid rounded-4 shadow-lg w-100" style="object-fit: cover; border: 8px solid rgba(255,255,255,0.5);">
                    
                    <!-- Floating Stat Card 1 -->
                    <div class="position-absolute bg-white rounded-4 p-3 shadow-bloomwell d-flex align-items-center gap-3" style="bottom: -20px; left: -20px; z-index: 2; animation: float 5s ease-in-out infinite;">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-5 text-dark">{{ __('landing.about.certified') }}</div>
                            <div class="small text-muted mb-0">{{ __('landing.about.eu_doctors') }}</div>
                        </div>
                    </div>
                </div>
                <!-- Decorative background blur shape -->
                <div class="position-absolute top-50 start-50 translate-middle rounded-circle bg-primary opacity-25"></div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 ps-lg-5">
                <div class="mb-4">
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 border shadow-sm fw-semibold mb-3">
                        <i class="bi bi-stars me-1 text-warning"></i> {{ __('landing.about.badge') }}
                    </span>
                    <h2 class="display-4 fw-bold mb-3 text-dark" style="line-height: 1.1;">{{ __('landing.about.welcome_to') }} <span class="text-primary">{{ $setting->business_name }}</span></h2>
                    <p class="lead text-muted mb-4" style="line-height: 1.8;">
                        {{ __('landing.about.description') }}
                    </p>
                </div>
                
                <div class="d-flex flex-column gap-3 mb-5">
                    <div class="d-flex align-items-start gap-3 bg-white p-3 rounded-4 shadow-sm border border-light hover-lift flex-column flex-sm-row">
                        <div class="bg-purple-light text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                            <i class="bi bi-laptop fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-1 text-dark">{{ __('landing.about.feature1_title') }}</h5>
                            <p class="text-muted small mb-0">{{ __('landing.about.feature1_text') }}</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start gap-3 bg-white p-3 rounded-4 shadow-sm border border-light hover-lift flex-column flex-sm-row">
                        <div class="bg-teal-light text-accent rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                            <i class="bi bi-file-earmark-medical fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-1 text-dark">{{ __('landing.about.feature2_title') }}</h5>
                            <p class="text-muted small mb-0">{{ __('landing.about.feature2_text') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <a href="{{ url('/about-us') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm align-items-center d-inline-flex gap-2">
                        {{ __('landing.about.story_cta') }}
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    
                    <div class="d-flex align-items-center gap-2 bg-white rounded-pill px-3 py-2 shadow-sm border">
                        <div class="d-flex" style="font-size: 0.9rem;">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <span class="fw-bold small text-dark">{{ number_format($reviews->count() * 12500) }}+ {{ __('landing.about.patients') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5 bg-bloomwell-dark text-white" id="why-choose-us">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">{{ __('landing.why_choose_us.title') }}</h2>
            <p class="lead" style="color: rgba(255,255,255,0.8);">{{ __('landing.why_choose_us.subtitle') }}</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-shield-check text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">{{ __('landing.why_choose_us.item1_title') }}</h5>
                    <p class="text-muted small mb-0">{{ __('landing.why_choose_us.item1_text') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-clock text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">{{ __('landing.why_choose_us.item2_title') }}</h5>
                    <p class="text-muted small mb-0">{{ __('landing.why_choose_us.item2_text') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-award text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">{{ __('landing.why_choose_us.item3_title') }}</h5>
                    <p class="text-muted small mb-0">{{ __('landing.why_choose_us.item3_text') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-lock text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">{{ __('landing.why_choose_us.item4_title') }}</h5>
                    <p class="text-muted small mb-0">{{ __('landing.why_choose_us.item4_text') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Safety & Quality Section -->
<section class="py-5" style="background-color: #f2efea !important;" id="safety">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 order-lg-2">
                <h2 class="display-5 fw-bold mb-4">{{ __('landing.safety.title') }}</h2>
                <p class="lead text-muted mb-4">
                    {{ __('landing.safety.subtitle') }}
                </p>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        {{ __('landing.safety.point1') }}
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        {{ __('landing.safety.point2') }}
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        {{ __('landing.safety.point3') }}
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        {{ __('landing.safety.point4') }}
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        {{ __('landing.safety.point5') }}
                    </li>
                </ul>
            </div>
            <div class="col-lg-6 order-lg-1">
                <img src="https://images.unsplash.com/photo-1584982751601-97dcc096659c?w=600&h=400&fit=crop" 
                     alt="{{ __('landing.safety.image_alt') }}" 
                     class="img-fluid rounded-3 shadow">
            </div>
        </div>
    </div>
</section>

<!-- Trust Section -->
<section class="py-5" style="background-color: #f2efea !important;" id="trust">
    <div class="container py-4">
        <!-- Trust Features -->
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="card bg-bloomwell-dark text-white border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-award text-primary fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">{{ __('landing.trust.item1_title') }}</h5>
                    <p class="small mb-0" style="color: rgba(255,255,255,0.8);">{{ __('landing.trust.item1_text') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-bloomwell-dark text-white border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-lock text-primary fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">{{ __('landing.trust.item2_title') }}</h5>
                    <p class="small mb-0" style="color: rgba(255,255,255,0.8);">{{ __('landing.trust.item2_text') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-bloomwell-dark text-white border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-clock text-primary fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">{{ __('landing.trust.item3_title') }}</h5>
                    <p class="small mb-0" style="color: rgba(255,255,255,0.8);">{{ __('landing.trust.item3_text') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-bloomwell-dark text-white border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-star-fill text-primary fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">
                        @if($reviews->count() > 0)
                            {{ number_format($reviews->avg('rate'), 1) }}/5 {{ __('landing.trust.rating') }}
                        @else
                        4.8/5 {{ __('landing.trust.rating') }}
                        @endif
                    </h5>
                    <p class="small mb-0" style="color: rgba(255,255,255,0.8);">{{ __('landing.trust.over_prefix') }} {{ number_format($reviews->count() * 12500) }} {{ __('landing.trust.over_suffix') }}</p>
                </div>
            </div>
        </div>

        <!-- Testimonials -->
        <div class="testimonials-section bg-bloomwell-dark text-white rounded-3 p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h2 class="display-6 fw-bold mb-4">{{ __('landing.testimonials.title') }}</h2>
                    
                    @forelse($reviews->take(2) as $review)
                        <div class="card mb-3 bg-white text-dark border-0 shadow-bloomwell">
                            <div class="card-body">
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill {{ $i <= $review->rate ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <p class="card-text text-muted mb-2">
                                    "{{ Str::limit($review->review, 150) }}"
                                </p>
                                <p class="fw-semibold mb-0">{{ $review->user ? $review->user->name : __('landing.testimonials.anonymous') }}</p>
                                <p class="small text-muted">{{ __('landing.testimonials.verified_patient') }}</p>
                            </div>
                        </div>
                    @empty
                        <!-- Fallback testimonials -->
                        <div class="card mb-3 bg-white text-dark border-0 shadow-bloomwell">
                            <div class="card-body">
                                <div class="mb-2">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                                <p class="card-text text-muted mb-2">
                                    "{{ __('landing.testimonials.fallback_quote') }}"
                                </p>
                                <p class="fw-semibold mb-0">Michael K.</p>
                                <p class="small text-muted">{{ __('landing.testimonials.verified_patient') }}</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&h=400&fit=crop" 
                         alt="{{ __('landing.testimonials.image_alt') }}" 
                         class="img-fluid rounded-3 shadow">
                </div>
            </div>
        </div>

        <!-- Certifications -->
        <div class="mt-5 pt-4 border-top">
            <p class="text-center text-muted small mb-3">{{ __('landing.certifications.title') }}</p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <div class="bg-light rounded px-4 py-2">
                    <span class="fw-medium">{{ __('landing.certifications.certified') }}</span>
                </div>
                <div class="bg-light rounded px-4 py-2">
                    <span class="fw-medium">{{ __('landing.certifications.licensed') }}</span>
                </div>
                <div class="bg-light rounded px-4 py-2">
                    <span class="fw-medium">{{ __('landing.certifications.gdpr') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5" style="background-color: #f2efea !important;" id="faq">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3 text-dark">{{ __('landing.faq.title') }}</h2>
            <p class="lead text-muted">{{ __('landing.faq.subtitle') }}</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion rounded-3 overflow-hidden" id="faqAccordion">
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                {{ __('landing.faq.q1') }}
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                {{ __('landing.faq.a1') }}
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                {{ __('landing.faq.q2') }}
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                {{ __('landing.faq.a2') }}
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                {{ __('landing.faq.q3') }}
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                {{ __('landing.faq.a3') }}
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                {{ __('landing.faq.q4') }}
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                {{ __('landing.faq.a4') }}
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                {{ __('landing.faq.q5') }}
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                {{ __('landing.faq.a5') }}
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                {{ __('landing.faq.q6') }}
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                {{ __('landing.faq.a6') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4 bg-white text-dark border-0">
                    <div class="card-body text-center p-4">
                        <h5 class="fw-semibold mb-2">{{ __('landing.faq.still_have_questions') }}</h5>
                        <p class="text-muted mb-3">{{ __('landing.faq.support_text') }}</p>
                        <a href="{{ url('/contact') }}" class="btn btn-primary">{{ __('landing.faq.contact_support') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('layout.partials.footer')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Treatment Areas Carousel -->
<script>
(function() {
    var viewport = document.getElementById('treatment-viewport');
    var track = document.getElementById('treatment-track');
    if (!viewport || !track) return;
    var cards = track.querySelectorAll('.treatment-area-card');
    if (cards.length === 0) return;

    var prevBtn = document.getElementById('treatment-prev-btn');
    var nextBtn = document.getElementById('treatment-next-btn');
    var dotsWrap = document.getElementById('treatment-dots');
    var index = 0;
    var dragging = false;
    var dragStartX = 0;
    var dragOffset = 0;
    var touchStartX = 0;
    var resizeTimer;

    function cardStep() {
        var gap = 20;
        var style = window.getComputedStyle(track);
        if (style.gap) gap = parseFloat(style.gap) || 20;
        return (cards[0].offsetWidth || 280) + gap;
    }

    function visibleCount() {
        return Math.max(1, Math.floor(viewport.offsetWidth / cardStep()));
    }

    function maxIdx() {
        return Math.max(0, cards.length - visibleCount());
    }

    function render(animated) {
        if (animated === false) track.style.transition = 'none';
        track.style.transform = 'translateX(-' + (index * cardStep()) + 'px)';
        if (animated === false) { void track.offsetWidth; track.style.transition = ''; }
        if (prevBtn) prevBtn.disabled = index <= 0;
        if (nextBtn) nextBtn.disabled = index >= maxIdx();
        var dots = dotsWrap.querySelectorAll('.treatment-dot');
        for (var i = 0; i < dots.length; i++) dots[i].classList.toggle('active', i === index);
    }

    function goTo(n) {
        index = Math.min(Math.max(n, 0), maxIdx());
        render(true);
    }

    function buildDots() {
        dotsWrap.innerHTML = '';
        var max = maxIdx();
        for (var i = 0; i <= max; i++) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'treatment-dot' + (i === 0 ? ' active' : '');
            btn.setAttribute('aria-label', @json(__('landing.treatments.go_to_slide')) + ' ' + (i + 1));
            (function(idx) { btn.addEventListener('click', function() { goTo(idx); }); })(i);
            dotsWrap.appendChild(btn);
        }
    }

    if (prevBtn) prevBtn.addEventListener('click', function() { goTo(index - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function() { goTo(index + 1); });

    viewport.addEventListener('mousedown', function(e) {
        dragging = true;
        dragStartX = e.clientX;
        dragOffset = index * cardStep();
        track.style.transition = 'none';
    });
    window.addEventListener('mousemove', function(e) {
        if (!dragging) return;
        var delta = dragStartX - e.clientX;
        var raw = Math.min(Math.max(dragOffset + delta, 0), maxIdx() * cardStep());
        track.style.transform = 'translateX(-' + raw + 'px)';
    });
    window.addEventListener('mouseup', function(e) {
        if (!dragging) return;
        dragging = false;
        track.style.transition = '';
        var delta = dragStartX - e.clientX;
        if (Math.abs(delta) > 60) goTo(delta > 0 ? index + 1 : index - 1);
        else render(true);
    });

    viewport.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        track.style.transition = 'none';
    }, { passive: true });
    viewport.addEventListener('touchend', function(e) {
        track.style.transition = '';
        var delta = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(delta) > 50) goTo(delta > 0 ? index + 1 : index - 1);
        else render(true);
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') goTo(index - 1);
        if (e.key === 'ArrowRight') goTo(index + 1);
    });

    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            index = Math.min(index, maxIdx());
            buildDots();
            render(false);
        }, 150);
    });

    buildDots();
    render(false);
})();
</script>
</body>
</html>
