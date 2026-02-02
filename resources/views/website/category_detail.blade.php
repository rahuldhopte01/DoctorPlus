@extends('layout.mainlayout', ['activePage' => 'category'])

@section('title', $category->name)

@section('content')
{{-- Hero Banner Section --}}
<div class="relative w-full bg-cover bg-center flex items-center justify-center" 
     style="min-height: 350px; background-image: url('{{ $category->fullImage }}'); background-color: #0b2c4e;">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div> {{-- Dark Overlay --}}
    <div class="z-10 text-center px-4">
        <h1 class="text-4xl md:text-5xl font-bold text-white font-fira-sans mb-4 tracking-wide">{{ $category->name }}</h1>
        <nav class="flex justify-center" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-gray-200">
                <li><a href="{{ url('/') }}" class="hover:text-white transition-colors">{{ __('Home') }}</a></li>
                <li><span class="mx-2">/</span></li>
                <li><a href="{{ route('categories') }}" class="hover:text-white transition-colors">{{ __('Categories') }}</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-white font-medium">{{ $category->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        {{-- Main Content Column --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Description Section --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4 mb-6">
                     <div class="p-3 bg-green-50 rounded-full">
                        <i class="fas fa-notes-medical text-2xl text-green-600"></i>
                     </div>
                     <h2 class="text-2xl font-bold text-gray-900 font-fira-sans">{{ __('Overview') }}</h2>
                </div>
                
                @if($category->description || ($treatment && $treatment->description))
                <div class="prose prose-lg text-gray-600 font-fira-sans leading-relaxed">
                    {!! $category->description ?? ($treatment->description ?? '') !!}
                </div>
                @else
                <p class="text-gray-500 italic">{{ __('No description available.') }}</p>
                @endif
                
                @if($treatment)
                <div class="mt-8 p-6 bg-blue-50 rounded-xl border-l-4 border-blue-500">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">{{ __('Treatment Focus') }}</h3>
                    <p class="text-blue-800 font-medium">{{ $treatment->name }}</p>
                </div>
                @endif
                
                {{-- Main Content CTA --}}
                {{-- Shown always for design verification --}}
                <div class="mt-8 bg-gradient-to-r from-primary to-primary-light rounded-xl p-6 text-center shadow-md">
                    <h2 class="font-fira-sans font-medium text-xl text-white mb-2">
                        {{ __('Ready to Get Started?') }}
                    </h2>
                    <p class="font-fira-sans text-white text-sm mb-4 opacity-90">
                        {{ __('Complete our questionnaire to help us understand your needs better') }}
                    </p>
                    <button class="start-questionnaire-btn bg-white text-primary font-fira-sans font-semibold px-6 py-2.5 rounded-full hover:bg-gray-50 transition duration-300 shadow-sm text-sm border-2 border-transparent hover:border-white uppercase tracking-wider flex items-center justify-center mx-auto">
                        <span class="mr-2">👉</span> {{ __('Do you want to take questionnaire?') }}
                    </button>
                </div>
            </div>

            {{-- Gallery Section --}}
            <div class="mt-12">
                <div class="flex items-center gap-4 mb-6">
                     <div class="w-1 h-8 bg-primary rounded-full"></div>
                     <h2 class="text-2xl font-bold text-gray-900 font-fira-sans">{{ __('Gallery') }}</h2>
                </div>
                
                {{-- Placeholder Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                     {{-- Since we don't have dynamic gallery data, we show placeholders or check provided images --}}
                     <div class="group relative aspect-video overflow-hidden rounded-xl bg-gray-100 hover:shadow-lg transition-all duration-300">
                        <img src="https://placehold.co/600x400/e2e8f0/64748b?text=Medical+Facility" alt="Gallery 1" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                     </div>
                     <div class="group relative aspect-video overflow-hidden rounded-xl bg-gray-100 hover:shadow-lg transition-all duration-300">
                        <img src="https://placehold.co/600x400/e2e8f0/64748b?text=Equipment" alt="Gallery 2" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                     </div>
                     <div class="group relative aspect-video overflow-hidden rounded-xl bg-gray-100 hover:shadow-lg transition-all duration-300">
                        <img src="https://placehold.co/600x400/e2e8f0/64748b?text=Patient+Care" alt="Gallery 3" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                     </div>
                     <div class="group relative aspect-video overflow-hidden rounded-xl bg-gray-100 hover:shadow-lg transition-all duration-300">
                        <img src="https://placehold.co/600x400/e2e8f0/64748b?text=Specialists" alt="Gallery 4" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                     </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Column --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Questionnaire CTA Card --}}
            @if($hasQuestionnaire)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 sticky top-24">
                <div class="bg-gradient-to-r from-primary to-blue-600 p-6 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                        <i class="fas fa-clipboard-check text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">{{ __('Start Consultation') }}</h3>
                    <p class="text-blue-100 text-sm mb-0">{{ __('Answer a few questions to get personalized advice.') }}</p>
                </div>
                <div class="p-6">
                    <button class="start-questionnaire-btn w-full bg-primary hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl transition duration-300 transform hover:-translate-y-1 shadow-md flex items-center justify-center group">
                        <span>{{ __('Take Questionnaire') }}</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                    </button>
                    <p class="text-xs text-center text-gray-400 mt-4 px-4">{{ __('Your responses are confidential and reviewed by certified doctors.') }}</p>
                </div>
            </div>
            @else
            <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-200 sticky top-24">
                <i class="fas fa-info-circle text-gray-400 text-3xl mb-3"></i>
                <p class="text-gray-600 font-medium">{{ __('No questionnaire available.') }}</p>
                <a href="{{ route('categories') }}" class="inline-block mt-4 text-primary hover:underline font-medium text-sm">
                    {{ __('Browse All Categories') }}
                </a>
            </div>
            @endif

            {{-- Helper Info --}}
            <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                <h4 class="font-bold text-blue-900 mb-2 flex items-center">
                    <i class="fas fa-shield-alt mr-2"></i> {{ __('Why Choose Us?') }}
                </h4>
                <ul class="space-y-3 mt-4">
                    <li class="flex items-start text-sm text-blue-800">
                        <i class="fas fa-check-circle mt-1 mr-2 text-green-500"></i>
                        <span>{{ __('Verified Specialists') }}</span>
                    </li>
                    <li class="flex items-start text-sm text-blue-800">
                        <i class="fas fa-check-circle mt-1 mr-2 text-green-500"></i>
                        <span>{{ __('Secure & Confidential') }}</span>
                    </li>
                    <li class="flex items-start text-sm text-blue-800">
                        <i class="fas fa-check-circle mt-1 mr-2 text-green-500"></i>
                        <span>{{ __('24/7 Support Available') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startBtns = document.querySelectorAll('.start-questionnaire-btn');
    startBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            @if(Auth::check())
                window.location.href = '{{ url("/questionnaire/category/" . $category->id) }}';
            @else
                const intent = {
                    categoryId: {{ $category->id }},
                    treatmentId: {{ $treatment ? $treatment->id : 'null' }},
                    redirectToQuestionnaire: true,
                    redirectUrl: '{{ url("/questionnaire/category/" . $category->id) }}'
                };
                localStorage.setItem('questionnaire_intent', JSON.stringify(intent));
                window.location.href = '{{ url("/patient-login") }}?redirect_to=' + encodeURIComponent(intent.redirectUrl);
            @endif
        });
    });
});
</script>
@endsection
