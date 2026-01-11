@extends('layout.mainlayout_admin',['activePage' => 'medicine'])

@section('title',__('Pharmacy Inventory Details'))

@section('content')
<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Pharmacy Inventory Details'),
        'url' => url('pharmacy_inventory'),
        'urlTitle' => __('Pharmacy Inventory')
    ])
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Medicine Name')}}</label>
                        <div class="form-control-plaintext">
                            {{$inventory->medicine->name ?? __('N/A')}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Brand')}}</label>
                        <div class="form-control-plaintext">
                            {{$inventory->medicine->brand->name ?? __('N/A')}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Strength')}}</label>
                        <div class="form-control-plaintext">
                            {{$inventory->medicine->strength ?? __('N/A')}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Form')}}</label>
                        <div class="form-control-plaintext">
                            {{$inventory->medicine->form ?? __('N/A')}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Price')}}</label>
                        <div class="form-control-plaintext">
                            {{ $currency }}{{number_format($inventory->price, 2)}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Quantity')}}</label>
                        <div class="form-control-plaintext">
                            {{$inventory->quantity}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Low Stock Threshold')}}</label>
                        <div class="form-control-plaintext">
                            {{$inventory->low_stock_threshold}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Stock Status')}}</label>
                        <div class="form-control-plaintext">
                            @if($inventory->quantity == 0)
                                <span class="btn btn-sm bg-danger-light">{{__('Out of Stock')}}</span>
                            @elseif($inventory->quantity <= $inventory->low_stock_threshold)
                                <span class="btn btn-sm bg-warning-light">{{__('Low Stock')}}</span>
                            @else
                                <span class="btn btn-sm bg-success-light">{{__('In Stock')}}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right mt-4">
                <a href="{{url('pharmacy_inventory')}}" class="btn btn-secondary">{{__('Back')}}</a>
                @can('medicine_edit')
                <a href="{{url('pharmacy_inventory/'.$inventory->id.'/edit')}}" class="btn btn-primary">{{__('Edit')}}</a>
                @endcan
            </div>
        </div>
    </div>
</section>
@endsection
