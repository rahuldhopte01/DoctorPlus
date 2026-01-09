@extends('layout.mainlayout_admin',['activePage' => 'questionnaire'])

@section('title',__('View Questionnaire'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('View Questionnaire'),
    ])

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>{{ $questionnaire->name }}</h4>
                <div class="card-header-action">
                    <span class="badge badge-{{ $questionnaire->status ? 'success' : 'secondary' }}">
                        {{ $questionnaire->status ? __('Active') : __('Inactive') }}
                    </span>
                    <span class="badge badge-info ml-2">v{{ $questionnaire->version }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">{{ __('Category') }}</h6>
                        <p class="mb-0">
                            <span class="badge badge-primary">{{ $questionnaire->category->name ?? 'N/A' }}</span>
                            @if($questionnaire->category && $questionnaire->category->treatment)
                                <span class="badge badge-info ml-2">{{ $questionnaire->category->treatment->name }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">{{ __('Description') }}</h6>
                        <p class="mb-0">{{ $questionnaire->description ?: 'No description' }}</p>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">{{ __('Sections & Questions') }}</h5>

                @foreach($questionnaire->sections as $section)
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-folder text-primary mr-2"></i>
                            {{ $section->name }}
                        </h6>
                        @if($section->description)
                            <small class="text-muted">{{ $section->description }}</small>
                        @endif
                    </div>
                    <div class="card-body">
                        @foreach($section->questions as $question)
                        <div class="border rounded p-3 mb-2 {{ $loop->last ? 'mb-0' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="mb-1 font-weight-bold">
                                        {{ $loop->iteration }}. {{ $question->question_text }}
                                        @if($question->required)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge badge-secondary">{{ $question::FIELD_TYPES[$question->field_type] ?? $question->field_type }}</span>
                                        
                                        @if($question->flagging_rules)
                                            @php $flagType = $question->flagging_rules['flag_type'] ?? 'soft'; @endphp
                                            <span class="badge badge-{{ $flagType == 'hard' ? 'danger' : 'warning' }}">
                                                <i class="fas fa-flag"></i> {{ ucfirst($flagType) }} Flag
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($question->requiresOptions() && !empty($question->options))
                                    <div class="mt-2">
                                        <small class="text-muted">{{ __('Options:') }}</small>
                                        <ul class="mb-0 pl-3">
                                            @foreach($question->options as $option)
                                                <li><small>{{ $option }}</small></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif

                                    @if($question->doctor_notes)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-stethoscope"></i> {{ __('Doctor Note:') }}
                                        </small>
                                        <small class="text-info">{{ $question->doctor_notes }}</small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            <div class="card-footer text-right">
                <a href="{{ url('questionnaire') }}" class="btn btn-secondary mr-2">{{ __('Back') }}</a>
                @can('questionnaire_edit')
                <a href="{{ url('questionnaire/'.$questionnaire->id.'/edit') }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>
                @endcan
            </div>
        </div>
    </div>
</section>
@endsection



