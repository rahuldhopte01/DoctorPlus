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

            @if(auth()->check())
            <a href="javascript:void(0)" class="header-icon" onclick="document.getElementById('website-logout-form').submit();" title="{{ __('Logout') }}" style="color:inherit;">
                <i class="bi bi-box-arrow-right"></i>
            </a>
            <form id="website-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
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
<style>
    .sidebar-overlay {
        position: fixed;
        top: 0;
        right: -400px;
        width: 100%;
        max-width: 400px;
        height: 100%;
        background: #fff;
        z-index: 2000;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: -10px 0 30px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        padding: 24px;
        font-family: 'Inter', sans-serif;
    }
    .sidebar-overlay.active {
        right: 0;
    }
    .sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .sidebar-header h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
    }
    .sidebar-close {
        width: 36px;
        height: 36px;
        background: #f3f0ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .sidebar-close:hover {
        background: #e7e0ff;
        transform: scale(1.05);
    }
    .sidebar-close i {
        font-size: 1.5rem;
        color: #1a1a1a;
    }

    /* Toggle Buttons */
    .sidebar-toggles {
        display: flex;
        gap: 12px;
        margin-bottom: 32px;
    }
    .toggle-btn {
        flex: 1;
        padding: 12px;
        border-radius: 30px;
        border: 1px solid #eee;
        background: #fff;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
    }
    .toggle-btn.active {
        background: #7b42f6;
        color: #fff;
        border-color: #7b42f6;
        box-shadow: 0 4px 15px rgba(123, 66, 246, 0.2);
    }

    /* Menu List */
    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        margin-right: -10px;
        padding-right: 10px;
    }
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .menu-section-header {
        color: #999;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin: 24px 0 16px 0;
        padding-left: 4px;
    }
    .sidebar-menu li {
        margin-bottom: 8px;
    }
    .sidebar-menu a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        color: #1a1a1a;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.05rem;
        border-radius: 12px;
        transition: all 0.2s;
    }
    .sidebar-menu a:hover {
        background: #f8f6ff;
        color: #7b42f6;
    }
    .menu-label-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .badge-neu {
        background: #7b42f6;
        color: #fff;
        font-size: 0.6rem;
        font-weight: 900;
        padding: 2px 8px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .menu-arrow {
        color: #ccc;
        font-size: 0.9rem;
        transition: transform 0.2s;
    }
    .sidebar-menu a:hover .menu-arrow {
        transform: translateX(4px);
        color: #7b42f6;
    }

    /* Mobile adjustments */
    @media (max-width: 480px) {
        .sidebar-overlay {
            max-width: 100%;
        }
    }

    /* Submenu Styles */
    .has-submenu .menu-arrow {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .has-submenu.open .menu-arrow {
        transform: rotate(90deg);
        color: #7b42f6;
    }
    .sidebar-submenu {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 0;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: #f9f9ff;
        border-radius: 0 0 12px 12px;
        opacity: 0;
    }
    .has-submenu.open + .sidebar-submenu {
        max-height: 2000px; /* Allow for many subcategories */
        margin-bottom: 12px;
        opacity: 1;
        padding: 8px 0;
    }
    .sidebar-submenu li {
        margin-bottom: 2px;
    }
    .sidebar-submenu li a {
        padding: 10px 16px 10px 48px !important;
        font-size: 0.95rem !important;
        font-weight: 500 !important;
        color: #666 !important;
        background: transparent !important;
    }
    .sidebar-submenu li a:hover {
        color: #7b42f6 !important;
        background: #f1eeff !important;
    }
</style>

<div class="sidebar-overlay" id="sidebarOverlay">
    <div class="sidebar-header">
        <h2>{{ __('Menu') }}</h2>
        <div class="sidebar-close" id="sidebarClose">
            <i class="bi bi-x"></i>
        </div>
    </div>

    <div class="sidebar-toggles">
        <div class="toggle-btn active" data-type="rezept">{{ __('Mit Rezept') }}</div>
        <div class="toggle-btn" data-type="no-rezept">{{ __('Ohne Rezept') }}</div>
    </div>

    <div class="sidebar-content">
        <ul class="sidebar-menu">
            <!-- 1. Database Hierarchical Categories (Top-Kategorien) -->
            @if(count($sidebar_treatments) > 0)
                <li class="menu-section-header">{{ __('TOP-KATEGORIEN') }}</li>
                @foreach($sidebar_treatments as $treatment)
                    @if(count($treatment->category) > 0)
                        <li>
                            <a href="javascript:void(0)" class="has-submenu">
                                <span class="menu-label-wrapper">
                                    {{ $treatment->name }}
                                </span>
                                <i class="bi bi-chevron-right menu-arrow"></i>
                            </a>
                            <ul class="sidebar-submenu">
                                @foreach($treatment->category as $subcat)
                                    <li>
                                        <a href="{{ route('category.detail', $subcat->id) }}">
                                            {{ $subcat->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('categories', ['treatment' => $treatment->id]) }}">
                                <span class="menu-label-wrapper">{{ $treatment->name }}</span>
                                <i class="bi bi-chevron-right menu-arrow"></i>
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif

            <!-- 2. Dynamic Pages (Manual Sidebar Menus & Footer Links) -->
            <li class="menu-section-header">{{ __('ENTDECKEN') }}</li>
            
            <!-- Manual Sidebar Menus from Settings -->
            @if(count($menus) > 0)
                @foreach($menus as $menu)
                    @if(($menu['type'] ?? 'link') == 'section')
                        {{-- Skip manual sections in the bottom to keep it clean --}}
                    @else
                        <li>
                            <a href="{{ $menu['url'] }}">
                                <span class="menu-label-wrapper">
                                    {{ $menu['label'] }}
                                    @if(!empty($menu['badge']))
                                        <span class="badge-neu">{{ $menu['badge'] }}</span>
                                    @endif
                                </span>
                                <i class="bi bi-chevron-right menu-arrow"></i>
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif

            <!-- Footer Dynamic Pages (Service, Legal, etc.) -->
            @foreach($sidebar_footer_cols as $index => $col)
                @if($index > 0) {{-- Skip the first column if it's "Behandlungen" to avoid duplication --}}
                    @foreach($col['links'] ?? [] as $link)
                        <li>
                            <a href="{{ $link['url'] ?? '#' }}">
                                <span class="menu-label-wrapper">{{ $link['name'] ?? $link['label'] ?? '' }}</span>
                                <i class="bi bi-chevron-right menu-arrow"></i>
                            </a>
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Fallback --}}
            @if(count($menus) == 0 && count($sidebar_treatments) == 0 && count($sidebar_footer_cols) == 0)
                <li><a href="{{ route('categories') }}"><span class="menu-label-wrapper">{{ __('Behandlungen') }}</span> <i class="bi bi-chevron-right menu-arrow"></i></a></li>
                <li><a href="{{ url('/about-us') }}"><span class="menu-label-wrapper">{{ __('Über uns') }}</span> <i class="bi bi-chevron-right menu-arrow"></i></a></li>
                <li><a href="{{ url('/#faq') }}"><span class="menu-label-wrapper">{{ __('Hilfe') }}</span> <i class="bi bi-chevron-right menu-arrow"></i></a></li>
            @endif
        </ul>
    </div>
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

    // Sidebar Toggles
    const toggleBtns = document.querySelectorAll('.toggle-btn');
    if (toggleBtns) {
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                toggleBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });
    }

    // Submenu Toggles
    const submenuTriggers = document.querySelectorAll('.has-submenu');
    submenuTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.parentElement;
            const isOpen = this.classList.contains('open');
            
            // Close other open submenus at same level
            // parent.parentElement.querySelectorAll('.has-submenu.open').forEach(opened => {
            //     if (opened !== this) opened.classList.remove('open');
            // });

            this.classList.toggle('open');
        });
    });
});
</script>
