@extends('layout.mainlayout', ['activePage' => 'category'])

@section('title', $category->name)

@section('content')
<div class="xl:w-3/4 mx-auto py-10">
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray">
            <li><a href="{{ url('/') }}" class="hover:text-primary">{{ __('Home') }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('categories') }}" class="hover:text-primary">{{ __('Categories') }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-black">{{ $category->name }}</li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="bg-white shadow-xl rounded-lg p-8 mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
            @if($category->image)
            <img src="{{ $category->fullImage }}" alt="{{ $category->name }}" 
                 class="h-32 w-32 object-cover rounded-lg">
            @else
            <div class="h-32 w-32 bg-primary bg-opacity-20 rounded-lg flex items-center justify-center">
                <i class="fas fa-stethoscope text-primary text-6xl"></i>
            </div>
            @endif
            
            <div class="flex-1">
                <h1 class="font-fira-sans font-medium text-3xl text-black mb-2">{{ $category->name }}</h1>
                
                @if($treatment)
                <p class="font-fira-sans text-lg text-gray mb-4">
                    <span class="font-medium">{{ __('Treatment:') }}</span> {{ $treatment->name }}
                </p>
                @endif

                @if($category->description || ($treatment && $treatment->description))
                <div class="font-fira-sans text-base text-gray">
                    {!! $category->description ?? ($treatment->description ?? '') !!}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Treatment Details Card -->
    @if($treatment)
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <h2 class="font-fira-sans font-medium text-2xl text-black mb-4">{{ __('Treatment Information') }}</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <p class="font-fira-sans text-sm text-gray mb-1">{{ __('Treatment Name') }}</p>
                <p class="font-fira-sans font-medium text-base text-black">{{ $treatment->name }}</p>
            </div>
            @if($treatment->fullImage)
            <div class="flex justify-center">
                <img src="{{ $treatment->fullImage }}" alt="{{ $treatment->name }}" 
                     class="h-24 w-24 object-cover rounded-lg">
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Questionnaire CTA -->
    @if($hasQuestionnaire)
    <div class="bg-gradient-to-r from-primary to-primary-light rounded-lg p-8 text-center shadow-xl">
        <h2 class="font-fira-sans font-medium text-2xl text-white mb-4">
            {{ __('Ready to Get Started?') }}
        </h2>
        <p class="font-fira-sans text-white text-lg mb-6">
            {{ __('Complete our questionnaire to help us understand your needs better') }}
        </p>
        <button id="startQuestionnaireBtn" 
                class="bg-white text-primary font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-gray-100 transition duration-300 transform hover:scale-105">
            👉 {{ __('Do you want to take questionnaire?') }}
        </button>
    </div>
    @else
    <div class="bg-gray-100 rounded-lg p-8 text-center">
        <p class="font-fira-sans text-gray text-lg">
            {{ __('No questionnaire is currently available for this category') }}
        </p>
        <a href="{{ route('categories') }}" class="inline-block mt-4 text-primary font-fira-sans font-medium hover:underline">
            {{ __('Browse Other Categories') }}
        </a>
    </div>
    @endif
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startBtn = document.getElementById('startQuestionnaireBtn');
    if (startBtn) {
        startBtn.addEventListener('click', function() {
            // Check if user is authenticated
            @if(Auth::check())
                // User is logged in - go directly to questionnaire
                window.location.href = '{{ url("/questionnaire/category/" . $category->id) }}';
            @else
                // User is NOT logged in - store intent and redirect to login
                // Store intent in localStorage for persistence across page reloads
                const intent = {
                    categoryId: {{ $category->id }},
                    treatmentId: {{ $treatment ? $treatment->id : 'null' }},
                    redirectToQuestionnaire: true,
                    redirectUrl: '{{ url("/questionnaire/category/" . $category->id) }}'
                };
                localStorage.setItem('questionnaire_intent', JSON.stringify(intent));
                
                // Redirect to login - intent will be stored in localStorage
                // Server-side session storage will be handled by the login page
                window.location.href = '{{ url("/patient-login") }}?redirect_to=' + encodeURIComponent(intent.redirectUrl);
            @endif
        });
    }
});
</script>
@endsection
