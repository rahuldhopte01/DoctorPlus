@extends('layout.mainlayout_admin',['activePage' => 'questionnaire'])

@section('title',__('Edit Questionnaire'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',['title' => __('Edit Questionnaire')])

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
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
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
                                <label>{{ __('Category') }}</label>
                                <input type="text" class="form-control" value="{{ $questionnaire->category->name ?? 'N/A' }}@if($questionnaire->category && $questionnaire->category->treatment) ({{ $questionnaire->category->treatment->name }})@endif" disabled>
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
                                        <div class="btn-group-vertical mr-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="moveSectionUp(this)" {{ $sectionIndex === 0 ? 'disabled' : '' }}><i class="fas fa-chevron-up" style="font-size:10px"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="moveSectionDown(this)" {{ $sectionIndex === $questionnaire->sections->count()-1 ? 'disabled' : '' }}><i class="fas fa-chevron-down" style="font-size:10px"></i></button>
                                        </div>
                                        <input type="text" name="sections[{{ $sectionIndex }}][name]" class="form-control form-control-sm" value="{{ $section->name }}" required style="max-width:300px">
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeSection(this)"><i class="fas fa-trash"></i></button>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <input type="text" name="sections[{{ $sectionIndex }}][description]" class="form-control form-control-sm" value="{{ $section->description }}" placeholder="{{ __('Section description (optional)') }}">
                                    </div>

                                    <div class="questions-container" id="questions{{ $sectionIndex }}">
                                        @foreach($section->questions as $questionIndex => $question)
                                        <div class="card question-card mb-2" data-question-index="{{ $questionIndex }}"
                                             data-option-behaviors='@json($question->option_behaviors ?? [])'>
                                            <div class="card-body p-3">
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <div class="btn-group-vertical">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="moveQuestionUp(this)" {{ $questionIndex === 0 ? 'disabled' : '' }}><i class="fas fa-chevron-up" style="font-size:10px"></i></button>
                                                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="moveQuestionDown(this)" {{ $questionIndex === $section->questions->count()-1 ? 'disabled' : '' }}><i class="fas fa-chevron-down" style="font-size:10px"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="form-group mb-2">
                                                            <label class="small">{{ __('Question Text') }} <span class="text-danger">*</span></label>
                                                            <textarea name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][question_text]" class="form-control form-control-sm" rows="2" required>{{ $question->question_text }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group mb-2">
                                                            <label class="small">{{ __('Field Type') }} <span class="text-danger">*</span></label>
                                                            <select name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][field_type]" class="form-control form-control-sm field-type-select" required onchange="toggleOptions(this)">
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
                                                                <input type="checkbox" name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][required]" class="custom-control-input" id="req_{{ $sectionIndex }}_{{ $questionIndex }}" {{ $question->required ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="req_{{ $sectionIndex }}_{{ $questionIndex }}">{{ __('Yes') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 text-right">
                                                        <button type="button" class="btn btn-danger btn-sm mt-4" onclick="removeQuestion(this)"><i class="fas fa-times"></i></button>
                                                    </div>
                                                </div>

                                                <div class="row options-row" style="{{ in_array($question->field_type, ['dropdown','radio','checkbox']) ? 'display:flex' : 'display:none' }}">
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-2">
                                                            <label class="small">{{ __('Options') }} <small class="text-muted">({{ __('one per line') }})</small></label>
                                                            <textarea name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][options]" class="form-control form-control-sm" rows="3">{{ is_array($question->options) ? implode("\n", $question->options) : '' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Advanced Options --}}
                                                <div class="mt-2">
                                                    <a class="small text-primary" data-toggle="collapse" href="#adv_{{ $sectionIndex }}_{{ $questionIndex }}">
                                                        <i class="fas fa-cog"></i> {{ __('Advanced Options') }}
                                                    </a>
                                                    <div class="collapse mt-2" id="adv_{{ $sectionIndex }}_{{ $questionIndex }}">
                                                        <div class="row mb-2">
                                                            <div class="col-md-12">
                                                                <label class="small">{{ __('Doctor Notes') }}</label>
                                                                <input type="text" name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][doctor_notes]" class="form-control form-control-sm" value="{{ $question->doctor_notes }}">
                                                            </div>
                                                        </div>

                                                        <div class="behaviors-builder border rounded p-2 bg-light">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <strong class="small">{{ __('Behaviors (Flags & Sub-Questions)') }}</strong>
                                                                <button type="button" class="btn btn-outline-success btn-xs" onclick="addBehaviorToBuilder(this)">
                                                                    <i class="fas fa-plus"></i> {{ __('Add Behavior') }}
                                                                </button>
                                                            </div>
                                                            <small class="text-muted d-block mb-2">{{ __('Each behavior: when answer matches a value → show flag and/or a sub-question.') }}</small>
                                                            <div class="behaviors-list"></div>
                                                        </div>

                                                        <input type="hidden" name="sections[{{ $sectionIndex }}][questions][{{ $questionIndex }}][option_behaviors_json]" class="behaviors-json-input">
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

{{-- ===== SECTION TEMPLATE (for new sections added dynamically) ===== --}}
<template id="sectionTemplate">
    <div class="card section-card mb-3" data-section-index="__SI__">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center flex-grow-1">
                <div class="btn-group-vertical mr-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="moveSectionUp(this)"><i class="fas fa-chevron-up" style="font-size:10px"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="moveSectionDown(this)"><i class="fas fa-chevron-down" style="font-size:10px"></i></button>
                </div>
                <input type="text" name="sections[__SI__][name]" class="form-control form-control-sm" placeholder="{{ __('Section Name') }}" required style="max-width:300px">
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeSection(this)"><i class="fas fa-trash"></i></button>
        </div>
        <div class="card-body">
            <div class="form-group">
                <input type="text" name="sections[__SI__][description]" class="form-control form-control-sm" placeholder="{{ __('Section description (optional)') }}">
            </div>
            <div class="questions-container" id="questions__SI__"></div>
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addQuestion(__SI__)">
                <i class="fas fa-plus"></i> {{ __('Add Question') }}
            </button>
        </div>
    </div>
</template>

{{-- ===== QUESTION TEMPLATE ===== --}}
<template id="questionTemplate">
    <div class="card question-card mb-2" data-question-index="__QI__">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-1">
                    <div class="btn-group-vertical">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="moveQuestionUp(this)"><i class="fas fa-chevron-up" style="font-size:10px"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="moveQuestionDown(this)"><i class="fas fa-chevron-down" style="font-size:10px"></i></button>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group mb-2">
                        <label class="small">{{ __('Question Text') }} <span class="text-danger">*</span></label>
                        <textarea name="sections[__SI__][questions][__QI__][question_text]" class="form-control form-control-sm" rows="2" required></textarea>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-2">
                        <label class="small">{{ __('Field Type') }} <span class="text-danger">*</span></label>
                        <select name="sections[__SI__][questions][__QI__][field_type]" class="form-control form-control-sm field-type-select" required onchange="toggleOptions(this)">
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
                            <input type="checkbox" name="sections[__SI__][questions][__QI__][required]" class="custom-control-input" id="req___SI_____QI__">
                            <label class="custom-control-label" for="req___SI_____QI__">{{ __('Yes') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 text-right">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="removeQuestion(this)"><i class="fas fa-times"></i></button>
                </div>
            </div>

            <div class="row options-row" style="display:none">
                <div class="col-md-12">
                    <div class="form-group mb-2">
                        <label class="small">{{ __('Options') }} <small class="text-muted">({{ __('one per line') }})</small></label>
                        <textarea name="sections[__SI__][questions][__QI__][options]" class="form-control form-control-sm" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-2">
                <a class="small text-primary" data-toggle="collapse" href="#adv___SI_____QI__">
                    <i class="fas fa-cog"></i> {{ __('Advanced Options') }}
                </a>
                <div class="collapse mt-2" id="adv___SI_____QI__">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label class="small">{{ __('Doctor Notes') }}</label>
                            <input type="text" name="sections[__SI__][questions][__QI__][doctor_notes]" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="behaviors-builder border rounded p-2 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="small">{{ __('Behaviors (Flags & Sub-Questions)') }}</strong>
                            <button type="button" class="btn btn-outline-success btn-xs" onclick="addBehaviorToBuilder(this)">
                                <i class="fas fa-plus"></i> {{ __('Add Behavior') }}
                            </button>
                        </div>
                        <small class="text-muted d-block mb-2">{{ __('Each behavior: when answer matches a value → show flag and/or a sub-question.') }}</small>
                        <div class="behaviors-list"></div>
                    </div>
                    <input type="hidden" name="sections[__SI__][questions][__QI__][option_behaviors_json]" class="behaviors-json-input">
                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@section('js')
<style>
.behavior-block{border:1px solid #dee2e6;border-radius:4px;padding:8px;margin-bottom:6px;background:#fff}
.sub-question-panel{border:1px solid #17a2b8;border-radius:4px;padding:8px;margin-top:6px;background:#f0fbff}
.sub-behaviors-area{margin-top:6px;padding-left:10px;border-left:2px solid #17a2b8}
.flag-row{background:#fff8e1;border:1px solid #ffc107;border-radius:3px;padding:4px 6px;margin-bottom:4px}
.btn-xs{font-size:0.7rem!important;padding:0.15rem 0.35rem!important}
.depth-badge{font-size:0.65rem;background:#6c757d;color:#fff;border-radius:3px;padding:1px 4px;margin-left:4px}
</style>

<script>
let sectionCount = {{ $questionnaire->sections->count() }};
let questionCounts = {
    @foreach($questionnaire->sections as $index => $section)
    {{ $index }}: {{ $section->questions->count() }},
    @endforeach
};

const FIELD_TYPES_MAP = @json($fieldTypes);

// ─── Section / Question management ────────────────────────────────────────────
function addSection() {
    const html = document.getElementById('sectionTemplate').innerHTML.replace(/__SI__/g, sectionCount);
    document.getElementById('sectionsList').insertAdjacentHTML('beforeend', html);
    questionCounts[sectionCount] = 0;
    addQuestion(sectionCount);
    sectionCount++;
}

function removeSection(btn) {
    if (document.querySelectorAll('.section-card').length <= 1) { alert('At least one section is required'); return; }
    btn.closest('.section-card').remove();
}

function addQuestion(si) {
    const qi = questionCounts[si] || 0;
    let html = document.getElementById('questionTemplate').innerHTML;
    html = html.replace(/__SI__/g, si).replace(/__QI__/g, qi);
    document.getElementById('questions' + si).insertAdjacentHTML('beforeend', html);
    questionCounts[si] = qi + 1;
}

function removeQuestion(btn) {
    const container = btn.closest('.questions-container');
    if (container.querySelectorAll('.question-card').length <= 1) { alert('At least one question is required per section'); return; }
    btn.closest('.question-card').remove();
}

function toggleOptions(select) {
    const row = select.closest('.card-body').querySelector('.options-row');
    row.style.display = ['dropdown','radio','checkbox'].includes(select.value) ? 'flex' : 'none';
}

// ─── Behaviors builder (shared with create view) ──────────────────────────────
function addBehaviorToBuilder(btn) {
    const list = btn.closest('.behaviors-builder').querySelector('.behaviors-list');
    renderBehaviorBlock(list, 1, null);
}

function renderBehaviorBlock(listEl, depth, data) {
    const blockId = 'blk_' + Math.random().toString(36).substr(2, 9);
    const opOpts = [['equals','Equals'],['not_equals','Not equals'],['contains','Contains'],['greater_than','Greater than'],['less_than','Less than']]
        .map(([v,l]) => `<option value="${v}"${data && data.condition && data.condition.operator===v?' selected':''}>${l}</option>`).join('');
    const condVal = data && data.condition ? (data.condition.value||'') : '';
    const subQBtn = depth < 3 ? `
        <button type="button" class="btn btn-outline-info btn-xs mt-1" onclick="addSubQuestionToBlock(this,${depth+1})"><i class="fas fa-plus-circle"></i> Add Sub-Question</button>
        <div class="sub-question-panel-container"></div>` : '';

    listEl.insertAdjacentHTML('beforeend', `
    <div class="behavior-block" data-block-id="${blockId}">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small font-weight-bold">Behavior <span class="depth-badge">depth ${depth}</span></span>
            <button type="button" class="btn btn-outline-danger btn-xs" onclick="this.closest('.behavior-block').remove()"><i class="fas fa-times"></i></button>
        </div>
        <div class="row no-gutters mb-1">
            <div class="col-5 pr-1"><label class="small mb-0">When answer:</label><select class="form-control form-control-sm behavior-operator">${opOpts}</select></div>
            <div class="col-7"><label class="small mb-0">Value:</label><input type="text" class="form-control form-control-sm behavior-value" placeholder="e.g. Yes" value="${escHtml(condVal)}"></div>
        </div>
        <div class="mb-1">
            <div class="d-flex justify-content-between align-items-center">
                <span class="small text-muted font-weight-bold">Flags:</span>
                <button type="button" class="btn btn-outline-warning btn-xs" onclick="addFlagToBlock(this)"><i class="fas fa-flag"></i> Add Flag</button>
            </div>
            <div class="flags-list mt-1"></div>
        </div>
        <div class="sub-question-area">${subQBtn}</div>
    </div>`);

    const block = listEl.querySelector(`[data-block-id="${blockId}"]`);
    if (data && data.flags) data.flags.forEach(f => renderFlagRow(block.querySelector('.flags-list'), f));
    if (data && data.sub_question && depth < 3) addSubQuestionToBlock(block.querySelector('.sub-question-area > button'), depth+1, data.sub_question);
}

function addFlagToBlock(btn) { renderFlagRow(btn.closest('.mb-1').querySelector('.flags-list'), null); }

function renderFlagRow(flagsList, data) {
    const ss = (!data||data.flag_type==='soft')?' selected':'';
    const hs = (data&&data.flag_type==='hard')?' selected':'';
    const msg = data ? escHtml(data.flag_message||'') : '';
    flagsList.insertAdjacentHTML('beforeend',`
    <div class="flag-row d-flex align-items-center mb-1">
        <select class="form-control form-control-sm flag-type mr-1" style="max-width:130px">
            <option value="soft"${ss}>Soft (Warning)</option><option value="hard"${hs}>Hard (Block)</option>
        </select>
        <input type="text" class="form-control form-control-sm flag-message mr-1" placeholder="Flag message for doctor" value="${msg}">
        <button type="button" class="btn btn-outline-danger btn-xs" onclick="this.closest('.flag-row').remove()"><i class="fas fa-times"></i></button>
    </div>`);
}

function addSubQuestionToBlock(btn, depth, data) {
    const area = btn.closest('.sub-question-area');
    const container = area.querySelector('.sub-question-panel-container');
    if (container.querySelector('.sub-question-panel')) return;
    btn.style.display = 'none';
    const tempId = data ? data.temp_id : ('sq_'+Math.random().toString(36).substr(2,8));
    const label  = data ? escHtml(data.label||'') : '';
    const ph     = data ? escHtml(data.placeholder||'') : '';
    const req    = (data&&data.required)?'checked':'';
    const ft     = data ? (data.field_type||'text') : 'text';
    const ftOpts = Object.entries(FIELD_TYPES_MAP).map(([v,l])=>`<option value="${v}"${ft===v?' selected':''}>${l}</option>`).join('');
    const needsOpts = ['dropdown','radio','checkbox'].includes(ft);
    const rawOpts   = data && data.options ? (Array.isArray(data.options)?data.options.join('\n'):data.options) : '';
    const sqId      = 'sqr_'+tempId;
    const subBehArea = depth<=3 ? `
        <div class="sub-behaviors-area">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small text-muted font-weight-bold">Sub-Behaviors:</span>
                <button type="button" class="btn btn-outline-success btn-xs" onclick="addSubBehavior(this,${depth})"><i class="fas fa-plus"></i> Add</button>
            </div>
            <div class="sub-behaviors-list"></div>
        </div>` : '';

    container.insertAdjacentHTML('beforeend',`
    <div class="sub-question-panel" data-temp-id="${tempId}">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small font-weight-bold text-info">Sub-Question <span class="depth-badge">depth ${depth-1}</span></span>
            <button type="button" class="btn btn-outline-danger btn-xs" onclick="removeSubQuestionPanel(this)"><i class="fas fa-times"></i></button>
        </div>
        <div class="row no-gutters mb-1">
            <div class="col-8 pr-1"><input type="text" class="form-control form-control-sm sq-label" placeholder="Sub-question label" value="${label}"></div>
            <div class="col-4"><select class="form-control form-control-sm sq-field-type" onchange="toggleSqOptions(this)">${ftOpts}</select></div>
        </div>
        <div class="row no-gutters mb-1">
            <div class="col-8 pr-1"><input type="text" class="form-control form-control-sm sq-placeholder" placeholder="Placeholder (optional)" value="${ph}"></div>
            <div class="col-4 d-flex align-items-center pl-1">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input sq-required" id="${sqId}" ${req}>
                    <label class="custom-control-label small" for="${sqId}">Required</label>
                </div>
            </div>
        </div>
        <div class="sq-options-row mb-1" style="display:${needsOpts?'block':'none'}">
            <label class="small mb-0">Options (one per line):</label>
            <textarea class="form-control form-control-sm sq-options" rows="3">${escHtml(rawOpts)}</textarea>
        </div>
        ${subBehArea}
    </div>`);

    if (data && data.behaviors) {
        const subList = container.querySelector('.sub-behaviors-list');
        if (subList) data.behaviors.forEach(b => renderBehaviorBlock(subList, depth, b));
    }
}

function addSubBehavior(btn, depth) { renderBehaviorBlock(btn.closest('.sub-behaviors-area').querySelector('.sub-behaviors-list'), depth, null); }
function removeSubQuestionPanel(btn) {
    const panel = btn.closest('.sub-question-panel');
    const addBtn = panel.closest('.sub-question-area').querySelector('button[onclick*="addSubQuestionToBlock"]');
    if (addBtn) addBtn.style.display = '';
    panel.remove();
}
function toggleSqOptions(select) {
    select.closest('.sub-question-panel').querySelector('.sq-options-row').style.display = ['dropdown','radio','checkbox'].includes(select.value)?'block':'none';
}

// ─── Serialise ────────────────────────────────────────────────────────────────
function serializeAllBehaviors() {
    document.querySelectorAll('.question-card').forEach(qCard => {
        const input     = qCard.querySelector('.behaviors-json-input');
        const bList     = qCard.querySelector('.behaviors-list');
        if (!input || !bList) return;
        const behaviors = [];
        bList.querySelectorAll(':scope > .behavior-block').forEach(b => behaviors.push(serializeBlock(b)));
        input.value = JSON.stringify({ behaviors });
    });
}

function serializeBlock(block) {
    const operator = block.querySelector(':scope > .row > .col-5 .behavior-operator').value;
    const value    = block.querySelector(':scope > .row > .col-7 .behavior-value').value;
    const flags    = [];
    block.querySelectorAll(':scope > .mb-1 > .flags-list > .flag-row').forEach(fr => {
        flags.push({ flag_type: fr.querySelector('.flag-type').value, flag_message: fr.querySelector('.flag-message').value });
    });
    let subQuestion = null;
    const sqPanel = block.querySelector(':scope > .sub-question-area > .sub-question-panel-container > .sub-question-panel');
    if (sqPanel) subQuestion = serializeSqPanel(sqPanel);
    return { condition: { operator, value }, flags, sub_question: subQuestion };
}

function serializeSqPanel(panel) {
    const tempId    = panel.dataset.tempId;
    const label     = panel.querySelector('.sq-label').value;
    const fieldType = panel.querySelector('.sq-field-type').value;
    const ph        = panel.querySelector('.sq-placeholder').value;
    const required  = panel.querySelector('.sq-required').checked;
    let options = null;
    const optTa = panel.querySelector('.sq-options');
    if (optTa && ['dropdown','radio','checkbox'].includes(fieldType)) {
        options = optTa.value.split('\n').map(s=>s.trim()).filter(Boolean);
    }
    const behaviors = [];
    const subList = panel.querySelector('.sub-behaviors-list');
    if (subList) subList.querySelectorAll(':scope > .behavior-block').forEach(b => behaviors.push(serializeBlock(b)));
    return { temp_id: tempId, label, field_type: fieldType, options, required, placeholder: ph, behaviors };
}

// ─── Reindex ──────────────────────────────────────────────────────────────────
function reindexForm() {
    document.querySelectorAll('.section-card').forEach((section, si) => {
        section.setAttribute('data-section-index', si);
        section.querySelectorAll('[name^="sections["]').forEach(field => {
            const n = field.getAttribute('name');
            if (n) field.setAttribute('name', n.replace(/sections\[\d+\]/, `sections[${si}]`));
        });
        section.querySelectorAll('.question-card').forEach((question, qi) => {
            question.setAttribute('data-question-index', qi);
            question.querySelectorAll('[name*="[questions]["]').forEach(field => {
                const n = field.getAttribute('name');
                if (n) field.setAttribute('name', n.replace(/\[questions\]\[\d+\]/, `[questions][${qi}]`));
            });
            const advLink = question.querySelector('[href^="#adv_"]');
            const advPanel = question.querySelector('.collapse[id^="adv_"]');
            if (advLink) advLink.setAttribute('href', `#adv_${si}_${qi}`);
            if (advPanel) advPanel.id = `adv_${si}_${qi}`;
        });
    });
}

function moveSectionUp(btn) { const s=btn.closest('.section-card'),p=s.previousElementSibling; if(p) s.parentNode.insertBefore(s,p); reindexForm(); }
function moveSectionDown(btn) { const s=btn.closest('.section-card'),n=s.nextElementSibling; if(n) s.parentNode.insertBefore(n,s); reindexForm(); }
function moveQuestionUp(btn) { const q=btn.closest('.question-card'),p=q.previousElementSibling; if(p) q.parentNode.insertBefore(q,p); reindexForm(); }
function moveQuestionDown(btn) { const q=btn.closest('.question-card'),n=q.nextElementSibling; if(n) q.parentNode.insertBefore(n,q); reindexForm(); }

function escHtml(str) { return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// ─── Init: deserialize existing option_behaviors back into builder UI ─────────
$(document).ready(function() {
    // For each existing question card that has option_behaviors data, populate the builder
    document.querySelectorAll('.question-card[data-option-behaviors]').forEach(qCard => {
        const raw = qCard.getAttribute('data-option-behaviors');
        if (!raw || raw === '[]' || raw === 'null') return;
        let data;
        try { data = JSON.parse(raw); } catch(e) { return; }
        if (!data || !data.behaviors || !data.behaviors.length) return;

        const bList = qCard.querySelector('.behaviors-list');
        if (!bList) return;

        data.behaviors.forEach(b => renderBehaviorBlock(bList, 1, b));
    });

    $('#questionnaireForm').on('submit', function() {
        reindexForm();
        serializeAllBehaviors();
    });
    reindexForm();
});
</script>
@endsection
