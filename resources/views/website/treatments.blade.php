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
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="{{asset('css/new-design.css')}}?v={{ time() }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/website_header.css') }}">
    <link href="{{asset('css/landing_styles.css')}}?v={{ time() }}" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Global typography: Inter (body) + Clash Display (headings) -->
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fafafe; }
        h1, h2, h3, h4, h5, h6, .display-4, .display-5 { font-family: 'Clash Display', sans-serif; }
        h1 span, h2 span, h3 span, h4 span, h5 span, h6 span, .display-4 span, .display-5 span { font-family: inherit; }
        
        .hero-fuxx {
            background: linear-gradient(135deg, #f3ecff 0%, #ffffff 100%) !important;
        }
        .btn-hero-premium {
            background-color: #8a48ff !important;
            border-color: #8a48ff !important;
            color: #ffffff !important;
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
            position: relative;
            overflow: hidden;
        }
        .btn-hero-premium:hover {
            transform: translateY(-4px) !important;
            background-color: #7a35fa !important;
            border-color: #7a35fa !important;
            color: #ffffff !important;
        }
        .search-input-premium {
            border-radius: 50px 0 0 50px !important;
            padding-left: 30px;
            border: 1px solid #e0e0e0;
            border-right: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            font-size: 1.1rem;
        }
        .search-input-premium:focus {
            border-color: #8a48ff;
            box-shadow: 0 10px 30px rgba(138, 72, 255, 0.1);
            outline: none;
        }
        .search-btn-premium {
            border-radius: 0 50px 50px 0 !important;
            padding: 0 40px;
        }
        
        .filter-btn {
            border-radius: 50px !important;
            padding: 10px 28px;
            font-weight: 600;
            border: 1px solid #eaeaea !important;
            background: #fff !important;
            color: #555 !important;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 2px 10px rgba(0,0,0,0.02) !important;
            white-space: nowrap;
        }
        .filter-btn:hover {
            border-color: #8a48ff !important;
            background-color: #8a48ff !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(138, 72, 255, 0.1);
        }
        .filter-btn.active {
            background: #8a48ff !important;
            border-color: #8a48ff !important;
            color: #fff !important;
            box-shadow: 0 6px 15px rgba(138, 72, 255, 0.25);
        }

        .treatment-group .category-icon {
            width: 55px;
            height: 55px;
            font-size: 1.6rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.04);
            background-color: #f4effe !important;
            color: #8a48ff !important;
        }
        
        .cta-gradient {
            background: linear-gradient(175deg, #f3ecff 0%, #e9e4ff 40%, #ffffff 100%);
        }
        
        /* Ensure hide scrollbar on filter categories wrapper so it looks neat */
        .category-filter-wrapper::-webkit-scrollbar {
            display: none;
        }
        /* Box / Card Enhancements - Brand New Premium Look */
        .premium-box-card {
            background-color: #ffffff !important;
            border-radius: 20px;
            padding: 24px 24px 20px;
            border: 1px solid rgba(138, 72, 255, 0.04); /* Subtle purple tint border */
            box-shadow: 0 4px 24px rgba(0,0,0,0.06), 0 1px 4px rgba(0,0,0,0.02);
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
            cursor: pointer;
            text-align: left;
            overflow: hidden;
            width: 320px; /* For carousel items */
            flex-shrink: 0;
            margin-bottom: 5px; /* For hover lift space */
        }
        .premium-box-card a {
            display: flex;
            flex-direction: column;
            height: 100%;
            text-decoration: none;
        }
        .premium-box-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 45px rgba(138, 72, 255, 0.22);
            background-color: #ffffff;
        }
        .premium-box-card .treatment-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 12px;
            font-family: 'Clash Display', sans-serif;
            transition: color 0.3s ease;
            line-height: 1.35;
        }
        .premium-box-card:hover .treatment-card-title {
            color: #8a48ff;
        }
        .premium-box-card .treatment-card-sub {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }
        .premium-box-card .treatment-tag {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.72rem;
            font-weight: 700;
            margin-right: 6px;
            margin-bottom: 14px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .premium-box-card .treatment-tag-type {
            background: #f4effe;
            color: #8a48ff;
        }
        .premium-box-card .treatment-tag-info {
            background: #eefaed;
            color: #22c55e;
        }
        .premium-box-card .treatment-card-cta {
            font-weight: 700;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #8a48ff;
            transition: gap 0.3s ease;
            margin-top: auto;
        }
        .premium-box-card:hover .treatment-card-cta {
            gap: 12px;
        }
    </style>
</head>
<body>
    @include('layout.partials.skeleton_loader')
<!-- Navigation -->
@include('layout.partials.navbar_website')

<!-- Hero Section -->
<section class="hero-fuxx position-relative overflow-hidden" style="padding-top: 80px; padding-bottom: 80px;">
    <!-- Wavy background line SVG -->
    <svg class="position-absolute w-100" style="bottom: 0px; left: 0; opacity: 0.18; pointer-events:none;" viewBox="0 0 1440 120" preserveAspectRatio="none">
        <path d="M0,60 C180,20 360,100 540,60 C720,20 900,100 1080,60 C1260,20 1380,80 1440,60" stroke="#7b42f6" stroke-width="3" fill="none"/>
    </svg>

    <div class="container position-relative" style="z-index: 3;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="text-uppercase fw-bold mb-3 d-inline-block px-3 py-1" style="color: #8a48ff; background-color: #f4effe; border-radius: 20px; letter-spacing: 1.5px; font-size: 0.85rem;">
                    Alle Behandlungen
                </div>
                
                <h1 class="display-4 fw-bold mb-4" style="color: #1a1a1a; letter-spacing: -1px;">
                    Finden Sie Ihre passende <span style="color: #8a48ff;">Behandlung</span>
                </h1>
                
                <p class="lead mb-5 mx-auto" style="color: #4a4a4a; max-width: 650px; font-size: 1.15rem; line-height: 1.7;">
                    Durchsuchen Sie unser umfassendes Angebot an medizinischen Behandlungen. Alle Rezepte werden von zertifizierten Ärzten ausgestellt und diskret nach Hause geliefert.
                </p>
                
                <!-- Search -->
                <form method="GET" action="{{ route('categories') }}" class="mb-4 position-relative mx-auto" style="max-width: 650px;">
                    <div class="input-group input-group-lg bg-white" style="border-radius: 50px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                        <input type="text" id="searchInput" name="search" class="form-control search-input-premium" 
                               placeholder="Suchen Sie nach einer Behandlung oder Beschwerde..." 
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-hero-premium search-btn-premium fs-5">
                            <i class="bi bi-search me-2"></i> Suchen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Filter Categories -->
<div class="bg-white border-bottom sticky-top" style="top: 86px; z-index: 1020; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
    <div class="container py-3">
        <div class="category-filter-wrapper">
            <div class="category-filter d-flex gap-3 align-items-center pb-2">
                <button class="filter-btn {{ !request('treatment') ? 'active' : '' }}" 
                        onclick="filterByTreatment('')">
                    Alle Behandlungen
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
</div>

<!-- Treatments List -->
<section class="py-5" style="background-color: transparent;">
    <div class="container" id="treatmentsContainer">
        @php
            // Icon and color mapping (all purple-themed for brand consistency)
            $iconMap = [
                "Men's Health" => ['icon' => 'bi-heart-pulse', 'color' => 'purple', 'badge' => 'Popular'],
                "Women's Health" => ['icon' => 'bi-person', 'color' => 'purple', 'badge' => null],
                "General Medicine" => ['icon' => 'bi-capsule', 'color' => 'purple', 'badge' => null],
                "Weight Management" => ['icon' => 'bi-activity', 'color' => 'purple', 'badge' => 'New'],
                "Travel Medicine" => ['icon' => 'bi-shield-check', 'color' => 'purple', 'badge' => null],
                "Skin Health" => ['icon' => 'bi-stars', 'color' => 'purple', 'badge' => null],
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
                    <div class="category-icon rounded-3 d-flex align-items-center justify-content-center">
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
                                <div class="premium-box-card">
                                    <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-decoration-none text-dark">
                                        <div class="treatment-card-body" style="flex-grow: 1; display: flex; flex-direction: column;">
                                            <h3 class="treatment-card-title">{{ $category->name }}</h3>
                                            <div class="treatment-card-tags">
                                                <span class="treatment-tag treatment-tag-type">{{ $treatmentName }}</span>
                                                @if($category->price && $category->price > 0)
                                                    <span class="treatment-tag treatment-tag-info">from {{ number_format($category->price, 0) }} €</span>
                                                @endif
                                            </div>
                                            <p class="treatment-card-sub">{{ $category->description ? Str::limit($category->description, 60) : 'Professionelle medizinische Beratung und Behandlung' }}</p>
                                            <span class="treatment-card-cta" style="color: #8a48ff;">Mehr erfahren <i class="bi bi-arrow-right"></i></span>
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
                            <div class="col-md-6 col-lg-4">
                                <div class="premium-box-card" style="width: 100%; max-width: none;" onclick="window.location.href='{{ route('category.detail', ['id' => $category->id]) }}'">
                                    <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-decoration-none text-dark">
                                        <div class="treatment-card-body" style="flex-grow: 1; display: flex; flex-direction: column;">
                                            <h3 class="treatment-card-title">{{ $category->name }}</h3>
                                            <div class="treatment-card-tags">
                                                <span class="treatment-tag treatment-tag-type">{{ $treatmentName }}</span>
                                                @if($category->price && $category->price > 0)
                                                    <span class="treatment-tag treatment-tag-info">from {{ number_format($category->price, 0) }} €</span>
                                                @endif
                                            </div>
                                            <p class="treatment-card-sub">{{ $category->description ? Str::limit($category->description, 60) : 'Professionelle medizinische Beratung und Behandlung' }}</p>
                                            <span class="treatment-card-cta" style="color: #8a48ff;">Mehr erfahren <i class="bi bi-arrow-right"></i></span>
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
<section class="cta-gradient py-5 mt-4">
    <div class="container py-4">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-4" style="color: #1a1a1a;">Sie finden nicht,<br> wonach Sie suchen?</h2>
                <p class="lead mb-5 mx-auto" style="color: #4a4a4a; max-width: 600px; font-size: 1.15rem; line-height: 1.7;">
                    Kontaktieren Sie unser medizinisches Team für Beratung zu anderen Behandlungen und Erkrankungen.
                </p>
                <a href="{{ url('/#contact') }}" class="btn btn-hero-premium btn-lg rounded-pill px-5 py-3 fs-5 fw-bold shadow-sm">
                    Kontaktieren Sie uns
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
@include('layout.partials.footer')

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
        var cards = track.querySelectorAll('.premium-box-card');
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
            var treatmentCards = document.querySelectorAll('.premium-box-card');
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
