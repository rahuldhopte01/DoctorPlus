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
                
                @if($submissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Patient') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Questionnaire') }}</th>
                                <th>{{ __('Submitted At') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Flagged') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
                            <tr>
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
                                    @if($submission['status'] === 'pending')
                                        <span class="badge badge-warning">{{ __('Pending') }}</span>
                                    @elseif($submission['status'] === 'under_review')
                                        <span class="badge badge-info">{{ __('Under Review') }}</span>
                                    @elseif($submission['status'] === 'approved')
                                        <span class="badge badge-success">{{ __('Approved') }}</span>
                                    @elseif($submission['status'] === 'rejected')
                                        <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                    @endif
                                </td>
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
                                    <a href="{{ route('doctor.questionnaire.show', [
                                        'userId' => $submission['user']->id,
                                        'categoryId' => $submission['category']->id,
                                        'questionnaireId' => $submission['questionnaire']->id
                                    ]) }}" 
                                    class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> {{ __('Review') }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ __('No questionnaire submissions found.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
