<!DOCTYPE html>
<html lang="en">
<head>
    @php
    $setting = App\Models\Setting::first();

    // Helper to get background-removed version of an image if it exists
    if (!function_exists('getLandingImage')) {
        function getLandingImage($image) {
            if (empty($image)) return '';
            
            $uploadFolder = 'images/upload/';
            
            // Map of known background removed images
            $replacements = [
                '69d607d8d2c54.png' => '69d607d8d2c54-Photoroom.png',
                '69d60c814e779.png' => '69d60c814e779-Photoroom.png',
                '69d60c814f564.png' => '69d60c814f564-Photoroom.png',
                '69d60d3acc448.jpg' => '69d60d3acc448-Photoroom.png',
                '69d60966d8131.png' => 'ChatGPT Image Apr 10, 2026, 08_09_01 PM.png',
                '69d60c814dd6d.png' => 'ChatGPT Image Apr 10, 2026, 08_07_12 PM.png',
            ];

            if (array_key_exists($image, $replacements)) {
                return url($uploadFolder . $replacements[$image]);
            }
            
            return url($uploadFolder . $image);
        }
    }
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
    <link href="{{asset('css/home_target_match.css')}}?v={{ time() }}" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bold Differentiation typography -->
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Slick Carousel CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6, .display-4, .display-5 { font-family: 'Clash Display', sans-serif; }
        h1 span, h2 span, h3 span, h4 span, h5 span, h6 span, .display-4 span, .display-5 span { font-family: inherit; }
    </style>
</head>
<body class="home-target">
    @include('layout.partials.skeleton_loader')
<!-- Navigation -->
@include('layout.partials.navbar_website')

<!-- Hero Section -->
@php
    $homeSettings = json_decode($setting->website_home_settings, true) ?: [];
    $hero = $homeSettings['hero'] ?? [];
@endphp

@php
    $heroBgColor = $hero['bg_color'] ?? '#f5f3ff';
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
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: #ffffff !important;
        padding: 11px 24px !important;
        border-radius: 50px !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.4) !important;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
        position: relative;
        overflow: hidden;
    }
    
    .btn-hero-premium:hover {
        transform: translateY(-2px) scale(1.02) !important;
        background-color: var(--primary-hover) !important;
        border-color: var(--primary-hover) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 24px rgba(124, 58, 237, 0.3) !important;
    }

    /* Trust Icons */
    .trust-icon-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 6px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        font-size: 0.75rem;
        background: #fff;
    }

    /* Categories Marquee */
    .categories-marquee-wrapper {
        width: 100%;
        overflow: hidden;
        position: relative;
        z-index: 10;
        mask-image: linear-gradient(to right, transparent 0%, black 15%, black 85%, transparent 100%);
        -webkit-mask-image: linear-gradient(to right, transparent 0%, black 15%, black 85%, transparent 100%);
    }
    .categories-marquee-container {
        display: flex;
        width: 100%;
    }
    .categories-marquee-content {
        display: flex;
        gap: 15px;
        animation: marquee-scroll 100s linear infinite;
        padding: 10px 0;
        width: max-content;
    }
    @keyframes marquee-scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .category-pill {
        background: #ffffff;
        border: 1px solid rgba(138, 72, 255, 0.1);
        border-radius: 50px;
        padding: 10px 24px;
        color: #1a1a1a;
        font-weight: 600;
        font-size: 0.95rem;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-block;
    }
    .category-pill-link:hover .category-pill {
        background-color: var(--primary-color);
        color: #ffffff;
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(124, 58, 237, 0.25);
        border-color: var(--primary-color);
    }
    .category-pill-link {
        text-decoration: none !important;
    }

    /* Pause animation on hover */
    .categories-marquee-wrapper:hover .categories-marquee-content {
        animation-play-state: paused;
    }

    @media (max-width: 768px) {
        .hero-fuxx {
            min-height: auto !important;
            padding-top: 30px !important;
            padding-bottom: 40px !important;
        }
        .hero-main-content h1 {
            font-size: 2rem !important;
            margin-bottom: 15px !important;
        }
        .hero-main-content .lead {
            font-size: 1rem !important;
            margin-bottom: 30px !important;
            padding: 0 10px;
        }
        .hero-main-content .btn-hero-premium {
            width: 100%;
            max-width: 320px;
            padding: 12px 20px !important;
            font-size: 1.1rem !important;
        }
        .hero-main-content {
            margin-top: 30px !important;
        }
    }
</style>

<section class="hero-fuxx position-relative overflow-hidden" style="min-height: 80vh; padding-top: 50px; padding-bottom: 0;">
    
    <!-- Center Foreground Product Image -->
    @if(!empty($hero['image']))
        <div class="hero-bg-wrapper position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1; pointer-events: none; width: 100%; max-width: 800px;">
            <img src="{{ getLandingImage($hero['image']) }}" alt="Background" class="img-fluid w-100" style="mask-image: linear-gradient(to top, transparent 0%, black 50%); -webkit-mask-image: linear-gradient(to top, transparent 0%, black 50%);">
        </div>
    @endif

    <!-- Small Decorative Hero Image (like the plant/product on drfuxx) centered with overlay -->
    @if(!empty($hero['bg_image']))
        <!-- Image sits at z-index:1 (lowest) -->
        <div class="position-absolute" style="top:50%;left:50%;transform:translate(-50%,-50%);z-index:1;pointer-events:none;max-width:380px;width:100%;">
            <img src="{{ getLandingImage($hero['bg_image']) }}" alt="" class="img-fluid" style="mask-image: linear-gradient(to top, transparent 0%, black 40%); -webkit-mask-image: linear-gradient(to top, transparent 0%, black 40%); opacity: 0.9;" loading="lazy">
        </div>
        <!-- Color overlay #f3ecfe sits at z-index:2, above image -->
        <div class="position-absolute" style="top:0;left:0;right:0;bottom:0;background-color:#f3ecfe;opacity:0.78;z-index:2;pointer-events:none;"></div>
    @endif

    <div class="container position-relative" style="z-index: 3;">
        
        <!-- Top Category Strip -->
        @php
            $cmsQuickLinks = $hero['quick_links'] ?? [];
            $heroCategories = [
                [
                    'title' => 'Med. Cannabis',
                    'desc' => 'Diskrete Beratung & Rezept online',
                    'badge' => 'NEU',
                    'url' => !empty($cmsQuickLinks[0]['url']) && $cmsQuickLinks[0]['url'] !== '#' ? $cmsQuickLinks[0]['url'] : route('categories'),
                    'image' => !empty($cmsQuickLinks[0]['image']) ? url('images/upload/'.$cmsQuickLinks[0]['image']) : '',
                    'alt' => 'Cannabis',
                    'active' => false,
                ],
                [
                    'title' => 'Erektions&shy;st&ouml;rungen',
                    'desc' => 'Vertraulich & ohne Wartezeit',
                    'badge' => '',
                    'url' => !empty($cmsQuickLinks[1]['url']) && $cmsQuickLinks[1]['url'] !== '#' ? $cmsQuickLinks[1]['url'] : route('erektionsstoerungen'),
                    'image' => !empty($cmsQuickLinks[1]['image']) ? url('images/upload/'.$cmsQuickLinks[1]['image']) : '',
                    'alt' => 'ED',
                    'active' => false,
                ],
                [
                    'title' => 'Testosteron',
                    'desc' => 'Fertige Injektion - direkt einsatzbereit',
                    'badge' => '',
                    'url' => !empty($cmsQuickLinks[2]['url']) && $cmsQuickLinks[2]['url'] !== '#' ? $cmsQuickLinks[2]['url'] : route('categories'),
                    'image' => !empty($cmsQuickLinks[2]['image']) ? url('images/upload/'.$cmsQuickLinks[2]['image']) : '',
                    'alt' => 'Testosteron',
                    'active' => false,
                ],
                [
                    'title' => 'Abnehmen',
                    'desc' => 'Abnehmspritze - ärztlich begleitet',
                    'badge' => '',
                    'url' => !empty($cmsQuickLinks[3]['url']) && $cmsQuickLinks[3]['url'] !== '#' ? $cmsQuickLinks[3]['url'] : route('categories'),
                    'image' => !empty($cmsQuickLinks[3]['image']) ? url('images/upload/'.$cmsQuickLinks[3]['image']) : '',
                    'alt' => 'Abnehmen',
                    'active' => false,
                ],
            ];
        @endphp
        <section class="desktop-only-cats">
            <div class="desktop-cats-grid">
                @foreach($heroCategories as $category)
                    <a href="{{ $category['url'] }}" class="tcat{{ $category['active'] ? ' tcat-active' : '' }}" data-bg="{{ $category['image'] }}">
                        <div class="tcat-bg" @if(!empty($category['image'])) style="background-image: url('{{ $category['image'] }}');" @endif></div>
                        <div class="tcat-overlay"></div>
                        <div class="tcat-content">
                            <span class="tcat-title">
                                {!! $category['title'] !!}
                                @if(!empty($category['badge']))
                                    <span class="tcat-badge">{{ $category['badge'] }}</span>
                                @endif
                            </span>
                            <span class="tcat-desc">{{ $category['desc'] }}</span>
                        </div>
                        <div class="tcat-img">
                            @if(!empty($category['image']))
                                <img src="{{ $category['image'] }}" alt="{{ $category['alt'] }}" width="120" height="120">
                            @elseif(!empty($cmsQuickLinks[$loop->index]['icon_class']))
                                <i class="{{ $cmsQuickLinks[$loop->index]['icon_class'] }}"></i>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- Main Content -->
        <div class="hero-main-content text-center mx-auto" style="max-width: 800px; margin-top: 60px;">
            <style>
                .hero-ticker-badge {
                    display: inline-block;
                    position: relative;
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

                    if (!tickerEl || !keywords.length) return;

                    tickerEl.textContent = keywords[0];
                    tickerEl.style.transition = 'opacity 0.4s ease, transform 0.4s ease';

                    function rotateKeyword() {
                        tickerEl.style.opacity = '0';
                        tickerEl.style.transform = 'translateY(10px)';

                        setTimeout(() => {
                            keywordIndex = (keywordIndex + 1) % keywords.length;
                            tickerEl.textContent = keywords[keywordIndex];
                            tickerEl.style.opacity = '1';
                            tickerEl.style.transform = 'translateY(0)';
                        }, 400);
                    }

                    setInterval(rotateKeyword, 3000);
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
            <div class="d-flex flex-wrap justify-content-center gap-4 mb-4 trust-motion">
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
            <!-- <div class="rating-section mb-3 d-flex flex-wrap align-items-center justify-content-center gap-2 mt-4">
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
            </div> -->

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

    <!-- Categories Marquee -->
    @php
        $marqueeItems = $how['hero_marquee'] ?? [];
        // If empty, fallback to categories for a good first impression 
        // but the user can now override this completely in settings.
        if (empty($marqueeItems) && isset($categories)) {
            foreach($categories as $cat) {
                $marqueeItems[] = ['text' => $cat->name, 'url' => route('category.detail', $cat->id)];
            }
        }
    @endphp

    @if(!empty($marqueeItems))
    <div class="categories-marquee-wrapper pt-4 pb-2 mt-auto">
        <div class="categories-marquee-container">
            <div class="categories-marquee-content">
                @foreach($marqueeItems as $item)
                    <a href="{{ $item['url'] ?? '#' }}" class="category-pill-link">
                        <span class="category-pill">{{ $item['text'] }}</span>
                    </a>
                @endforeach
                {{-- Duplicate for seamless loop --}}
                @foreach($marqueeItems as $item)
                    <a href="{{ $item['url'] ?? '#' }}" class="category-pill-link">
                        <span class="category-pill">{{ $item['text'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</section>

<main class="home-flow">

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
             'text' => ">bi-box-seam:Online Express:1–2 Werktage\n>bi-house:Apotheke vor Ort:Abholung in Partnerapotheken",
             'icon' => ''],
        ];
    }
@endphp

<!-- How It Works Section -->
<section class="home-step-story pb-5 pt-3 position-relative overflow-hidden" style="background: linear-gradient(180deg, #cdc7e8 0%, #c8c2e4 45%, #cdc7e8 100%); min-height: 560px;">

    <!-- Wavy background line SVG -->
    <svg class="position-absolute w-100" style="bottom: 60px; left: 0; opacity: 0.22; pointer-events:none;" viewBox="0 0 1440 120" preserveAspectRatio="none">
        <path d="M0,60 C180,20 360,100 540,60 C720,20 900,100 1080,60 C1260,20 1380,80 1440,60" stroke="#b3a4ea" stroke-width="3" fill="none"/>
    </svg>

    <style>
        .hiw-title  { font-size: 2.3rem; font-weight: 700; color: #15141c; letter-spacing: -0.4px; font-family: 'DM Serif Display', serif; }
        .hiw-sub    { font-size: 2.1rem; font-weight: 700; color: #6a40ee; letter-spacing: -0.4px; font-style: normal; font-family: 'DM Serif Display', serif; }
        .hiw-badge  { display: inline-flex; align-items: center; gap: 8px; background: #d9d1ef; border: 1px solid #b8a6e8; border-radius: 999px; padding: 7px 16px; font-size: 0.82rem; font-weight: 700; color: #6548cf; box-shadow: 0 2px 10px rgba(95,78,156,0.12); }
        .hiw-dot    { width: 8px; height: 8px; background: #6a40ee; border-radius: 50%; flex-shrink: 0; animation: hiwDotPulse 2s ease-in-out infinite; }
        @keyframes hiwDotPulse { 0%,100%{box-shadow:0 0 0 0 rgba(106,64,238,0.45)} 50%{box-shadow:0 0 0 6px rgba(106,64,238,0)} }

        /* Deck layout — exact reference values */
        .hiw-deck   { display: flex; justify-content: center; align-items: flex-end; gap: 0; padding: 40px 0 20px; }
        .step-card-tilted {
            background: #efedf4;
            border: 1px solid rgba(214, 206, 231, 0.9);
            border-radius: 20px;
            padding: 28px 24px 20px;
            text-align: left;
            position: relative;
            overflow: hidden;
            width: 280px;
            flex-shrink: 0;
            min-height: 360px;
            box-shadow: 0 12px 30px rgba(82, 69, 124, 0.14), 0 1px 4px rgba(0,0,0,0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease, z-index 0s;
        }
        .step-card-tilted:hover {
            box-shadow: 0 18px 48px rgba(77, 61, 129, 0.24);
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

        .step-num-tilted { width: 38px; height: 38px; background: #6a40ee; color: #fff; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1rem; margin-bottom: 16px; box-shadow: 0 6px 18px rgba(106,64,238,0.34); }
        .step-card-tilted h3 { font-family: 'DM Serif Display', serif; font-size: 1.3rem; font-weight: 600; color: #1a1a1a; line-height: 1.35; margin-bottom: 6px; }
        .step-card-tilted h3 span { color: #6a40ee; display: block; }
        .step-card-tilted > p { color: #666; font-size: 0.88rem; line-height: 1.6; margin-bottom: 16px; }
        .hiw-sub-items { 
            margin-top: 16px; 
            display: flex; 
            flex-wrap: wrap; 
            gap: 10px; 
            justify-content: center; 
        }
        .hiw-sub-item  { 
            flex: 1; 
            min-width: 100px;
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            text-align: center;
            padding: 16px 8px;
            border: 1px solid #e7e2f3; 
            border-radius: 16px; 
            margin-bottom: 0px; 
            background: #f5f2fb;
            box-shadow: 0 4px 12px rgba(79, 63, 125, 0.08);
            transition: transform 0.2s ease;
        }
        .hiw-sub-item:hover { transform: translateY(-3px); border-color: #8c72e2; }
        .hiw-sub-item i { font-size: 1.5rem; margin-bottom: 10px; flex-shrink: 0; }
        .hiw-sub-item i.bi-box-seam { color: #6a40ee; }
        .hiw-sub-item i.bi-house { color: #e24b4a; }
        .hiw-sub-item-label { font-weight: 700; font-size: 0.78rem; color: #111; line-height: 1.2; margin-bottom: 4px; }
        .hiw-sub-item-desc  { font-size: 0.65rem; color: #777; line-height: 1.3; }
        .hiw-card-photo { display: block; width: 100%; margin-top: 20px; object-fit: contain; max-height: 190px; }

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
                
                // Use pre-processed sub_items from controller
                $subItems = $step['sub_items'] ?? [];
                
                // Fallback: If sub_items is empty, check if we can parse it from 'text' (legacy support)
                if (empty($subItems) && !empty($step['text'])) {
                    foreach(explode("\n", $step['text']) as $line) {
                        $line = trim($line);
                        if(str_starts_with($line, '>')) {
                            $parts = explode(':', ltrim($line, '>'), 3);
                            if (count($parts) === 3) {
                                $subItems[] = ['icon' => trim($parts[0]), 'label' => trim($parts[1]), 'desc' => trim($parts[2])];
                            } else {
                                $subItems[] = ['icon' => 'bi-check2-circle', 'label' => trim($parts[0]), 'desc' => isset($parts[1]) ? trim($parts[1]) : ''];
                            }
                        }
                    }
                }
            @endphp
            <div class="step-card-tilted {{ $tiltClass }}">
                <div class="step-num-tilted">{{ $i + 1 }}</div>
                <h3>{{ $titleNormal }}@if($titlePurple)<span>{{ $titlePurple }}</span>@endif
                </h3>
                
                @if(!empty($subItems))
                <div class="hiw-sub-items">
                    @foreach($subItems as $sub)
                    <div class="hiw-sub-item">
                        <i class="bi {{ $sub['icon'] ?? 'bi-check2-circle' }}"></i>
                        <div>
                            <div class="hiw-sub-item-label">{{ $sub['label'] ?? '' }}</div>
                            @if(!empty($sub['desc']))<div class="hiw-sub-item-desc">{{ $sub['desc'] }}</div>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @elseif(!empty($step['text']) && strpos($step['text'], '>') === false)
                    <p class="mt-2" style="font-size:0.83rem;color:#666;">{{ trim($step['text']) }}</p>
                @endif
                
                @if(!empty($step['icon']))
                    <div class="hiw-card-photo-wrap">
                        <img src="{{ getLandingImage($step['icon']) }}" class="hiw-card-photo" alt="" loading="lazy">
                    </div>
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

<!-- Intermission Banner -->
@php
    $inter = $homeSettings['intermission_banner'] ?? [];
    $bannerText = $inter['text'] ?? 'Deutschlands größte Online Klinik – mit echten deutschen Ärzten, rund um die Uhr für dich da';
    $bannerBg = $inter['bg_color'] ?? '#8a48ff';
    $bannerTextCol = $inter['text_color'] ?? '#ffffff';
@endphp
<section class="py-5 home-intermission-banner home-story-intermission" style="background-color: {{ $bannerBg }}; color: {{ $bannerTextCol }};">
    <div class="container text-center py-4">
        <h2 class="fw-bold mb-0" style="font-size: clamp(1.4rem, 4vw, 2rem); line-height: 1.4; color: inherit;">
            {{ $bannerText }}
        </h2>
    </div>
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
<div class="premium-section-outer home-story-cannabis">
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
            
            <h2 class="cbs-heading">{{ $rTitleNormal }}@if($rTitleGreen)<br><span class="cbs-green">{{ $rTitleGreen }}</span>@endif</h2>

            <!-- Center hero image with buttons overlaid -->
            <div class="cbs-hero-img-wrap">
                <img src="{{ !empty($relief['image']) ? getLandingImage($relief['image']) : 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=800&q=80' }}" alt="" class="cbs-hero-img" loading="lazy">

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
                    <img src="{{ getLandingImage($card['icon']) }}" alt="" class="cbs-card-img" />
                </div>
                @endif
            </div>
            @endif
            @endforeach
        </div>
    </section>
</div>

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

    $edHeroImage = !empty($ed['hero_image']) ? getLandingImage($ed['hero_image']) : 'https://images.unsplash.com/photo-1511130558040-bb3396b42b79?q=80&w=1000&auto=format&fit=crop';
    $edLandingUrl = route('erektionsstoerungen');
    
    $edBtn1Text = !empty($ed['btn1_text']) ? $ed['btn1_text'] : 'Meine Behandlung finden';
    $edBtn1Url = !empty($ed['btn1_url']) && $ed['btn1_url'] !== '#' ? $ed['btn1_url'] : $edLandingUrl;
    $edBtn2Text = !empty($ed['btn2_text']) ? $ed['btn2_text'] : 'Meine kostenlose Beratung starten';
    $edBtn2Url = !empty($ed['btn2_url']) && $ed['btn2_url'] !== '#' ? $ed['btn2_url'] : $edLandingUrl;

    $largeCard = $ed['large_card'] ?? [];
    $largeTitle = !empty($largeCard['title']) ? str_replace('|', '<br>', $largeCard['title']) : 'Es kommt häufiger vor, als Sie denken.';
    $largeBtnText = !empty($largeCard['btn_text']) ? $largeCard['btn_text'] : 'Mehr über Ursachen erfahren';
    $largeBtnUrl = !empty($largeCard['btn_url']) && $largeCard['btn_url'] !== '#' ? $largeCard['btn_url'] : $edLandingUrl;
    $largeImage = !empty($largeCard['image']) ? getLandingImage($largeCard['image']) : 'https://images.unsplash.com/photo-1621348123733-47a824707db9?q=80&w=1000&auto=format&fit=crop';

    $r1 = $ed['right_card_1'] ?? [];
    $r1Title = !empty($r1['title']) ? str_replace('|', '<br>', $r1['title']) : 'Wenn Leistung zu Druck wird';
    $r1BtnText = !empty($r1['btn_text']) ? $r1['btn_text'] : 'Verstehen, wie ED funktioniert';
    $r1BtnUrl = !empty($r1['btn_url']) && $r1['btn_url'] !== '#' ? $r1['btn_url'] : $edLandingUrl;
    $r1Image = !empty($r1['image']) ? url('images/upload/'.$r1['image']) : 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?q=80&w=1000&auto=format&fit=crop';

    $r2 = $ed['right_card_2'] ?? [];
    $r2Title = !empty($r2['title']) ? str_replace('|', '<br>', $r2['title']) : 'Professionelle Hilfe, die diskret wirkt.';
    $r2BtnText = !empty($r2['btn_text']) ? $r2['btn_text'] : 'Mein Rezept erhalten';
    $r2BtnUrl = !empty($r2['btn_url']) && $r2['btn_url'] !== '#' ? $r2['btn_url'] : $edLandingUrl;
    $r2Image = !empty($r2['image']) ? url('images/upload/'.$r2['image']) : 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?q=80&w=1000&auto=format&fit=crop';
@endphp
<div class="premium-section-outer home-story-ed">
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
</div>

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

    $tCards = $testo['cards'] ?? [];
    if (empty($tCards)) {
        $tCards = [
            [
                'title' => $testo['left_card']['title'] ?? 'Energie und Antrieb zurückgewinnen',
                'btn_text' => $testo['left_card']['btn_text'] ?? 'Mehr erfahren',
                'btn_url' => $testo['left_card']['btn_url'] ?? route('categories'),
                'icon' => $testo['left_card']['image'] ?? ''
            ],
            [
                'title' => $testo['right_card']['title'] ?? 'Fertige Injektion — einfach und sicher',
                'btn_text' => $testo['right_card']['btn_text'] ?? 'Behandlung starten',
                'btn_url' => $testo['right_card']['btn_url'] ?? route('categories'),
                'icon' => $testo['right_card']['image'] ?? ''
            ]
        ];
    }
@endphp

<div class="premium-section-outer home-story-testo">
    <section class="testo-banner-section">
        <div class="testo-inner">
            @if(!empty($testoPill))
                <span class="testo-pill">{{ $testoPill }}</span>
            @endif
            
            <h2 class="testo-heading">{!! $testoHeading !!}</h2>

            <div class="testo-hero-img-wrap">
                <img src="{{ $testoHeroImage }}" alt="Testosteron" class="testo-hero-img">

                <div class="testo-btns-overlay">
                    @if(!empty($testoBtn1Text))
                        <a href="{{ $testoBtn1Url }}" class="testo-btn testo-btn-outline">{{ $testoBtn1Text }}</a>
                    @endif
                    @if(!empty($testoBtn2Text))
                        <a href="{{ $testoBtn2Url }}" class="testo-btn testo-btn-filled">{{ $testoBtn2Text }}</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="testo-cards">
            @foreach($tCards as $card)
            @if(!empty($card['title']))
            <div class="testo-card">
                <div class="testo-card-text" @if(empty($card['icon'])) style="width:100%;padding-right:0;" @endif>
                    <h3>{!! str_replace('|', '<br>', $card['title']) !!}</h3>
                    @if(!empty($card['btn_text']))
                        <a href="{{ $card['btn_url'] ?? '#' }}" class="testo-card-btn">{{ $card['btn_text'] }}</a>
                    @endif
                </div>
                @if(!empty($card['icon']))
                <div class="testo-card-img-wrap">
                    <img src="{{ url('images/upload/'.$card['icon']) }}" alt="" class="testo-card-img" />
                </div>
                @endif
            </div>
            @endif
            @endforeach
        </div>
    </section>
</div>

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

    $wlHeroImage = !empty($wl['hero_image']) ? getLandingImage($wl['hero_image']) : 'https://images.unsplash.com/photo-1518310383802-640c2de311b2?w=800&q=80';
    
    $wlBtn1Text = !empty($wl['btn1_text']) ? $wl['btn1_text'] : 'Mehr erfahren';
    $wlBtn1Url = !empty($wl['btn1_url']) ? $wl['btn1_url'] : route('categories');
    $wlBtn2Text = !empty($wl['btn2_text']) ? $wl['btn2_text'] : 'Beratung starten';
    $wlBtn2Url = !empty($wl['btn2_url']) ? $wl['btn2_url'] : route('categories');

    $wlLeft = $wl['left_card'] ?? [];
    $wlLeftTitle = !empty($wlLeft['title']) ? str_replace('|', '<br>', $wlLeft['title']) : 'Abnehmspritze — einfach und effektiv';
    $wlLeftBtnText = !empty($wlLeft['btn_text']) ? $wlLeft['btn_text'] : 'Jetzt informieren';
    $wlLeftBtnUrl = !empty($wlLeft['btn_url']) ? $wlLeft['btn_url'] : route('categories');
    $wlLeftImage = !empty($wlLeft['image']) ? getLandingImage($wlLeft['image']) : 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?w=800&q=80';

    $wlRight = $wl['right_card'] ?? [];
    $wlRightTitle = !empty($wlRight['title']) ? str_replace('|', '<br>', $wlRight['title']) : 'Ärztlich begleitet — sicher zum Wunschgewicht';
    $wlRightBtnText = !empty($wlRight['btn_text']) ? $wlRight['btn_text'] : 'Behandlung starten';
    $wlRightBtnUrl = !empty($wlRight['btn_url']) ? $wlRight['btn_url'] : route('categories');
    $wlRightImage = !empty($wlRight['image']) ? getLandingImage($wlRight['image']) : 'https://images.unsplash.com/photo-1576091160550-2173ff9e5ee5?w=800&q=80';
@endphp
<div class="premium-section-outer home-story-weight">
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
</div>

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
<section class="advisors-section home-story-advisors">
    <div class="advisors-container">
        <h2 class="advisors-heading">{{ $advHeading }}</h2>
        <div class="advisors-grid">
            @foreach($advSlots as $slot)
                @if(!empty($slot['name']) || !empty($slot['image']))
                    <div class="advisor-card">
                        <div class="advisor-img-wrap">
                            @if(!empty($slot['image']))
                                <img src="{{ getLandingImage($slot['image']) }}" alt="{{ $slot['name'] ?? 'Advisor' }}" class="advisor-img" />
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
<section class="stats-section home-story-stats">
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
    $compCenterImage = !empty($compData['center_image']) ? getLandingImage($compData['center_image']) : 'https://images.unsplash.com/photo-1512428559087-560fa5ceab42?w=800&q=80'; /* default phone placeholder */

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
<section class="comparison-section home-story-comparison">
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
<section class="faq-section home-story-faq">
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

<section class="press-logos home-story-press">
    <div class="press-logos-inner">
        <span class="press-label">{{ $mediaData['heading'] ?? 'Bekannt aus' }}</span>
        <div class="press-track">
            @foreach($mediaItems as $item)
                <span class="press-logo-item">{{ $item }}</span>
            @endforeach
        </div>
    </div>
</section>

<section class="mid-cta home-story-cta">
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

<section class="privacy-v2 home-story-privacy">
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
    $nlBgDefault = url('images/upload/69d60e2caf968.png');
    $nlBg = $nlData['bg_image'] ?? '';
    if (empty($nlBg) || str_contains($nlBg, 'WhatsApp%20Image%202026-03-17')) {
        $nlBg = $nlBgDefault;
    }
@endphp

<section class="newsletter-hero home-story-newsletter">
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

 </main>



@include('layout.partials.footer')

<!-- jQuery (Required for Slick) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Slick Carousel JS -->
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
    $(document).ready(function(){
        const $quickLinksSlider = $('#quickLinksSlider');

        function syncQuickLinksSlider() {
            if (!$quickLinksSlider.length) return;

            if (window.innerWidth <= 767) {
                if (!$quickLinksSlider.hasClass('slick-initialized')) {
                    $quickLinksSlider.slick({
                        infinite: true,
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        autoplay: false,
                        dots: false,
                        arrows: false,
                        responsive: [
                            {
                                breakpoint: 576,
                                settings: {
                                    slidesToShow: 1
                                }
                            }
                        ]
                    });
                }
            } else if ($quickLinksSlider.hasClass('slick-initialized')) {
                $quickLinksSlider.slick('unslick');
            }
        }

        syncQuickLinksSlider();
        $(window).on('resize orientationchange', syncQuickLinksSlider);
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!document.body.classList.contains('home-target')) return;

    const header = document.querySelector('.main-header');
    const trustMotion = document.querySelector('.trust-motion');
    const heroRating = document.querySelector('.rating-section');
    const heroTitle = document.querySelector('.hero-main-content h1');

    let scrollTick = false;
    function updateHeaderState() {
        if (!scrollTick) {
            window.requestAnimationFrame(() => {
                if (header) {
                    header.classList.toggle('is-scrolled', window.scrollY > 10);
                }

                const sY = window.scrollY;
                if (sY < 600) {
                    if (trustMotion) trustMotion.style.transform = 'translate3d(0, ' + (sY * 0.08) + 'px, 0)';
                    if (heroRating) heroRating.style.transform = 'translate3d(0, ' + (sY * 0.05) + 'px, 0)';
                }
                scrollTick = false;
            });
            scrollTick = true;
        }
    }

    updateHeaderState();
    window.addEventListener('scroll', updateHeaderState, { passive: true });

    if (heroTitle && !heroTitle.querySelector('.shimmer-text') && /dr\.fuxx/i.test(heroTitle.innerHTML)) {
        heroTitle.innerHTML = heroTitle.innerHTML.replace(/dr\.fuxx/ig, '<span class="shimmer-text">$&</span>');
    }

    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const href = anchor.getAttribute('href');
            if (!href || href === '#') return;
            const target = document.querySelector(href);
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    const motionGroups = [
        { selector: '.tcat', className: 'motion-scale-in', stagger: 0.08 },
        { selector: '.home-story-cannabis .cbs-inner, .home-story-testo .testo-inner, .home-story-comparison .comp-header, .home-story-comparison .comp-board, .home-story-comparison .comp-cta-wrap, .home-story-privacy .row.align-items-center, .home-story-newsletter .nl-hero-content', className: 'motion-slide-left', stagger: 0.06 },
        { selector: '.home-story-ed .ed-inner, .home-story-weight .wl-inner, .home-story-faq .faq-container, .home-story-advisors .advisors-container, .home-story-stats .stats-container, .home-story-press .press-logos-inner, .home-story-cta .mid-cta-inner', className: 'motion-slide-right', stagger: 0.06 },
        { selector: '.cbs-card, .ed-card, .testo-card, .wl-card, .advisor-card, .stats-card, .faq-item, .privacy-feature-item, .priv-pill', className: 'motion-reveal', stagger: 0.08 },
        { selector: '.hiw-title, .hiw-sub, .hiw-badge, .advisors-heading, .stats-heading, .comp-pill, .comp-heading, .comp-sub, .faq-heading, .faq-sub, .privacy-label, .privacy-heading, .privacy-intro, .press-label, .mid-cta h2, .mid-cta-note, .nl-hero-content h2, .nl-hero-content > p, .nl-hero-legal', className: 'motion-title', stagger: 0.08 }
    ];

    const motionElements = [];

    motionGroups.forEach(function (group) {
        document.querySelectorAll(group.selector).forEach(function (el, index) {
            el.classList.add(group.className);
            el.style.transitionDelay = (group.stagger || 0) * (index % 5) + 's';
            motionElements.push(el);
        });
    });

    if (!('IntersectionObserver' in window)) {
        motionElements.forEach(function (el) {
            el.classList.add('motion-visible');
        });
    } else {
        const motionObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('motion-visible');
                    motionObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.06, rootMargin: '0px 0px -12% 0px' });

        motionElements.forEach(function (el) {
            motionObserver.observe(el);
        });
    }

    document.querySelectorAll('.header-btn, .btn-hero-premium, .cbs-btn-filled, .ed-btn-filled, .testo-btn-filled, .wl-btn-filled, .comp-btn, .btn-cta-lg').forEach(function (el) {
        el.classList.add('motion-bounce');
    });

    document.querySelectorAll('.cbs-card, .ed-card-small, .testo-card, .wl-card').forEach(function (el) {
        el.classList.add('motion-tilt');
    });

    document.querySelectorAll('.advisor-card').forEach(function (el) {
        el.classList.add('motion-glow');
    });

    document.querySelectorAll('.footer-col-v2 a').forEach(function (el) {
        el.classList.add('motion-underline');
    });

    document.querySelectorAll('.live-viewers .spinner-grow, .hiw-dot').forEach(function (el) {
        el.classList.add('motion-pulse-ring');
    });

    document.querySelectorAll('.stats-num').forEach(function (el) {
        const numericText = el.textContent.replace(/[^0-9]/g, '');
        const target = parseInt(numericText, 10);
        if (!target) return;

        let counted = false;
        el.classList.add('motion-count-up');

        const countObserver = new IntersectionObserver(function (entries) {
            if (!entries[0].isIntersecting || counted) return;
            counted = true;

            const duration = 1500;
            let startTime = null;

            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                const progress = Math.min((timestamp - startTime) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target).toLocaleString('de-DE');
                if (progress < 1) requestAnimationFrame(step);
            }

            requestAnimationFrame(step);
            countObserver.unobserve(el);
        }, { threshold: 0.5 });

        countObserver.observe(el);
    });
});
</script>
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
    let dragTick = false;
    window.addEventListener('mousemove', function(e) {
        if (!dragging) return;
        if (!dragTick) {
            window.requestAnimationFrame(() => {
                var delta = dragStartX - e.clientX;
                var raw = Math.min(Math.max(dragOffset + delta, 0), maxIdx() * cardStep());
                track.style.transform = 'translate3d(-' + raw + 'px, 0, 0)';
                dragTick = false;
            });
            dragTick = true;
        }
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
