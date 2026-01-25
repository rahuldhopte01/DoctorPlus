@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', __('Select Medicines'))

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
                {{ __('Choose the medicines you need for this category. Doctor can modify if needed.') }}
            </p>
        </div>

        <form id="medicineForm" method="POST" action="{{ route('questionnaire.save-medicine', ['categoryId' => $category->id]) }}">
            @csrf

            @if($medicines->count() > 0)
            @php
                $selectedIds = array_column($selectedMedicines ?? [], 'medicine_id');
            @endphp
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8" id="medicineList">
                @foreach($medicines as $medicine)
                <label class="block relative cursor-pointer">
                    <div class="medicine-card relative border-2 rounded-lg p-4 transition-all
                        {{ in_array($medicine->id, $selectedIds) ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-primary' }}">
                        <input type="checkbox" name="medicine_ids[]" value="{{ $medicine->id }}"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer medicine-checkbox"
                               {{ in_array($medicine->id, $selectedIds) ? 'checked' : '' }}>
                        <h3 class="font-fira-sans font-medium text-lg text-black mb-2">{{ $medicine->name }}</h3>
                        @if($medicine->brand)
                        <p class="font-fira-sans text-primary text-sm mb-2">{{ $medicine->brand->name }}</p>
                        @endif
                        @if($medicine->strength)
                        <p class="font-fira-sans text-gray text-sm mb-2">{{ __('Strength') }}: {{ $medicine->strength }}</p>
                        @endif
                        @if($medicine->form)
                        <p class="font-fira-sans text-gray text-sm">{{ __('Form') }}: {{ $medicine->form }}</p>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                <p class="font-fira-sans text-blue-800 text-sm">
                    <i class="fas fa-info-circle"></i>
                    {{ __('Select one or more medicines. The doctor will review and may modify your selection if medically required.') }}
                </p>
            </div>
            @else
            <div class="text-center py-12 mb-8">
                <p class="font-fira-sans text-gray text-lg">{{ __('No medicines available for this category.') }}</p>
                <p class="font-fira-sans text-gray text-sm mt-2">{{ __('Please contact support or try another category.') }}</p>
            </div>
            @endif

            <div class="flex justify-end gap-4">
                <a href="{{ $submission->delivery_type === 'delivery' ? route('questionnaire.delivery-address', ['categoryId' => $category->id]) : route('questionnaire.pharmacy-selection', ['categoryId' => $category->id]) }}"
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
    var cards = form.querySelectorAll('.medicine-card');
    var checkboxes = form.querySelectorAll('.medicine-checkbox');

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

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', updateCardStyles);
    });

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
            formData.append('medicines[' + i + '][medicine_id]', cb.value);
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
            var msg = (data && data.message) || (data && data.errors && JSON.stringify(data.errors)) || '{{ __("An error occurred. Please try again.") }}';
            alert(msg);
        })
        .catch(function(err) {
            console.error(err);
            submitBtn.disabled = false;
            submitBtn.textContent = '{{ __("Continue") }}';
            alert('{{ __("An error occurred. Please try again.") }}');
        });
    });
})();
</script>
@endif
@endsection
