@extends('layout.mainlayout_admin',['activePage' => 'questionnaire'])

@section('title',__('Questionnaires'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Questionnaires'),
    ])
    <div class="section-body">
        @if (session('status'))
            @include('superAdmin.auth.status',['status' => session('status')])
        @endif
        <div class="card">
            <div class="card-header w-100 text-right d-flex justify-content-between">
                @include('superAdmin.auth.exportButtons')
                @can('questionnaire_add')
                    <a href="{{ url('questionnaire/create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Add New Questionnaire') }}
                    </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="w-100 display table datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{__('Category')}}</th>
                                <th>{{__('Treatment')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Sections')}}</th>
                                <th>{{__('Version')}}</th>
                                <th>{{__('Status')}}</th>
                                @if (Gate::check('questionnaire_edit') || Gate::check('questionnaire_delete'))
                                    <th>{{__('Action')}}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questionnaires as $questionnaire)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($questionnaire->category)
                                            <span class="badge badge-primary">{{ $questionnaire->category->name }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($questionnaire->category && $questionnaire->category->treatment)
                                            <span class="badge badge-info">{{ $questionnaire->category->treatment->name }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $questionnaire->name }}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $questionnaire->sections_count }} {{ __('sections') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light">v{{ $questionnaire->version }}</span>
                                    </td>
                                    <td>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" id="status{{ $questionnaire->id }}" 
                                                class="custom-switch-input" name="status" 
                                                onchange="change_status('questionnaire/change-status', {{ $questionnaire->id }})" 
                                                {{ $questionnaire->status == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </td>
                                    @if (Gate::check('questionnaire_edit') || Gate::check('questionnaire_delete'))
                                        <td>
                                            @can('questionnaire_access')
                                            <a class="text-info mr-2" href="{{ url('questionnaire/'.$questionnaire->id) }}" title="{{__('View')}}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('questionnaire_edit')
                                            <a class="text-success mr-2" href="{{ url('questionnaire/'.$questionnaire->id.'/edit') }}" title="{{__('Edit')}}">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('questionnaire_delete')
                                            <a class="text-danger" href="javascript:void(0);" onclick="deleteData('questionnaire', {{ $questionnaire->id }})" title="{{__('Delete')}}">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection



