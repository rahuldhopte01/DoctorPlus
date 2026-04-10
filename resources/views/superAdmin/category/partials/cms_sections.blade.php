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

    $sectionLabels = [
        'hero'             => ['icon' => 'fas fa-image',          'label' => 'Hero Section'],
        'features_bar'     => ['icon' => 'fas fa-th-list',        'label' => 'Features Bar'],
        'steps'            => ['icon' => 'fas fa-list-ol',        'label' => 'Steps Section'],
        'payment_bar'      => ['icon' => 'fas fa-credit-card',    'label' => 'Payment Bar'],
        'medical_content'  => ['icon' => 'fas fa-file-medical',   'label' => 'Medical Content'],
        'doctor_review'    => ['icon' => 'fas fa-user-md',        'label' => 'Doctor Review'],
        'faq'              => ['icon' => 'fas fa-question-circle','label' => 'FAQ Section'],
        'testo_info'       => ['icon' => 'fas fa-info-circle',    'label' => 'Testosterone Info'],
        'testo_treatments' => ['icon' => 'fas fa-pills',          'label' => 'Testosterone Treatments'],
        'security'         => ['icon' => 'fas fa-shield-alt',     'label' => 'Security / Trust'],
        'sidebar_nav'      => ['icon' => 'fas fa-bars',            'label' => 'Sidebar Navigation'],
    ];
    $defaultOrder = array_keys($sectionLabels);
    $sectionOrder = $cms['section_order'] ?? $defaultOrder;
    foreach ($defaultOrder as $_k) {
        if (!in_array($_k, $sectionOrder)) $sectionOrder[] = $_k;
    }

    $defaultFeatures = [
        ['title' => 'Das Rezept wird online ausgestellt.',      'subtitle' => 'Ein Klinikbesuch ist nicht erforderlich.'],
        ['title' => 'Lieferung innerhalb von 1–2 Werktagen.',   'subtitle' => 'Schnelle, zuverlässige Lieferung.'],
        ['title' => 'Originalmedizin und Generika.',            'subtitle' => 'Aus zertifizierten Apotheken.'],
        ['title' => 'Beratung über Online-Fragebogen.',         'subtitle' => 'Schnelle medizinische Beratung'],
    ];
    $fbFeatures = $fb['features'] ?? $defaultFeatures;

    $defaultSteps = [
        ['title_plain' => 'Füllen Sie den',  'title_highlighted' => 'medizinischen Fragebogen aus', 'highlight_color' => '#3b6fd4', 'description' => 'Starten Sie die Online-Konsultation und beantworten Sie die medizinischen Fragen.',    'image' => null, 'icon' => 'bx bx-file'],
        ['title_plain' => 'Wählen Sie die',  'title_highlighted' => 'gewünschte Behandlung',        'highlight_color' => '#3b6fd4', 'description' => 'Der behandelnde Arzt prüft Ihre Angaben und stellt Ihnen bei Bedarf ein Rezept aus.', 'image' => null, 'icon' => 'bx bx-user'],
        ['title_plain' => 'Lieferung in',    'title_highlighted' => '1–2 Werktagen',                'highlight_color' => '#3b6fd4', 'description' => 'Sie erhalten Ihre Medikamente diskret und sicher.',                                    'image' => null, 'icon' => 'bx bx-truck'],
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

        {{-- ============================
             SECTION ORDER
        ============================ --}}
        <div class="px-3 pt-3 pb-2 border-bottom" style="background:#fff9f0;">
            <h6 class="font-weight-bold mb-1" style="color:#e85d04;">
                <i class="fas fa-sort mr-1"></i> Section Order
                <small class="text-muted font-weight-normal ml-2" style="font-size:0.8rem;">Drag rows to set the display order on the public page</small>
            </h6>
            <input type="hidden" name="sections[section_order]" id="cmsOrderInput" value="{{ implode(',', $sectionOrder) }}">
            <ul id="cmsSortableOrder" class="list-unstyled mb-0 mt-2" style="display:flex;flex-wrap:wrap;gap:6px;">
                @foreach($sectionOrder as $sKey)
                @if(isset($sectionLabels[$sKey]))
                <li data-key="{{ $sKey }}"
                    style="cursor:grab;background:#fff;border:1px solid #dee2e6;border-radius:6px;padding:6px 12px;display:flex;align-items:center;gap:8px;font-size:0.85rem;font-weight:600;user-select:none;white-space:nowrap;">
                    <i class="fas fa-grip-vertical text-muted" style="font-size:0.75rem;cursor:grab;"></i>
                    <i class="{{ $sectionLabels[$sKey]['icon'] }} text-primary" style="font-size:0.8rem;"></i>
                    {{ $sectionLabels[$sKey]['label'] }}
                </li>
                @endif
                @endforeach
            </ul>
        </div>

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
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="cms-toggle-label mb-3">
                                    <input type="checkbox" name="sections[hero][enabled]" value="1" {{ ($hero['enabled'] ?? true) ? 'checked' : '' }}>
                                    Show Hero Section
                                </label>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Hero Type</label>
                                <select name="sections[hero][type]" class="form-control" id="hero_type_selector">
                                    <option value="type1" {{ ($hero['type'] ?? 'type1') == 'type1' ? 'selected' : '' }}>Type 1: Default (Classic Banner)</option>
                                    <option value="type2" {{ ($hero['type'] ?? '') == 'type2' ? 'selected' : '' }}>Type 2: Cannabis (Split Design)</option>
                                    <option value="type3" {{ ($hero['type'] ?? '') == 'type3' ? 'selected' : '' }}>Type 3: Testosterone (Floating Card)</option>
                                </select>
                            </div>
                        </div>

                        {{-- TYPE DEPENDENT FIELDS --}}
                        <div id="hero_type_fields">
                            
                            {{-- Type 1 & 3 Shared Background --}}
                            <div class="hero-field-group hero-type-type1 hero-type-type3">
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label>Background / Banner Image</label>
                                        <input type="file" class="form-control-file" name="hero_background_image"
                                               accept="image/jpeg,image/png,image/jpg,image/webp">
                                        @if(!empty($hero['background_image']))
                                            <div class="mt-2 d-flex align-items-center" style="gap:12px;">
                                                <img src="{{ asset('images/upload/' . $hero['background_image']) }}"
                                                     alt="Hero Banner" style="height:80px;border-radius:4px;border:1px solid #dee2e6;object-fit:cover;">
                                                <small class="text-muted">Current banner image. Upload a new file to replace it.</small>
                                            </div>
                                        @endif
                                        <small class="text-muted">Accepted: jpeg, png, jpg, webp — max 2 MB. This image is used as the background.</small>
                                    </div>
                                </div>
                            </div>

                            {{-- TYPE 1 FIELDS --}}
                            <div class="hero-field-group hero-type-type1">
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

                            {{-- TYPE 2 FIELDS (Cannabis) --}}
                            <div class="hero-field-group hero-type-type2">
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label>Hero Heading (use &lt;span class="text-success"&gt;Cannabis&lt;/span&gt; for color)</label>
                                        <input type="text" class="form-control" name="sections[hero][t2_heading]"
                                               value="{{ $hero['t2_heading'] ?? 'Therapie mit medizinischem Cannabis' }}">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" rows="3" name="sections[hero][t2_description]">{{ $hero['t2_description'] ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>CTA Text</label>
                                        <input type="text" class="form-control" name="sections[hero][cta_text]"
                                               value="{{ $hero['cta_text'] ?? 'Zu den medizinischen Fragen' }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Pricing Subtext</label>
                                        <input type="text" class="form-control" name="sections[hero][t2_subtext]"
                                               value="{{ $hero['t2_subtext'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Main Image (transparent recommended)</label>
                                        <input type="file" class="form-control-file" name="sections[hero][t2_main_image]"
                                               accept="image/*.">
                                        @if(!empty($hero['t2_main_image']))
                                            <div class="mt-2">
                                                <img src="{{ asset('images/upload/' . $hero['t2_main_image']) }}"
                                                     style="height:80px; border-radius:4px; border:1px solid #ddd;">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Rating Value</label>
                                        <input type="text" class="form-control" name="sections[hero][rating_value]"
                                               value="{{ $hero['rating_value'] ?? '4,79' }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Review Count</label>
                                        <input type="text" class="form-control" name="sections[hero][rating_count]"
                                               value="{{ $hero['rating_count'] ?? '14.082' }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label>Info Box 1 Value</label>
                                        <input type="text" class="form-control" name="sections[hero][t2_info_1_val]"
                                               value="{{ $hero['t2_info_1_val'] ?? '700+' }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Info Box 1 Label</label>
                                        <input type="text" class="form-control" name="sections[hero][t2_info_1_lbl]"
                                               value="{{ $hero['t2_info_1_lbl'] ?? 'ANGESCHLOSSENE APOTHEKEN' }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Info Box 2 Value</label>
                                        <input type="text" class="form-control" name="sections[hero][t2_info_2_val]"
                                               value="{{ $hero['t2_info_2_val'] ?? '1,5K+' }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Info Box 2 Label</label>
                                        <input type="text" class="form-control" name="sections[hero][t2_info_2_lbl]"
                                               value="{{ $hero['t2_info_2_lbl'] ?? 'CANNABIS BLÜTEN' }}">
                                    </div>
                                </div>
                            </div>

                            {{-- TYPE 3 FIELDS (Testosterone) --}}
                            <div class="hero-field-group hero-type-type3">
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label>Hero Heading</label>
                                        <input type="text" class="form-control" name="sections[hero][t3_heading]"
                                               value="{{ $hero['t3_heading'] ?? '' }}">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label>Hero Subheading</label>
                                        <textarea class="form-control" rows="2" name="sections[hero][t3_subheading]">{{ $hero['t3_subheading'] ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>CTA 1 (Solid) Text</label>
                                            <input type="text" class="form-control" name="sections[hero][t3_cta_1_text]"
                                                   value="{{ $hero['t3_cta_1_text'] ?? 'Jetzt Beratung starten' }}">
                                        </div>
                                        <div class="form-group">
                                            <label>CTA 1 URL</label>
                                            <input type="text" class="form-control" name="sections[hero][t3_cta_1_url]"
                                                   value="{{ $hero['t3_cta_1_url'] ?? '#' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>CTA 2 (Outline) Text</label>
                                            <input type="text" class="form-control" name="sections[hero][t3_cta_2_text]"
                                                   value="{{ $hero['t3_cta_2_text'] ?? 'Mehr erfahren' }}">
                                        </div>
                                        <div class="form-group">
                                            <label>CTA 2 URL</label>
                                            <input type="text" class="form-control" name="sections[hero][t3_cta_2_url]"
                                                   value="{{ $hero['t3_cta_2_url'] ?? '#' }}">
                                        </div>
                                    </div>
                                </div>
                                <label class="font-weight-bold d-block mt-2">Bottom Feature Items (3 items recommended)</label>
                                <div id="t3_bottom_items_container">
                                    @php $t3Items = $hero['t3_bottom_items'] ?? [['icon'=>'bi-person','text'=>'Deutsche Ärzte'],['icon'=>'bi-shield-check','text'=>'100% DSGVO-konform'],['icon'=>'bi-truck','text'=>'Expressversand']]; @endphp
                                    @foreach($t3Items as $idx => $t3Item)
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm" name="sections[hero][t3_bottom_items][{{ $idx }}][icon]" 
                                                   value="{{ $t3Item['icon'] ?? '' }}" placeholder="Icon (e.g. bi-person)">
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control form-control-sm" name="sections[hero][t3_bottom_items][{{ $idx }}][text]" 
                                                   value="{{ $t3Item['text'] ?? '' }}" placeholder="Text">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger js-remove-t3-item">×</button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-1 js-add-t3-item">+ Add Item</button>
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

                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Steps Design Type</label>
                            <select name="sections[steps][type]" id="steps_type_selector" class="form-control select2">
                                <option value="type1" {{ ($steps['type'] ?? 'type1') == 'type1' ? 'selected' : '' }}>Type 1 (Default - Wide Cards)</option>
                                <option value="type2" {{ ($steps['type'] ?? 'type1') == 'type2' ? 'selected' : '' }}>Type 2 (Testosterone - Grid Cards)</option>
                            </select>
                        </div>

                        <div id="steps_type_fields">
                            {{-- TYPE 1 FIELDS (Default) --}}
                            <div class="steps-field-group steps-type-type1">
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

                            {{-- TYPE 2 FIELDS (Testosterone) --}}
                            <div class="steps-field-group steps-type-type2">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Main Title (e.g. So einfach geht's)</label>
                                        <input type="text" class="form-control" name="sections[steps][t2_title]"
                                               value="{{ $steps['t2_title'] ?? 'So einfach geht\'s' }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Subtitle (Highlighted Row)</label>
                                        <input type="text" class="form-control" name="sections[steps][t2_subtitle]"
                                               value="{{ $steps['t2_subtitle'] ?? 'In 3 Schritten zur Behandlung' }}">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label>Description under Title</label>
                                        <textarea class="form-control" rows="2" name="sections[steps][t2_desc]">{{ $steps['t2_desc'] ?? '' }}</textarea>
                                    </div>
                                </div>

                                @foreach($defaultSteps as $i => $default)
                                @php $step = $stepsData[$i] ?? $default; @endphp
                                <div class="card card-body mb-3" style="background:#f8f9fa;">
                                    <h6 class="font-weight-bold mb-3 text-danger">Step {{ $i + 1 }}</h6>
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label class="text-muted" style="font-size:0.8rem;">Step Icon (Boxicons class)</label>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="sections[steps][steps][{{ $i }}][icon]"
                                                   value="{{ $step['icon'] ?? $default['icon'] }}" placeholder="bx bx-file">
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <label class="text-muted" style="font-size:0.8rem;">Title (Use &lt;span style="color:#000"&gt;...&lt;/span&gt; for black text)</label>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="sections[steps][steps][{{ $i }}][t2_title]"
                                                   value="{{ $step['t2_title'] ?? ($i==0 ? 'Fragebogen ausfüllen' : ($i==1 ? 'Ärztliche Prüfung' : 'Lieferung in 1-2 Werktagen')) }}">
                                        </div>
                                        <div class="col-12 form-group">
                                            <label class="text-muted" style="font-size:0.8rem;">Description</label>
                                            <textarea class="form-control form-control-sm" rows="2"
                                                      name="sections[steps][steps][{{ $i }}][t2_description]">{{ $step['description'] ?? $default['description'] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
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
                                <div class="card-header d-flex align-items-center justify-content-between py-2" style="background:#e9ecef;">
                                    <strong data-toggle="collapse" data-target="#article-body-{{ $ai }}" style="cursor:pointer; flex:1;">Article {{ $ai + 1 }}: {{ $article['heading'] ?? '' }}</strong>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-remove-article ml-2">× Remove</button>
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
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Section Title</label>
                                <input type="text" class="form-control" name="sections[faq][title]"
                                       value="{{ $faqCms['title'] ?? 'Frequently asked questions' }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Extra Title (Subtitle)</label>
                                <input type="text" class="form-control" name="sections[faq][subtitle]"
                                       value="{{ $faqCms['subtitle'] ?? '' }}">
                            </div>
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

            {{-- ============================
                 8. TESTOSTERONE INFO
            ============================ --}}
            @php
                $ti = $cms['testo_info'] ?? [];
                $defaultTestoCards = [
                    ['icon' => 'bi-activity',     'title' => 'Fertige Injektion',        'subtitle' => 'Sofort einsatzbereit, keine Vorbereitung'],
                    ['icon' => 'bi-check-circle', 'title' => 'Keine Vorbereitung nötig', 'subtitle' => 'Kein Mischen, kein Dosieren'],
                    ['icon' => 'bi-person',       'title' => 'Ärztlich dosiert',         'subtitle' => 'Individuell geprüft und verschrieben'],
                    ['icon' => 'bi-truck',        'title' => 'Express-Lieferung',        'subtitle' => 'Diskret in 1-2 Werktagen bei Ihnen'],
                ];
                $tiCards = $ti['cards'] ?? $defaultTestoCards;
            @endphp
            <div class="card mb-0 border-0 border-bottom rounded-0">
                <div class="card-header cms-panel-header" id="headingTestoInfo" style="background:#f8f9fa;">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button"
                                data-toggle="collapse" data-target="#collapseTestoInfo"
                                aria-expanded="false" aria-controls="collapseTestoInfo">
                            <i class="fas fa-syringe mr-2" style="color:#e63946;"></i> Section 8 — Testosterone Info
                        </button>
                    </h2>
                </div>
                <div id="collapseTestoInfo" class="collapse" data-parent="#cmsSectionsAccordion">
                    <div class="card-body">
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[testo_info][enabled]" value="1"
                                   {{ ($ti['enabled'] ?? true) ? 'checked' : '' }}>
                            Show Testosterone Info Section
                        </label>
                        <div class="form-group">
                            <label>Section Heading</label>
                            <input type="text" class="form-control" name="sections[testo_info][heading]"
                                   value="{{ $ti['heading'] ?? 'Was ist eine Testosteron-Injektion?' }}">
                        </div>
                        <div class="form-group">
                            <label>Paragraph 1</label>
                            <textarea class="form-control" rows="3"
                                      name="sections[testo_info][paragraph_1]">{{ $ti['paragraph_1'] ?? 'Testosteron ist das wichtigste männliche Sexualhormon und spielt eine zentrale Rolle für Energie, Muskelaufbau, Stimmung und Libido. Mit zunehmendem Alter oder durch bestimmte Erkrankungen kann der Testosteronspiegel sinken — oft mit spürbaren Auswirkungen auf Körper und Wohlbefinden.' }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Paragraph 2</label>
                            <textarea class="form-control" rows="3"
                                      name="sections[testo_info][paragraph_2]">{{ $ti['paragraph_2'] ?? 'Unsere fertige Testosteron-Injektion wurde speziell für die einfache Anwendung entwickelt: kein Mischen, kein Vorbereiten. Sie ist ärztlich dosiert, qualitätsgeprüft und sofort einsatzbereit. Ideal für Männer, die ihren Testosteronspiegel effektiv und unkompliziert anheben möchten.' }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Paragraph 3</label>
                            <textarea class="form-control" rows="3"
                                      name="sections[testo_info][paragraph_3]">{{ $ti['paragraph_3'] ?? 'Die Behandlung erfolgt unter ärztlicher Aufsicht: Ein zugelassener Arzt prüft Ihre Angaben, stellt das Rezept aus und die fertige Injektion wird diskret zu Ihnen nach Hause geliefert.' }}</textarea>
                        </div>

                        <label class="font-weight-bold d-block mt-3 mb-2">Info Cards (4 fixed)</label>
                        @foreach($defaultTestoCards as $i => $default)
                        @php $tiCard = $tiCards[$i] ?? $default; @endphp
                        <div class="card card-body mb-3" style="background:#f8f9fa;">
                            <h6 class="font-weight-bold mb-3 text-primary">Card {{ $i + 1 }}</h6>
                            <div class="row">
                                <div class="col-md-3 form-group mb-0">
                                    <label class="text-muted" style="font-size:0.8rem;">Bootstrap Icon Class</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="sections[testo_info][cards][{{ $i }}][icon]"
                                           value="{{ $tiCard['icon'] ?? $default['icon'] }}"
                                           placeholder="e.g. bi-activity">
                                </div>
                                <div class="col-md-4 form-group mb-0">
                                    <label class="text-muted" style="font-size:0.8rem;">Title</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="sections[testo_info][cards][{{ $i }}][title]"
                                           value="{{ $tiCard['title'] ?? $default['title'] }}">
                                </div>
                                <div class="col-md-5 form-group mb-0">
                                    <label class="text-muted" style="font-size:0.8rem;">Subtitle</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="sections[testo_info][cards][{{ $i }}][subtitle]"
                                           value="{{ $tiCard['subtitle'] ?? $default['subtitle'] }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ============================
                 9. TESTOSTERONE TREATMENTS
            ============================ --}}
            @php
                $tt = $cms['testo_treatments'] ?? [];
                $defaultTreatCards = [
                    ['image' => null, 'title' => 'Energie und Antrieb zurückgewinnen',     'description' => 'Spüren Sie wieder mehr Vitalität, Leistungsfähigkeit und Lebensfreude. Unsere Testosteron-Injektion unterstützt Sie dabei, Ihren Alltag mit neuer Energie zu meistern.', 'button_text' => 'Behandlung starten', 'button_url' => '#'],
                    ['image' => null, 'title' => 'Fertige Injektion — einfach und sicher', 'description' => 'Keine komplizierte Vorbereitung, kein Mischen. Die Injektion ist ärztlich dosiert und sofort anwendbar — für maximale Sicherheit und Komfort.',                         'button_text' => 'Jetzt anfragen',     'button_url' => '#'],
                ];
                $ttCards = $tt['cards'] ?? $defaultTreatCards;
            @endphp
            <div class="card mb-0 border-0 border-bottom rounded-0">
                <div class="card-header cms-panel-header" id="headingTestoTreat" style="background:#f8f9fa;">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button"
                                data-toggle="collapse" data-target="#collapseTestoTreat"
                                aria-expanded="false" aria-controls="collapseTestoTreat">
                            <i class="fas fa-flask mr-2" style="color:#8b5cf6;"></i> Section 9 — Testosterone Treatments
                        </button>
                    </h2>
                </div>
                <div id="collapseTestoTreat" class="collapse" data-parent="#cmsSectionsAccordion">
                    <div class="card-body">
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[testo_treatments][enabled]" value="1"
                                   {{ ($tt['enabled'] ?? true) ? 'checked' : '' }}>
                            Show Testosterone Treatments Section
                        </label>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Section Heading</label>
                                <input type="text" class="form-control" name="sections[testo_treatments][heading]"
                                       value="{{ $tt['heading'] ?? 'Unsere Testosteron-Behandlungen' }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Section Subheading</label>
                                <input type="text" class="form-control" name="sections[testo_treatments][subheading]"
                                       value="{{ $tt['subheading'] ?? 'Wählen Sie die passende Behandlung — ärztlich geprüft und fertig zur Anwendung.' }}">
                            </div>
                        </div>

                        <label class="font-weight-bold d-block mt-2 mb-2">Treatment Cards (2 fixed)</label>
                        @foreach($defaultTreatCards as $i => $default)
                        @php $ttCard = $ttCards[$i] ?? $default; @endphp
                        <div class="card card-body mb-3" style="background:#f8f9fa;">
                            <h6 class="font-weight-bold mb-3 text-primary">Card {{ $i + 1 }}</h6>
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label class="text-muted" style="font-size:0.8rem;">Card Image</label>
                                    @if(!empty($ttCard['image']))
                                        <div class="mb-1">
                                            <img src="{{ asset('images/upload/' . $ttCard['image']) }}"
                                                 style="height:48px; border-radius:6px; object-fit:cover; border:1px solid #ddd;">
                                        </div>
                                        <small class="text-muted d-block mb-1">Upload to replace</small>
                                    @endif
                                    <input type="file" class="form-control-file"
                                           name="sections[testo_treatments][cards][{{ $i }}][image]"
                                           accept=".jpg,.jpeg,.png,.webp">
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-12 form-group">
                                            <label class="text-muted" style="font-size:0.8rem;">Title</label>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="sections[testo_treatments][cards][{{ $i }}][title]"
                                                   value="{{ $ttCard['title'] ?? $default['title'] }}">
                                        </div>
                                        <div class="col-12 form-group">
                                            <label class="text-muted" style="font-size:0.8rem;">Description</label>
                                            <textarea class="form-control form-control-sm" rows="2"
                                                      name="sections[testo_treatments][cards][{{ $i }}][description]">{{ $ttCard['description'] ?? $default['description'] }}</textarea>
                                        </div>
                                        <div class="col-md-6 form-group mb-0">
                                            <label class="text-muted" style="font-size:0.8rem;">Button Text</label>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="sections[testo_treatments][cards][{{ $i }}][button_text]"
                                                   value="{{ $ttCard['button_text'] ?? $default['button_text'] }}">
                                        </div>
                                        <div class="col-md-6 form-group mb-0">
                                            <label class="text-muted" style="font-size:0.8rem;">Button URL</label>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="sections[testo_treatments][cards][{{ $i }}][button_url]"
                                                   value="{{ $ttCard['button_url'] ?? $default['button_url'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ============================
                 10. SECURITY / TRUST
            ============================ --}}
            @php
                $sec = $cms['security'] ?? [];
                $defaultSecCards = [
                    ['icon' => 'bi-shield', 'title' => '100% DSGVO-konform',   'description' => 'Ihre persönlichen und medizinischen Daten werden nach höchsten deutschen Datenschutzstandards verschlüsselt und geschützt.'],
                    ['icon' => 'bi-person', 'title' => 'Deutsche Ärzte',        'description' => 'Alle Rezepte werden von in Deutschland zugelassenen Ärzten ausgestellt. Qualität und Sicherheit stehen bei uns an erster Stelle.'],
                    ['icon' => 'bi-lock',   'title' => 'Diskret & vertraulich', 'description' => 'Neutrale Verpackung, verschlüsselte Kommunikation und keine Weitergabe Ihrer Daten an Dritte.'],
                ];
                $secCards = $sec['cards'] ?? $defaultSecCards;
            @endphp
            <div class="card mb-0 border-0 rounded-0">
                <div class="card-header cms-panel-header" id="headingSecurity" style="background:#f8f9fa;">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button"
                                data-toggle="collapse" data-target="#collapseSecurity"
                                aria-expanded="false" aria-controls="collapseSecurity">
                            <i class="fas fa-shield-alt mr-2" style="color:#e63946;"></i> Section 10 — Security / Trust
                        </button>
                    </h2>
                </div>
                <div id="collapseSecurity" class="collapse" data-parent="#cmsSectionsAccordion">
                    <div class="card-body">
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[security][enabled]" value="1"
                                   {{ ($sec['enabled'] ?? true) ? 'checked' : '' }}>
                            Show Security Section
                        </label>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Section Heading</label>
                                <input type="text" class="form-control" name="sections[security][heading]"
                                       value="{{ $sec['heading'] ?? 'Ihre Sicherheit ist unsere Priorität' }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Section Subheading</label>
                                <input type="text" class="form-control" name="sections[security][subheading]"
                                       value="{{ $sec['subheading'] ?? 'Vertrauen, Datenschutz und medizinische Qualität — darauf können Sie sich bei dr.fuxx verlassen.' }}">
                            </div>
                        </div>

                        <label class="font-weight-bold d-block mt-2 mb-2">Security Cards (3 fixed)</label>
                        @foreach($defaultSecCards as $i => $default)
                        @php $secCard = $secCards[$i] ?? $default; @endphp
                        <div class="card card-body mb-3" style="background:#f8f9fa;">
                            <h6 class="font-weight-bold mb-3 text-primary">Card {{ $i + 1 }}</h6>
                            <div class="row">
                                <div class="col-md-3 form-group mb-0">
                                    <label class="text-muted" style="font-size:0.8rem;">Bootstrap Icon Class</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="sections[security][cards][{{ $i }}][icon]"
                                           value="{{ $secCard['icon'] ?? $default['icon'] }}"
                                           placeholder="e.g. bi-shield">
                                </div>
                                <div class="col-md-4 form-group mb-0">
                                    <label class="text-muted" style="font-size:0.8rem;">Title</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="sections[security][cards][{{ $i }}][title]"
                                           value="{{ $secCard['title'] ?? $default['title'] }}">
                                </div>
                                <div class="col-md-5 form-group mb-0">
                                    <label class="text-muted" style="font-size:0.8rem;">Description</label>
                                    <textarea class="form-control form-control-sm" rows="2"
                                              name="sections[security][cards][{{ $i }}][description]">{{ $secCard['description'] ?? $default['description'] }}</textarea>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ============================
                 11. SIDEBAR NAVIGATION
            ============================ --}}
            @php
                $snav = $cms['sidebar_nav'] ?? [];
                $snavItems = $snav['items'] ?? [];
            @endphp
            <div class="card mb-0 border-0 rounded-0">
                <div class="card-header cms-panel-header" id="headingSidebarNav" style="background:#f8f9fa;">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button"
                                data-toggle="collapse" data-target="#collapseSidebarNav"
                                aria-expanded="false" aria-controls="collapseSidebarNav">
                            <i class="fas fa-bars mr-2" style="color:#6f42c1;"></i> Section 11 — Sidebar Navigation
                        </button>
                    </h2>
                </div>
                <div id="collapseSidebarNav" class="collapse" data-parent="#cmsSectionsAccordion">
                    <div class="card-body">
                        <label class="cms-toggle-label mb-3">
                            <input type="checkbox" name="sections[sidebar_nav][enabled]" value="1"
                                   {{ ($snav['enabled'] ?? false) ? 'checked' : '' }}>
                            Show Sidebar Navigation on category page
                        </label>
                        <p class="text-muted" style="font-size:0.85rem;">
                            Add main categories and optional sub-categories that appear as a sidebar on the public category page.
                            Set <strong>Type = Link</strong> to open a direct URL, or <strong>Type = Dropdown</strong> to show a sub-menu list.
                        </p>

                        <div id="snav-items-container">
                            @forelse($snavItems as $si => $snavItem)
                            <div class="snav-item card card-body mb-3 border" style="background:#f8f9fa;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <strong class="snav-item-label">{{ $snavItem['label'] ?? 'Item '.($si+1) }}</strong>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-remove-snav-item">× Remove</button>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="text-muted" style="font-size:0.8rem;">Label / Title</label>
                                        <input type="text" class="form-control form-control-sm snav-label-input"
                                               name="sections[sidebar_nav][items][{{ $si }}][label]"
                                               value="{{ $snavItem['label'] ?? '' }}" placeholder="e.g. Behandlungen">
                                    </div>
                                    <div class="col-md-3 form-group mb-2">
                                        <label class="text-muted" style="font-size:0.8rem;">Icon (Font Awesome class)</label>
                                        <input type="text" class="form-control form-control-sm"
                                               name="sections[sidebar_nav][items][{{ $si }}][icon]"
                                               value="{{ $snavItem['icon'] ?? 'fas fa-circle' }}" placeholder="e.g. fas fa-home">
                                    </div>
                                    <div class="col-md-3 form-group mb-2">
                                        <label class="text-muted" style="font-size:0.8rem;">Type</label>
                                        <select class="form-control form-control-sm snav-type-select"
                                                name="sections[sidebar_nav][items][{{ $si }}][type]">
                                            <option value="link" {{ ($snavItem['type'] ?? 'link') === 'link' ? 'selected' : '' }}>Link (direct URL)</option>
                                            <option value="dropdown" {{ ($snavItem['type'] ?? '') === 'dropdown' ? 'selected' : '' }}>Dropdown (sub-items)</option>
                                        </select>
                                    </div>
                                </div>
                                {{-- URL field (shown when type=link) --}}
                                <div class="snav-url-row row {{ ($snavItem['type'] ?? 'link') === 'dropdown' ? 'd-none' : '' }}">
                                    <div class="col-md-6 form-group mb-2">
                                        <label class="text-muted" style="font-size:0.8rem;">URL</label>
                                        <input type="text" class="form-control form-control-sm"
                                               name="sections[sidebar_nav][items][{{ $si }}][url]"
                                               value="{{ $snavItem['url'] ?? '#' }}" placeholder="/category/page">
                                    </div>
                                </div>
                                {{-- Sub-items (shown when type=dropdown) --}}
                                <div class="snav-subitems-wrap {{ ($snavItem['type'] ?? 'link') === 'link' ? 'd-none' : '' }}">
                                    <label class="font-weight-bold d-block mb-2" style="font-size:0.85rem;">Sub-items</label>
                                    <div class="snav-subitems-container pl-3" style="border-left:3px solid #6f42c1;">
                                        @foreach($snavItem['sub_items'] ?? [] as $sj => $subItem)
                                        <div class="snav-subitem d-flex mb-2" style="gap:8px;">
                                            <input type="text" class="form-control form-control-sm"
                                                   name="sections[sidebar_nav][items][{{ $si }}][sub_items][{{ $sj }}][label]"
                                                   value="{{ $subItem['label'] ?? '' }}" placeholder="Sub-item label">
                                            <input type="text" class="form-control form-control-sm"
                                                   name="sections[sidebar_nav][items][{{ $si }}][sub_items][{{ $sj }}][url]"
                                                   value="{{ $subItem['url'] ?? '#' }}" placeholder="URL">
                                            <button type="button" class="btn btn-sm btn-outline-danger js-remove-snav-subitem flex-shrink-0">×</button>
                                        </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-1 js-add-snav-subitem">+ Add Sub-item</button>
                                </div>
                            </div>
                            @empty
                            <p id="no-snav-msg" class="text-muted">No sidebar items yet. Click "Add Sidebar Item" to begin.</p>
                            @endforelse
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm mt-2 js-add-snav-item">
                            <i class="fas fa-plus mr-1"></i> Add Sidebar Item
                        </button>
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
        <div class="card-header d-flex align-items-center justify-content-between py-2" style="background:#e9ecef;">
            <strong class="cms-article-label" data-toggle="collapse" data-target="#article-body-__AI__" style="cursor:pointer; flex:1;">Article __NUM__</strong>
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-article ml-2">× Remove</button>
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
<template id="tpl-t3-item">
    <div class="row mb-2">
        <div class="col-md-4">
            <input type="text" class="form-control form-control-sm" name="sections[hero][t3_bottom_items][__IDX__][icon]" 
                   value="" placeholder="Icon (e.g. bi-person)">
        </div>
        <div class="col-md-7">
            <input type="text" class="form-control form-control-sm" name="sections[hero][t3_bottom_items][__IDX__][text]" 
                   value="" placeholder="Text">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-t3-item">×</button>
        </div>
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
            if (n) {
                $(this).attr('name', n.replace(/\[headers\]\[\d+\]/, '[headers][' + ci + ']'));
            } else {
                $(this).attr('name', 'sections[medical_content][articles][' + ai + '][blocks][' + bi + '][headers][' + ci + ']');
            }
        });
        $block.find('.cms-table-body .cms-table-data-row').each(function (ri) {
            $(this).find('td input').each(function (ci) {
                var n = $(this).attr('name');
                if (n) {
                    n = n.replace(/\[rows\]\[\d+\]\[\d+\]/, '[rows][' + ri + '][' + ci + ']');
                } else {
                    n = 'sections[medical_content][articles][' + ai + '][blocks][' + bi + '][rows][' + ri + '][' + ci + ']';
                }
                $(this).attr('name', n);
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

    // ── Hero Type Toggling ───────────────────────────────────────────
    function toggleHeroFields() {
        var type = $('#hero_type_selector').val();
        $('#hero_type_fields .hero-field-group').hide();
        $('#hero_type_fields .hero-type-' + type).fadeIn();
    }
    $('#hero_type_selector').on('change', toggleHeroFields);
    toggleHeroFields(); // init

    // ── Steps Type Toggling ───────────────────────────────────────────
    function toggleStepsFields() {
        var type = $('#steps_type_selector').val();
        $('#steps_type_fields .steps-field-group').hide();
        $('#steps_type_fields .steps-type-' + type).fadeIn();
    }
    $('#steps_type_selector').on('change', toggleStepsFields);
    toggleStepsFields(); // init

    // ── T3 Bottom Items ──────────────────────────────────────────────
    function reindexT3Items() {
        $('#t3_bottom_items_container .row').each(function (i) {
            $(this).find('input').each(function () {
                var n = $(this).attr('name');
                if (n) $(this).attr('name', n.replace(/\[t3_bottom_items\]\[\d+\]/, '[t3_bottom_items][' + i + ']'));
            });
        });
    }
    $(document).on('click', '.js-add-t3-item', function () {
        var count = $('#t3_bottom_items_container .row').length;
        var html = document.getElementById('tpl-t3-item').innerHTML.replace(/__IDX__/g, count);
        $('#t3_bottom_items_container').append(html);
    });
    $(document).on('click', '.js-remove-t3-item', function () {
        $(this).closest('.row').remove();
        reindexT3Items();
    });

    // ── Sidebar Navigation ────────────────────────────────────────────
    function reindexSnavItems() {
        $('#snav-items-container .snav-item').each(function (i) {
            $(this).find('[name]').each(function () {
                var n = $(this).attr('name');
                if (n) $(this).attr('name', n.replace(/\[items\]\[\d+\]/, '[items][' + i + ']'));
            });
            reindexSnavSubitems($(this), i);
        });
    }
    function reindexSnavSubitems($item, i) {
        $item.find('.snav-subitem').each(function (j) {
            $(this).find('[name]').each(function () {
                var n = $(this).attr('name');
                if (n) $(this).attr('name', n.replace(/\[sub_items\]\[\d+\]/, '[sub_items][' + j + ']'));
            });
        });
    }

    // Add a top-level sidebar item
    $(document).on('click', '.js-add-snav-item', function () {
        $('#no-snav-msg').remove();
        var i = $('#snav-items-container .snav-item').length;
        var html = document.getElementById('tpl-snav-item').innerHTML.replace(/__SI__/g, i);
        $('#snav-items-container').append(html);
    });

    // Remove a top-level sidebar item
    $(document).on('click', '.js-remove-snav-item', function () {
        $(this).closest('.snav-item').remove();
        reindexSnavItems();
    });

    // Toggle URL / sub-items based on type
    $(document).on('change', '.snav-type-select', function () {
        var $item = $(this).closest('.snav-item');
        if ($(this).val() === 'dropdown') {
            $item.find('.snav-url-row').addClass('d-none');
            $item.find('.snav-subitems-wrap').removeClass('d-none');
        } else {
            $item.find('.snav-url-row').removeClass('d-none');
            $item.find('.snav-subitems-wrap').addClass('d-none');
        }
    });

    // Live-update item label from input
    $(document).on('input', '.snav-label-input', function () {
        var val = $(this).val() || 'Untitled';
        $(this).closest('.snav-item').find('.snav-item-label').text(val);
    });

    // Add a sub-item
    $(document).on('click', '.js-add-snav-subitem', function () {
        var $item = $(this).closest('.snav-item');
        var i = $('#snav-items-container .snav-item').index($item);
        var j = $item.find('.snav-subitem').length;
        var html = '<div class="snav-subitem d-flex mb-2" style="gap:8px;">' +
            '<input type="text" class="form-control form-control-sm" ' +
                   'name="sections[sidebar_nav][items][' + i + '][sub_items][' + j + '][label]" placeholder="Sub-item label">' +
            '<input type="text" class="form-control form-control-sm" ' +
                   'name="sections[sidebar_nav][items][' + i + '][sub_items][' + j + '][url]" placeholder="URL" value="#">' +
            '<button type="button" class="btn btn-sm btn-outline-danger js-remove-snav-subitem flex-shrink-0">×</button>' +
            '</div>';
        $item.find('.snav-subitems-container').append(html);
    });

    // Remove a sub-item
    $(document).on('click', '.js-remove-snav-subitem', function () {
        var $item = $(this).closest('.snav-item');
        var i = $('#snav-items-container .snav-item').index($item);
        $(this).closest('.snav-subitem').remove();
        reindexSnavSubitems($item, i);
    });

    // ── Re-index ALL before submit ────────────────────────────────────
    $('form').on('submit', function () {
        reindexToc();
        reindexArticles();
        reindexFaq();
        reindexDrParas();
        reindexT3Items();
        reindexSnavItems();
    });
});
</script>

{{-- SortableJS for section ordering --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
(function () {
    var list  = document.getElementById('cmsSortableOrder');
    var input = document.getElementById('cmsOrderInput');
    if (!list || !input) return;

    new Sortable(list, {
        animation: 150,
        ghostClass: 'cms-order-ghost',
        onEnd: function () {
            var order = [];
            list.querySelectorAll('li[data-key]').forEach(function (li) {
                order.push(li.getAttribute('data-key'));
            });
            input.value = order.join(',');
        }
    });
})();
</script>
<style>
.cms-order-ghost { opacity: 0.5; background: #e8f0fe !important; border-color: #4285f4 !important; }
</style>

{{-- Sidebar Nav item template --}}
<template id="tpl-snav-item">
    <div class="snav-item card card-body mb-3 border" style="background:#f8f9fa;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <strong class="snav-item-label">New Item</strong>
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-snav-item">× Remove</button>
        </div>
        <div class="row">
            <div class="col-md-4 form-group mb-2">
                <label class="text-muted" style="font-size:0.8rem;">Label / Title</label>
                <input type="text" class="form-control form-control-sm snav-label-input"
                       name="sections[sidebar_nav][items][__SI__][label]"
                       value="" placeholder="e.g. Behandlungen">
            </div>
            <div class="col-md-3 form-group mb-2">
                <label class="text-muted" style="font-size:0.8rem;">Icon (Font Awesome class)</label>
                <input type="text" class="form-control form-control-sm"
                       name="sections[sidebar_nav][items][__SI__][icon]"
                       value="fas fa-circle" placeholder="e.g. fas fa-home">
            </div>
            <div class="col-md-3 form-group mb-2">
                <label class="text-muted" style="font-size:0.8rem;">Type</label>
                <select class="form-control form-control-sm snav-type-select"
                        name="sections[sidebar_nav][items][__SI__][type]">
                    <option value="link" selected>Link (direct URL)</option>
                    <option value="dropdown">Dropdown (sub-items)</option>
                </select>
            </div>
        </div>
        <div class="snav-url-row row">
            <div class="col-md-6 form-group mb-2">
                <label class="text-muted" style="font-size:0.8rem;">URL</label>
                <input type="text" class="form-control form-control-sm"
                       name="sections[sidebar_nav][items][__SI__][url]"
                       value="#" placeholder="/category/page">
            </div>
        </div>
        <div class="snav-subitems-wrap d-none">
            <label class="font-weight-bold d-block mb-2" style="font-size:0.85rem;">Sub-items</label>
            <div class="snav-subitems-container pl-3" style="border-left:3px solid #6f42c1;"></div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-1 js-add-snav-subitem">+ Add Sub-item</button>
        </div>
    </div>
</template>
