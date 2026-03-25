{{--
  Recursive sub-answer renderer for doctor review.
  Variables:
    $subAnswers  — array of sub-answer items (each has temp_id, label, field_type, value, is_flagged, flag_reason, sub_answers)
    $depth       — current nesting depth (1, 2, 3)
--}}
@foreach($subAnswers as $sub)
@php
    $indentPx = ($depth - 1) * 20;
    $val = $sub['value'] ?? null;
    $displayVal = is_array($val) ? implode(', ', $val) : ($val ?? '—');
    $isFlagged = !empty($sub['is_flagged']);
    $flagReason = $sub['flag_reason'] ?? null;
    $nested = $sub['sub_answers'] ?? [];
@endphp
<tr class="{{ $isFlagged ? 'bg-danger-light' : 'bg-light' }}" style="font-size:0.88em;">
    <td class="text-center text-muted" style="padding-left: {{ $indentPx + 12 }}px;">
        <i class="fas fa-level-up-alt fa-rotate-90 text-muted" style="font-size:0.75em;"></i>
    </td>
    <td style="padding-left: {{ $indentPx + 12 }}px;" class="text-dark">
        <span class="badge badge-secondary mr-1" style="font-size:0.7em;">{{ __('Sub') }}</span>
        {{ $sub['label'] ?? '' }}
        @if($isFlagged)
            <span class="badge badge-danger ml-1"><i class="fas fa-flag"></i></span>
        @endif
    </td>
    <td>
        <span class="font-weight-bold text-dark">{{ $displayVal }}</span>
        @if($isFlagged && $flagReason)
            <div class="mt-1 p-1 border border-danger rounded" style="background-color:#fff5f5;">
                <small class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i><strong>{{ __('Flag:') }}</strong> {{ $flagReason }}</small>
            </div>
        @endif
    </td>
    <td></td>
</tr>
@if(!empty($nested))
    @include('doctor.questionnaire.partials.sub_answer_display', [
        'subAnswers' => $nested,
        'depth'      => $depth + 1,
    ])
@endif
@endforeach
