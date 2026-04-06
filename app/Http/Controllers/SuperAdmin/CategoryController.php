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
            'name' => 'bail|required|unique:category',
            'price' => 'bail|required|numeric|min:0',
            'image' => 'bail|mimes:jpeg,png,jpg|max:1000',
        ],
            [
                'image.max' => 'The Image May Not Be Greater Than 1 MegaBytes.',
            ]);
        $data = $request->only(['name', 'description', 'price', 'treatment_id']);
        if ($request->hasFile('image')) {
            $data['image'] = (new CustomController)->imageUpload($request->image);
        } else {
            $data['image'] = 'prod_default.png';
        }
        $data['status'] = $request->has('status') ? 1 : 0;
        $data['is_cannaleo_only'] = $request->boolean('is_cannaleo_only');
        $data['cms_sections'] = $this->buildCmsSections($request, null);
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
            'name' => 'bail|required|unique:category,name,'.$id.',id',
            'price' => 'bail|required|numeric|min:0',
            'image' => 'bail|mimes:jpeg,png,jpg|max:1000',
        ],
            [
                'image.max' => 'The Image May Not Be Greater Than 1 MegaBytes.',
            ]);
        $data = $request->only(['name', 'description', 'price', 'treatment_id']);
        $data['is_cannaleo_only'] = $request->boolean('is_cannaleo_only');
        $category = Category::find($id);
        if ($request->hasFile('image')) {
            (new CustomController)->deleteFile($category->image);
            $data['image'] = (new CustomController)->imageUpload($request->image);
        }
        $data['cms_sections'] = $this->buildCmsSections($request, $category->cms_sections);
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

    private function buildCmsSections(Request $request, ?array $existing): array
    {
        $s = $request->input('sections', []);
        $existing = $existing ?? [];

        // --- Hero ---
        $hero = array_merge([
            'enabled'              => true,
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
        ], $existing['hero'] ?? [], [
            'enabled'              => isset($s['hero']['enabled']),
            'cta_text'             => $s['hero']['cta_text'] ?? 'Zu den medizinischen Fragen',
            'cta_color'            => $s['hero']['cta_color'] ?? '#3b6fd4',
            'consultation_fee'     => $s['hero']['consultation_fee'] ?? '29',
            'badge_enabled'        => isset($s['hero']['badge_enabled']),
            'badge_percentage'     => $s['hero']['badge_percentage'] ?? '85',
            'badge_text'           => $s['hero']['badge_text'] ?? 'der Männer berichten von einer Besserung',
            'badge_bg_color_start' => $s['hero']['badge_bg_color_start'] ?? '#3b6fd4',
            'badge_bg_color_end'   => $s['hero']['badge_bg_color_end'] ?? '#1e3c8c',
            'rating_enabled'       => isset($s['hero']['rating_enabled']),
            'rating_value'         => $s['hero']['rating_value'] ?? '4,79',
            'rating_count'         => $s['hero']['rating_count'] ?? '14.082',
        ]);

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
            ['title_plain' => 'Füllen Sie den',  'title_highlighted' => 'medizinischen Fragebogen aus', 'highlight_color' => '#3b6fd4', 'description' => 'Starten Sie die Online-Konsultation und beantworten Sie die medizinischen Fragen.',           'image' => null],
            ['title_plain' => 'Wählen Sie die',  'title_highlighted' => 'gewünschte Behandlung',        'highlight_color' => '#3b6fd4', 'description' => 'Der behandelnde Arzt prüft Ihre Angaben und stellt Ihnen bei Bedarf ein Rezept aus.',          'image' => null],
            ['title_plain' => 'Lieferung in',    'title_highlighted' => '1–2 Werktagen',                'highlight_color' => '#3b6fd4', 'description' => 'Sie erhalten Ihre Medikamente diskret und sicher.',                                             'image' => null],
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
            ];
        }
        $stepsSection = [
            'enabled'         => isset($s['steps']['enabled']),
            'section_title'   => $s['steps']['section_title'] ?? '3 einfache Schritte',
            'section_subtitle'=> $s['steps']['section_subtitle'] ?? '100 % online',
            'subtitle_color'  => $s['steps']['subtitle_color'] ?? '#3b6fd4',
            'step_number_bg'  => $s['steps']['step_number_bg'] ?? '#3b6fd4',
            'steps'           => $steps,
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

        return [
            'hero'            => $hero,
            'features_bar'    => $featuresBar,
            'steps'           => $stepsSection,
            'payment_bar'     => $paymentBar,
            'medical_content' => $medicalContent,
            'doctor_review'   => $doctorReview,
            'faq'             => $faq,
        ];
    }
}
