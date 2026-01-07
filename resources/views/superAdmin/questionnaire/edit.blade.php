@extends('layout.mainlayout_admin',['activePage' => 'questionnaire'])

@section('title',__('Edit Questionnaire'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Edit Questionnaire'),
    ])

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>{{ __('Edit Questionnaire') }} - {{ $questionnaire->name }}</h4>
                <div class="card-header-action">
                    <span class="badge badge-info">v{{ $questionnaire->version }}</span>
                </div>
            </div>
            <form action="{{ url('questionnaire/'.$questionnaire->id) }}" method="POST" id="questionnaireForm">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ __('Updating this questionnaire will increment its version. Existing appointment answers will be preserved with the old version.') }}
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="treatment_id">{{ __('Treatment') }}</label>
                                <input type="text" class="form-control" value="{{ $questionnaire->treatment->name ?? 'N/A' }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('Questionnaire Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $questionnaire->name) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">{{ __('Description') }}</label>
                                <textarea name="description" id="description" class="form-control" rows="2">{{ old('description', $questionnaire->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="custom-switch pl-0">
                                    <input type="checkbox" name="status" class="custom-switch-input" {{ $questionnaire->status ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">{{ __('Active') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Sections Container -->
                    <div id="sectionsContainer">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ __('Sections & Questions') }}</h5>
                            <button type="button" class="btn btn-success btn-sm" onclick="addSection()">
                                <i class="fas fa-plus"></i> {{ __('Add Section') }}
                            </button>
                        </div>

                        <div id="sectionsList">
                            @foreach($questionnaire->sections as $sectionIndex => $section)
                            <div class="card section-card mb-3" data-section-index="{{ $sectionIndex }}">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <i class="fas fa-grip-vertical text-muted mr-2"></i>
                                        <input type="text" name="sections[{{ $sectionIndex }}][name]" class="form-control form-control-sm" 
                                            value="{{ $section->name }}" required style="max-width: 300px;">
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeSection(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <input type="text" name="sections[{{ $sectionIndex }}][description]" class="form-control form-control-sm" 
                                            value="{{ $section->description }}" placeholder="{{ __('Section description (optional)') }}">
                                    </div>
                                    
                                    <div class="questions-container" id="questions{{ $sectionIndex }}">
                                        @foreach($section->questions as $questionIndex => $question)
                                        <div class="card question-card mb-2" data-question-index="{{ $questionIndex }}">
                                            <div class="card-body p-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-2">
                                                            <label class="small">{{ __('Question Text') }} <span class="text-danger">*</span></label>
                                                            <textarea name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][question_text]" 
                                                                class="form-control form-control-sm" rows="2" required>{{ $question->question_text }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group mb-2">
                                                            <label class="small">{{ __('Field Type') }} <span class="text-danger">*</span></label>
                                                            <select name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][field_type]" 
                                                                class="form-control form-control-sm field-type-select" required onchange="toggleOptions(this)">
                                                                @foreach($fieldTypes as $value => $label)
                                                                    <option value="{{ $value }}" {{ $question->field_type == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group mb-2">
                                                            <label class="small">{{ __('Required') }}</label>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][required]" 
                                                                    class="custom-control-input" id="required_{{ $sectionIndex }}_{{ $questionIndex }}" {{ $question->required ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="required_{{ $sectionIndex }}_{{ $questionIndex }}">{{ __('Yes') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 text-right">
                                                        <button type="button" class="btn btn-danger btn-sm mt-4" onclick="removeQuestion(this)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Options for dropdown/radio/checkbox -->
                                                <div class="row options-row" style="{{ in_array($question->field_type, ['dropdown', 'radio', 'checkbox']) ? 'display: flex;' : 'display: none;' }}">
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-2">
                                                            <label class="small">{{ __('Options') }} <small class="text-muted">({{ __('one per line') }})</small></label>
                                                            <textarea name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][options]" 
                                                                class="form-control form-control-sm" rows="3">{{ is_array($question->options) ? implode("\n", $question->options) : '' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Advanced Options -->
                                                <div class="mt-2">
                                                    <a class="small text-primary" data-toggle="collapse" href="#advanced_{{ $sectionIndex }}_{{ $questionIndex }}">
                                                        <i class="fas fa-cog"></i> {{ __('Advanced Options') }}
                                                    </a>
                                                    <div class="collapse mt-2" id="advanced_{{ $sectionIndex }}_{{ $questionIndex }}">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group mb-2">
                                                                    <label class="small">{{ __('Flag Type') }}</label>
                                                                    <select name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][flag_type]" class="form-control form-control-sm">
                                                                        <option value="">{{ __('No Flag') }}</option>
                                                                        <option value="soft" {{ ($question->flagging_rules['flag_type'] ?? '') == 'soft' ? 'selected' : '' }}>{{ __('Soft Flag (Warning)') }}</option>
                                                                        <option value="hard" {{ ($question->flagging_rules['flag_type'] ?? '') == 'hard' ? 'selected' : '' }}>{{ __('Hard Flag (Block)') }}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group mb-2">
                                                                    <label class="small">{{ __('Flag When Value') }}</label>
                                                                    <input type="text" name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][flag_value]" 
                                                                        class="form-control form-control-sm" value="{{ $question->flagging_rules['conditions'][0]['value'] ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group mb-2">
                                                                    <label class="small">{{ __('Flag Message') }}</label>
                                                                    <input type="text" name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][flag_message]" 
                                                                        class="form-control form-control-sm" value="{{ $question->flagging_rules['conditions'][0]['flag_message'] ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-0">
                                                                    <label class="small">{{ __('Doctor Notes') }}</label>
                                                                    <input type="text" name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][doctor_notes]" 
                                                                        class="form-control form-control-sm" value="{{ $question->doctor_notes }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addQuestion({{ $sectionIndex }})">
                                        <i class="fas fa-plus"></i> {{ __('Add Question') }}
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="{{ url('questionnaire') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Update Questionnaire') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Section Template -->
<template id="sectionTemplate">
    <div class="card section-card mb-3" data-section-index="__SECTION_INDEX__">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center flex-grow-1">
                <i class="fas fa-grip-vertical text-muted mr-2"></i>
                <input type="text" name="sections[__SECTION_INDEX__][name]" class="form-control form-control-sm" 
                    placeholder="{{ __('Section Name') }}" required style="max-width: 300px;">
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeSection(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="form-group">
                <input type="text" name="sections[__SECTION_INDEX__][description]" class="form-control form-control-sm" 
                    placeholder="{{ __('Section description (optional)') }}">
            </div>
            
            <div class="questions-container" id="questions__SECTION_INDEX__">
            </div>
            
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addQuestion(__SECTION_INDEX__)">
                <i class="fas fa-plus"></i> {{ __('Add Question') }}
            </button>
        </div>
    </div>
</template>

<!-- Question Template -->
<template id="questionTemplate">
    <div class="card question-card mb-2" data-question-index="__QUESTION_INDEX__">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-2">
                        <label class="small">{{ __('Question Text') }} <span class="text-danger">*</span></label>
                        <textarea name="sections[__SECTION_INDEX__][questions][__QUESTION_INDEX__][question_text]" 
                            class="form-control form-control-sm" rows="2" required></textarea>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-2">
                        <label class="small">{{ __('Field Type') }} <span class="text-danger">*</span></label>
                        <select name="sections[__SECTION_INDEX__][questions][__QUESTION_INDEX__][field_type]" 
                            class="form-control form-control-sm field-type-select" required onchange="toggleOptions(this)">
                            @foreach($fieldTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-2">
                        <label class="small">{{ __('Required') }}</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="sections[__SECTION_INDEX__][questions][__QUESTION_INDEX__][required]" 
                                class="custom-control-input" id="required___SECTION_INDEX_____QUESTION_INDEX__">
                            <label class="custom-control-label" for="required___SECTION_INDEX_____QUESTION_INDEX__">{{ __('Yes') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 text-right">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="removeQuestion(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="row options-row" style="display: none;">
                <div class="col-md-12">
                    <div class="form-group mb-2">
                        <label class="small">{{ __('Options') }} <small class="text-muted">({{ __('one per line') }})</small></label>
                        <textarea name="sections[__SECTION_INDEX__][questions][__QUESTION_INDEX__][options]" 
                            class="form-control form-control-sm" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-2">
                <a class="small text-primary" data-toggle="collapse" href="#advanced___SECTION_INDEX_____QUESTION_INDEX__">
                    <i class="fas fa-cog"></i> {{ __('Advanced Options') }}
                </a>
                <div class="collapse mt-2" id="advanced___SECTION_INDEX_____QUESTION_INDEX__">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="small">{{ __('Flag Type') }}</label>
                                <select name="sections[__SECTION_INDEX__][questions][__QUESTION_INDEX__][flag_type]" class="form-control form-control-sm">
                                    <option value="">{{ __('No Flag') }}</option>
                                    <option value="soft">{{ __('Soft Flag (Warning)') }}</option>
                                    <option value="hard">{{ __('Hard Flag (Block)') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="small">{{ __('Flag When Value') }}</label>
                                <input type="text" name="sections[__SECTION_INDEX__][questions][__QUESTION_INDEX__][flag_value]" 
                                    class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="small">{{ __('Flag Message') }}</label>
                                <input type="text" name="sections[__SECTION_INDEX__][questions][__QUESTION_INDEX__][flag_message]" 
                                    class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                <label class="small">{{ __('Doctor Notes') }}</label>
                                <input type="text" name="sections[__SECTION_INDEX__][questions][__QUESTION_INDEX__][doctor_notes]" 
                                    class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
let sectionCount = {{ $questionnaire->sections->count() }};
let questionCounts = {
    @foreach($questionnaire->sections as $index => $section)
    {{ $index }}: {{ $section->questions->count() }},
    @endforeach
};

function addSection() {
    const template = document.getElementById('sectionTemplate').innerHTML;
    const html = template.replace(/__SECTION_INDEX__/g, sectionCount);
    document.getElementById('sectionsList').insertAdjacentHTML('beforeend', html);
    questionCounts[sectionCount] = 0;
    addQuestion(sectionCount);
    sectionCount++;
}

function removeSection(btn) {
    if (document.querySelectorAll('.section-card').length <= 1) {
        alert('{{ __("At least one section is required") }}');
        return;
    }
    btn.closest('.section-card').remove();
}

function addQuestion(sectionIndex) {
    const template = document.getElementById('questionTemplate').innerHTML;
    const questionIndex = questionCounts[sectionIndex] || 0;
    let html = template.replace(/__SECTION_INDEX__/g, sectionIndex);
    html = html.replace(/__QUESTION_INDEX__/g, questionIndex);
    document.getElementById('questions' + sectionIndex).insertAdjacentHTML('beforeend', html);
    questionCounts[sectionIndex] = questionIndex + 1;
}

function removeQuestion(btn) {
    const container = btn.closest('.questions-container');
    if (container.querySelectorAll('.question-card').length <= 1) {
        alert('{{ __("At least one question is required per section") }}');
        return;
    }
    btn.closest('.question-card').remove();
}

function toggleOptions(select) {
    const optionsRow = select.closest('.card-body').querySelector('.options-row');
    const needsOptions = ['dropdown', 'radio', 'checkbox'].includes(select.value);
    optionsRow.style.display = needsOptions ? 'flex' : 'none';
}
</script>
@endpush

