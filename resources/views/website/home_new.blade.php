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
    <link href="{{asset('css/new-design.css')}}?v={{ time() }}" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bold Differentiation typography -->
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6, .display-4, .display-5 { font-family: 'Clash Display', sans-serif; }
    </style>
</head>
<body>
    @include('layout.partials.skeleton_loader')
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top border-bottom" style="background-color: #f2efea !important;">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            @if($setting->company_logo && file_exists(public_path('images/upload/'.$setting->company_logo)))
                <img src="{{ $setting->logo }}" alt="{{ $setting->business_name }}">
            @else
                <img src="{{ url('/images/upload_empty/fuxxlogo.png') }}" alt="{{ $setting->business_name }}">
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
                    <a class="nav-link" href="{{ url('/about-us') }}">About us</a>
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
<section class="hero-bloomwell text-start text-white">
    <div class="container position-relative z-index-2 w-100 h-100 d-flex align-items-center">
        <div class="row align-items-center justify-content-between w-100">
            
            <!-- Left Column: Text & Search -->
            <div class="col-lg-6 col-xl-5 mb-5 mb-lg-0">
                <!-- Trustpilot / Rating Badge -->
                <div class="mb-4 d-inline-flex align-items-center gap-2 px-3 py-2 bg-dark rounded-pill" style="border: 1px solid rgba(255,255,255,0.1);">
                    <div class="d-flex text-success">
                        <i class="bi bi-star-fill mx-1"></i>
                        <i class="bi bi-star-fill mx-1"></i>
                        <i class="bi bi-star-fill mx-1"></i>
                        <i class="bi bi-star-fill mx-1"></i>
                        <i class="bi bi-star-fill mx-1"></i>
                    </div>
                    <span class="fw-bold small ms-1">Hervorragend {{ number_format($reviews->count() * 12500) }}+</span>
                </div>

                <!-- Main Heading -->
                <h1 class="display-5 fw-bold mb-4" style="line-height: 1.25;">
                    Behandlung & Rezepte <span class="text-primary text-nowrap">bequem online</span> erhalten
                </h1>
                
                <p class="lead mb-4" style="color: rgba(255,255,255,0.8);">
                    Ihr legaler & sicherer Weg zur telemedizinischen Behandlung von zu Hause.
                </p>

                <!-- Search Bar -->
                <form action="{{ route('categories') }}" method="GET" class="mb-5 bloomwell-search w-100">
                    <div class="input-group input-group-lg shadow-lg rounded-3 overflow-hidden">
                        <span class="input-group-text border-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0" placeholder="Was suchen Sie? (z.B. Haarausfall)" value="{{ request('search') }}">
                        <button type="submit" class="bloomwell-btn ms-0 rounded-0 rounded-end">Suchen</button>
                    </div>
                </form>
                
                <!-- Trust Indicators -->
                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center text-white" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <span class="small fw-medium">EU-registered doctors</span>
                    </div>
                    <div class="d-flex align-items-center text-white" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <span class="small fw-medium">Free shipping</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Image & Floating Cards -->
            <div class="col-lg-6 col-xl-6 position-relative d-flex justify-content-center justify-content-lg-end align-items-center" style="z-index: 2;">
                <div class="hero-image-container ms-auto me-auto me-lg-0 mt-4 mt-lg-0 w-100">
                    <!-- Central Subject (Using a transparent cutout style image) -->
                    <img src="https://images.unsplash.com/photo-1638202993928-7267aad84c31?q=80&w=600&auto=format&fit=crop&bg=transparent" alt="Telemedicine Patient" class="img-fluid" style="mask-image: linear-gradient(to top, transparent 0%, black 20%); -webkit-mask-image: linear-gradient(to top, transparent 0%, black 20%);">
                    
                    <!-- Floating Feature Cards -->
                    <div class="bloomwell-floating-card card-1 d-none d-md-flex text-dark">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-6">Bestellung online</div>
                            <div class="small text-muted">in weniger als 5 Min*</div>
                        </div>
                    </div>

                    <div class="bloomwell-floating-card card-2 d-none d-md-flex text-dark">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-6">Kostenloser Versand</div>
                            <div class="small text-muted">Diskret verpackt</div>
                        </div>
                    </div>

                    <div class="bloomwell-floating-card card-3 d-none d-md-flex text-dark">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-6">EU zugelassen</div>
                            <div class="small text-muted">Zertifizierte Ärzte</div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="treatment-areas-section py-5" style="background-color: #f2efea !important;" id="services">
<!-- Our Treatment Areas – Carousel Section -->
    <div class="container py-4">
        <div class="treatment-areas-header mb-4">
            <span class="treatment-areas-label">Treatment Areas</span>
            <h2 class="display-5 fw-bold mb-2">Our Treatment Areas</h2>
            <p class="lead text-muted mb-0">Advanced medical treatments tailored for you – all supervised by certified doctors</p>
        </div>

        <div class="treatment-areas-viewport" id="treatment-viewport">
            <div class="treatment-areas-track" id="treatment-track">
                @php
                    $iconMap = [
                        "Men's Health" => ['icon' => 'bi-heart-pulse', 'badge' => 'Popular'],
                        "Women's Health" => ['icon' => 'bi-person', 'badge' => null],
                        "General Medicine" => ['icon' => 'bi-capsule', 'badge' => null],
                        "Weight Management" => ['icon' => 'bi-activity', 'badge' => 'New'],
                        "Travel Medicine" => ['icon' => 'bi-shield-check', 'badge' => null],
                        "Skin Health" => ['icon' => 'bi-stars', 'badge' => null],
                    ];
                    $treatmentCards = $categories->take(12);
                @endphp
                @forelse($treatmentCards as $index => $category)
                    @php
                        $treatmentName = $category->treatment ? $category->treatment->name : 'General Medicine';
                        $iconData = $iconMap[$treatmentName] ?? ['icon' => 'bi-capsule', 'badge' => null];
                    @endphp
                    <div class="treatment-area-card">
                        <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-decoration-none text-dark">
                            <div class="treatment-card-body">
                                <h3 class="treatment-card-title">{{ $category->name }}</h3>
                                <div class="treatment-card-tags">
                                    <span class="treatment-tag treatment-tag-type">{{ $treatmentName }}</span>
                                    @if($category->price && $category->price > 0)
                                        <span class="treatment-tag treatment-tag-info">from {{ number_format($category->price, 0) }} €</span>
                                    @endif
                                </div>
                                <p class="treatment-card-sub">{{ $category->description ? Str::limit($category->description, 60) : 'Professional medical consultation and treatment' }}</p>
                                <span class="treatment-card-cta">Learn more <i class="bi bi-arrow-right"></i></span>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="treatment-area-card">
                        <a href="{{ route('categories') }}" class="text-decoration-none text-dark">
                            <div class="treatment-card-body">
                                <h3 class="treatment-card-title">Browse treatments</h3>
                                <div class="treatment-card-tags">
                                    <span class="treatment-tag treatment-tag-type">General Medicine</span>
                                </div>
                                <p class="treatment-card-sub">Professional medical consultation and treatment</p>
                                <span class="treatment-card-cta">Learn more <i class="bi bi-arrow-right"></i></span>
                            </div>
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="treatment-areas-controls">
            <a href="{{ route('categories') }}" class="btn btn-primary treatment-btn-discover" id="treatment-btn-discover">View all treatments</a>
            <div class="treatment-controls-right">
                <div class="treatment-dots" id="treatment-dots"></div>
                <div class="treatment-arrow-group">
                    <button type="button" class="treatment-arrow-btn" id="treatment-prev-btn" aria-label="Previous">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="treatment-arrow-btn" id="treatment-next-btn" aria-label="Next">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-bloomwell-dark text-white" id="how-it-works">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">How It Works</h2>
            <p class="lead" style="color: rgba(255,255,255,0.8);">Your treatment in just 4 simple steps</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 position-relative shadow-bloomwell">
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
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 position-relative shadow-bloomwell">
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
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 position-relative shadow-bloomwell">
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
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
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
            <a href="{{ route('categories') }}" class="btn btn-primary btn-lg btn-start-treatment">Start treatment now</a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5 position-relative overflow-hidden" style="background-color: #f2efea !important;" id="about">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 order-2 order-lg-1 position-relative">
                <div class="position-relative z-index-1">
                    <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&h=600&fit=crop" 
                         alt="Medical team" 
                         class="img-fluid rounded-4 shadow-lg w-100" style="object-fit: cover; border: 8px solid rgba(255,255,255,0.5);">
                    
                    <!-- Floating Stat Card 1 -->
                    <div class="position-absolute bg-white rounded-4 p-3 shadow-bloomwell d-flex align-items-center gap-3" style="bottom: -20px; left: -20px; z-index: 2; animation: float 5s ease-in-out infinite;">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-5 text-dark">Certified</div>
                            <div class="small text-muted mb-0">EU Doctors</div>
                        </div>
                    </div>
                </div>
                <!-- Decorative background blur shape -->
                <div class="position-absolute top-50 start-50 translate-middle rounded-circle bg-primary opacity-25"></div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 ps-lg-5">
                <div class="mb-4">
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 border shadow-sm fw-semibold mb-3">
                        <i class="bi bi-stars me-1 text-warning"></i> About Us
                    </span>
                    <h2 class="display-4 fw-bold mb-3 text-dark" style="line-height: 1.1;">Welcome to <span class="text-primary">{{ $setting->business_name }}</span></h2>
                    <p class="lead text-muted mb-4" style="line-height: 1.8;">
                        Your trusted, discreet, and reliable partner for telemedical health queries. We blend technology with professional medical care to provide accessible treatments.
                    </p>
                </div>
                
                <div class="d-flex flex-column gap-3 mb-5">
                    <div class="d-flex align-items-start gap-3 bg-white p-3 rounded-4 shadow-sm border border-light hover-lift flex-column flex-sm-row">
                        <div class="bg-purple-light text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                            <i class="bi bi-laptop fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-1 text-dark">Comfortable & Private</h5>
                            <p class="text-muted small mb-0">Consultations matching your schedule from the comfort and privacy of your home.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start gap-3 bg-white p-3 rounded-4 shadow-sm border border-light hover-lift flex-column flex-sm-row">
                        <div class="bg-teal-light text-accent rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                            <i class="bi bi-file-earmark-medical fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-1 text-dark">Compliant & Secure</h5>
                            <p class="text-muted small mb-0">Strict adherence to medical regulations and total GDPR data protection.</p>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <a href="{{ url('/about-us') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm align-items-center d-inline-flex gap-2">
                        Discover Our Story
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    
                    <div class="d-flex align-items-center gap-2 bg-white rounded-pill px-3 py-2 shadow-sm border">
                        <div class="d-flex" style="font-size: 0.9rem;">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <span class="fw-bold small text-dark">{{ number_format($reviews->count() * 12500) }}+ Patients</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5 bg-bloomwell-dark text-white" id="why-choose-us">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Why Choose Us</h2>
            <p class="lead" style="color: rgba(255,255,255,0.8);">The advantages of our online medical practice</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-shield-check text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">100% Discreet</h5>
                    <p class="text-muted small mb-0">Plain packaging and confidential service</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-clock text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">24-48h Delivery</h5>
                    <p class="text-muted small mb-0">Fast and free shipping to your door</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-award text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Licensed Doctors</h5>
                    <p class="text-muted small mb-0">Certified medical professionals</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-white text-dark border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-lock text-primary"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Secure & Private</h5>
                    <p class="text-muted small mb-0">GDPR compliant data protection</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Safety & Quality Section -->
<section class="py-5" style="background-color: #f2efea !important;" id="safety">
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
<section class="py-5" style="background-color: #f2efea !important;" id="trust">
    <div class="container py-4">
        <!-- Trust Features -->
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="card bg-bloomwell-dark text-white border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-award text-primary fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Certified doctors</h5>
                    <p class="small mb-0" style="color: rgba(255,255,255,0.8);">All our doctors are registered and licensed</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-bloomwell-dark text-white border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-lock text-primary fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Privacy guaranteed</h5>
                    <p class="small mb-0" style="color: rgba(255,255,255,0.8);">Your data is encrypted and protected according to GDPR</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-bloomwell-dark text-white border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-clock text-primary fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Fast delivery</h5>
                    <p class="small mb-0" style="color: rgba(255,255,255,0.8);">Shipping within 24-48 hours after medical approval</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card bg-bloomwell-dark text-white border-0 rounded-3 p-4 text-center h-100 shadow-bloomwell">
                    <div class="feature-icon bg-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="bi bi-star-fill text-primary fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">
                        @if($reviews->count() > 0)
                            {{ number_format($reviews->avg('rate'), 1) }}/5 rating
                        @else
                        4.8/5 rating
                        @endif
                    </h5>
                    <p class="small mb-0" style="color: rgba(255,255,255,0.8);">Over {{ number_format($reviews->count() * 12500) }} satisfied patients trust us</p>
                </div>
            </div>
        </div>

        <!-- Testimonials -->
        <div class="testimonials-section bg-bloomwell-dark text-white rounded-3 p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h2 class="display-6 fw-bold mb-4">What our patients say</h2>
                    
                    @forelse($reviews->take(2) as $review)
                        <div class="card mb-3 bg-white text-dark border-0 shadow-bloomwell">
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
                        <div class="card mb-3 bg-white text-dark border-0 shadow-bloomwell">
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
<section class="py-5" style="background-color: #f2efea !important;" id="faq">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3 text-dark">Frequently Asked Questions</h2>
            <p class="lead text-muted">Everything you need to know about our service</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion rounded-3 overflow-hidden" id="faqAccordion">
                    <div class="accordion-item border-0 border-bottom">
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

                    <div class="accordion-item border-0 border-bottom">
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

                    <div class="accordion-item border-0 border-bottom">
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

                    <div class="accordion-item border-0 border-bottom">
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

                    <div class="accordion-item border-0 border-bottom">
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

                    <div class="accordion-item border-0">
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

                <div class="card mt-4 bg-white text-dark border-0 shadow-bloomwell">
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
                    <img src="{{ $setting->logo }}" alt="{{ $setting->business_name }}" class="footer-logo mb-3">
                @else
                    <img src="{{ url('/images/upload_empty/fuxxlogo.png') }}" alt="{{ $setting->business_name }}" class="footer-logo mb-3">
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
                    <li class="mb-2"><a href="{{ url('/about-us') }}" class="text-light text-decoration-none">About us</a></li>
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
<!-- Treatment Areas Carousel -->
<script>
(function() {
    var viewport = document.getElementById('treatment-viewport');
    var track = document.getElementById('treatment-track');
    if (!viewport || !track) return;
    var cards = track.querySelectorAll('.treatment-area-card');
    if (cards.length === 0) return;

    var prevBtn = document.getElementById('treatment-prev-btn');
    var nextBtn = document.getElementById('treatment-next-btn');
    var dotsWrap = document.getElementById('treatment-dots');
    var index = 0;
    var dragging = false;
    var dragStartX = 0;
    var dragOffset = 0;
    var touchStartX = 0;
    var resizeTimer;

    function cardStep() {
        var gap = 20;
        var style = window.getComputedStyle(track);
        if (style.gap) gap = parseFloat(style.gap) || 20;
        return (cards[0].offsetWidth || 280) + gap;
    }

    function visibleCount() {
        return Math.max(1, Math.floor(viewport.offsetWidth / cardStep()));
    }

    function maxIdx() {
        return Math.max(0, cards.length - visibleCount());
    }

    function render(animated) {
        if (animated === false) track.style.transition = 'none';
        track.style.transform = 'translateX(-' + (index * cardStep()) + 'px)';
        if (animated === false) { void track.offsetWidth; track.style.transition = ''; }
        if (prevBtn) prevBtn.disabled = index <= 0;
        if (nextBtn) nextBtn.disabled = index >= maxIdx();
        var dots = dotsWrap.querySelectorAll('.treatment-dot');
        for (var i = 0; i < dots.length; i++) dots[i].classList.toggle('active', i === index);
    }

    function goTo(n) {
        index = Math.min(Math.max(n, 0), maxIdx());
        render(true);
    }

    function buildDots() {
        dotsWrap.innerHTML = '';
        var max = maxIdx();
        for (var i = 0; i <= max; i++) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'treatment-dot' + (i === 0 ? ' active' : '');
            btn.setAttribute('aria-label', 'Go to slide ' + (i + 1));
            (function(idx) { btn.addEventListener('click', function() { goTo(idx); }); })(i);
            dotsWrap.appendChild(btn);
        }
    }

    if (prevBtn) prevBtn.addEventListener('click', function() { goTo(index - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function() { goTo(index + 1); });

    viewport.addEventListener('mousedown', function(e) {
        dragging = true;
        dragStartX = e.clientX;
        dragOffset = index * cardStep();
        track.style.transition = 'none';
    });
    window.addEventListener('mousemove', function(e) {
        if (!dragging) return;
        var delta = dragStartX - e.clientX;
        var raw = Math.min(Math.max(dragOffset + delta, 0), maxIdx() * cardStep());
        track.style.transform = 'translateX(-' + raw + 'px)';
    });
    window.addEventListener('mouseup', function(e) {
        if (!dragging) return;
        dragging = false;
        track.style.transition = '';
        var delta = dragStartX - e.clientX;
        if (Math.abs(delta) > 60) goTo(delta > 0 ? index + 1 : index - 1);
        else render(true);
    });

    viewport.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        track.style.transition = 'none';
    }, { passive: true });
    viewport.addEventListener('touchend', function(e) {
        track.style.transition = '';
        var delta = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(delta) > 50) goTo(delta > 0 ? index + 1 : index - 1);
        else render(true);
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') goTo(index - 1);
        if (e.key === 'ArrowRight') goTo(index + 1);
    });

    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            index = Math.min(index, maxIdx());
            buildDots();
            render(false);
        }, 150);
    });

    buildDots();
    render(false);
})();
</script>
</body>
</html>
