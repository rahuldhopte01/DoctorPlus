@extends('layout.mainlayout_admin',['activePage' => 'questionnaire'])

@section('title',__('Create Questionnaire'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',['title' => __('Create Questionnaire')])

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>{{ __('Questionnaire Details') }}</h4>
            </div>
            <form action="{{ url('questionnaire') }}" method="POST" id="questionnaireForm">
                @csrf
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id">{{ __('Category') }} <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-control select2" required>
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $selectedCategory && $selectedCategory->id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}@if($category->treatment) ({{ $category->treatment->name }})@endif
                                        </option>
                                    @endforeach
                                </select>
                                @if($categories->isEmpty())
                                    <small class="text-warning">{{ __('All categories already have questionnaires assigned.') }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('Questionnaire Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="{{ __('e.g., Medical History Intake') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">{{ __('Description') }}</label>
                                <textarea name="description" id="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="custom-switch pl-0">
                                    <input type="checkbox" name="status" class="custom-switch-input" checked>
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
                        <div id="sectionsList"></div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="{{ url('questionnaire') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Create Questionnaire') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- ===== SECTION TEMPLATE ===== --}}
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
                        <textarea name="sections[__SI__][questions][__QI__][question_text]" class="form-control form-control-sm" rows="2" required placeholder="{{ __('Enter your question here...') }}"></textarea>
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
                        <textarea name="sections[__SI__][questions][__QI__][options]" class="form-control form-control-sm" rows="3" placeholder="{{ __('Option 1') }}&#10;{{ __('Option 2') }}&#10;{{ __('Option 3') }}"></textarea>
                    </div>
                </div>
            </div>

            {{-- Advanced Options (collapsible) --}}
            <div class="mt-2">
                <a class="small text-primary" data-toggle="collapse" href="#adv___SI_____QI__">
                    <i class="fas fa-cog"></i> {{ __('Advanced Options') }}
                </a>
                <div class="collapse mt-2" id="adv___SI_____QI__">

                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label class="small">{{ __('Doctor Notes') }}</label>
                            <input type="text" name="sections[__SI__][questions][__QI__][doctor_notes]" class="form-control form-control-sm" placeholder="{{ __('Notes visible to doctor during review') }}">
                        </div>
                    </div>

                    {{-- Behaviors builder --}}
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

                    {{-- Hidden JSON input — populated on submit --}}
                    <input type="hidden" name="sections[__SI__][questions][__QI__][option_behaviors_json]" class="behaviors-json-input">
                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@section('js')
<style>
.behavior-block { border:1px solid #dee2e6; border-radius:4px; padding:8px; margin-bottom:6px; background:#fff; }
.sub-question-panel { border:1px solid #17a2b8; border-radius:4px; padding:8px; margin-top:6px; background:#f0fbff; }
.sub-behaviors-area { margin-top:6px; padding-left:10px; border-left:2px solid #17a2b8; }
.flag-row { background:#fff8e1; border:1px solid #ffc107; border-radius:3px; padding:4px 6px; margin-bottom:4px; }
.btn-xs { font-size:0.7rem !important; padding:0.15rem 0.35rem !important; }
.depth-badge { font-size:0.65rem; background:#6c757d; color:#fff; border-radius:3px; padding:1px 4px; margin-left:4px; }
</style>

<script>
let sectionCount = 0;
let questionCounts = {};

const FIELD_TYPES_MAP = @json($fieldTypes);

const OPERATORS = {
    equals:       'Equals',
    not_equals:   'Not equals',
    contains:     'Contains',
    greater_than: 'Greater than',
    less_than:    'Less than',
};

// ─── Section / Question management ───────────────────────────────────────────
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

// ─── Behaviors builder ────────────────────────────────────────────────────────
function addBehaviorToBuilder(btn) {
    const list = btn.closest('.behaviors-builder').querySelector('.behaviors-list');
    renderBehaviorBlock(list, 1, null);
}

/**
 * @param {HTMLElement} listEl  — behaviors-list or sub-behaviors-list container
 * @param {int} depth           — 1, 2, or 3
 * @param {object|null} data    — pre-filled data for edit/deserialize
 */
function renderBehaviorBlock(listEl, depth, data) {
    const blockId = 'blk_' + Math.random().toString(36).substr(2, 9);

    const opOpts = Object.entries(OPERATORS).map(([v,l]) => {
        const s = (data && data.condition && data.condition.operator === v) ? ' selected' : '';
        return `<option value="${v}"${s}>${l}</option>`;
    }).join('');

    const condVal = data && data.condition ? (data.condition.value || '') : '';

    const subQBtn = depth < 3 ? `
        <button type="button" class="btn btn-outline-info btn-xs mt-1" onclick="addSubQuestionToBlock(this, ${depth + 1})">
            <i class="fas fa-plus-circle"></i> Add Sub-Question
        </button>
        <div class="sub-question-panel-container"></div>` : '';

    listEl.insertAdjacentHTML('beforeend', `
    <div class="behavior-block" data-block-id="${blockId}">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small font-weight-bold">Behavior <span class="depth-badge">depth ${depth}</span></span>
            <button type="button" class="btn btn-outline-danger btn-xs" onclick="this.closest('.behavior-block').remove()"><i class="fas fa-times"></i></button>
        </div>
        <div class="row no-gutters mb-1">
            <div class="col-5 pr-1">
                <label class="small mb-0">When answer:</label>
                <select class="form-control form-control-sm behavior-operator">${opOpts}</select>
            </div>
            <div class="col-7">
                <label class="small mb-0">Value:</label>
                <input type="text" class="form-control form-control-sm behavior-value" placeholder="e.g. Yes" value="${escHtml(condVal)}">
            </div>
        </div>
        <div class="mb-1">
            <div class="d-flex justify-content-between align-items-center">
                <span class="small text-muted font-weight-bold">Flags:</span>
                <button type="button" class="btn btn-outline-warning btn-xs" onclick="addFlagToBlock(this)"><i class="fas fa-flag"></i> Add Flag</button>
            </div>
            <div class="flags-list mt-1"></div>
        </div>
        <div class="sub-question-area">
            ${subQBtn}
        </div>
    </div>`);

    const block = listEl.querySelector(`[data-block-id="${blockId}"]`);

    // Pre-fill flags
    if (data && data.flags) {
        data.flags.forEach(f => renderFlagRow(block.querySelector('.flags-list'), f));
    }
    // Pre-fill sub_question
    if (data && data.sub_question && depth < 3) {
        addSubQuestionToBlock(block.querySelector('.sub-question-area > button'), depth + 1, data.sub_question);
    }
}

function addFlagToBlock(btn) {
    const flagsList = btn.closest('.mb-1').querySelector('.flags-list');
    renderFlagRow(flagsList, null);
}

function renderFlagRow(flagsList, data) {
    const softSel = (!data || data.flag_type === 'soft') ? ' selected' : '';
    const hardSel = (data && data.flag_type === 'hard') ? ' selected' : '';
    const msg = data ? escHtml(data.flag_message || '') : '';
    flagsList.insertAdjacentHTML('beforeend', `
    <div class="flag-row d-flex align-items-center mb-1">
        <select class="form-control form-control-sm flag-type mr-1" style="max-width:130px">
            <option value="soft"${softSel}>Soft (Warning)</option>
            <option value="hard"${hardSel}>Hard (Block)</option>
        </select>
        <input type="text" class="form-control form-control-sm flag-message mr-1" placeholder="Flag message for doctor" value="${msg}">
        <button type="button" class="btn btn-outline-danger btn-xs" onclick="this.closest('.flag-row').remove()"><i class="fas fa-times"></i></button>
    </div>`);
}

function addSubQuestionToBlock(btn, depth, data) {
    const area = btn.closest('.sub-question-area');
    const container = area.querySelector('.sub-question-panel-container');
    if (container.querySelector('.sub-question-panel')) return; // already exists
    btn.style.display = 'none';

    const tempId = data ? data.temp_id : ('sq_' + Math.random().toString(36).substr(2, 8));
    const label  = data ? escHtml(data.label || '') : '';
    const ph     = data ? escHtml(data.placeholder || '') : '';
    const req    = (data && data.required) ? 'checked' : '';
    const ft     = data ? (data.field_type || 'text') : 'text';

    const ftOpts = Object.entries(FIELD_TYPES_MAP).map(([v,l]) => {
        const s = ft === v ? ' selected' : '';
        return `<option value="${v}"${s}>${l}</option>`;
    }).join('');

    const needsOpts = ['dropdown','radio','checkbox'].includes(ft);
    const rawOpts   = data && data.options ? (Array.isArray(data.options) ? data.options.join('\n') : data.options) : '';
    const sqId      = 'sqr_' + tempId;

    const subBehArea = depth <= 3 ? `
        <div class="sub-behaviors-area">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small text-muted font-weight-bold">Sub-Behaviors:</span>
                <button type="button" class="btn btn-outline-success btn-xs" onclick="addSubBehavior(this, ${depth})"><i class="fas fa-plus"></i> Add</button>
            </div>
            <div class="sub-behaviors-list"></div>
        </div>` : '';

    container.insertAdjacentHTML('beforeend', `
    <div class="sub-question-panel" data-temp-id="${tempId}">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small font-weight-bold text-info">Sub-Question <span class="depth-badge">depth ${depth - 1}</span></span>
            <button type="button" class="btn btn-outline-danger btn-xs" onclick="removeSubQuestionPanel(this)"><i class="fas fa-times"></i></button>
        </div>
        <div class="row no-gutters mb-1">
            <div class="col-8 pr-1">
                <input type="text" class="form-control form-control-sm sq-label" placeholder="Sub-question label / text" value="${label}">
            </div>
            <div class="col-4">
                <select class="form-control form-control-sm sq-field-type" onchange="toggleSqOptions(this)">${ftOpts}</select>
            </div>
        </div>
        <div class="row no-gutters mb-1">
            <div class="col-8 pr-1">
                <input type="text" class="form-control form-control-sm sq-placeholder" placeholder="Placeholder (optional)" value="${ph}">
            </div>
            <div class="col-4 d-flex align-items-center pl-1">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input sq-required" id="${sqId}" ${req}>
                    <label class="custom-control-label small" for="${sqId}">Required</label>
                </div>
            </div>
        </div>
        <div class="sq-options-row mb-1" style="display:${needsOpts ? 'block' : 'none'}">
            <label class="small mb-0">Options (one per line):</label>
            <textarea class="form-control form-control-sm sq-options" rows="3">${escHtml(rawOpts)}</textarea>
        </div>
        ${subBehArea}
    </div>`);

    // Pre-fill sub-behaviors
    if (data && data.behaviors) {
        const subList = container.querySelector('.sub-behaviors-list');
        if (subList) {
            data.behaviors.forEach(b => renderBehaviorBlock(subList, depth, b));
        }
    }
}

function addSubBehavior(btn, depth) {
    const list = btn.closest('.sub-behaviors-area').querySelector('.sub-behaviors-list');
    renderBehaviorBlock(list, depth, null);
}

function removeSubQuestionPanel(btn) {
    const panel   = btn.closest('.sub-question-panel');
    const addBtn  = panel.closest('.sub-question-area').querySelector('button[onclick*="addSubQuestionToBlock"]');
    if (addBtn) addBtn.style.display = '';
    panel.remove();
}

function toggleSqOptions(select) {
    const row = select.closest('.sub-question-panel').querySelector('.sq-options-row');
    row.style.display = ['dropdown','radio','checkbox'].includes(select.value) ? 'block' : 'none';
}

// ─── Serialise behaviors to JSON ─────────────────────────────────────────────
function serializeAllBehaviors() {
    document.querySelectorAll('.question-card').forEach(qCard => {
        const hiddenInput = qCard.querySelector('.behaviors-json-input');
        if (!hiddenInput) return;
        const builderList = qCard.querySelector('.behaviors-list');
        if (!builderList) return;

        const behaviors = [];
        builderList.querySelectorAll(':scope > .behavior-block').forEach(block => {
            behaviors.push(serializeBlock(block));
        });
        hiddenInput.value = JSON.stringify({ behaviors });
    });
}

function serializeBlock(block) {
    const operator = block.querySelector(':scope > .row > .col-5 .behavior-operator').value;
    const value    = block.querySelector(':scope > .row > .col-7 .behavior-value').value;

    const flags = [];
    block.querySelectorAll(':scope > .mb-1 > .flags-list > .flag-row').forEach(fr => {
        flags.push({
            flag_type:    fr.querySelector('.flag-type').value,
            flag_message: fr.querySelector('.flag-message').value,
        });
    });

    let subQuestion = null;
    const sqPanel = block.querySelector(':scope > .sub-question-area > .sub-question-panel-container > .sub-question-panel');
    if (sqPanel) {
        subQuestion = serializeSubQuestionPanel(sqPanel);
    }

    return { condition: { operator, value }, flags, sub_question: subQuestion };
}

function serializeSubQuestionPanel(panel) {
    const tempId    = panel.dataset.tempId;
    const label     = panel.querySelector('.sq-label').value;
    const fieldType = panel.querySelector('.sq-field-type').value;
    const ph        = panel.querySelector('.sq-placeholder').value;
    const required  = panel.querySelector('.sq-required').checked;

    let options = null;
    const optTa = panel.querySelector('.sq-options');
    if (optTa && ['dropdown','radio','checkbox'].includes(fieldType)) {
        options = optTa.value.split('\n').map(s => s.trim()).filter(Boolean);
    }

    const behaviors = [];
    const subList = panel.querySelector('.sub-behaviors-list');
    if (subList) {
        subList.querySelectorAll(':scope > .behavior-block').forEach(b => behaviors.push(serializeBlock(b)));
    }

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
        section.querySelectorAll('[href^="#adv_"]').forEach(l => {
            l.setAttribute('href', l.getAttribute('href').replace(/adv_\d+_\d+/, `adv_${si}_0`));
        });
        section.querySelectorAll('.collapse[id^="adv_"]').forEach((c,ci) => { c.id = `adv_${si}_${ci}`; });

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

function moveSectionUp(btn) { const s = btn.closest('.section-card'), p = s.previousElementSibling; if (p) s.parentNode.insertBefore(s, p); reindexForm(); }
function moveSectionDown(btn) { const s = btn.closest('.section-card'), n = s.nextElementSibling; if (n) s.parentNode.insertBefore(n, s); reindexForm(); }
function moveQuestionUp(btn) { const q = btn.closest('.question-card'), p = q.previousElementSibling; if (p) q.parentNode.insertBefore(q, p); reindexForm(); }
function moveQuestionDown(btn) { const q = btn.closest('.question-card'), n = q.nextElementSibling; if (n) q.parentNode.insertBefore(n, q); reindexForm(); }

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

$(document).ready(function() {
    addSection();
    $('#questionnaireForm').on('submit', function() {
        reindexForm();
        serializeAllBehaviors();
    });
    setTimeout(reindexForm, 100);
});
</script>
@endsection
