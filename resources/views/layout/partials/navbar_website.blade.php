@php
    $marquees = json_decode($setting->website_header_top_marquee, true) ?: [];
    $menus = json_decode($setting->website_header_sidebar_menu, true) ?: [];
    $promo = json_decode($setting->website_header_promo_bar, true) ?: [];
@endphp


<!-- Top Header Marquee -->
@if(count($marquees) > 0)
<div class="top-header-marquee">
    <div class="marquee-content">
        @foreach(array_merge($marquees, $marquees) as $item) <!-- Repeated for seamless animation -->
            <div class="marquee-item">
                @if($item['icon'])
                    <img src="{{ url('images/upload/'.$item['icon']) }}" alt="">
                @endif
                <span>{{ $item['text'] }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Main Header -->
<header class="main-header">
    <div class="container header-container">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="header-logo">
            @if($setting->website_header_logo)
                <img src="{{ url('images/upload/'.$setting->website_header_logo) }}" alt="{{ $setting->business_name }}">
            @else
                <img src="{{ url('/images/upload_empty/fuxxlogo.png') }}" alt="{{ $setting->business_name }}">
            @endif
        </a>

        <!-- Actions -->
        <div class="header-actions">
            <!-- Search Bar (Expandable) -->
            @if($setting->website_header_search)
            <div class="header-search" id="headerSearch">
                <button class="search-toggle" id="searchToggle" aria-label="Toggle Search">
                    <i class="bi bi-search"></i>
                </button>
                <form action="{{ route('categories') }}" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Suche..." value="{{ request('search') }}">
                    <button type="submit" class="search-submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
            @endif

            @if($setting->website_header_btn_text)
            <a href="{{ $setting->website_header_btn_url ?: route('categories') }}" 
               class="header-btn d-none d-md-block" 
               style="background-color: {{ $setting->website_header_btn_bg_color ?: '#7b42f6' }}; color: {{ $setting->website_header_btn_text_color ?: '#fff' }};">
                {{ $setting->website_header_btn_text }}
            </a>
            @endif

            @if($setting->website_header_user)
            <a href="{{ auth()->check() ? url('user_profile') : url('patient-login') }}" class="header-icon">
                <i class="bi bi-person"></i>
            </a>
            @endif

            @if($setting->website_header_hamburger)
            <div class="header-icon" id="hamburgerMenu">
                <i class="bi bi-list"></i>
            </div>
            @endif
        </div>
    </div>
</header>

<!-- Promo Bar -->
@if(!empty($promo['status']) && $promo['status'] == 1)
<style>
    .fuxx-promo-bar {
        background-color: #3b5ef8;
        color: white;
        font-family: 'Inter', sans-serif;
        position: relative;
        z-index: 1040;
    }
    .fuxx-promo-bar .decor-icon {
        position: absolute;
        opacity: 0.15;
    }
    .promo-countdown-box {
        background-color: #2441d1 !important;
        border-radius: 4px;
        min-width: 32px;
        text-align: center;
        padding: 2px 4px;
        font-weight: 800;
        line-height: 1.2;
    }
    .promo-countdown-label {
        font-size: 0.55rem;
        letter-spacing: 1px;
        font-weight: 800;
        text-transform: uppercase;
        margin-top: 2px;
    }
</style>
<div class="fuxx-promo-bar d-flex justify-content-center align-items-center py-2 px-3 overflow-hidden position-relative">
    <!-- Decor -->
    <i class="bi bi-flower1 decor-icon" style="font-size: 1.5rem; top: 5px; left: 10%;"></i>
    <i class="bi bi-flower3 decor-icon" style="font-size: 2.2rem; top: -10px; left: 15%;"></i>
    <i class="bi bi-star-fill decor-icon" style="font-size: 0.8rem; top: 22px; left: 18%;"></i>

    <!-- Text content -->
    <div class="pe-3 text-center d-flex flex-column flex-md-row align-items-center" style="z-index: 2;">
        @if(!empty($promo['text_italic']))
            <span class="fst-italic me-1" style="opacity: 0.9;">{{ $promo['text_italic'] }}</span>
        @endif
        @if(!empty($promo['text_bold']))
            <span class="fw-bold">{{ $promo['text_bold'] }}</span>
        @endif
    </div>

    <!-- Countdown Timer -->
    @if(!empty($promo['end_date']))
    <div class="d-flex align-items-center gap-1 fw-bold promo-timer-container ms-2" style="z-index: 2;" data-endtime="{{ $promo['end_date'] }}">
        <div class="d-flex flex-column align-items-center">
            <div class="promo-countdown-box fs-6 hours-box">00</div>
            <span class="promo-countdown-label">STD</span>
        </div>
        <span class="fs-6 pb-2">:</span>
        <div class="d-flex flex-column align-items-center">
            <div class="promo-countdown-box fs-6 mins-box">00</div>
            <span class="promo-countdown-label">MIN</span>
        </div>
        <span class="fs-6 pb-2">:</span>
        <div class="d-flex flex-column align-items-center">
            <div class="promo-countdown-box fs-6 secs-box">00</div>
            <span class="promo-countdown-label">SEK</span>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timerContainer = document.querySelector('.promo-timer-container');
            if(timerContainer) {
                const endTimeStr = timerContainer.getAttribute('data-endtime');
                // The input might be datetime-local which creates a string like "2024-04-01T23:59"
                const endTime = new Date(endTimeStr).getTime();
                
                const hoursBox = timerContainer.querySelector('.hours-box');
                const minsBox = timerContainer.querySelector('.mins-box');
                const secsBox = timerContainer.querySelector('.secs-box');

                if(!isNaN(endTime)) {
                    const updateTimer = () => {
                        const now = new Date().getTime();
                        const distance = endTime - now;

                        if (distance < 0) {
                            hoursBox.innerHTML = "00";
                            minsBox.innerHTML = "00";
                            secsBox.innerHTML = "00";
                            return;
                        }

                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)) + Math.floor(distance / (1000 * 60 * 60 * 24)) * 24;
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        hoursBox.innerHTML = hours < 10 ? "0" + hours : hours;
                        minsBox.innerHTML = minutes < 10 ? "0" + minutes : minutes;
                        secsBox.innerHTML = seconds < 10 ? "0" + seconds : seconds;
                    };

                    updateTimer(); // Initial call
                    setInterval(updateTimer, 1000);
                }
            }
        });
    </script>
    @endif
</div>
@endif

<!-- Sidebar Overlay Menu -->
<div class="sidebar-overlay" id="sidebarOverlay">
    <div class="sidebar-close" id="sidebarClose">
        <i class="bi bi-x"></i>
    </div>
    <ul class="sidebar-menu">
        @if(count($menus) > 0)
            @foreach($menus as $menu)
                <li><a href="{{ $menu['url'] }}">{{ $menu['label'] }}</a></li>
            @endforeach
        @else
            <li><a href="{{ route('categories') }}">{{ __('Behandlungen') }}</a></li>
            <li><a href="{{ url('/#how-it-works') }}">{{ __('Wie es funktioniert') }}</a></li>
            <li><a href="{{ url('/about-us') }}">{{ __('Über uns') }}</a></li>
            <li><a href="{{ url('/#faq') }}">{{ __('Hilfe') }}</a></li>
        @endif
    </ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburgerMenu');
    const sidebar = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    const searchToggle = document.getElementById('searchToggle');
    const headerSearch = document.getElementById('headerSearch');

    if (hamburger && sidebar) {
        hamburger.addEventListener('click', () => {
            sidebar.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if (sidebarClose && sidebar) {
        sidebarClose.addEventListener('click', () => {
            sidebar.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // Toggle search bar expansion
    if (searchToggle && headerSearch) {
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            headerSearch.classList.toggle('active');
            if (headerSearch.classList.contains('active')) {
                headerSearch.querySelector('input').focus();
            }
        });

        // Close search if clicking outside
        document.addEventListener('click', function(e) {
            if (!headerSearch.contains(e.target) && !searchToggle.contains(e.target)) {
                headerSearch.classList.remove('active');
            }
        });
    }
});
</script>
