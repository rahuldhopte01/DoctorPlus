@extends('layout.mainlayout_admin',['activePage' => 'login'])

@section('title',__('Forgot Password'))

@section('content')
<section class="section">
    <div class="d-flex flex-wrap align-items-stretch min-vh-100">
        <div class="col-lg-4 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white d-flex align-items-center justify-content-center">
            <div class="p-4 p-lg-5 w-100" style="max-width: 500px;">
                @if (session('status'))
                @include('superAdmin.auth.status',[
                'status' => session('status')])
                @endif
                
                @php
                $app_logo = App\Models\Setting::first();
                @endphp
                
                <div class="text-center mb-5">
                    @if(isset($app_logo->logo))
                    <img src="{{ $app_logo->logo }}" alt="logo" width="180" class="mb-4 mt-2">
                    @else
                    <img src="{{url('/images/upload_empty/fuxxlogo.png')}}" alt="logo" width="180" class="mb-4 mt-2" />
                    @endif
                    <h4 class="text-dark font-weight-bold mb-2">{{__('Reset Password')}}</h4>
                    <p class="text-muted">{{__('Add Email Address To Get New Password')}}</p>
                </div>
                
                @if ($errors->any())
                @foreach ($errors->all() as $item)
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $item }}
                </div>
                @endforeach
                @endif
                
                <form action="{{ url('send_forgot_password') }}" method="post" class="myform needs-validation" novalidate="">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="email" class="text-muted font-weight-600">{{ __('Email') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-envelope text-muted"></i></span>
                            </div>
                            <input id="email" type="email" class="form-control border-left-0 bg-light @error('email') is-invalid @enderror" required name="email" value="{{ old('email') }}" tabindex="1" autofocus style="border-radius: 0 0.5rem 0.5rem 0;">
                        </div>
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    
                    <div class="form-group mt-5">
                        <button class="btn btn-primary btn-lg w-100 shadow-sm" type="submit" tabindex="4" style="border-radius: 8px;">
                            {{__('Send Email')}} <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-5 pt-3 border-top">
                    <p class="text-muted mb-0 font-weight-600">{{__('Remembered your password?')}}</p>
                    @if ($from == 'super admin')
                    <a href="{{ url('/login') }}" class="text-primary text-decoration-none font-weight-bold">{{__('Login Here')}}</a>
                    @endif
                    @if ($from =='doctor')
                    <a href="{{ url('doctor/doctor_login') }}" class="text-primary text-decoration-none font-weight-bold">{{__('Login Here')}}</a>
                    @endif
                    @if ($from =='pharmacy')
                    <a href="{{ url('pharmacy_login') }}" class="text-primary text-decoration-none font-weight-bold">{{__('Login Here')}}</a>
                    @endif
                    @if ($from =='lab')
                    <a href="{{ url('pathologist_login') }}" class="text-primary text-decoration-none font-weight-bold">{{__('Login Here')}}</a>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-8 col-12 order-lg-2 order-1 min-vh-100 position-relative p-0 d-none d-lg-block">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(0, 166, 81, 0.8) 0%, rgba(20, 83, 45, 0.9) 100%); z-index: 1;"></div>
            <div class="bg-image h-100" style="background-image: url('{{ url('assets/img/login.png') }}'); background-size: cover; background-position: center; filter: grayscale(20%);"></div>
            
            <div class="position-absolute bottom-0 start-0 w-100 p-5 text-white" style="z-index: 2;">
                <div class="p-4 rounded-4 backdrop-blur-sm" style="background: rgba(255, 255, 255, 0.1); max-width: 600px; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <h1 class="display-4 fw-bold mb-3 font-clash" style="text-shadow: 0 4px 12px rgba(0,0,0,0.1);">{{ __('Secure Access') }}</h1>
                    <p class="lead mb-0" style="opacity: 0.9;">{{ __('Regain access to your admin portal globally securely and efficiently.') }}</p>
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
</style>
@endsection

@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key={{App\Models\Setting::first()->map_key}}&callback=initAutocomplete&libraries=places&v=weekly" async></script>
<script src="{{ url('assets_admin/js/hospital_map.js') }}"></script>
@endsection
