{{--
  Recursive sub-question renderer.
  Variables:
    $subQuestion  — array from option_behaviors.behaviors[*].sub_question
    $parentPath   — dot-style path, e.g. "42" or "42.sq_abc1"
    $depth        — 1, 2, or 3
    $savedSubAnswers — flat array keyed by temp_id, from session (optional)
--}}
@php
    $tempId     = $subQuestion['temp_id']   ?? '';
    $fieldType  = $subQuestion['field_type'] ?? 'text';
    $label      = $subQuestion['label']      ?? '';
    $required   = $subQuestion['required']   ?? false;
    $placeholder= $subQuestion['placeholder'] ?? '';
    $options    = $subQuestion['options']    ?? [];
    $behaviors  = $subQuestion['behaviors']  ?? [];
    $path       = $parentPath . '.' . $tempId;
    $inputName  = 'sub_answer_' . str_replace('.', '_', $path);
    $savedVal   = $savedSubAnswers[$tempId] ?? null;
@endphp

<div class="sub-question-wrapper hidden ml-4 mt-3 pl-3 border-l-2 border-primary"
     data-temp-id="{{ $tempId }}"
     data-path="{{ $path }}"
     data-depth="{{ $depth }}"
     data-behaviors='@json($behaviors)'>

    <label class="block font-body font-semibold text-gray-700 mb-2 text-sm">
        {{ $label }}
        @if($required)<span class="text-danger ml-1">*</span>@endif
    </label>

    @switch($fieldType)

        @case('text')
        @case('number')
            <input type="{{ $fieldType }}"
                   name="{{ $inputName }}"
                   class="sub-question-input w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary"
                   placeholder="{{ $placeholder }}"
                   value="{{ $savedVal ?? '' }}"
                   data-temp-id="{{ $tempId }}">
            @break

        @case('textarea')
            <textarea name="{{ $inputName }}"
                      class="sub-question-input w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary"
                      rows="3"
                      placeholder="{{ $placeholder }}"
                      data-temp-id="{{ $tempId }}">{{ $savedVal ?? '' }}</textarea>
            @break

        @case('radio')
            <div class="flex flex-wrap gap-2">
                @foreach($options as $opt)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio"
                           name="{{ $inputName }}"
                           value="{{ $opt }}"
                           class="sub-question-input"
                           data-temp-id="{{ $tempId }}"
                           {{ ($savedVal === $opt) ? 'checked' : '' }}>
                    <span class="text-sm">{{ $opt }}</span>
                </label>
                @endforeach
            </div>
            @break

        @case('dropdown')
            <select name="{{ $inputName }}"
                    class="sub-question-input w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary"
                    data-temp-id="{{ $tempId }}">
                <option value="">{{ __('Select an option') }}</option>
                @foreach($options as $opt)
                    <option value="{{ $opt }}" {{ ($savedVal === $opt) ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
            @break

        @case('checkbox')
            @php $savedChecks = is_array($savedVal) ? $savedVal : (is_string($savedVal) ? json_decode($savedVal, true) ?? [] : []); @endphp
            <div class="flex flex-wrap gap-2">
                @foreach($options as $opt)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox"
                           name="{{ $inputName }}[]"
                           value="{{ $opt }}"
                           class="sub-question-input"
                           data-temp-id="{{ $tempId }}"
                           {{ in_array($opt, $savedChecks) ? 'checked' : '' }}>
                    <span class="text-sm">{{ $opt }}</span>
                </label>
                @endforeach
            </div>
            @break

    @endswitch

    <div class="text-red-500 text-xs mt-1" id="sq_error_{{ str_replace('.', '_', $path) }}"></div>

    {{-- Container for nested sub-questions triggered by THIS sub-question's answer --}}
    <div class="nested-sub-questions-container" data-parent-path="{{ $path }}">
        @foreach($behaviors as $behavior)
            @if(!empty($behavior['sub_question']))
                @include('website.questionnaire.partials.sub_question_render', [
                    'subQuestion'      => $behavior['sub_question'],
                    'parentPath'       => $path,
                    'depth'            => $depth + 1,
                    'savedSubAnswers'  => $savedSubAnswers ?? [],
                ])
            @endif
        @endforeach
    </div>
</div>
