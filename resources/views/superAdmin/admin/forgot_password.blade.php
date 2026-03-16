@extends('layout.mainlayout',['activePage' => 'login'])

@section('title',__('Forgot Password'))

@section('content')
<div class="container py-5 mt-5">
    <div class="row min-vh-75 align-items-center justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card border-0 shadow-bloomwell rounded-4 overflow-hidden">
                <div class="row g-0">
                    <!-- Left side: Image -->
                    <div class="col-md-6 d-none d-md-block bg-light position-relative">
                        <div class="position-absolute top-50 start-50 translate-middle w-100 p-5 text-center z-index-2">
                            <h2 class="display-6 fw-bold mb-4" style="color: var(--primary-color);">Secure Access.</h2>
                            <img src="{{asset('assets/image/login.png')}}" class="img-fluid custom-login-image" alt="Forgot Password Graphic" style="max-height: 250px; object-fit: contain;">
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(242, 239, 234, 0.9) 0%, rgba(255, 255, 255, 0.4) 100%);"></div>
                    </div>

                    <!-- Right side: Form -->
                    <div class="col-md-6 p-4 p-lg-5">
                        <div class="mb-5">
                            <h2 class="fw-bold fs-3 text-dark mb-1">{{__('Reset Password')}}</h2>
                            <h3 class="fw-medium text-muted fs-5">{{__('Add Email Address To Get New Password')}}</h3>
                        </div>

                        @if (session('status'))
                            @include('superAdmin.auth.status', ['status' => session('status')])
                        @endif

                        @if ($errors->any())
                            @foreach ($errors->all() as $item)
                                <div class="alert alert-danger rounded-3 p-2 mb-4 d-flex align-items-center small" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>{{ $item }}</div>
                                </div>
                            @endforeach
                        @endif

                        <form action="{{ url('send_forgot_password') }}" method="post" class="myform needs-validation" novalidate="">
                            @csrf
                            
                            <div class="form-floating mb-4">
                                <input type="email" name="email" id="email" class="form-control rounded-3 border-light shadow-sm @error('email') is-invalid @enderror" placeholder="{{__('Enter email')}}" value="{{ old('email') }}" required autofocus>
                                <label for="email" class="text-muted"><i class="bi bi-envelope me-2"></i>{{__('Email Address')}}</label>
                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-4 bloomwell-btn border-0 py-3 fw-semibold">
                                {{__('Send Email')}} <i class="fas fa-paper-plane ms-2"></i>
                            </button>

                            <div class="mt-5 text-center">
                                <p class="text-muted mb-3 font-weight-600 small">{{__('Remembered your password?')}}</p>
                                
                                @if ($from == 'super admin')
                                <a href="{{ url('/login') }}" class="btn btn-outline-secondary rounded-pill w-100"><i class="fas fa-sign-in-alt me-2"></i> {{__('Login Here')}}</a>
                                @endif
                                @if ($from =='doctor')
                                <a href="{{ url('doctor/doctor_login') }}" class="btn btn-outline-secondary rounded-pill w-100"><i class="fas fa-sign-in-alt me-2"></i> {{__('Login Here')}}</a>
                                @endif
                                @if ($from =='pharmacy')
                                <a href="{{ url('pharmacy_login') }}" class="btn btn-outline-secondary rounded-pill w-100"><i class="fas fa-sign-in-alt me-2"></i> {{__('Login Here')}}</a>
                                @endif
                                @if ($from =='lab')
                                <a href="{{ url('pathologist_login') }}" class="btn btn-outline-secondary rounded-pill w-100"><i class="fas fa-sign-in-alt me-2"></i> {{__('Login Here')}}</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .shadow-bloomwell {
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
    }
    .hover-primary:hover {
        color: var(--primary-color) !important;
    }
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(0, 166, 81, 0.25);
    }
</style>
@endsection
@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key={{App\Models\Setting::first()->map_key}}&callback=initAutocomplete&libraries=places&v=weekly" async></script>
<script src="{{ url('assets_admin/js/hospital_map.js') }}"></script>
@endsection
