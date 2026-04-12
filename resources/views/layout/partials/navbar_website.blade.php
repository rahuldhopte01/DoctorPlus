@php
    $marquees = json_decode($setting->website_header_top_marquee, true) ?: [];
    $menus = json_decode($setting->website_header_sidebar_menu, true) ?: [];
    $promo = json_decode($setting->website_header_promo_bar, true) ?: [];
@endphp


<!-- Top Header Marquee -->
@if(count($marquees) > 0)
<div class="top-header-marquee">
    @for ($i = 0; $i < 2; $i++)
    <div class="marquee-content" {!! $i > 0 ? 'aria-hidden="true"' : '' !!}>
        @foreach($marquees as $item)
            <div class="marquee-item">
                @if(!empty($item['icon']))
                    @if(strpos($item['icon'], '.') !== false || strpos($item['icon'], '/') !== false)
                        <img src="{{ url('images/upload/'.$item['icon']) }}" alt="">
                    @else
                        <i class="{{ $item['icon'] }}"></i>
                    @endif
                @endif
                <span>{{ $item['text'] }}</span>
            </div>
        @endforeach
    </div>
    @endfor
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
            <div class="profile-dropdown-container">
                <a href="javascript:void(0)" class="header-icon" id="profileDropdownToggle">
                    <i class="bi bi-person"></i>
                </a>
            </div>
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
        background-color: #ffffff;
        color: var(--site_color, #7b42f6);
        font-family: 'Inter', sans-serif;
        position: relative;
        z-index: 2;
        border-bottom: 1px solid rgba(123, 66, 246, 0.05);
    }
    .fuxx-promo-bar .decor-icon {
        position: absolute;
        opacity: 0.08;
        color: var(--site_color, #7b42f6);
    }
    .promo-countdown-box {
        background-color: var(--site_color, #7b42f6) !important;
        color: #ffffff !important;
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
        color: var(--site_color, #7b42f6);
        opacity: 0.8;
    }
    @media (max-width: 768px) {
        .fuxx-promo-bar {
            padding-top: 10px !important;
            padding-bottom: 10px !important;
        }
        .fuxx-promo-bar .pe-3 {
            padding-right: 0 !important;
            margin-bottom: 8px;
            font-size: 0.88rem;
            line-height: 1.3;
        }
        .promo-countdown-box {
            min-width: 28px !important;
            font-size: 0.8rem !important;
        }
        .promo-timer-container {
            margin-left: 0 !important;
        }
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

    /* Search Suggestions Styles */
    .header-search {
        position: relative;
    }
    .search-suggestions-container {
        position: absolute;
        top: calc(100% + 14px);
        right: 0;
        left: auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.12);
        max-height: 400px;
        overflow-y: auto;
        z-index: 2000;
        display: none;
        padding: 14px;
        width: 300px; /* Matches .header-search.active .search-form width */
        border: 1px solid rgba(0,0,0,0.05);
    }
    @media (max-width: 768px) {
        .search-suggestions-container {
            width: calc(100vw - 30px);
            right: 15px;
            top: 60px;
            position: fixed;
        }
    }
    .search-suggestions-container.active {
        display: block;
    }
    .suggestion-item {
        display: block;
        padding: 9px 15px;
        color: #1a1a1a;
        text-decoration: none;
        transition: all 0.2s ease-out;
        border-radius: 30px;
        margin-bottom: 8px;
        font-size: 0.85rem;
        background: #f9f9f9;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 500;
        border: 1px solid #f0f0f0;
    }
    .suggestion-item:last-child {
        margin-bottom: 0;
    }
    .suggestion-item:hover {
        background: #fff;
        color: #7b42f6;
        border-color: #7b42f6;
        box-shadow: 0 4px 15px rgba(123, 66, 246, 0.1);
        transform: translateY(-2px);
    }
    .suggestion-item mark {
        background: none;
        color: #10b981; /* Green highlight like in reference */
        padding: 0;
        font-weight: 700;
    }
    .suggestion-type-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #bbb;
        margin: 15px 0 8px 14px;
        font-weight: 800;
    }
    .suggestion-type-label:first-child {
        margin-top: 5px;
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
            <!-- 1. TOP-KATEGORIEN -->
            @if(count($sidebar_top_items) > 0)
                <li class="menu-section-header">{{ __('TOP-KATEGORIEN') }}</li>
                @foreach($sidebar_top_items as $item)
                    <li>
                        <a href="{{ route('category.detail', $item->id) }}">
                            <span class="menu-label-wrapper">
                                {{ $item->sidebar_custom_title }}
                                @if($item->is_sidebar_new)
                                    <span class="badge-neu">{{ __('NEU') }}</span>
                                @endif
                            </span>
                            <i class="bi bi-chevron-right menu-arrow"></i>
                        </a>
                    </li>
                @endforeach
            @endif

            <!-- 2. ENTDECKEN -->
            @if(count($sidebar_entdecken_items) > 0)
                <li class="menu-section-header">{{ __('ENTDECKEN') }}</li>
                @foreach($sidebar_entdecken_items as $item)
                    <li>
                        <a href="{{ route('category.detail', $item->id) }}">
                            <span class="menu-label-wrapper">
                                {{ $item->sidebar_custom_title }}
                                @if($item->is_sidebar_new)
                                    <span class="badge-neu">{{ __('NEU') }}</span>
                                @endif
                            </span>
                            <i class="bi bi-chevron-right menu-arrow"></i>
                        </a>
                    </li>
                @endforeach
            @endif

            <!-- 3. LERNEN SIE DR.FUXX KENNEN -->
            @if(count($sidebar_lernen_items) > 0)
                <li class="menu-section-header">{{ __('LERNEN SIE DR.FUXX KENNEN') }}</li>
                @foreach($sidebar_lernen_items as $item)
                    <li>
                        <a href="{{ $item['url'] ?? '#' }}">
                            <span class="menu-label-wrapper">{{ $item['label'] ?? '' }}</span>
                            <i class="bi bi-chevron-right menu-arrow"></i>
                        </a>
                    </li>
                @endforeach
            @endif

            {{-- Fallback --}}
            @if(count($sidebar_top_items) == 0 && count($sidebar_entdecken_items) == 0 && count($sidebar_lernen_items) == 0)
                <li><a href="{{ route('categories') }}"><span class="menu-label-wrapper">{{ __('Behandlungen') }}</span> <i class="bi bi-chevron-right menu-arrow"></i></a></li>
                <li><a href="{{ url('/about-us') }}"><span class="menu-label-wrapper">{{ __('Über uns') }}</span> <i class="bi bi-chevron-right menu-arrow"></i></a></li>
            @endif
        </ul>
    </div>
</div>

{{-- Moved Profile Dropdown to Global Scope --}}
@if($setting->website_header_user)
<div class="profile-dropdown" id="profileDropdown">
    <div class="dropdown-header">
        <div class="dropdown-close" id="profileDropdownClose">
            <i class="bi bi-x"></i>
        </div>
        <div class="dropdown-avatar">
            @if(auth()->check() && auth()->user()->image)
                <img src="{{ url('images/upload/'.auth()->user()->image) }}" alt="">
            @else
                <i class="bi bi-person-fill"></i>
            @endif
        </div>
        @if(auth()->check())
            <p class="dropdown-header-text">Hallo, {{ auth()->user()->name }}</p>
        @else
            <p class="dropdown-header-text">Bitte einloggen/registrieren um Mein Konto zu sehen</p>
        @endif
    </div>
    <div class="dropdown-body">
        @if(auth()->check())
            <ul class="profile-links">
                <li><a href="{{ url('user_profile') }}"><i class="bi bi-person-circle"></i> {{ __('Mein Profil') }}</a></li>
                {{-- <li><a href="{{ url('user_orders') }}"><i class="bi bi-box-seam"></i> {{ __('Meine Bestellungen') }}</a></li> --}}
            </ul>
            <button class="dropdown-btn-logout" onclick="document.getElementById('website-logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> {{ __('Logout') }}
            </button>
            <form id="website-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        @else
            <a href="{{ url('patient-login') }}" class="dropdown-btn-green">
                <i class="bi bi-person"></i> {{ __('Anmelden') }}
            </a>
        @endif
    </div>
    @if(!auth()->check())
    <div class="dropdown-footer">
        <a href="{{ url('patient-register') }}" class="dropdown-footer-link">{{ __('Sie haben kein Konto? Hier registrieren') }}</a>
    </div>
    @endif
</div>
@endif

<!-- Bottom Fixed Overlay for Mobile/Sidebar -->
<div class="fuxx-global-overlay" id="globalOverlay"></div>

@php
    $badgeSett = isset($setting->website_badge_settings) ? json_decode($setting->website_badge_settings, true) : [];
@endphp

@if((Request::is('/') || Request::is('home_new')) && !empty($badgeSett['title']))
<div class="made-in-germany-badge" id="stickyBadgeInst">
    <img src="{{ url('images/germany-flag.svg') }}" alt="DE" class="badge-flag" onerror="this.src='https://upload.wikimedia.org/wikipedia/en/b/ba/Flag_of_Germany.svg';">
    <div class="badge-text-group">
        <div class="badge-title">{{ $badgeSett['title'] }}</div>
        @if(!empty($badgeSett['points']))
        <div class="badge-points-horizontal">
            @php $pCount = 0; @endphp
            @foreach($badgeSett['points'] as $point)
                @if(!empty($point))
                    @if($pCount > 0) <span class="point-sep">•</span> @endif
                    <span class="badge-point">{{ $point }}</span>
                    @php $pCount++; @endphp
                @endif
            @endforeach
        </div>
        @endif
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburgerMenu');
    const sidebar = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    const searchToggle = document.getElementById('searchToggle');
    const headerSearch = document.getElementById('headerSearch');
    const globalOverlay = document.getElementById('globalOverlay');
    
    // Toggle Profile Dropdown
    const profileToggle = document.getElementById('profileDropdownToggle');
    const profileDropdown = document.getElementById('profileDropdown');
    const profileClose = document.getElementById('profileDropdownClose');

    const toggleOverlay = (show) => {
        if (show) {
            globalOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        } else {
            globalOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    const closeAllMenus = () => {
        if (sidebar) sidebar.classList.remove('active');
        if (profileDropdown) profileDropdown.classList.remove('active');
        toggleOverlay(false);
    };

    if (hamburger && sidebar) {
        hamburger.addEventListener('click', () => {
            closeAllMenus(); // Close profile if open
            sidebar.classList.add('active');
            toggleOverlay(true);
        });
    }

    if (sidebarClose || globalOverlay) {
        const closers = [sidebarClose, globalOverlay];
        closers.forEach(closer => {
            if (closer) {
                closer.addEventListener('click', () => {
                    closeAllMenus();
                });
            }
        });
    }

    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (window.innerWidth <= 768) {
                if (!profileDropdown.classList.contains('active')) {
                    closeAllMenus(); // Close sidebar if open
                    profileDropdown.classList.add('active');
                    toggleOverlay(true);
                } else {
                    closeAllMenus();
                }
            } else {
                profileDropdown.classList.toggle('active');
            }
        });

        if (profileClose) {
            profileClose.addEventListener('click', function() {
                closeAllMenus();
            });
        }
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
            this.classList.toggle('open');
        });
    });

    // --- Search Suggestions Logic ---
    const searchWrapper = document.getElementById('headerSearch');
    if (searchWrapper) {
        const searchInput = searchWrapper.querySelector('input');
        const suggestionsContainer = document.createElement('div');
        suggestionsContainer.id = 'searchSuggestions';
        suggestionsContainer.className = 'search-suggestions-container';
        searchWrapper.appendChild(suggestionsContainer);

        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                suggestionsContainer.classList.remove('active');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            renderSuggestions(data, query, suggestionsContainer);
                            suggestionsContainer.classList.add('active');
                        } else {
                            suggestionsContainer.classList.remove('active');
                        }
                    })
                    .catch(err => console.error('Search error:', err));
            }, 300);
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                suggestionsContainer.classList.remove('active');
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchWrapper.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.classList.remove('active');
            }
        });
    }

    function renderSuggestions(data, query, container) {
        container.innerHTML = '';
        
        const categories = data.filter(i => i.type === 'category');
        const medicines = data.filter(i => i.type === 'medicine');

        if (categories.length > 0) {
            const label = document.createElement('div');
            label.className = 'suggestion-type-label';
            label.textContent = 'Kategorien';
            container.appendChild(label);
            categories.forEach(item => addItem(item, query, container));
        }

        if (medicines.length > 0) {
            const label = document.createElement('div');
            label.className = 'suggestion-type-label';
            label.textContent = 'Produkte';
            container.appendChild(label);
            medicines.forEach(item => addItem(item, query, container));
        }
    }

    function addItem(item, query, container) {
        const a = document.createElement('a');
        a.href = item.url;
        a.className = 'suggestion-item';
        
        // Highlight logic (case-insensitive)
        const regex = new RegExp(`(${escapeRegExp(query)})`, 'gi');
        const highlighted = item.label.replace(regex, '<mark>$1</mark>');
        a.innerHTML = highlighted;
        
        container.appendChild(a);
    }

    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
});
</script>
