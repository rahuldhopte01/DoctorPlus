@extends('layout.mainlayout_admin',['activePage' => 'appointment'])

@section('title', __('Questionnaire Review'))

@section('content')
<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Questionnaire Review'),
    ])

    <div class="section-body">
        <!-- Patient & Appointment Info -->
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
                                <td><strong>{{ $appointment->patient_name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Age') }}</td>
                                <td>{{ $appointment->age }} {{ __('years') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Phone') }}</td>
                                <td>{{ $appointment->phone_no }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Appointment ID') }}</td>
                                <td><code>{{ $appointment->appointment_id }}</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Date') }}</td>
                                <td>{{ $appointment->date }} {{ $appointment->time }}</td>
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
                                <td><strong>{{ $appointment->questionnaire->name ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Version') }}</td>
                                <td>v{{ $appointment->questionnaire->version ?? '1' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Completed At') }}</td>
                                <td>{{ $appointment->questionnaire_completed_at ? \Carbon\Carbon::parse($appointment->questionnaire_completed_at)->format('M d, Y H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Status') }}</td>
                                <td>
                                    @if($appointment->questionnaire_locked)
                                        <span class="badge badge-secondary"><i class="fas fa-lock mr-1"></i>{{ __('Locked') }}</span>
                                    @else
                                        <span class="badge badge-success"><i class="fas fa-unlock mr-1"></i>{{ __('Editable') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flag Alert -->
        @if($hasFlaggedAnswers)
        <div class="alert alert-warning">
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
                @forelse($groupedAnswers as $sectionName => $answers)
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
                                @foreach($answers as $index => $answer)
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
                                                <i class="fas fa-download mr-1"></i>{{ __('View File') }}
                                            </a>
                                            <br><small class="text-muted">{{ $answer['answer'] }}</small>
                                        @else
                                            <strong>{{ $answer['answer'] }}</strong>
                                        @endif
                                        
                                        @if($answer['is_flagged'] && $answer['flag_reason'])
                                            <div class="mt-1">
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                    {{ $answer['flag_reason'] }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($answer['doctor_notes'])
                                            <small class="text-info">
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
                <div class="d-flex justify-content-between">
                    <a href="{{ url('doctor_home') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Appointments') }}
                    </a>
                    <div>
                        @if($appointment->appointment_status === 'pending')
                            <a href="{{ url('acceptAppointment/'.$appointment->id) }}" class="btn btn-success mr-2">
                                <i class="fas fa-check mr-2"></i>{{ __('Accept Appointment') }}
                            </a>
                            <a href="{{ url('cancelAppointment/'.$appointment->id) }}" class="btn btn-danger">
                                <i class="fas fa-times mr-2"></i>{{ __('Reject Appointment') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

