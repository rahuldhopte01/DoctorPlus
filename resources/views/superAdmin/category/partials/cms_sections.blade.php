{{--
  CMS Sections partial — included in both create and edit category forms.
  Expects: $cms (array|null) — the category's existing cms_sections data.
  Uses Bootstrap 4 accordion (data-toggle / data-target).
--}}
@php
    $cms   = $cms ?? [];
    $hero  = $cms['hero'] ?? [];
    $fb    = $cms['features_bar'] ?? [];
    $steps = $cms['steps'] ?? [];
    $pay   = $cms['payment_bar'] ?? [];

    $defaultFeatures = [
        ['title' => 'Das Rezept wird online ausgestellt.',      'subtitle' => 'Ein Klinikbesuch ist nicht erforderlich.'],
        ['title' => 'Lieferung innerhalb von 1–2 Werktagen.',   'subtitle' => 'Schnelle, zuverlässige Lieferung.'],
        ['title' => 'Originalmedizin und Generika.',            'subtitle' => 'Aus zertifizierten Apotheken.'],
        ['title' => 'Beratung über Online-Fragebogen.',         'subtitle' => 'Schnelle medizinische Beratung'],
    ];
    $fbFeatures = $fb['features'] ?? $defaultFeatures;

    $defaultSteps = [
        ['title_plain' => 'Füllen Sie den',  'title_highlighted' => 'medizinischen Fragebogen aus', 'highlight_color' => '#3b6fd4', 'description' => 'Starten Sie die Online-Konsultation und beantworten Sie die medizinischen Fragen.',    'image' => null],
        ['title_plain' => 'Wählen Sie die',  'title_highlighted' => 'gewünschte Behandlung',        'highlight_color' => '#3b6fd4', 'description' => 'Der behandelnde Arzt prüft Ihre Angaben und stellt Ihnen bei Bedarf ein Rezept aus.', 'image' => null],
        ['title_plain' => 'Lieferung in',    'title_highlighted' => '1–2 Werktagen',                'highlight_color' => '#3b6fd4', 'description' => 'Sie erhalten Ihre Medikamente diskret und sicher.',                                    'image' => null],
    ];
    $stepsData = $steps['steps'] ?? $defaultSteps;

    $payMethods = $pay['methods'] ?? ['klarna'=>true,'visa'=>true,'maestro'=>true,'gpay'=>true,'apple_pay'=>true,'paypal'=>true];
@endphp

<style>
    .cms-panel-header .btn-link {
        font-weight: 600;
        font-size: 1rem;
        color: #343a40;
        text-decoration: none;
    }
    .cms-panel-header .btn-link:hover { color: #007bff; }
    .cms-panel-header .btn-link::before {
        content: '▸ ';
        font-size: 0.8em;
        transition: transform 0.2s;
    }
    .cms-panel-header .btn-link[aria-expanded="true"]::before { content: '▾ '; }
    .cms-color-row { display: flex; align-items: center; gap: 8px; }
    .cms-color-row input[type="color"] { width: 44px; height: 34px; padding: 2px 3px; border-radius: 4px; cursor: pointer; }
    .cms-color-row .color-hex { width: 90px; }
    .cms-toggle-label { display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; margin-bottom: 0; }
    .cms-toggle-label input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
</style>

<div class="card mt-4">
    <div class="card-header d-flex align-items-center justify-content-between" style="background:#e85d04;">
        <h5 class="mb-0" style="color:#fff;">Page Sections CMS</h5>
        <small style="color:rgba(255,255,255,0.8);">Control which sections appear on the public category page and customise their content</small>
    </div>
    <div class="card-body p-0">
        <div id="cmsSectionsAccordion">

            {{-- ============================
                 1. HERO SECTION
            ============================ --}}
            <div class="card mb-0 border-0 border-bottom rounded-0">
                <div class="card-header cms-panel-header" id="headingHero" style="background:#f8f9fa;">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button"
                                data-toggle="collapse" data-target="#collapseHero"
                                aria-expanded="false" aria-controls="collapseHero">
                            <i class="fas fa-image mr-2 text-primary"></i> Hero Section
                        </button>
                    </h2>
                </div>
                <div id="collapseHero" class="collapse" data-parent="#cmsSectionsAccordion">
                    <div class="card-body">
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[hero][enabled]" value="1" {{ ($hero['enabled'] ?? true) ? 'checked' : '' }}>
                            Show Hero Section
                        </label>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>CTA Button Text</label>
                                <input type="text" class="form-control" name="sections[hero][cta_text]"
                                       value="{{ $hero['cta_text'] ?? 'Zu den medizinischen Fragen' }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>CTA Button Color</label>
                                <div class="cms-color-row">
                                    <input type="color" name="sections[hero][cta_color]"
                                           value="{{ $hero['cta_color'] ?? '#3b6fd4' }}" class="cms-color-picker">
                                    <input type="text" class="form-control color-hex"
                                           value="{{ $hero['cta_color'] ?? '#3b6fd4' }}">
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Consultation Fee (€)</label>
                                <input type="text" class="form-control" name="sections[hero][consultation_fee]"
                                       value="{{ $hero['consultation_fee'] ?? '29' }}">
                            </div>
                        </div>

                        <hr>
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[hero][badge_enabled]" value="1" {{ ($hero['badge_enabled'] ?? true) ? 'checked' : '' }}>
                            Show Improvement Badge (85%)
                        </label>
                        <div class="row">
                            <div class="col-md-2 form-group">
                                <label>Badge %</label>
                                <input type="text" class="form-control" name="sections[hero][badge_percentage]"
                                       value="{{ $hero['badge_percentage'] ?? '85' }}">
                            </div>
                            <div class="col-md-5 form-group">
                                <label>Badge Body Text</label>
                                <input type="text" class="form-control" name="sections[hero][badge_text]"
                                       value="{{ $hero['badge_text'] ?? 'der Männer berichten von einer Besserung' }}">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Badge Color 1</label>
                                <div class="cms-color-row">
                                    <input type="color" name="sections[hero][badge_bg_color_start]"
                                           value="{{ $hero['badge_bg_color_start'] ?? '#3b6fd4' }}" class="cms-color-picker">
                                    <input type="text" class="form-control color-hex"
                                           value="{{ $hero['badge_bg_color_start'] ?? '#3b6fd4' }}">
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Badge Color 2 (gradient end)</label>
                                <div class="cms-color-row">
                                    <input type="color" name="sections[hero][badge_bg_color_end]"
                                           value="{{ $hero['badge_bg_color_end'] ?? '#1e3c8c' }}" class="cms-color-picker">
                                    <input type="text" class="form-control color-hex"
                                           value="{{ $hero['badge_bg_color_end'] ?? '#1e3c8c' }}">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[hero][rating_enabled]" value="1" {{ ($hero['rating_enabled'] ?? true) ? 'checked' : '' }}>
                            Show Star Rating
                        </label>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Rating Value (e.g. 4,79)</label>
                                <input type="text" class="form-control" name="sections[hero][rating_value]"
                                       value="{{ $hero['rating_value'] ?? '4,79' }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Review Count (e.g. 14.082)</label>
                                <input type="text" class="form-control" name="sections[hero][rating_count]"
                                       value="{{ $hero['rating_count'] ?? '14.082' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================
                 2. FEATURES BAR
            ============================ --}}
            <div class="card mb-0 border-0 border-bottom rounded-0">
                <div class="card-header cms-panel-header" id="headingFeatures" style="background:#f8f9fa;">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button"
                                data-toggle="collapse" data-target="#collapseFeatures"
                                aria-expanded="false" aria-controls="collapseFeatures">
                            <i class="fas fa-list-ul mr-2 text-success"></i> Features Bar
                        </button>
                    </h2>
                </div>
                <div id="collapseFeatures" class="collapse" data-parent="#cmsSectionsAccordion">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3" style="gap:24px;">
                            <label class="cms-toggle-label mb-0">
                                <input type="checkbox" name="sections[features_bar][enabled]" value="1" {{ ($fb['enabled'] ?? true) ? 'checked' : '' }}>
                                Show Features Bar
                            </label>
                            <div class="cms-color-row">
                                <label class="mb-0 text-muted" style="font-size:0.85rem;">Background Color</label>
                                <input type="color" name="sections[features_bar][bg_color]"
                                       value="{{ $fb['bg_color'] ?? '#fafafa' }}" class="cms-color-picker">
                                <input type="text" class="form-control color-hex"
                                       value="{{ $fb['bg_color'] ?? '#fafafa' }}">
                            </div>
                        </div>
                        @foreach($defaultFeatures as $i => $default)
                        @php $feat = $fbFeatures[$i] ?? $default; @endphp
                        <div class="card card-body mb-3" style="background:#f8f9fa;">
                            <label class="cms-toggle-label mb-2">
                                <input type="checkbox" name="sections[features_bar][features][{{ $i }}][enabled]" value="1"
                                       {{ ($feat['enabled'] ?? true) ? 'checked' : '' }}>
                                Feature {{ $i + 1 }}
                            </label>
                            <div class="row">
                                <div class="col-md-6 form-group mb-0">
                                    <label class="text-muted" style="font-size:0.8rem;">Title</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="sections[features_bar][features][{{ $i }}][title]"
                                           value="{{ $feat['title'] ?? $default['title'] }}">
                                </div>
                                <div class="col-md-6 form-group mb-0">
                                    <label class="text-muted" style="font-size:0.8rem;">Subtitle</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="sections[features_bar][features][{{ $i }}][subtitle]"
                                           value="{{ $feat['subtitle'] ?? $default['subtitle'] }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ============================
                 3. 3 STEPS SECTION
            ============================ --}}
            <div class="card mb-0 border-0 border-bottom rounded-0">
                <div class="card-header cms-panel-header" id="headingSteps" style="background:#f8f9fa;">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button"
                                data-toggle="collapse" data-target="#collapseSteps"
                                aria-expanded="false" aria-controls="collapseSteps">
                            <i class="fas fa-list-ol mr-2 text-warning"></i> 3 Steps Section
                        </button>
                    </h2>
                </div>
                <div id="collapseSteps" class="collapse" data-parent="#cmsSectionsAccordion">
                    <div class="card-body">
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[steps][enabled]" value="1" {{ ($steps['enabled'] ?? true) ? 'checked' : '' }}>
                            Show Steps Section
                        </label>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Section Main Title</label>
                                <input type="text" class="form-control" name="sections[steps][section_title]"
                                       value="{{ $steps['section_title'] ?? '3 einfache Schritte' }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Section Subtitle (italic line)</label>
                                <input type="text" class="form-control" name="sections[steps][section_subtitle]"
                                       value="{{ $steps['section_subtitle'] ?? '100 % online' }}">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Subtitle Color</label>
                                <div class="cms-color-row">
                                    <input type="color" name="sections[steps][subtitle_color]"
                                           value="{{ $steps['subtitle_color'] ?? '#3b6fd4' }}" class="cms-color-picker">
                                    <input type="text" class="form-control color-hex"
                                           value="{{ $steps['subtitle_color'] ?? '#3b6fd4' }}">
                                </div>
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Step Number Color</label>
                                <div class="cms-color-row">
                                    <input type="color" name="sections[steps][step_number_bg]"
                                           value="{{ $steps['step_number_bg'] ?? '#3b6fd4' }}" class="cms-color-picker">
                                    <input type="text" class="form-control color-hex"
                                           value="{{ $steps['step_number_bg'] ?? '#3b6fd4' }}">
                                </div>
                            </div>
                        </div>

                        @foreach($defaultSteps as $i => $default)
                        @php $step = $stepsData[$i] ?? $default; @endphp
                        <div class="card card-body mb-3" style="background:#f8f9fa;">
                            <h6 class="font-weight-bold mb-3 text-primary">Step {{ $i + 1 }}</h6>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="text-muted" style="font-size:0.8rem;">Title — plain part</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="sections[steps][steps][{{ $i }}][title_plain]"
                                           value="{{ $step['title_plain'] ?? $default['title_plain'] }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="text-muted" style="font-size:0.8rem;">Title — highlighted part</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="sections[steps][steps][{{ $i }}][title_highlighted]"
                                           value="{{ $step['title_highlighted'] ?? $default['title_highlighted'] }}">
                                </div>
                                <div class="col-md-2 form-group">
                                    <label class="text-muted" style="font-size:0.8rem;">Highlight Color</label>
                                    <div class="cms-color-row">
                                        <input type="color" name="sections[steps][steps][{{ $i }}][highlight_color]"
                                               value="{{ $step['highlight_color'] ?? '#3b6fd4' }}" class="cms-color-picker">
                                        <input type="text" class="form-control color-hex"
                                               value="{{ $step['highlight_color'] ?? '#3b6fd4' }}">
                                    </div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label class="text-muted" style="font-size:0.8rem;">Card Image</label>
                                    @if(!empty($step['image']))
                                        <div class="mb-1">
                                            <img src="{{ asset('images/upload/' . $step['image']) }}"
                                                 style="height:48px; border-radius:6px; object-fit:cover; border:1px solid #ddd;">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control-file"
                                           name="sections[steps][steps][{{ $i }}][image]"
                                           accept=".jpg,.jpeg,.png,.webp">
                                    @if(!empty($step['image']))
                                        <small class="text-muted">Upload to replace current image</small>
                                    @endif
                                </div>
                                <div class="col-12 form-group">
                                    <label class="text-muted" style="font-size:0.8rem;">Description</label>
                                    <textarea class="form-control form-control-sm" rows="2"
                                              name="sections[steps][steps][{{ $i }}][description]">{{ $step['description'] ?? $default['description'] }}</textarea>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ============================
                 4. PAYMENT BAR
            ============================ --}}
            <div class="card mb-0 border-0 rounded-0">
                <div class="card-header cms-panel-header" id="headingPayment" style="background:#f8f9fa;">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button"
                                data-toggle="collapse" data-target="#collapsePayment"
                                aria-expanded="false" aria-controls="collapsePayment">
                            <i class="fas fa-credit-card mr-2 text-danger"></i> Payment Methods Bar
                        </button>
                    </h2>
                </div>
                <div id="collapsePayment" class="collapse" data-parent="#cmsSectionsAccordion">
                    <div class="card-body">
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[payment_bar][enabled]" value="1" {{ ($pay['enabled'] ?? true) ? 'checked' : '' }}>
                            Show Payment Bar
                        </label>
                        <div class="row">
                            <div class="col-md-5 form-group">
                                <label>Label Text</label>
                                <input type="text" class="form-control" name="sections[payment_bar][label]"
                                       value="{{ $pay['label'] ?? 'Akzeptierte Zahlungsmethoden:' }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Background Color</label>
                                <div class="cms-color-row">
                                    <input type="color" name="sections[payment_bar][bg_color]"
                                           value="{{ $pay['bg_color'] ?? '#1a1a1a' }}" class="cms-color-picker">
                                    <input type="text" class="form-control color-hex"
                                           value="{{ $pay['bg_color'] ?? '#1a1a1a' }}">
                                </div>
                            </div>
                        </div>
                        <label class="font-weight-bold d-block mb-2">Show Payment Methods</label>
                        <div class="d-flex flex-wrap" style="gap:16px;">
                            @foreach(['klarna' => 'Klarna', 'visa' => 'VISA', 'maestro' => 'Maestro', 'gpay' => 'G Pay', 'apple_pay' => 'Apple Pay', 'paypal' => 'PayPal'] as $key => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="sections[payment_bar][methods][{{ $key }}]"
                                       id="pay_{{ $key }}" value="1"
                                       {{ ($payMethods[$key] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="pay_{{ $key }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end accordion --}}
    </div>
</div>

<script>
$(function () {
    // Color picker ↔ hex text sync
    $(document).on('input', '.cms-color-picker', function () {
        $(this).siblings('.color-hex').val(this.value);
    });
    $(document).on('blur', '.color-hex', function () {
        var val = this.value;
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            $(this).siblings('.cms-color-picker').val(val);
        }
    });
});
</script>
