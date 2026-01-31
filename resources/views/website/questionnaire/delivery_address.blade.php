@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', __('Delivery Address'))

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
            <li class="text-black">{{ __('Delivery Address') }}</li>
        </ol>
    </nav>

    <!-- Address Form Card -->
    <div class="bg-white shadow-xl rounded-lg p-8">
        <div class="text-center mb-8">
            <h1 class="font-fira-sans font-medium text-3xl text-black mb-4">
                {{ __('Delivery Address') }}
            </h1>
            <p class="font-fira-sans text-gray text-lg">
                {{ __('Please provide your delivery address') }}
            </p>
        </div>

        <form id="addressForm" method="POST" action="{{ route('questionnaire.save-address', ['categoryId' => $category->id]) }}">
            @csrf

            <!-- Existing Addresses -->
            @if($existingAddresses->count() > 0)
            <div class="mb-6">
                <label class="block font-fira-sans font-medium text-black mb-3">{{ __('Select Existing Address') }}</label>
                <div class="space-y-3">
                    @foreach($existingAddresses as $address)
                    <label class="flex items-start gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary transition-all">
                        <input type="radio" name="address_id" value="{{ $address->id }}" class="mt-1">
                        <div class="flex-1">
                            <p class="font-fira-sans text-black">{{ $address->address }}</p>
                            @if($address->label)
                            <p class="font-fira-sans text-gray text-sm">{{ $address->label }}</p>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
                <div class="text-center my-4">
                    <span class="text-gray">{{ __('OR') }}</span>
                </div>
            </div>
            @endif

            <!-- New Address Form -->
            <div class="space-y-4">
                <div>
                    <label class="block font-fira-sans font-medium text-black mb-2">{{ __('Address') }} <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3" required 
                              class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:border-primary"
                              placeholder="{{ __('Enter your full address') }}">{{ $submission->delivery_address ?? old('address') }}</textarea>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-fira-sans font-medium text-black mb-2">{{ __('Postcode') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="postcode" required 
                               class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:border-primary"
                               value="{{ $submission->delivery_postcode ?? old('postcode') }}"
                               placeholder="{{ __('Postcode') }}">
                    </div>

                    <div>
                        <label class="block font-fira-sans font-medium text-black mb-2">{{ __('City') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="city" required 
                               class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:border-primary"
                               value="{{ $submission->delivery_city ?? old('city') }}"
                               placeholder="{{ __('City') }}">
                    </div>

                    <div>
                        <label class="block font-fira-sans font-medium text-black mb-2">{{ __('State') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="state" required 
                               class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:border-primary"
                               value="{{ $submission->delivery_state ?? old('state') }}"
                               placeholder="{{ __('State') }}">
                    </div>
                </div>

                <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                <input type="hidden" name="lang" id="lang" value="{{ old('lang') }}">
            </div>

            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('questionnaire.delivery-choice', ['categoryId' => $category->id]) }}" 
                   class="bg-gray-200 text-gray-700 font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-gray-300 transition duration-300">
                    {{ __('Back') }}
                </a>
                <button type="submit" class="bg-primary text-white font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-opacity-90 transition duration-300">
                    {{ __('Continue') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('addressForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            alert(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>
@endsection
