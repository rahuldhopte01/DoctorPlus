@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', __('Select Medicines'))

@section('css')
<link href="{{ asset('css/medicine-selection.css') }}" rel="stylesheet">
@endsection

@section('content')
<main class="main-content medicine-selection-ui">
    <!-- Breadcrumb (same as index.html) -->
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

    <!-- Title Section (same as index.html) -->
    <div class="title-section">
        <h1 class="title">{{ __('Select Your Medicines') }}</h1>
        <p class="subtitle">{{ __('Choose up to 3 medicines you need for this category. Doctor can modify if needed.') }}</p>
    </div>

    <form id="medicineForm" method="POST" action="{{ route('questionnaire.save-medicine', ['categoryId' => $category->id]) }}">
        @csrf

        @if($medicines->count() > 0)
        @php
            $selectedIds = array_column($selectedMedicines ?? [], 'medicine_id');
        @endphp
        <!-- Medicine Cards Grid (same as index.html) -->
        <div class="medicine-grid" id="medicineGrid">
            @foreach($medicines as $medicine)
            @php $isSelected = in_array($medicine->id, $selectedIds); @endphp
            <label class="medicine-card-wrap">
                <input type="checkbox" name="medicine_ids[]" value="{{ $medicine->id }}" class="medicine-checkbox" {{ $isSelected ? 'checked' : '' }}>
                <div class="medicine-card {{ $isSelected ? 'selected' : '' }}">
                    <div class="medicine-image-container">
                        <img src="{{ $medicine->image_url }}" alt="{{ $medicine->name }}" class="medicine-image">
                        @if($isSelected)
                        <div class="checkmark-badge">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M13.333 4L6 11.333L2.667 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="medicine-content">
                        <h3 class="medicine-name">{{ $medicine->name }}</h3>
                        <p class="medicine-subtitle">{{ $medicine->brand ? $medicine->brand->name : $medicine->name }}</p>
                        <div class="medicine-details">
                            @if($medicine->strength)
                            <div class="medicine-detail-row">
                                <span class="medicine-detail-label">{{ __('Strength') }}:</span>
                                <span class="medicine-detail-value">{{ $medicine->strength }}</span>
                            </div>
                            @endif
                            @if($medicine->form)
                            <div class="medicine-detail-row">
                                <span class="medicine-detail-label">{{ __('Form') }}:</span>
                                <span class="medicine-detail-value">{{ $medicine->form }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="selection-indicator {{ $isSelected ? '' : 'hidden' }}">
                        <div class="selection-indicator-content">
                            <p class="selection-indicator-text">✓ {{ __('SELECTED') }}</p>
                        </div>
                    </div>
                </div>
            </label>
            @endforeach
        </div>

        <!-- Info Box (same as index.html) -->
        <div class="info-box">
            <div class="info-content">
                <svg class="info-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <p class="info-text">{{ __('You can select up to 3 medicines. The doctor will review and may modify your selection if medically required.') }}</p>
            </div>
            <p class="info-selected">Selected: <span id="selectedCount">{{ count($selectedIds) }}</span> / 3</p>
        </div>
        @else
        <div class="info-box" style="text-align: center; padding: 2rem;">
            <p style="color: #374151;">{{ __('No medicines available for this category.') }}</p>
            <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem;">{{ __('Please contact support or try another category.') }}</p>
        </div>
        @endif

        <div class="form-actions">
            <a href="{{ $submission->delivery_type === 'delivery' ? route('questionnaire.delivery-address', ['categoryId' => $category->id]) : route('questionnaire.pharmacy-selection', ['categoryId' => $category->id]) }}" class="btn-back">{{ __('Back') }}</a>
            @if($medicines->count() > 0)
            <button type="submit" id="medicineSubmitBtn" class="btn-continue">{{ __('Continue') }}</button>
            @endif
        </div>
    </form>
</main>

@if($medicines->count() > 0)
<script>
(function() {
    var form = document.getElementById('medicineForm');
    var submitBtn = document.getElementById('medicineSubmitBtn');
    var countEl = document.getElementById('selectedCount');
    var wraps = form.querySelectorAll('.medicine-card-wrap');
    var checkboxes = form.querySelectorAll('.medicine-checkbox');
    var maxSelection = 3;

    function updateUi() {
        var checked = form.querySelectorAll('.medicine-checkbox:checked');
        var count = checked.length;
        if (countEl) countEl.textContent = count;

        wraps.forEach(function(wrap, i) {
            var cb = checkboxes[i];
            var card = wrap.querySelector('.medicine-card');
            var imgContainer = card ? card.querySelector('.medicine-image-container') : null;
            var indicator = card ? card.querySelector('.selection-indicator') : null;
            var checkmark = card ? card.querySelector('.checkmark-badge') : null;
            var isSelected = cb && cb.checked;

            if (card) card.classList.toggle('selected', isSelected);
            if (indicator) indicator.classList.toggle('hidden', !isSelected);
            if (isSelected && imgContainer && !checkmark) {
                var badge = document.createElement('div');
                badge.className = 'checkmark-badge';
                badge.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M13.333 4L6 11.333L2.667 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                imgContainer.appendChild(badge);
            } else if (!isSelected && checkmark) checkmark.remove();
        });

        checkboxes.forEach(function(cb) {
            var wrap = cb.closest('.medicine-card-wrap');
            if (count >= maxSelection && !cb.checked) {
                cb.disabled = true;
                if (wrap) { wrap.style.opacity = '0.5'; wrap.style.pointerEvents = 'none'; }
            } else {
                cb.disabled = false;
                if (wrap) { wrap.style.opacity = '1'; wrap.style.pointerEvents = ''; }
            }
        });
    }

    wraps.forEach(function(wrap) {
        wrap.addEventListener('click', function(e) {
            if (e.target.tagName === 'INPUT') return;
            var cb = wrap.querySelector('.medicine-checkbox');
            if (!cb || cb.disabled) return;
            cb.checked = !cb.checked;
            if (form.querySelectorAll('.medicine-checkbox:checked').length > maxSelection) cb.checked = false;
            updateUi();
        });
    });
    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', updateUi);
    });
    updateUi();

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var checked = form.querySelectorAll('.medicine-checkbox:checked');
        if (checked.length === 0) {
            alert('{{ __("Please select at least one medicine.") }}');
            return;
        }
        if (checked.length > maxSelection) {
            alert('{{ __("You can select a maximum of 3 medicines.") }}');
            return;
        }
        if (submitBtn.disabled) return;
        submitBtn.disabled = true;
        submitBtn.textContent = '{{ __("Saving...") }}';
        var formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        checked.forEach(function(cb, i) { formData.append('medicines[' + i + '][medicine_id]', cb.value); });
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
        .then(function(_) {
            if (_.ok && _.data.success) { window.location.href = _.data.redirect_url; return; }
            submitBtn.disabled = false;
            submitBtn.textContent = '{{ __("Continue") }}';
            alert(_.data.message || (_.data.errors && JSON.stringify(_.data.errors)) || '{{ __("An error occurred. Please try again.") }}');
        })
        .catch(function() {
            submitBtn.disabled = false;
            submitBtn.textContent = '{{ __("Continue") }}';
            alert('{{ __("An error occurred. Please try again.") }}');
        });
    });
})();
</script>
@endif
@endsection
