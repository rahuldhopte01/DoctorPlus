@extends('layout.mainlayout_admin',['activePage' => 'medicine_master'])

@section('title',__('Medicine Master'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Medicine Master'),
    ])
    <div class="section-body">
        @if (session('status'))
            @include('superAdmin.auth.status',['status' => session('status')])
        @endif
        <div class="card">
            <div class="card-header w-100 text-right d-flex justify-content-between">
                @include('superAdmin.auth.exportButtons')
                @can('medicine_add')
                    <a href="{{ url('medicine_master/create') }}">{{ __('Add New') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="w-100 display table datatable">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>{{__('Medicine Name')}}</th>
                                <th>{{__('Strength')}}</th>
                                <th>{{__('Form')}}</th>
                                <th>{{__('Brands')}}</th>
                                <th>{{__('Status')}}</th>
                                @if (Gate::check('medicine_edit') || Gate::check('medicine_delete'))
                                    <th> {{__('Action')}} </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($medicines as $medicine)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$medicine->name}}</td>
                                    <td>{{$medicine->strength ?? 'N/A'}}</td>
                                    <td>{{$medicine->form ?? 'N/A'}}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $medicine->brands->count() }} {{__('brands')}}</span>
                                    </td>
                                    <td>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" id="status{{$medicine->id}}" class="custom-switch-input" name="status" onchange="change_status('medicine_master',{{ $medicine->id }})" {{ $medicine->status == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </td>
                                    @if (Gate::check('medicine_edit') || Gate::check('medicine_delete'))
                                        <td>
                                            @can('medicine_edit')
                                            <a class="text-success" href="{{url('medicine_master/'.$medicine->id.'/edit/')}}">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('medicine_access')
                                            <a class="text-info ml-2" href="{{url('medicine_master/'.$medicine->id)}}">
                                                <i class="far fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('medicine_delete')
                                            <a class="text-danger ml-2" href="javascript:void(0);" onclick="deleteData('medicine_master',{{ $medicine->id }})">
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
