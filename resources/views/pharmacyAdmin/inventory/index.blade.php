@extends('layout.mainlayout_admin',['activePage' => 'pharmacy-inventory'])

@section('title',__('Inventory Management'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Inventory Management'),
    ])
    @if (session('status'))
        @include('superAdmin.auth.status',['status' => session('status')])
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            @include('superAdmin.auth.exportButtons')
            <a href="{{url('pharmacy-inventory/create')}}" class="btn btn-primary">{{__('Add New')}}</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="w-100 display table datatable text-center">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th>{{__('Medicine')}}</th>
                            <th>{{__('Brand')}}</th>
                            <th>{{__('Price')}}</th>
                            <th>{{__('Quantity')}}</th>
                            <th>{{__('Low Stock Threshold')}}</th>
                            <th>{{__('Stock Status')}}</th>
                            <th> {{__('Action')}} </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventory as $item)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$item->medicine->name ?? 'N/A'}}</td>
                                <td>{{$item->brand->brand_name ?? 'N/A'}}</td>
                                <td>{{$item->price}}</td>
                                <td>{{$item->quantity}}</td>
                                <td>{{$item->low_stock_threshold}}</td>
                                <td>
                                    @if($item->stock_status == 'in_stock')
                                        <span class="badge badge-success">{{__('In Stock')}}</span>
                                    @elseif($item->stock_status == 'low_stock')
                                        <span class="badge badge-warning">{{__('Low Stock')}}</span>
                                    @else
                                        <span class="badge badge-danger">{{__('Out of Stock')}}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{url('pharmacy-inventory/'.$item->id.'/edit')}}" class="text-success">
                                        <i class="far fa-edit"></i>
                                    </a>
                                    <form action="{{url('pharmacy-inventory/'.$item->id)}}" method="post" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm bg-white" onclick="confirm('{{__('Are you sure you want to delete this item?')}}')">
                                            <i class="far fa-trash-alt text-danger"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection
