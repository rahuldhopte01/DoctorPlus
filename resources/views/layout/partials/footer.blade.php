@php
    $footerSettings = json_decode($setting->website_footer_settings, true) ?: [];
    
    // New Trust Bar dynamic points
    $trustBar = $footerSettings['trust_bar'] ?? [];
    
    // Legacy fallback
    $tickerItems = $footerSettings['ticker'] ?? ['100% DSGVO-konform', 'Deutsche Server', 'Deutsche Ärzte', 'Express in 1-2 Tagen'];
    $tickerIcons = ['fa-shield-halved', 'fa-server', 'fa-user-doctor', 'fa-truck-fast'];
    
    $brandDesc = $footerSettings['desc'] ?? 'Deutschlands moderne Telemedizin-Plattform. Zertifizierte Medikamente, deutsche Ärzte, diskret zu Ihnen nach Hause.';
    $brandAddr = $footerSettings['address'] ?? 'dr.fuxx GmbH · Berlin, Deutschland';
    $brandEmail = !empty($footerSettings['email']) ? $footerSettings['email'] : ($setting->email ?? 'info@drfuxx.de');
    $cols = $footerSettings['columns'] ?? [
        ['title' => 'Behandlungen', 'links' => [['name' => 'Med. Cannabis', 'url' => '#'], ['name' => 'Erektionsstörungen', 'url' => '#']]],
        ['title' => 'Service', 'links' => [['name' => 'FAQ (Hilfe)', 'url' => '#'], ['name' => 'Versand', 'url' => '#']]],
        ['title' => 'Rechtliches', 'links' => [['name' => 'AGB', 'url' => '#'], ['name' => 'Datenschutz', 'url' => '#']]]
    ];
    $disclaimer = $footerSettings['disclaimer'] ?? 'dr.fuxx ist eine Vermittlungsplattform – keine Internetapotheke und kein Ersatz für ärztliche Beratung.';
    $bottomInfo = $footerSettings['bottom_info'] ?? 'dr.fuxx GmbH · Berlin, Deutschland info@drfuxx.de';
@endphp

<!-- Top Ticker Bar (Footer Trust Bar) -->
<div class="ticker-bar-v2">
    <div class="ticker-inner-v2">
        @if(!empty($trustBar))
            @foreach($trustBar as $point)
                <div class="ticker-item-v2">
                    <i class="{{ strpos($point['icon'], 'fa-') === false ? 'bi ' . $point['icon'] : 'fa-solid ' . $point['icon'] }}"></i>
                    <span>{{ $point['text'] }}</span>
                </div>
            @endforeach
        @else
            @foreach($tickerItems as $index => $item)
                @if(!empty($item))
                <div class="ticker-item-v2">
                    <i class="fa-solid {{ $tickerIcons[$index] ?? 'fa-check-circle' }}"></i>
                    <span>{{ $item }}</span>
                </div>
                @endif
            @endforeach
        @endif
    </div>
</div>

<footer class="footer-v2">
    <div class="footer-inner-v2">
        <!-- Brand Column -->
        <div class="footer-brand-v2">
            <a href="{{ url('/') }}" class="footer-logo">
                @if(!empty($footerSettings['logo']) && file_exists(public_path('images/upload/'.$footerSettings['logo'])))
                    <img src="{{ url('images/upload/'.$footerSettings['logo']) }}" width="140" alt="Logo">
                @elseif($setting->company_white_logo && file_exists(public_path('images/upload/'.$setting->company_white_logo)))
                    <img src="{{ url('images/upload/'.$setting->company_white_logo) }}" width="140" alt="Logo">
                @else
                    <img src="{{ url('/images/upload_empty/logo_white.png') }}" width="140" alt="Logo">
                @endif
            </a>
            <p>{{ $brandDesc }}</p>
            
            <div class="footer-social-v2">
                @if($setting->facebook_url)
                    <a href="{{ $setting->facebook_url }}" class="soc-icon-v2" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                @endif
                @if($setting->twitter_url)
                    <a href="{{ $setting->twitter_url }}" class="soc-icon-v2" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>
                @endif
                @if($setting->instagram_url)
                    <a href="{{ $setting->instagram_url }}" class="soc-icon-v2" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                @endif
                @if($setting->linkdin_url)
                    <a href="{{ $setting->linkdin_url }}" class="soc-icon-v2" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
                @endif
            </div>

            <div class="footer-contact-v2">
                {!! nl2br(e($brandAddr)) !!}<br>
                <a href="mailto:{{ $brandEmail }}">{{ $brandEmail }}</a>
            </div>
        </div>

        <!-- Dynamic Columns -->
        @foreach($cols as $col)
        <div class="footer-col-v2">
            <h4>{{ $col['title'] ?? '' }}</h4>
            <ul>
                @foreach($col['links'] ?? [] as $link)
                <li><a href="{{ $link['url'] ?? '#' }}">{{ $link['name'] ?? $link['label'] ?? '' }}</a></li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>

    <!-- Bottom Disclaimer & Copyright -->
    <div class="footer-bottom-v2">
        <div class="footer-disclaimer-v2">
            {{ $disclaimer }}
        </div>
        <div class="footer-bottom-info-v2" style="font-size: 0.78rem; color: rgba(255, 255, 255, 0.4); margin-bottom: 10px;">
            {{ $bottomInfo }}
        </div>
        <div class="footer-copyright-v2">
            @if(!empty($footerSettings['copy']))
                {{ $footerSettings['copy'] }}
            @else
                © {{ date('Y') }} {{ __($setting->business_name) }}. Alle Rechte vorbehalten.
            @endif
        </div>
    </div>
</footer>
