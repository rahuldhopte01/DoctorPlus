@extends('layout.mainlayout')

@section('title', __('Medical Questionnaire'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Doctor Info Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <img src="{{ $doctor->fullImage }}" alt="{{ $doctor->name }}" 
                        class="rounded-circle mr-3" style="width: 60px; height: 60px; object-fit: cover;">
                    <div>
                        <h5 class="mb-1">{{ $doctor->name }}</h5>
                        <p class="text-muted mb-0">
                            {{ $doctor->treatment->name ?? '' }} 
                            @if($doctor->category)
                                | {{ $doctor->category->name }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Questionnaire Card -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        {{ $questionnaire->name }}
                    </h4>
                    @if($questionnaire->description)
                        <small class="opacity-75">{{ $questionnaire->description }}</small>
                    @endif
                </div>

                <form id="questionnaireForm" method="POST">
                    @csrf
                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                    <input type="hidden" name="questionnaire_id" value="{{ $questionnaire->id }}">

                    <div class="card-body">
                        <!-- Progress Indicator -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('Progress') }}</span>
                                <span class="text-muted" id="progressText">0%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" id="progressBar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Sections -->
                        @foreach($questionnaire->sections as $sectionIndex => $section)
                        <div class="questionnaire-section mb-4" data-section="{{ $sectionIndex }}">
                            <h5 class="border-bottom pb-2 mb-3">
                                <span class="badge badge-primary mr-2">{{ $sectionIndex + 1 }}</span>
                                {{ $section->name }}
                            </h5>
                            @if($section->description)
                                <p class="text-muted small mb-3">{{ $section->description }}</p>
                            @endif

                            @foreach($section->questions as $question)
                            <div class="question-wrapper mb-4" data-question-id="{{ $question->id }}" 
                                @if($question->conditional_logic)
                                    data-conditional='@json($question->conditional_logic)'
                                @endif>
                                <label class="form-label font-weight-bold">
                                    {{ $question->question_text }}
                                    @if($question->required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @switch($question->field_type)
                                    @case('text')
                                        <input type="text" 
                                            name="answers[{{ $question->id }}]" 
                                            class="form-control question-input"
                                            data-question-id="{{ $question->id }}"
                                            {{ $question->required ? 'required' : '' }}
                                            @if($question->validation_rules)
                                                @if(isset($question->validation_rules['min']))
                                                    minlength="{{ $question->validation_rules['min'] }}"
                                                @endif
                                                @if(isset($question->validation_rules['max']))
                                                    maxlength="{{ $question->validation_rules['max'] }}"
                                                @endif
                                            @endif>
                                        @break

                                    @case('textarea')
                                        <textarea 
                                            name="answers[{{ $question->id }}]" 
                                            class="form-control question-input"
                                            data-question-id="{{ $question->id }}"
                                            rows="3"
                                            {{ $question->required ? 'required' : '' }}></textarea>
                                        @break

                                    @case('number')
                                        <input type="number" 
                                            name="answers[{{ $question->id }}]" 
                                            class="form-control question-input"
                                            data-question-id="{{ $question->id }}"
                                            {{ $question->required ? 'required' : '' }}
                                            @if($question->validation_rules)
                                                @if(isset($question->validation_rules['min']))
                                                    min="{{ $question->validation_rules['min'] }}"
                                                @endif
                                                @if(isset($question->validation_rules['max']))
                                                    max="{{ $question->validation_rules['max'] }}"
                                                @endif
                                            @endif>
                                        @break

                                    @case('dropdown')
                                        <select name="answers[{{ $question->id }}]" 
                                            class="form-control question-input"
                                            data-question-id="{{ $question->id }}"
                                            {{ $question->required ? 'required' : '' }}>
                                            <option value="">{{ __('Select an option') }}</option>
                                            @foreach($question->options ?? [] as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                        @break

                                    @case('radio')
                                        <div class="mt-2">
                                            @foreach($question->options ?? [] as $optionIndex => $option)
                                                <div class="custom-control custom-radio mb-2">
                                                    <input type="radio" 
                                                        id="q{{ $question->id }}_opt{{ $optionIndex }}"
                                                        name="answers[{{ $question->id }}]" 
                                                        value="{{ $option }}"
                                                        class="custom-control-input question-input"
                                                        data-question-id="{{ $question->id }}"
                                                        {{ $question->required ? 'required' : '' }}>
                                                    <label class="custom-control-label" for="q{{ $question->id }}_opt{{ $optionIndex }}">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @break

                                    @case('checkbox')
                                        <div class="mt-2">
                                            @foreach($question->options ?? [] as $optionIndex => $option)
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input type="checkbox" 
                                                        id="q{{ $question->id }}_opt{{ $optionIndex }}"
                                                        name="answers[{{ $question->id }}][]" 
                                                        value="{{ $option }}"
                                                        class="custom-control-input question-input"
                                                        data-question-id="{{ $question->id }}">
                                                    <label class="custom-control-label" for="q{{ $question->id }}_opt{{ $optionIndex }}">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @break

                                    @case('file')
                                        <div class="custom-file">
                                            <input type="file" 
                                                name="files[{{ $question->id }}]" 
                                                class="custom-file-input question-input"
                                                data-question-id="{{ $question->id }}"
                                                id="file{{ $question->id }}"
                                                {{ $question->required ? 'required' : '' }}
                                                @if($question->validation_rules && isset($question->validation_rules['file_types']))
                                                    accept=".{{ implode(',.', $question->validation_rules['file_types']) }}"
                                                @endif>
                                            <label class="custom-file-label" for="file{{ $question->id }}">
                                                {{ __('Choose file') }}
                                            </label>
                                        </div>
                                        @if($question->validation_rules && isset($question->validation_rules['file_types']))
                                            <small class="text-muted">
                                                {{ __('Allowed types:') }} {{ implode(', ', $question->validation_rules['file_types']) }}
                                            </small>
                                        @endif
                                        @break
                                @endswitch

                                <div class="invalid-feedback" id="error_{{ $question->id }}"></div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach

                        <!-- Blocked Message (hidden by default) -->
                        <div id="blockedMessage" class="alert alert-danger d-none">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span id="blockedText"></span>
                        </div>

                        <!-- Warning Flags (hidden by default) -->
                        <div id="warningFlags" class="alert alert-warning d-none">
                            <h6><i class="fas fa-exclamation-circle mr-2"></i>{{ __('Please note:') }}</h6>
                            <ul id="warningList" class="mb-0"></ul>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="{{ url('/doctor-profile/'.$doctor->id.'/'.$doctor->name) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                {{ __('Continue to Booking') }}
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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

    // Update progress bar
    function updateProgress() {
        const questions = document.querySelectorAll('.question-wrapper:not(.d-none)');
        const answered = document.querySelectorAll('.question-input:not(:placeholder-shown), .question-input:checked');
        let answeredCount = 0;
        
        questions.forEach(q => {
            const input = q.querySelector('.question-input');
            if (input) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    const name = input.name;
                    if (document.querySelector(`input[name="${name}"]:checked`)) {
                        answeredCount++;
                    }
                } else if (input.value) {
                    answeredCount++;
                }
            }
        });

        const progress = Math.round((answeredCount / questions.length) * 100);
        progressBar.style.width = progress + '%';
        progressText.textContent = progress + '%';
    }

    // Handle conditional logic
    function handleConditionalLogic() {
        document.querySelectorAll('[data-conditional]').forEach(wrapper => {
            const conditional = JSON.parse(wrapper.dataset.conditional);
            if (conditional && conditional.show_if) {
                const targetQuestion = document.querySelector(`[data-question-id="${conditional.show_if.question_id}"]`);
                if (targetQuestion) {
                    const input = targetQuestion.querySelector('.question-input');
                    let currentValue = '';
                    
                    if (input.type === 'radio') {
                        const checked = document.querySelector(`input[name="${input.name}"]:checked`);
                        currentValue = checked ? checked.value : '';
                    } else {
                        currentValue = input.value;
                    }

                    const shouldShow = evaluateCondition(currentValue, conditional.show_if);
                    wrapper.classList.toggle('d-none', !shouldShow);
                }
            }
        });
    }

    function evaluateCondition(value, condition) {
        const operator = condition.operator || 'equals';
        const targetValue = condition.value;

        switch (operator) {
            case 'equals':
                return value == targetValue;
            case 'not_equals':
                return value != targetValue;
            case 'contains':
                return value.toLowerCase().includes(targetValue.toLowerCase());
            case 'greater_than':
                return parseFloat(value) > parseFloat(targetValue);
            case 'less_than':
                return parseFloat(value) < parseFloat(targetValue);
            default:
                return true;
        }
    }

    // Listen for input changes
    document.querySelectorAll('.question-input').forEach(input => {
        input.addEventListener('change', function() {
            updateProgress();
            handleConditionalLogic();
            // Clear error
            const errorDiv = document.getElementById('error_' + this.dataset.questionId);
            if (errorDiv) {
                errorDiv.textContent = '';
                this.classList.remove('is-invalid');
            }
        });
        input.addEventListener('input', updateProgress);
    });

    // Handle file input label
    document.querySelectorAll('.custom-file-input').forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : '{{ __("Choose file") }}';
            this.nextElementSibling.textContent = fileName;
        });
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset states
        blockedMessage.classList.add('d-none');
        warningFlags.classList.add('d-none');
        warningList.innerHTML = '';
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __("Validating...") }}';

        const formData = new FormData(form);

        fetch('{{ url("/questionnaire/validate/" . $doctor->id) }}', {
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
            submitBtn.innerHTML = '{{ __("Continue to Booking") }} <i class="fas fa-arrow-right ml-2"></i>';

            if (data.blocked) {
                blockedMessage.classList.remove('d-none');
                document.getElementById('blockedText').textContent = data.message;
                return;
            }

            if (data.errors) {
                Object.keys(data.errors).forEach(questionId => {
                    const input = document.querySelector(`[data-question-id="${questionId}"] .question-input`);
                    const errorDiv = document.getElementById('error_' + questionId);
                    if (input) input.classList.add('is-invalid');
                    if (errorDiv) errorDiv.textContent = data.errors[questionId];
                });
                return;
            }

            if (data.success) {
                if (data.has_warnings && data.flags) {
                    warningFlags.classList.remove('d-none');
                    Object.values(data.flags).forEach(flag => {
                        const li = document.createElement('li');
                        li.textContent = flag.flag_message;
                        warningList.appendChild(li);
                    });
                }

                // Redirect to booking page
                window.location.href = '{{ url("/booking/" . $doctor->id . "/" . urlencode($doctor->name)) }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '{{ __("Continue to Booking") }} <i class="fas fa-arrow-right ml-2"></i>';
            alert('{{ __("An error occurred. Please try again.") }}');
        });
    });

    // Initial setup
    updateProgress();
    handleConditionalLogic();
});
</script>
@endpush

