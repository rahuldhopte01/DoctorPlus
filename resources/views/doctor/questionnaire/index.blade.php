@extends('layout.mainlayout_admin',['activePage' => 'questionnaire_submissions'])

@section('title', __('Questionnaire Submissions'))

@section('content')
<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Questionnaire Submissions'),
    ])

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-clipboard-list mr-2"></i>{{ __('Questionnaire Submissions') }}</h4>
            </div>
            <div class="card-body">
                @if(!$doctor->category_id)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ __('Your doctor profile does not have a category assigned. Please contact the administrator to assign a category.') }}
                </div>
                @endif

                @if($doctor->isAdminDoctor())
                <form method="GET" class="mb-3" id="filter-form">
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label>{{ __('Filter by Doctor') }}</label>
                                <select name="review_doctor_id" class="form-control">
                                    <option value="{{ $doctor->id }}" {{ (int)($selectedReviewDoctorId ?? $doctor->id) === $doctor->id ? 'selected' : '' }}>
                                        {{ __('My Questionnaires') }}
                                    </option>
                                    @foreach($reviewDoctors as $reviewDoctor)
                                        <option value="{{ $reviewDoctor->id }}" {{ (int)($selectedReviewDoctorId ?? 0) === $reviewDoctor->id ? 'selected' : '' }}>
                                            {{ $reviewDoctor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter mr-1"></i>{{ __('Apply') }}
                            </button>
                        </div>
                    </div>
                </form>
                @endif

                {{-- ============================================================
                     BULK APPROVE FORM — wraps both pending & under-review tables
                     ============================================================ --}}
                <form method="POST" action="{{ route('doctor.questionnaire.bulk-approve') }}" id="bulk-approve-form">
                    @csrf

                    {{-- Bulk action toolbar --}}
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <button type="submit" class="btn btn-success" id="bulk-approve-btn" disabled
                                onclick="return confirm('{{ __('Approve all selected questionnaires and auto-generate prescriptions using the medicines each patient selected? This cannot be undone.') }}')">
                                <i class="fas fa-check-double mr-1"></i>
                                {{ __('Bulk Approve & Generate Prescriptions') }}
                                <span class="badge badge-light ml-1" id="selected-count">0</span>
                            </button>
                        </div>
                        <small class="text-muted">
                            {{ __('Select pending or under-review questionnaires to approve them in one go.') }}
                        </small>
                    </div>

                    {{-- ===== UNDER REVIEW TABLE ===== --}}
                    <h5 class="mb-3">{{ __('Questionnaires Under Review') }}</h5>
                    @if($reviewSubmissions->count() > 0)
                    <div class="table-responsive mb-4">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width:40px;">
                                        <input type="checkbox" class="select-all-check" data-group="review"
                                            title="{{ __('Select all under review') }}">
                                    </th>
                                    <th>{{ __('Patient') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Questionnaire') }}</th>
                                    <th>{{ __('Submitted At') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Selected Medicines') }}</th>
                                    @if($doctor->isAdminDoctor())
                                    <th>{{ __('Reviewing Doctor') }}</th>
                                    @endif
                                    <th>{{ __('Flagged') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reviewSubmissions as $submission)
                                @php
                                    $submittedAtVal = $submission['submitted_at']
                                        ? \Carbon\Carbon::parse($submission['submitted_at'])->format('Y-m-d H:i:s')
                                        : '';
                                    $bulkKey = $submission['user']->id . '|' . $submission['category']->id . '|' . $submission['questionnaire']->id . '|' . $submittedAtVal;
                                    $status = strtolower($submission['status'] ?? '');
                                    $isBulkEligible = in_array($status, ['pending','under_review','in_review'])
                                        || in_array(strtoupper($submission['status'] ?? ''), ['IN_REVIEW','UNDER_REVIEW']);
                                @endphp
                                <tr>
                                    <td>
                                        @if($isBulkEligible)
                                        <input type="checkbox" class="submission-check" data-group="review"
                                            name="submissions[]" value="{{ $bulkKey }}">
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $submission['user']->name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $submission['user']->email ?? '' }}</small>
                                    </td>
                                    <td>{{ $submission['category']->name ?? 'N/A' }}</td>
                                    <td>{{ $submission['questionnaire']->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $submission['submitted_at'] ? \Carbon\Carbon::parse($submission['submitted_at'])->format('M d, Y H:i') : 'N/A' }}
                                    </td>
                                    <td>
                                        @if($status === 'pending')
                                            <span class="badge badge-warning">{{ __('Pending') }}</span>
                                        @elseif($status === 'under_review' || $status === 'in_review' || strtoupper($submission['status'] ?? '') === 'IN_REVIEW')
                                            <span class="badge badge-info">{{ __('Under Review') }}</span>
                                        @elseif($status === 'approved')
                                            <span class="badge badge-success">{{ __('Approved') }}</span>
                                        @elseif($status === 'rejected')
                                            <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                        @elseif($status === 'review_completed' || strtoupper($submission['status'] ?? '') === 'REVIEW_COMPLETED')
                                            <span class="badge badge-secondary">{{ __('Review Completed') }}</span>
                                        @elseif(!empty($submission['status']))
                                            <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $submission['status'])) }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('Unknown') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($submission['selected_medicines']))
                                            @foreach($submission['selected_medicines'] as $medicine)
                                                <span class="badge badge-light">
                                                    {{ $medicine['name'] }}
                                                    @if(!empty($medicine['strength']))
                                                        <small>({{ $medicine['strength'] }})</small>
                                                    @endif
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @if($doctor->isAdminDoctor())
                                    <td>
                                        {{ $submission['reviewing_doctor']->name ?? __('Unassigned') }}
                                    </td>
                                    @endif
                                    <td>
                                        @if($submission['flagged_count'] > 0)
                                            <span class="badge badge-danger">
                                                <i class="fas fa-flag"></i> {{ $submission['flagged_count'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $reviewUrl = route('doctor.questionnaire.show', [
                                                'userId' => $submission['user']->id,
                                                'categoryId' => $submission['category']->id,
                                                'questionnaireId' => $submission['questionnaire']->id
                                            ]);
                                            if ($submission['submitted_at']) {
                                                $reviewUrl .= '?submitted_at=' . urlencode(\Carbon\Carbon::parse($submission['submitted_at'])->format('Y-m-d H:i:s'));
                                            }
                                        @endphp
                                        <a href="{{ $reviewUrl }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> {{ __('Review') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 mb-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted">{{ __('No questionnaires under review found.') }}</p>
                    </div>
                    @endif

                    {{-- ===== PENDING TABLE ===== --}}
                    <h5 class="mb-3">{{ __('Pending Questionnaires') }}</h5>
                    @if($pendingSubmissions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width:40px;">
                                        <input type="checkbox" class="select-all-check" data-group="pending"
                                            title="{{ __('Select all pending') }}">
                                    </th>
                                    <th>{{ __('Patient') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Questionnaire') }}</th>
                                    <th>{{ __('Submitted At') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Selected Medicines') }}</th>
                                    @if($doctor->isAdminDoctor())
                                    <th>{{ __('Reviewing Doctor') }}</th>
                                    @endif
                                    <th>{{ __('Flagged') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingSubmissions as $submission)
                                @php
                                    $submittedAtValP = $submission['submitted_at']
                                        ? \Carbon\Carbon::parse($submission['submitted_at'])->format('Y-m-d H:i:s')
                                        : '';
                                    $bulkKeyP = $submission['user']->id . '|' . $submission['category']->id . '|' . $submission['questionnaire']->id . '|' . $submittedAtValP;
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" class="submission-check" data-group="pending"
                                            name="submissions[]" value="{{ $bulkKeyP }}">
                                    </td>
                                    <td>
                                        <strong>{{ $submission['user']->name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $submission['user']->email ?? '' }}</small>
                                    </td>
                                    <td>{{ $submission['category']->name ?? 'N/A' }}</td>
                                    <td>{{ $submission['questionnaire']->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $submission['submitted_at'] ? \Carbon\Carbon::parse($submission['submitted_at'])->format('M d, Y H:i') : 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ __('Pending') }}</span>
                                    </td>
                                    <td>
                                        @if(!empty($submission['selected_medicines']))
                                            @foreach($submission['selected_medicines'] as $medicine)
                                                <span class="badge badge-light">
                                                    {{ $medicine['name'] }}
                                                    @if(!empty($medicine['strength']))
                                                        <small>({{ $medicine['strength'] }})</small>
                                                    @endif
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @if($doctor->isAdminDoctor())
                                    <td>
                                        {{ $submission['reviewing_doctor']->name ?? __('Unassigned') }}
                                    </td>
                                    @endif
                                    <td>
                                        @if($submission['flagged_count'] > 0)
                                            <span class="badge badge-danger">
                                                <i class="fas fa-flag"></i> {{ $submission['flagged_count'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $reviewUrlPending = route('doctor.questionnaire.show', [
                                                'userId' => $submission['user']->id,
                                                'categoryId' => $submission['category']->id,
                                                'questionnaireId' => $submission['questionnaire']->id
                                            ]);
                                            if ($submission['submitted_at']) {
                                                $reviewUrlPending .= '?submitted_at=' . urlencode(\Carbon\Carbon::parse($submission['submitted_at'])->format('Y-m-d H:i:s'));
                                            }
                                        @endphp
                                        <a href="{{ $reviewUrlPending }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> {{ __('Review') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted">{{ __('No pending questionnaires found.') }}</p>
                    </div>
                    @endif

                </form>{{-- end bulk-approve-form --}}
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form       = document.getElementById('bulk-approve-form');
    var approveBtn = document.getElementById('bulk-approve-btn');
    var countBadge = document.getElementById('selected-count');

    function updateToolbar() {
        var checked = form.querySelectorAll('.submission-check:checked').length;
        countBadge.textContent = checked;
        approveBtn.disabled = checked === 0;
    }

    form.addEventListener('change', function (e) {
        // "Select all" header checkbox
        if (e.target.classList.contains('select-all-check')) {
            var group    = e.target.dataset.group;
            var children = form.querySelectorAll('.submission-check[data-group="' + group + '"]');
            children.forEach(function (cb) { cb.checked = e.target.checked; });
        }

        // Sync header indeterminate state when individual box changes
        if (e.target.classList.contains('submission-check')) {
            var group      = e.target.dataset.group;
            var allInGroup = form.querySelectorAll('.submission-check[data-group="' + group + '"]');
            var header     = form.querySelector('.select-all-check[data-group="' + group + '"]');
            if (header) {
                var allChecked  = Array.from(allInGroup).every(function (cb) { return cb.checked; });
                var someChecked = Array.from(allInGroup).some(function (cb) { return cb.checked; });
                header.checked       = allChecked;
                header.indeterminate = !allChecked && someChecked;
            }
        }

        updateToolbar();
    });
});
</script>
@endsection
