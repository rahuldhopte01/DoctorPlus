<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\Treatments;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = Category::with('treatment')->orderBy('id', 'DESC')->get();

        return view('superAdmin.category.category', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('category_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $treats = Treatments::whereStatus(1)->orderBy('id', 'DESC')->get();

        return view('superAdmin.category.create_category', compact('treats'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'bail|required|unique:category',
            'price'                 => 'bail|nullable|numeric|min:0',
            'image'                 => 'bail|mimes:jpeg,png,jpg|max:1000',
            'hero_background_image' => 'bail|nullable|mimes:jpeg,png,jpg,webp|max:2048',
        ],
            [
                'image.max'                 => 'The Image May Not Be Greater Than 1 MegaBytes.',
                'hero_background_image.max' => 'The hero banner image may not be greater than 2 MB.',
            ]);
        $data = $request->only(['name', 'description', 'treatment_id']);
        $data['price'] = $request->input('price') ?? 0;
        if ($request->hasFile('image')) {
            $data['image'] = (new CustomController)->imageUpload($request->image);
        } else {
            $data['image'] = 'prod_default.png';
        }
        $heroImage = null;
        if ($request->hasFile('hero_background_image')) {
            $heroImage = (new CustomController)->imageUpload($request->file('hero_background_image'));
        }
        $data['status'] = $request->has('status') ? 1 : 0;
        $data['is_cannaleo_only'] = $request->boolean('is_cannaleo_only');
        $data['cms_sections'] = $this->buildCmsSections($request, null, $heroImage);
        Category::create($data);

        return redirect('category')->withStatus(__('Category created successfully..!'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $treats = Treatments::whereStatus(1)->orderBy('id', 'DESC')->get();
        $category = Category::find($id);

        return view('superAdmin.category.edit_category', compact('category', 'treats'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'                  => 'bail|required|unique:category,name,'.$id.',id',
            'price'                 => 'bail|nullable|numeric|min:0',
            'image'                 => 'bail|mimes:jpeg,png,jpg|max:1000',
            'hero_background_image' => 'bail|nullable|mimes:jpeg,png,jpg,webp|max:2048',
        ],
            [
                'image.max'                 => 'The Image May Not Be Greater Than 1 MegaBytes.',
                'hero_background_image.max' => 'The hero banner image may not be greater than 2 MB.',
            ]);
        $data = $request->only(['name', 'description', 'treatment_id']);
        $data['price'] = $request->input('price') ?? 0;
        $data['is_cannaleo_only'] = $request->boolean('is_cannaleo_only');
        $category = Category::find($id);
        if ($request->hasFile('image')) {
            (new CustomController)->deleteFile($category->image);
            $data['image'] = (new CustomController)->imageUpload($request->image);
        }
        $heroImage = null;
        if ($request->hasFile('hero_background_image')) {
            $existingHeroImage = $category->cms_sections['hero']['background_image'] ?? null;
            if ($existingHeroImage) {
                (new CustomController)->deleteFile($existingHeroImage);
            }
            $heroImage = (new CustomController)->imageUpload($request->file('hero_background_image'));
        }
        $data['cms_sections'] = $this->buildCmsSections($request, $category->cms_sections, $heroImage);
        $category->update($data);

        return redirect('category')->withStatus(__('Category updated successfully..!!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (Doctor::whereHas('categories', function($query) use ($id) {
            $query->where('category.id', $id);
        })->exists()) {
            return response(['success' => false, 'msg' => 'This category is being used by one or more doctors!']);
        }
        $category = Category::find($id);
        $category->delete();

        return response(['success' => true]);
    }

    public function change_status(Request $reqeust)
    {
        $category = Category::find($reqeust->id);
        $data['status'] = $category->status == 1 ? 0 : 1;
        $category->update($data);

        return response(['success' => true]);
    }

    private function buildCmsSections(Request $request, ?array $existing, ?string $heroImage = null): array
    {
        $s = $request->input('sections', []);
        $existing = $existing ?? [];

        // --- Hero ---
        $existingHero = $existing['hero'] ?? [];
        $heroInput = $s['hero'] ?? [];

        $hero = array_merge([
            'type'                 => 'type1', // Default
            'enabled'              => true,
            'background_image'     => null,
            'cta_text'             => 'Zu den medizinischen Fragen',
            'cta_color'            => '#3b6fd4',
            'consultation_fee'     => '29',
            'badge_enabled'        => true,
            'badge_percentage'     => '85',
            'badge_text'           => 'der Männer berichten von einer Besserung',
            'badge_bg_color_start' => '#3b6fd4',
            'badge_bg_color_end'   => '#1e3c8c',
            'rating_enabled'       => true,
            'rating_value'         => '4,79',
            'rating_count'         => '14.082',

            // Type 2 specific defaults
            't2_heading'           => 'Therapie mit medizinischem Cannabis',
            't2_description'       => 'Füllen Sie einen Online-Fragebogen aus und lassen Sie Ihre Angaben von einem zugelassenen Arzt überprüfen...',
            't2_subtext'           => 'Ärztliche Beurteilung und Verordnung 14,9 € + Cannabis-Therapeutikum ab 3 €',
            't2_main_image'        => null,
            't2_info_1_val'        => '700+',
            't2_info_1_lbl'        => 'ANGESCHLOSSENE APOTHEKEN',
            't2_info_2_val'        => '1,5K+',
            't2_info_2_lbl'        => 'CANNABIS BLÜTEN',

            // Type 3 specific defaults
            't3_heading'           => 'Testosteron-Injektion — fertig zur Direktnutzung',
            't3_subheading'        => 'Ärztlich geprüft, sofort einsatzbereit. Kein Mischen, keine Vorbereitung — einfach anwenden.',
            't3_cta_1_text'        => 'Jetzt Beratung starten',
            't3_cta_1_url'         => '#',
            't3_cta_2_text'        => 'Mehr erfahren',
            't3_cta_2_url'         => '#',
            't3_bottom_items'      => [
                ['icon' => 'bx bx-user', 'text' => 'Deutsche Ärzte'],
                ['icon' => 'bx bx-shield-check', 'text' => '100% DSGVO-konform'],
                ['icon' => 'bx bx-truck', 'text' => 'Expressversand'],
            ],
        ], $existingHero, [
            'type'                 => $heroInput['type'] ?? 'type1',
            'enabled'              => isset($heroInput['enabled']),
            'cta_text'             => $heroInput['cta_text'] ?? 'Zu den medizinischen Fragen',
            'cta_color'            => $heroInput['cta_color'] ?? '#3b6fd4',
            'consultation_fee'     => $heroInput['consultation_fee'] ?? '29',
            'badge_enabled'        => isset($heroInput['badge_enabled']),
            'badge_percentage'     => $heroInput['badge_percentage'] ?? '85',
            'badge_text'           => $heroInput['badge_text'] ?? 'der Männer berichten von einer Besserung',
            'badge_bg_color_start' => $heroInput['badge_bg_color_start'] ?? '#3b6fd4',
            'badge_bg_color_end'   => $heroInput['badge_bg_color_end'] ?? '#1e3c8c',
            'rating_enabled'       => isset($heroInput['rating_enabled']),
            'rating_value'         => $heroInput['rating_value'] ?? '4,79',
            'rating_count'         => $heroInput['rating_count'] ?? '14.082',

            // Type 2 inputs
            't2_heading'           => $heroInput['t2_heading'] ?? 'Therapie mit medizinischem Cannabis',
            't2_description'       => $heroInput['t2_description'] ?? 'Füllen Sie einen Online-Fragebogen aus und lassen Sie Ihre Angaben von einem zugelassenen Arzt überprüfen...',
            't2_subtext'           => $heroInput['t2_subtext'] ?? 'Ärztliche Beurteilung und Verordnung 14,9 € + Cannabis-Therapeutikum ab 3 €',
            't2_info_1_val'        => $heroInput['t2_info_1_val'] ?? '700+',
            't2_info_1_lbl'        => $heroInput['t2_info_1_lbl'] ?? 'ANGESCHLOSSENE APOTHEKEN',
            't2_info_2_val'        => $heroInput['t2_info_2_val'] ?? '1,5K+',
            't2_info_2_lbl'        => $heroInput['t2_info_2_lbl'] ?? 'CANNABIS BLÜTEN',

            // Type 3 inputs
            't3_heading'           => $heroInput['t3_heading'] ?? 'Testosteron-Injektion — fertig zur Direktnutzung',
            't3_subheading'        => $heroInput['t3_subheading'] ?? 'Ärztlich geprüft, sofort einsatzbereit. Kein Mischen, keine Vorbereitung — einfach anwenden.',
            't3_cta_1_text'        => $heroInput['t3_cta_1_text'] ?? 'Jetzt Beratung starten',
            't3_cta_1_url'         => $heroInput['t3_cta_1_url'] ?? '#',
            't3_cta_2_text'        => $heroInput['t3_cta_2_text'] ?? 'Mehr erfahren',
            't3_cta_2_url'         => $heroInput['t3_cta_2_url'] ?? '#',
        ]);

        // Process bottom items for Type 3
        if (isset($heroInput['t3_bottom_items'])) {
            $bottomItems = [];
            foreach ($heroInput['t3_bottom_items'] as $item) {
                if (!empty($item['text'])) {
                    $bottomItems[] = [
                        'icon' => $item['icon'] ?? 'bx bx-check-circle',
                        'text' => $item['text'],
                    ];
                }
            }
            $hero['t3_bottom_items'] = $bottomItems;
        }

        // Overwrite background_image (Banner) only when a new file was actually uploaded
        if ($heroImage !== null) {
            $hero['background_image'] = $heroImage;
        }

        // Handle Type 2 Main Image
        if ($request->hasFile('sections.hero.t2_main_image')) {
            $existingT2Img = $existingHero['t2_main_image'] ?? null;
            if ($existingT2Img) {
                (new CustomController)->deleteFile($existingT2Img);
            }
            $hero['t2_main_image'] = (new CustomController)->imageUpload($request->file('sections.hero.t2_main_image'));
        }

        // --- Features Bar ---
        $defaultFeatures = [
            ['enabled' => true, 'title' => 'Das Rezept wird online ausgestellt.',      'subtitle' => 'Ein Klinikbesuch ist nicht erforderlich.'],
            ['enabled' => true, 'title' => 'Lieferung innerhalb von 1–2 Werktagen.',   'subtitle' => 'Schnelle, zuverlässige Lieferung.'],
            ['enabled' => true, 'title' => 'Originalmedizin und Generika.',            'subtitle' => 'Aus zertifizierten Apotheken.'],
            ['enabled' => true, 'title' => 'Beratung über Online-Fragebogen.',         'subtitle' => 'Schnelle medizinische Beratung'],
        ];
        $featuresInput = $s['features_bar']['features'] ?? [];
        $features = [];
        foreach ($defaultFeatures as $i => $default) {
            $features[] = [
                'enabled'  => isset($featuresInput[$i]['enabled']),
                'title'    => $featuresInput[$i]['title'] ?? $default['title'],
                'subtitle' => $featuresInput[$i]['subtitle'] ?? $default['subtitle'],
            ];
        }
        $featuresBar = [
            'enabled'  => isset($s['features_bar']['enabled']),
            'bg_color' => $s['features_bar']['bg_color'] ?? '#fafafa',
            'features' => $features,
        ];

        // --- Steps ---
        $defaultSteps = [
            ['title_plain' => 'Füllen Sie den',  'title_highlighted' => 'medizinischen Fragebogen aus', 'highlight_color' => '#3b6fd4', 'description' => 'Starten Sie die Online-Konsultation und beantworten Sie die medizinischen Fragen.',           'image' => null, 'icon' => 'bx bx-file'],
            ['title_plain' => 'Wählen Sie die',  'title_highlighted' => 'gewünschte Behandlung',        'highlight_color' => '#3b6fd4', 'description' => 'Der behandelnde Arzt prüft Ihre Angaben und stellt Ihnen bei Bedarf ein Rezept aus.',          'image' => null, 'icon' => 'bx bx-user'],
            ['title_plain' => 'Lieferung in',    'title_highlighted' => '1–2 Werktagen',                'highlight_color' => '#3b6fd4', 'description' => 'Sie erhalten Ihre Medikamente diskret und sicher.',                                             'image' => null, 'icon' => 'bx bx-truck'],
        ];
        $stepsInput = $s['steps']['steps'] ?? [];
        $existingSteps = $existing['steps']['steps'] ?? [];
        $steps = [];
        foreach ($defaultSteps as $i => $default) {
            $existingImage = $existingSteps[$i]['image'] ?? null;
            $uploadedImage = null;
            if ($request->hasFile("sections.steps.steps.{$i}.image")) {
                $uploadedImage = (new CustomController)->imageUpload($request->file("sections.steps.steps.{$i}.image"));
            }
            $steps[] = [
                'title_plain'       => $stepsInput[$i]['title_plain'] ?? $default['title_plain'],
                'title_highlighted' => $stepsInput[$i]['title_highlighted'] ?? $default['title_highlighted'],
                'highlight_color'   => $stepsInput[$i]['highlight_color'] ?? $default['highlight_color'],
                'description'       => $stepsInput[$i]['description'] ?? $default['description'],
                'image'             => $uploadedImage ?? $existingImage,
                'icon'              => $stepsInput[$i]['icon'] ?? $default['icon'],
                't2_title'          => $stepsInput[$i]['t2_title'] ?? ($i==0 ? 'Fragebogen ausfüllen' : ($i==1 ? 'Ärztliche Prüfung' : 'Lieferung in 1-2 Werktagen')),
            ];
        }
        $stepsSection = [
            'enabled'            => isset($s['steps']['enabled']),
            'type'               => $s['steps']['type'] ?? 'type1',
            'section_title'      => $s['steps']['section_title'] ?? '3 einfache Schritte',
            'section_subtitle'   => $s['steps']['section_subtitle'] ?? '100 % online',
            'subtitle_color'     => $s['steps']['subtitle_color'] ?? '#3b6fd4',
            'step_number_bg'     => $s['steps']['step_number_bg'] ?? '#3b6fd4',
            't2_title'           => $s['steps']['t2_title'] ?? 'So einfach geht\'s',
            't2_subtitle'        => $s['steps']['t2_subtitle'] ?? 'In 3 Schritten zur Behandlung',
            't2_subtitle_italic' => $s['steps']['t2_subtitle_italic'] ?? 'In 3 Schritten zur Behandlung',
            't2_desc'            => $s['steps']['t2_desc'] ?? 'Schnell, diskret und ärztlich betreut — Ihre Testosteron-Injektion in wenigen Schritten.',
            'steps'              => $steps,
        ];

        // --- Payment Bar ---
        $methodsInput = $s['payment_bar']['methods'] ?? [];
        $paymentBar = [
            'enabled'  => isset($s['payment_bar']['enabled']),
            'label'    => $s['payment_bar']['label'] ?? 'Akzeptierte Zahlungsmethoden:',
            'bg_color' => $s['payment_bar']['bg_color'] ?? '#1a1a1a',
            'methods'  => [
                'klarna'    => isset($methodsInput['klarna']),
                'visa'      => isset($methodsInput['visa']),
                'maestro'   => isset($methodsInput['maestro']),
                'gpay'      => isset($methodsInput['gpay']),
                'apple_pay' => isset($methodsInput['apple_pay']),
                'paypal'    => isset($methodsInput['paypal']),
            ],
        ];

        // --- Medical Content ---
        $mcInput    = $s['medical_content'] ?? [];
        $tocItems   = [];
        foreach ($mcInput['toc_items'] ?? [] as $ti) {
            $label = trim($ti['label'] ?? '');
            if ($label !== '') {
                $tocItems[] = ['label' => $label, 'url' => $ti['url'] ?? '#'];
            }
        }
        $articles = [];
        foreach ($mcInput['articles'] ?? [] as $article) {
            $heading = trim($article['heading'] ?? '');
            if ($heading === '') continue;
            $blocks = [];
            foreach ($article['blocks'] ?? [] as $block) {
                $type = $block['type'] ?? '';
                switch ($type) {
                    case 'text':
                        $content = trim($block['content'] ?? '');
                        if ($content !== '') $blocks[] = ['type' => 'text', 'content' => $content];
                        break;
                    case 'subheading':
                        $text = trim($block['text'] ?? '');
                        if ($text !== '') $blocks[] = ['type' => 'subheading', 'level' => in_array($block['level'] ?? '', ['h3','h4']) ? $block['level'] : 'h3', 'text' => $text];
                        break;
                    case 'table':
                        $headers = array_values(array_filter(array_map('trim', $block['headers'] ?? []), fn($h) => $h !== ''));
                        $rows = [];
                        foreach ($block['rows'] ?? [] as $row) {
                            $rows[] = array_map('trim', array_values($row));
                        }
                        if (!empty($headers)) {
                            $blocks[] = [
                                'type'              => 'table',
                                'heading'           => trim($block['heading'] ?? ''),
                                'header_bg'         => $block['header_bg'] ?? '#3b6fd4',
                                'header_text_color' => $block['header_text_color'] ?? '#ffffff',
                                'alt_row_bg'        => $block['alt_row_bg'] ?? '#f8f9fa',
                                'border_color'      => $block['border_color'] ?? '#dee2e6',
                                'headers'           => $headers,
                                'rows'              => $rows,
                            ];
                        }
                        break;
                    case 'list':
                        $items = [];
                        foreach ($block['items'] ?? [] as $item) {
                            $text = trim($item['text'] ?? '');
                            if ($text !== '') $items[] = ['label' => trim($item['label'] ?? ''), 'text' => $text];
                        }
                        if (!empty($items)) $blocks[] = ['type' => 'list', 'items' => $items];
                        break;
                    case 'callout':
                        $content = trim($block['content'] ?? '');
                        if ($content !== '') {
                            $blocks[] = [
                                'type'         => 'callout',
                                'bg_color'     => $block['bg_color'] ?? '#eff3fb',
                                'border_color' => $block['border_color'] ?? '#3b6fd4',
                                'heading'      => trim($block['heading'] ?? ''),
                                'content'      => $content,
                            ];
                        }
                        break;
                }
            }
            $articles[] = [
                'anchor_id' => trim($article['anchor_id'] ?? \Str::slug($heading)),
                'heading'   => $heading,
                'blocks'    => $blocks,
            ];
        }
        $medicalContent = [
            'enabled'       => isset($mcInput['enabled']),
            'section_title' => $mcInput['section_title'] ?? 'Behandlungen bei',
            'toc_enabled'   => isset($mcInput['toc_enabled']),
            'toc_title'     => $mcInput['toc_title'] ?? 'Themenliste',
            'toc_items'     => $tocItems,
            'articles'      => $articles,
        ];

        // --- Doctor Review ---
        $drInput        = $s['doctor_review'] ?? [];
        $existingDrImage = $existing['doctor_review']['image'] ?? null;
        $drImage = $existingDrImage;
        if ($request->hasFile('sections.doctor_review.image')) {
            $drImage = (new CustomController)->imageUpload($request->file('sections.doctor_review.image'));
        }
        $drParagraphs = [];
        foreach ($drInput['paragraphs'] ?? [] as $p) {
            $p = trim($p);
            if ($p !== '') $drParagraphs[] = $p;
        }
        $doctorReview = [
            'enabled'           => isset($drInput['enabled']),
            'image'             => $drImage,
            'name'              => $drInput['name'] ?? 'Dr. med. Experte',
            'role'              => $drInput['role'] ?? 'Facharzt für Urologie',
            'title'             => $drInput['title'] ?? 'Medizinisch-fachlich geprüft',
            'paragraphs'        => $drParagraphs,
            'link_text'         => $drInput['link_text'] ?? 'Redaktionsprozess',
            'link_url'          => $drInput['link_url'] ?? '#',
            'show_last_updated' => isset($drInput['show_last_updated']),
        ];

        // --- FAQ ---
        $faqInput = $s['faq'] ?? [];
        $faqItems = [];
        foreach ($faqInput['items'] ?? [] as $item) {
            $q = trim($item['question'] ?? '');
            $a = trim($item['answer'] ?? '');
            if ($q !== '' && $a !== '') $faqItems[] = ['question' => $q, 'answer' => $a];
        }
        $faq = [
            'enabled' => isset($faqInput['enabled']),
            'title'   => $faqInput['title'] ?? 'Frequently asked questions',
            'items'   => $faqItems,
        ];

        // --- Testosterone Info (Section 8) ---
        $tiInput = $s['testo_info'] ?? [];
        $defaultTestoCards = [
            ['icon' => 'bi-activity',     'title' => 'Fertige Injektion',        'subtitle' => 'Sofort einsatzbereit, keine Vorbereitung'],
            ['icon' => 'bi-check-circle', 'title' => 'Keine Vorbereitung nötig', 'subtitle' => 'Kein Mischen, kein Dosieren'],
            ['icon' => 'bi-person',       'title' => 'Ärztlich dosiert',         'subtitle' => 'Individuell geprüft und verschrieben'],
            ['icon' => 'bi-truck',        'title' => 'Express-Lieferung',        'subtitle' => 'Diskret in 1-2 Werktagen bei Ihnen'],
        ];
        $testoCards = [];
        foreach ($defaultTestoCards as $i => $default) {
            $card = $tiInput['cards'][$i] ?? [];
            $testoCards[] = [
                'icon'     => $card['icon']     ?? $default['icon'],
                'title'    => $card['title']    ?? $default['title'],
                'subtitle' => $card['subtitle'] ?? $default['subtitle'],
            ];
        }
        $testoInfo = [
            'enabled'     => isset($tiInput['enabled']),
            'heading'     => $tiInput['heading']     ?? 'Was ist eine Testosteron-Injektion?',
            'paragraph_1' => $tiInput['paragraph_1'] ?? 'Testosteron ist das wichtigste männliche Sexualhormon und spielt eine zentrale Rolle für Energie, Muskelaufbau, Stimmung und Libido. Mit zunehmendem Alter oder durch bestimmte Erkrankungen kann der Testosteronspiegel sinken — oft mit spürbaren Auswirkungen auf Körper und Wohlbefinden.',
            'paragraph_2' => $tiInput['paragraph_2'] ?? 'Unsere fertige Testosteron-Injektion wurde speziell für die einfache Anwendung entwickelt: kein Mischen, kein Vorbereiten. Sie ist ärztlich dosiert, qualitätsgeprüft und sofort einsatzbereit. Ideal für Männer, die ihren Testosteronspiegel effektiv und unkompliziert anheben möchten.',
            'paragraph_3' => $tiInput['paragraph_3'] ?? 'Die Behandlung erfolgt unter ärztlicher Aufsicht: Ein zugelassener Arzt prüft Ihre Angaben, stellt das Rezept aus und die fertige Injektion wird diskret zu Ihnen nach Hause geliefert.',
            'cards'       => $testoCards,
        ];

        // --- Testosterone Treatments (Section 9) ---
        $ttInput  = $s['testo_treatments'] ?? [];
        $existingTt = $existing['testo_treatments'] ?? [];
        $defaultTreatCards = [
            ['image' => null, 'title' => 'Energie und Antrieb zurückgewinnen',      'description' => 'Spüren Sie wieder mehr Vitalität, Leistungsfähigkeit und Lebensfreude. Unsere Testosteron-Injektion unterstützt Sie dabei, Ihren Alltag mit neuer Energie zu meistern.', 'button_text' => 'Behandlung starten', 'button_url' => '#'],
            ['image' => null, 'title' => 'Fertige Injektion — einfach und sicher',  'description' => 'Keine komplizierte Vorbereitung, kein Mischen. Die Injektion ist ärztlich dosiert und sofort anwendbar — für maximale Sicherheit und Komfort.',                         'button_text' => 'Jetzt anfragen',     'button_url' => '#'],
        ];
        $treatCards = [];
        foreach ($defaultTreatCards as $i => $default) {
            $card            = $ttInput['cards'][$i] ?? [];
            $existingImg     = $existingTt['cards'][$i]['image'] ?? null;
            $uploadedImg     = null;
            if ($request->hasFile("sections.testo_treatments.cards.{$i}.image")) {
                $uploadedImg = (new CustomController)->imageUpload($request->file("sections.testo_treatments.cards.{$i}.image"));
            }
            $treatCards[] = [
                'image'       => $uploadedImg ?? $existingImg,
                'title'       => $card['title']       ?? $default['title'],
                'description' => $card['description'] ?? $default['description'],
                'button_text' => $card['button_text'] ?? $default['button_text'],
                'button_url'  => $card['button_url']  ?? $default['button_url'],
            ];
        }
        $testoTreatments = [
            'enabled'    => isset($ttInput['enabled']),
            'heading'    => $ttInput['heading']    ?? 'Unsere Testosteron-Behandlungen',
            'subheading' => $ttInput['subheading'] ?? 'Wählen Sie die passende Behandlung — ärztlich geprüft und fertig zur Anwendung.',
            'cards'      => $treatCards,
        ];

        // --- Security / Trust (Section 10) ---
        $secInput = $s['security'] ?? [];
        $defaultSecCards = [
            ['icon' => 'bi-shield', 'title' => '100% DSGVO-konform',   'description' => 'Ihre persönlichen und medizinischen Daten werden nach höchsten deutschen Datenschutzstandards verschlüsselt und geschützt.'],
            ['icon' => 'bi-person', 'title' => 'Deutsche Ärzte',        'description' => 'Alle Rezepte werden von in Deutschland zugelassenen Ärzten ausgestellt. Qualität und Sicherheit stehen bei uns an erster Stelle.'],
            ['icon' => 'bi-lock',   'title' => 'Diskret & vertraulich', 'description' => 'Neutrale Verpackung, verschlüsselte Kommunikation und keine Weitergabe Ihrer Daten an Dritte.'],
        ];
        $secCards = [];
        foreach ($defaultSecCards as $i => $default) {
            $card = $secInput['cards'][$i] ?? [];
            $secCards[] = [
                'icon'        => $card['icon']        ?? $default['icon'],
                'title'       => $card['title']       ?? $default['title'],
                'description' => $card['description'] ?? $default['description'],
            ];
        }
        $security = [
            'enabled'    => isset($secInput['enabled']),
            'heading'    => $secInput['heading']    ?? 'Ihre Sicherheit ist unsere Priorität',
            'subheading' => $secInput['subheading'] ?? 'Vertrauen, Datenschutz und medizinische Qualität — darauf können Sie sich bei dr.fuxx verlassen.',
            'cards'      => $secCards,
        ];

        return [
            'hero'             => $hero,
            'features_bar'     => $featuresBar,
            'steps'            => $stepsSection,
            'payment_bar'      => $paymentBar,
            'medical_content'  => $medicalContent,
            'doctor_review'    => $doctorReview,
            'faq'              => $faq,
            'testo_info'       => $testoInfo,
            'testo_treatments' => $testoTreatments,
            'security'         => $security,
        ];
    }
}
