@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', $questionnaire->name)

@section('css')
<style>
    /* Force white text on selected radio buttons */
    input[type="radio"]:checked + label {
        color: #ffffff !important;
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

        <form id="questionnaireForm" method="POST" action="#" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <input type="hidden" name="questionnaire_id" value="{{ $questionnaire->id }}">
            <input type="hidden" name="submission_flow" id="submissionFlow" value="with_medicine">

            <div class="p-6 sm:p-10">
                <!-- Progress Indicator -->
                <div class="mb-5 pb-5 border-b border-gray-100">
                    <div class="flex justify-between items-end mb-2">
                        <span class="font-body text-sm font-medium text-gray-500 uppercase tracking-wider">{{ __('Progress') }}</span>
                        <span class="font-body text-sm font-bold text-primary" id="progressText">0%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full transition-all duration-500 ease-in-out" id="progressBar" style="width: 0%"></div>
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
                <div class="questionnaire-section mb-12" data-section="{{ $sectionIndex }}">
                    <div class="mb-6">
                        <h4 class="font-heading font-semibold text-xl text-gray-900 flex items-center mb-0">
                            <span class="flex items-center justify-center bg-primary text-white rounded-circle flex-shrink-0 w-10 h-10 text-base font-bold mr-3 shadow-sm">{{ $sectionIndex + 1 }}</span>
                            {{ $section->name }}
                        </h4>
                        @if($section->description)
                        <p class="font-body text-muted text-sm mt-3 ml-[3.25rem]">{{ $section->description }}</p>
                        @endif
                    </div>

                    @foreach($section->questions as $question)
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
                                    $savedValue = is_array($savedValue) ? '' : (string) $savedValue;
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
                                    $savedValue = is_array($savedValue) ? '' : (string) $savedValue;
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
                                <select name="answers[{{ $question->id }}]" 
                                    class="w-full px-4 py-3 bg-[#f8f9fa] border border-gray-200 rounded-3 focus:bg-white focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-200 font-body question-input text-gray-800 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat"
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
                                <div class="space-y-3">
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
                                @php
                                    $savedFilePath = isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : '';
                                    $savedFilePath = is_array($savedFilePath) ? '' : (string) $savedFilePath;
                                @endphp
                                <div class="border-2 border-dashed border-gray-300 rounded-4 p-8 bg-[#f8f9fa] hover:bg-purple-light transition-all duration-300 text-center group cursor-pointer" onclick="document.getElementById('file{{ $question->id }}').click()">
                                    <div class="mb-4">
                                        <div class="w-16 h-16 bg-white shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center border border-gray-100 group-hover:border-primary group-hover:shadow-md transition-all">
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

            <div class="bg-light border-t border-gray-200 px-6 py-4 flex justify-between items-center sticky bottom-0 z-10">
                <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="font-body text-gray-500 hover:text-primary transition-colors duration-200 no-underline hover:no-underline font-medium">
                    <i class="bi bi-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
                <div class="flex items-center gap-3">
                    @if(empty($category->is_cannaleo_only))
                    <button type="submit" class="btn btn-outline-secondary font-body font-medium px-5 py-2.5 rounded-pill shadow-sm hover-lift" id="submitPrescriptionBtn" data-submission-flow="prescription_only"
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit']) disabled @endif>
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit'])
                            {{ __('Under Review') }}
                            <i class="bi bi-lock-fill ml-2"></i>
                        @else
                            {{ __('Prescription Only') }}
                            <i class="bi bi-file-medical ml-2"></i>
                        @endif
                    </button>
                    @endif
                    <button type="submit" class="btn btn-primary font-body font-medium px-5 py-2.5 rounded-pill shadow-sm hover-lift" id="submitWithMedicineBtn" data-submission-flow="with_medicine"
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit']) disabled @endif>
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit'])
                            {{ __('Under Review') }}
                            <i class="bi bi-lock-fill ml-2"></i>
                        @else
                            {{ __('With Medicine') }}
                            <i class="bi bi-check2 ml-2 text-lg"></i>
                        @endif
                    </button>
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
                        } else {
                            input.value = answers[questionId];
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

    // Update progress bar
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
        progressBar.style.width = progress + '%';
        progressText.textContent = progress + '%';
    }

    // Handle conditional logic
    function handleConditionalLogic() {
        document.querySelectorAll('[data-conditional]').forEach(wrapper => {
            try {
                const conditional = JSON.parse(wrapper.dataset.conditional);
                if (conditional && conditional.show_if) {
                    const targetQuestion = document.querySelector(`[data-question-id="${conditional.show_if.question_id}"]`);
                    if (targetQuestion) {
                        const inputs = targetQuestion.querySelectorAll('.question-input');
                        let currentValue = '';
                        
                        inputs.forEach(input => {
                            if (input.type === 'radio' && input.checked) {
                                currentValue = input.value;
                            } else if (input.type !== 'radio' && input.type !== 'checkbox') {
                                currentValue = input.value;
                            }
                        });

                        const shouldShow = evaluateCondition(currentValue, conditional.show_if);
                        wrapper.classList.toggle('hidden', !shouldShow);
                    }
                }
            } catch (e) {
                console.error('Conditional logic error:', e);
            }
        });
    }

    function evaluateCondition(value, condition) {
        const operator = condition.operator || 'equals';
        const targetValue = condition.value;

        switch (operator) {
            case 'equals': return value == targetValue;
            case 'not_equals': return value != targetValue;
            case 'contains': return value.toLowerCase().includes(targetValue.toLowerCase());
            case 'greater_than': return parseFloat(value) > parseFloat(targetValue);
            case 'less_than': return parseFloat(value) < parseFloat(targetValue);
            default: return true;
        }
    }

    // Listen for input changes
    document.querySelectorAll('.question-input').forEach(input => {
        input.addEventListener('change', function() {
            saveToLocalStorage();
            updateProgress();
            handleConditionalLogic();
            autoSave();
            
            // Clear error
            const errorDiv = document.getElementById('error_' + this.dataset.questionId);
            if (errorDiv) {
                errorDiv.textContent = '';
                this.classList.remove('border-red-500');
            }
        });
        input.addEventListener('input', function() {
            updateProgress();
            saveToLocalStorage();
            autoSave();
        });
    });

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
    updateProgress();
    handleConditionalLogic();
});
</script>
@endsection
