@extends('layout.mainlayout_admin',['activePage' => 'pharmacy-inventory'])

@section('title',__('Add Inventory Item'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Add Inventory Item'),
        'url' => url('pharmacy-inventory'),
        'urlTitle' => __('Inventory Management')
    ])
    <form action="{{ url('pharmacy-inventory') }}" method="post" class="myform">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label class="col-form-label">{{__('Medicine')}} <span class="text-danger">*</span></label>
                    <select name="medicine_id" id="medicine_id" class="select2 @error('medicine_id') is-invalid @enderror" required>
                        <option value="">{{__('Select Medicine')}}</option>
                        @foreach($medicines as $medicine)
                            <option value="{{$medicine->id}}" {{ old('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                {{$medicine->name}} @if($medicine->strength) ({{$medicine->strength}}) @endif
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
                    <label class="col-form-label">{{__('Brand')}} <span class="text-danger">*</span></label>
                    <select name="medicine_brand_id" id="medicine_brand_id" class="select2 @error('medicine_brand_id') is-invalid @enderror" required>
                        <option value="">{{__('Select Medicine First')}}</option>
                    </select>
                    @error('medicine_brand_id')
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
                    <label class="col-form-label">{{__('Low Stock Threshold')}} <span class="text-danger">*</span></label>
                    <input type="number" min="0" value="{{ old('low_stock_threshold', 10) }}" name="low_stock_threshold" class="form-control @error('low_stock_threshold') is-invalid @enderror" required>
                    <small class="form-text text-muted">{{__('Notification will be sent when quantity falls below this number')}}</small>
                    @error('low_stock_threshold')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                    <a href="{{url('pharmacy-inventory')}}" class="btn btn-secondary">{{__('Cancel')}}</a>
                </div>
            </div>
        </div>
    </form>
</section>

@endsection

@section('js')
<script>
$(document).ready(function() {
    $('#medicine_id').on('change', function() {
        var medicineId = $(this).val();
        var brandSelect = $('#medicine_brand_id');
        
        brandSelect.html('<option value="">{{__('Loading...')}}</option>');
        
        if (medicineId) {
            @foreach($medicines as $medicine)
                if ({{$medicine->id}} == medicineId) {
                    brandSelect.html('<option value="">{{__('Select Brand')}}</option>');
                    @foreach($medicine->activeBrands as $brand)
                        brandSelect.append('<option value="{{$brand->id}}">{{$brand->brand_name}}</option>');
                    @endforeach
                }
            @endforeach
        } else {
            brandSelect.html('<option value="">{{__('Select Medicine First')}}</option>');
        }
    });
    
    @if(old('medicine_id'))
        $('#medicine_id').trigger('change');
        setTimeout(function() {
            $('#medicine_brand_id').val('{{old('medicine_brand_id')}}').trigger('change');
        }, 100);
    @endif
});
</script>
@endsection
