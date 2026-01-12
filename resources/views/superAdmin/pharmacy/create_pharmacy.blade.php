@extends('layout.mainlayout_admin',['activePage' => 'pharmacy'])

@section('title',__('Add pharmacy'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Add Pharmacy'),
        'url' => url('pharmacy'),
        'urlTitle' => __('Pharmacy')
    ])
    @if (session('status'))
    @include('superAdmin.auth.status',[
        'status' => session('status')])
    @endif

    <div class="section_body">
    <form action="{{ url('pharmacy') }}" method="post" class="myform">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Name')}} <span class="text-danger">*</span></label>
                        <input type="text" value="{{ old('name') }}" name="name" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{__('Email')}} <span class="text-danger">*</span></label>
                        <input type="email" value="{{ old('email') }}" name="email" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group mt-4">
                        <label for="phone_number" class="ul-form__label"> {{__('Phone number')}} <span class="text-danger">*</span></label>
                        <div class="d-flex @error('phone') is-invalid @enderror">
                            <select name="phone_code" class="phone_code_select2" required>
                                @foreach ($countries as $country)
                                    <option value="+{{$country->phonecode}}" {{(old('phone_code') == $country->phonecode) ? 'selected':''}}>+{{ $country->phonecode }}</option>
                                @endforeach
                            </select>
                            <input type="number" min="1" name="phone" class="form-control" value="{{ old('phone') }}" required>
                        </div>
                        @error('phone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="row mt-4">
                        <div class="pac-card col-md-12 mb-3" id="pac-card">
                            <label for="pac-input">{{__('Location based on latitude/longtitude')}} <span class="text-danger">*</span></label>
                            <div id="pac-container">
                                <input id="pac-input" type="text" name="address" class="form-control" value="{{ old('address') }}" required/>
                                <input type="hidden" name="lat" value="{{$setting->lat}}" id="lat">
                                <input type="hidden" name="lang" value="{{$setting->lang}}" id="lng">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div id="map" class="mapClass"></div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label class="col-form-label">{{__('Postcode')}}</label>
                        <input type="text" name="postcode" class="form-control @error('postcode') is-invalid @enderror" value="{{ old('postcode') }}" placeholder="{{__('Postcode')}}">
                        @error('postcode')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label class="col-form-label">{{__('Mark as My Pharmacy (Priority)')}}</label>
                        <label class="cursor-pointer ml-2">
                            <input type="checkbox" id="is_priority" class="custom-switch-input" name="is_priority">
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </div>
                </div>
                <div class="text-right m-4">
                    <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection

@section('js')
    <script src="{{ url('assets_admin/js/hospital_map.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{App\Models\Setting::first()->map_key}}&callback=initAutocomplete&libraries=places&v=weekly" async></script>
@endsection
