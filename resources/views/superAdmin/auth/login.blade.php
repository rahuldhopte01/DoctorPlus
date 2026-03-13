@extends('layout.mainlayout_admin',['activePage' => 'login'])

@section('title', __('Admin login'))

@section('content')
<section class="section">
    <div class="d-flex flex-wrap align-items-stretch min-vh-100">
        <div class="col-lg-4 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white d-flex align-items-center justify-content-center">
            <div class="p-4 p-lg-5 w-100" style="max-width: 500px;">
                @php
                    $app_logo = App\Models\Setting::first();
                @endphp
                
                <div class="text-center mb-5">
                    @if(isset($app_logo->logo))
                    <img src="{{ $app_logo->logo }}" alt="logo" width="180" class="mb-4">
                    @else
                    <img src="{{url('/images/upload_empty/fuxxlogo.png')}}" alt="logo" width="180" class="mb-4" />
                    @endif
                    <h4 class="text-dark font-weight-bold mb-2">{{__('Admin Portal')}}</h4>
                    <p class="text-muted">{{__('Sign in to manage the platform.')}}</p>
                </div>

                @if ($errors->any())
                    @foreach ($errors->all() as $item)
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $item }}
                        </div>
                    @endforeach
                @endif
                
                <form method="POST" action="{{ url('admin/verify_admin') }}" class="needs-validation" novalidate="">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="email" class="text-muted font-weight-600">{{ __('Email') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-envelope text-muted"></i></span>
                            </div>
                            <input id="email" type="email" class="form-control border-left-0 bg-light @error('email') is-invalid @enderror"
                                name="email" tabindex="1" required autofocus style="border-radius: 0 0.5rem 0.5rem 0;">
                        </div>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="password" class="control-label text-muted font-weight-600 mb-0">{{ __('Password') }}</label>
                            <a href="{{ url('admin_forgot_password') }}" class="text-small text-primary font-weight-600">
                                {{ __('Forgot Password?') }}
                            </a>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock text-muted"></i></span>
                            </div>
                            <input id="password" type="password"
                                class="form-control border-left-0 bg-light @error('password') is-invalid @enderror" name="password" tabindex="2"
                                required style="border-radius: 0 0.5rem 0.5rem 0;">
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group mt-5">
                        <button type="submit" class="btn btn-primary btn-lg w-100 btn-icon icon-right shadow-sm" tabindex="4" style="border-radius: 8px;">
                            {{ __('Login') }} <i class="fas fa-sign-in-alt ml-2"></i>
                        </button>
                    </div>
                </form>
                
                <div class="mt-5 text-center">
                    <p class="text-muted mb-3 font-weight-600">{{__('Other Portals')}}</p>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ url('doctor/doctor_login') }}" class="btn btn-outline-secondary rounded-pill w-100 mb-2"><i class="fas fa-user-md mr-2"></i> {{ __('Doctor Login') }}</a>
                        <a href="{{ url('pharmacy_login') }}" class="btn btn-outline-secondary rounded-pill w-100 mb-2"><i class="fas fa-pills mr-2"></i> {{ __('Pharmacy Login') }}</a>
                        <a href="{{ url('pathologist_login') }}" class="btn btn-outline-secondary rounded-pill w-100 mb-2"><i class="fas fa-microscope mr-2"></i> {{ __('Lab Login') }}</a>
                    </div>
                    <div class="mt-4">
                        <a href="{{ url('patient-login') }}" class="text-muted small"><i class="fas fa-arrow-left mr-1"></i> Back to Patient Login</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8 col-12 order-lg-2 order-1 d-none d-lg-block p-0 position-relative">
            <div class="h-100 w-100" style="background-image: url('{{ url('assets_admin/img/login.png') }}'); background-size: cover; background-position: center;">
                <div class="position-absolute w-100 h-100" style="background: linear-gradient(135deg, rgba(0, 166, 81, 0.8) 0%, rgba(0, 90, 40, 0.9) 100%);"></div>
                <div class="position-absolute bottom-0 text-white p-5 w-100">
                    <div class="mb-5 pb-3 px-4">
                        <h1 class="font-weight-bold mb-3" style="font-size: 3rem;">{{ __('Admin Control') }}</h1>
                        <p class="lead" style="max-width: 500px; opacity: 0.9;">System management, analytics, and oversight the entire telemedicine platform operation.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .input-group-text { border-radius: 0.5rem 0 0 0.5rem; }
    .form-control:focus { box-shadow: none; border-color: #e4e6fc; background-color: #fff !important; }
    .input-group:focus-within .input-group-text, .input-group:focus-within .form-control { background-color: #fff !important; border-color: var(--primary) !important; }
    .input-group:focus-within .input-group-text i { color: var(--primary) !important; }
</style>
@endsection
