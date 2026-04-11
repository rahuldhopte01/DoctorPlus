@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', $questionnaire->name . ' - ' . $currentSection->name)

@section('css')
<style>
    /* Force white text and primary background on selected radio buttons */
    input[type="radio"]:checked + label {
        color: #ffffff !important;
        background-color: var(--primary-color, #4A3AFF) !important;
        border-color: var(--primary-color, #4A3AFF) !important;
        box-shadow: 0 4px 12px rgba(74, 58, 255, 0.15) !important;
    }
    
    /* Modern Progress Bar */
    .progress-container {
        position: relative;
        padding-top: 25px;
    }
    .progress-badge {
        position: absolute;
        top: 0;
        transform: translateX(-50%);
        background: var(--primary-color);
        color: white;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        transition: left 0.5s ease-in-out;
        z-index: 5;
    }
    .progress-badge::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 50%;
        transform: translateX(-50%);
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 5px solid var(--primary-color);
    }
    .premium-progress {
        height: 10px !important;
        background: #f1f5f9 !important;
        border-radius: 20px !important;
        overflow: visible !important;
    }
    .premium-progress-bar {
        background: var(--primary-color) !important;
        border-radius: 20px !important;
    }

    /* Custom Dropdown UI Enhancement */
    .custom-dropdown-container {
        position: relative;
        z-index: 10;
    }
    .custom-dropdown-container.open {
        z-index: 100;
    }
    .custom-dropdown-container.open .custom-select-options {
        opacity: 1 !important;
        pointer-events: auto !important;
        transform: translateY(0) scale(1) !important;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15) !important;
    }
    .custom-dropdown-container.open .dropdown-icon {
        transform: rotate(180deg);
    }
    .custom-dropdown-container.open .trigger-icon-bg {
        border-color: var(--primary-color, #4A3AFF);
        background-color: #f8f9fa;
    }
    .custom-select-trigger {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-select-trigger:hover {
        border-color: var(--primary-color);
        background-color: #fcfcff;
    }
    .custom-select-options {
        transform: translateY(-10px) scale(0.98);
        transform-origin: top center;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-color, #4A3AFF) #f8f9fa;
        border: 2px solid var(--purple-light) !important;
    }
    
    /* Modern Question Wrapper */
    .question-wrapper {
        padding: 1.5rem 2rem;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.5rem !important;
    }
    
    /* Modern Checkbox Style */
    .modern-check-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1.25rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .modern-check-label:hover {
        border-color: var(--primary-color);
        background: #f8fafc;
    }
    input[type="checkbox"]:checked + .modern-check-label {
        border-color: var(--primary-color);
        background: #f0edff; /* Clearly filled appearance */
    }
    input[type="checkbox"]:checked + .modern-check-label .check-icon-box {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    input[type="checkbox"]:checked + .modern-check-label .check-icon-box i {
        opacity: 1 !important;
    }
</style>
@endsection

@section('content')
<section class="relative w-full overflow-hidden" style="background: var(--primary-color); padding-top: 40px; padding-bottom: 60px;">
    
    <div class="relative z-10 px-4 w-full max-w-4xl mx-auto text-center">
        <h1 class="text-2xl md:text-3xl font-bold text-white font-heading mb-2">{{ $questionnaire->name }}</h1>
        <nav class="flex justify-center" aria-label="Breadcrumb">
            <ol class="flex items-center flex-wrap space-x-2 text-sm text-white font-body justify-center">
                <li><a href="{{ url('/') }}" class="text-white hover:text-white transition-colors opacity-80 hover:opacity-100">Startseite</a></li>
                <li><span class="mx-2 text-white opacity-50">/</span></li>
                <li><a href="{{ route('categories') }}" class="text-white hover:text-white transition-colors opacity-80 hover:opacity-100">Kategorien</a></li>
                <li><span class="mx-2 text-white opacity-50">/</span></li>
                <li><a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-white hover:text-white transition-colors opacity-80 hover:opacity-100">{{ $category->name }}</a></li>
                <li><span class="mx-2 text-white opacity-50">/</span></li>
                <li class="text-white font-semibold opacity-100">Fragebogen</li>
            </ol>
        </nav>
    </div>
</section>

<div class="py-12">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 -mt-12">
    <!-- Questionnaire Card -->
    <div class="bg-white font-body shadow-sm border border-gray-200" style="border-radius: 12px; overflow: visible; position: relative;">
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

        <!-- Modern Step Indicator -->
        <div class="mb-6 pb-6 border-b border-gray-100 px-6 sm:px-10">
            <div class="flex justify-between items-center mb-3">
                <span class="font-body text-xs font-bold text-gray-500 uppercase tracking-wider">Fortschritt des Fragebogens</span>
                <span class="font-heading text-lg font-bold text-primary">{{ round((($sectionIndex + 1) / $totalSections) * 100) }}%</span>
            </div>
            <div class="progress h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="bg-primary h-full rounded-full transition-all duration-700 ease-out" style="width: {{ (($sectionIndex + 1) / $totalSections) * 100 }}%"></div>
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
                        Bitte beheben Sie die folgenden Validierungsfehler:
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
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border-l-4 border-primary shadow-sm">
                    <div class="flex items-center">
                        <span class="flex items-center justify-center bg-primary text-white rounded-lg flex-shrink-0 w-10 h-10 text-base font-bold mr-4">{{ $sectionIndex + 1 }}</span>
                        <div>
                            <h4 class="font-heading font-bold text-xl text-gray-900 mb-0">{{ $currentSection->name }}</h4>
                            @if($currentSection->description)
                            <p class="font-body text-gray-500 text-xs mt-0.5 mb-0">{{ $currentSection->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Section Questions -->
                @foreach($currentSection->questions as $question)
                @php $savedSubAnswers = $savedAnswers['sub_answers'][$question->id] ?? []; @endphp
                <div class="question-wrapper mb-10" data-question-id="{{ $question->id }}"
                     data-behaviors='@json($question->option_behaviors ?? [])'>
                    <label class="block font-heading font-bold text-gray-900 mb-6 text-2xl leading-tight">
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
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all font-body question-input placeholder-gray-400 text-gray-800 text-base"
                                data-question-id="{{ $question->id }}"
                                value="{{ $savedValue }}"
                                placeholder="Geben Sie hier Ihre Antwort ein..."
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
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all font-body question-input placeholder-gray-400 text-gray-800 text-base"
                                data-question-id="{{ $question->id }}"
                                rows="4"
                                placeholder="Geben Sie hier Ihre detaillierte Antwort ein..."
                                @if($question->required) required @endif>{{ $savedValue }}</textarea>
                            @break

                        @case('number')
                            @php
                                $savedValue = isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : '';
                                $savedValue = is_array($savedValue) ? '' : (string) $savedValue;
                            @endphp
                            <input type="number" 
                                name="answers[{{ $question->id }}]" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all font-body question-input placeholder-gray-400 text-gray-800 text-base"
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
                                        <option value="">Option auswählen</option>
                                        @foreach($question->options ?? [] as $option)
                                            <option value="{{ $option }}" 
                                                {{ $savedValue !== null && $savedValue == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    <div class="custom-select-trigger w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus-visible:outline-none focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/10 transition-all font-body text-gray-800 flex justify-between items-center cursor-pointer hover:border-primary shadow-sm group text-base" tabindex="0">
                                        <span class="selected-text truncate font-medium align-middle {{ $savedValue !== null && $savedValue !== '' ? 'text-gray-900' : 'text-gray-500' }}">
                                            {{ $savedValue !== null && $savedValue !== '' ? $savedValue : 'Option auswählen' }}
                                        </span>
                                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm border border-gray-100 flex-shrink-0 transition-colors duration-300 trigger-icon-bg group-hover:border-primary/50">
                                            <i class="fas fa-chevron-down text-primary text-sm transition-transform duration-300 dropdown-icon"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="custom-select-options absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 pointer-events-none transition-all duration-300 font-body overflow-hidden p-1 text-base" style="z-index: 1000; top: 100%; left: 0; max-height: 250px; overflow-y: auto;">
                                        <div class="option-item flex items-center justify-between px-4 py-2 mb-1 rounded-md cursor-pointer transition-all duration-200 {{ $savedValue === null || $savedValue === '' ? 'bg-purple-light text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}" data-value="">
                                            <span>Option auswählen</span>
                                            <i class="fas fa-check text-primary {{ $savedValue === null || $savedValue === '' ? 'opacity-100' : 'opacity-0' }} transition-opacity"></i>
                                        </div>
                                        @foreach($question->options ?? [] as $option)
                                        <div class="option-item flex items-center justify-between px-4 py-2 mb-1 last:mb-0 rounded-md cursor-pointer transition-all duration-200 {{ $savedValue !== null && $savedValue == $option ? 'bg-purple-light text-primary font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary' }}" data-value="{{ $option }}">
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
                                        class="flex items-center justify-center min-w-[120px] px-8 py-4 text-base font-bold text-gray-700 bg-white border-2 border-gray-100 rounded-full cursor-pointer hover:-translate-y-1 hover:shadow-md hover:border-primary peer-checked:bg-primary peer-checked:!text-white peer-checked:border-primary peer-checked:shadow-lg peer-focus:ring-4 peer-focus:ring-primary/20 transition-all duration-300 font-body shadow-sm text-center">
                                        {{ $option }}
                                    </label>
                                    </div>
                                    @endforeach
                                </div>
                            @break

                        @case('checkbox')
                                <div class="space-y-4">
                                    @php
                                        $savedCheckboxes = isset($savedAnswers['answers'][$question->id]) 
                                            ? (is_array($savedAnswers['answers'][$question->id]) 
                                                ? $savedAnswers['answers'][$question->id] 
                                                : json_decode($savedAnswers['answers'][$question->id], true) ?? [])
                                            : [];
                                    @endphp
                                    @foreach($question->options ?? [] as $optionIndex => $option)
                                    <div class="relative">
                                        <input type="checkbox" 
                                            id="q{{ $question->id }}_opt{{ $optionIndex }}"
                                            name="answers[{{ $question->id }}][]" 
                                            value="{{ $option }}"
                                            class="question-input sr-only"
                                            data-question-id="{{ $question->id }}"
                                            {{ in_array($option, $savedCheckboxes) ? 'checked' : '' }}>
                                        <label for="q{{ $question->id }}_opt{{ $optionIndex }}" class="modern-check-label">
                                            <span class="font-body text-gray-800 font-semibold text-lg">
                                                {{ $option }}
                                            </span>
                                            <div class="w-7 h-7 rounded-full border-2 border-gray-200 flex items-center justify-center transition-all bg-white check-icon-box">
                                                <i class="bi bi-check-lg text-white text-base opacity-0 transition-opacity"></i>
                                            </div>
                                        </label>
                                    </div>
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
                                    Erlaubte Typen: PDF, JPG, PNG. Maximale Größe: 5MB
                                </p>
                                @if(isset($savedAnswers['answers'][$question->id]) && !empty($savedAnswers['answers'][$question->id]))
                                    @php
                                        $filePath = $savedAnswers['answers'][$question->id];
                                        $filePath = is_array($filePath) ? '' : (string) $filePath;
                                    @endphp
                                    @if(!empty($filePath))
                                    <p class="mt-2 text-sm text-green-600 font-fira-sans">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Datei hochgeladen: {{ basename($filePath) }}
                                    </p>
                                    @endif
                                @endif
                            </div>
                            @break
                    @endswitch

                    <div class="text-red-500 text-sm mt-1 font-fira-sans" id="error_{{ $question->id }}"></div>

                    <div class="behavior-flag-container mt-2" id="flags_{{ $question->id }}"></div>

                    <div class="sub-questions-container mt-2" data-question-id="{{ $question->id }}">
                        @foreach($question->option_behaviors['behaviors'] ?? [] as $behavior)
                            @if(!empty($behavior['sub_question']))
                                @include('website.questionnaire.partials.sub_question_render', [
                                    'subQuestion'    => $behavior['sub_question'],
                                    'parentPath'     => (string) $question->id,
                                    'depth'          => 1,
                                    'savedSubAnswers'=> $savedSubAnswers,
                                ])
                            @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="bg-light border-t border-gray-200 px-6 py-4 flex justify-between items-center sticky bottom-0 z-10" style="border-radius: 0 0 20px 20px;">
                <div>
                    @if($sectionIndex > 0)
                    <button type="button" id="prevBtn" class="btn border font-heading font-semibold px-4 py-2 text-sm shadow-sm transition-all" style="border-radius: 6px; background: white; color: var(--primary-color); border-color: var(--primary-color);">
                        <i class="bi bi-arrow-left mr-2"></i>Zurück
                    </button>
                    @else
                    <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="font-body text-gray-500 hover:text-primary transition-colors duration-200 no-underline hover:no-underline font-medium">
                        <i class="bi bi-arrow-left mr-2"></i>Zurück
                    </a>
                    @endif
                </div>
                <div>
                    @if($sectionIndex < $totalSections - 1)
                    <button type="button" id="nextBtn" class="btn font-heading font-semibold px-6 py-2 text-sm shadow-sm transition-all" style="border-radius: 6px; background: var(--primary-color); color: #fff;">
                        Weiter
                        <i class="bi bi-arrow-right ml-2"></i>
                    </button>
                    @else
                    <button type="button" id="submitBtn" class="btn font-heading font-semibold px-6 py-2 text-sm shadow-sm transition-all" style="border-radius: 6px; background: var(--primary-color); color: #fff;">
                        Mit Medikament
                    </button>
                    @endif
                </div>
            </div>
        </form>
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
    
    // Refined Custom Dropdown Logic
    function initCustomDropdowns() {
        document.querySelectorAll('.custom-dropdown-container').forEach(container => {
            const trigger = container.querySelector('.custom-select-trigger');
            const hiddenSelect = container.querySelector('select');
            const selectedText = container.querySelector('.selected-text');
            const optionItems = container.querySelectorAll('.option-item');

            if (!trigger || !hiddenSelect) return;

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

            // Handle user selection
            optionItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const value = this.getAttribute('data-value');
                    const text = this.querySelector('span').textContent.trim();
                    
                    hiddenSelect.value = value;
                    selectedText.textContent = text;
                    
                    if(value === '') {
                        selectedText.classList.remove('text-gray-900');
                        selectedText.classList.add('text-gray-500');
                    } else {
                        selectedText.classList.remove('text-gray-500');
                        selectedText.classList.add('text-gray-900');
                    }

                    // Update option active states and icons
                    optionItems.forEach(o => {
                        const checkIcon = o.querySelector('.fa-check');
                        if(o === item) {
                            o.classList.add('bg-purple-light', 'text-primary', 'font-semibold');
                            if(checkIcon) {
                                checkIcon.classList.remove('opacity-0');
                                checkIcon.classList.add('opacity-100');
                            }
                        } else {
                            o.classList.remove('bg-purple-light', 'text-primary', 'font-semibold');
                            if(checkIcon) {
                                checkIcon.classList.remove('opacity-100');
                                checkIcon.classList.add('opacity-0');
                            }
                        }
                    });
                    
                    container.classList.remove('open');
                    hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });
        });

        // Global click listener to close dropdowns
        document.addEventListener('click', function() {
            document.querySelectorAll('.custom-dropdown-container.open').forEach(openContainer => {
                openContainer.classList.remove('open');
            });
        });
    }

    initCustomDropdowns();

    // ── Behavior Engine (same logic as category_form) ─────────────────────────
    function evaluateCondition(value, condition) {
        const op  = condition.operator || 'equals';
        const tgt = condition.value;
        switch (op) {
            case 'equals':       return value == tgt;
            case 'not_equals':   return value != tgt;
            case 'contains':     return String(value).toLowerCase().includes(String(tgt).toLowerCase());
            case 'greater_than': return parseFloat(value) > parseFloat(tgt);
            case 'less_than':    return parseFloat(value) < parseFloat(tgt);
            default:             return false;
        }
    }

    function getQuestionValue(wrapper) {
        let value = '';
        // Use broad selector — sub-question inputs use 'sub-question-input' class, not 'question-input'
        wrapper.querySelectorAll('.question-input').forEach(inp => {
            if (inp.type === 'radio' && inp.checked)       value = inp.value;
            else if (inp.type !== 'radio' && inp.type !== 'checkbox' && inp.type !== 'file') value = inp.value;
        });
        return value;
    }

    function getSubQuestionValue(sqWrapper) {
        let value = '';
        sqWrapper.querySelectorAll(':scope > input.sub-question-input, :scope > select.sub-question-input, :scope > textarea.sub-question-input').forEach(inp => {
            if (inp.type === 'radio' && inp.checked) value = inp.value;
            else if (inp.type !== 'radio' && inp.type !== 'checkbox') value = inp.value;
        });
        return value;
    }

    function applyBehaviors(questionValue, behaviors, subQContainer, flagContainer) {
        // Clear existing flags
        if (flagContainer) flagContainer.innerHTML = '';

        if (!behaviors || !behaviors.length) return;

        if (subQContainer) {
            subQContainer.querySelectorAll(':scope > .sub-question-wrapper').forEach(sq => {
                sq.classList.add('hidden');
                sq.querySelectorAll('input, select, textarea').forEach(i => i.disabled = true);
            });
        }

        const match = behaviors.find(b => evaluateCondition(questionValue, b.condition || {}));
        if (!match) return;

        // Show flags for the matched behavior
        if (flagContainer && match.flags && match.flags.length) {
            match.flags.forEach(flag => {
                const div = document.createElement('div');
                if (flag.flag_type === 'hard') {
                    div.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 font-body flex items-start gap-2';
                    div.innerHTML = '<i class="fas fa-ban mt-0.5 flex-shrink-0"></i><span>' + flag.flag_message + '</span>';
                } else {
                    div.className = 'mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700 font-body flex items-start gap-2';
                    div.innerHTML = '<i class="fas fa-exclamation-triangle mt-0.5 flex-shrink-0"></i><span>' + flag.flag_message + '</span>';
                }
                flagContainer.appendChild(div);
            });
        }

        // Show sub-question for the matched behavior
        if (match.sub_question && subQContainer) {
            const sq = subQContainer.querySelector(`:scope > .sub-question-wrapper[data-temp-id="${match.sub_question.temp_id}"]`);
            if (sq) {
                sq.classList.remove('hidden');
                sq.querySelectorAll('input, select, textarea').forEach(i => i.disabled = false);
                const subVal = getSubQuestionValue(sq);
                const nested = sq.querySelector('.nested-sub-questions-container');
                applyBehaviors(subVal, match.sub_question.behaviors || [], nested);
            }
        }
    }

    function handleBehaviors() {
        document.querySelectorAll('.question-wrapper').forEach(wrapper => {
            try {
                const raw = wrapper.dataset.behaviors;
                if (!raw || raw === '[]' || raw === 'null') return;
                const behaviors = JSON.parse(raw);
                if (!behaviors || !behaviors.behaviors) return;
                const qId = wrapper.dataset.questionId;
                applyBehaviors(
                    getQuestionValue(wrapper),
                    behaviors.behaviors,
                    wrapper.querySelector('.sub-questions-container'),
                    document.getElementById('flags_' + qId)
                );
            } catch(e) {}
        });
    }

    function collectSubAnswersFromContainer(container) {
        if (!container) return [];
        const results = [];
        container.querySelectorAll(':scope > .sub-question-wrapper:not(.hidden)').forEach(sq => {
            let value = '';
            sq.querySelectorAll(':scope > input.sub-question-input:not(:disabled), :scope > select.sub-question-input:not(:disabled), :scope > textarea.sub-question-input:not(:disabled)').forEach(inp => {
                if (inp.type === 'radio' && inp.checked) value = inp.value;
                else if (inp.type !== 'radio' && inp.type !== 'checkbox') value = inp.value;
            });
            results.push({ temp_id: sq.dataset.tempId, value, sub_answers: collectSubAnswersFromContainer(sq.querySelector('.nested-sub-questions-container')) });
        });
        return results;
    }

    function appendSubAnswersToFormData(fd) {
        document.querySelectorAll('.question-wrapper').forEach(wrapper => {
            const qId = wrapper.dataset.questionId;
            const subs = collectSubAnswersFromContainer(wrapper.querySelector('.sub-questions-container'));
            if (subs && subs.length) fd.append('sub_answers_json[' + qId + ']', JSON.stringify(subs));
        });
    }

    document.querySelectorAll('.question-input, .sub-question-input').forEach(input => {
        input.addEventListener('change', function() { handleBehaviors(); });
        input.addEventListener('input',  function() { handleBehaviors(); });
    });

    handleBehaviors();

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
                alert('Dateigröße überschreitet das Limit von 5MB');
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
        appendSubAnswersToFormData(formData);

        // Disable buttons and show loading state
        const activeButton = action === 'next' ? nextBtn : prevBtn;
        if (nextBtn) nextBtn.disabled = true;
        if (prevBtn) prevBtn.disabled = true;
        
        const originalButtonText = activeButton ? activeButton.innerHTML : '';
        if (activeButton) {
            activeButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Speichern...';
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
                nextBtn.innerHTML = 'Nächster Abschnitt <i class="fas fa-arrow-right ml-2"></i>';
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
                nextBtn.innerHTML = 'Nächster Abschnitt <i class="fas fa-arrow-right ml-2"></i>';
            }
            if (prevBtn) {
                prevBtn.disabled = false;
            }
            alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
        });
    }

    // Submit questionnaire (Issue 1: Correct POST endpoint)
    function submitQuestionnaire() {
        // First save current section
        const formData = new FormData(form);
        formData.append('action', 'submit');
        appendSubAnswersToFormData(formData);

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Wird übermittelt...';

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
                submitBtn.innerHTML = 'Fragebogen abschicken <i class="fas fa-check ml-2"></i>';
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
                submitBtn.innerHTML = 'Fragebogen abschicken <i class="fas fa-check ml-2"></i>';

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
                submitBtn.innerHTML = 'Fragebogen abschicken <i class="fas fa-check ml-2"></i>';
                alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
            });
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Fragebogen abschicken <i class="fas fa-check ml-2"></i>';
            alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
        });
    }
});
</script>
@endsection
