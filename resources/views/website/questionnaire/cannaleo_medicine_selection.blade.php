@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', __('Select Medicines'))

@section('css')
<link href="{{ asset('css/medicine-selection.css') }}?v={{ time() }}" rel="stylesheet">
<style>
    .medicine-selection-ui {
        background: linear-gradient(180deg, #fdfbff 0%, #ffffff 100%);
        min-height: 100vh;
    }
</style>
@endsection

@section('content')
<main class="main-content medicine-selection-ui">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="{{ url('/') }}" class="breadcrumb-link">{{ __('Home') }}</a>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="breadcrumb-arrow">
                <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <a href="{{ route('categories') }}" class="breadcrumb-link">{{ __('Categories') }}</a>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="breadcrumb-arrow">
                <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="breadcrumb-link">{{ $category->name }}</a>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="breadcrumb-arrow">
                <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="breadcrumb-current">{{ __('Select Medicines') }}</span>
        </nav>

        <!-- Title Section -->
        <div class="title-section">
            <h1 class="title">{{ __('Select Your Medicines') }}</h1>
            <p class="subtitle">{{ __('Choose up to 3 medicines you need for this category. The doctor will review and may modify your selection if medically required.') }}</p>
        </div>

        <form id="medicineForm" method="POST" action="{{ route('questionnaire.save-cannaleo-medicine', ['categoryId' => $category->id]) }}">
            @csrf

            @if($medicines->count() > 0)
            @php
                $selectedIds = array_map('strval', $selectedCannaleoIds ?? []);
                $medicinesForJs = $medicines->map(function ($m) {
                    $strength = '';
                    if ($m->thc !== null || $m->cbd !== null) {
                        $strength = 'THC: ' . number_format($m->thc ?? 0, 1) . '% | CBD: ' . number_format($m->cbd ?? 0, 1) . '%';
                    }
                    return [
                        'id' => (string) $m->id,
                        'name' => $m->name,
                        'subtitle' => $m->category ?: 'Cannaleo Partnership',
                        'strength' => $strength,
                        'form' => $m->price !== null ? number_format($m->price, 2) . ' €' : 'On Request',
                        'image' => asset('images/upload_empty/medicine_placeholder.svg'),
                        'is_available' => true
                    ];
                })->values();
            @endphp

            <!-- Premium Info Bar -->
            <div class="info-box info-box--sticky">
                <div class="info-content">
                    <div class="info-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </div>
                    <p class="info-text">{{ __('Select up to 3 items for professional medical review.') }}</p>
                </div>
                <div class="info-selected-wrapper">
                    <p class="info-selected" id="selectedCount">{{ __('Selected') }}: {{ count($selectedIds) }} / 3</p>
                </div>
            </div>

            <!-- Medicine Cards Grid -->
            <div class="medicine-grid" id="medicineGrid">
                <!-- Data-driven cards rendered via JS for dynamic filtering/updates if needed -->
            </div>

            <!-- New sticky footer container for actions -->
            <div class="form-actions">
                <a href="{{ route('questionnaire.cannaleo-delivery-selection', ['categoryId' => $category->id]) }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i>{{ __('Back') }}
                </a>
                <button type="submit" id="medicineSubmitBtn" class="btn-continue">
                    {{ __('Continue') }}<i class="bi bi-arrow-right"></i>
                </button>
            </div>
            @else
            <div class="info-box info-box--empty">
                <div class="mb-3">
                    <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                </div>
                <h3 class="h5 fw-bold">{{ __('No medicines available') }}</h3>
                <p class="info-text--muted">{{ __('We couldn\'t find any medicines for the selected pharmacy and category.') }}</p>
                <a href="{{ route('questionnaire.cannaleo-delivery-selection', ['categoryId' => $category->id]) }}" class="btn-back mt-3">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('Return to Pharmacy Selection') }}
                </a>
            </div>
            @endif
        </form>
    </div>
</main>

@if($medicines->count() > 0)
<script>
(function() {
    const medicines = @json($medicinesForJs);
    const initialSelected = @json($selectedIds);
    let selectedMedicines = initialSelected.length ? initialSelected.slice() : [];

    const maxSelection = 3;
    const grid = document.getElementById('medicineGrid');
    const form = document.getElementById('medicineForm');
    const submitBtn = document.getElementById('medicineSubmitBtn');
    const countEl = document.getElementById('selectedCount');

    function createMedicineCard(medicine) {
        const isSelected = selectedMedicines.includes(medicine.id);
        return `
            <div class="medicine-card ${isSelected ? 'selected' : ''}" data-id="${medicine.id}" onclick="window.toggleMedicine('${medicine.id}')">
                <div class="medicine-image-container">
                    <img src="${medicine.image}" alt="${medicine.name}" class="medicine-image">
                    ${isSelected ? `
                        <div class="checkmark-badge">
                            <i class="bi bi-check-lg" style="font-size: 1.25rem;"></i>
                        </div>
                    ` : ''}
                </div>
                <div class="medicine-content">
                    <p class="medicine-subtitle">${medicine.subtitle}</p>
                    <h3 class="medicine-name">${medicine.name}</h3>
                    <div class="medicine-details">
                        ${medicine.strength ? `
                        <div class="medicine-detail-row">
                            <span class="medicine-detail-label">{{ __('Content') }}</span>
                            <span class="medicine-detail-value">${medicine.strength}</span>
                        </div>
                        ` : ''}
                        ${medicine.form ? `
                        <div class="medicine-detail-row">
                            <span class="medicine-detail-label">{{ __('Price') }}</span>
                            <span class="medicine-detail-value">${medicine.form}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
                <div class="selection-indicator ${!isSelected ? 'hidden' : ''}">
                    <span class="selection-indicator-text">{{ __('SELECTED') }}</span>
                </div>
            </div>
        `;
    }

    window.toggleMedicine = function(id) {
        const index = selectedMedicines.indexOf(id);
        if (index !== -1) {
            selectedMedicines.splice(index, 1);
        } else {
            if (selectedMedicines.length < maxSelection) {
                selectedMedicines.push(id);
            } else {
                // Optional: show a toast or message
                return;
            }
        }
        renderMedicineCards();
        updateSelectedCount();
    };

    function renderMedicineCards() {
        grid.innerHTML = medicines.map(medicine => createMedicineCard(medicine)).join('');
    }

    function updateSelectedCount() {
        if (countEl) {
            countEl.textContent = '{{ __("Selected") }}: ' + selectedMedicines.length + ' / ' + maxSelection;
        }
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (selectedMedicines.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("Selection Required") }}',
                text: '{{ __("Please select at least one medicine to continue.") }}',
                confirmButtonColor: '#8a48ff'
            });
            return;
        }
        
        if (submitBtn.disabled) return;
        submitBtn.disabled = true;
        
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("Saving...") }}';
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        selectedMedicines.forEach((id, i) => {
            formData.append('medicines[' + i + '][cannaleo_medicine_id]', id);
        });

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json' 
            }
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(res => {
            if (res.ok && res.data.success) {
                window.location.href = res.data.redirect_url;
                return;
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            Swal.fire({
                icon: 'error',
                title: '{{ __("Error") }}',
                text: res.data.message || '{{ __("An error occurred. Please try again.") }}',
                confirmButtonColor: '#8a48ff'
            });
        })
        .catch(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            Swal.fire({
                icon: 'error',
                title: '{{ __("Error") }}',
                text: '{{ __("A network error occurred. Please try again.") }}',
                confirmButtonColor: '#8a48ff'
            });
        });
    });

    // Initial render
    renderMedicineCards();
})();
</script>
@endif
@endsection
