<!DOCTYPE html>
<html lang="en">
<head>
    @php
    $setting = App\Models\Setting::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Treatments - {{ $setting->business_name }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link href="{{asset('css/new-design.css')}}" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            @if($setting->company_logo && file_exists(public_path('images/upload/'.$setting->company_logo)))
                <img src="{{ $setting->logo }}" alt="{{ $setting->business_name }}" height="40">
            @else
                <img src="{{ url('/images/upload_empty/fuxxlogo.png') }}" style="width:200px" alt="{{ $setting->business_name }}" height="40">
            @endif
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('categories') }}">Treatments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}#how-it-works">How it works</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}#about">About us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}#faq">Help</a>
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
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">All Treatments</h1>
                <p class="lead text-muted mb-4">
                    Browse our comprehensive range of medical treatments. All prescriptions are issued by certified doctors 
                    and delivered discreetly to your door.
                </p>
                
                <!-- Search -->
                <form method="GET" action="{{ route('categories') }}" class="mb-4">
                    <div class="input-group input-group-lg">
                        <input type="text" id="searchInput" name="search" class="form-control" 
                               placeholder="Search for a treatment or condition..." 
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary px-4">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Filter Categories -->
<div class="bg-white border-bottom sticky-top" style="top: 56px; z-index: 1020;">
    <div class="container py-3">
        <div class="category-filter d-flex gap-2">
            <button class="filter-btn {{ !request('treatment') ? 'active' : '' }}" 
                    onclick="filterByTreatment('')">
                All Treatments
            </button>
            @foreach($treatments as $treatment)
                <button class="filter-btn {{ request('treatment') == $treatment->id ? 'active' : '' }}" 
                        onclick="filterByTreatment({{ $treatment->id }})">
                    {{ $treatment->name }}
                </button>
            @endforeach
        </div>
    </div>
</div>

<!-- Treatments List -->
<section class="py-5 bg-light">
    <div class="container" id="treatmentsContainer">
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
            
            // Group categories by treatment
            $groupedCategories = $categories->groupBy('treatment_id');
        @endphp
        
        @forelse($groupedCategories as $treatmentId => $categoryGroup)
            @php
                $firstCategory = $categoryGroup->first();
                $treatment = $firstCategory->treatment;
                $treatmentName = $treatment ? $treatment->name : 'General Medicine';
                $iconData = $iconMap[$treatmentName] ?? ['icon' => 'bi-capsule', 'color' => 'teal'];
            @endphp
            
            <div class="mb-5" data-treatment-id="{{ $treatmentId ?? 'none' }}">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="category-icon bg-{{ $iconData['color'] }}-light text-{{ $iconData['color'] }} rounded-3 d-flex align-items-center justify-content-center">
                        <i class="bi {{ $iconData['icon'] }}"></i>
                    </div>
                    <div>
                        <h2 class="display-6 fw-bold mb-1">{{ $treatmentName }}</h2>
                        <p class="text-muted mb-0">{{ $categoryGroup->count() }} treatment{{ $categoryGroup->count() !== 1 ? 's' : '' }} available</p>
                    </div>
                </div>
                
                <div class="row g-4">
                    @foreach($categoryGroup as $category)
                        <div class="col-md-6 col-lg-4">
                            <div class="treatment-card card h-100 border-2 border-{{ $iconData['color'] }} rounded-3 p-4" 
                                 onclick="window.location.href='{{ route('category.detail', ['id' => $category->id]) }}'">
                                <div class="mb-3">
                                    <h5 class="fw-semibold mb-2">{{ $category->name }}</h5>
                                    <p class="text-muted small mb-3">
                                        {{ $category->description ? Str::limit($category->description, 100) : 'Professional medical consultation and treatment' }}
                                    </p>
                                    <p class="text-primary fw-bold fs-5 mb-0">
                                        @if(isset($category->price) && $category->price)
                                            {{ $category->price }}
                                        @else
                                            Starting from €29.99
                                        @endif
                                    </p>
                                </div>
                                
                                <button class="btn btn-primary w-100 mb-3" onclick="event.stopPropagation(); window.location.href='{{ route('category.detail', ['id' => $category->id]) }}'">
                                    View details
                                </button>
                                
                                <div class="d-flex align-items-center justify-content-center gap-2 small text-muted">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Free shipping</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h3 class="mt-3">No treatments found</h3>
                <p class="text-muted">Try adjusting your search or filter criteria</p>
            </div>
        @endforelse
    </div>
</section>

<!-- CTA Section -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold mb-3">Can't find what you're looking for?</h2>
                <p class="lead text-muted mb-4">
                    Contact our medical team for advice on other treatments and conditions
                </p>
                <a href="#" class="btn btn-primary btn-lg">Contact us</a>
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
                    <img src="{{ $setting->logo }}" alt="{{ $setting->business_name }}" height="40" class="mb-3" style="filter: brightness(0) invert(1);">
                @else
                    <img src="{{ url('/images/upload_empty/fuxxlogo.png') }}" style="width:200px" alt="{{ $setting->business_name }}" height="40" class="mb-3" style="filter: brightness(0) invert(1);">
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
                    @foreach($treatments->take(5) as $treatment)
                        <li class="mb-2">
                            <a href="{{ route('categories', ['treatment' => $treatment->id]) }}" class="text-light text-decoration-none">
                                {{ $treatment->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Company -->
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-3">Company</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ url('/') }}#about" class="text-light text-decoration-none">About us</a></li>
                    <li class="mb-2"><a href="{{ url('/') }}#how-it-works" class="text-light text-decoration-none">How it works</a></li>
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
                    <li class="mb-2"><a href="{{ url('/') }}#faq" class="text-light text-decoration-none">FAQ</a></li>
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

<!-- Page Script -->
<script>
    // Filter by treatment
    function filterByTreatment(treatmentId) {
        const url = new URL(window.location.href);
        if (treatmentId) {
            url.searchParams.set('treatment', treatmentId);
        } else {
            url.searchParams.delete('treatment');
        }
        // Keep search parameter if exists
        const search = document.getElementById('searchInput').value;
        if (search) {
            url.searchParams.set('search', search);
        } else {
            url.searchParams.delete('search');
        }
        window.location.href = url.toString();
    }

    // Client-side filtering for better UX (optional enhancement)
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchQuery = '{{ request('search') }}';
        
        if (searchQuery) {
            // Highlight search results
            const treatmentCards = document.querySelectorAll('.treatment-card');
            treatmentCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(searchQuery.toLowerCase())) {
                    card.style.border = '2px solid var(--primary-color)';
                }
            });
        }
        
        // Filter by treatment on page load
        const treatmentFilter = '{{ request('treatment') }}';
        if (treatmentFilter) {
            const allGroups = document.querySelectorAll('[data-treatment-id]');
            allGroups.forEach(group => {
                if (group.getAttribute('data-treatment-id') !== treatmentFilter) {
                    group.style.display = 'none';
                }
            });
        }
    });
</script>
</body>
</html>
