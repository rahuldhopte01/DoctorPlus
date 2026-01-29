<!DOCTYPE html>
<html lang="en">
<head>
    @php
    $setting = App\Models\Setting::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->business_name }} - Online Medical Consultation</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link href="{{asset('css/new-design.css')}}" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* Override any conflicting styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            @if($setting->company_logo && file_exists(public_path('images/upload/'.$setting->company_logo)))
                <img src="{{ $setting->logo }}" alt="{{ $setting->business_name }}" height="40">
            @else
                <img src="{{ url('/images/upload_empty/lojf.png') }}" alt="{{ $setting->business_name }}" height="40">
            @endif
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories') }}">Treatments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#how-it-works">How it works</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#faq">Help</a>
                </li>
            </ul>
            
            <div class="d-flex gap-2">
                @if(auth()->check())
                    <a href="{{ url('user_profile') }}" class="btn btn-link text-dark text-decoration-none">
                        <i class="bi bi-person"></i> {{ auth()->user()->name }}
                    </a>
                @else
                    <a href="{{ url('patient-login') }}" class="btn btn-link text-dark text-decoration-none">
                        <i class="bi bi-person"></i> Sign in
                    </a>
                @endif
                <a href="{{ route('categories') }}" class="btn btn-primary">Start treatment</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="badge bg-orange-light text-orange mb-3">Certified Online Medical Practice</span>
                
                <h1 class="display-4 fw-bold mb-3">Online Doctor Visit – Simple, Discreet & Secure</h1>
                
                <p class="lead text-muted mb-4">
                    Get medical consultation and your prescription from the comfort of your home. 
                    Delivered discreetly and free of charge.
                </p>

                <!-- Search Bar -->
                <form action="{{ route('categories') }}" method="GET" class="mb-4">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="What are you looking for? (e.g. Hair loss, Birth control)" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>

                <!-- Trust Indicators -->
                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span class="small">EU-registered doctors</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span class="small">Free shipping</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span class="small">Discreet packaging</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="position-relative">
                    @if($setting->banner_image && file_exists(public_path('images/upload/'.$setting->banner_image)))
                        <img src="{{ url('images/upload/'.$setting->banner_image) }}" 
                             alt="Online medical consultation" 
                             class="img-fluid rounded-3 shadow-lg">
                    @else
                        <img src="https://images.unsplash.com/photo-1758691463198-dc663b8a64e4?w=600&h=400&fit=crop" 
                             alt="Online medical consultation" 
                             class="img-fluid rounded-3 shadow-lg">
                    @endif
                    
                    <!-- Floating card -->
                    <div class="hero-card position-absolute bottom-0 start-0 ms-4 mb-4 bg-white rounded-3 shadow p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="hero-card-icon bg-orange-light rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-check-circle text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ number_format($reviews->count() * 12500) }}+</div>
                                <div class="small text-muted">Satisfied patients</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5 bg-white" id="services">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Our Treatment Areas</h2>
            <p class="lead text-muted">Choose from a variety of treatments – all supervised by certified doctors</p>
        </div>

        <div class="row g-4 mb-4">
            @php
                // Icon and color mapping based on treatment name
                $iconMap = [
                    "Men's Health" => ['icon' => 'bi-heart-pulse', 'color' => 'blue'],
                    "Women's Health" => ['icon' => 'bi-person', 'color' => 'pink'],
                    "General Medicine" => ['icon' => 'bi-capsule', 'color' => 'teal'],
                    "Weight Management" => ['icon' => 'bi-activity', 'color' => 'green'],
                    "Travel Medicine" => ['icon' => 'bi-shield-check', 'color' => 'purple'],
                    "Skin Health" => ['icon' => 'bi-stars', 'color' => 'orange'],
                ];
                
                // Get categories grouped by treatment, limit to 6
                $serviceCategories = $categories->take(6);
            @endphp
            
            @forelse($serviceCategories as $category)
                @php
                    $treatmentName = $category->treatment ? $category->treatment->name : 'General Medicine';
                    $iconData = $iconMap[$treatmentName] ?? ['icon' => 'bi-capsule', 'color' => 'teal'];
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="service-card card h-100 border rounded-3 p-4" onclick="window.location.href='{{ route('category.detail', ['id' => $category->id]) }}'">
                        <div class="service-icon bg-{{ $iconData['color'] }}-light text-{{ $iconData['color'] }} rounded-3 d-inline-flex align-items-center justify-content-center mb-3">
                            <i class="bi {{ $iconData['icon'] }}"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">{{ $category->name }}</h5>
                        <p class="text-muted small mb-3">{{ $category->description ? Str::limit($category->description, 80) : 'Professional medical consultation and treatment' }}</p>
                        <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-primary small fw-medium text-decoration-none">
                            Learn more <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <!-- Fallback static cards if no categories -->
                <div class="col-md-6 col-lg-4">
                    <div class="service-card card h-100 border rounded-3 p-4">
                        <div class="service-icon bg-blue-light text-blue rounded-3 d-inline-flex align-items-center justify-content-center mb-3">
                            <i class="bi bi-heart-pulse"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Men's Health</h5>
                        <p class="text-muted small mb-3">Discreet treatment for erectile dysfunction, hair loss and more</p>
                        <a href="{{ route('categories') }}" class="text-primary small fw-medium text-decoration-none">
                            Learn more <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="text-center">
            <a href="{{ route('categories') }}" class="btn btn-primary btn-lg">View all treatments</a>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-light" id="how-it-works">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">How It Works</h2>
            <p class="lead text-muted">Your treatment in just 4 simple steps</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white border-0 rounded-3 p-4 text-center h-100 position-relative">
                    <div class="step-connector d-none d-lg-block"></div>
                    <div class="position-relative mb-3">
                        <div class="step-icon bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto">
                            <i class="bi bi-file-text text-white"></i>
                        </div>
                        <span class="step-number position-absolute bg-white text-primary border border-primary rounded-circle">01</span>
                    </div>
                    <h5 class="fw-semibold mb-2">Complete questionnaire</h5>
                    <p class="text-muted small">Answer a short medical questionnaire about your health</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card bg-white border-0 rounded-3 p-4 text-center h-100 position-relative">
                    <div class="step-connector d-none d-lg-block"></div>
                    <div class="position-relative mb-3">
                        <div class="step-icon bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto">
                            <i class="bi bi-heart-pulse text-white"></i>
                        </div>
                        <span class="step-number position-absolute bg-white text-primary border border-primary rounded-circle">02</span>
                    </div>
                    <h5 class="fw-semibold mb-2">Medical consultation</h5>
                    <p class="text-muted small">Our certified doctors review your information and create a prescription</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card bg-white border-0 rounded-3 p-4 text-center h-100 position-relative">
                    <div class="step-connector d-none d-lg-block"></div>
                    <div class="position-relative mb-3">
                        <div class="step-icon bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto">
                            <i class="bi bi-box-seam text-white"></i>
                        </div>
                        <span class="step-number position-absolute bg-white text-primary border border-primary rounded-circle">03</span>
                    </div>
                    <h5 class="fw-semibold mb-2">Shipping</h5>
                    <p class="text-muted small">Your medication is delivered discreetly and free of charge</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card bg-white border-0 rounded-3 p-4 text-center h-100">
                    <div class="position-relative mb-3">
                        <div class="step-icon bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto">
                            <i class="bi bi-check-circle text-white"></i>
                        </div>
                        <span class="step-number position-absolute bg-white text-primary border border-primary rounded-circle">04</span>
                    </div>
                    <h5 class="fw-semibold mb-2">Done!</h5>
                    <p class="text-muted small">Receive your treatment at home – usually within 24-48 hours</p>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('categories') }}" class="btn btn-primary btn-lg">Start treatment now</a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5 bg-white" id="about">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="display-5 fw-bold mb-4">About {{ $setting->business_name }}</h2>
                <p class="lead text-muted mb-4">
                    We are a certified online medical practice dedicated to providing accessible, 
                    discreet, and professional healthcare services.
                </p>
                <p class="text-muted mb-4">
                    Our team of licensed doctors is committed to helping you access the treatments 
                    you need from the comfort and privacy of your home. With over {{ number_format($reviews->count() * 12500) }} satisfied 
                    patients, we've built a reputation for excellence in online healthcare.
                </p>
                <p class="text-muted">
                    All our services are fully compliant with medical regulations and GDPR data 
                    protection standards, ensuring your health information remains completely confidential.
                </p>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=600&h=400&fit=crop" 
                     alt="Medical team" 
                     class="img-fluid rounded-3 shadow">
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5 bg-light" id="why-choose-us">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Why Choose Us</h2>
            <p class="lead text-muted">The advantages of our online medical practice</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="bi bi-shield-check text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">100% Discreet</h5>
                    <p class="text-muted small">Plain packaging and confidential service</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="bi bi-clock text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">24-48h Delivery</h5>
                    <p class="text-muted small">Fast and free shipping to your door</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="bi bi-award text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Licensed Doctors</h5>
                    <p class="text-muted small">Certified medical professionals</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="bi bi-lock text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Secure & Private</h5>
                    <p class="text-muted small">GDPR compliant data protection</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Safety & Quality Section -->
<section class="py-5 bg-white" id="safety">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 order-lg-2">
                <h2 class="display-5 fw-bold mb-4">Safety & Quality</h2>
                <p class="lead text-muted mb-4">
                    Your health and safety are our top priorities. We maintain the highest 
                    standards of medical care and data protection.
                </p>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        All medications sourced from licensed pharmacies
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Thorough medical review by qualified doctors
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Secure encrypted data transmission
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Certified processes and quality assurance
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Full GDPR compliance for data protection
                    </li>
                </ul>
            </div>
            <div class="col-lg-6 order-lg-1">
                <img src="https://images.unsplash.com/photo-1584982751601-97dcc096659c?w=600&h=400&fit=crop" 
                     alt="Medical safety" 
                     class="img-fluid rounded-3 shadow">
            </div>
        </div>
    </div>
</section>

<!-- Trust Section -->
<section class="py-5 bg-white" id="trust">
    <div class="container py-4">
        <!-- Trust Features -->
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="bi bi-award text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Certified doctors</h5>
                    <p class="text-muted small">All our doctors are registered and licensed</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="bi bi-lock text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Privacy guaranteed</h5>
                    <p class="text-muted small">Your data is encrypted and protected according to GDPR</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="bi bi-clock text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Fast delivery</h5>
                    <p class="text-muted small">Shipping within 24-48 hours after medical approval</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="bi bi-star-fill text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">
                        @if($reviews->count() > 0)
                            {{ number_format($reviews->avg('rate'), 1) }}/5 rating
                        @else
                        4.8/5 rating
                        @endif
                    </h5>
                    <p class="text-muted small">Over {{ number_format($reviews->count() * 12500) }} satisfied patients trust us</p>
                </div>
            </div>
        </div>

        <!-- Testimonials -->
        <div class="testimonials-section bg-gradient-orange rounded-3 p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h2 class="display-6 fw-bold mb-4">What our patients say</h2>
                    
                    @forelse($reviews->take(2) as $review)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill {{ $i <= $review->rate ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <p class="card-text text-muted mb-2">
                                    "{{ Str::limit($review->review, 150) }}"
                                </p>
                                <p class="fw-semibold mb-0">{{ $review->user ? $review->user->name : 'Anonymous' }}</p>
                                <p class="small text-muted">Verified patient</p>
                            </div>
                        </div>
                    @empty
                        <!-- Fallback testimonials -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="mb-2">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                                <p class="card-text text-muted mb-2">
                                    "Very professional service. The medical consultation was thorough and the medication arrived quickly. Highly recommended!"
                                </p>
                                <p class="fw-semibold mb-0">Michael K.</p>
                                <p class="small text-muted">Verified patient</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&h=400&fit=crop" 
                         alt="Healthcare professionals" 
                         class="img-fluid rounded-3 shadow">
                </div>
            </div>
        </div>

        <!-- Certifications -->
        <div class="mt-5 pt-4 border-top">
            <p class="text-center text-muted small mb-3">Certified and verified by:</p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <div class="bg-light rounded px-4 py-2">
                    <span class="fw-medium">Certified</span>
                </div>
                <div class="bg-light rounded px-4 py-2">
                    <span class="fw-medium">Licensed</span>
                </div>
                <div class="bg-light rounded px-4 py-2">
                    <span class="fw-medium">GDPR Compliant</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light" id="faq">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Frequently Asked Questions</h2>
            <p class="lead text-muted">Everything you need to know about our service</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How does the online consultation work?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Simply complete our secure medical questionnaire. Our licensed doctors will review your information and, if appropriate, issue a prescription. The entire process typically takes 24-48 hours.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Are the doctors real and licensed?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Yes, all our doctors are fully licensed and registered with medical authorities. They have years of experience and are qualified to prescribe medication.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                How long does delivery take?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Once your prescription is approved, medications are typically delivered within 24-48 hours. We offer free express shipping with discreet packaging.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Is my information secure?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Absolutely. We use bank-level encryption and are fully GDPR-compliant. Your medical information is confidential and never shared with third parties.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                What if the treatment is not suitable for me?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Our doctors carefully review each case. If a treatment is not medically appropriate, they will not issue a prescription and will recommend alternative options or suggest consulting your GP.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                Can I get a refund?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Due to the nature of prescription medications, we cannot accept returns. However, if there is an issue with your order, please contact our customer service team who will be happy to help.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <h5 class="fw-semibold mb-2">Still have questions?</h5>
                        <p class="text-muted mb-3">Our customer support team is here to help you</p>
                        <a href="{{ url('/contact') }}" class="btn btn-primary">Contact support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer-dark text-light py-5">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Company Info -->
            <div class="col-md-6 col-lg-3">
                @if($setting->company_logo && file_exists(public_path('images/upload/'.$setting->company_logo)))
                    <img src="{{ $setting->logo }}" alt="{{ $setting->business_name }}" height="40" class="mb-3">
                @else
                    <img src="{{ url('/images/upload_empty/lojf.png') }}" alt="{{ $setting->business_name }}" height="40" class="mb-3" style="filter: brightness(0) invert(1);">
                @endif
                <p class="small mb-3">Your trusted online medical practice for discreet and secure medical consultation.</p>
                <div class="d-flex gap-3">
                    @if($setting->facebook_link)
                        <a href="{{ $setting->facebook_link }}" class="text-light" target="_blank"><i class="bi bi-facebook"></i></a>
                    @endif
                    @if($setting->twitter_link)
                        <a href="{{ $setting->twitter_link }}" class="text-light" target="_blank"><i class="bi bi-twitter"></i></a>
                    @endif
                    @if($setting->instagram_link)
                        <a href="{{ $setting->instagram_link }}" class="text-light" target="_blank"><i class="bi bi-instagram"></i></a>
                    @endif
                    @if($setting->linkedin_link)
                        <a href="{{ $setting->linkedin_link }}" class="text-light" target="_blank"><i class="bi bi-linkedin"></i></a>
                    @endif
                </div>
            </div>

            <!-- Treatments -->
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-3">Treatments</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('categories') }}" class="text-light text-decoration-none">All treatments</a></li>
                    @foreach($categories->take(5) as $category)
                        <li class="mb-2"><a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-light text-decoration-none">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Company -->
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-3">Company</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#about" class="text-light text-decoration-none">About us</a></li>
                    <li class="mb-2"><a href="#how-it-works" class="text-light text-decoration-none">How it works</a></li>
                    <li class="mb-2"><a href="{{ url('show-doctors') }}" class="text-light text-decoration-none">Our doctors</a></li>
                    <li class="mb-2"><a href="{{ url('our_blogs') }}" class="text-light text-decoration-none">Blog</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-3">Support</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Help center</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Contact</a></li>
                    <li class="mb-2"><a href="#faq" class="text-light text-decoration-none">FAQ</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Shipping & delivery</a></li>
                </ul>
            </div>
        </div>

        <div class="border-top border-secondary pt-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="small mb-0">© {{ date('Y') }} {{ $setting->business_name }} All rights reserved.</p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-3 small">
                        <a href="{{ url('/privacy-policy') }}" class="text-light text-decoration-none">Privacy</a>
                        <a href="#" class="text-light text-decoration-none">Terms</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
