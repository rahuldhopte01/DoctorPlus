@extends('layout.mainlayout',['activePage' => 'login'])

@section('title',__('Doctor Register'))

@section('content')
<div class="container py-5 mt-5">
    <div class="row min-vh-75 align-items-center justify-content-center">
        <div class="col-xl-9">
            <div class="card border-0 shadow-bloomwell rounded-4 overflow-hidden">
                <div class="row g-0">
                    <!-- Left side: Image -->
                    <div class="col-md-5 d-none d-lg-block bg-light position-relative">
                        <div class="position-absolute top-50 start-50 translate-middle w-100 p-5 text-center z-index-2">
                            <h2 class="display-6 fw-bold mb-4" style="color: var(--primary-color);">Welcome to DoctorPlus.</h2>
                            <img src="{{asset('assets/image/doctor-nurses.png')}}" class="img-fluid custom-login-image" alt="Doctor Registration Graphic">
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(242, 239, 234, 0.9) 0%, rgba(255, 255, 255, 0.4) 100%);"></div>
                    </div>

                    <!-- Right side: Form -->
                    <div class="col-md-12 col-lg-7 p-4 p-lg-5">
                        <div class="mb-4 text-center text-lg-start">
                            <h2 class="fw-bold fs-3 text-dark mb-1">{{__('Doctor Portal')}}</h2>
                            <h3 class="fw-medium text-muted fs-5">{{__('Create your doctor account.')}}</h3>
                        </div>

                        @if ($errors->any())
                            @foreach ($errors->all() as $item)
                                <div class="alert alert-danger rounded-3 p-2 mb-4 d-flex align-items-center small" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>{{ $item }}</div>
                                </div>
                            @endforeach
                        @endif

                        <form action="{{ url('doctor/doctor_register') }}" method="post" class="needs-validation" novalidate="">
                            @csrf
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="name" id="name" class="form-control rounded-3 border-light shadow-sm @error('name') is-invalid @enderror" placeholder="{{__('Name')}}" value="{{ old('name') }}" required>
                                        <label for="name" class="text-muted small"><i class="bi bi-person me-2"></i>{{__('Full Name')}}</label>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" name="email" id="email" class="form-control rounded-3 border-light shadow-sm @error('email') is-invalid @enderror" placeholder="{{__('Email')}}" value="{{ old('email') }}" required>
                                        <label for="email" class="text-muted small"><i class="bi bi-envelope me-2"></i>{{__('Email Address')}}</label>
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3 text-start">
                                        <div class="input-group shadow-sm rounded-3 overflow-hidden border border-light phone-input-group" style="height: 58px;">
                                            <div class="bg-light d-flex align-items-center px-2 border-end" style="width: 80px;">
                                                <select name="phone_code" class="form-select border-0 bg-transparent p-0 ps-1 small" style="box-shadow: none; font-size: 0.85rem;">
                                                    @foreach ($countries as $country)
                                                        <option value="+{{$country->phonecode}}" {{(env('DEFAULT_DIALING_CODE') == $country->phonecode) ? 'selected':''}}>+{{ $country->phonecode }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-floating flex-grow-1">
                                                <input type="number" name="phone" id="phone" class="form-control border-0 h-100 @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="{{__('Phone Number')}}" style="box-shadow: none;" required>
                                                <label for="phone" class="text-muted small"><i class="bi bi-telephone me-2"></i>{{__('Phone Number')}}</label>
                                            </div>
                                        </div>
                                        @error('phone')
                                        <div class="invalid-feedback d-block small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="dob" id="dob" class="form-control datePicker rounded-3 border-light shadow-sm @error('dob') is-invalid @enderror" placeholder="{{__('Date of Birth')}}" required>
                                        <label for="dob" class="text-muted small"><i class="bi bi-calendar-event me-2"></i>{{__('Date of Birth')}}</label>
                                        @error('dob')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select name="gender" id="gender" class="form-select rounded-3 border-light shadow-sm @error('gender') is-invalid @enderror" required>
                                            <option value="male">{{__('Male')}}</option>
                                            <option value="female">{{__('Female')}}</option>
                                            <option value="other">{{__('Other')}}</option>
                                        </select>
                                        <label for="gender" class="text-muted small"><i class="bi bi-gender-ambiguous me-2"></i>{{__('Gender')}}</label>
                                        @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-4">
                                        <input type="password" name="password" id="password" class="form-control rounded-3 border-light shadow-sm @error('password') is-invalid @enderror" placeholder="{{__('Password')}}" required>
                                        <label for="password" class="text-muted small"><i class="bi bi-lock me-2"></i>{{__('Password')}}</label>
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-4 bloomwell-btn border-0 py-3 fw-semibold transition-all">
                                {{ __('Sign Up') }}
                            </button>

                            <div class="mt-4 text-center pt-3 border-top">
                                <p class="text-muted small mb-0">{{__("Already have an account?")}} 
                                    <a href="{{ url('doctor/doctor_login') }}" class="text-primary text-decoration-none fw-semibold ms-1 transition-all hover-primary">{{ __('Login')}}</a>
                                </p>
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
    .transition-all {
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(0, 166, 81, 0.25);
    }
    .datePicker {
        background-color: #fff !important;
    }
</style>
@endsection
