@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', __('Select Delivery Option'))

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
            <li class="text-black">{{ __('Delivery Option') }}</li>
        </ol>
    </nav>

    <div class="bg-white shadow-xl rounded-lg p-8">
        <div class="text-center mb-8">
            <h1 class="font-fira-sans font-medium text-3xl text-black mb-4">
                {{ __('Choose Delivery Option') }}
            </h1>
            <p class="font-fira-sans text-gray text-lg">
                {{ __('Select how you would like to receive your order from :pharmacy.', ['pharmacy' => $pharmacy->name]) }}
            </p>
        </div>

        <form id="deliveryForm" method="POST" action="{{ route('questionnaire.save-cannaleo-delivery', ['categoryId' => $category->id]) }}">
            @csrf

            @if(count($deliveryOptions) > 0)
            <div class="space-y-4 mb-8" id="deliveryList">
                @foreach($deliveryOptions as $option)
                <label class="block relative cursor-pointer">
                    <div class="delivery-option-card relative border-2 rounded-lg p-6 transition-all
                        {{ ($selectedOption === $option['key']) || (!$selectedOption && $loop->first) ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-primary' }}">
                        <input type="radio" name="cannaleo_delivery_option" value="{{ $option['key'] }}"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               {{ ($selectedOption === $option['key']) || (!$selectedOption && $loop->first) ? 'checked' : '' }}>
                        <div class="flex items-start gap-4 relative">
                            <div class="flex-shrink-0">
                                @if($option['key'] === 'pickup')
                                <i class="fas fa-store text-3xl text-primary"></i>
                                @elseif($option['key'] === 'express')
                                <i class="fas fa-bolt text-3xl text-primary"></i>
                                @else
                                <i class="fas fa-truck text-3xl text-primary"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-fira-sans font-medium text-xl text-black mb-1">{{ $option['label'] }}</h3>
                                <p class="font-fira-sans text-gray text-sm">{{ $option['description'] }}</p>
                                @if($option['cost'] !== null)
                                <p class="font-fira-sans text-primary font-medium mt-2">{{ number_format($option['cost'], 2, ',', '.') }} €</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <p class="font-fira-sans text-gray text-lg">{{ __('No delivery options available for this pharmacy.') }}</p>
                <p class="font-fira-sans text-gray text-sm mt-2">{{ __('Please go back and choose another pharmacy or contact support.') }}</p>
            </div>
            @endif

            <div class="flex justify-end gap-4">
                <a href="{{ route('questionnaire.cannaleo-pharmacy-selection', ['categoryId' => $category->id]) }}"
                   class="bg-gray-200 text-gray-700 font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-gray-300 transition duration-300">
                    {{ __('Back') }}
                </a>
                @if(count($deliveryOptions) > 0)
                <button type="submit" id="deliverySubmitBtn" class="bg-primary text-white font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-opacity-90 transition duration-300">
                    {{ __('Continue') }}
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

@if(count($deliveryOptions) > 0)
<script>
(function() {
    var form = document.getElementById('deliveryForm');
    var submitBtn = document.getElementById('deliverySubmitBtn');
    var cards = form.querySelectorAll('.delivery-option-card');
    var radios = form.querySelectorAll('input[name="cannaleo_delivery_option"]');

    function updateStyles() {
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
    radios.forEach(function(r) { r.addEventListener('change', updateStyles); });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var chosen = form.querySelector('input[name="cannaleo_delivery_option"]:checked');
        if (!chosen) {
            alert('{{ __("Please select a delivery option.") }}');
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
