@extends('layout.mainlayout_admin',['activePage' => 'login'])

@section('title',__('Pharmacy Register'))

@section('content')
<section class="section">
    <div class="d-flex flex-wrap align-items-stretch min-vh-100">
        <div class="col-lg-5 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white d-flex align-items-center justify-content-center">
            <div class="p-4 p-lg-5 w-100 shadow-sm rounded my-4" style="max-width: 600px; max-height: 95vh; overflow-y: auto;">
                @php
                $setting = App\Models\Setting::first();
                @endphp
                
                <div class="text-center mb-4">
                    @if(isset($setting->logo))
                    <img src="{{ $setting->logo }}" alt="logo" width="180" class="mb-3 mt-2">
                    @else
                    <img src="{{url('/images/upload_empty/fuxxlogo.png')}}" alt="logo" width="180" class="mb-3 mt-2" />
                    @endif
                    <h4 class="text-dark font-weight-bold mb-1">{{__('Pharmacy Portal')}}</h4>
                    <p class="text-muted">{{__('Create your pharmacy account.')}}</p>
                </div>
                
                @if ($errors->any())
                @foreach ($errors->all() as $item)
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $item }}
                </div>
                @endforeach
                @endif
                
                <form action="{{ url('pharmacy_register') }}" method="post" class="needs-validation" novalidate="">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="text-muted font-weight-600 mb-1">{{ __('Name') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-store text-muted"></i></span>
                                    </div>
                                    <input type="text" value="{{ old('name') }}" name="name" class="form-control border-left-0 bg-light @error('name') is-invalid @enderror" style="border-radius: 0 0.5rem 0.5rem 0;" required>
                                </div>
                                @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="text-muted font-weight-600 mb-1">{{ __('Email') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-envelope text-muted"></i></span>
                                    </div>
                                    <input type="email" value="{{ old('email') }}" name="email" class="form-control border-left-0 bg-light @error('email') is-invalid @enderror" style="border-radius: 0 0.5rem 0.5rem 0;" required>
                                </div>
                                @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-600 mb-1">{{ __('Password') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock text-muted"></i></span>
                            </div>
                            <input type="password" value="{{ old('password') }}" name="password" class="form-control border-left-0 bg-light @error('password') is-invalid @enderror" style="border-radius: 0 0.5rem 0.5rem 0;" required>
                        </div>
                        @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-600 mb-1">{{ __('Phone number') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend" style="width: 30%">
                                <select name="phone_code" class="form-control bg-light h-100 phone_code_select2" style="border-radius: 0.5rem 0 0 0.5rem;">
                                    @foreach ($countries as $country)
                                    <option value="+{{$country->phonecode}}" {{(old('phone_code') == $country->phonecode) ? 'selected':''}}>+{{ $country->phonecode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="number" name="phone" class="form-control bg-light w-50 border-left-0 @error('phone') is-invalid @enderror" value="{{ old('phone') }}" style="border-radius: 0 0.5rem 0.5rem 0;" required>
                        </div>
                        @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-600 mb-1">{{ __('Location Based On Latitude/Longitude') }}</label>
                        <div class="card border border-light shadow-sm rounded-3">
                            <div class="card-body p-2">
                                <div class="pac-card mb-2" id="pac-card">
                                    <div id="pac-container">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                            </div>
                                            <input id="pac-input" type="text" name="address" class="form-control bg-light" placeholder="{{__('Search Location')}}" />
                                        </div>
                                        <input type="hidden" name="lat" value="{{$setting->lat}}" id="lat">
                                        <input type="hidden" name="lang" value="{{$setting->lang}}" id="lng">
                                    </div>
                                </div>
                                <div id="map" class="mapClass rounded border" style="height: 180px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="text-muted font-weight-600 mb-1">{{ __('Postcode') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-mail-bulk text-muted"></i></span>
                            </div>
                            <input type="text" value="{{ old('postcode') }}" name="postcode" class="form-control border-left-0 bg-light @error('postcode') is-invalid @enderror" placeholder="{{__('Postcode')}}" style="border-radius: 0 0.5rem 0.5rem 0;" required>
                        </div>
                        @error('postcode')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm" tabindex="4" style="border-radius: 8px;">
                            {{ __('Register') }} <i class="fas fa-user-plus ml-2"></i>
                        </button>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="text-muted mb-0 font-weight-600">{{__("Already have an account?")}} 
                            <a href="{{ url('pharmacy_login') }}" class="text-primary text-decoration-none font-weight-bold ml-1">{{ __('Login')}}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-lg-7 col-12 order-lg-2 order-1 min-vh-100 position-relative p-0 d-none d-lg-block">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(0, 166, 81, 0.8) 0%, rgba(20, 83, 45, 0.9) 100%); z-index: 1;"></div>
            <div class="bg-image h-100" style="background-image: url('{{ url('assets/img/login.png') }}'); background-size: cover; background-position: center; filter: grayscale(20%);"></div>
            
            <div class="position-absolute bottom-0 start-0 w-100 p-5 text-white" style="z-index: 2;">
                <div class="p-4 rounded-4 backdrop-blur-sm" style="background: rgba(255, 255, 255, 0.1); max-width: 600px; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <h1 class="display-4 fw-bold mb-3 font-clash" style="text-shadow: 0 4px 12px rgba(0,0,0,0.1);">{{ __('Join DoctorPlus') }}</h1>
                    <p class="lead mb-0" style="opacity: 0.9;">{{ __('Partner with thousands of doctors and supply medications seamlessly through our platform.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .backdrop-blur-sm {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: none;
        background-color: #fff !important;
    }
    .input-group-text {
        border-radius: 0.5rem 0 0 0.5rem;
    }
    .font-weight-600 {
        font-weight: 600;
    }
    .select2-container .select2-selection--single {
        height: calc(1.5em + 1.2rem + 2px) !important;
        border: 1px solid #e4e6fc;
        background-color: #f8f9fa;
        border-radius: 0.5rem 0 0 0.5rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(1.5em + 1.2rem + 2px) !important;
        color: #495057;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 1.2rem + 2px) !important;
    }
</style>
@endsection

@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key={{App\Models\Setting::first()->map_key}}&callback=initAutocomplete&libraries=places&v=weekly" async></script>
<script src="{{ url('assets_admin/js/hospital_map.js') }}"></script>
@endsection
