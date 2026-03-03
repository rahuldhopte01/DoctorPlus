@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', __('Select Cannaleo Medicines'))

@section('content')
<div class="xl:w-3/4 mx-auto py-10">
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray">
            <li><a href="{{ url('/') }}" class="hover:text-primary">{{ __('Home') }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('categories') }}" class="hover:text-primary">{{ __('Categories') }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('category.detail', ['id' => $category->id]) }}" class="hover:text-primary">{{ $category->name }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-black">{{ __('Select Medicines') }}</li>
        </ol>
    </nav>

    <div class="bg-white shadow-xl rounded-lg p-8">
        <div class="text-center mb-8">
            <h1 class="font-fira-sans font-medium text-3xl text-black mb-4">
                {{ __('Select Your Medicines') }}
            </h1>
            <p class="font-fira-sans text-gray text-lg">
                {{ __('Choose up to 3 medicines from the partner pharmacy. Doctor will review your selection.') }}
            </p>
        </div>

        <form id="medicineForm" method="POST" action="{{ route('questionnaire.save-cannaleo-medicine', ['categoryId' => $category->id]) }}">
            @csrf

            @if($medicines->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8" id="medicineList">
                @foreach($medicines as $medicine)
                <label class="block relative cursor-pointer">
                    <div class="medicine-card relative border-2 rounded-lg p-4 transition-all
                        {{ in_array($medicine->id, $selectedCannaleoIds ?? []) ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-primary' }}">
                        <input type="checkbox" name="cannaleo_medicine_ids[]" value="{{ $medicine->id }}"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer medicine-checkbox"
                               {{ in_array($medicine->id, $selectedCannaleoIds ?? []) ? 'checked' : '' }}>
                        <h3 class="font-fira-sans font-medium text-lg text-black mb-2">{{ $medicine->name }}</h3>
                        @if($medicine->price !== null)
                        <p class="font-fira-sans text-primary text-sm mb-2">{{ number_format($medicine->price, 2) }} €</p>
                        @endif
                        @if($medicine->thc !== null || $medicine->cbd !== null)
                        <p class="font-fira-sans text-gray text-sm mb-2">THC: {{ number_format($medicine->thc ?? 0, 1) }}% / CBD: {{ number_format($medicine->cbd ?? 0, 1) }}%</p>
                        @endif
                        @if($medicine->category)
                        <p class="font-fira-sans text-gray text-sm">{{ $medicine->category }}</p>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                <p class="font-fira-sans text-blue-800 text-sm">
                    <i class="fas fa-info-circle"></i>
                    {{ __('You can select up to 3 medicines. The doctor will review and may modify your selection if medically required.') }}
                </p>
                <p class="font-fira-sans text-blue-800 text-sm mt-2" id="selectionCount">
                    <strong>{{ __('Selected:') }} <span id="selectedCount">0</span> / 3</strong>
                </p>
            </div>
            @else
            <div class="text-center py-12 mb-8">
                <p class="font-fira-sans text-gray text-lg">{{ __('No medicines available for this pharmacy and category.') }}</p>
            </div>
            @endif

            <div class="flex justify-end gap-4">
                <a href="{{ route('questionnaire.cannaleo-pharmacy-selection', ['categoryId' => $category->id]) }}"
                   class="bg-gray-200 text-gray-700 font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-gray-300 transition duration-300">
                    {{ __('Back') }}
                </a>
                @if($medicines->count() > 0)
                <button type="submit" id="medicineSubmitBtn" class="bg-primary text-white font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-opacity-90 transition duration-300">
                    {{ __('Continue') }}
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

@if($medicines->count() > 0)
<script>
(function() {
    var form = document.getElementById('medicineForm');
    var submitBtn = document.getElementById('medicineSubmitBtn');
    var checkboxes = form.querySelectorAll('.medicine-checkbox');
    var cards = form.querySelectorAll('.medicine-card');
    var maxSelection = 3;

    function updateCardStyles() {
        cards.forEach(function(card, i) {
            var cb = checkboxes[i];
            if (cb && cb.checked) {
                card.classList.add('border-primary', 'bg-primary/5');
                card.classList.remove('border-gray-200');
            } else {
                card.classList.remove('border-primary', 'bg-primary/5');
                card.classList.add('border-gray-200');
            }
        });
    }
    function updateSelectionCount() {
        var checked = form.querySelectorAll('.medicine-checkbox:checked');
        var count = checked.length;
        var el = document.getElementById('selectedCount');
        if (el) el.textContent = count;
        checkboxes.forEach(function(cb) {
            var label = cb.closest('label');
            if (count >= maxSelection && !cb.checked) {
                cb.disabled = true;
                if (label) { label.style.opacity = '0.5'; label.style.cursor = 'not-allowed'; }
            } else {
                cb.disabled = false;
                if (label) { label.style.opacity = '1'; label.style.cursor = 'pointer'; }
            }
        });
    }
    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            var checked = form.querySelectorAll('.medicine-checkbox:checked');
            if (checked.length > maxSelection) {
                this.checked = false;
                alert('{{ __("You can select a maximum of 3 medicines.") }}');
                return;
            }
            updateCardStyles();
            updateSelectionCount();
        });
    });
    updateSelectionCount();
    updateCardStyles();

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var checked = form.querySelectorAll('.medicine-checkbox:checked');
        if (checked.length === 0) {
            alert('{{ __("Please select at least one medicine.") }}');
            return;
        }
        if (submitBtn.disabled) return;
        submitBtn.disabled = true;
        submitBtn.textContent = '{{ __("Saving...") }}';

        var formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        checked.forEach(function(cb, i) {
            formData.append('medicines[' + i + '][cannaleo_medicine_id]', cb.value);
        });

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(function(res) { return res.json().then(function(data) { return { ok: res.ok, data: data }; }); })
        .then(function(_) {
            var ok = _.ok, data = _.data;
            if (ok && data.success) {
                window.location.href = data.redirect_url;
                return;
            }
            submitBtn.disabled = false;
            submitBtn.textContent = '{{ __("Continue") }}';
            alert((data && data.message) || '{{ __("An error occurred. Please try again.") }}');
        })
        .catch(function(err) {
            submitBtn.disabled = false;
            submitBtn.textContent = '{{ __("Continue") }}';
            alert('{{ __("An error occurred. Please try again.") }}');
        });
    });
})();
</script>
@endif
@endsection
