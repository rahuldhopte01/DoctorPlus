@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', __('Select Pickup Branch'))

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
            <li class="text-black">{{ __('Pickup Branch') }}</li>
        </ol>
    </nav>

    <div class="bg-white shadow-xl rounded-lg p-8">
        <div class="text-center mb-8">
            <h1 class="font-fira-sans font-medium text-3xl text-black mb-4">
                {{ __('Choose Pickup Branch') }}
            </h1>
            <p class="font-fira-sans text-gray text-lg">
                {{ __('Select which branch of :pharmacy you will collect your order from.', ['pharmacy' => $pharmacy->name]) }}
            </p>
        </div>

        <form id="branchForm" method="POST" action="{{ route('questionnaire.save-cannaleo-branch', ['categoryId' => $category->id]) }}">
            @csrf

            <div class="space-y-4 mb-8" id="branchList">
                @foreach($branches as $branch)
                @php
                    $branchId = (string) ($branch['id'] ?? '');
                    $branchName = $branch['name'] ?? __('Branch');
                    $branchAddress = isset($branch['address']) ? $branch['address'] : '';
                    $isSelected = $selectedBranchId === $branchId;
                @endphp
                <label class="block relative cursor-pointer">
                    <div class="branch-card relative border-2 rounded-lg p-6 transition-all
                        {{ $isSelected ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-primary' }}">
                        <input type="radio" name="pickup_branch_id" value="{{ $branchId }}"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               {{ $isSelected ? 'checked' : '' }}>
                        <div class="flex items-start gap-4 relative">
                            <div class="flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-3xl text-primary"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-fira-sans font-medium text-xl text-black mb-1">{{ $branchName }}</h3>
                                @if($branchAddress)
                                <p class="font-fira-sans text-gray text-sm">{{ $branchAddress }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('questionnaire.cannaleo-delivery-selection', ['categoryId' => $category->id]) }}"
                   class="bg-gray-200 text-gray-700 font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-gray-300 transition duration-300">
                    {{ __('Back') }}
                </a>
                <button type="submit" id="branchSubmitBtn"
                        class="bg-primary text-white font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-opacity-90 transition duration-300">
                    {{ __('Continue') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('branchForm');
    var submitBtn = document.getElementById('branchSubmitBtn');
    var cards = form.querySelectorAll('.branch-card');
    var radios = form.querySelectorAll('input[name="pickup_branch_id"]');

    function updateStyles() {
        cards.forEach(function (card, i) {
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
    radios.forEach(function (r) { r.addEventListener('change', updateStyles); });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var chosen = form.querySelector('input[name="pickup_branch_id"]:checked');
        if (!chosen) {
            alert('{{ __("Please select a pickup branch.") }}');
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
        .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
        .then(function (_) {
            if (_.ok && _.data.success) {
                window.location.href = _.data.redirect_url;
                return;
            }
            submitBtn.disabled = false;
            submitBtn.textContent = '{{ __("Continue") }}';
            alert((_.data && _.data.message) || '{{ __("An error occurred. Please try again.") }}');
        })
        .catch(function () {
            submitBtn.disabled = false;
            submitBtn.textContent = '{{ __("Continue") }}';
            alert('{{ __("An error occurred. Please try again.") }}');
        });
    });
})();
</script>
@endsection
