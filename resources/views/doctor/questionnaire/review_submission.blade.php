@extends('layout.mainlayout_admin',['activePage' => 'questionnaire_submissions'])

@section('title', __('Questionnaire Review'))

@section('content')
<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Questionnaire Review'),
    ])

    <div class="section-body">
        <!-- Patient & Questionnaire Info -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-user mr-2"></i>{{ __('Patient Information') }}</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">{{ __('Name') }}</td>
                                <td><strong>{{ $firstAnswer->user->name ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Email') }}</td>
                                <td>{{ $firstAnswer->user->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Phone') }}</td>
                                <td>{{ $firstAnswer->user->phone ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-clipboard-list mr-2"></i>{{ __('Questionnaire Info') }}</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">{{ __('Questionnaire') }}</td>
                                <td><strong>{{ $firstAnswer->questionnaire->name ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Category') }}</td>
                                <td>{{ $firstAnswer->category->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Version') }}</td>
                                <td>v{{ $firstAnswer->questionnaire_version ?? '1' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Submitted At') }}</td>
                                <td>{{ $firstAnswer->submitted_at ? \Carbon\Carbon::parse($firstAnswer->submitted_at)->format('M d, Y H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Status') }}</td>
                                <td>
                                    @if($firstAnswer->status === 'pending')
                                        <span class="badge badge-warning">{{ __('Pending') }}</span>
                                    @elseif($firstAnswer->status === 'under_review')
                                        <span class="badge badge-info">{{ __('Under Review') }}</span>
                                    @elseif($firstAnswer->status === 'approved')
                                        <span class="badge badge-success">{{ __('Approved') }}</span>
                                    @elseif($firstAnswer->status === 'rejected')
                                        <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Update Form -->
        <div class="card mb-3">
            <div class="card-header">
                <h4><i class="fas fa-edit mr-2"></i>{{ __('Update Status') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('doctor.questionnaire.update-status', [
                    'userId' => $firstAnswer->user_id,
                    'categoryId' => $firstAnswer->category_id,
                    'questionnaireId' => $firstAnswer->questionnaire_id
                ]) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Status') }}</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" {{ $firstAnswer->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="under_review" {{ $firstAnswer->status === 'under_review' ? 'selected' : '' }}>{{ __('Under Review') }}</option>
                                    <option value="approved" {{ $firstAnswer->status === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                    <option value="rejected" {{ $firstAnswer->status === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-save mr-2"></i>{{ __('Update Status') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                
                @if($firstAnswer->status === 'approved')
                <div class="mt-4 pt-4 border-top">
                    @if($prescription)
                    <div class="alert alert-info">
                        <i class="fas fa-prescription-bottle-alt mr-2"></i>
                        <strong>{{ __('Prescription Already Created') }}</strong>
                        <p class="mb-0 mt-2">{{ __('A prescription has been created for this questionnaire. View details below.') }}</p>
                    </div>
                    @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>{{ __('Questionnaire Approved') }}</strong>
                        <p class="mb-0 mt-2">{{ __('You can now create a prescription for this patient.') }}</p>
                    </div>
                    <a href="{{ route('doctor.questionnaire.create-prescription', [
                        'userId' => $firstAnswer->user_id,
                        'categoryId' => $firstAnswer->category_id,
                        'questionnaireId' => $firstAnswer->questionnaire_id
                    ]) }}" class="btn btn-success btn-lg">
                        <i class="fas fa-prescription-bottle-alt mr-2"></i>
                        {{ __('Create Prescription') }}
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Prescription Details -->
        @if($prescription)
        <div class="card mb-3">
            <div class="card-header">
                <h4><i class="fas fa-prescription-bottle-alt mr-2"></i>{{ __('Prescription Details') }}</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">{{ __('Status') }}</td>
                                <td>
                                    @if($prescription->status === 'approved_pending_payment')
                                        <span class="badge badge-warning">{{ __('Approved - Pending Payment') }}</span>
                                    @elseif($prescription->status === 'active')
                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                    @elseif($prescription->status === 'approved')
                                        <span class="badge badge-info">{{ __('Approved') }}</span>
                                    @elseif($prescription->status === 'expired')
                                        <span class="badge badge-danger">{{ __('Expired') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $prescription->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Valid From') }}</td>
                                <td>{{ $prescription->valid_from ? \Carbon\Carbon::parse($prescription->valid_from)->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Valid Until') }}</td>
                                <td>{{ $prescription->valid_until ? \Carbon\Carbon::parse($prescription->valid_until)->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Validity Days') }}</td>
                                <td>{{ $prescription->validity_days ?? 'N/A' }} {{ __('days') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <h5 class="mb-3">{{ __('Medicines') }}</h5>
                @php
                    $medicines = json_decode($prescription->medicines, true);
                @endphp
                @if($medicines && count($medicines) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="60%">{{ __('Medicine Name') }}</th>
                                <th width="35%">{{ __('Strength') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicines as $index => $medicine)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $medicine['medicine'] ?? 'N/A' }}</strong></td>
                                <td>{{ $medicine['strength'] ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ __('No medicines found in this prescription.') }}
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Flag Alert -->
        @if($hasFlaggedAnswers)
        <div class="alert alert-warning mb-3">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>{{ __('Attention:') }}</strong> {{ __('This questionnaire contains flagged answers that require your review.') }}
        </div>
        @endif

        <!-- Questionnaire Answers -->
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-file-medical-alt mr-2"></i>{{ __('Questionnaire Answers') }}</h4>
            </div>
            <div class="card-body">
                @forelse($groupedAnswers as $sectionName => $sectionAnswers)
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-folder-open text-primary mr-2"></i>
                        {{ $sectionName }}
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="40%">{{ __('Question') }}</th>
                                    <th width="35%">{{ __('Answer') }}</th>
                                    <th width="20%">{{ __('Notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sectionAnswers as $index => $answer)
                                <tr class="{{ $answer['is_flagged'] ? 'table-warning' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $answer['question'] }}
                                        @if($answer['is_flagged'])
                                            <span class="badge badge-danger ml-2">
                                                <i class="fas fa-flag"></i> {{ __('Flagged') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($answer['field_type'] === 'file' && $answer['file_url'])
                                            <a href="{{ $answer['file_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download mr-1"></i>{{ __('Download File') }}
                                            </a>
                                            @if(isset($answer['file_name']))
                                                <br><small class="text-muted d-block mt-1">
                                                    <i class="fas fa-file mr-1"></i>{{ basename($answer['file_name']) }}
                                                </small>
                                            @endif
                                        @else
                                            <div class="answer-text">
                                                {{ $answer['answer'] }}
                                            </div>
                                        @endif
                                        
                                        @if($answer['is_flagged'] && $answer['flag_reason'])
                                            <div class="mt-2 p-2 bg-danger-light rounded">
                                                <small class="text-danger font-weight-bold">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    <strong>{{ __('Flag Reason:') }}</strong> {{ $answer['flag_reason'] }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($answer['doctor_notes'])
                                            <small class="text-info d-block">
                                                <i class="fas fa-sticky-note mr-1"></i>
                                                {{ $answer['doctor_notes'] }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ __('No questionnaire answers found.') }}</p>
                </div>
                @endforelse
            </div>
            <div class="card-footer">
                <a href="{{ route('doctor.questionnaire.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to List') }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
