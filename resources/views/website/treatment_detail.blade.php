<!DOCTYPE html>
<html lang="en">
<head>
    @php
    $setting = App\Models\Setting::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - {{ $setting->business_name }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link href="{{asset('css/new-design.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/website_header.css') }}">
    <link href="{{asset('styles.css')}}?v={{ time() }}" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Global typography: Inter (body) + Clash Display (headings) -->
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6, .display-4, .display-5 { font-family: 'Clash Display', sans-serif; }
        h1 span, h2 span, h3 span, h4 span, h5 span, h6 span, .display-4 span, .display-5 span { font-family: inherit; }
    </style>
    <style>
        .bg-light-green {
            background-color: #f0fdf4;
        }
        .bg-light-red {
            background-color: #fef2f2;
        }
        .bg-light-yellow {
            background-color: #fefce8;
        }
    </style>
    <style>
        /* ── FAQ Section ── */
        .faq-section {
            background: radial-gradient(circle at 12% 18%, rgba(124,58,237,0.07), transparent 34%),
                        radial-gradient(circle at 88% 10%, rgba(139,92,246,0.06), transparent 28%),
                        linear-gradient(180deg, #f0edf6 0%, #ece8f3 100%);
            padding: 100px 20px;
        }
        .faq-container {
            max-width: 850px;
            margin: 0 auto;
        }
        .faq-header {
            text-align: center;
            margin-bottom: 60px;
        }
        .faq-heading {
            font-family: 'Inter', system-ui, sans-serif !important;
            font-size: 3rem;
            font-weight: 900 !important;
            color: #1a1a1a;
            margin-bottom: 12px;
            letter-spacing: -1px;
        }
        .faq-accordion {
            background: transparent;
        }
        .faq-item {
            border-bottom: 1px solid #e0e0e0;
            background: transparent;
        }
        .faq-question {
            padding: 28px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            border-left: 5px solid transparent;
            padding-left: 0;
            transition: all 0.3s ease;
        }
        .faq-q-text {
            font-family: 'Inter', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #1f2129;
            line-height: 1.4;
        }
        .faq-chevron {
            font-size: 1.1rem;
            color: #1f2129;
            transition: transform 0.3s ease;
            font-weight: bold;
        }
        .faq-question:hover {
            border-left: 5px solid var(--primary-color, #7c3aed);
            padding-left: 25px;
        }
        .faq-question:hover .faq-q-text {
            color: var(--primary-color, #7c3aed);
        }
        .faq-question[aria-expanded="true"] .faq-chevron {
            transform: rotate(180deg);
        }
        .faq-question:hover .faq-chevron {
            color: var(--primary-color, #7c3aed);
        }
        .faq-answer {
            padding: 0 0 28px 0;
            color: #605f64;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.6;
            max-width: 100%;
        }
        @media (max-width: 768px) {
            .faq-section { padding: 60px 15px; }
            .faq-heading { font-size: 1.8rem; }
            .faq-q-text { font-size: 1rem; }
        }
    </style>
</head>
<body>
    @include('layout.partials.skeleton_loader')
<!-- Navigation -->
@include('layout.partials.navbar_website')

<!-- Breadcrumb -->
<div class="bg-light border-bottom">
    <div class="container py-3">
        <a href="{{ route('categories') }}" class="breadcrumb-link text-decoration-none">
            <i class="bi bi-chevron-left"></i> Back to all treatments
        </a>
    </div>
</div>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-orange-light text-orange mb-3">
                    {{ $treatment ? $treatment->name : 'General Medicine' }}
                </span>
                <h1 class="display-4 fw-bold mb-3">{{ $category->name }}</h1>
                <p class="lead text-muted mb-4">
                    {{ $category->description ? Str::limit($category->description, 150) : 'Professional medical consultation and treatment for your needs.' }}
                </p>
                
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="display-6 fw-bold text-primary">
                        @if(isset($category->price) && $category->price)
                            {{ number_format($category->price, 2, ',', '.') }} €
                        @else
                            Price not available
                        @endif
                    </div>
                    <div class="text-muted">per month</div>
                </div>
                
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span class="small">Free shipping</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span class="small">Discreet packaging</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span class="small">Licensed doctors</span>
                    </div>
                </div>
                
                @if($hasQuestionnaire)
                    <a href="{{ auth()->check() ? url('/questionnaire/category/' . $category->id) : url('/patient-login?redirect_to=' . urlencode('/questionnaire/category/' . $category->id)) }}" 
                       class="btn btn-primary btn-lg w-100 w-lg-auto">
                        Start consultation now
                    </a>
                @else
                    <a href="{{ route('categories') }}" class="btn btn-primary btn-lg w-100 w-lg-auto">
                        Browse treatments
                    </a>
                @endif
            </div>
            
            <div class="col-lg-4">
                <div class="row g-3">
                    <div class="col-4">
                        <div class="card text-center p-3">
                            <i class="bi bi-clock text-primary fs-3 mb-2"></i>
                            <div class="fw-bold">24-48h</div>
                            <div class="small text-muted">Delivery</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card text-center p-3">
                            <i class="bi bi-shield-check text-primary fs-3 mb-2"></i>
                            <div class="fw-bold">100%</div>
                            <div class="small text-muted">Discreet</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card text-center p-3">
                            <i class="bi bi-box-seam text-primary fs-3 mb-2"></i>
                            <div class="fw-bold">Free</div>
                            <div class="small text-muted">Shipping</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-5">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Overview -->
                <div class="mb-5">
                    <h2 class="display-6 fw-bold mb-4">Overview</h2>
                    <p class="lead text-muted">
                        {{ $category->description ?: 'This treatment provides professional medical consultation and care tailored to your specific needs. Our licensed doctors will review your information and provide appropriate medical guidance.' }}
                    </p>
                </div>

                <!-- How It Works -->
                <div class="mb-5">
                    <h2 class="display-6 fw-bold mb-4">How it works</h2>
                    <div class="d-flex flex-column gap-3">
                        @php
                            $howItWorks = [];
                            if (isset($category->how_it_works) && is_array($category->how_it_works)) {
                                $howItWorks = $category->how_it_works;
                            } elseif (isset($category->how_it_works) && is_string($category->how_it_works)) {
                                $howItWorks = json_decode($category->how_it_works, true) ?? [];
                            }
                            
                            // Default steps if not available
                            if (empty($howItWorks)) {
                                $howItWorks = [
                                    'Complete our secure medical questionnaire about your health and medical history',
                                    'Our certified doctors review your information and assess your suitability',
                                    'If appropriate, a prescription is issued and your medication is prepared',
                                    'Your treatment is delivered discreetly to your door within 24-48 hours'
                                ];
                            }
                        @endphp
                        
                        @foreach($howItWorks as $index => $step)
                            <div class="d-flex gap-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; flex-shrink: 0;">
                                    {{ $index + 1 }}
                                </div>
                                <p class="mb-0 pt-1">{{ is_array($step) ? ($step['step'] ?? $step['description'] ?? '') : $step }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Medications -->
                @if($medicines->count() > 0)
                <div class="mb-5">
                    <h2 class="display-6 fw-bold mb-4">Available medications</h2>
                    <div class="d-flex flex-column gap-3">
                        @foreach($medicines as $medicine)
                            <div class="card bg-light border-0 p-4">
                                <h5 class="fw-semibold mb-2">{{ $medicine->name }}</h5>
                                @if($medicine->description)
                                    <p class="text-muted mb-2">{{ $medicine->description }}</p>
                                @endif
                                @if($medicine->strength)
                                    <p class="small text-muted mb-0">
                                        <span class="fw-medium">Strength:</span> {{ $medicine->strength }}
                                        @if($medicine->form)
                                            ({{ $medicine->form }})
                                        @endif
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Suitable For / Not Suitable For -->
                @php
                    $suitableFor = [];
                    $notSuitableFor = [];
                    
                    if (isset($category->suitable_for)) {
                        if (is_array($category->suitable_for)) {
                            $suitableFor = $category->suitable_for;
                        } elseif (is_string($category->suitable_for)) {
                            $suitableFor = json_decode($category->suitable_for, true) ?? [];
                        }
                    }
                    
                    if (isset($category->not_suitable_for)) {
                        if (is_array($category->not_suitable_for)) {
                            $notSuitableFor = $category->not_suitable_for;
                        } elseif (is_string($category->not_suitable_for)) {
                            $notSuitableFor = json_decode($category->not_suitable_for, true) ?? [];
                        }
                    }
                    
                    // Default values if not available
                    if (empty($suitableFor)) {
                        $suitableFor = [
                            'Adults over 18 years of age',
                            'Individuals seeking professional medical consultation',
                            'Patients looking for discreet and convenient healthcare'
                        ];
                    }
                    
                    if (empty($notSuitableFor)) {
                        $notSuitableFor = [
                            'Children under 18 years of age',
                            'Pregnant or breastfeeding women (unless specifically approved)',
                            'Individuals with severe medical conditions requiring immediate attention'
                        ];
                    }
                @endphp
                
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="card border-success border-2 bg-light-green h-100 p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <h5 class="fw-semibold mb-0">Suitable for</h5>
                            </div>
                            <ul class="list-unstyled mb-0">
                                @foreach($suitableFor as $item)
                                    <li class="d-flex gap-2 mb-2">
                                        <i class="bi bi-check-circle-fill text-success" style="flex-shrink: 0;"></i>
                                        <span class="small">{{ is_array($item) ? ($item['item'] ?? $item['text'] ?? '') : $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-danger border-2 bg-light-red h-100 p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-exclamation-circle-fill text-danger fs-5"></i>
                                <h5 class="fw-semibold mb-0">Not suitable for</h5>
                            </div>
                            <ul class="list-unstyled mb-0">
                                @foreach($notSuitableFor as $item)
                                    <li class="d-flex gap-2 mb-2">
                                        <i class="bi bi-exclamation-circle-fill text-danger" style="flex-shrink: 0;"></i>
                                        <span class="small">{{ is_array($item) ? ($item['item'] ?? $item['text'] ?? '') : $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Side Effects -->
                @php
                    $sideEffects = [];
                    if (isset($category->side_effects)) {
                        if (is_array($category->side_effects)) {
                            $sideEffects = $category->side_effects;
                        } elseif (is_string($category->side_effects)) {
                            $sideEffects = json_decode($category->side_effects, true) ?? [];
                        }
                    }
                    
                    // Default side effects if not available
                    if (empty($sideEffects)) {
                        $sideEffects = [
                            'Mild nausea',
                            'Headache',
                            'Dizziness',
                            'Fatigue'
                        ];
                    }
                @endphp
                
                <div class="mb-5">
                    <h2 class="display-6 fw-bold mb-4">Possible side effects</h2>
                    <p class="text-muted mb-3">
                        Like all medications, this treatment may cause side effects, although not everyone gets them. 
                        Most side effects are mild and temporary.
                    </p>
                    <div class="card border-warning border-2 bg-light-yellow p-4 mb-3">
                        <div class="row g-3">
                            @foreach($sideEffects as $effect)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-warning rounded-circle" style="width: 8px; height: 8px;"></div>
                                        <span>{{ is_array($effect) ? ($effect['effect'] ?? $effect['name'] ?? '') : $effect }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <p class="small text-muted">
                        If you experience severe side effects, stop taking the medication and contact a doctor immediately.
                    </p>
                </div>

            </div>

            <!-- Right Column - Sticky CTA -->
            <div class="col-lg-4">
                <div class="sticky-cta">
                    <div class="card border-2 shadow p-4">
                        <h5 class="fw-bold mb-3">Ready to get started?</h5>
                        <p class="text-muted mb-4">
                            Complete a quick medical questionnaire and get your prescription today.
                        </p>
                        
                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light-green rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-check text-success"></i>
                                </div>
                                <span class="small">Free consultation with doctor</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light-green rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-check text-success"></i>
                                </div>
                                <span class="small">Delivered in 24-48 hours</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light-green rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-check text-success"></i>
                                </div>
                                <span class="small">Cancel anytime</span>
                            </div>
                        </div>

                        <div class="card bg-light border-0 p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Starting from:</span>
                                <span class="h4 text-primary fw-bold mb-0">
                                    @if(isset($category->price) && $category->price)
                                        {{ number_format($category->price, 2, ',', '.') }} €
                                    @else
                                        Price not available
                                    @endif
                                </span>
                            </div>
                            <p class="small text-muted mb-0">Free shipping • Discreet packaging</p>
                        </div>

                        @if($hasQuestionnaire)
                            <a href="{{ auth()->check() ? url('/questionnaire/category/' . $category->id) : url('/patient-login?redirect_to=' . urlencode('/questionnaire/category/' . $category->id)) }}" 
                               class="btn btn-primary btn-lg w-100 mb-2">
                                Start consultation
                            </a>
                        @else
                            <a href="{{ route('categories') }}" class="btn btn-primary btn-lg w-100 mb-2">
                                Browse treatments
                            </a>
                        @endif
                        <p class="small text-center text-muted mb-0">
                            Takes 5 minutes • 100% confidential
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQs -->
@php
    $faqs = [];
    if (isset($category->faqs)) {
        if (is_array($category->faqs)) {
            $faqs = $category->faqs;
        } elseif (is_string($category->faqs)) {
            $faqs = json_decode($category->faqs, true) ?? [];
        }
    }
    if (empty($faqs)) {
        $faqs = [
            ['question' => 'How long does the consultation process take?', 'answer' => 'The entire process typically takes 24-48 hours from questionnaire submission to prescription approval and shipping.'],
            ['question' => 'Is this treatment suitable for me?', 'answer' => 'Our doctors will review your questionnaire and medical history to determine if this treatment is appropriate for your specific situation.'],
            ['question' => 'What if I have questions about my medication?', 'answer' => 'You can contact our medical team at any time with questions about your treatment. We provide ongoing support throughout your treatment period.'],
        ];
    }
@endphp
<section class="faq-section home-story-faq">
    <div class="faq-container">
        <div class="faq-header">
            <h2 class="faq-heading">Frequently asked questions</h2>
        </div>
        <div class="faq-accordion" id="categoryFaqAccordion">
            @foreach($faqs as $index => $faq)
            <div class="faq-item">
                <div class="faq-question"
                     data-bs-toggle="collapse"
                     data-bs-target="#catFaqCollapse{{ $index }}"
                     aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                     aria-controls="catFaqCollapse{{ $index }}">
                    <span class="faq-q-text">{{ is_array($faq) ? ($faq['question'] ?? '') : (isset($faq->question) ? $faq->question : '') }}</span>
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </div>
                <div id="catFaqCollapse{{ $index }}"
                     class="collapse {{ $index === 0 ? 'show' : '' }}"
                     data-bs-parent="#categoryFaqAccordion">
                    <div class="faq-answer">
                        {!! nl2br(e(is_array($faq) ? ($faq['answer'] ?? '') : (isset($faq->answer) ? $faq->answer : ''))) !!}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Bottom CTA -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold mb-3">Still have questions?</h2>
                <p class="lead text-muted mb-4">
                    Our medical team is here to help. Contact us for personalized advice.
                </p>
                <a href="#" class="btn btn-outline-primary btn-lg border-2">Contact our team</a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
@include('layout.partials.footer')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
