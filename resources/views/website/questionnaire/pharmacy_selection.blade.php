@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', __('Select Pharmacy'))

@section('content')
<div class="xl:w-3/4 mx-auto py-10">
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray">
            <li><a href="{{ url('/') }}" class="hover:text-primary">{{ __('Home') }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('categories') }}" class="hover:text-primary">{{ __('Categories') }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('category.detail', ['id' => $category->id]) }}" class="hover:text-primary">{{ $category->name }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-black">{{ __('Select Pharmacy') }}</li>
        </ol>
    </nav>

    <!-- Pharmacy Selection Card -->
    <div class="bg-white shadow-xl rounded-lg p-8">
        <div class="text-center mb-8">
            <h1 class="font-fira-sans font-medium text-3xl text-black mb-4">
                {{ __('Select a Pharmacy') }}
            </h1>
            <p class="font-fira-sans text-gray text-lg">
                {{ __('Choose a pharmacy near you for pickup') }}
            </p>
        </div>

        <form id="pharmacyForm" method="POST" action="{{ route('questionnaire.save-pharmacy', ['categoryId' => $category->id]) }}">
            @csrf

            @if($pharmacies->count() > 0)
            <div class="space-y-4 mb-8" id="pharmacyList">
                @foreach($pharmacies as $pharmacy)
                <label class="block relative cursor-pointer">
                    <div class="pharmacy-card relative border-2 rounded-lg p-6 transition-all
                        {{ ($selectedPharmacyId == $pharmacy->id) || (!$selectedPharmacyId && $loop->first) ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-primary' }}">
                        <input type="radio" name="pharmacy_id" value="{{ $pharmacy->id }}"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               {{ ($selectedPharmacyId == $pharmacy->id) || (!$selectedPharmacyId && $loop->first) ? 'checked' : '' }}>
                        <div class="flex items-start gap-4 relative">
                            <div class="flex-shrink-0">
                                <i class="fas fa-store text-3xl text-primary"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-fira-sans font-medium text-xl text-black mb-2">{{ $pharmacy->name }}</h3>
                                <p class="font-fira-sans text-gray mb-2">{{ $pharmacy->address }}</p>
                                @if($pharmacy->postcode)
                                <p class="font-fira-sans text-gray text-sm mb-2">{{ __('Postcode') }}: {{ $pharmacy->postcode }}</p>
                                @endif
                                @if(isset($pharmacy->distance))
                                <p class="font-fira-sans text-primary text-sm">
                                    <i class="fas fa-map-marker-alt"></i> {{ number_format($pharmacy->distance, 2) }} {{ __('miles away') }}
                                </p>
                                @endif
                                @if($pharmacy->phone)
                                <p class="font-fira-sans text-gray text-sm mt-2">
                                    <i class="fas fa-phone"></i> {{ $pharmacy->phone }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <p class="font-fira-sans text-gray text-lg">{{ __('No pharmacies available at the moment.') }}</p>
            </div>
            @endif

            <div class="flex justify-end gap-4">
                <a href="{{ route('questionnaire.delivery-choice', ['categoryId' => $category->id]) }}" 
                   class="bg-gray-200 text-gray-700 font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-gray-300 transition duration-300">
                    {{ __('Back') }}
                </a>
                @if($pharmacies->count() > 0)
                <button type="submit" id="pharmacySubmitBtn" class="bg-primary text-white font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-opacity-90 transition duration-300">
                    {{ __('Continue') }}
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

@if($pharmacies->count() > 0)
<script>
(function() {
    var form = document.getElementById('pharmacyForm');
    var submitBtn = document.getElementById('pharmacySubmitBtn');
    var cards = form.querySelectorAll('.pharmacy-card');
    var radios = form.querySelectorAll('input[name="pharmacy_id"]');

    function updatePharmacyCardStyles() {
        cards.forEach(function(card, i) {
            var radio = radios[i];
            if (radio && radio.checked) {
                card.classList.add('border-primary', 'bg-primary/5');
                card.classList.remove('border-gray-200');
            } else {
                card.classList.remove('border-primary', 'bg-primary/5');
                card.classList.add('border-gray-200');
            }
        });
    }

    radios.forEach(function(r) {
        r.addEventListener('change', updatePharmacyCardStyles);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var chosen = form.querySelector('input[name="pharmacy_id"]:checked');
        if (!chosen) {
            alert('{{ __("Please select a pharmacy.") }}');
            return;
        }
        if (submitBtn.disabled) return;
        submitBtn.disabled = true;
        submitBtn.textContent = '{{ __("Saving...") }}';

        var formData = new FormData(form);
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
