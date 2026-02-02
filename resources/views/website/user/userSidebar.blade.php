<div class="w-full bg-white shadow-lg rounded-xl px-2 pt-8 pb-4 border border-gray-100">
    <div class="text-center pb-6 border-b border-gray-100 mb-4">
        <div class="relative inline-block">
            <img src="{{ auth()->user()->fullImage }}" class="w-24 h-24 rounded-full p-1 border-2 border-[#4A3AFF] object-cover m-auto shadow-md" alt="{{ auth()->user()->name }}">
        </div>
        <div class="mt-3 text-xl font-bold font-fira-sans text-gray-800">
            {{ auth()->user()->name }}
        </div>
        <p class="text-sm text-gray-500 mt-1">{{ __('Patient') }}</p>
    </div>

    <ul class="sidebar flex flex-col space-y-2">
      <li class="relative">
        <a href="{{ url('user_profile') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ $active == 'dashboard' ? 'shadow-md' : 'hover:bg-gray-50' }}"
           style="{{ $active == 'dashboard' ? 'background-color: #4A3AFF !important; color: #ffffff !important; text-decoration: none !important;' : 'color: #4b5563; text-decoration: none !important;' }}"
           @if($active != 'dashboard')
           onmouseover="this.style.color='#4A3AFF'; this.querySelector('i').style.color='#4A3AFF';" 
           onmouseout="this.style.color='#4b5563'; this.querySelector('i').style.color='#4A3AFF';"
           @endif
           >
            <i class="fas fa-th-large text-lg w-8" style="{{ $active == 'dashboard' ? 'color: #ffffff !important;' : 'color: #4A3AFF;' }}"></i>
            <span>{{ __('Dashboard') }}</span>
        </a>
      </li>

      <li class="relative">
        <a href="{{ url('test-report') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ $active == 'testReport' ? 'shadow-md' : 'hover:bg-gray-50' }}"
           style="{{ $active == 'testReport' ? 'background-color: #4A3AFF !important; color: #ffffff !important; text-decoration: none !important;' : 'color: #4b5563; text-decoration: none !important;' }}"
           @if($active != 'testReport')
           onmouseover="this.style.color='#4A3AFF'; this.querySelector('i').style.color='#4A3AFF';" 
           onmouseout="this.style.color='#4b5563'; this.querySelector('i').style.color='#4A3AFF';"
           @endif
           >
            <i class="fas fa-file-medical text-lg w-8" style="{{ $active == 'testReport' ? 'color: #ffffff !important;' : 'color: #4A3AFF;' }}"></i>
            <span>{{ __('Test Report') }}</span>
        </a>
      </li>

      <li class="relative">
        <a href="{{ url('patient-address') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ $active == 'patientAddress' ? 'shadow-md' : 'hover:bg-gray-50' }}"
           style="{{ $active == 'patientAddress' ? 'background-color: #4A3AFF !important; color: #ffffff !important; text-decoration: none !important;' : 'color: #4b5563; text-decoration: none !important;' }}"
           @if($active != 'patientAddress')
           onmouseover="this.style.color='#4A3AFF'; this.querySelector('i').style.color='#4A3AFF';" 
           onmouseout="this.style.color='#4b5563'; this.querySelector('i').style.color='#4A3AFF';"
           @endif
           >
            <i class="fas fa-map-marker-alt text-lg w-8" style="{{ $active == 'patientAddress' ? 'color: #ffffff !important;' : 'color: #4A3AFF;' }}"></i>
            <span>{{ __('Patient Address') }}</span>
        </a>
      </li>

      <li class="relative">
        <a href="{{ url('favorite') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ $active == 'favirote' ? 'shadow-md' : 'hover:bg-gray-50' }}"
           style="{{ $active == 'favirote' ? 'background-color: #4A3AFF !important; color: #ffffff !important; text-decoration: none !important;' : 'color: #4b5563; text-decoration: none !important;' }}"
           @if($active != 'favirote')
           onmouseover="this.style.color='#4A3AFF'; this.querySelector('i').style.color='#4A3AFF';" 
           onmouseout="this.style.color='#4b5563'; this.querySelector('i').style.color='#4A3AFF';"
           @endif
           >
            <i class="fas fa-heart text-lg w-8" style="{{ $active == 'favirote' ? 'color: #ffffff !important;' : 'color: #4A3AFF;' }}"></i>
            <span>{{ __('Favorite') }}</span>
        </a>
      </li>

      <li class="relative">
        <a href="{{ url('profile-setting') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ $active == 'profileSetting' ? 'shadow-md' : 'hover:bg-gray-50' }}"
           style="{{ $active == 'profileSetting' ? 'background-color: #4A3AFF !important; color: #ffffff !important; text-decoration: none !important;' : 'color: #4b5563; text-decoration: none !important;' }}"
           @if($active != 'profileSetting')
           onmouseover="this.style.color='#4A3AFF'; this.querySelector('i').style.color='#4A3AFF';" 
           onmouseout="this.style.color='#4b5563'; this.querySelector('i').style.color='#4A3AFF';"
           @endif
           >
            <i class="fas fa-cog text-lg w-8" style="{{ $active == 'profileSetting' ? 'color: #ffffff !important;' : 'color: #4A3AFF;' }}"></i>
            <span>{{ __('Profile Settings') }}</span>
        </a>
      </li>

      <li class="relative">
        <a href="{{ url('change-password') }}" 
           class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 {{ $active == 'changePassword' ? 'shadow-md' : 'hover:bg-gray-50' }}"
           style="{{ $active == 'changePassword' ? 'background-color: #4A3AFF !important; color: #ffffff !important; text-decoration: none !important;' : 'color: #4b5563; text-decoration: none !important;' }}"
           @if($active != 'changePassword')
           onmouseover="this.style.color='#4A3AFF'; this.querySelector('i').style.color='#4A3AFF';" 
           onmouseout="this.style.color='#4b5563'; this.querySelector('i').style.color='#4A3AFF';"
           @endif
           >
            <i class="fas fa-lock text-lg w-8" style="{{ $active == 'changePassword' ? 'color: #ffffff !important;' : 'color: #4A3AFF;' }}"></i>
            <span>{{ __('Change Password') }}</span>
        </a>
      </li>
    </ul>
</div>
