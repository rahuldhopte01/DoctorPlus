@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', __('Choose Delivery Method'))

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
            <li class="text-black">{{ __('Delivery Method') }}</li>
        </ol>
    </nav>

    <!-- Delivery Choice Card -->
    <div class="bg-white shadow-xl rounded-lg p-8">
        <div class="text-center mb-8">
            <h1 class="font-fira-sans font-medium text-3xl text-black mb-4">
                {{ __('Choose Your Delivery Method') }}
            </h1>
            <p class="font-fira-sans text-gray text-lg">
                {{ __('How would you like to receive your medication?') }}
            </p>
        </div>

        <form id="deliveryChoiceForm" method="POST" action="{{ route('questionnaire.save-delivery-choice', ['categoryId' => $category->id]) }}">
            @csrf
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Delivery Option -->
                <label class="block relative cursor-pointer">
                    <div class="relative border-2 rounded-lg p-6 transition-all delivery-option-card
                        {{ $submission->delivery_type === 'delivery' || !$submission->delivery_type ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-primary' }}">
                        <input type="radio" name="delivery_type" value="delivery" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               {{ $submission->delivery_type === 'delivery' || !$submission->delivery_type ? 'checked' : '' }}>
                        <div class="flex items-start gap-4 relative">
                            <div class="flex-shrink-0">
                                <i class="fas fa-truck text-3xl text-primary"></i>
                            </div>
                            <div>
                                <h3 class="font-fira-sans font-medium text-xl text-black mb-2">{{ __('Home Delivery') }}</h3>
                                <p class="font-fira-sans text-gray">{{ __('Get your medication delivered to your doorstep') }}</p>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- Pickup Option -->
                <label class="block relative cursor-pointer">
                    <div class="relative border-2 rounded-lg p-6 transition-all delivery-option-card
                        {{ $submission->delivery_type === 'pickup' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-primary' }}">
                        <input type="radio" name="delivery_type" value="pickup" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               {{ $submission->delivery_type === 'pickup' ? 'checked' : '' }}>
                        <div class="flex items-start gap-4 relative">
                            <div class="flex-shrink-0">
                                <i class="fas fa-store text-3xl text-primary"></i>
                            </div>
                            <div>
                                <h3 class="font-fira-sans font-medium text-xl text-black mb-2">{{ __('Pharmacy Pickup') }}</h3>
                                <p class="font-fira-sans text-gray">{{ __('Pick up your medication from a nearby pharmacy') }}</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('category.detail', ['id' => $category->id]) }}" 
                   class="bg-gray-200 text-gray-700 font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-gray-300 transition duration-300">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="bg-primary text-white font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-opacity-90 transition duration-300">
                    {{ __('Continue') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const form = document.getElementById('deliveryChoiceForm');
    const cards = form.querySelectorAll('.delivery-option-card');
    const radios = form.querySelectorAll('input[name="delivery_type"]');

    function updateSelectedStyle() {
        cards.forEach(function(card, i) {
            const radio = radios[i];
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
        r.addEventListener('change', updateSelectedStyle);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var chosen = form.querySelector('input[name="delivery_type"]:checked');
        if (!chosen) {
            alert('{{ __("Please select a delivery method.") }}');
            return;
        }
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
            var res = _.ok, data = _.data;
            if (res && data.success) {
                window.location.href = data.redirect_url;
            } else {
                var msg = (data && data.message) || (data && data.errors && JSON.stringify(data.errors)) || '{{ __("An error occurred. Please try again.") }}';
                alert(msg);
            }
        })
        .catch(function(err) {
            console.error(err);
            alert('{{ __("An error occurred. Please try again.") }}');
        });
    });
})();
</script>
@endsection
