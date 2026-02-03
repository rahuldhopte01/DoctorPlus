<nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-20">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/') }}">
                    @if($setting->company_logo && file_exists(public_path('images/upload/'.$setting->company_logo)))
                        <img src="{{$setting->logo}}" class="h-8 w-auto" alt="Logo" />
                    @else
                        <img src="{{url('/images/upload_empty/fuxxlogo.png')}}" class="h-8 w-auto" alt="Logo" />
                    @endif
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex space-x-8 items-center ml-auto mr-8">
                <a href="#" class="text-gray-600 hover:text-primary font-fira-sans font-medium text-sm transition-colors no-underline hover:no-underline">{{ __('Treatments') }}</a>
                <a href="#" class="text-gray-600 hover:text-primary font-fira-sans font-medium text-sm transition-colors no-underline hover:no-underline">{{ __('How it works') }}</a>
                <a href="#" class="text-gray-600 hover:text-primary font-fira-sans font-medium text-sm transition-colors no-underline hover:no-underline">{{ __('About us') }}</a>
                <a href="#" class="text-gray-600 hover:text-primary font-fira-sans font-medium text-sm transition-colors no-underline hover:no-underline">{{ __('Help') }}</a>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                
                @if (auth()->check())
                    <!-- User Dropdown -->
                    <div class="relative ml-3">
                        <div>
                            <button type="button" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary items-center gap-2" id="user-menu-button" aria-expanded="false" aria-haspopup="true" onclick="document.getElementById('user-dropdown').classList.toggle('hidden')">
                                <span class="sr-only">Open user menu</span>
                                <div class="flex flex-col text-right hidden sm:block mr-1">
                                    <span class="text-sm font-medium text-gray-700 font-fira-sans">{{ auth()->user()->name }}</span>
                                </div>
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ url('images/upload/'.auth()->user()->image) }}" alt="">
                            </button>
                        </div>
                        <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="user-dropdown">
                            <a href="{{ url('user_profile') }}" class="block px-4 py-2 text-sm text-gray-700 font-fira-sans hover:bg-gray-100 no-underline hover:no-underline" role="menuitem">{{ __('Dashboard') }}</a>
                            <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-gray-700 font-fira-sans hover:bg-gray-100 no-underline hover:no-underline" role="menuitem">{{ __('Sign out') }}</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Login Link -->
                    <a href="{{ url('/patient-login') }}" class="text-gray-600 hover:text-primary font-fira-sans font-medium text-sm hidden sm:block no-underline hover:no-underline">{{ __('Log in') }}</a>
                @endif

                <!-- Start Treatment Button -->
                <a href="{{ url('/') }}" class="bg-cta hover:bg-orange-600 text-white font-fira-sans font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors shadow-sm no-underline hover:no-underline">
                    {{ __('Start treatment') }}
                </a>

                <!-- Mobile menu button -->
                <div class="-mr-2 flex md:hidden">
                    <button type="button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" aria-controls="mobile-menu" aria-expanded="false" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="hidden md:hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-primary hover:text-primary font-fira-sans no-underline hover:no-underline">{{ __('Treatments') }}</a>
            <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-primary hover:text-primary font-fira-sans no-underline hover:no-underline">{{ __('How it works') }}</a>
            <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-primary hover:text-primary font-fira-sans no-underline hover:no-underline">{{ __('About us') }}</a>
            <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-primary hover:text-primary font-fira-sans no-underline hover:no-underline">{{ __('Help') }}</a>
            @if (!auth()->check())
                <a href="{{ url('/patient-login') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-primary hover:text-primary font-fira-sans no-underline hover:no-underline">{{ __('Log in') }}</a>
            @endif
        </div>
    </div>
</nav>
