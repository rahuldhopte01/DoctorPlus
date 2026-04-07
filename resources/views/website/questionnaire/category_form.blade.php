@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', $questionnaire->name)

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
                <li><a href="{{ url('/') }}" class="text-white hover:text-white transition-colors opacity-80 hover:opacity-100">{{ __('Home') }}</a></li>
                <li><span class="mx-2 text-white opacity-50">/</span></li>
                <li><a href="{{ route('categories') }}" class="text-white hover:text-white transition-colors opacity-80 hover:opacity-100">{{ __('Categories') }}</a></li>
                <li><span class="mx-2 text-white opacity-50">/</span></li>
                <li><a href="{{ route('category.detail', ['id' => $category->id]) }}" class="text-white hover:text-white transition-colors opacity-80 hover:opacity-100">{{ $category->name }}</a></li>
                <li><span class="mx-2 text-white opacity-50">/</span></li>
                <li class="text-white font-semibold opacity-100">{{ __('Questionnaire') }}</li>
            </ol>
        </nav>
    </div>
</section>

<div class="py-12">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 -mt-16">
    <!-- Questionnaire Card -->
    <div class="bg-white font-body shadow-sm border border-gray-200" style="border-radius: 12px; overflow: visible; position: relative;">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50" style="border-radius: 12px 12px 0 0;">
             <div class="flex items-center gap-4">
                 <div class="w-12 h-12 bg-white border border-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class="fas fa-clipboard-list text-xl text-primary"></i>
                 </div>
                 <div>
                    <h2 class="text-xl font-bold text-gray-900 font-heading mb-0">{{ $questionnaire->name }}</h2>
                    @if($treatment)
                    <p class="font-body text-gray-500 text-xs mt-0.5 mb-0">{{ $treatment->name }}</p>
                    @endif
                 </div>
            </div>
        </div>

        <form id="questionnaireForm" method="POST" action="#" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <input type="hidden" name="questionnaire_id" value="{{ $questionnaire->id }}">
            <input type="hidden" name="submission_flow" id="submissionFlow" value="with_medicine">

            <div class="p-6 sm:p-10">
                <!-- Modern Progress Indicator -->
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <div class="flex justify-between items-center mb-3">
                        <span class="font-body text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Questionnaire Progress') }}</span>
                        <span class="font-heading text-lg font-bold text-primary" id="progressText">0%</span>
                    </div>
                    <div class="progress h-2.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="bg-primary h-full rounded-full transition-all duration-700 ease-out" id="progressBar" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Auto-save indicator -->
                <div id="saveIndicator" class="hidden mb-5 p-3 bg-purple-light border border-primary/20 rounded-3">
                    <div class="flex items-center text-primary">
                        <i class="fas fa-save mr-2"></i>
                        <span class="font-body text-sm fw-medium" id="saveIndicatorText">{{ __('Saving...') }}</span>
                    </div>
                </div>

                <!-- Sections -->
                @foreach($questionnaire->sections as $sectionIndex => $section)
                <div class="questionnaire-section mb-10" data-section="{{ $sectionIndex }}">
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border-l-4 border-primary shadow-sm">
                        <div class="flex items-center">
                            <span class="flex items-center justify-center bg-primary text-white rounded-lg flex-shrink-0 w-10 h-10 text-base font-bold mr-4">{{ $sectionIndex + 1 }}</span>
                            <div>
                                <h4 class="font-heading font-bold text-xl text-gray-900 mb-0">{{ $section->name }}</h4>
                                @if($section->description)
                                <p class="font-body text-gray-500 text-xs mt-0.5 mb-0">{{ $section->description }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @foreach($section->questions as $question)
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
                                    $savedValue = is_array($savedValue) ? '' : (string) $savedValue;
                                @endphp
                                <input type="text" 
                                    name="answers[{{ $question->id }}]" 
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all font-body question-input placeholder-gray-400 text-gray-800 text-base"
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
                                    $savedValue = is_array($savedValue) ? '' : (string) $savedValue;
                                @endphp
                                <textarea 
                                    name="answers[{{ $question->id }}]" 
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all font-body question-input placeholder-gray-400 text-gray-800 text-base"
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
                                        <option value="">{{ __('Select an option') }}</option>
                                        @foreach($question->options ?? [] as $option)
                                            <option value="{{ $option }}" 
                                                {{ $savedValue !== null && $savedValue == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    <div class="custom-select-trigger w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus-visible:outline-none focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/10 transition-all font-body text-gray-800 flex justify-between items-center cursor-pointer hover:border-primary shadow-sm group text-base" tabindex="0">
                                        <span class="selected-text truncate font-medium align-middle {{ $savedValue !== null && $savedValue !== '' ? 'text-gray-900' : 'text-gray-500' }}">
                                            {{ $savedValue !== null && $savedValue !== '' ? $savedValue : __('Select an option') }}
                                        </span>
                                        <div class="w-8 h-8 rounded-circle bg-white flex items-center justify-center shadow-sm border border-gray-100 flex-shrink-0 transition-colors duration-300 trigger-icon-bg group-hover:border-primary/50">
                                            <i class="fas fa-chevron-down text-primary text-sm transition-transform duration-300 dropdown-icon"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="custom-select-options absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 pointer-events-none transition-all duration-300 font-body overflow-hidden p-1 text-base" style="z-index: 1000; top: 100%; left: 0; max-height: 250px; overflow-y: auto;">
                                        <div class="option-item flex items-center justify-between px-4 py-2 mb-1 rounded-md cursor-pointer transition-all duration-200 {{ $savedValue === null || $savedValue === '' ? 'bg-purple-light text-primary font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}" data-value="">
                                            <span>{{ __('Select an option') }}</span>
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
                                <div class="flex flex-wrap gap-2">
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
                                        class="flex items-center justify-center min-w-[80px] px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-primary peer-checked:bg-primary peer-checked:!text-white peer-checked:border-primary transition-all font-body text-center">
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
                                @php
                                    $savedFilePath = isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : '';
                                    $savedFilePath = is_array($savedFilePath) ? '' : (string) $savedFilePath;
                                @endphp
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
                                        @if($question->validation_rules && isset($question->validation_rules['file_types']))
                                            accept=".{{ implode(',.', $question->validation_rules['file_types']) }}"
                                        @endif>
                                    @if($question->validation_rules && isset($question->validation_rules['file_types']))
                                        <p class="mt-3 text-xs text-muted font-body">
                                            {{ __('Allowed types:') }} <span class="font-medium text-gray-700">{{ implode(', ', $question->validation_rules['file_types']) }}</span>
                                        </p>
                                    @endif
                                </div>
                                @break
                        @endswitch

                        <div class="text-red-500 text-sm mt-1 font-fira-sans" id="error_{{ $question->id }}"></div>

                        {{-- Sub-questions container: all possible sub-questions pre-rendered as hidden --}}
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
                @endforeach

                <!-- Blocked Message -->
                <div id="blockedMessage" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center text-red-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span id="blockedText" class="font-fira-sans"></span>
                    </div>
                </div>

                <!-- Submission Status Message -->
                <div id="submissionStatusMessage" class="hidden mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span id="submissionStatusText" class="font-fira-sans"></span>
                    </div>
                </div>

                @if(isset($submissionCheck) && !$submissionCheck['can_submit'])
                <!-- Server-side submission status check -->
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="font-fira-sans">{{ $submissionCheck['message'] }}</span>
                    </div>
                </div>
                @endif

                <!-- Warning Flags -->
                <div id="warningFlags" class="hidden mb-5 p-4 bg-orange-light border border-warning/30 rounded-3">
                    <h5 class="font-heading font-medium text-warning mb-2 d-flex align-items-center">
                        <i class="bi bi-exclamation-circle-fill mr-2"></i> {{ __('Please note:') }}
                    </h5>
                    <ul id="warningList" class="list-disc list-inside text-muted font-body mb-0"></ul>
                </div>
            </div>

            <div class="bg-light border-t border-gray-200 px-6 py-4 flex justify-between items-center sticky bottom-0 z-10" style="border-radius: 0 0 20px 20px;">
                <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="font-body text-gray-500 hover:text-primary transition-colors duration-200 no-underline hover:no-underline font-medium">
                    <i class="bi bi-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
                <div class="flex items-center gap-3">
                    @if(empty($category->is_cannaleo_only))
                    <button type="submit" class="btn border font-heading font-semibold px-4 py-2 text-sm shadow-sm transition-all" id="submitPrescriptionBtn" data-submission-flow="prescription_only"
                        style="border-radius: 6px; background: white; color: var(--primary-color); border-color: var(--primary-color);"
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit']) disabled @endif>
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit'])
                            {{ __('Under Review') }}
                        @else
                            {{ __('Prescription Only') }}
                        @endif
                    </button>
                    @endif
                    <button type="submit" class="btn font-heading font-semibold px-4 py-2 text-sm shadow-sm transition-all" id="submitWithMedicineBtn" data-submission-flow="with_medicine"
                        style="border-radius: 6px; background: var(--primary-color); color: #fff;"
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit']) disabled @endif>
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit'])
                            {{ __('Under Review') }}
                        @else
                            {{ __('With Medicine') }}
                        @endif
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('questionnaireForm');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const submitButtons = document.querySelectorAll('[data-submission-flow]');
    const submissionFlowInput = document.getElementById('submissionFlow');
    const blockedMessage = document.getElementById('blockedMessage');
    const warningFlags = document.getElementById('warningFlags');
    const warningList = document.getElementById('warningList');
    const saveIndicator = document.getElementById('saveIndicator');
    const saveIndicatorText = document.getElementById('saveIndicatorText');
    const submissionStatusMessage = document.getElementById('submissionStatusMessage');
    const submissionStatusText = document.getElementById('submissionStatusText');
    const categoryId = {{ $category->id }};
    
    let saveTimeout;
    const SAVE_DELAY = 2000; // Auto-save after 2 seconds of no typing

    let activeSubmitBtn = null;
    let activeSubmitBtnHtml = '';

    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            activeSubmitBtn = this;
            activeSubmitBtnHtml = this.innerHTML;
            submissionFlowInput.value = this.dataset.submissionFlow || 'with_medicine';
        });
    });

    // Check submission status on page load
    function checkSubmissionStatus() {
        fetch('{{ route("questionnaire.check-status", ["categoryId" => $category->id]) }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && !data.can_submit) {
                // Patient has an active submission - disable submit button
                submitButtons.forEach(button => {
                    button.disabled = true;
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                    button.innerHTML = '{{ __("Questionnaire Under Review") }} <i class="fas fa-lock ml-2"></i>';
                });
                
                // Show status message
                submissionStatusMessage.classList.remove('hidden');
                submissionStatusText.textContent = data.message || 'Your questionnaire for this category is currently under review.';
                
                // Disable all form inputs
                const inputs = form.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.disabled = true;
                    input.classList.add('opacity-50', 'cursor-not-allowed');
                });
            }
        })
        .catch(error => {
            console.error('Error checking submission status:', error);
            // Don't block submission if check fails - let backend handle it
        });
    }

    // Check status on page load
    checkSubmissionStatus();

    // Load saved answers from localStorage on page load (client-side persistence)
    function loadFromLocalStorage() {
        const saved = localStorage.getItem('questionnaire_answers_' + categoryId);
        if (saved) {
            try {
                const answers = JSON.parse(saved);
                // Restore answers to form fields
                Object.keys(answers).forEach(questionId => {
                    const input = document.querySelector(`[name="answers[${questionId}]"], [name="answers[${questionId}][]"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            // Handle checkboxes
                            document.querySelectorAll(`[name="answers[${questionId}][]"]`).forEach(cb => {
                                if (answers[questionId].includes(cb.value)) {
                                    cb.checked = true;
                                }
                            });
                        } else if (input.type === 'radio') {
                            // Handle radios
                            const radio = document.querySelector(`[name="answers[${questionId}"][value="${answers[questionId]}"]`);
                            if (radio) radio.checked = true;
                            input.value = answers[questionId];
                            if(input.tagName.toLowerCase() === 'select') {
                                input.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        }
                    }
                });
                updateProgress();
            } catch (e) {
                console.error('Error loading from localStorage:', e);
            }
        }
    }

    // Save to localStorage (client-side persistence)
    function saveToLocalStorage() {
        const formData = new FormData(form);
        const answers = {};
        
        // Collect answers
        document.querySelectorAll('.question-input').forEach(input => {
            if (input.type === 'checkbox') {
                const name = input.name.match(/\[(\d+)\]/)[1];
                if (!answers[name]) answers[name] = [];
                if (input.checked) answers[name].push(input.value);
            } else if (input.type === 'radio') {
                if (input.checked) {
                    const name = input.name.match(/\[(\d+)\]/)[1];
                    answers[name] = input.value;
                }
            } else if (input.type !== 'file' && input.value) {
                const name = input.name.match(/\[(\d+)\]/)[1];
                answers[name] = input.value;
            }
        });
        
        localStorage.setItem('questionnaire_answers_' + categoryId, JSON.stringify(answers));
    }

    // Auto-save to server (progressive saving)
    function autoSave() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(function() {
            const formData = new FormData(form);
            appendSubAnswersToFormData(formData);

            saveIndicator.classList.remove('hidden');
            saveIndicatorText.textContent = '{{ __("Saving...") }}';
            
            fetch('{{ route("questionnaire.save", ["categoryId" => $category->id]) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    saveIndicatorText.textContent = '{{ __("Saved") }}';
                    setTimeout(() => saveIndicator.classList.add('hidden'), 2000);
                } else {
                    saveIndicatorText.textContent = '{{ __("Save failed") }}';
                }
            })
            .catch(error => {
                console.error('Auto-save error:', error);
                saveIndicatorText.textContent = '{{ __("Save failed") }}';
            });
        }, SAVE_DELAY);
    }

    // Modernized Update progress bar
    function updateProgress() {
        const questions = document.querySelectorAll('.question-wrapper:not(.hidden)');
        let answeredCount = 0;
        
        questions.forEach(q => {
            const inputs = q.querySelectorAll('.question-input');
            let hasAnswer = false;
            
            inputs.forEach(input => {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    if (input.checked) hasAnswer = true;
                } else if (input.type === 'file') {
                    if (input.files.length > 0) hasAnswer = true;
                } else if (input.value && input.value.trim() !== '') {
                    hasAnswer = true;
                }
            });
            
            if (hasAnswer) answeredCount++;
        });

        const progress = questions.length > 0 ? Math.round((answeredCount / questions.length) * 100) : 0;
        const clampedProgress = Math.min(100, Math.max(0, progress));
        
        if (progressBar) progressBar.style.width = clampedProgress + '%';
        if (progressText) progressText.textContent = clampedProgress + '%';
        
        const badge = document.getElementById('progressBadge');
        if (badge) {
            badge.style.left = clampedProgress + '%';
            badge.textContent = clampedProgress + '%';
        }
    }

    // ── Behavior Engine ───────────────────────────────────────────────────────
    function evaluateCondition(value, condition) {
        const operator = condition.operator || 'equals';
        const target   = condition.value;
        switch (operator) {
            case 'equals':       return value == target;
            case 'not_equals':   return value != target;
            case 'contains':     return String(value).toLowerCase().includes(String(target).toLowerCase());
            case 'greater_than': return parseFloat(value) > parseFloat(target);
            case 'less_than':    return parseFloat(value) < parseFloat(target);
            default:             return false;
        }
    }

    function getQuestionValue(wrapper) {
        const inputs = wrapper.querySelectorAll(':scope > .question-input, :scope > div > .question-input, :scope > .custom-dropdown-container > select');
        let value = '';
        inputs.forEach(inp => {
            if (inp.type === 'radio' && inp.checked)       value = inp.value;
            else if (inp.type === 'checkbox' && inp.checked) value = inp.value; // last checked wins for single-val check
            else if (inp.type !== 'radio' && inp.type !== 'checkbox' && inp.type !== 'file') value = inp.value;
        });
        return value;
    }

    function getSubQuestionValue(sqWrapper) {
        let value = '';
        const inputs = sqWrapper.querySelectorAll(':scope > input.sub-question-input, :scope > select.sub-question-input, :scope > textarea.sub-question-input');
        inputs.forEach(inp => {
            if (inp.type === 'radio' && inp.checked) value = inp.value;
            else if (inp.type !== 'radio' && inp.type !== 'checkbox') value = inp.value;
        });
        return value;
    }

    // Active soft-flag messages (rebuilt on every behavior pass)
    let activeSoftFlags = new Set();

    function applyBehaviors(questionValue, behaviors, subQContainer) {
        if (!subQContainer) return;

        // Hide all direct sub-question wrappers first, disable their inputs
        subQContainer.querySelectorAll(':scope > .sub-question-wrapper').forEach(sq => {
            sq.classList.add('hidden');
            sq.querySelectorAll('input, select, textarea').forEach(i => i.disabled = true);
        });

        if (!behaviors || !behaviors.length) return;

        const matchingBehavior = behaviors.find(b => evaluateCondition(questionValue, b.condition || {}));
        if (!matchingBehavior) return;

        // Collect soft flags
        (matchingBehavior.flags || []).forEach(f => {
            if (f.flag_type === 'soft') activeSoftFlags.add(f.flag_message);
        });

        // Show sub-question if exists
        if (matchingBehavior.sub_question) {
            const tempId = matchingBehavior.sub_question.temp_id;
            const sqWrapper = subQContainer.querySelector(`:scope > .sub-question-wrapper[data-temp-id="${tempId}"]`);
            if (sqWrapper) {
                sqWrapper.classList.remove('hidden');
                sqWrapper.querySelectorAll('input, select, textarea').forEach(i => i.disabled = false);

                // Recurse into sub-question's own behaviors
                const subVal      = getSubQuestionValue(sqWrapper);
                const subBehaviors = matchingBehavior.sub_question.behaviors || [];
                const nestedContainer = sqWrapper.querySelector('.nested-sub-questions-container');
                applyBehaviors(subVal, subBehaviors, nestedContainer);
            }
        }
    }

    function handleBehaviors() {
        activeSoftFlags = new Set();

        document.querySelectorAll('.question-wrapper').forEach(wrapper => {
            try {
                const raw = wrapper.dataset.behaviors;
                if (!raw || raw === '[]' || raw === 'null') return;
                const behaviors = JSON.parse(raw);
                if (!behaviors || !behaviors.behaviors) return;

                const questionValue  = getQuestionValue(wrapper);
                const subQContainer  = wrapper.querySelector('.sub-questions-container');
                applyBehaviors(questionValue, behaviors.behaviors, subQContainer);
            } catch(e) {
                console.error('Behavior engine error:', e);
            }
        });

        // Render soft flag warnings
        warningList.innerHTML = '';
        if (activeSoftFlags.size > 0) {
            activeSoftFlags.forEach(msg => {
                const li = document.createElement('li');
                li.textContent = msg;
                warningList.appendChild(li);
            });
            warningFlags.classList.remove('hidden');
        } else {
            warningFlags.classList.add('hidden');
        }

        updateProgress();
    }

    // ── Collect sub-answers as JSON ───────────────────────────────────────────
    function collectSubAnswersForQuestion(questionWrapper) {
        const subContainer = questionWrapper.querySelector('.sub-questions-container');
        if (!subContainer) return null;
        return collectSubAnswersFromContainer(subContainer);
    }

    function collectSubAnswersFromContainer(container) {
        const results = [];
        container.querySelectorAll(':scope > .sub-question-wrapper:not(.hidden)').forEach(sqWrapper => {
            const tempId    = sqWrapper.dataset.tempId;
            let value       = '';
            const inputs    = sqWrapper.querySelectorAll(':scope > input.sub-question-input:not(:disabled), :scope > select.sub-question-input:not(:disabled), :scope > textarea.sub-question-input:not(:disabled)');
            inputs.forEach(inp => {
                if (inp.type === 'radio' && inp.checked)  value = inp.value;
                else if (inp.type === 'checkbox' && inp.checked) value = inp.value;
                else if (inp.type !== 'radio' && inp.type !== 'checkbox') value = inp.value;
            });
            const nestedContainer = sqWrapper.querySelector('.nested-sub-questions-container');
            const subAnswers = nestedContainer ? collectSubAnswersFromContainer(nestedContainer) : [];
            results.push({ temp_id: tempId, value, sub_answers: subAnswers });
        });
        return results;
    }

    function appendSubAnswersToFormData(formData) {
        document.querySelectorAll('.question-wrapper').forEach(wrapper => {
            const qId = wrapper.dataset.questionId;
            const subAnswers = collectSubAnswersForQuestion(wrapper);
            if (subAnswers && subAnswers.length > 0) {
                formData.append('sub_answers_json[' + qId + ']', JSON.stringify(subAnswers));
            }
        });
    }

    // ── Input listeners ───────────────────────────────────────────────────────
    function attachInputListeners(root) {
        root.querySelectorAll('.question-input, .sub-question-input').forEach(input => {
            input.addEventListener('change', function() {
                saveToLocalStorage();
                handleBehaviors();
                autoSave();
                const qId = this.dataset.questionId;
                if (qId) {
                    const errorDiv = document.getElementById('error_' + qId);
                    if (errorDiv) errorDiv.textContent = '';
                    this.classList.remove('border-red-500');
                }
            });
            input.addEventListener('input', function() {
                handleBehaviors();
                saveToLocalStorage();
                autoSave();
            });
        });
    }

    attachInputListeners(document);

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

    // Form submission (Final Submit - Phase 6)
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        blockedMessage.classList.add('hidden');
        warningFlags.classList.add('hidden');
        warningList.innerHTML = '';
        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));

        const defaultButton = document.getElementById('submitWithMedicineBtn');
        if (!activeSubmitBtn) {
            activeSubmitBtn = defaultButton || submitButtons[0];
            activeSubmitBtnHtml = activeSubmitBtn ? activeSubmitBtn.innerHTML : '';
            if (submissionFlowInput.value !== 'prescription_only') {
                submissionFlowInput.value = 'with_medicine';
            }
        }

        submitButtons.forEach(button => {
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');
        });
        if (activeSubmitBtn) {
            activeSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __("Submitting...") }}';
        }

        const formData = new FormData(form);
        appendSubAnswersToFormData(formData);

        fetch('{{ route("questionnaire.prepare-submit", ["categoryId" => $category->id]) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            submitButtons.forEach(button => {
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            });
            if (activeSubmitBtn) {
                activeSubmitBtn.innerHTML = activeSubmitBtnHtml;
            }

            if (data.blocked) {
                blockedMessage.classList.remove('hidden');
                document.getElementById('blockedText').textContent = data.message;
                return;
            }

            if (data.errors) {
                Object.keys(data.errors).forEach(questionId => {
                    const input = document.querySelector(`[data-question-id="${questionId}"] .question-input, [data-question-id="${questionId}"]`);
                    const errorDiv = document.getElementById('error_' + questionId);
                    if (input) input.classList.add('border-red-500');
                    if (errorDiv) errorDiv.textContent = data.errors[questionId];
                });
                return;
            }

            if (data.success) {
                // Clear localStorage
                localStorage.removeItem('questionnaire_answers_' + categoryId);

                // Redirect to payment page (after payment, questionnaire is submitted and user is redirected to next step)
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.href = '{{ route("questionnaire.success", ["categoryId" => $category->id]) }}';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitButtons.forEach(button => {
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            });
            if (activeSubmitBtn) {
                activeSubmitBtn.innerHTML = activeSubmitBtnHtml;
            }
            alert('{{ __("An error occurred. Please try again.") }}');
        });
    });

    // Initial setup
    loadFromLocalStorage();
    handleBehaviors();
    updateProgress();
});
</script>
@endsection
