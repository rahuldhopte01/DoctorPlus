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

            {{-- ============================
                 5. MEDICAL CONTENT
            ============================ --}}
            @php
                $mc        = $cms['medical_content'] ?? [];
                $mcTocItems = $mc['toc_items'] ?? [];
                $mcArticles = $mc['articles'] ?? [];
            @endphp
            <div class="card mb-0 border-0 border-bottom rounded-0">
                <div class="card-header cms-panel-header" id="headingMedical" style="background:#f8f9fa;">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button"
                                data-toggle="collapse" data-target="#collapseMedical"
                                aria-expanded="false" aria-controls="collapseMedical">
                            <i class="fas fa-file-medical mr-2" style="color:#6f42c1;"></i> Medical Content Section
                        </button>
                    </h2>
                </div>
                <div id="collapseMedical" class="collapse" data-parent="#cmsSectionsAccordion">
                    <div class="card-body">
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[medical_content][enabled]" value="1"
                                   {{ ($mc['enabled'] ?? true) ? 'checked' : '' }}>
                            Show Medical Content Section
                        </label>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Section Title <small class="text-muted">(category name appended automatically)</small></label>
                                <input type="text" class="form-control" name="sections[medical_content][section_title]"
                                       value="{{ $mc['section_title'] ?? 'Behandlungen bei' }}">
                            </div>
                            <div class="col-md-2 form-group">
                                <label class="cms-toggle-label mt-4">
                                    <input type="checkbox" name="sections[medical_content][toc_enabled]" value="1"
                                           {{ ($mc['toc_enabled'] ?? true) ? 'checked' : '' }}>
                                    Show TOC
                                </label>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>TOC Box Title</label>
                                <input type="text" class="form-control" name="sections[medical_content][toc_title]"
                                       value="{{ $mc['toc_title'] ?? 'Themenliste' }}">
                            </div>
                        </div>

                        {{-- TOC Items --}}
                        <div class="card card-body mb-3" style="background:#f8f9fa;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="font-weight-bold mb-0">TOC Items</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary js-add-toc">+ Add TOC Item</button>
                            </div>
                            <div id="toc-items-container">
                                @foreach($mcTocItems as $ti => $tocItem)
                                <div class="toc-item-row d-flex mb-2" style="gap:8px;" data-toc-idx="{{ $ti }}">
                                    <input type="text" class="form-control form-control-sm" placeholder="Label"
                                           name="sections[medical_content][toc_items][{{ $ti }}][label]"
                                           value="{{ $tocItem['label'] ?? '' }}">
                                    <input type="text" class="form-control form-control-sm" placeholder="URL (e.g. #anchor)"
                                           name="sections[medical_content][toc_items][{{ $ti }}][url]"
                                           value="{{ $tocItem['url'] ?? '#' }}">
                                    <button type="button" class="btn btn-sm btn-outline-danger js-remove-toc flex-shrink-0">×</button>
                                </div>
                                @endforeach
                                @if(empty($mcTocItems))
                                {{-- show one empty row by default --}}
                                <div class="toc-item-row d-flex mb-2" style="gap:8px;" data-toc-idx="0">
                                    <input type="text" class="form-control form-control-sm" placeholder="Label"
                                           name="sections[medical_content][toc_items][0][label]" value="">
                                    <input type="text" class="form-control form-control-sm" placeholder="URL (e.g. #anchor)"
                                           name="sections[medical_content][toc_items][0][url]" value="#">
                                    <button type="button" class="btn btn-sm btn-outline-danger js-remove-toc flex-shrink-0">×</button>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Articles --}}
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="font-weight-bold mb-0">Articles</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary js-add-article">+ Add Article</button>
                        </div>
                        <div id="articles-container">
                            @foreach($mcArticles as $ai => $article)
                            <div class="card mb-3 border cms-article" data-article-idx="{{ $ai }}">
                                <div class="card-header d-flex align-items-center justify-content-between py-2" style="background:#e9ecef; cursor:pointer;"
                                     data-toggle="collapse" data-target="#article-body-{{ $ai }}">
                                    <strong>Article {{ $ai + 1 }}: {{ $article['heading'] ?? '' }}</strong>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-remove-article ml-2" onclick="event.stopPropagation()">× Remove</button>
                                </div>
                                <div id="article-body-{{ $ai }}" class="collapse show">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label class="text-muted" style="font-size:0.8rem;">Heading</label>
                                                <input type="text" class="form-control form-control-sm cms-article-heading"
                                                       name="sections[medical_content][articles][{{ $ai }}][heading]"
                                                       value="{{ $article['heading'] ?? '' }}" placeholder="Article heading">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="text-muted" style="font-size:0.8rem;">Anchor ID <small>(for TOC links, no #)</small></label>
                                                <input type="text" class="form-control form-control-sm"
                                                       name="sections[medical_content][articles][{{ $ai }}][anchor_id]"
                                                       value="{{ $article['anchor_id'] ?? '' }}" placeholder="e.g. was-ist-ed">
                                            </div>
                                        </div>
                                        {{-- Blocks --}}
                                        <div class="cms-blocks-container" data-article="{{ $ai }}">
                                            @foreach($article['blocks'] ?? [] as $bi => $block)
                                            @include('superAdmin.category.partials.cms_block', ['block' => $block, 'ai' => $ai, 'bi' => $bi])
                                            @endforeach
                                        </div>
                                        <div class="dropdown mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle js-add-block" data-toggle="dropdown">
                                                + Add Block
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item js-block-type" href="#" data-type="text">Text Paragraph</a>
                                                <a class="dropdown-item js-block-type" href="#" data-type="subheading">Subheading</a>
                                                <a class="dropdown-item js-block-type" href="#" data-type="table">Table</a>
                                                <a class="dropdown-item js-block-type" href="#" data-type="list">List</a>
                                                <a class="dropdown-item js-block-type" href="#" data-type="callout">Callout Box</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @if(empty($mcArticles))
                            <p class="text-muted small" id="no-articles-msg">No articles yet. Click "+ Add Article" to begin.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================
                 6. DOCTOR REVIEW
            ============================ --}}
            @php $dr = $cms['doctor_review'] ?? []; @endphp
            <div class="card mb-0 border-0 border-bottom rounded-0">
                <div class="card-header cms-panel-header" id="headingDr" style="background:#f8f9fa;">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button"
                                data-toggle="collapse" data-target="#collapseDr"
                                aria-expanded="false" aria-controls="collapseDr">
                            <i class="fas fa-user-md mr-2 text-info"></i> Doctor Review Section
                        </button>
                    </h2>
                </div>
                <div id="collapseDr" class="collapse" data-parent="#cmsSectionsAccordion">
                    <div class="card-body">
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[doctor_review][enabled]" value="1"
                                   {{ ($dr['enabled'] ?? true) ? 'checked' : '' }}>
                            Show Doctor Review Section
                        </label>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Doctor Photo</label>
                                @if(!empty($dr['image']))
                                    <div class="mb-1">
                                        <img src="{{ asset('images/upload/' . $dr['image']) }}"
                                             style="height:80px; border-radius:8px; object-fit:cover; border:1px solid #ddd;">
                                    </div>
                                    <small class="text-muted d-block mb-1">Upload to replace</small>
                                @endif
                                <input type="file" class="form-control-file"
                                       name="sections[doctor_review][image]" accept=".jpg,.jpeg,.png,.webp">
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Doctor Name</label>
                                        <input type="text" class="form-control" name="sections[doctor_review][name]"
                                               value="{{ $dr['name'] ?? 'Dr. med. Experte' }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Role / Specialty</label>
                                        <input type="text" class="form-control" name="sections[doctor_review][role]"
                                               value="{{ $dr['role'] ?? 'Facharzt für Urologie' }}">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label>Section Title</label>
                                        <input type="text" class="form-control" name="sections[doctor_review][title]"
                                               value="{{ $dr['title'] ?? 'Medizinisch-fachlich geprüft' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Description Paragraphs</label>
                            <div id="dr-paragraphs-container">
                                @php $drParas = $dr['paragraphs'] ?? ['Die medizinischen Inhalte auf dieser Seite wurden in Zusammenarbeit mit einem unserer Ärzte bzw. medizinischen Experten erstellt und von diesen überprüft.']; @endphp
                                @foreach($drParas as $pi => $para)
                                <div class="d-flex mb-2" style="gap:8px;">
                                    <textarea class="form-control form-control-sm" rows="2"
                                              name="sections[doctor_review][paragraphs][{{ $pi }}]">{{ $para }}</textarea>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-remove-dr-para flex-shrink-0" style="align-self:flex-start;">×</button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-1 js-add-dr-para">+ Add Paragraph</button>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Link Text</label>
                                <input type="text" class="form-control" name="sections[doctor_review][link_text]"
                                       value="{{ $dr['link_text'] ?? 'Redaktionsprozess' }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Link URL</label>
                                <input type="text" class="form-control" name="sections[doctor_review][link_url]"
                                       value="{{ $dr['link_url'] ?? '#' }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="cms-toggle-label mt-4">
                                    <input type="checkbox" name="sections[doctor_review][show_last_updated]" value="1"
                                           {{ ($dr['show_last_updated'] ?? true) ? 'checked' : '' }}>
                                    Show "Last updated" date
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================
                 7. FAQ
            ============================ --}}
            @php $faqCms = $cms['faq'] ?? []; @endphp
            <div class="card mb-0 border-0 rounded-0">
                <div class="card-header cms-panel-header" id="headingFaq" style="background:#f8f9fa;">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button"
                                data-toggle="collapse" data-target="#collapseFaq"
                                aria-expanded="false" aria-controls="collapseFaq">
                            <i class="fas fa-question-circle mr-2 text-secondary"></i> FAQ Section
                        </button>
                    </h2>
                </div>
                <div id="collapseFaq" class="collapse" data-parent="#cmsSectionsAccordion">
                    <div class="card-body">
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[faq][enabled]" value="1"
                                   {{ ($faqCms['enabled'] ?? true) ? 'checked' : '' }}>
                            Show FAQ Section
                        </label>
                        <div class="form-group col-md-6 pl-0">
                            <label>Section Title</label>
                            <input type="text" class="form-control" name="sections[faq][title]"
                                   value="{{ $faqCms['title'] ?? 'Frequently asked questions' }}">
                        </div>
                        <div id="faq-items-container">
                            @php $faqItems = $faqCms['items'] ?? []; @endphp
                            @foreach($faqItems as $fi => $fitem)
                            <div class="card card-body mb-2 faq-item" style="background:#f8f9fa;">
                                <div class="form-group mb-2">
                                    <label class="text-muted" style="font-size:0.8rem;">Question</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="sections[faq][items][{{ $fi }}][question]"
                                           value="{{ $fitem['question'] ?? '' }}">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-muted" style="font-size:0.8rem;">Answer</label>
                                    <textarea class="form-control form-control-sm" rows="2"
                                              name="sections[faq][items][{{ $fi }}][answer]">{{ $fitem['answer'] ?? '' }}</textarea>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger js-remove-faq align-self-start">× Remove</button>
                            </div>
                            @endforeach
                            @if(empty($faqItems))
                            <p class="text-muted small" id="no-faq-msg">No FAQ items yet.</p>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 js-add-faq">+ Add FAQ Item</button>
                    </div>
                </div>
            </div>

        </div>{{-- end accordion --}}
    </div>
</div>

{{-- =====================================================================
     TEMPLATES (hidden) — cloned by JS when adding items dynamically
===================================================================== --}}
<template id="tpl-toc-item">
    <div class="toc-item-row d-flex mb-2" style="gap:8px;">
        <input type="text" class="form-control form-control-sm" placeholder="Label"
               name="sections[medical_content][toc_items][__TOC__][label]" value="">
        <input type="text" class="form-control form-control-sm" placeholder="URL (e.g. #anchor)"
               name="sections[medical_content][toc_items][__TOC__][url]" value="#">
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-toc flex-shrink-0">×</button>
    </div>
</template>

<template id="tpl-article">
    <div class="card mb-3 border cms-article">
        <div class="card-header d-flex align-items-center justify-content-between py-2" style="background:#e9ecef; cursor:pointer;"
             data-toggle="collapse" data-target="#article-body-__AI__">
            <strong class="cms-article-label">Article __NUM__</strong>
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-article ml-2" onclick="event.stopPropagation()">× Remove</button>
        </div>
        <div id="article-body-__AI__" class="collapse show">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="text-muted" style="font-size:0.8rem;">Heading</label>
                        <input type="text" class="form-control form-control-sm cms-article-heading"
                               name="sections[medical_content][articles][__AI__][heading]" value="" placeholder="Article heading">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="text-muted" style="font-size:0.8rem;">Anchor ID <small>(for TOC links, no #)</small></label>
                        <input type="text" class="form-control form-control-sm"
                               name="sections[medical_content][articles][__AI__][anchor_id]" value="" placeholder="e.g. was-ist-ed">
                    </div>
                </div>
                <div class="cms-blocks-container"></div>
                <div class="dropdown mt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle js-add-block" data-toggle="dropdown">
                        + Add Block
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item js-block-type" href="#" data-type="text">Text Paragraph</a>
                        <a class="dropdown-item js-block-type" href="#" data-type="subheading">Subheading</a>
                        <a class="dropdown-item js-block-type" href="#" data-type="table">Table</a>
                        <a class="dropdown-item js-block-type" href="#" data-type="list">List</a>
                        <a class="dropdown-item js-block-type" href="#" data-type="callout">Callout Box</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="tpl-block-text">
    <div class="cms-block card card-body mb-2 border-left border-primary" style="border-left-width:3px!important; background:#fff;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge badge-primary">Text Paragraph</span>
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-block">× Remove</button>
        </div>
        <input type="hidden" name="sections[medical_content][articles][__AI__][blocks][__BI__][type]" value="text">
        <textarea class="form-control form-control-sm" rows="3"
                  name="sections[medical_content][articles][__AI__][blocks][__BI__][content]"
                  placeholder="Paragraph text..."></textarea>
    </div>
</template>

<template id="tpl-block-subheading">
    <div class="cms-block card card-body mb-2 border-left border-secondary" style="border-left-width:3px!important; background:#fff;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge badge-secondary">Subheading</span>
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-block">× Remove</button>
        </div>
        <input type="hidden" name="sections[medical_content][articles][__AI__][blocks][__BI__][type]" value="subheading">
        <div class="d-flex" style="gap:8px;">
            <select class="form-control form-control-sm" style="width:80px; flex-shrink:0;"
                    name="sections[medical_content][articles][__AI__][blocks][__BI__][level]">
                <option value="h3">H3</option>
                <option value="h4">H4</option>
            </select>
            <input type="text" class="form-control form-control-sm"
                   name="sections[medical_content][articles][__AI__][blocks][__BI__][text]"
                   placeholder="Subheading text">
        </div>
    </div>
</template>

<template id="tpl-block-table">
    <div class="cms-block card card-body mb-2 border-left border-warning" style="border-left-width:3px!important; background:#fff;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge badge-warning text-dark">Table</span>
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-block">× Remove</button>
        </div>
        <input type="hidden" name="sections[medical_content][articles][__AI__][blocks][__BI__][type]" value="table">
        <div class="row mb-2">
            <div class="col-md-4 form-group mb-2">
                <label class="text-muted" style="font-size:0.75rem;">Table Heading (optional)</label>
                <input type="text" class="form-control form-control-sm"
                       name="sections[medical_content][articles][__AI__][blocks][__BI__][heading]" placeholder="Table heading">
            </div>
            <div class="col-md-2 form-group mb-2">
                <label class="text-muted" style="font-size:0.75rem;">Header BG</label>
                <div class="cms-color-row">
                    <input type="color" class="cms-color-picker" value="#3b6fd4"
                           name="sections[medical_content][articles][__AI__][blocks][__BI__][header_bg]">
                    <input type="text" class="form-control form-control-sm color-hex" value="#3b6fd4">
                </div>
            </div>
            <div class="col-md-2 form-group mb-2">
                <label class="text-muted" style="font-size:0.75rem;">Header Text</label>
                <div class="cms-color-row">
                    <input type="color" class="cms-color-picker" value="#ffffff"
                           name="sections[medical_content][articles][__AI__][blocks][__BI__][header_text_color]">
                    <input type="text" class="form-control form-control-sm color-hex" value="#ffffff">
                </div>
            </div>
            <div class="col-md-2 form-group mb-2">
                <label class="text-muted" style="font-size:0.75rem;">Alt Row BG</label>
                <div class="cms-color-row">
                    <input type="color" class="cms-color-picker" value="#f8f9fa"
                           name="sections[medical_content][articles][__AI__][blocks][__BI__][alt_row_bg]">
                    <input type="text" class="form-control form-control-sm color-hex" value="#f8f9fa">
                </div>
            </div>
            <div class="col-md-2 form-group mb-2">
                <label class="text-muted" style="font-size:0.75rem;">Border Color</label>
                <div class="cms-color-row">
                    <input type="color" class="cms-color-picker" value="#dee2e6"
                           name="sections[medical_content][articles][__AI__][blocks][__BI__][border_color]">
                    <input type="text" class="form-control form-control-sm color-hex" value="#dee2e6">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm cms-editable-table mb-1">
                <thead>
                    <tr class="cms-table-header-row">
                        <th><input type="text" class="form-control form-control-sm"
                                   name="sections[medical_content][articles][__AI__][blocks][__BI__][headers][0]"
                                   placeholder="Column 1"></th>
                        <th><input type="text" class="form-control form-control-sm"
                                   name="sections[medical_content][articles][__AI__][blocks][__BI__][headers][1]"
                                   placeholder="Column 2"></th>
                        <th style="width:40px;"><button type="button" class="btn btn-sm btn-outline-danger js-remove-col" title="Remove last column">−col</button></th>
                    </tr>
                </thead>
                <tbody class="cms-table-body">
                    <tr class="cms-table-data-row">
                        <td><input type="text" class="form-control form-control-sm"
                                   name="sections[medical_content][articles][__AI__][blocks][__BI__][rows][0][0]"></td>
                        <td><input type="text" class="form-control form-control-sm"
                                   name="sections[medical_content][articles][__AI__][blocks][__BI__][rows][0][1]"></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger js-remove-row">×</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex" style="gap:8px;">
            <button type="button" class="btn btn-xs btn-outline-secondary js-add-col" style="font-size:0.75rem; padding:2px 8px;">+ Column</button>
            <button type="button" class="btn btn-xs btn-outline-secondary js-add-row" style="font-size:0.75rem; padding:2px 8px;">+ Row</button>
        </div>
    </div>
</template>

<template id="tpl-block-list">
    <div class="cms-block card card-body mb-2 border-left border-info" style="border-left-width:3px!important; background:#fff;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge badge-info">List</span>
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-block">× Remove</button>
        </div>
        <input type="hidden" name="sections[medical_content][articles][__AI__][blocks][__BI__][type]" value="list">
        <div class="cms-list-items-container mb-2">
            <div class="d-flex mb-1" style="gap:8px;">
                <input type="text" class="form-control form-control-sm" style="max-width:140px;"
                       name="sections[medical_content][articles][__AI__][blocks][__BI__][items][0][label]"
                       placeholder="Bold label (optional)">
                <input type="text" class="form-control form-control-sm"
                       name="sections[medical_content][articles][__AI__][blocks][__BI__][items][0][text]"
                       placeholder="Item text">
                <button type="button" class="btn btn-sm btn-outline-danger js-remove-list-item flex-shrink-0">×</button>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary js-add-list-item">+ Add Item</button>
    </div>
</template>

<template id="tpl-block-callout">
    <div class="cms-block card card-body mb-2 border-left border-success" style="border-left-width:3px!important; background:#fff;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge badge-success">Callout Box</span>
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-block">× Remove</button>
        </div>
        <input type="hidden" name="sections[medical_content][articles][__AI__][blocks][__BI__][type]" value="callout">
        <div class="row mb-2">
            <div class="col-md-3 form-group mb-2">
                <label class="text-muted" style="font-size:0.75rem;">Background Color</label>
                <div class="cms-color-row">
                    <input type="color" class="cms-color-picker" value="#eff3fb"
                           name="sections[medical_content][articles][__AI__][blocks][__BI__][bg_color]">
                    <input type="text" class="form-control form-control-sm color-hex" value="#eff3fb">
                </div>
            </div>
            <div class="col-md-3 form-group mb-2">
                <label class="text-muted" style="font-size:0.75rem;">Left Border Color</label>
                <div class="cms-color-row">
                    <input type="color" class="cms-color-picker" value="#3b6fd4"
                           name="sections[medical_content][articles][__AI__][blocks][__BI__][border_color]">
                    <input type="text" class="form-control form-control-sm color-hex" value="#3b6fd4">
                </div>
            </div>
        </div>
        <div class="form-group mb-2">
            <label class="text-muted" style="font-size:0.75rem;">Callout Heading (optional)</label>
            <input type="text" class="form-control form-control-sm"
                   name="sections[medical_content][articles][__AI__][blocks][__BI__][heading]"
                   placeholder="e.g. Wie dr.fuxx helfen kann?">
        </div>
        <div class="form-group mb-0">
            <label class="text-muted" style="font-size:0.75rem;">Content</label>
            <textarea class="form-control form-control-sm" rows="3"
                      name="sections[medical_content][articles][__AI__][blocks][__BI__][content]"
                      placeholder="Callout body text..."></textarea>
        </div>
    </div>
</template>

<template id="tpl-faq-item">
    <div class="card card-body mb-2 faq-item" style="background:#f8f9fa;">
        <div class="form-group mb-2">
            <label class="text-muted" style="font-size:0.8rem;">Question</label>
            <input type="text" class="form-control form-control-sm"
                   name="sections[faq][items][__FI__][question]" value="">
        </div>
        <div class="form-group mb-2">
            <label class="text-muted" style="font-size:0.8rem;">Answer</label>
            <textarea class="form-control form-control-sm" rows="2"
                      name="sections[faq][items][__FI__][answer]"></textarea>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-faq align-self-start">× Remove</button>
    </div>
</template>

<template id="tpl-dr-para">
    <div class="d-flex mb-2" style="gap:8px;">
        <textarea class="form-control form-control-sm" rows="2"
                  name="sections[doctor_review][paragraphs][__PI__]"></textarea>
        <button type="button" class="btn btn-sm btn-outline-danger js-remove-dr-para flex-shrink-0" style="align-self:flex-start;">×</button>
    </div>
</template>

<script>
$(function () {
    // ── Color picker ↔ hex text sync ──────────────────────────────────
    $(document).on('input', '.cms-color-picker', function () {
        $(this).siblings('.color-hex').val(this.value);
    });
    $(document).on('blur', '.color-hex', function () {
        var val = this.value;
        if (/^#[0-9a-fA-F]{6}$/.test(val)) $(this).siblings('.cms-color-picker').val(val);
    });

    // ── Re-index helpers ──────────────────────────────────────────────
    function reindexNames($container, placeholder, attr) {
        attr = attr || 'name';
        $container.find('[' + attr + '*="' + placeholder + '"]').each(function (i) {
            // find last occurrence of placeholder in the name and replace
            var n = $(this).attr(attr);
            // replace ALL occurrences of this placeholder in the attribute
            $(this).attr(attr, n.replace(new RegExp('\\[' + placeholder + '\\]', 'g'), '[' + i + ']'));
        });
    }

    function reindexToc() {
        $('#toc-items-container .toc-item-row').each(function (i) {
            $(this).find('input').each(function () {
                var n = $(this).attr('name');
                if (n) $(this).attr('name', n.replace(/\[toc_items\]\[\d+\]/, '[toc_items][' + i + ']'));
            });
        });
    }
    function reindexFaq() {
        $('#faq-items-container .faq-item').each(function (i) {
            $(this).find('input, textarea').each(function () {
                var n = $(this).attr('name');
                if (n) $(this).attr('name', n.replace(/\[items\]\[\d+\]/, '[items][' + i + ']'));
            });
        });
    }
    function reindexDrParas() {
        $('#dr-paragraphs-container > div').each(function (i) {
            $(this).find('textarea').each(function () {
                var n = $(this).attr('name');
                if (n) $(this).attr('name', n.replace(/\[paragraphs\]\[\d+\]/, '[paragraphs][' + i + ']'));
            });
        });
    }
    function reindexArticles() {
        $('#articles-container .cms-article').each(function (ai) {
            $(this).find('[name]').each(function () {
                var n = $(this).attr('name');
                if (n) $(this).attr('name', n.replace(/\[articles\]\[\d+\]/, '[articles][' + ai + ']'));
            });
            // also fix collapse target id
            var $header = $(this).find('[data-target]');
            var $body   = $(this).find('.collapse').first();
            var newId   = 'article-body-' + ai;
            $header.attr('data-target', '#' + newId);
            $body.attr('id', newId);
            // re-index blocks within this article
            reindexBlocks($(this).find('.cms-blocks-container'), ai);
        });
    }
    function reindexBlocks($container, ai) {
        $container.find('.cms-block').each(function (bi) {
            $(this).find('[name]').each(function () {
                var n = $(this).attr('name');
                if (!n) return;
                n = n.replace(/\[articles\]\[\d+\]/, '[articles][' + ai + ']');
                n = n.replace(/\[blocks\]\[\d+\]/, '[blocks][' + bi + ']');
                $(this).attr('name', n);
            });
            // re-index table rows/headers within this block
            reindexTableCells($(this), ai, bi);
        });
    }
    function reindexTableCells($block, ai, bi) {
        $block.find('.cms-table-header-row th input').each(function (ci) {
            var n = $(this).attr('name');
            if (n) $(this).attr('name', n.replace(/\[headers\]\[\d+\]/, '[headers][' + ci + ']'));
        });
        $block.find('.cms-table-body .cms-table-data-row').each(function (ri) {
            $(this).find('td input').each(function (ci) {
                var n = $(this).attr('name');
                if (n) {
                    n = n.replace(/\[rows\]\[\d+\]\[\d+\]/, '[rows][' + ri + '][' + ci + ']');
                    $(this).attr('name', n);
                }
            });
        });
        $block.find('.cms-list-items-container > div').each(function (ii) {
            $(this).find('input').each(function () {
                var n = $(this).attr('name');
                if (n) $(this).attr('name', n.replace(/\[items\]\[\d+\]/, '[items][' + ii + ']'));
            });
        });
    }

    // ── TOC ──────────────────────────────────────────────────────────
    $(document).on('click', '.js-add-toc', function () {
        var count = $('#toc-items-container .toc-item-row').length;
        var html = document.getElementById('tpl-toc-item').innerHTML
                    .replace(/__TOC__/g, count);
        $('#toc-items-container').append(html);
    });
    $(document).on('click', '.js-remove-toc', function () {
        $(this).closest('.toc-item-row').remove();
        reindexToc();
    });

    // ── Articles ─────────────────────────────────────────────────────
    $(document).on('click', '.js-add-article', function () {
        var ai = $('#articles-container .cms-article').length;
        var html = document.getElementById('tpl-article').innerHTML
                    .replace(/__AI__/g, ai)
                    .replace(/__NUM__/g, ai + 1);
        $('#no-articles-msg').remove();
        $('#articles-container').append(html);
    });
    $(document).on('click', '.js-remove-article', function () {
        $(this).closest('.cms-article').remove();
        reindexArticles();
    });
    // Update article label from heading input
    $(document).on('input', '.cms-article-heading', function () {
        var val = $(this).val() || 'Untitled';
        $(this).closest('.cms-article').find('.cms-article-label').text($(this).closest('.cms-article').find('.cms-blocks-container').data('article') + ' - ' + val);
    });

    // ── Blocks ───────────────────────────────────────────────────────
    $(document).on('click', '.js-block-type', function (e) {
        e.preventDefault();
        var type    = $(this).data('type');
        var $article= $(this).closest('.cms-article');
        var $container = $article.find('.cms-blocks-container');
        var ai = $('#articles-container .cms-article').index($article);
        var bi = $container.find('.cms-block').length;
        var html = document.getElementById('tpl-block-' + type).innerHTML
                    .replace(/__AI__/g, ai)
                    .replace(/__BI__/g, bi);
        $container.append(html);
    });
    $(document).on('click', '.js-remove-block', function () {
        var $article = $(this).closest('.cms-article');
        var ai = $('#articles-container .cms-article').index($article);
        $(this).closest('.cms-block').remove();
        reindexBlocks($article.find('.cms-blocks-container'), ai);
    });

    // ── Table: add/remove row and column ─────────────────────────────
    $(document).on('click', '.js-add-col', function () {
        var $block = $(this).closest('.cms-block');
        var $hRow  = $block.find('.cms-table-header-row');
        var $body  = $block.find('.cms-table-body');
        var ci     = $hRow.find('th input').length; // current col count (before add)
        // add header cell
        var $removeBtn = $hRow.find('th:last');
        $('<th><input type="text" class="form-control form-control-sm" placeholder="Column ' + (ci+1) + '"></th>').insertBefore($removeBtn);
        // add data cell in each row
        $body.find('.cms-table-data-row').each(function () {
            var $removeCell = $(this).find('td:last');
            $('<td><input type="text" class="form-control form-control-sm"></td>').insertBefore($removeCell);
        });
        // re-index whole block
        var $article = $(this).closest('.cms-article');
        var ai = $('#articles-container .cms-article').index($article);
        var $blocksContainer = $article.find('.cms-blocks-container');
        reindexBlocks($blocksContainer, ai);
        // now fix names for header inputs
        $hRow.find('th input').each(function (i) {
            var n = $(this).attr('name');
            if (!n) {
                // newly added, set name from sibling pattern
                var pattern = $hRow.find('th input:first').attr('name');
                if (pattern) $(this).attr('name', pattern.replace(/\[headers\]\[\d+\]/, '[headers][' + i + ']'));
            }
        });
    });
    $(document).on('click', '.js-remove-col', function () {
        var $block = $(this).closest('.cms-block');
        var $hRow  = $block.find('.cms-table-header-row');
        if ($hRow.find('th input').length <= 1) return; // keep at least 1 col
        $hRow.find('th:nth-last-child(2)').remove();
        $block.find('.cms-table-body .cms-table-data-row').each(function () {
            $(this).find('td:nth-last-child(2)').remove();
        });
        var $article = $(this).closest('.cms-article');
        var ai = $('#articles-container .cms-article').index($article);
        reindexBlocks($article.find('.cms-blocks-container'), ai);
    });
    $(document).on('click', '.js-add-row', function () {
        var $block = $(this).closest('.cms-block');
        var $body  = $block.find('.cms-table-body');
        var $hRow  = $block.find('.cms-table-header-row');
        var colCount = $hRow.find('th input').length;
        var $newRow = $('<tr class="cms-table-data-row"></tr>');
        for (var c = 0; c < colCount; c++) {
            $newRow.append('<td><input type="text" class="form-control form-control-sm"></td>');
        }
        $newRow.append('<td><button type="button" class="btn btn-sm btn-outline-danger js-remove-row">×</button></td>');
        $body.append($newRow);
        var $article = $(this).closest('.cms-article');
        var ai = $('#articles-container .cms-article').index($article);
        reindexBlocks($article.find('.cms-blocks-container'), ai);
    });
    $(document).on('click', '.js-remove-row', function () {
        var $block   = $(this).closest('.cms-block');
        var $article = $(this).closest('.cms-article');
        var ai = $('#articles-container .cms-article').index($article);
        $(this).closest('tr').remove();
        reindexBlocks($article.find('.cms-blocks-container'), ai);
    });

    // ── List items ────────────────────────────────────────────────────
    $(document).on('click', '.js-add-list-item', function () {
        var $block   = $(this).closest('.cms-block');
        var $article = $(this).closest('.cms-article');
        var ai = $('#articles-container .cms-article').index($article);
        var bi = $article.find('.cms-blocks-container .cms-block').index($block);
        var ii = $block.find('.cms-list-items-container > div').length;
        var $newItem = $('<div class="d-flex mb-1" style="gap:8px;">' +
            '<input type="text" class="form-control form-control-sm" style="max-width:140px;" ' +
                   'name="sections[medical_content][articles][' + ai + '][blocks][' + bi + '][items][' + ii + '][label]" placeholder="Bold label (optional)">' +
            '<input type="text" class="form-control form-control-sm" ' +
                   'name="sections[medical_content][articles][' + ai + '][blocks][' + bi + '][items][' + ii + '][text]" placeholder="Item text">' +
            '<button type="button" class="btn btn-sm btn-outline-danger js-remove-list-item flex-shrink-0">×</button>' +
        '</div>');
        $block.find('.cms-list-items-container').append($newItem);
    });
    $(document).on('click', '.js-remove-list-item', function () {
        var $block   = $(this).closest('.cms-block');
        var $article = $(this).closest('.cms-article');
        var ai = $('#articles-container .cms-article').index($article);
        $(this).closest('div').remove();
        reindexBlocks($article.find('.cms-blocks-container'), ai);
    });

    // ── Doctor Review paragraphs ──────────────────────────────────────
    $(document).on('click', '.js-add-dr-para', function () {
        var count = $('#dr-paragraphs-container > div').length;
        var html = document.getElementById('tpl-dr-para').innerHTML
                    .replace(/__PI__/g, count);
        $('#dr-paragraphs-container').append(html);
    });
    $(document).on('click', '.js-remove-dr-para', function () {
        $(this).closest('div').remove();
        reindexDrParas();
    });

    // ── FAQ ──────────────────────────────────────────────────────────
    $(document).on('click', '.js-add-faq', function () {
        var count = $('#faq-items-container .faq-item').length;
        var html = document.getElementById('tpl-faq-item').innerHTML
                    .replace(/__FI__/g, count);
        $('#no-faq-msg').remove();
        $('#faq-items-container').append(html);
    });
    $(document).on('click', '.js-remove-faq', function () {
        $(this).closest('.faq-item').remove();
        reindexFaq();
    });

    // ── Re-index ALL before submit ────────────────────────────────────
    $('form').on('submit', function () {
        reindexToc();
        reindexArticles();
        reindexFaq();
        reindexDrParas();
    });
});
</script>
