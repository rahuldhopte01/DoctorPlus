@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', $questionnaire->name . ' - ' . $currentSection->name)

@section('css')
<style>
    /* Force white text and primary background on selected radio buttons */
    input[type="radio"]:checked + label {
        color: #ffffff !important;
        background-color: var(--primary-color, #4A3AFF) !important;
        border-color: var(--primary-color, #4A3AFF) !important;
    }
    
    /* Custom Dropdown UI */
    .custom-dropdown-container.open .custom-select-options {
        opacity: 1 !important;
        pointer-events: auto !important;
        transform: translateY(0) scale(1) !important;
    }
    .custom-dropdown-container.open .dropdown-icon {
        transform: rotate(180deg);
    }
    .custom-dropdown-container.open .trigger-icon-bg {
        border-color: var(--primary-color, #4A3AFF);
        background-color: #f8f9fa;
    }
    .custom-select-trigger:focus {
        border-color: var(--primary-color, #4A3AFF);
        box-shadow: 0 0 0 4px rgba(74, 58, 255, 0.1);
    }
    .custom-select-options {
        transform: translateY(-10px) scale(0.98);
        transform-origin: top center;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-color, #4A3AFF) #f8f9fa;
    }
    .custom-select-options::-webkit-scrollbar {
        width: 6px;
    }
    .custom-select-options::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 8px;
    }
    .custom-select-options::-webkit-scrollbar-thumb {
        background-color: var(--primary-color, #4A3AFF);
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
{{-- Hero Banner Section --}}
<div class="relative w-full bg-bloomwell-dark flex items-center justify-center border-0" 
     style="min-height: 250px;">
    <div class="z-10 text-center px-4 w-full max-w-4xl mx-auto mt-4">
        <h1 class="text-3xl md:text-5xl font-bold text-white font-heading mb-4">{{ $questionnaire->name }}</h1>
        <nav class="flex justify-center" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-white opacity-90 font-body">
                <li><a href="{{ url('/') }}" class="text-white hover:text-primary transition-colors opacity-75 hover:opacity-100">{{ __('Home') }}</a></li>
                <li><span class="mx-2 text-white opacity-50">/</span></li>
                <li><a href="{{ route('categories') }}" class="text-white hover:text-primary transition-colors opacity-75 hover:opacity-100">{{ __('Categories') }}</a></li>
                <li><span class="mx-2 text-white opacity-50">/</span></li>
                <li><a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-white hover:text-primary transition-colors opacity-75 hover:opacity-100">{{ $category->name }}</a></li>
                <li><span class="mx-2 text-white opacity-50">/</span></li>
                <li class="text-white font-medium opacity-100">{{ __('Questionnaire') }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="w-full pb-16" style="background-color: #f2efea !important;">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-20">
    <!-- Questionnaire Card -->
    <div class="bg-white rounded-4 shadow-bloomwell border-0 overflow-hidden font-body">
        <div class="p-8 border-b border-gray-100">
             <div class="flex items-center gap-4 mb-2">
                 <div class="w-14 h-14 bg-purple-light rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clipboard-list text-2xl text-primary"></i>
                 </div>
                 <div>
                    <h2 class="text-2xl font-bold text-gray-900 font-heading mb-1">{{ $questionnaire->name }}</h2>
                    @if($treatment)
                    <p class="font-body text-gray-500 text-sm mb-0">{{ $treatment->name }}</p>
                    @endif
                 </div>
            </div>
            @if($questionnaire->description)
            <p class="text-muted mt-4 leading-relaxed font-body mb-0">{{ $questionnaire->description }}</p>
            @endif
        </div>

        <!-- Step Indicator (Issue 2: Section-wise navigation) -->
        <div class="p-6 bg-[#f8f9fa] border-b border-gray-100 sm:px-10">
            <div class="flex items-center justify-between mb-2">
                <span class="font-body text-sm font-medium text-gray-500 uppercase tracking-wider">{{ __('Section') }} {{ $sectionIndex + 1 }} {{ __('of') }} {{ $totalSections }}</span>
                <span class="font-body text-sm font-bold text-primary">{{ round((($sectionIndex + 1) / $totalSections) * 100) }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-primary h-2 rounded-full transition-all duration-500 ease-in-out" style="width: {{ (($sectionIndex + 1) / $totalSections) * 100 }}%"></div>
            </div>
        </div>

        <!-- Validation Errors Display Area -->
        <div id="validationErrorsContainer" class="hidden p-6 bg-red-50 border-b border-red-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-lg font-fira-sans font-medium text-red-800 mb-2">
                        {{ __('Please fix the following validation errors:') }}
                    </h3>
                    <ul id="validationErrorsList" class="list-disc list-inside space-y-2 text-sm font-fira-sans text-red-700">
                        <!-- Errors will be populated here by JavaScript -->
                    </ul>
                </div>
                <button type="button" id="closeErrorsBtn" class="ml-4 flex-shrink-0 text-red-400 hover:text-red-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <form id="sectionForm" method="POST" action="#" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <input type="hidden" name="questionnaire_id" value="{{ $questionnaire->id }}">
            <input type="hidden" name="section_index" value="{{ $sectionIndex }}" id="sectionIndex">

            <div class="p-6 sm:p-10">
                <!-- Current Section Header -->
                <div class="mb-8">
                    <h4 class="font-heading font-semibold text-xl text-gray-900 flex items-center mb-0">
                        <span class="flex items-center justify-center bg-primary text-white rounded-circle flex-shrink-0 w-10 h-10 text-base font-bold mr-3 shadow-sm">{{ $sectionIndex + 1 }}</span>
                        {{ $currentSection->name }}
                    </h4>
                    @if($currentSection->description)
                    <p class="font-body text-muted text-sm mt-3 ml-[3.25rem]">{{ $currentSection->description }}</p>
                    @endif
                </div>

                <!-- Section Questions -->
                @foreach($currentSection->questions as $question)
                <div class="question-wrapper mb-8 ml-[3.25rem]" data-question-id="{{ $question->id }}" 
                     @if($question->conditional_logic) data-conditional='@json($question->conditional_logic)' @endif>
                    <label class="block font-body font-semibold text-gray-800 mb-3 text-[1.05rem]">
                        {{ $question->question_text }}
                        @if($question->required)
                        <span class="text-danger ml-1">*</span>
                        @endif
                    </label>

                    @switch($question->field_type)
                        @case('text')
                            @php
                                $savedValue = isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : '';
                                $savedValue = is_array($savedValue) ? json_encode($savedValue) : (string) $savedValue;
                            @endphp
                            <input type="text" 
                                name="answers[{{ $question->id }}]" 
                                class="w-full px-4 py-3 bg-[#f8f9fa] border border-gray-200 rounded-3 focus:bg-white focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-200 font-body question-input placeholder-gray-400 text-gray-800"
                                data-question-id="{{ $question->id }}"
                                value="{{ $savedValue }}"
                                placeholder="{{ __('Type your answer here...') }}"
                                @if($question->required) required @endif
                                @if($question->validation_rules)
                                    @if(isset($question->validation_rules['min'])) minlength="{{ $question->validation_rules['min'] }}" @endif
                                    @if(isset($question->validation_rules['max'])) maxlength="{{ $question->validation_rules['max'] }}" @endif
                                @endif>
                            @break

                        @case('textarea')
                            @php
                                $savedValue = isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : '';
                                $savedValue = is_array($savedValue) ? json_encode($savedValue) : (string) $savedValue;
                            @endphp
                            <textarea 
                                name="answers[{{ $question->id }}]" 
                                class="w-full px-4 py-3 bg-[#f8f9fa] border border-gray-200 rounded-3 focus:bg-white focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-200 font-body question-input placeholder-gray-400 text-gray-800"
                                data-question-id="{{ $question->id }}"
                                rows="4"
                                placeholder="{{ __('Type your detailed answer here...') }}"
                                @if($question->required) required @endif>{{ $savedValue }}</textarea>
                            @break

                        @case('number')
                            @php
                                $savedValue = isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : '';
                                $savedValue = is_array($savedValue) ? '' : (string) $savedValue;
                            @endphp
                            <input type="number" 
                                name="answers[{{ $question->id }}]" 
                                class="w-full px-4 py-3 bg-[#f8f9fa] border border-gray-200 rounded-3 focus:bg-white focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-200 font-body question-input placeholder-gray-400 text-gray-800"
                                data-question-id="{{ $question->id }}"
                                value="{{ $savedValue }}"
                                placeholder="0"
                                @if($question->required) required @endif
                                @if($question->validation_rules)
                                    @if(isset($question->validation_rules['min'])) min="{{ $question->validation_rules['min'] }}" @endif
                                    @if(isset($question->validation_rules['max'])) max="{{ $question->validation_rules['max'] }}" @endif
                                @endif>
                            @break

                        @case('dropdown')
                            @php
                                $savedValue = isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : null;
                                $savedValue = is_array($savedValue) ? null : $savedValue;
                            @endphp
                                <div class="custom-dropdown-container relative" data-question-id="{{ $question->id }}">
                                    <!-- Hidden visually but focusable for HTML5 validation -->
                                    <select name="answers[{{ $question->id }}]" 
                                        class="question-input absolute w-0 h-0 opacity-0 pointer-events-none"
                                        style="left: 50%; top: 50%;"
                                        data-question-id="{{ $question->id }}"
                                        @if($question->required) required @endif>
                                        <option value="">{{ __('Select an option') }}</option>
                                        @foreach($question->options ?? [] as $option)
                                            <option value="{{ $option }}" 
                                                {{ $savedValue !== null && $savedValue == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    <div class="custom-select-trigger w-full px-4 py-3 bg-[#f8f9fa] border border-gray-200 rounded-3 focus-visible:outline-none focus-visible:border-primary focus-visible:ring-4 focus-visible:ring-primary/10 transition-all duration-300 font-body text-gray-800 flex justify-between items-center cursor-pointer hover:bg-white hover:border-primary shadow-sm group" tabindex="0">
                                        <span class="selected-text truncate font-medium align-middle {{ $savedValue !== null && $savedValue !== '' ? 'text-gray-900' : 'text-gray-500' }}">
                                            {{ $savedValue !== null && $savedValue !== '' ? $savedValue : __('Select an option') }}
                                        </span>
                                        <div class="w-8 h-8 rounded-circle bg-white flex items-center justify-center shadow-sm border border-gray-100 flex-shrink-0 transition-colors duration-300 trigger-icon-bg group-hover:border-primary/50">
                                            <i class="fas fa-chevron-down text-primary text-sm transition-transform duration-300 dropdown-icon"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="custom-select-options absolute w-full mt-2 bg-white border border-gray-100 rounded-4 shadow-bloomwell opacity-0 pointer-events-none transition-all duration-300 font-body overflow-hidden p-2" style="z-index: 1000; top: 100%; left: 0; max-height: 280px; overflow-y: auto;">
                                        <div class="option-item flex items-center justify-between px-4 py-2.5 mb-1 rounded-3 cursor-pointer transition-all duration-200 {{ $savedValue === null || $savedValue === '' ? 'bg-purple-light text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}" data-value="">
                                            <span>{{ __('Select an option') }}</span>
                                            <i class="fas fa-check text-primary {{ $savedValue === null || $savedValue === '' ? 'opacity-100' : 'opacity-0' }} transition-opacity"></i>
                                        </div>
                                        @foreach($question->options ?? [] as $option)
                                        <div class="option-item flex items-center justify-between px-4 py-2.5 mb-1 last:mb-0 rounded-3 cursor-pointer transition-all duration-200 {{ $savedValue !== null && $savedValue == $option ? 'bg-purple-light text-primary font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary' }}" data-value="{{ $option }}">
                                            <span>{{ $option }}</span>
                                            <i class="fas fa-check text-primary {{ $savedValue !== null && $savedValue == $option ? 'opacity-100' : 'opacity-0' }} transition-opacity"></i>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @break

                        @case('radio')
                            @php
                                $savedValue = isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : null;
                                $savedValue = is_array($savedValue) ? null : $savedValue;
                            @endphp
                            <div class="flex flex-wrap gap-4">
                                @foreach($question->options ?? [] as $optionIndex => $option)
                                <div class="relative">
                                    <input type="radio" 
                                        id="q{{ $question->id }}_opt{{ $optionIndex }}"
                                        name="answers[{{ $question->id }}]" 
                                        value="{{ $option }}"
                                        class="question-input peer sr-only"
                                        data-question-id="{{ $question->id }}"
                                        {{ $savedValue !== null && $savedValue == $option ? 'checked' : '' }}
                                        @if($question->required) required @endif>
                                <label for="q{{ $question->id }}_opt{{ $optionIndex }}" 
                                    class="flex items-center justify-center min-w-[120px] px-5 py-3 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-200 rounded-3 cursor-pointer hover:bg-purple-light hover:border-primary peer-checked:bg-primary peer-checked:!text-white peer-checked:border-primary peer-focus:ring-4 peer-focus:ring-primary/20 transition-all duration-300 font-body shadow-sm text-center">
                                    {{ $option }}
                                </label>
                                </div>
                                @endforeach
                            </div>
                            @break

                        @case('checkbox')
                            <div class="space-y-2">
                                @php
                                    $savedCheckboxes = isset($savedAnswers['answers'][$question->id]) 
                                        ? (is_array($savedAnswers['answers'][$question->id]) 
                                            ? $savedAnswers['answers'][$question->id] 
                                            : json_decode($savedAnswers['answers'][$question->id], true) ?? [])
                                        : [];
                                @endphp
                                @foreach($question->options ?? [] as $optionIndex => $option)
                                <label class="flex items-center justify-between p-4 bg-white border-2 border-gray-200 rounded-3 cursor-pointer transition-all duration-300 hover:border-primary hover:bg-purple-light group mb-3 shadow-sm hover-lift">
                                    <span class="font-body text-gray-800 font-medium group-hover:text-primary transition-colors">
                                        {{ $option }}
                                    </span>
                                    
                                    <div class="relative flex items-center">
                                        <input type="checkbox" 
                                            id="q{{ $question->id }}_opt{{ $optionIndex }}"
                                            name="answers[{{ $question->id }}][]" 
                                            value="{{ $option }}"
                                            class="w-6 h-6 text-primary bg-white border-2 border-gray-300 rounded focus:ring-primary focus:ring-offset-2 question-input cursor-pointer transition-colors"
                                            data-question-id="{{ $question->id }}"
                                            {{ in_array($option, $savedCheckboxes) ? 'checked' : '' }}>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @break

                        @case('file')
                            <!-- Issue 3: File upload field (supports birth certificate and other files) -->
                            <!-- Issue 3: File upload field (supports birth certificate and other files) -->
                            <div class="border-2 border-dashed border-gray-300 rounded-4 p-8 bg-[#f8f9fa] hover:bg-purple-light transition-all duration-300 text-center group cursor-pointer" onclick="document.getElementById('file{{ $question->id }}').click()">
                                <div class="mb-4">
                                    <div class="bg-white shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center border border-gray-100 group-hover:border-primary group-hover:shadow-md transition-all" style="width: 64px; height: 64px;">
                                        <i class="bi bi-cloud-upload text-3xl text-primary opacity-75 group-hover:opacity-100 transition-colors duration-200"></i>
                                    </div>
                                </div>
                                <input type="file" 
                                    name="files[{{ $question->id }}]" 
                                    class="question-input w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-pill file:border-0 file:text-sm file:font-medium file:bg-purple-light file:text-primary hover:file:bg-primary hover:file:text-white cursor-pointer transition-all duration-300"
                                    data-question-id="{{ $question->id }}"
                                    id="file{{ $question->id }}"
                                    @if($question->required) required @endif
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    data-max-size="5242880">
                                <p class="mt-3 text-xs text-muted font-body">
                                    {{ __('Allowed types: PDF, JPG, PNG. Max size: 5MB') }}
                                </p>
                                @if(isset($savedAnswers['answers'][$question->id]) && !empty($savedAnswers['answers'][$question->id]))
                                    @php
                                        $filePath = $savedAnswers['answers'][$question->id];
                                        $filePath = is_array($filePath) ? '' : (string) $filePath;
                                    @endphp
                                    @if(!empty($filePath))
                                    <p class="mt-2 text-sm text-green-600 font-fira-sans">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ __('File uploaded:') }} {{ basename($filePath) }}
                                    </p>
                                    @endif
                                @endif
                            </div>
                            @break
                    @endswitch

                    <div class="text-red-500 text-sm mt-1 font-fira-sans" id="error_{{ $question->id }}"></div>
                </div>
                @endforeach
            </div>

            <!-- Navigation Buttons (Issue 2: Section-wise navigation) -->
            <!-- Navigation Buttons (Issue 2: Section-wise navigation) -->
            <div class="bg-light border-t border-gray-200 px-6 py-4 flex justify-between items-center sticky bottom-0 z-10">
                <div>
                    @if($sectionIndex > 0)
                    <button type="button" id="prevBtn" class="btn btn-outline-secondary font-body font-medium px-5 py-2.5 rounded-pill shadow-sm hover-lift">
                        <i class="bi bi-arrow-left mr-2"></i>{{ __('Previous') }}
                    </button>
                    @else
                    <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="font-body text-gray-500 hover:text-primary transition-colors duration-200 no-underline hover:no-underline font-medium">
                        <i class="bi bi-arrow-left mr-2"></i>{{ __('Back') }}
                    </a>
                    @endif
                </div>
                <div>
                    @if($sectionIndex < $totalSections - 1)
                    <button type="button" id="nextBtn" class="btn btn-primary font-body font-medium px-5 py-2.5 rounded-pill shadow-sm hover-lift">
                        {{ __('Next Section') }}
                        <i class="bi bi-arrow-right ml-2"></i>
                    </button>
                    @else
                    <button type="button" id="submitBtn" class="btn btn-primary font-body font-medium px-5 py-2.5 rounded-pill shadow-sm hover-lift">
                        {{ __('Submit') }}
                        <i class="bi bi-check2 ml-2 text-lg"></i>
                    </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('sectionForm');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    const sectionIndex = parseInt(document.getElementById('sectionIndex').value);
    const categoryId = {{ $category->id }};
    const totalSections = {{ $totalSections }};
    const errorsContainer = document.getElementById('validationErrorsContainer');
    const errorsList = document.getElementById('validationErrorsList');
    const closeErrorsBtn = document.getElementById('closeErrorsBtn');

    // Close errors container
    if (closeErrorsBtn) {
        closeErrorsBtn.addEventListener('click', function() {
            errorsContainer.classList.add('hidden');
        });
    }
      // Check errors on load if any
    if (Object.keys(window.serverValidationErrors || {}).length > 0) {
        displayValidationErrors(window.serverValidationErrors, window.serverErrorDetails);
    }
    
    // Custom Dropdown Logic
    function initCustomDropdowns() {
        document.querySelectorAll('.custom-dropdown-container').forEach(container => {
            const trigger = container.querySelector('.custom-select-trigger');
            const optionsDiv = container.querySelector('.custom-select-options');
            const hiddenSelect = container.querySelector('select');
            const selectedText = container.querySelector('.selected-text');
            const optionItems = container.querySelectorAll('.option-item');

            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!container.contains(e.target)) {
                    container.classList.remove('open');
                }
            });

            // Toggle dropdown
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // Close all other dropdowns
                document.querySelectorAll('.custom-dropdown-container.open').forEach(other => {
                    if (other !== container) other.classList.remove('open');
                });
                container.classList.toggle('open');
            });
            
            // Allow keyboard activation
            trigger.addEventListener('keydown', function(e) {
                if(e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    trigger.click();
                }
            });

            // Handle hidden select value changes (e.g., from localStorage)
            hiddenSelect.addEventListener('change', function(e) {
                // If it's a programmatic change or our own dispatch, sync UI
                // Wait, if it's our own dispatch, we don't want an infinite loop. 
                // But we handle that by just updating UI state idempotently.
                const value = this.value;
                const opt = Array.from(optionItems).find(o => o.getAttribute('data-value') === value);
                
                if (opt) {
                    const text = opt.querySelector('span').textContent.trim();
                    selectedText.textContent = text;
                    if(value === '') {
                        selectedText.classList.remove('text-gray-900');
                        selectedText.classList.add('text-gray-500');
                    } else {
                        selectedText.classList.remove('text-gray-500');
                        selectedText.classList.add('text-gray-900');
                    }
                    optionItems.forEach(o => {
                        const optValue = o.getAttribute('data-value');
                        const checkIcon = o.querySelector('.fa-check');
                        if(optValue === value) {
                            o.className = 'option-item flex items-center justify-between px-4 py-2.5 mb-1 last:mb-0 rounded-3 cursor-pointer transition-all duration-200 bg-purple-light text-primary font-semibold';
                            if(checkIcon) {
                                checkIcon.classList.remove('opacity-0');
                                checkIcon.classList.add('opacity-100');
                            }
                        } else {
                            o.className = 'option-item flex items-center justify-between px-4 py-2.5 mb-1 last:mb-0 rounded-3 cursor-pointer transition-all duration-200 text-gray-700 hover:bg-gray-50 hover:text-primary';
                            if(checkIcon) {
                                checkIcon.classList.remove('opacity-100');
                                checkIcon.classList.add('opacity-0');
                            }
                        }
                    });
                }
            });

            // Handle user selection
            optionItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const value = this.getAttribute('data-value');
                    
                    if(hiddenSelect.value !== value) {
                        hiddenSelect.value = value;
                        hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    
                    // Close dropdown
                    container.classList.remove('open');
                });
            });
        });
    }

    initCustomDropdowns();

    // Section completion validationus errors
        errorsList.innerHTML = '';
        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
        document.querySelectorAll('[id^="error_"]').forEach(el => el.textContent = '');
        
        let currentSectionErrors = [];
        let otherSectionErrors = [];
        
        // Separate errors by section
        Object.keys(errors).forEach(questionId => {
            const errorDetail = errorDetails && errorDetails[questionId] ? errorDetails[questionId] : null;
            const errorMessage = errors[questionId];
            
            // Try to find question in current section
            const errorDiv = document.getElementById('error_' + questionId);
            const input = document.querySelector(`[data-question-id="${questionId}"] input, [data-question-id="${questionId}"] select, [data-question-id="${questionId}"] textarea`);
            
            if (errorDiv && input) {
                // Error is in current section - display inline
                errorDiv.textContent = errorMessage;
                errorDiv.style.color = '#ef4444';
                input.classList.add('border-red-500');
                currentSectionErrors.push({ questionId, errorMessage, errorDetail });
            } else if (errorDetail) {
                // Error is in another section - add to list
                otherSectionErrors.push({ questionId, errorMessage, errorDetail });
            } else {
                // Fallback if no detail available
                otherSectionErrors.push({ questionId, errorMessage, errorDetail: null });
            }
        });
        
        // Build error list HTML
        if (currentSectionErrors.length > 0 || otherSectionErrors.length > 0) {
            currentSectionErrors.forEach(({ questionId, errorMessage, errorDetail }) => {
                const li = document.createElement('li');
                li.innerHTML = `<strong>${errorDetail ? errorDetail.question_text : 'Question ' + questionId}:</strong> ${errorMessage}`;
                errorsList.appendChild(li);
            });
            
            otherSectionErrors.forEach(({ questionId, errorMessage, errorDetail }) => {
                const li = document.createElement('li');
                if (errorDetail) {
                    li.innerHTML = `<strong>Section "${errorDetail.section_name}" - ${errorDetail.question_text}:</strong> ${errorMessage}`;
                } else {
                    li.innerHTML = `<strong>Question ${questionId}:</strong> ${errorMessage}`;
                }
                errorsList.appendChild(li);
            });
            
            // Show errors container
            errorsContainer.classList.remove('hidden');
            
            // Scroll to errors container
            errorsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Scroll to first error in current section if any
            if (currentSectionErrors.length > 0) {
                const firstQuestionId = currentSectionErrors[0].questionId;
                const firstInput = document.querySelector(`[data-question-id="${firstQuestionId}"] input, [data-question-id="${firstQuestionId}"] select, [data-question-id="${firstQuestionId}"] textarea`);
                if (firstInput) {
                    setTimeout(() => {
                        firstInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInput.focus();
                    }, 300);
                }
            }
        }
    }

    // File size validation (Issue 3: File upload validation)
    document.querySelectorAll('input[type="file"]').forEach(fileInput => {
        fileInput.addEventListener('change', function() {
            const maxSize = parseInt(this.dataset.maxSize) || 5242880; // 5MB default
            if (this.files[0] && this.files[0].size > maxSize) {
                alert('{{ __("File size exceeds 5MB limit") }}');
                this.value = '';
            }
        });
    });

    // Next button handler (Issue 2: Section navigation)
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            saveAndNavigate('next');
        });
    }

    // Previous button handler
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            saveAndNavigate('previous');
        });
    }

    // Submit button handler (Issue 1: Correct POST endpoint)
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            submitQuestionnaire();
        });
    }

    // Save and navigate function
    function saveAndNavigate(action) {
        const formData = new FormData(form);
        formData.append('action', action);

        // Disable buttons and show loading state
        const activeButton = action === 'next' ? nextBtn : prevBtn;
        if (nextBtn) nextBtn.disabled = true;
        if (prevBtn) prevBtn.disabled = true;
        
        const originalButtonText = activeButton ? activeButton.innerHTML : '';
        if (activeButton) {
            activeButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __("Saving...") }}';
        }

        fetch('{{ url("/questionnaire/category/" . $category->id . "/save-section") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Re-enable buttons and restore text
            if (nextBtn) {
                nextBtn.disabled = false;
                nextBtn.innerHTML = '{{ __("Next Section") }} <i class="fas fa-arrow-right ml-2"></i>';
            }
            if (prevBtn) {
                prevBtn.disabled = false;
            }

            if (data.errors) {
                // Show validation errors
                Object.keys(data.errors).forEach(questionId => {
                    const input = document.querySelector(`[data-question-id="${questionId}"] .question-input, [data-question-id="${questionId}"] input, [data-question-id="${questionId}"] select, [data-question-id="${questionId}"] textarea`);
                    const errorDiv = document.getElementById('error_' + questionId);
                    if (input) input.classList.add('border-red-500');
                    if (errorDiv) errorDiv.textContent = data.errors[questionId];
                });
                return;
            }

            if (data.success) {
                // Navigate to next/previous section
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Re-enable buttons and restore text
            if (nextBtn) {
                nextBtn.disabled = false;
                nextBtn.innerHTML = '{{ __("Next Section") }} <i class="fas fa-arrow-right ml-2"></i>';
            }
            if (prevBtn) {
                prevBtn.disabled = false;
            }
            alert('{{ __("An error occurred. Please try again.") }}');
        });
    }

    // Submit questionnaire (Issue 1: Correct POST endpoint)
    function submitQuestionnaire() {
        // First save current section
        const formData = new FormData(form);
        formData.append('action', 'submit');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __("Submitting...") }}';

        // Save current section first
        fetch('{{ url("/questionnaire/category/" . $category->id . "/save-section") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.errors) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '{{ __("Submit Questionnaire") }} <i class="fas fa-check ml-2"></i>';
                Object.keys(data.errors).forEach(questionId => {
                    const input = document.querySelector(`[data-question-id="${questionId}"] .question-input, [data-question-id="${questionId}"] input`);
                    const errorDiv = document.getElementById('error_' + questionId);
                    if (input) input.classList.add('border-red-500');
                    if (errorDiv) errorDiv.textContent = data.errors[questionId];
                });
                return;
            }

            // Now submit final questionnaire - backend reads directly from session
            // No need to send answers in request body, backend will read from session
            fetch('{{ url("/questionnaire/category/" . $category->id . "/submit") }}', {
                method: 'POST',
                body: new FormData(), // Empty - answers are in session
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '{{ __("Submit Questionnaire") }} <i class="fas fa-check ml-2"></i>';

                if (data.blocked) {
                    alert(data.message);
                    return;
                }

                if (data.errors) {
                    // Display validation errors using the error container
                    displayValidationErrors(data.errors, data.error_details || {});
                    return;
                }

                if (data.success) {
                    // Redirect to success page
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = '{{ url("/questionnaire/category/" . $category->id . "/success") }}';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '{{ __("Submit Questionnaire") }} <i class="fas fa-check ml-2"></i>';
                alert('{{ __("An error occurred. Please try again.") }}');
            });
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '{{ __("Submit Questionnaire") }} <i class="fas fa-check ml-2"></i>';
            alert('{{ __("An error occurred. Please try again.") }}');
        });
    }
});
</script>
@endsection
