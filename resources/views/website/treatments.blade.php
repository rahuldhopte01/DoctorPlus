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
    
    <!-- Global typography: Inter (body) + Clash Display (headings) -->
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
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
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
                    <a class="nav-link active" href="{{ route('categories') }}">Treatments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}#how-it-works">How it works</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/about-us') }}">About us</a>
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
            // Icon and color mapping (same as landing page for card consistency)
            $iconMap = [
                "Men's Health" => ['icon' => 'bi-heart-pulse', 'color' => 'blue', 'badge' => 'Popular'],
                "Women's Health" => ['icon' => 'bi-person', 'color' => 'pink', 'badge' => null],
                "General Medicine" => ['icon' => 'bi-capsule', 'color' => 'teal', 'badge' => null],
                "Weight Management" => ['icon' => 'bi-activity', 'color' => 'green', 'badge' => 'New'],
                "Travel Medicine" => ['icon' => 'bi-shield-check', 'color' => 'purple', 'badge' => null],
                "Skin Health" => ['icon' => 'bi-stars', 'color' => 'orange', 'badge' => null],
            ];
            
            // Group categories by treatment
            $groupedCategories = $categories->groupBy('treatment_id');
            $carouselThreshold = 3; // Show carousel only when more than 3 categories in group
        @endphp
        
        @forelse($groupedCategories as $treatmentId => $categoryGroup)
            @php
                $firstCategory = $categoryGroup->first();
                $treatment = $firstCategory->treatment;
                $treatmentName = $treatment ? $treatment->name : 'General Medicine';
                $iconData = $iconMap[$treatmentName] ?? ['icon' => 'bi-capsule', 'color' => 'teal', 'badge' => null];
                $useCarousel = $categoryGroup->count() > $carouselThreshold;
                $carouselId = 't' . (int) $treatmentId;
            @endphp
            
            <div class="mb-5 treatment-group" data-treatment-id="{{ $treatmentId ?? 'none' }}">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="category-icon bg-{{ $iconData['color'] }}-light text-{{ $iconData['color'] }} rounded-3 d-flex align-items-center justify-content-center">
                        <i class="bi {{ $iconData['icon'] }}"></i>
                    </div>
                    <div>
                        <h2 class="display-6 fw-bold mb-1">{{ $treatmentName }}</h2>
                        <p class="text-muted mb-0">{{ $categoryGroup->count() }} treatment{{ $categoryGroup->count() !== 1 ? 's' : '' }} available</p>
                    </div>
                </div>
                
                @if($useCarousel)
                    {{-- Carousel: same structure as landing page treatment areas --}}
                    <div class="treatment-areas-viewport" id="treatment-viewport-{{ $carouselId }}" data-carousel-id="{{ $carouselId }}">
                        <div class="treatment-areas-track" id="treatment-track-{{ $carouselId }}">
                            @foreach($categoryGroup as $category)
                                @php
                                    $cardImage = ($category->image && file_exists(public_path('images/upload/'.$category->image)))
                                        ? $category->fullImage
                                        : 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=500&h=400&fit=crop';
                                @endphp
                                <div class="treatment-area-card">
                                    <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-decoration-none text-dark">
                                        <div class="treatment-card-image">
                                            <img src="{{ $cardImage }}" alt="{{ $category->name }}">
                                            @if($iconData['badge'] ?? null)
                                                <span class="treatment-card-badge treatment-badge-primary">{{ $iconData['badge'] }}</span>
                                            @endif
                                        </div>
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
                            @endforeach
                        </div>
                    </div>
                    <div class="treatment-areas-controls">
                        <div class="treatment-controls-right ms-auto">
                            <div class="treatment-dots" id="treatment-dots-{{ $carouselId }}"></div>
                            <div class="treatment-arrow-group">
                                <button type="button" class="treatment-arrow-btn" id="treatment-prev-btn-{{ $carouselId }}" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button type="button" class="treatment-arrow-btn" id="treatment-next-btn-{{ $carouselId }}" aria-label="Next">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Grid: ≤3 items, same card style as landing for consistency --}}
                    <div class="row g-4">
                        @foreach($categoryGroup as $category)
                            @php
                                $cardImage = ($category->image && file_exists(public_path('images/upload/'.$category->image)))
                                    ? $category->fullImage
                                    : 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=500&h=400&fit=crop';
                            @endphp
                            <div class="col-md-6 col-lg-4">
                                <div class="treatment-area-card" style="min-width: auto; max-width: none; width: 100%;" onclick="window.location.href='{{ route('category.detail', ['id' => $category->id]) }}'">
                                    <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-decoration-none text-dark">
                                        <div class="treatment-card-image">
                                            <img src="{{ $cardImage }}" alt="{{ $category->name }}">
                                            @if($iconData['badge'] ?? null)
                                                <span class="treatment-card-badge treatment-badge-primary">{{ $iconData['badge'] }}</span>
                                            @endif
                                        </div>
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
                            </div>
                        @endforeach
                    </div>
                @endif
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
                    <li class="mb-2"><a href="{{ url('/about-us') }}" class="text-light text-decoration-none">About us</a></li>
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

<!-- Treatment carousels: one per group when group has >3 items (same logic as landing) -->
<script>
(function() {
    var viewports = document.querySelectorAll('.treatment-areas-viewport[data-carousel-id]');
    viewports.forEach(function(viewport) {
        var carouselId = viewport.getAttribute('data-carousel-id');
        if (!carouselId) return;
        var track = document.getElementById('treatment-track-' + carouselId);
        var dotsWrap = document.getElementById('treatment-dots-' + carouselId);
        var prevBtn = document.getElementById('treatment-prev-btn-' + carouselId);
        var nextBtn = document.getElementById('treatment-next-btn-' + carouselId);
        if (!track || !viewport) return;
        var cards = track.querySelectorAll('.treatment-area-card');
        if (cards.length === 0) return;

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
            if (dotsWrap) {
                var dots = dotsWrap.querySelectorAll('.treatment-dot');
                for (var i = 0; i < dots.length; i++) dots[i].classList.toggle('active', i === index);
            }
        }

        function goTo(n) {
            index = Math.min(Math.max(n, 0), maxIdx());
            render(true);
        }

        function buildDots() {
            if (!dotsWrap) return;
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
    });
})();
</script>

<!-- Page Script -->
<script>
    // Filter by treatment (reloads page with ?treatment=id; backend returns only that treatment's categories)
    function filterByTreatment(treatmentId) {
        var url = new URL(window.location.href);
        if (treatmentId) {
            url.searchParams.set('treatment', treatmentId);
        } else {
            url.searchParams.delete('treatment');
        }
        var searchEl = document.getElementById('searchInput');
        if (searchEl && searchEl.value) {
            url.searchParams.set('search', searchEl.value);
        } else {
            url.searchParams.delete('search');
        }
        window.location.href = url.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        var searchQuery = '{{ request('search') }}';
        if (searchQuery) {
            var treatmentCards = document.querySelectorAll('.treatment-area-card');
            treatmentCards.forEach(function(card) {
                var text = card.textContent.toLowerCase();
                if (text.indexOf(searchQuery.toLowerCase()) !== -1) {
                    card.style.boxShadow = '0 0 0 2px var(--primary-color)';
                }
            });
        }
        // When filtered via URL, only one treatment group is returned by backend; carousel logic still applies per group
        var treatmentFilter = '{{ request('treatment') }}';
        if (treatmentFilter) {
            var allGroups = document.querySelectorAll('.treatment-group[data-treatment-id]');
            allGroups.forEach(function(group) {
                if (group.getAttribute('data-treatment-id') !== treatmentFilter) {
                    group.style.display = 'none';
                }
            });
        }
    });
</script>
</body>
</html>
