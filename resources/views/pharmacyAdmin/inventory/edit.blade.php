@extends('layout.mainlayout_admin',['activePage' => 'pharmacy-inventory'])

@section('title',__('Edit Inventory Item'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Edit Inventory Item'),
        'url' => url('pharmacy-inventory'),
        'urlTitle' => __('Inventory Management')
    ])
    <form action="{{ url('pharmacy-inventory/'.$inventory->id) }}" method="post" class="myform">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label class="col-form-label">{{__('Medicine')}}</label>
                    <input type="text" value="{{$inventory->medicine->name ?? 'N/A'}} @if($inventory->medicine && $inventory->medicine->strength) ({{$inventory->medicine->strength}}) @endif" class="form-control" disabled>
                    <small class="form-text text-muted">{{__('Medicine cannot be changed after creation')}}</small>
                </div>

                <div class="form-group">
                    <label class="col-form-label">{{__('Brand')}}</label>
                    <input type="text" value="{{$inventory->brand->brand_name ?? 'N/A'}}" class="form-control" disabled>
                    <small class="form-text text-muted">{{__('Brand cannot be changed after creation')}}</small>
                </div>

                <div class="form-group">
                    <label class="col-form-label">{{__('Price')}} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" value="{{ old('price', $inventory->price) }}" name="price" class="form-control @error('price') is-invalid @enderror" required>
                    @error('price')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="col-form-label">{{__('Quantity')}} <span class="text-danger">*</span></label>
                    <input type="number" min="0" value="{{ old('quantity', $inventory->quantity) }}" name="quantity" class="form-control @error('quantity') is-invalid @enderror" required>
                    @error('quantity')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="col-form-label">{{__('Low Stock Threshold')}} <span class="text-danger">*</span></label>
                    <input type="number" min="0" value="{{ old('low_stock_threshold', $inventory->low_stock_threshold) }}" name="low_stock_threshold" class="form-control @error('low_stock_threshold') is-invalid @enderror" required>
                    <small class="form-text text-muted">{{__('Notification will be sent when quantity falls below this number')}}</small>
                    @error('low_stock_threshold')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="col-form-label">{{__('Current Stock Status')}}</label>
                    <div>
                        @if($inventory->stock_status == 'in_stock')
                            <span class="badge badge-success">{{__('In Stock')}}</span>
                        @elseif($inventory->stock_status == 'low_stock')
                            <span class="badge badge-warning">{{__('Low Stock')}}</span>
                        @else
                            <span class="badge badge-danger">{{__('Out of Stock')}}</span>
                        @endif
                    </div>
                    <small class="form-text text-muted">{{__('Status is automatically updated based on quantity and threshold')}}</small>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
                    <a href="{{url('pharmacy-inventory')}}" class="btn btn-secondary">{{__('Cancel')}}</a>
                </div>
            </div>
        </div>
    </form>
</section>

@endsection
