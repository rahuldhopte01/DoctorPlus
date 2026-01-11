@extends('layout.mainlayout_admin',['activePage' => 'medicineBrand'])

@section('title',__('All Medicine Brand'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Medicine Brand'),
    ])
    <div class="section_body">
        @if (session('status'))
        @include('superAdmin.auth.status',[
            'status' => session('status')])
        @endif
        <div class="card">
            <div class="card-header w-100 text-right d-flex justify-content-between">
                @include('superAdmin.auth.exportButtons')
                @can('medicine_category_add')
                    <a href="{{ url('medicineBrand/create') }}">{{ __('Add New') }}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="w-100 display table datatable">
                        <thead>
                            <tr>
                                <th>
                                    <input name="select_all" value="1" id="master" type="checkbox" />
                                    <label for="master"></label>
                                </th>
                                <th> # </th>
                                <th>{{__('Brand Name')}}</th>
                                @if (Gate::check('medicine_category_edit') || Gate::check('medicine_category_delete'))
                                    <th> {{__('Action')}} </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($medicineBrands as $medicineBrand)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="id[]" value="{{$medicineBrand->id}}" id="{{$medicineBrand->id}}" data-id="{{ $medicineBrand->id }}" class="sub_chk">
                                        <label for="{{$medicineBrand->id}}"></label>
                                    </td>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$medicineBrand->name}}</td>
                                    @if (Gate::check('medicine_category_edit') || Gate::check('medicine_category_delete'))
                                        <td>
                                            @can('medicine_category_edit')
                                            <a class="text-success" href="{{url('medicineBrand/'.$medicineBrand->id.'/edit/')}}">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('medicine_category_delete')
                                                <a class="text-danger" href="javascript:void(0);" onclick="deleteData('medicineBrand',{{ $medicineBrand->id }})">
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
            <div class="card-footer">
                <input type="button" value="delete selected" onclick="deleteAll('medicineBrand_all_delete')" class="btn btn-primary">
            </div>
        </div>
    </div>
</section>

@endsection
