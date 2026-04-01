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
    <link href="{{ asset('css/drfuxx-styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/drfuxx-mobile.css') }}" rel="stylesheet">
    <link href="{{ asset('css/drfuxx-landing.css') }}" rel="stylesheet">
    <style>
      :root {
        --color-primary: {{ $setting->website_color ?? '#7c3aed' }};
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

        /* ── Landing-page navbar overrides ── */
        nav.navbar {
            background-color: rgba(250, 248, 255, 0.95) !important;
            border-bottom: 1px solid rgba(0,0,0,0.06) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding-top: 10px;
            padding-bottom: 10px;
        }
        nav.navbar .navbar-brand img { height: 48px !important; }
        nav.navbar .nav-link {
            color: #1a1a1a !important;
            font-weight: 500;
            font-size: 0.92rem;
        }
        nav.navbar .nav-link:hover { color: var(--color-primary) !important; }

        /* Replace teal CTA buttons with drfuxx purple */
        nav.navbar a[style*="00bda6"],
        nav.navbar a[style*="00bda6"]:visited {
            color: var(--color-primary) !important;
            border-color: var(--color-primary) !important;
        }
        /* Filled Start treatment button */
        nav.navbar .d-flex a:last-child {
            background-color: var(--color-primary) !important;
            color: #fff !important;
            border-color: var(--color-primary) !important;
            border-radius: 999px !important;
        }
        nav.navbar .d-flex a:last-child:hover {
            background-color: var(--color-primary-dark, #6d28d9) !important;
        }
        /* Sign in button */
        nav.navbar .d-flex a:not(:last-child)[href*="login"],
        nav.navbar .d-flex a:not(:last-child)[href*="login"]:visited {
            color: var(--color-primary) !important;
            border-color: var(--color-primary) !important;
            border-radius: 999px !important;
        }

        /* ── Bootstrap reset fixes for drfuxx sections ── */
        /* Prevent Bootstrap from making all images block/max-width:100% inside sections */
        .ticker-bar img, .tcat img, .mhn-cat-img,
        .step-img-tilted img, .sbook-img img,
        .cbs-card-img, .ed-card-img, .ha-card-img,
        .feat-bg-img, .prv2-img-wrap img,
        .doc-card img, .advisory-img { max-width: none; }

        /* Fix Bootstrap's h1-h6 margin overriding section titles */
        .section-title, .faq-title, .faq-subtitle,
        .cbs-heading, .ed-heading, .ha-heading,
        .comparison-title, .advisory-heading { margin-bottom: 0; }

        /* Fix Bootstrap ul reset inside footer */
        .footer-col ul { padding-left: 0; list-style: none; margin-bottom: 0; }
        .footer-col ul li { margin-bottom: 10px; }

        /* Fix Bootstrap p margin inside drfuxx sections */
        .hero-sub, .cbs-sub, .ed-sub, .ha-sub,
        .prv2-text p, .faq-a p, .orange-cta-sub,
        .nl-hero-content p { margin-bottom: 0; }
    </style>
</head>
<body>
    @include('layout.partials.skeleton_loader')
<!-- Ticker Bar -->
<div class="ticker-bar">
  <div class="ticker-track" id="tickerTrack">
    <span class="ticker-item"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg> Warum dr.fuxx?</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> 100 % sicher &amp; gepr&uuml;ft</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Faire Preise, keine Tricks</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg> 800.000+ zufriedene Nutzer</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg> 40+ Behandlungsbereiche</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg> 3 Mio.+ erfolgreiche Bestellungen</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Seit &uuml;ber 7 Jahren dabei</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg> Diskret &amp; vertraulich</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg> Arztgespr&auml;ch &amp; Rezept online</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg> Bei dir in 1&ndash;2 Tagen</span>
    <!-- Duplicate for seamless loop -->
    <span class="ticker-item"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg> Warum dr.fuxx?</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> 100 % sicher &amp; gepr&uuml;ft</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Faire Preise, keine Tricks</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg> 800.000+ zufriedene Nutzer</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg> 40+ Behandlungsbereiche</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg> 3 Mio.+ erfolgreiche Bestellungen</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Seit &uuml;ber 7 Jahren dabei</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg> Diskret &amp; vertraulich</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg> Arztgespr&auml;ch &amp; Rezept online</span>
    <span class="ticker-item"><svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg> Bei dir in 1&ndash;2 Tagen</span>
  </div>
</div>

<!-- Navigation -->
@include('layout.partials.navbar_website')

<!-- Promo Banner with Countdown -->
<div class="promo-banner">
  <div class="floral-left"></div>
  <div class="floral-right"></div>
  <div class="promo-text">
    <em>Erfrischen Sie im Fr&uuml;hjahr Ihre Gesundheit:</em>
    &nbsp;Mit dem Rabattcode <span class="promo-code">{{ $home['promo']['code'] ?? 'SAVE10' }}</span> sparen Sie 10 &euro;.
    <span class="promo-countdown" id="promoCountdown">
      <span class="cd-block"><span id="cdHours">00</span><small>Std</small></span>
      <span class="cd-sep">:</span>
      <span class="cd-block"><span id="cdMins">00</span><small>Min</small></span>
      <span class="cd-sep">:</span>
      <span class="cd-block"><span id="cdSecs">00</span><small>Sek</small></span>
    </span>
  </div>
</div>

@php
  $home = $home ?? [];
@endphp

<!-- Hidden data element for JS dynamic values (CMS-controlled) -->
<span id="drfuxx-keywords-data"
  data-keywords="{{ $home['hero']['typing_keywords'] ?? 'Med. Cannabis,Erektionsstörungen,Testosteron,Haarausfall' }}"
  data-live-users="{{ preg_replace('/[^0-9]/', '', $home['hero']['live_viewers'] ?? '127') }}"
  style="display:none;"></span>

<!-- DRFUXX_SECTIONS_START -->

@php
  $hero = $home['hero'] ?? [];
  $quickLinks = $hero['quick_links'] ?? [];
@endphp

<!-- ============ DESKTOP: Top Categories (hidden on mobile) ============ -->
<section class="desktop-only-cats" style="padding: 10px 10px 0; width: 100%; box-sizing: border-box;">
  <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; width: 100%; max-width: 900px; margin: 0 auto;">
    @if(count($quickLinks) > 0)
      @foreach($quickLinks as $ql)
      <a href="{{ $ql['url'] ?? '#' }}" class="tcat" data-bg="{{ $ql['image'] ? url('images/upload/'.$ql['image']) : '' }}" style="text-decoration:none;color:inherit;display:block;">
        <div class="tcat-overlay"></div>
        <div class="tcat-content">
          <span class="tcat-title">{{ $ql['title'] ?? '' }}@if(!empty($ql['badge'])) <span class="tcat-badge">{{ $ql['badge'] }}</span>@endif</span>
          <span class="tcat-desc">{{ $ql['subtitle'] ?? '' }}</span>
        </div>
        <div class="tcat-img"><img src="{{ $ql['image'] ? url('images/upload/'.$ql['image']) : '' }}" alt="{{ $ql['title'] ?? '' }}"></div>
      </a>
      @endforeach
    @else
      <a href="#" class="tcat" data-bg="" style="text-decoration:none;color:inherit;display:block;">
        <div class="tcat-overlay"></div>
        <div class="tcat-content">
          <span class="tcat-title">Med. Cannabis <span class="tcat-badge">NEU</span></span>
          <span class="tcat-desc">Diskrete Beratung &amp; Rezept online</span>
        </div>
        <div class="tcat-img"><img src="" alt="Cannabis"></div>
      </a>
      <a href="#" class="tcat" data-bg="" style="text-decoration:none;color:inherit;display:block;">
        <div class="tcat-overlay"></div>
        <div class="tcat-content">
          <span class="tcat-title">Erektions&shy;st&ouml;rungen</span>
          <span class="tcat-desc">Vertraulich &amp; ohne Wartezeit</span>
        </div>
        <div class="tcat-img"><img src="" alt="ED"></div>
      </a>
      <a href="#" class="tcat" data-bg="" style="text-decoration:none;color:inherit;display:block;">
        <div class="tcat-overlay"></div>
        <div class="tcat-content">
          <span class="tcat-title">Testosteron</span>
          <span class="tcat-desc">Fertige Injektion &ndash; direkt einsatzbereit</span>
        </div>
        <div class="tcat-img"><img src="" alt="Testosteron"></div>
      </a>
    @endif
  </div>
</section>

<!-- ============ MOBILE: DoktorABC-style Hero (hidden on desktop) ============ -->
<section class="mobile-hero-new">
  <!-- Keyword + Title -->
  <div class="mhn-keyword" id="mhnKeyword">{{ strtoupper(explode(',', $hero['typing_keywords'] ?? 'Med. Cannabis')[0]) }}</div>
  <h1 class="mhn-title">{{ $hero['title'] ?? 'Ganz einfach mit dr.fuxx' }}</h1>
  <p class="mhn-sub">{{ $hero['description'] ?? 'Ihr Online-Arzt für diskrete Behandlungen' }}</p>

  <!-- Rating -->
  <div class="mhn-rating">
    <span class="stars" style="color:#f59e0b;">
      @for($i = 0; $i < ($hero['rating_stars'] ?? 5); $i++)&starf;@endfor
    </span>
    <strong>{{ $hero['rating_score'] ?? '4,79' }}</strong> {{ $hero['rating_text'] ?? 'Hervorragend' }}
  </div>

  <!-- CTA Button -->
  <div style="text-align:center; margin-bottom: 20px;">
    <a href="{{ $hero['btn_url'] ?? '#' }}" class="btn-cta btn-cta-pulse" id="mhnCta" style="padding:14px 40px; font-size:0.95rem; border-radius:50px; box-shadow:0 6px 20px rgba(124,58,237,0.35);">{{ $hero['btn_text'] ?? 'Jetzt starten' }}</a>
  </div>

  <!-- Category List Cards -->
  <div class="mhn-cat-list">
    @foreach($quickLinks as $ql)
    <div class="mhn-cat-item" data-bg="{{ $ql['image'] ? url('images/upload/'.$ql['image']) : '' }}">
      <div class="mhn-cat-bg"></div>
      <div class="mhn-cat-overlay"></div>
      <span class="mhn-cat-name">{{ $ql['title'] ?? '' }}</span>
      @if(!empty($ql['image']))<img src="{{ url('images/upload/'.$ql['image']) }}" alt="{{ $ql['title'] ?? '' }}" class="mhn-cat-img">@endif
      <span class="mhn-cat-arrow">&rsaquo;</span>
    </div>
    @endforeach
  </div>
</section>

<!-- ============ DESKTOP: Hero (hidden on mobile) ============ -->
<section class="hero desktop-hero" style="position:relative;overflow:hidden;" @if(!empty($hero['bg_image'])) style="background-image: url('{{ url('images/upload/'.$hero['bg_image']) }}')"@endif>
  <div class="hero-keyword" id="heroKeyword" style="position:relative;z-index:2;">Ihr Online-Arzt &ndash; diskret, schnell &amp; zuverl&auml;ssig</div>
  <h1>{{ $hero['title'] ?? 'Ganz einfach mit dr.fuxx' }}</h1>
  <p class="hero-sub">{{ $hero['description'] ?? 'Ihr Online-Arzt für diskrete Behandlungen' }}</p>
  <div class="hero-search" style="justify-content:center; border:none; box-shadow:none; padding:0; background:transparent;">
    <a href="{{ $hero['btn_url'] ?? '#' }}" class="btn-cta btn-cta-pulse" id="heroCta">{{ $hero['btn_text'] ?? 'Jetzt starten' }}</a>
  </div>
  <div class="hero-trust-row">
    @forelse($hero['trust_items'] ?? [] as $ti)
    <div class="hero-trust-item">
      <i class="{{ $ti['icon_class'] ?? 'bi-check-circle' }}"></i>
      <span>{{ $ti['text'] ?? '' }}</span>
    </div>
    @empty
    <div class="hero-trust-item">
      <svg viewBox="0 0 24 24" width="20" height="20"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" fill="none" stroke="#7c3aed" stroke-width="2"/><path d="M9 12l2 2 4-4" fill="none" stroke="#7c3aed" stroke-width="2"/></svg>
      <span>Deutsche &Auml;rzte</span>
    </div>
    <div class="hero-trust-item">
      <svg viewBox="0 0 24 24" width="20" height="20"><rect x="3" y="11" width="18" height="11" rx="2" ry="2" fill="none" stroke="#7c3aed" stroke-width="2"/><path d="M7 11V7a5 5 0 0110 0v4" fill="none" stroke="#7c3aed" stroke-width="2"/></svg>
      <span>100% DSGVO-konform</span>
    </div>
    <div class="hero-trust-item">
      <svg viewBox="0 0 24 24" width="20" height="20"><rect x="1" y="3" width="15" height="13" fill="none" stroke="#7c3aed" stroke-width="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8" fill="none" stroke="#7c3aed" stroke-width="2"/><circle cx="5.5" cy="18.5" r="2.5" fill="none" stroke="#7c3aed" stroke-width="2"/><circle cx="18.5" cy="18.5" r="2.5" fill="none" stroke="#7c3aed" stroke-width="2"/></svg>
      <span>Expressversand</span>
    </div>
    <div class="hero-trust-item">
      <span style="font-size:16px;">&#127465;&#127466;</span>
      <span>Made in Germany</span>
    </div>
    @endforelse
  </div>
  <div class="hero-rating">
    <span class="stars">@for($i = 0; $i < ($hero['rating_stars'] ?? 5); $i++)&starf;@endfor</span>
    <span class="rating-score">{{ $hero['rating_score'] ?? '4,79' }}</span>
    <span>{{ $hero['rating_text'] ?? 'Hervorragend' }}</span>
  </div>
  <div class="hero-live-users">
    <span class="live-dot"></span> <strong>{{ $hero['live_viewers'] ?? '127 Nutzer online' }}</strong> schauen sich gerade Behandlungen an
  </div>
</section>

<!-- ============ Sub-categories Ticker ============ -->
@php $subCats = $home['sub_categories'] ?? []; @endphp
<section class="subcat-ticker">
  <div class="subcat-track" id="subcatTrack">
    @if(count($subCats) > 0)
      @foreach(array_merge($subCats, $subCats) as $sc)
        <a href="{{ $sc['url'] ?? '#' }}" class="subcat-item">{{ $sc['text'] ?? '' }}</a>
      @endforeach
    @else
      <a href="#" class="subcat-item">Vorzeitiger Samenerguss</a>
      <a href="#" class="subcat-item">Chlamydien</a>
      <a href="#" class="subcat-item">Gonorrhoe</a>
      <a href="#" class="subcat-item">Genitalwarzen</a>
      <a href="#" class="subcat-item">Diabetes Typ-2</a>
      <a href="#" class="subcat-item">Asthma</a>
      <a href="#" class="subcat-item">Gesichtsbehandlung</a>
      <a href="#" class="subcat-item">Bakterielle Vaginose</a>
      <a href="#" class="subcat-item">Schlafst&ouml;rungen</a>
      <a href="#" class="subcat-item">Migr&auml;ne</a>
      <a href="#" class="subcat-item">Bluthochdruck</a>
      <a href="#" class="subcat-item">Cholesterin</a>
      <a href="#" class="subcat-item">Vorzeitiger Samenerguss</a>
      <a href="#" class="subcat-item">Chlamydien</a>
      <a href="#" class="subcat-item">Gonorrhoe</a>
      <a href="#" class="subcat-item">Genitalwarzen</a>
      <a href="#" class="subcat-item">Diabetes Typ-2</a>
      <a href="#" class="subcat-item">Asthma</a>
      <a href="#" class="subcat-item">Gesichtsbehandlung</a>
      <a href="#" class="subcat-item">Bakterielle Vaginose</a>
      <a href="#" class="subcat-item">Schlafst&ouml;rungen</a>
      <a href="#" class="subcat-item">Migr&auml;ne</a>
      <a href="#" class="subcat-item">Bluthochdruck</a>
      <a href="#" class="subcat-item">Cholesterin</a>
    @endif
  </div>
</section>

<!-- ============ How It Works ============ -->
@php $how = $home['how_it_works'] ?? []; $howSteps = $how['steps'] ?? []; @endphp
<section class="online-section" id="online-section">
  <h2 class="section-title">{{ $how['title'] ?? '3 einfache Schritte' }}<br><span>{{ $how['subtitle'] ?? '100 % online' }}</span></h2>
  <div class="steps-badge">
    <span class="steps-badge-dot"></span>
    {{ $how['badge'] ?? '5 Ärzte online | täglich 8–18 Uhr' }}
  </div>

  <!-- DESKTOP: tilted 3-column grid -->
  <div class="steps-wrapper steps-desktop-only">
    <svg class="steps-wave" viewBox="0 0 1200 200" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
      <path d="M-50 120 C150 40, 350 180, 550 100 C750 20, 950 160, 1250 80" stroke="#c4b5fd" stroke-width="3" stroke-opacity="0.6" fill="none"/>
    </svg>
    <div class="steps-grid-tilted">
      @if(count($howSteps) > 0)
        @foreach($howSteps as $i => $step)
          @php
            $tiltClass = ['tilt-left','tilt-center','tilt-right'][$i % 3];
            $stepNum = $i + 1;
            $iconUrl = !empty($step['icon']) ? url('images/upload/'.$step['icon']) : '';
          @endphp
          <div class="step-card-tilted {{ $tiltClass }}">
            <div class="step-num-tilted">{{ $stepNum }}</div>
            <h3>{{ $step['title'] ?? '' }}<br><span>{{ $step['text'] ?? '' }}</span></h3>
            @if($iconUrl)
            <div class="step-img-tilted photo-frame step-img-swap">
              <img class="step-img-default" src="{{ $iconUrl }}" alt="Step {{ $stepNum }}" loading="lazy">
              <img class="step-img-hover" src="{{ $iconUrl }}" alt="Step {{ $stepNum }}" loading="lazy">
            </div>
            @endif
          </div>
        @endforeach
      @else
        <div class="step-card-tilted tilt-left">
          <div class="step-num-tilted">1</div>
          <h3>F&uuml;ll den<br><span>medizinischen<br>Fragebogen aus</span></h3>
          <div class="step-img-tilted photo-frame step-img-swap">
            <img class="step-img-default" src="" alt="Fragebogen" loading="lazy" style="object-fit:contain;background:#f3f0ff;">
            <img class="step-img-hover" src="" alt="Fragebogen" loading="lazy" style="object-fit:cover;">
          </div>
        </div>
        <div class="step-card-tilted tilt-center">
          <div class="step-num-tilted">2</div>
          <h3>W&auml;hle die<br><span>gew&uuml;nschte<br>Behandlung</span></h3>
          <div class="step-img-tilted photo-frame step-img-swap step-img-swap-2">
            <img class="step-img-default" src="" alt="Arzt" loading="lazy">
            <img class="step-img-hover" src="" alt="Arzt" loading="lazy">
          </div>
        </div>
        <div class="step-card-tilted tilt-right">
          <div class="step-num-tilted">3</div>
          <h3>Lieferung<br><span>flexibel<br>w&auml;hlen</span></h3>
          <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">
            <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:#f3f0ff;border-radius:12px;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
              <div>
                <div style="font-size:0.82rem;font-weight:700;color:#1a1a1a;">Online Express</div>
                <div style="font-size:0.72rem;color:#888;">Lieferung in 1&ndash;2 Werktagen</div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:#f3f0ff;border-radius:12px;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              <div>
                <div style="font-size:0.82rem;font-weight:700;color:#1a1a1a;">Apotheke vor Ort</div>
                <div style="font-size:0.72rem;color:#888;">Selbstabholung m&ouml;glich</div>
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>

  <!-- MOBILE: Page-Turn Card Book -->
  <div class="steps-mobile-only">
    <div class="sbook-wrap">
      <div class="sbook" id="stepBook">
        @if(count($howSteps) > 0)
          @foreach($howSteps as $i => $step)
            @php $iconUrl = !empty($step['icon']) ? url('images/upload/'.$step['icon']) : ''; @endphp
            <div class="sbook-page" data-idx="{{ $i }}">
              <div class="sbook-num">{{ $i + 1 }}</div>
              <div class="sbook-text">
                <h3>{{ $step['title'] ?? '' }} <span>{{ $step['text'] ?? '' }}</span></h3>
              </div>
              @if($iconUrl)
              <div class="sbook-img">
                <img src="{{ $iconUrl }}" alt="Step {{ $i + 1 }}">
              </div>
              @endif
              <div class="sbook-curl"></div>
            </div>
          @endforeach
        @else
          <div class="sbook-page" data-idx="0">
            <div class="sbook-num">1</div>
            <div class="sbook-text">
              <h3>F&uuml;ll den <span>medizinischen Fragebogen aus</span></h3>
            </div>
            <div class="sbook-img"><img src="" alt="Fragebogen"></div>
            <div class="sbook-curl"></div>
          </div>
          <div class="sbook-page" data-idx="1">
            <div class="sbook-num">2</div>
            <div class="sbook-text">
              <h3>W&auml;hle die <span>gew&uuml;nschte Behandlung</span></h3>
            </div>
            <div class="sbook-img"><img src="" alt="Arzt"></div>
            <div class="sbook-curl"></div>
          </div>
          <div class="sbook-page" data-idx="2">
            <div class="sbook-num">3</div>
            <div class="sbook-text">
              <h3>Lieferung <span>flexibel w&auml;hlen</span></h3>
            </div>
            <div style="display:flex;flex-direction:column;gap:8px;padding:0 16px 16px;">
              <div style="display:flex;align-items:center;gap:10px;padding:12px;background:#f3f0ff;border-radius:12px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                <div>
                  <div style="font-size:0.85rem;font-weight:700;color:#1a1a1a;">Online Express</div>
                  <div style="font-size:0.75rem;color:#888;">1&ndash;2 Werktage</div>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:10px;padding:12px;background:#f3f0ff;border-radius:12px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <div>
                  <div style="font-size:0.85rem;font-weight:700;color:#1a1a1a;">Apotheke vor Ort</div>
                  <div style="font-size:0.75rem;color:#888;">Selbstabholung</div>
                </div>
              </div>
            </div>
          </div>
        @endif
      </div>
      <div class="sbook-hint" id="sbookHint">
        <svg viewBox="0 0 48 16" fill="none"><path d="M2 8h42M36 2l8 6-8 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Wischen zum Bl&auml;ttern
      </div>
      <div class="sbook-dots" id="sbookDots">
        @if(count($howSteps) > 0)
          @foreach($howSteps as $i => $step)
            <span class="sbook-dot {{ $i === 0 ? 'active' : '' }}"></span>
          @endforeach
        @else
          <span class="sbook-dot active"></span>
          <span class="sbook-dot"></span>
          <span class="sbook-dot"></span>
        @endif
      </div>
    </div>
  </div>
</section>

<!-- ============ Trust Banner ============ -->
@php $trust_text = $home['trust_banner']['text'] ?? "Deutschlands größte Online-Klinik – mit echten deutschen Ärzten, rund um die Uhr für dich da"; @endphp
<section class="trust-banner">
  <h2>{{ $trust_text }}</h2>
</section>

<!-- ============ Cannabis Banner Section ============ -->
@php $relief = $home['natural_relief'] ?? []; @endphp
<section class="cannabis-banner-section">
  <div class="cbs-inner">
    <span class="cbs-pill">{{ $relief['badge'] ?? 'REZEPT WIRD ONLINE AUSGESTELLT' }}</span>
    @php
      $cbsTitle = $relief['title'] ?? 'Finden Sie natürliche und sichere|Linderung';
      $cbsParts = explode('|', $cbsTitle, 2);
    @endphp
    <h2 class="cbs-heading">{{ $cbsParts[0] }}@if(isset($cbsParts[1]))<span class="cbs-green">{{ $cbsParts[1] }}</span>@endif</h2>

    <div class="cbs-hero-img-wrap">
      @if(!empty($relief['image']))
        <img src="{{ asset('images/upload/'.$relief['image']) }}" alt="Patient meditierend" class="cbs-hero-img" />
      @else
        <img src="anmol/Design%20ohne%20Titel%20%2873%29.png" alt="Patient meditierend" class="cbs-hero-img" />
      @endif
      <div class="cbs-btns-overlay">
        <a href="{{ $relief['btn1_url'] ?? '#' }}" class="cbs-btn cbs-btn-outline">{{ $relief['btn1_text'] ?? 'Berechtigung prüfen' }}</a>
        <a href="{{ $relief['btn2_url'] ?? '#' }}" class="cbs-btn cbs-btn-filled">{{ $relief['btn2_text'] ?? 'Gratis Beratung starten' }}</a>
      </div>
    </div>
  </div>

  <div class="cbs-cards">
    @php $cbsCards = array_slice($relief['cards'] ?? [], 0, 2); @endphp
    @if(count($cbsCards) > 0)
      @foreach($cbsCards as $card)
      <div class="cbs-card">
        <div class="cbs-card-text">
          <h3>{{ $card['title'] ?? '' }}</h3>
          <a href="{{ $card['btn_url'] ?? '#' }}" class="cbs-card-btn">{{ $card['btn_text'] ?? 'Mehr erfahren' }}</a>
        </div>
        <div class="cbs-card-img-wrap">
          @if(!empty($card['icon']))
            <img src="{{ asset('images/upload/'.$card['icon']) }}" alt="" class="cbs-card-img" />
          @else
            <img src="anmol%20dr%20fuxx%20neu%20neu%20heit%20en%20/HOME%20/Finden%20Sie%20nat%C3%BCrliche%20und%20sichere%20Linderung/Pink%20and%20Black%20Minimalist%20Phone%20Mockup%20Instagram%20Post%20%2851%29.png" alt="" class="cbs-card-img" />
          @endif
        </div>
      </div>
      @endforeach
    @else
      <div class="cbs-card">
        <div class="cbs-card-text">
          <h3>Wenn Schmerzen nicht aufhören…</h3>
          <a href="cannabis.html" class="cbs-card-btn">Mehr erfahren</a>
        </div>
        <div class="cbs-card-img-wrap">
          <img src="anmol%20dr%20fuxx%20neu%20neu%20heit%20en%20/HOME%20/Finden%20Sie%20nat%C3%BCrliche%20und%20sichere%20Linderung/Pink%20and%20Black%20Minimalist%20Phone%20Mockup%20Instagram%20Post%20%2851%29.png" alt="Frau entspannt" class="cbs-card-img" />
        </div>
      </div>
      <div class="cbs-card">
        <div class="cbs-card-text">
          <h3>Sind Sie es leid, alles andere auszuprobieren?</h3>
          <a href="cannabis.html" class="cbs-card-btn">Behandlungen entdecken</a>
        </div>
        <div class="cbs-card-img-wrap">
          <img src="anmol/drugs%20/Design%20ohne%20Titel%20%2879%29.png" alt="Cannabis" class="cbs-card-img" />
        </div>
      </div>
    @endif
  </div>
</section>

<!-- ============ ED Banner Section ============ -->
@php $ed = $home['ed_banner'] ?? []; @endphp
<section class="ed-banner-section">
  <div class="ed-inner">
    <span class="ed-pill">{{ $ed['pill'] ?? 'LÖSUNG FÜR EREKTILE DYSFUNKTION' }}</span>
    @php
      $edTitle = $ed['title'] ?? 'Gewinnen Sie Ihr|Selbstvertrauen und Ihre Intimität zurück';
      $edParts = explode('|', $edTitle, 2);
    @endphp
    <h2 class="ed-heading">{{ $edParts[0] }}@if(isset($edParts[1]))<span class="ed-blue">{{ $edParts[1] }}</span>@endif</h2>

    <div class="ed-hero-img-wrap">
      @if(!empty($ed['hero_image']))
        <img src="{{ asset('images/upload/'.$ed['hero_image']) }}" alt="Paar" class="ed-hero-img" />
      @else
        <img src="anmol%20dr%20fuxx%20neu%20neu%20heit%20en%20/HOME%20/Gewinnen%20Sie%20Ihr%20Selbstvertrauen%20und%20Ihre%20Intimit%C3%A4t%20zur%C3%BCck/Pink%20and%20Black%20Minimalist%20Phone%20Mockup%20Instagram%20Post%20%2856%29.png" alt="Paar" class="ed-hero-img" />
      @endif
      <div class="ed-btns-overlay">
        <a href="{{ $ed['btn1_url'] ?? '#' }}" class="ed-btn ed-btn-outline">{{ $ed['btn1_text'] ?? 'Meine Behandlung finden' }}</a>
        <a href="{{ $ed['btn2_url'] ?? '#' }}" class="ed-btn ed-btn-filled">{{ $ed['btn2_text'] ?? 'Meine kostenlose Beratung starten' }}</a>
      </div>
    </div>
  </div>

  <div class="ed-cards">
    @php $edLarge = $ed['large_card'] ?? []; @endphp
    <div class="ed-card ed-card-large">
      <div class="ed-card-text">
        <h3>{{ $edLarge['title'] ?? 'Es kommt häufiger vor, als Sie denken.' }}</h3>
        <a href="{{ $edLarge['btn_url'] ?? '#' }}" class="ed-card-btn">{{ $edLarge['btn_text'] ?? 'Mehr über Ursachen erfahren' }}</a>
      </div>
      <div class="ed-card-img-wrap">
        @if(!empty($edLarge['image']))
          <img src="{{ asset('images/upload/'.$edLarge['image']) }}" alt="" class="ed-card-img" />
        @else
          <img src="ed-card-1.png" alt="" class="ed-card-img" />
        @endif
      </div>
    </div>
    <div class="ed-cards-right">
      @php $edR1 = $ed['right_card_1'] ?? []; @endphp
      <div class="ed-card ed-card-small">
        <div class="ed-card-text">
          <h3>{{ $edR1['title'] ?? 'Wenn Leistung zu Druck wird' }}</h3>
          <a href="{{ $edR1['btn_url'] ?? '#' }}" class="ed-card-btn">{{ $edR1['btn_text'] ?? 'Verstehen, wie ED funktioniert' }}</a>
        </div>
        <div class="ed-card-img-wrap">
          @if(!empty($edR1['image']))
            <img src="{{ asset('images/upload/'.$edR1['image']) }}" alt="" class="ed-card-img" />
          @else
            <img src="anmol%20dr%20fuxx%20neu%20neu%20heit%20en%20/HOME%20/Gewinnen%20Sie%20Ihr%20Selbstvertrauen%20und%20Ihre%20Intimit%C3%A4t%20zur%C3%BCck/Pink%20and%20Black%20Minimalist%20Phone%20Mockup%20Instagram%20Post%20%2859%29.png" alt="" class="ed-card-img" />
          @endif
        </div>
      </div>
      @php $edR2 = $ed['right_card_2'] ?? []; @endphp
      <div class="ed-card ed-card-small">
        <div class="ed-card-text">
          <h3>{{ $edR2['title'] ?? 'Professionelle Hilfe, die diskret wirkt.' }}</h3>
          <a href="{{ $edR2['btn_url'] ?? '#' }}" class="ed-card-btn">{{ $edR2['btn_text'] ?? 'Mein Rezept erhalten' }}</a>
        </div>
        <div class="ed-card-img-wrap">
          @if(!empty($edR2['image']))
            <img src="{{ asset('images/upload/'.$edR2['image']) }}" alt="" class="ed-card-img" />
          @else
            <img src="anmol%20dr%20fuxx%20neu%20neu%20heit%20en%20/HOME%20/Gewinnen%20Sie%20Ihr%20Selbstvertrauen%20und%20Ihre%20Intimit%C3%A4t%20zur%C3%BCck/Pink%20and%20Black%20Minimalist%20Phone%20Mockup%20Instagram%20Post%20%2863%29.png" alt="" class="ed-card-img" />
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ Testosterone Banner Section ============ -->
@php $testo = $home['testosterone'] ?? []; @endphp
<section class="ha-banner-section" style="position:relative;overflow:hidden;background:linear-gradient(160deg, #f3f0ff 0%, #ede9fe 50%, #f3f0ff 100%) !important;min-height:580px;display:flex;flex-direction:column;justify-content:center;">
  @if(!empty($testo['bg_image']))
    <img src="{{ asset('images/upload/'.$testo['bg_image']) }}" alt="" style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);height:90%;width:auto;object-fit:contain;z-index:0;opacity:0.5;">
  @else
    <img src="anmol/testo/hf_20260323_154644_5cbf7a47-4a8d-4d8c-bb8e-9e0abda5afd2%20Background%20Removed.png" alt="" style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);height:90%;width:auto;object-fit:contain;z-index:0;opacity:0.5;">
  @endif
  <div style="position:absolute;inset:0;background:linear-gradient(to top, rgba(237,233,254,0.9) 0%, rgba(243,240,255,0.5) 40%, transparent 70%);z-index:1;"></div>
  <div class="ha-inner" style="position:relative;z-index:2;">
    <span class="ha-pill" style="border-color:#7c3aed !important;color:#7c3aed !important;background:rgba(250,248,255,0.8);backdrop-filter:blur(4px);">{{ $testo['pill'] ?? 'TESTOSTERON-INJEKTION' }}</span>
    @php
      $testoTitle = $testo['title'] ?? 'Testosteron-Injektion — |fertig zur Direktnutzung';
      $testoParts = explode('|', $testoTitle, 2);
    @endphp
    <h2 class="ha-heading" style="color:#1a1a1a;text-shadow:none;">{{ $testoParts[0] }}@if(isset($testoParts[1]))<span style="color:#7c3aed !important;">{{ $testoParts[1] }}</span>@endif</h2>

    <div class="testo-btns">
      <a href="{{ $testo['btn1_url'] ?? '#' }}" class="testo-btn-outline" style="color:#1a1a1a;border-color:#7c3aed;background:rgba(250,248,255,0.8);">{{ $testo['btn1_text'] ?? 'Mehr erfahren' }}</a>
      <a href="{{ $testo['btn2_url'] ?? '#' }}" class="testo-btn-filled" style="background:#7c3aed;border-color:#7c3aed;">{{ $testo['btn2_text'] ?? 'Jetzt Beratung starten' }}</a>
    </div>
  </div>
</section>

<!-- Medical Advisory Board -->
@php $advisory = $home['advisory'] ?? []; @endphp
<section class="advisory-section">
  <div style="max-width:900px;margin:0 auto;">
    <h2 style="font-size:1.8rem;font-weight:800;color:#1a1a1a;text-align:center;margin-bottom:8px;">{{ $advisory['title'] ?? 'Unser medizinischer Beirat' }}</h2>
    <div class="doc-grid">
      @if(!empty($advisory['doctors']))
        @foreach($advisory['doctors'] as $doctor)
        <div class="doc-card">
          @if(!empty($doctor['image']))
            <img src="{{ url('images/upload/'.$doctor['image']) }}" alt="{{ $doctor['name'] ?? 'Dr. med. Expert' }}">
          @else
            <div style="width:120px;height:120px;border-radius:50%;background:#e0d9ff;margin:0 auto;"></div>
          @endif
          <div class="doc-card-info">
            <div class="doc-card-name">{{ $doctor['name'] ?? 'Dr. med. Expert' }}</div>
            @if(!empty($doctor['role']))<div style="font-size:0.85rem;color:#666;">{{ $doctor['role'] }}</div>@endif
          </div>
        </div>
        @endforeach
      @else
        @for($i = 0; $i < 5; $i++)
        <div class="doc-card">
          <div style="width:120px;height:120px;border-radius:50%;background:#e0d9ff;margin:0 auto;"></div>
          <div class="doc-card-info"><div class="doc-card-name">Dr. med. Expert</div></div>
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

<!-- Stats Section -->
@php $stats = $home['stats'] ?? []; @endphp
<section class="cbs-stats-section">
  <p class="cbs-stats-sub">{{ $stats['subtitle'] ?? 'Rund um die Uhr Hilfe von deutschen Ärzten' }}</p>
  <div class="cbs-stats-grid">
    @if(!empty($stats['items']))
      @foreach($stats['items'] as $item)
      <div class="cbs-stat">
        <span class="cbs-stat-label">{{ $item['label'] ?? '' }}</span>
        <span class="cbs-stat-num">{{ $item['number'] ?? '' }}</span>
        <span class="cbs-stat-title">{{ $item['title'] ?? '' }}</span>
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

<!-- Comparison Section -->
@php $compare = $home['comparison'] ?? []; @endphp
<section class="comparison-section" style="position:relative;overflow:hidden;">
  <div style="text-align:center;margin-bottom:-20px;position:relative;z-index:1;">
    @if(!empty($compare['bg_image']))
      <img src="{{ url('images/upload/'.$compare['bg_image']) }}" alt="" style="height:180px;width:auto;object-fit:contain;opacity:0.9;">
    @endif
  </div>
  <div class="comparison-inner" style="position:relative;z-index:2;">
    <h2 class="comparison-title" style="margin-top:0;">{{ $compare['title'] ?? 'Warum dr.fuxx?' }}</h2>
    <div class="comp-table">
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
      @endif
    </div>
  </div>
</section>

<!-- FAQ Section -->
@php $faq = $home['faq'] ?? []; @endphp
<section class="faq-section" id="faq-section">
  <div class="faq-inner">
    <div class="faq-header">
      <h2 class="faq-title">{{ $faq['title'] ?? 'Sie haben Fragen?' }}</h2>
      <p class="faq-subtitle">{{ $faq['subtitle'] ?? 'Hier gibt es Antworten!' }}</p>
    </div>
    <div class="faq-list">
      @if(!empty($faq['items']))
        @foreach($faq['items'] as $i => $item)
        <div class="faq-item {{ $i === 0 ? 'active' : '' }}">
          <button class="faq-q">{{ $item['question'] }}<span class="faq-icon">{{ $i === 0 ? '&#8963;' : '&#8964;' }}</span></button>
          <div class="faq-a"><p>{{ $item['answer'] }}</p></div>
        </div>
        @endforeach
      @else
        <div class="faq-item active">
          <button class="faq-q">Was ist dr.fuxx?<span class="faq-icon">&#8963;</span></button>
          <div class="faq-a"><p>dr.fuxx ist eine digitale Gesundheits- und Apothekenplattform mit Sitz in Deutschland. Wir verbinden Patienten mit zugelassenen Ärzten, die medizinische Fernuntersuchungen durchführen und bei Bedarf elektronische Rezepte ausstellen. Die Medikamente werden von unserem Netzwerk aus Partnerapotheken ausgegeben.</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q">Wie funktioniert dr.fuxx?<span class="faq-icon">&#8964;</span></button>
          <div class="faq-a"><p>Wählen Sie Ihre Behandlung aus, beantworten Sie einen kurzen medizinischen Fragebogen und ein zugelassener Arzt prüft Ihre Angaben. Bei Eignung wird ein Rezept ausgestellt und das Medikament diskret zu Ihnen nach Hause geliefert – alles online, ohne Wartezeit.</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q">Wer sind die Anbieter bei dr.fuxx?<span class="faq-icon">&#8964;</span></button>
          <div class="faq-a"><p>Alle Ärzte auf dr.fuxx sind vollständig zugelassen und in Deutschland registriert. Unsere Partnerapotheken sind ebenfalls staatlich zertifiziert und unterliegen strengen Qualitätskontrollen.</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q">Benötigt man für dr.fuxx eine Versicherung?<span class="faq-icon">&#8964;</span></button>
          <div class="faq-a"><p>Nein, eine Krankenversicherung ist nicht erforderlich. dr.fuxx ist ein Privatanbieter und kann von jedem genutzt werden.</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q">Für wen ist dr.fuxx gedacht?<span class="faq-icon">&#8964;</span></button>
          <div class="faq-a"><p>dr.fuxx richtet sich an Erwachsene, die eine schnelle, diskrete und unkomplizierte medizinische Beratung und Behandlung suchen – ganz ohne Arzttermin oder lange Wartezeiten.</p></div>
        </div>
      @endif
    </div>
  </div>
</section>

{{-- SECTION: Press Logos --}}
@php $press = $home['press'] ?? []; @endphp
<section class="press-logos">
  <div class="press-logos-inner">
    <span class="press-label">{{ $press['label'] ?? 'Bekannt aus' }}</span>
    <div class="press-track">
      @if(!empty($press['logos']))
        @foreach($press['logos'] as $logo)
          <span class="press-logo-item">{{ $logo['name'] }}</span>
        @endforeach
      @else
        <span class="press-logo-item">BILD</span>
        <span class="press-logo-item">TAGESSPIEGEL</span>
        <span class="press-logo-item">FOCUS</span>
        <span class="press-logo-item">news.de</span>
        <span class="press-logo-item">OK!</span>
        <span class="press-logo-item">WESTFALEN</span>
      @endif
    </div>
  </div>
</section>

{{-- SECTION: Mid-page CTA --}}
@php $cta = $home['mid_cta'] ?? []; @endphp
<section class="mid-cta">
  <div class="mid-cta-inner">
    <h2>{{ $cta['heading'] ?? 'Bereit? In 3 Minuten zu deinem Rezept.' }}</h2>
    @if(!empty($cta['subtext']))<p>{{ $cta['subtext'] }}</p>@endif
    <a href="{{ $cta['btn_url'] ?? '#' }}" class="btn-cta-lg">{{ $cta['btn_text'] ?? 'Jetzt kostenlos starten' }}</a>
    <div class="mid-cta-note">{{ $cta['note'] ?? 'Keine Kosten bis zur Rezeptausstellung' }}</div>
  </div>
</section>

{{-- SECTION: Privacy --}}
@php $priv = $home['privacy_section'] ?? []; @endphp
<section class="privacy-v2">
  <div class="prv2-card">
    <div class="prv2-text">
      <h2>{{ $priv['heading'] ?? 'Ihre Privatsphäre' }}<br><span>{{ $priv['span'] ?? 'Unsere Priorität' }}</span></h2>
      <div class="prv2-flags">
        <div class="prv2-flag-item">
          <span style="font-size:1.5rem;">&#127466;&#127482;</span>
          <span style="color:#dc2626;font-size:1.2rem;font-weight:800;">&times;</span>
        </div>
        <div class="prv2-flag-item">
          <span style="font-size:1.5rem;">&#127465;&#127466;</span>
          <span style="color:#7c3aed;font-size:1.2rem;font-weight:800;">&#10003;</span>
        </div>
      </div>
      <p style="font-weight:700;color:#1a1a1a;margin-bottom:12px;font-size:1rem;">{{ $priv['description'] ?? 'Ihre Daten bleiben sicher in Deutschland' }}</p>
      <div class="prv2-mig-badge">
        <div style="display:flex;flex-direction:column;">
          <div style="width:32px;height:6px;background:#000;border-radius:2px 2px 0 0;"></div>
          <div style="width:32px;height:6px;background:#dc2626;"></div>
          <div style="width:32px;height:6px;background:#f59e0b;border-radius:0 0 2px 2px;"></div>
        </div>
        <div>
          <div style="font-size:0.85rem;font-weight:800;color:#1a1a1a;">Made in Germany</div>
          <div style="font-size:0.68rem;color:#666;">Deutsche Ärzte &bull; DSGVO &bull; Sitz in DE</div>
        </div>
      </div>
    </div>
    <div class="prv2-img-wrap">
      @if(!empty($priv['image']))
        <img src="{{ url('images/upload/'.$priv['image']) }}" alt="Person">
      @else
        <img src="{{ asset('images/privacy-person-placeholder.png') }}" alt="Person">
      @endif
    </div>
  </div>
</section>

{{-- SECTION: Newsletter --}}
@php $news = $home['newsletter'] ?? []; @endphp
<section class="newsletter-hero">
  <div class="nl-hero-bg" @if(!empty($news['bg_image'])) style="background-image:url('{{ url('images/upload/'.$news['bg_image']) }}');background-size:cover;background-position:center center;" @endif></div>
  <div class="nl-hero-content">
    <h2>{{ $news['heading'] ?? 'Bleib auf dem Laufenden' }}</h2>
    <p>{{ $news['description'] ?? 'Meld dich für unseren Newsletter an und erhalte exklusive Angebote' }}</p>
    <form class="nl-hero-form" action="#" method="POST" onsubmit="return false;">
      <input type="email" placeholder="E-Mail">
      <button type="submit">Abonnieren</button>
    </form>
    <p class="nl-hero-legal">{{ $news['legal_text'] ?? 'Mit der Erstellung eines Kontos stimmst du unseren Nutzungsbedingungen zu.' }}</p>
  </div>
</section>

{{-- SECTION: Footer --}}
@php
  $footer = json_decode($setting->website_footer_settings ?? '{}', true) ?? [];
  $footer_cols = $footer['columns'] ?? [];
@endphp
<footer class="footer" id="footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <a href="/" class="logo">
        @if(!empty($setting->company_white_logo) && file_exists(public_path('images/upload/'.$setting->company_white_logo)))
          <img src="{{ $setting->companyWhite }}" style="height:40px;" alt="{{ $setting->business_name }}">
        @else
          <img src="{{ url('/images/upload_empty/fuxxlogo.png') }}" style="height:40px;" alt="{{ $setting->business_name }}">
        @endif
      </a>
      <div class="footer-addr">
        {{ $setting->business_name ?? '' }}<br>
        @if(!empty($setting->phone))Telefon: <a href="tel:{{ $setting->phone }}">{{ $setting->phone }}</a><br>@endif
        @if(!empty($setting->email))E-Mail: <a href="mailto:{{ $setting->email }}">{{ $setting->email }}</a>@endif
      </div>
      <div class="footer-social">
        @php $fb = $footer['facebook'] ?? ($setting->facebook_url ?? '#'); @endphp
        @php $tw = $footer['twitter'] ?? ($setting->twitter_url ?? '#'); @endphp
        @php $ig = $footer['instagram'] ?? ($setting->instagram_url ?? '#'); @endphp
        @php $li = $footer['linkedin'] ?? ($setting->linkdin_url ?? '#'); @endphp
        <a href="{{ $fb }}" class="soc-icon" aria-label="Facebook"><svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
        <a href="{{ $tw }}" class="soc-icon" aria-label="Twitter"><svg viewBox="0 0 24 24"><path d="M4 4l11.733 16h4.267l-11.733-16zM4 20l6.768-6.768M13.232 10.232L20 4"/></svg></a>
        <a href="{{ $ig }}" class="soc-icon" aria-label="Instagram"><svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="white" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="white" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.5" fill="white"/></svg></a>
        <a href="{{ $li }}" class="soc-icon" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></a>
      </div>
    </div>

    @if(count($footer_cols) > 0)
      @foreach($footer_cols as $col)
        <div class="footer-col">
          <h4>{{ $col['title'] }}</h4>
          <ul>
            @foreach($col['links'] ?? [] as $link)
              <li><a href="{{ $link['url'] ?? '#' }}">{{ $link['label'] }}</a></li>
            @endforeach
          </ul>
        </div>
      @endforeach
    @else
      <div class="footer-col">
        <h4>Unser Service</h4>
        <ul>
          <li><a href="#">So funktioniert es</a></li>
          <li><a href="#">Behandlungen</a></li>
          <li><a href="#">Online-Videosprechstunde</a></li>
          <li><a href="#">FAQ (Hilfe)</a></li>
          <li><a href="#">dr.fuxx Erfahrungen</a></li>
          <li><a href="#">Wellness Magazin</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Informationen</h4>
        <ul>
          <li><a href="#">Nutzungsbedingungen</a></li>
          <li><a href="#">Allgemeine Gesch&auml;ftsbedingungen</a></li>
          <li><a href="#">Datenschutz</a></li>
          <li><a href="#">Versand</a></li>
          <li><a href="#">Zahlungsm&ouml;glichkeiten</a></li>
          <li><a href="#">Cookieeinstellungen</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>&Uuml;ber dr.fuxx</h4>
        <ul>
          <li><a href="#">&Uuml;ber uns</a></li>
          <li><a href="#">Medizinischer Beirat</a></li>
          <li><a href="#">Werde Partner</a></li>
          <li><a href="#">Presse</a></li>
          <li><a href="#">Impressum</a></li>
          <li><a href="#">Kontakt</a></li>
        </ul>
      </div>
    @endif
  </div>
  <div class="footer-bottom">
    <p>dr.fuxx ist eine Vermittlungsplattform &ndash; keine Internetapotheke und kein Ersatz f&uuml;r &auml;rztliche Beratung.</p>
    <p style="margin-top:8px;">{{ $footer['copy'] ?? ('&copy; '.date('Y').' '.($setting->business_name ?? 'dr.fuxx').'. Alle Rechte vorbehalten.') }}</p>
  </div>
</footer>

<!-- DRFUXX_SECTIONS_END -->

<!-- Mobile Nav -->
<nav class="mobile-nav" id="mobileNav">
  <div class="mobile-nav-head">
    <span style="font-weight:700;">Men&uuml;</span>
    <button class="close-btn" id="closeNav">&times;</button>
  </div>
  <ul>
    <li><a href="{{ $hero['btn_url'] ?? '#' }}">Jetzt Rezept anfragen</a></li>
    <li><a href="#">Med. Cannabis</a></li>
    <li><a href="#">Erektionsst&ouml;rungen</a></li>
    <li><a href="#">Testosteron</a></li>
    <li><a href="#">Haarausfall</a></li>
    <li><a href="{{ url('/#online-section') }}">So funktioniert es</a></li>
    <li><a href="{{ url('/about-us') }}">&Uuml;ber uns</a></li>
    <li><a href="{{ url('/#faq-section') }}">FAQ (Hilfe)</a></li>
  </ul>
</nav>
<div class="overlay" id="overlay"></div>

<!-- Sticky Bottom CTA Bar -->
<div class="sticky-cta-bar" id="stickyCta">
  <div class="sticky-cta-inner">
    <span class="sticky-cta-text">Ihr Online-Arzt &ndash; diskret, schnell &amp; zuverl&auml;ssig</span>
    <a href="{{ $hero['btn_url'] ?? '#' }}" class="sticky-btn">{{ $hero['btn_text'] ?? 'Jetzt starten' }}</a>
  </div>
</div>

<!-- Social Proof Toast -->
<div class="social-proof-toast" id="socialProofToast">
  <div class="spt-inner">
    <div class="spt-icon">&#128100;</div>
    <div class="spt-text">
      <strong id="sptName"></strong>
      <span id="sptAction"></span>
      <small id="sptTime"></small>
    </div>
  </div>
</div>

<!-- Made in Germany Badge -->
<div class="made-in-germany-badge" id="madeInGermanyBadge">
  <div class="mig-inner">
    <div class="mig-flag">
      <div class="mig-stripe mig-black"></div>
      <div class="mig-stripe mig-red"></div>
      <div class="mig-stripe mig-gold"></div>
    </div>
    <div class="mig-text">
      <strong>Made in Germany</strong>
      <small>Deutsche &Auml;rzte &bull; DSGVO &bull; Sitz in DE</small>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- drfuxx JS -->
<script src="{{ asset('js/drfuxx-main.js') }}"></script>
<script src="{{ asset('js/drfuxx-landing.js') }}"></script>
</body>
</html>
