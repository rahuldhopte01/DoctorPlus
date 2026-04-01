@php $setting = $setting ?? App\Models\Setting::first(); @endphp

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
