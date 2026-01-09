@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', $questionnaire->name)

@section('content')
<div class="xl:w-3/4 mx-auto py-10">
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray">
            <li><a href="{{ url('/') }}" class="hover:text-primary">{{ __('Home') }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('categories') }}" class="hover:text-primary">{{ __('Categories') }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('category.detail', ['id' => $category->id]) }}" class="hover:text-primary">{{ $category->name }}</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-black">{{ __('Questionnaire') }}</li>
        </ol>
    </nav>

    <!-- Category/Treatment Info Card -->
    <div class="bg-white shadow-xl rounded-lg p-6 mb-6">
        <div class="flex items-center gap-4">
            @if($category->image)
            <img src="{{ $category->fullImage }}" alt="{{ $category->name }}" class="h-16 w-16 object-cover rounded-lg">
            @endif
            <div>
                <h2 class="font-fira-sans font-medium text-2xl text-black">{{ $category->name }}</h2>
                @if($treatment)
                <p class="font-fira-sans text-gray">{{ $treatment->name }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Questionnaire Card -->
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="bg-primary text-white p-6">
            <h3 class="font-fira-sans font-medium text-2xl mb-2">{{ $questionnaire->name }}</h3>
            @if($questionnaire->description)
            <p class="text-white text-opacity-90">{{ $questionnaire->description }}</p>
            @endif
        </div>

        <form id="questionnaireForm" method="POST" action="#" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <input type="hidden" name="questionnaire_id" value="{{ $questionnaire->id }}">

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
                                <input type="text" 
                                    name="answers[{{ $question->id }}]" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg font-fira-sans question-input"
                                    data-question-id="{{ $question->id }}"
                                    value="{{ isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : '' }}"
                                    @if($question->required) required @endif
                                    @if($question->validation_rules)
                                        @if(isset($question->validation_rules['min'])) minlength="{{ $question->validation_rules['min'] }}" @endif
                                        @if(isset($question->validation_rules['max'])) maxlength="{{ $question->validation_rules['max'] }}" @endif
                                    @endif>
                                @break

                            @case('textarea')
                                <textarea 
                                    name="answers[{{ $question->id }}]" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg font-fira-sans question-input"
                                    data-question-id="{{ $question->id }}"
                                    rows="4"
                                    @if($question->required) required @endif>{{ isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : '' }}</textarea>
                                @break

                            @case('number')
                                <input type="number" 
                                    name="answers[{{ $question->id }}]" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg font-fira-sans question-input"
                                    data-question-id="{{ $question->id }}"
                                    value="{{ isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : '' }}"
                                    @if($question->required) required @endif
                                    @if($question->validation_rules)
                                        @if(isset($question->validation_rules['min'])) min="{{ $question->validation_rules['min'] }}" @endif
                                        @if(isset($question->validation_rules['max'])) max="{{ $question->validation_rules['max'] }}" @endif
                                    @endif>
                                @break

                            @case('dropdown')
                                <select name="answers[{{ $question->id }}]" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg font-fira-sans question-input"
                                    data-question-id="{{ $question->id }}"
                                    @if($question->required) required @endif>
                                    <option value="">{{ __('Select an option') }}</option>
                                    @foreach($question->options ?? [] as $option)
                                        <option value="{{ $option }}" 
                                            {{ isset($savedAnswers['answers'][$question->id]) && $savedAnswers['answers'][$question->id] == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                                @break

                            @case('radio')
                                <div class="space-y-2">
                                    @foreach($question->options ?? [] as $optionIndex => $option)
                                    <div class="flex items-center">
                                        <input type="radio" 
                                            id="q{{ $question->id }}_opt{{ $optionIndex }}"
                                            name="answers[{{ $question->id }}]" 
                                            value="{{ $option }}"
                                            class="question-input"
                                            data-question-id="{{ $question->id }}"
                                            {{ isset($savedAnswers['answers'][$question->id]) && $savedAnswers['answers'][$question->id] == $option ? 'checked' : '' }}
                                            @if($question->required) required @endif>
                                        <label for="q{{ $question->id }}_opt{{ $optionIndex }}" class="ml-2 font-fira-sans text-gray">
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
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                            id="q{{ $question->id }}_opt{{ $optionIndex }}"
                                            name="answers[{{ $question->id }}][]" 
                                            value="{{ $option }}"
                                            class="question-input"
                                            data-question-id="{{ $question->id }}"
                                            {{ in_array($option, $savedCheckboxes) ? 'checked' : '' }}>
                                        <label for="q{{ $question->id }}_opt{{ $optionIndex }}" class="ml-2 font-fira-sans text-gray">
                                            {{ $option }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @break

                            @case('file')
                                <div class="border border-gray-300 rounded-lg p-4">
                                    <input type="file" 
                                        name="files[{{ $question->id }}]" 
                                        class="question-input"
                                        data-question-id="{{ $question->id }}"
                                        id="file{{ $question->id }}"
                                        @if($question->required) required @endif
                                        @if($question->validation_rules && isset($question->validation_rules['file_types']))
                                            accept=".{{ implode(',.', $question->validation_rules['file_types']) }}"
                                        @endif>
                                    @if($question->validation_rules && isset($question->validation_rules['file_types']))
                                        <p class="mt-2 text-sm text-gray font-fira-sans">
                                            {{ __('Allowed types:') }} {{ implode(', ', $question->validation_rules['file_types']) }}
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

                <!-- Warning Flags -->
                <div id="warningFlags" class="hidden mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h5 class="font-fira-sans font-medium text-yellow-800 mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ __('Please note:') }}
                    </h5>
                    <ul id="warningList" class="list-disc list-inside text-yellow-700 font-fira-sans"></ul>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="font-fira-sans text-gray hover:text-primary">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
                <button type="submit" class="bg-primary text-white font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-opacity-90 transition duration-300" id="submitBtn">
                    {{ __('Submit Questionnaire') }}
                    <i class="fas fa-check ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('questionnaireForm');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const submitBtn = document.getElementById('submitBtn');
    const blockedMessage = document.getElementById('blockedMessage');
    const warningFlags = document.getElementById('warningFlags');
    const warningList = document.getElementById('warningList');
    const saveIndicator = document.getElementById('saveIndicator');
    const saveIndicatorText = document.getElementById('saveIndicatorText');
    const categoryId = {{ $category->id }};
    
    let saveTimeout;
    const SAVE_DELAY = 2000; // Auto-save after 2 seconds of no typing

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

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __("Submitting...") }}';

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
            submitBtn.disabled = false;
            submitBtn.innerHTML = '{{ __("Submit Questionnaire") }} <i class="fas fa-check ml-2"></i>';

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
            submitBtn.disabled = false;
            submitBtn.innerHTML = '{{ __("Submit Questionnaire") }} <i class="fas fa-check ml-2"></i>';
            alert('{{ __("An error occurred. Please try again.") }}');
        });
    });

    // Initial setup
    loadFromLocalStorage();
    updateProgress();
    handleConditionalLogic();
});
</script>
@endpush
