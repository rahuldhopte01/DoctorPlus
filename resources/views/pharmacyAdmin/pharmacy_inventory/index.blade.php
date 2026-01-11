@extends('layout.mainlayout_admin',['activePage' => 'medicine'])

@section('title',__('Pharmacy Inventory'))

@section('content')
<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Pharmacy Inventory'),
    ])
    @if (session('status'))
    @include('superAdmin.auth.status',[
        'status' => session('status')])
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            @include('superAdmin.auth.exportButtons')
            @can('medicine_add')
                <a href="{{url('pharmacy_inventory/create')}}">{{__('Add New')}}</a>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="w-100 display table datatable text-center">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th>{{__('Medicine Name')}}</th>
                            <th>{{__('Brand')}}</th>
                            <th>{{__('Strength')}}</th>
                            <th>{{__('Form')}}</th>
                            <th>{{__('Price')}}</th>
                            <th>{{__('Quantity')}}</th>
                            <th>{{__('Low Stock Threshold')}}</th>
                            <th>{{__('Stock Status')}}</th>
                            @if (Gate::check('medicine_edit') || Gate::check('medicine_delete'))
                                <th> {{__('Action')}} </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventories as $inventory)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>
                                    {{$inventory->medicine->name ?? __('N/A')}}
                                </td>
                                <td>
                                    {{$inventory->medicine->brand->name ?? __('N/A')}}
                                </td>
                                <td>
                                    {{$inventory->medicine->strength ?? __('N/A')}}
                                </td>
                                <td>
                                    {{$inventory->medicine->form ?? __('N/A')}}
                                </td>
                                <td>{{ $currency }}{{number_format($inventory->price, 2)}}</td>
                                <td>{{$inventory->quantity}}</td>
                                <td>{{$inventory->low_stock_threshold}}</td>
                                <td>
                                    @if($inventory->quantity == 0)
                                        <span class="btn btn-sm bg-danger-light">{{__('Out of Stock')}}</span>
                                    @elseif($inventory->quantity <= $inventory->low_stock_threshold)
                                        <span class="btn btn-sm bg-warning-light">{{__('Low Stock')}}</span>
                                    @else
                                        <span class="btn btn-sm bg-success-light">{{__('In Stock')}}</span>
                                    @endif
                                </td>
                                @if (Gate::check('medicine_edit') || Gate::check('medicine_delete'))
                                    <td>
                                        @can('medicine_edit')
                                        <a class="text-success" href="{{url('pharmacy_inventory/'.$inventory->id.'/edit/')}}">
                                            <i class="far fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('medicine_delete')
                                        <a class="text-danger" href="javascript:void(0);" onclick="deleteData('pharmacy_inventory',{{ $inventory->id }})">
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
</section>
@endsection
