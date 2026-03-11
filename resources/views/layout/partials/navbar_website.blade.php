<nav class="navbar navbar-expand-lg navbar-light sticky-top border-bottom" style="background-color: #f2efea !important;">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            @if($setting->company_logo && file_exists(public_path('images/upload/'.$setting->company_logo)))
                <img src="{{ $setting->logo }}" alt="{{ $setting->business_name }}" style="height: 32px;">
            @else
                <img src="{{ url('/images/upload_empty/fuxxlogo.png') }}" alt="{{ $setting->business_name }}" style="height: 32px;">
            @endif
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item">
                    <a class="nav-link px-3" href="{{ route('categories') }}">{{ __('Treatments') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="{{ url('/#how-it-works') }}">{{ __('How it works') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="{{ url('/about-us') }}">{{ __('About us') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="{{ url('/#faq') }}">{{ __('Help') }}</a>
                </li>
            </ul>
            
            <div class="d-flex gap-3 align-items-center mt-3 mt-lg-0">
                @php
                    $website_languages = \App\Models\Language::where('status', 1)->get();
                    $current_lang = \App\Models\Language::where('name', session('locale'))->first();
                    $current_lang_image = $current_lang ? $current_lang->image : 'english.png';
                @endphp
                @if($website_languages->count() > 1)
                <div class="dropdown">
                    <button class="text-dark text-decoration-none dropdown-toggle px-0 d-flex align-items-center gap-1 bg-transparent border-0" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="rounded object-fit-cover" style="width: 20px; height: 20px;" src="{{ asset('images/upload/'.$current_lang_image) }}" alt="{{ session('locale', 'en') }}">
                        <span class="d-none d-sm-inline small fw-medium">{{ $current_lang ? $current_lang->name : __('English') }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="langDropdown">
                        @foreach ($website_languages as $lang)
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 {{ session('locale') == $lang->name ? 'bg-light fw-medium' : '' }}" href="{{ url('/select_language/'.$lang->id) }}">
                                <img class="rounded object-fit-cover" style="width: 20px; height: 20px;" src="{{ asset('images/upload/'.$lang->image) }}" alt="{{ $lang->name }}">
                                <span>{{ $lang->name }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                @if(auth()->check())
                    <div class="dropdown">
                        <a href="#" class="btn btn-link text-dark text-decoration-none dropdown-toggle px-0" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person"></i> <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ url('user_profile') }}">{{ __('Dashboard') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Sign out') }}</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ url('patient-login') }}" class="text-decoration-none d-flex align-items-center justify-content-center gap-1" style="color: #00bda6; border: 1px solid #00bda6; border-radius: 6px; background-color: transparent; font-weight: 500; font-size: 0.95rem; height: 38px; padding: 0 16px;">
                        <i class="bi bi-person" style="font-size: 1.1rem; line-height: 1;"></i> {{ __('Sign in') }}
                    </a>
                @endif
                <a href="{{ route('categories') }}" class="text-decoration-none d-flex align-items-center justify-content-center" style="color: #00bda6; border: 1px solid #00bda6; border-radius: 6px; background-color: transparent; font-weight: 500; font-size: 0.95rem; height: 38px; padding: 0 16px;">{{ __('Start treatment') }}</a>
            </div>
        </div>
    </div>
</nav>
