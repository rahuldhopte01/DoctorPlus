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
    
    <!-- Custom CSS -->
    <link href="{{asset('css/new-design.css')}}?v={{ time() }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/website_header.css') }}">
    <link href="{{asset('styles.css')}}?v={{ time() }}" rel="stylesheet">
    <link href="{{asset('css/drfuxx-landing.css')}}" rel="stylesheet">
    <style>
        /* ── CSS vars used by drfuxx-landing.css ── */
        :root {
            --color-primary: #7c3aed;
            --color-primary-bg: #f5f3ff;
            --color-dark: #1a1a1a;
            --font-heading: 'Clash Display', sans-serif;
        }
        /* ── Sub-categories Ticker ── */
        .subcat-ticker {
            overflow: hidden;
            padding: 14px 0;
            border-top: 1px solid #e9e5f5;
            border-bottom: 1px solid #e9e5f5;
            background: #faf8ff;
        }
        .subcat-track {
            display: flex;
            gap: 8px;
            animation: subcatScroll 40s linear infinite;
            width: max-content;
        }
        .subcat-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 50px;
            border: 1.5px solid #e0e0e0;
            background: #faf8ff;
            color: #1f2129;
            font-size: 0.82rem;
            font-weight: 500;
            white-space: nowrap;
            text-decoration: none;
            transition: all 0.25s ease;
        }
        .subcat-item:hover {
            border-color: #7c3aed;
            color: #7c3aed;
            background: #f5f3ff;
        }
        @keyframes subcatScroll {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        /* ── Online Section (How It Works) ── */
        .online-section {
            background: linear-gradient(160deg, #ece8ff 0%, #ddd6fe 50%, #ece8ff 100%);
            padding: 80px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .section-title {
            font-family: 'Clash Display', sans-serif;
            font-size: 2.4rem;
            font-weight: 400;
            color: #1a1a1a;
            margin-bottom: 12px;
        }
        .section-title span { color: #7c3aed !important; }
        .section-sub {
            color: #605f64;
            max-width: 620px;
            margin: 0 auto 40px;
            font-size: 1rem;
        }
        .steps-wrapper {
            position: relative;
            max-width: 1280px;
            margin: 0 auto;
        }
        .steps-wave {
            position: absolute;
            top: 50%;
            left: -5%;
            width: 110%;
            height: 200px;
            transform: translateY(-50%);
            z-index: 0;
            pointer-events: none;
        }
        .steps-grid-tilted {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0;
            position: relative;
            z-index: 1;
            padding: 40px 20px;
        }
        .step-img-tilted {
            width: 100%;
            flex: 1;
            min-height: 180px;
            border-radius: 14px;
            overflow: hidden;
            position: relative;
            background: linear-gradient(135deg, #f3f0ff, #f5f3ff);
            margin-top: auto;
        }
        .step-img-tilted img { width:100%; height:100%; object-fit:cover; border-radius:14px; display:block; }
        /* ── Trust Banner ── */
        .trust-banner {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 60%, #5b21b6 100%);
            color: white;
            text-align: center;
            padding: 80px 24px;
        }
        .trust-banner h2 {
            font-family: 'Clash Display', sans-serif;
            font-size: 2.6rem;
            font-weight: 400;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.25;
        }
    </style>
    
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
            @if(!empty($relief['image']))
                <img src="{{ url('images/upload/'.$relief['image']) }}" alt="" class="cbs-hero-img">
            @else
                <!-- Placeholder if no image set so layout doesn't break -->
                <div style="height: 350px;"></div>
            @endif
            
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
        <div class="cbs-card">
            <div class="cbs-card-text">
                <h3>{{ $card['title'] }}</h3>
                @if(!empty($card['btn_text']))
                    <a href="{{ $card['btn_url'] ?? '#' }}" class="cbs-card-btn">{{ $card['btn_text'] }}</a>
                @endif
            </div>
            <div class="cbs-card-img-wrap">
                @if(!empty($card['icon']))
                    <img src="{{ url('images/upload/'.$card['icon']) }}" alt="" class="cbs-card-img" />
                @endif
            </div>
        </div>
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

<!-- Testosterone Section -->
@php
    $testo = $homeSettings['testosterone'] ?? [];
    $testoPill = $testo['pill'] ?? 'TESTOSTERON-INJEKTION';
    $testoRaw = $testo['title'] ?? 'Testosteron-Injektion —|fertig zur Direktnutzung';
    $testoParts = explode('|', $testoRaw, 2);
    $testoLine1 = trim($testoParts[0]);
    $testoLine2 = isset($testoParts[1]) ? trim($testoParts[1]) : '';
@endphp
<section class="ha-banner-section" style="background:linear-gradient(160deg, #f5f3ff 0%, #ede9fe 50%, #f5f3ff 100%) !important;position:relative;overflow:hidden;min-height:500px;display:flex;flex-direction:column;justify-content:center;">
    @if(!empty($testo['bg_image']))
        <img src="{{ url('images/upload/'.$testo['bg_image']) }}" alt="" style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);height:90%;width:auto;object-fit:contain;z-index:0;opacity:0.5;">
        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(245,243,255,0.9) 0%,rgba(237,233,254,0.5) 40%,transparent 70%);z-index:1;"></div>
    @endif
    <div class="ha-inner" style="position:relative;z-index:2;">
        <span class="ha-pill" style="border-color:#7c3aed !important;color:#7c3aed !important;">{{ $testoPill }}</span>
        <h2 class="ha-heading">{{ $testoLine1 }}@if($testoLine2)<br><span style="color:#7c3aed">{{ $testoLine2 }}</span>@endif</h2>
        <div class="testo-btns">
            <a href="{{ $testo['btn1_url'] ?? '#' }}" class="testo-btn-outline">{{ $testo['btn1_text'] ?? 'Mehr erfahren' }}</a>
            <a href="{{ $testo['btn2_url'] ?? '#' }}" class="testo-btn-filled" style="background:#7c3aed;border-color:#7c3aed;">{{ $testo['btn2_text'] ?? 'Jetzt Beratung starten' }}</a>
        </div>
    </div>
</section>

<!-- Advisory Board Section (COMMAND 7a) -->
@php $advisory = $home['advisory'] ?? []; @endphp
<section class="advisory-section">
    <div style="max-width:900px;margin:0 auto;">
        <h2 style="font-size:1.8rem;font-weight:800;color:#1a1a1a;text-align:center;margin-bottom:8px;">{{ $advisory['title'] ?? 'Unser medizinischer Beirat' }}</h2>
        <div class="doc-grid">
            @if(!empty($advisory['doctors']))
                @foreach($advisory['doctors'] as $doctor)
                <div class="doc-card">
                    @if(!empty($doctor['image']))
                        <img src="{{ url('images/upload/'.$doctor['image']) }}" alt="{{ $doctor['name'] ?? '' }}">
                    @else
                        <img src="https://placehold.co/320x200/f3f0ff/1a1a1a?text=Dr.+med." alt="Placeholder">
                    @endif
                    <div class="doc-card-info">
                        <div class="doc-card-name">{{ $doctor['name'] ?? '' }}</div>
                        @if(!empty($doctor['role']))<div class="doc-card-role">{{ $doctor['role'] }}</div>@endif
                    </div>
                </div>
                @endforeach
            @else
                @for($i = 1; $i <= 5; $i++)
                <div class="doc-card">
                    <img src="https://placehold.co/320x200/f3f0ff/1a1a1a?text=Dr.+med.+Expert" alt="Dr. med. Expert">
                    <div class="doc-card-info">
                        <div class="doc-card-name">Dr. med. Expert</div>
                    </div>
                </div>
                @endfor
            @endif
        </div>
        <div class="doc-dots" id="docDots">
            <span class="doc-dot active"></span>
            <span class="doc-dot"></span>
            <span class="doc-dot"></span>
            <span class="doc-dot"></span>
            <span class="doc-dot"></span>
        </div>
    </div>
</section>

<!-- Stats Section (COMMAND 7b) -->
@php $stats = $home['stats'] ?? []; @endphp
<section class="cbs-stats-section">
    <p class="cbs-stats-sub">{{ $stats['subtitle'] ?? 'Rund um die Uhr Hilfe von deutschen Ärzten' }}</p>
    <div class="cbs-stats-grid">
        @if(!empty($stats['items']))
            @foreach($stats['items'] as $stat)
            <div class="cbs-stat">
                <span class="cbs-stat-label">ÜBER</span>
                <span class="cbs-stat-num">{{ $stat['number'] ?? '' }}</span>
                <span class="cbs-stat-title">{{ $stat['title'] ?? '' }}</span>
            </div>
            @endforeach
        @else
            <div class="cbs-stat">
                <span class="cbs-stat-label">ÜBER</span>
                <span class="cbs-stat-num">8.000</span>
                <span class="cbs-stat-title">Stammkunden</span>
            </div>
            <div class="cbs-stat">
                <span class="cbs-stat-label">ÜBER</span>
                <span class="cbs-stat-num">12</span>
                <span class="cbs-stat-title">Jahre Expertise</span>
            </div>
        @endif
    </div>
</section>

<!-- Comparison Section (COMMAND 7b) -->
@php $compare = $home['comparison'] ?? []; @endphp
<section class="comparison-section" style="position:relative;overflow:hidden;@if(!empty($compare['bg_image']))background-image:url('{{ url('images/upload/'.$compare['bg_image']) }}');background-size:cover;background-position:center;@endif">
    <div class="comparison-inner" style="position:relative;z-index:2;">
        <h2 class="comparison-title" style="margin-top:0;">{{ $compare['title'] ?? 'Warum dr.fuxx?' }}</h2>
        <div class="comp-table">
            <!-- Header row (hardcoded) -->
            <div class="comp-row comp-header">
                <div class="comp-col comp-left">
                    <span class="comp-badge-red">&#10007;</span>
                    Andere Anbieter
                </div>
                <div class="comp-col comp-right">
                    <span class="comp-badge-green">&#10003;</span>
                    Dr. Fuxx
                </div>
            </div>
            @if(!empty($compare['rows']))
                @foreach($compare['rows'] as $row)
                <div class="comp-row">
                    <div class="comp-col comp-left"><span class="comp-x">&times;</span> {{ $row['left'] ?? '' }}</div>
                    <div class="comp-col comp-right"><span class="comp-check">&#10003;</span> {{ $row['right'] ?? '' }}</div>
                </div>
                @endforeach
            @else
                <div class="comp-row">
                    <div class="comp-col comp-left"><span class="comp-x">&times;</span> Keine deutschen Ärzte</div>
                    <div class="comp-col comp-right"><span class="comp-check">&#10003;</span> Deutsche Ärzte</div>
                </div>
                <div class="comp-row">
                    <div class="comp-col comp-left"><span class="comp-x">&times;</span> Daten im Ausland</div>
                    <div class="comp-col comp-right"><span class="comp-check">&#10003;</span> Daten sicher in DE</div>
                </div>
                <div class="comp-row">
                    <div class="comp-col comp-left"><span class="comp-x">&times;</span> Keine DSGVO</div>
                    <div class="comp-col comp-right"><span class="comp-check">&#10003;</span> 100% DSGVO-konform</div>
                </div>
                <div class="comp-row">
                    <div class="comp-col comp-left"><span class="comp-x">&times;</span> Support eingeschränkt</div>
                    <div class="comp-col comp-right"><span class="comp-check">&#10003;</span> Immer erreichbar</div>
                </div>
                <div class="comp-row">
                    <div class="comp-col comp-left"><span class="comp-x">&times;</span> Ausländische Tech</div>
                    <div class="comp-col comp-right"><span class="comp-check">&#10003;</span> Made in Germany</div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- FAQ Section (COMMAND 7c) -->
@php $faq = $home['faq'] ?? []; @endphp
<section class="faq-section" id="faq-section">
    <div class="faq-inner">
        <div class="faq-header">
            <h2 class="faq-title">{{ $faq['title'] ?? 'Sie haben Fragen?' }}</h2>
            <p class="faq-subtitle">{{ $faq['subtitle'] ?? 'Hier gibt es Antworten!' }}</p>
        </div>
        <div class="faq-list">
            @if(!empty($faq['items']))
                @foreach($faq['items'] as $index => $item)
                <div class="faq-item {{ $index === 0 ? 'active' : '' }}">
                    <button class="faq-q">{{ $item['question'] ?? '' }}<span class="faq-icon">{{ $index === 0 ? '&#8963;' : '&#8964;' }}</span></button>
                    <div class="faq-a">
                        <p>{{ $item['answer'] ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            @else
                <div class="faq-item active">
                    <button class="faq-q">Was ist dr.fuxx?<span class="faq-icon">&#8963;</span></button>
                    <div class="faq-a">
                        <p>dr.fuxx ist eine digitale Gesundheits- und Apothekenplattform mit Sitz in Deutschland. Wir verbinden Patienten mit zugelassenen Ärzten, die medizinische Fernuntersuchungen durchführen und bei Bedarf elektronische Rezepte ausstellen.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-q">Wie funktioniert dr.fuxx?<span class="faq-icon">&#8964;</span></button>
                    <div class="faq-a">
                        <p>Wählen Sie Ihre Behandlung aus, beantworten Sie einen kurzen medizinischen Fragebogen und ein zugelassener Arzt prüft Ihre Angaben. Bei Eignung wird ein Rezept ausgestellt und das Medikament diskret zu Ihnen nach Hause geliefert – alles online, ohne Wartezeit.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-q">Wer sind die Anbieter bei dr.fuxx?<span class="faq-icon">&#8964;</span></button>
                    <div class="faq-a">
                        <p>Alle Ärzte auf dr.fuxx sind vollständig zugelassen und in Deutschland registriert. Unsere Partnerapotheken sind ebenfalls staatlich zertifiziert und unterliegen strengen Qualitätskontrollen.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-q">Benötigt man für dr.fuxx eine Versicherung?<span class="faq-icon">&#8964;</span></button>
                    <div class="faq-a">
                        <p>Nein, eine Krankenversicherung ist nicht erforderlich. dr.fuxx ist ein Privatanbieter und kann von jedem genutzt werden. Je nach Behandlung und Versicherung können jedoch Erstattungen möglich sein.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-q">Für wen ist dr.fuxx gedacht?<span class="faq-icon">&#8964;</span></button>
                    <div class="faq-a">
                        <p>dr.fuxx richtet sich an Erwachsene, die eine schnelle, diskrete und unkomplizierte medizinische Beratung und Behandlung suchen – ganz ohne Arzttermin oder lange Wartezeiten.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Press Logos Section (COMMAND 8) -->
@php $press = $home['press'] ?? []; @endphp
<section class="press-logos">
    <div class="press-logos-inner">
        <span class="press-label">{{ $press['label'] ?? 'Bekannt aus' }}</span>
        <div class="press-logos-track">
            @if(!empty($press['logos']))
                @foreach($press['logos'] as $logo)
                    <span class="press-logo-name">{{ $logo['name'] }}</span>
                @endforeach
            @else
                <span class="press-logo-name">BILD</span>
                <span class="press-logo-name">TAGESSPIEGEL</span>
                <span class="press-logo-name">FOCUS</span>
                <span class="press-logo-name">news.de</span>
                <span class="press-logo-name">OK!</span>
                <span class="press-logo-name">WESTFALEN</span>
            @endif
        </div>
    </div>
</section>

<!-- Mid-page CTA Section (COMMAND 8) -->
@php $cta = $home['mid_cta'] ?? []; @endphp
<section class="mid-cta">
    <div class="mid-cta-inner">
        <h2 class="mid-cta-heading">{{ $cta['heading'] ?? 'Bereit? In 3 Minuten zu deinem Rezept.' }}</h2>
        <a href="{{ $cta['btn_url'] ?? '#' }}" class="mid-cta-btn">{{ $cta['btn_text'] ?? 'Jetzt kostenlos starten' }}</a>
        <p class="mid-cta-note">{{ $cta['note'] ?? 'Keine Kosten bis zur Rezeptausstellung' }}</p>
    </div>
</section>

<!-- Privacy Section (COMMAND 8) -->
@php $priv = $home['privacy_section'] ?? []; @endphp
<section class="privacy-v2">
    <div class="privacy-v2-inner">
        <div class="privacy-v2-text">
            <h2 class="privacy-v2-heading">
                {{ $priv['heading'] ?? 'Ihre Privatsphäre' }}
                <span class="privacy-v2-span">{{ $priv['span'] ?? 'Unsere Priorität' }}</span>
            </h2>
            <p class="privacy-v2-desc">{{ $priv['description'] ?? 'Ihre Daten bleiben sicher in Deutschland' }}</p>
            <div class="privacy-v2-badge">
                <span class="privacy-v2-flag">🇩🇪</span>
                <span class="privacy-v2-made">Made in Germany</span>
            </div>
        </div>
        <div class="privacy-v2-image">
            @if(!empty($priv['image']))
                <img src="{{ url('images/upload/'.$priv['image']) }}" alt="Privacy" />
            @else
                <div class="privacy-v2-placeholder">
                    <svg viewBox="0 0 120 160" xmlns="http://www.w3.org/2000/svg" fill="none">
                        <circle cx="60" cy="45" r="30" fill="#d0e8d0"/>
                        <ellipse cx="60" cy="130" rx="45" ry="35" fill="#d0e8d0"/>
                    </svg>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Newsletter Section (COMMAND 8) -->
@php $news = $home['newsletter'] ?? []; @endphp
<section class="newsletter-hero"@if(!empty($news['bg_image'])) style="background-image: url('{{ url('images/upload/'.$news['bg_image']) }}'); background-size: cover; background-position: center;"@endif>
    <div class="newsletter-hero-inner">
        <h2 class="newsletter-hero-heading">{{ $news['heading'] ?? 'Bleib auf dem Laufenden' }}</h2>
        <p class="newsletter-hero-desc">{{ $news['description'] ?? 'Meld dich für unseren Newsletter an und erhalte die neuesten Gesundheitstipps und Angebote direkt in dein Postfach.' }}</p>
        <form class="newsletter-hero-form" action="#" method="post">
            @csrf
            <input class="newsletter-hero-input" type="email" name="email" placeholder="Deine E-Mail-Adresse" required />
            <button class="newsletter-hero-submit" type="submit">Anmelden</button>
        </form>
        <p class="newsletter-hero-legal">{{ $news['legal_text'] ?? 'Mit der Erstellung eines Kontos stimmst du unseren Nutzungsbedingungen und unserer Datenschutzrichtlinie zu.' }}</p>
    </div>
</section>

<!-- Sub-categories Ticker (COMMAND 5a) -->
<section class="subcat-ticker">
    <div class="subcat-track">
        <a href="{{ route('categories') }}" class="subcat-item">💊 Erektile Dysfunktion</a>
        <a href="{{ route('categories') }}" class="subcat-item">🌿 Haarausfall</a>
        <a href="{{ route('categories') }}" class="subcat-item">🌱 Cannabis-Therapie</a>
        <a href="{{ route('categories') }}" class="subcat-item">💪 Testosteron</a>
        <a href="{{ route('categories') }}" class="subcat-item">❤️ Herzgesundheit</a>
        <a href="{{ route('categories') }}" class="subcat-item">😴 Schlafstörungen</a>
        <a href="{{ route('categories') }}" class="subcat-item">🧬 Diabetes</a>
        <a href="{{ route('categories') }}" class="subcat-item">🫁 Atemwegserkrankungen</a>
        <a href="{{ route('categories') }}" class="subcat-item">🧠 Mentale Gesundheit</a>
        <a href="{{ route('categories') }}" class="subcat-item">🌸 Frauengesundheit</a>
        <a href="{{ route('categories') }}" class="subcat-item">⚖️ Gewichtsmanagement</a>
        <a href="{{ route('categories') }}" class="subcat-item">🔬 Labordiagnostik</a>
        {{-- Duplicate for seamless loop --}}
        <a href="{{ route('categories') }}" class="subcat-item">💊 Erektile Dysfunktion</a>
        <a href="{{ route('categories') }}" class="subcat-item">🌿 Haarausfall</a>
        <a href="{{ route('categories') }}" class="subcat-item">🌱 Cannabis-Therapie</a>
        <a href="{{ route('categories') }}" class="subcat-item">💪 Testosteron</a>
        <a href="{{ route('categories') }}" class="subcat-item">❤️ Herzgesundheit</a>
        <a href="{{ route('categories') }}" class="subcat-item">😴 Schlafstörungen</a>
        <a href="{{ route('categories') }}" class="subcat-item">🧬 Diabetes</a>
        <a href="{{ route('categories') }}" class="subcat-item">🫁 Atemwegserkrankungen</a>
        <a href="{{ route('categories') }}" class="subcat-item">🧠 Mentale Gesundheit</a>
        <a href="{{ route('categories') }}" class="subcat-item">🌸 Frauengesundheit</a>
        <a href="{{ route('categories') }}" class="subcat-item">⚖️ Gewichtsmanagement</a>
        <a href="{{ route('categories') }}" class="subcat-item">🔬 Labordiagnostik</a>
    </div>
</section>

<!-- How It Works — drfuxx .online-section (COMMAND 5b) -->
<section class="online-section">
    <h2 class="section-title">{{ $how['title'] ?? '3 einfache Schritte' }} <span>{{ $how['subtitle'] ?? '100 % online' }}</span></h2>
    <p class="section-sub">
        <span style="display:inline-flex;align-items:center;gap:8px;background:#fff;border-radius:50px;padding:6px 16px;font-size:0.82rem;font-weight:600;color:#333;box-shadow:0 2px 12px rgba(0,0,0,0.07);">
            <span style="width:9px;height:9px;background:#22c55e;border-radius:50%;flex-shrink:0;display:inline-block;"></span>
            {{ $how['badge'] ?? '5 Ärzte online' }}
        </span>
    </p>

    {{-- Desktop: tilted deck --}}
    <div class="steps-wrapper steps-desktop-only">
        <svg class="steps-wave" viewBox="0 0 1440 200" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0,100 C180,40 360,160 540,100 C720,40 900,160 1080,100 C1260,40 1380,120 1440,100" stroke="#c4b5fd" stroke-width="2" fill="none"/>
        </svg>
        <div class="steps-grid-tilted">
            @foreach($how['steps'] as $sIdx => $sStep)
            @php
                $sTiltClass = ['tilt-left','tilt-center','tilt-right'][$sIdx] ?? 'tilt-center';
                $sTitleParts = explode('|', $sStep['title'] ?? '', 2);
                $sTitleNormal = trim($sTitleParts[0]);
                $sTitlePurple = isset($sTitleParts[1]) ? trim($sTitleParts[1]) : '';
                $sIconUrl = !empty($sStep['icon']) ? url('images/upload/'.$sStep['icon']) : null;
            @endphp
            <div class="step-card-tilted {{ $sTiltClass }}">
                <div class="step-num-tilted">{{ $sIdx + 1 }}</div>
                <h3>{{ $sTitleNormal }}@if($sTitlePurple)<span>{{ $sTitlePurple }}</span>@endif</h3>
                @if(!empty($sStep['text']))<p>{{ $sStep['text'] }}</p>@endif
                <div class="step-img-tilted">
                    @if($sIconUrl)
                    <div class="step-img-swap">
                        <img src="{{ $sIconUrl }}" class="step-img-default" alt="">
                        <img src="{{ $sIconUrl }}" class="step-img-hover" alt="">
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Mobile: sbook page-turn --}}
    <div class="sbook-wrap steps-mobile-only">
        <div class="sbook" id="stepBook">
            @foreach($how['steps'] as $sIdx => $sStep)
            @php
                $sTitleParts = explode('|', $sStep['title'] ?? '', 2);
                $sTitleNormal = trim($sTitleParts[0]);
                $sTitlePurple = isset($sTitleParts[1]) ? trim($sTitleParts[1]) : '';
                $sIconUrl = !empty($sStep['icon']) ? url('images/upload/'.$sStep['icon']) : null;
            @endphp
            <div class="sbook-page">
                <div class="sbook-num">{{ $sIdx + 1 }}</div>
                <div class="sbook-text">
                    <h3>{{ $sTitleNormal }}@if($sTitlePurple)<span>{{ $sTitlePurple }}</span>@endif</h3>
                    @if(!empty($sStep['text']))<p>{{ $sStep['text'] }}</p>@endif
                </div>
                <div class="sbook-img">
                    @if($sIconUrl)
                        <img src="{{ $sIconUrl }}" alt="">
                    @else
                        <div style="width:100%;height:100%;background:linear-gradient(135deg,#f3f0ff,#ede9fe);display:flex;align-items:center;justify-content:center;">
                            <span style="font-size:3rem;opacity:0.35;font-weight:800;color:#7c3aed;">{{ $sIdx + 1 }}</span>
                        </div>
                    @endif
                </div>
                <div class="sbook-curl"></div>
            </div>
            @endforeach
        </div>
        <div class="sbook-hint" id="sbookHint">
            <svg viewBox="0 0 28 10" fill="none"><path d="M2 5 H26 M20 1 L26 5 L20 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            Wisch um zu blättern
        </div>
        <div class="sbook-dots" id="sbookDots">
            @foreach($how['steps'] as $sIdx => $sStep)
            <span class="sbook-dot {{ $sIdx === 0 ? 'active' : '' }}"></span>
            @endforeach
        </div>
    </div>
</section>

{{-- Trust Banner (COMMAND 5c) --}}
@php $trust_text = $homeSettings['trust_banner']['text'] ?? "Deutschlands größte Online-Klinik – mit echten deutschen Ärzten, rund um die Uhr für dich da"; @endphp
<section class="trust-banner">
    <h2>{{ $trust_text }}</h2>
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
                <p class="small mb-3">{{ __('landing.footer.company_text') }}</p>
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
                <h5 class="text-white mb-3">{{ __('landing.footer.treatments') }}</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('categories') }}" class="text-light text-decoration-none">{{ __('landing.footer.all_treatments') }}</a></li>
                    @foreach($categories->take(5) as $category)
                        <li class="mb-2"><a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-light text-decoration-none">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Company -->
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-3">{{ __('landing.footer.company') }}</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ url('/about-us') }}" class="text-light text-decoration-none">{{ __('landing.footer.about_us') }}</a></li>
                    <li class="mb-2"><a href="#how-it-works" class="text-light text-decoration-none">{{ __('landing.footer.how_it_works') }}</a></li>
                    <li class="mb-2"><a href="{{ url('show-doctors') }}" class="text-light text-decoration-none">{{ __('landing.footer.our_doctors') }}</a></li>
                    <li class="mb-2"><a href="{{ url('our_blogs') }}" class="text-light text-decoration-none">{{ __('landing.footer.blog') }}</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-3">{{ __('landing.footer.support') }}</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">{{ __('landing.footer.help_center') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">{{ __('landing.footer.contact') }}</a></li>
                    <li class="mb-2"><a href="#faq" class="text-light text-decoration-none">{{ __('landing.footer.faq') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">{{ __('landing.footer.shipping_delivery') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="border-top border-secondary pt-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="small mb-0">© {{ date('Y') }} {{ $setting->business_name }} {{ __('landing.footer.all_rights_reserved') }}</p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-3 small">
                        <a href="{{ url('/privacy-policy') }}" class="text-light text-decoration-none">{{ __('landing.footer.privacy') }}</a>
                        <a href="#" class="text-light text-decoration-none">{{ __('landing.footer.terms') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

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
<script>
// ── Step Book — Page-Turn Swiper (COMMAND 5) ──
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        var book = document.getElementById('stepBook');
        if (!book) return;

        var pages   = Array.from(book.querySelectorAll('.sbook-page'));
        var dots    = Array.from(document.querySelectorAll('#sbookDots .sbook-dot'));
        var hint    = document.getElementById('sbookHint');
        var current = 0;
        var busy    = false;
        var turned  = pages.map(function() { return false; });

        function applyLayout() {
            pages.forEach(function(p) {
                p.classList.remove('turning-out', 'turning-back', 'rising-in');
            });
            pages.forEach(function(p, i) {
                if (turned[i]) {
                    p.style.transition = 'none';
                    p.style.transform  = 'rotateY(-100deg)';
                    p.style.zIndex     = '1';
                } else if (i === current) {
                    p.style.transition = '';
                    p.style.transform  = '';
                    p.style.zIndex     = '30';
                } else {
                    var depth = i - current;
                    p.style.transition = 'none';
                    p.style.transform  = 'translateX(' + (depth * 5) + 'px) translateY(' + (depth * 5) + 'px) scale(' + (1 - depth * 0.025) + ')';
                    p.style.zIndex     = String(20 - depth);
                }
            });
        }
        applyLayout();

        function updateDots() {
            dots.forEach(function(d, i) { d.classList.toggle('active', i === current); });
        }

        function goTo(next) {
            if (busy || next === current || next < 0 || next >= pages.length) return;
            busy = true;
            var prev    = current;
            var forward = next > prev;
            if (forward) {
                pages[prev].style.transition = '';
                pages[prev].style.zIndex     = '50';
                pages[prev].classList.add('turning-out');
                pages[next].style.transition = '';
                pages[next].style.zIndex     = '40';
                pages[next].classList.add('rising-in');
                turned[prev] = true;
            } else {
                pages[next].style.transition = 'none';
                pages[next].style.transform  = 'rotateY(-100deg)';
                pages[next].style.zIndex     = '50';
                void pages[next].offsetWidth;
                pages[next].style.transition = '';
                pages[next].classList.add('turning-back');
                turned[next] = false;
            }
            current = next;
            updateDots();
            if (hint) hint.classList.add('hidden');
            setTimeout(function() { applyLayout(); busy = false; }, 660);
        }

        var tx = 0, ty = 0;
        book.addEventListener('touchstart', function(e) {
            tx = e.touches[0].clientX;
            ty = e.touches[0].clientY;
        }, { passive: true });
        book.addEventListener('touchend', function(e) {
            var dx = e.changedTouches[0].clientX - tx;
            var dy = e.changedTouches[0].clientY - ty;
            if (Math.abs(dx) < 35 || Math.abs(dy) > Math.abs(dx)) return;
            if (dx < 0) goTo(current + 1);
            else        goTo(current - 1);
        }, { passive: true });
        book.addEventListener('click', function(e) {
            var rect = book.getBoundingClientRect();
            if (e.clientX > rect.left + rect.width / 2) goTo(current + 1);
            else goTo(current - 1);
        });
    });
})();
</script>
<script>
// ── Doc-grid swipe dots (COMMAND 7a) ──
document.addEventListener('DOMContentLoaded', function() {
    var grid = document.querySelector('.doc-grid');
    var dots = document.querySelectorAll('#docDots .doc-dot');
    if (!grid || !dots.length) return;
    grid.addEventListener('scroll', function() {
        var cards = grid.querySelectorAll('.doc-card');
        if (!cards.length) return;
        var cardW = cards[0].offsetWidth + 12;
        var idx = Math.min(Math.round(grid.scrollLeft / cardW), dots.length - 1);
        dots.forEach(function(d, i) { d.classList.toggle('active', i === idx); });
    }, { passive: true });
});

// ── FAQ accordion (COMMAND 7c) ──
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#faq-section .faq-q').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var item = this.closest('.faq-item');
            var isActive = item.classList.contains('active');
            document.querySelectorAll('#faq-section .faq-item').forEach(function(el) {
                el.classList.remove('active');
                el.querySelector('.faq-icon').innerHTML = '&#8964;';
            });
            if (!isActive) {
                item.classList.add('active');
                item.querySelector('.faq-icon').innerHTML = '&#8963;';
            }
        });
    });
});
</script>
</body>
</html>
