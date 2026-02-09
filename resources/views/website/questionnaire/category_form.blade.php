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
<div class="relative w-full bg-cover bg-center flex items-center justify-center" 
     style="min-height: 350px; background-image: url('https://placehold.co/1920x600/e2e8f0/64748b?text=Pharmacy+Banner'); background-color: #0b2c4e;">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div> {{-- Dark Overlay --}}
    <div class="z-10 text-center px-4">
        <h1 class="text-4xl md:text-5xl font-bold text-white font-fira-sans mb-4 tracking-wide">{{ $questionnaire->name }}</h1>
        <nav class="flex justify-center" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-gray-200">
                <li><a href="{{ url('/') }}" class="hover:text-white transition-colors">{{ __('Home') }}</a></li>
                <li><span class="mx-2">/</span></li>
                <li><a href="{{ route('categories') }}" class="hover:text-white transition-colors">{{ __('Categories') }}</a></li>
                <li><span class="mx-2">/</span></li>
                <li><a href="{{ route('category.detail', ['id' => $category->id]) }}" class="hover:text-white transition-colors">{{ $category->name }}</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-white font-medium">{{ __('Questionnaire') }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Questionnaire Card -->
    <div class="bg-white rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.1)] border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-100">
             <div class="flex items-center gap-4 mb-2">
                 <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clipboard-list text-2xl text-primary"></i>
                 </div>
                 <div>
                    <h2 class="text-2xl font-bold text-gray-900 font-fira-sans">{{ $questionnaire->name }}</h2>
                    @if($treatment)
                    <p class="font-fira-sans text-gray-500 text-sm">{{ $treatment->name }}</p>
                    @endif
                 </div>
            </div>
            @if($questionnaire->description)
            <p class="text-gray-600 mt-4 leading-relaxed font-fira-sans">{{ $questionnaire->description }}</p>
            @endif
        </div>

        <form id="questionnaireForm" method="POST" action="#" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <input type="hidden" name="questionnaire_id" value="{{ $questionnaire->id }}">
            <input type="hidden" name="submission_flow" id="submissionFlow" value="with_medicine">

            <div class="p-6">
                <!-- Progress Indicator -->
                <div class="mb-6">
                    <div class="flex justify-between mb-2">
                        <span class="font-fira-sans text-gray">{{ __('Progress') }}</span>
                        <span class="font-fira-sans text-gray" id="progressText">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full transition-all duration-300" id="progressBar" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Auto-save indicator -->
                <div id="saveIndicator" class="hidden mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center text-blue-700">
                        <i class="fas fa-save mr-2"></i>
                        <span class="font-fira-sans text-sm" id="saveIndicatorText">{{ __('Saving...') }}</span>
                    </div>
                </div>

                <!-- Sections -->
                @foreach($questionnaire->sections as $sectionIndex => $section)
                <div class="questionnaire-section mb-8" data-section="{{ $sectionIndex }}">
                    <div class="border-b border-gray-300 pb-3 mb-4">
                        <h4 class="font-fira-sans font-medium text-xl text-black">
                            <span class="inline-block bg-primary text-white rounded-full w-8 h-8 text-center leading-8 mr-3">{{ $sectionIndex + 1 }}</span>
                            {{ $section->name }}
                        </h4>
                        @if($section->description)
                        <p class="font-fira-sans text-gray text-sm mt-2 ml-11">{{ $section->description }}</p>
                        @endif
                    </div>

                    @foreach($section->questions as $question)
                    <div class="question-wrapper mb-6 ml-11" data-question-id="{{ $question->id }}" 
                         @if($question->conditional_logic) data-conditional='@json($question->conditional_logic)' @endif>
                        <label class="block font-fira-sans font-medium text-base text-black mb-2">
                            {{ $question->question_text }}
                            @if($question->required)
                            <span class="text-red-500">*</span>
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
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all duration-200 font-fira-sans question-input placeholder-gray-400 text-gray-800"
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
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all duration-200 font-fira-sans question-input placeholder-gray-400 text-gray-800"
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
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all duration-200 font-fira-sans question-input placeholder-gray-400 text-gray-800"
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
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all duration-200 font-fira-sans question-input text-gray-800 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_1rem_center] bg-no-repeat"
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
                                            class="flex items-center justify-center w-[150px] px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border-2 border-blue-200 rounded-lg cursor-pointer hover:bg-blue-100 hover:border-blue-300 peer-checked:bg-blue-600 peer-checked:!text-white peer-checked:border-blue-600 peer-focus:ring-2 peer-focus:ring-blue-600 peer-focus:ring-offset-2 transition-all duration-200 font-fira-sans shadow-sm text-center h-full">
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
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" 
                                                id="q{{ $question->id }}_opt{{ $optionIndex }}"
                                                name="answers[{{ $question->id }}][]" 
                                                value="{{ $option }}"
                                                class="question-input w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary focus:ring-2 transition duration-150 ease-in-out cursor-pointer"
                                                data-question-id="{{ $question->id }}"
                                                {{ in_array($option, $savedCheckboxes) ? 'checked' : '' }}>
                                        </div>
                                        <label for="q{{ $question->id }}_opt{{ $optionIndex }}" class="ml-3 font-fira-sans text-gray-700 font-medium cursor-pointer select-none">
                                            {{ $option }}
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
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 text-center group">
                                    <div class="mb-3">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 group-hover:text-primary transition-colors duration-200"></i>
                                    </div>
                                    <input type="file" 
                                        name="files[{{ $question->id }}]" 
                                        class="question-input w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer"
                                        data-question-id="{{ $question->id }}"
                                        id="file{{ $question->id }}"
                                        @if($question->required) required @endif
                                        @if($question->validation_rules && isset($question->validation_rules['file_types']))
                                            accept=".{{ implode(',.', $question->validation_rules['file_types']) }}"
                                        @endif>
                                    @if($question->validation_rules && isset($question->validation_rules['file_types']))
                                        <p class="mt-3 text-xs text-gray-500 font-fira-sans">
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
                <div id="warningFlags" class="hidden mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h5 class="font-fira-sans font-medium text-yellow-800 mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ __('Please note:') }}
                    </h5>
                    <ul id="warningList" class="list-disc list-inside text-yellow-700 font-fira-sans"></ul>
                </div>
            </div>

            <div class="bg-white border-t border-gray-200 px-6 py-4 flex justify-between items-center sticky bottom-0 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
                <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="font-fira-sans text-gray hover:text-primary transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-white text-gray-700 border border-gray-300 font-fira-sans font-medium px-6 py-3 rounded-xl hover:bg-gray-50 transition duration-300 shadow-sm" id="submitPrescriptionBtn" data-submission-flow="prescription_only"
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit']) disabled @endif>
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit'])
                            {{ __('Under Review') }}
                            <i class="fas fa-lock ml-2"></i>
                        @else
                            {{ __('Prescription Only') }}
                            <i class="fas fa-file-medical ml-2"></i>
                        @endif
                    </button>
                    <button type="submit" class="bg-primary text-white font-fira-sans font-medium px-6 py-3 rounded-xl hover:bg-opacity-90 transition duration-300 shadow-lg shadow-primary/30" id="submitWithMedicineBtn" data-submission-flow="with_medicine"
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit']) disabled @endif>
                        @if(isset($submissionCheck) && !$submissionCheck['can_submit'])
                            {{ __('Under Review') }}
                            <i class="fas fa-lock ml-2"></i>
                        @else
                            {{ __('With Medicine') }}
                            <i class="fas fa-check ml-2"></i>
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

        fetch('{{ route("questionnaire.submit", ["categoryId" => $category->id]) }}', {
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
                
                if (data.has_warnings && data.flags) {
                    warningFlags.classList.remove('hidden');
                    Object.values(data.flags).forEach(flag => {
                        const li = document.createElement('li');
                        li.textContent = flag.flag_message;
                        warningList.appendChild(li);
                    });
                }

                // Redirect to success page
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
