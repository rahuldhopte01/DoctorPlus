@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', $questionnaire->name . ' - ' . $currentSection->name)

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

        <!-- Step Indicator (Issue 2: Section-wise navigation) -->
        <div class="p-6 bg-gray-50 border-b">
            <div class="flex items-center justify-between mb-2">
                <span class="font-fira-sans text-sm text-gray">{{ __('Section') }} {{ $sectionIndex + 1 }} {{ __('of') }} {{ $totalSections }}</span>
                <span class="font-fira-sans text-sm text-gray">{{ round((($sectionIndex + 1) / $totalSections) * 100) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: {{ (($sectionIndex + 1) / $totalSections) * 100 }}%"></div>
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

            <div class="p-6">
                <!-- Current Section Header -->
                <div class="border-b border-gray-300 pb-4 mb-6">
                    <h4 class="font-fira-sans font-medium text-2xl text-black">
                        <span class="inline-block bg-primary text-white rounded-full w-10 h-10 text-center leading-10 mr-3">{{ $sectionIndex + 1 }}</span>
                        {{ $currentSection->name }}
                    </h4>
                    @if($currentSection->description)
                    <p class="font-fira-sans text-gray text-sm mt-3 ml-13">{{ $currentSection->description }}</p>
                    @endif
                </div>

                <!-- Section Questions -->
                @foreach($currentSection->questions as $question)
                <div class="question-wrapper mb-6" data-question-id="{{ $question->id }}" 
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
                                $savedValue = is_array($savedValue) ? json_encode($savedValue) : (string) $savedValue;
                            @endphp
                            <input type="text" 
                                name="answers[{{ $question->id }}]" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg font-fira-sans question-input"
                                data-question-id="{{ $question->id }}"
                                value="{{ $savedValue }}"
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
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg font-fira-sans question-input"
                                data-question-id="{{ $question->id }}"
                                rows="4"
                                @if($question->required) required @endif>{{ $savedValue }}</textarea>
                            @break

                        @case('number')
                            @php
                                $savedValue = isset($savedAnswers['answers'][$question->id]) ? $savedAnswers['answers'][$question->id] : '';
                                $savedValue = is_array($savedValue) ? '' : (string) $savedValue;
                            @endphp
                            <input type="number" 
                                name="answers[{{ $question->id }}]" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg font-fira-sans question-input"
                                data-question-id="{{ $question->id }}"
                                value="{{ $savedValue }}"
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
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg font-fira-sans question-input"
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
                            <div class="space-y-2">
                                @foreach($question->options ?? [] as $optionIndex => $option)
                                <div class="flex items-center">
                                    <input type="radio" 
                                        id="q{{ $question->id }}_opt{{ $optionIndex }}"
                                        name="answers[{{ $question->id }}]" 
                                        value="{{ $option }}"
                                        class="question-input"
                                        data-question-id="{{ $question->id }}"
                                        {{ $savedValue !== null && $savedValue == $option ? 'checked' : '' }}
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
                            <!-- Issue 3: File upload field (supports birth certificate and other files) -->
                            <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                                <input type="file" 
                                    name="files[{{ $question->id }}]" 
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark question-input"
                                    data-question-id="{{ $question->id }}"
                                    id="file{{ $question->id }}"
                                    @if($question->required) required @endif
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    data-max-size="5242880">
                                <p class="mt-2 text-xs text-gray-600 font-fira-sans">
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
            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                <div>
                    @if($sectionIndex > 0)
                    <button type="button" id="prevBtn" class="font-fira-sans text-gray hover:text-primary">
                        <i class="fas fa-arrow-left mr-2"></i>{{ __('Previous') }}
                    </button>
                    @else
                    <a href="{{ route('category.detail', ['id' => $category->id]) }}" class="font-fira-sans text-gray hover:text-primary">
                        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Category') }}
                    </a>
                    @endif
                </div>
                <div>
                    @if($sectionIndex < $totalSections - 1)
                    <button type="button" id="nextBtn" class="bg-primary text-white font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-opacity-90 transition duration-300">
                        {{ __('Next Section') }}
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    @else
                    <button type="button" id="submitBtn" class="bg-primary text-white font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-opacity-90 transition duration-300">
                        {{ __('Submit Questionnaire') }}
                        <i class="fas fa-check ml-2"></i>
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

    // Function to display validation errors
    function displayValidationErrors(errors, errorDetails) {
        // Clear previous errors
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
