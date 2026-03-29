@php
    $footer = json_decode($setting->website_footer_settings ?? '{}', true) ?? [];
    $footer_cols = $footer['columns'] ?? [];
@endphp

<footer class="footer" id="footer">
    <div class="footer-inner">

        {{-- Brand Column --}}
        <div class="footer-brand">
            <a href="{{ url('/') }}" class="logo">
                @if(!empty($setting->company_white_logo) && file_exists(public_path('images/upload/'.$setting->company_white_logo)))
                    <img src="{{ $setting->companyWhite }}" alt="{{ $setting->business_name }}" class="logo-img" style="height:70px;width:auto;display:block;object-fit:contain;">
                @elseif(!empty($setting->company_logo) && file_exists(public_path('images/upload/'.$setting->company_logo)))
                    <img src="{{ asset('images/upload/'.$setting->company_logo) }}" alt="{{ $setting->business_name }}" class="logo-img" style="height:70px;width:auto;display:block;object-fit:contain;filter:invert(1);">
                @else
                    <img src="{{ url('/images/upload_empty/logo_white.png') }}" alt="{{ $setting->business_name }}" class="logo-img" style="height:70px;width:auto;display:block;object-fit:contain;">
                @endif
            </a>

            <div class="footer-addr">
                {{ $setting->business_name }}<br>
                @if($setting->phone)
                    Telefon: <a href="tel:{{ $setting->phone }}">{{ $setting->phone }}</a><br>
                @endif
                @if($setting->email)
                    E-Mail: <a href="mailto:{{ $setting->email }}">{{ $setting->email }}</a>
                @endif
            </div>

            <div class="footer-social">
                <a href="{{ $footer['facebook'] ?? $setting->facebook_url ?? '#' }}" target="_blank" class="soc-icon" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                </a>
                <a href="{{ $footer['twitter'] ?? $setting->twitter_url ?? '#' }}" target="_blank" class="soc-icon" aria-label="Twitter">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733-16zM4 20l6.768-6.768M13.232 10.232L20 4"/></svg>
                </a>
                <a href="{{ $footer['instagram'] ?? $setting->instagram_url ?? '#' }}" target="_blank" class="soc-icon" aria-label="Instagram">
                    <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="white" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="white" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.5" fill="white"/></svg>
                </a>
                <a href="{{ $footer['linkedin'] ?? $setting->linkdin_url ?? '#' }}" target="_blank" class="soc-icon" aria-label="LinkedIn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                </a>
            </div>
        </div>

        {{-- Dynamic CMS columns --}}
        @if(count($footer_cols) > 0)
            @foreach($footer_cols as $col)
                <div class="footer-col">
                    <h4>{{ $col['title'] ?? '' }}</h4>
                    <ul>
                        @foreach($col['links'] ?? [] as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        @else
            {{-- Fallback: 3 hardcoded columns from drfuxx design --}}
            <div class="footer-col">
                <h4>Unser Service</h4>
                <ul>
                    <li><a href="#">So funktioniert es</a></li>
                    <li><a href="#">Behandlungen</a></li>
                    <li><a href="#">Hersteller</a></li>
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
                    <li><a href="#">Serviceeinschr&auml;nkungen</a></li>
                    <li><a href="#">Versand</a></li>
                    <li><a href="#">Zahlungsm&ouml;glichkeiten</a></li>
                    <li><a href="#">Cookieeinstellungen</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>&Uuml;ber {{ $setting->business_name }}</h4>
                <ul>
                    <li><a href="{{ url('about-us') }}">&Uuml;ber uns</a></li>
                    <li><a href="#">Medizinischer Beirat</a></li>
                    <li><a href="#">Werde Partner</a></li>
                    <li><a href="#">Werde Affiliate</a></li>
                    <li><a href="#">Werde Influencer</a></li>
                    <li><a href="#">Presse</a></li>
                    <li><a href="#">Impressum</a></li>
                    <li><a href="{{ url('contact-us') }}">Kontakt</a></li>
                </ul>
            </div>
        @endif

    </div>

    <div class="footer-bottom">
        <p>dr.fuxx ist eine Vermittlungsplattform &ndash; keine Internetapotheke und kein Ersatz f&uuml;r &auml;rztliche Beratung.</p>
        <p style="margin-top:8px;">{{ $footer['copy'] ?? ('&copy; '.date('Y').' '.$setting->business_name) }}</p>
    </div>
</footer>
