@php
    $marquees = json_decode($setting->website_header_top_marquee, true) ?: [];
    $menus = json_decode($setting->website_header_sidebar_menu, true) ?: [];
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

        <!-- Search Bar -->
        @if($setting->website_header_search)
        <div class="header-search">
            <form action="{{ route('categories') }}" method="GET">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="{{ __('Suche...') }}">
            </form>
        </div>
        @endif

        <!-- Actions -->
        <div class="header-actions">
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
            <div class="header-icon" id="sidebar-toggle">
                <i class="bi bi-list"></i>
            </div>
            @endif
        </div>
    </div>
</header>

<!-- Sidebar Overlay Menu -->
<div class="sidebar-overlay" id="sidebar-menu">
    <div class="sidebar-close" id="sidebar-close">
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
        const toggle = document.getElementById('sidebar-toggle');
        const close = document.getElementById('sidebar-close');
        const menu = document.getElementById('sidebar-menu');

        if (toggle && menu) {
            toggle.addEventListener('click', () => {
                menu.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }

        if (close && menu) {
            close.addEventListener('click', () => {
                menu.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
    });
</script>
