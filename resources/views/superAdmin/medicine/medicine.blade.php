@extends('layout.mainlayout_admin',['activePage' => 'medicine'])

@section('title',__('All Medicine'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Medicine'),
        'url' => url('medicine'),
        'urlTitle' => __('Medicine'),
    ])
    <div class="section_body">
        @if (session('status'))
        @include('superAdmin.auth.status',[
            'status' => session('status')])
        @endif
        <div class="card">
            <div class="card-header d-flex justify-content-between mt-2 p-1">
                @include('superAdmin.auth.exportButtons')
                @can('admin_medicine_add')
                    <a href="{{url('medicine/create')}}" class="btn btn-primary float-right">{{__('Add New')}}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="w-100 display table datatable text-center">
                        <thead>
                            <tr>
                                <th>
                                    <input name="select_all" value="1" id="master" type="checkbox" />
                                    <label for="master"></label>
                                </th>
                                <th> # </th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Strength')}}</th>
                                <th>{{__('Form')}}</th>
                                <th>{{__('Brand')}}</th>
                                <th>{{__('Status')}}</th>
                                @if (Gate::check('admin_medicine_edit') || Gate::check('admin_medicine_delete'))
                                    <th> {{__('Action')}} </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($medicines as $medicine)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="id[]" value="{{$medicine->id}}" id="{{$medicine->id}}" data-id="{{ $medicine->id }}" class="sub_chk">
                                        <label for="{{$medicine->id}}"></label>
                                    </td>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$medicine->name}}</td>
                                    <td>{{$medicine->strength ?? __('N/A')}}</td>
                                    <td>{{$medicine->form ?? __('N/A')}}</td>
                                    <td>{{$medicine->brand->name ?? __('N/A')}}</td>
                                    <td>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" id="status{{$medicine->id}}" class="custom-switch-input" name="status" onchange="change_status('medicine',{{ $medicine->id }})" {{ $medicine->status == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </td>
                                    <td>
                                        @if (Gate::check('admin_medicine_edit') || Gate::check('admin_medicine_delete'))
                                            @can('admin_medicine_edit')
                                            <a class="text-success" href="{{url('medicine/'.$medicine->id.'/edit')}}">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('admin_medicine_delete')
                                            <a class="text-danger" href="javascript:void(0);" onclick="deleteData('medicine',{{ $medicine->id }})">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" onclick="deleteAll('medicine_all_delete')" class="btn btn-primary">{{__('delete selected')}}</button>
            </div>
        </div>
    </div>
</section>
@endsection
