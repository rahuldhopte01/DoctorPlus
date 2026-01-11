@extends('layout.mainlayout_admin',['activePage' => 'medicine'])

@section('title',__('Add Pharmacy Inventory'))

@section('content')
<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Add Pharmacy Inventory'),
        'url' => url('pharmacy_inventory'),
        'urlTitle' => __('Pharmacy Inventory')
    ])
    <form action="{{ url('pharmacy_inventory') }}" method="post" class="myform">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label class="col-form-label">{{__('Medicine')}} <span class="text-danger">*</span></label>
                    <select name="medicine_id" class="select2 @error('medicine_id') is-invalid @enderror" required>
                        <option value="">{{__('Select Medicine')}}</option>
                        @foreach ($medicines as $medicine)
                            <option value="{{$medicine->id}}" {{ old('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                {{$medicine->name}} 
                                @if($medicine->strength)
                                    - {{$medicine->strength}}
                                @endif
                                @if($medicine->form)
                                    ({{$medicine->form}})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('medicine_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="col-form-label">{{__('Price')}} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" value="{{ old('price') }}" name="price" class="form-control @error('price') is-invalid @enderror" required>
                    @error('price')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="col-form-label">{{__('Quantity')}} <span class="text-danger">*</span></label>
                    <input type="number" min="0" value="{{ old('quantity', 0) }}" name="quantity" class="form-control @error('quantity') is-invalid @enderror" required>
                    @error('quantity')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="col-form-label">{{__('Low Stock Threshold')}}</label>
                    <input type="number" min="0" value="{{ old('low_stock_threshold', 0) }}" name="low_stock_threshold" class="form-control @error('low_stock_threshold') is-invalid @enderror">
                    @error('low_stock_threshold')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <small class="form-text text-muted">{{__('Alert when quantity falls below this number')}}</small>
                </div>
                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection
