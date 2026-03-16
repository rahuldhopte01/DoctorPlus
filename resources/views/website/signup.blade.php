@extends('layout.mainlayout',['activePage' => 'signup'])

@section('css')
<link rel="stylesheet" href="{{ url('assets/css/intlTelInput.css') }}" />
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
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(0, 166, 81, 0.25);
    }
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(0, 166, 81, 0.25);
    }
    
    .nav-pills .nav-link {
        color: #6c757d;
        border-radius: 50rem;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .nav-pills .nav-link.active {
        background-color: var(--primary-color);
        color: #fff;
    }
    .nav-pills .nav-link:hover:not(.active) {
        background-color: rgba(0, 166, 81, 0.1);
        color: var(--primary-color);
    }
    
    .iti {
        display: block !important;
        width: 100%;
    }
    .iti__tel-input {
        padding-left: 95px !important;
    }
    .form-floating > .iti {
        height: calc(3.5rem + 2px);
    }
    .form-floating > .iti > input {
        height: 100%;
        border-radius: 0.5rem;
    }
    .hide { display: none; }
    
    /* Custom Styling for the switch */
    .role-switcher {
        background: #f8f9fa;
        border-radius: 50rem;
        padding: 0.25rem;
        display: inline-flex;
    }
</style>
@endsection

@section('content')

<div class="container py-5 mt-5">
    <div class="row min-vh-75 align-items-center justify-content-center">
        <div class="col-xl-11">
            <div class="card border-0 shadow-bloomwell rounded-4 overflow-hidden">
                <div class="row g-0">
                    <!-- Left side: Image -->
                    <div class="col-md-5 d-none d-lg-block bg-light position-relative">
                        <div class="position-absolute top-50 start-50 translate-middle w-100 p-5 text-center z-index-2">
                            <h2 class="display-6 fw-bold mb-4" style="color: var(--primary-color);">Find the best doctor and medicine for you.</h2>
                            <img src="{{asset('assets/image/doctor-nurses.png')}}" class="img-fluid custom-login-image mt-4" alt="Signup Graphic">
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(242, 239, 234, 0.9) 0%, rgba(255, 255, 255, 0.4) 100%);"></div>
                    </div>

                    <!-- Right side: Form -->
                    <div class="col-md-12 col-lg-7 p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold fs-3 text-dark mb-1">{{__('Welcome,')}}</h2>
                            <h3 class="fw-medium text-muted fs-5">{{__('Create New Account!')}}</h3>
                        </div>

                        @php
                            $active = old('from') ? (old('from') == 'doctor' ? 'doctor' : 'patient') : 'patient';
                        @endphp

                        <div class="d-flex justify-content-center mb-5">
                            <ul class="nav nav-pills role-switcher gap-2" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $active == 'doctor' ? 'active' : '' }} signupDiv" data-attr="doctor" id="pills-doctor-tab" type="button" role="tab">
                                        <i class="bi bi-stethoscope me-1"></i> {{ __('Register as Doctor') }}
                                        <input type="radio" value="doctor" name="signup_title" class="d-none signup_title" {{ $active == 'doctor' ? 'checked' : '' }}>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $active == 'patient' ? 'active' : '' }} signupDiv" data-attr="patient" id="pills-patient-tab" type="button" role="tab">
                                        <i class="bi bi-person me-1"></i> {{ __('Register as Patient') }}
                                        <input type="radio" value="patient" name="signup_title" class="d-none signup_title" {{ $active == 'patient' ? 'checked' : '' }}>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content" id="pills-tabContent">
                            <!-- DOCTOR SIGNUP -->
                            <div class="doctorDiv {{ $active == 'doctor' ? 'active' : 'hide' }}">
                                <form action="{{ url('doctorRegister') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="from" value="doctor">
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" name="doc_name" id="doc_name" value="{{ old('doc_name') }}" class="form-control rounded-3 border-light shadow-sm @error('doc_name') is-invalid @enderror" placeholder="{{__('Enter doctor name')}}">
                                                <label for="doc_name" class="text-muted"><i class="bi bi-person me-2"></i>{{__('Doctor Name')}}</label>
                                                @error('doc_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="email" name="doc_email" id="doc_email" value="{{ old('doc_email') }}" class="form-control rounded-3 border-light shadow-sm @error('doc_email') is-invalid @enderror" placeholder="{{__('Enter email')}}">
                                                <label for="doc_email" class="text-muted"><i class="bi bi-envelope me-2"></i>{{__('Email Address')}}</label>
                                                @error('doc_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="doc_phone" class="form-label text-muted small fw-semibold">{{__('Phone Number')}}</label>
                                                <input type="number" name="doc_phone" id="doc_phone" value="{{ old('doc_phone') }}" class="form-control rounded-3 border-light shadow-sm doc_phone @error('doc_phone') is-invalid @enderror" placeholder="{{__('Enter Phone Number')}}">
                                                <input type="hidden" name="phone_code" value="{{ "+".env('DEFAULT_DIALING_CODE') }}">
                                                @error('doc_phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="doc_dob" class="form-label text-muted small fw-semibold">{{__('Birth Date')}}</label>
                                                <input type="date" name="doc_dob" id="doc_dob" value="{{ old('doc_dob') }}" class="form-control rounded-3 border-light shadow-sm py-2 @error('doc_dob') is-invalid @enderror">
                                                @error('doc_dob')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-floating mb-4">
                                        <input type="password" name="doc_password" id="doc_password" class="form-control rounded-3 border-light shadow-sm @error('doc_password') is-invalid @enderror" placeholder="{{__('Enter password')}}">
                                        <label for="doc_password" class="text-muted"><i class="bi bi-lock me-2"></i>{{__('Create Password')}}</label>
                                        @error('doc_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label d-block text-muted small fw-semibold">{{__('Gender')}}</label>
                                        <div class="d-flex gap-3 w-100" role="group">
                                            <input type="radio" class="btn-check" name="doc_gender" id="doc_gender_male" value="male" checked>
                                            <label class="btn btn-outline-primary w-50 shadow-sm rounded-3 py-2" for="doc_gender_male">{{ __('Male') }}</label>

                                            <input type="radio" class="btn-check" name="doc_gender" id="doc_gender_female" value="female">
                                            <label class="btn btn-outline-primary w-50 shadow-sm rounded-3 py-2" for="doc_gender_female">{{ __('Female') }}</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-4 bloomwell-btn border-0 py-3 fw-semibold">
                                        {{__('Submit Registration')}}
                                    </button>

                                    <div class="text-center pt-3 border-top mt-4">
                                        <p class="text-muted mb-0 small">{{__('Already have an account?')}} 
                                            <a href="{{url('patient-login')}}" class="text-primary text-decoration-none fw-semibold tracking-wide ms-1">{{__('Login')}}</a>
                                        </p>
                                    </div>
                                </form>
                            </div>

                            <!-- PATIENT SIGNUP -->
                            <div class="patientDiv {{ $active == 'patient' ? 'active' : 'hide' }}">
                                <form action="{{ url('signUp') }}" method="post">
                                    <input type="hidden" name="from" value="patient">
                                    @csrf
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control rounded-3 border-light shadow-sm @error('name') is-invalid @enderror" placeholder="{{__('Enter patient name')}}">
                                                <label for="name" class="text-muted"><i class="bi bi-person me-2"></i>{{__('Patient Name')}}</label>
                                                @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="email" name="email" id="patient_email" value="{{ old('email') }}" class="form-control rounded-3 border-light shadow-sm @error('email') is-invalid @enderror" placeholder="{{__('Enter email')}}">
                                                <label for="patient_email" class="text-muted"><i class="bi bi-envelope me-2"></i>{{__('Email Address')}}</label>
                                                @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="phone" class="form-label text-muted small fw-semibold">{{__('Phone Number')}}</label>
                                                <input type="number" name="phone" id="phone" value="{{ old('phone') }}" class="form-control rounded-3 border-light shadow-sm phone @error('phone') is-invalid @enderror" placeholder="{{__('Enter Phone Number')}}">
                                                <input type="hidden" name="phone_code" value="+880">
                                                @error('phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="dob" class="form-label text-muted small fw-semibold">{{__('Birth Date')}}</label>
                                                <input type="date" name="dob" id="dob" value="{{ old('dob') }}" class="form-control rounded-3 border-light shadow-sm py-2 @error('dob') is-invalid @enderror">
                                                @error('dob')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-floating mb-4">
                                        <input type="password" name="password" id="password" class="form-control rounded-3 border-light shadow-sm @error('password') is-invalid @enderror" placeholder="{{__('Enter password')}}">
                                        <label for="password" class="text-muted"><i class="bi bi-lock me-2"></i>{{__('Create Password')}}</label>
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label d-block text-muted small fw-semibold">{{__('Gender')}}</label>
                                        <div class="d-flex gap-3 w-100" role="group">
                                            <input type="radio" class="btn-check" name="gender" id="gender_male" value="male" checked>
                                            <label class="btn btn-outline-primary w-50 shadow-sm rounded-3 py-2" for="gender_male">{{ __('Male') }}</label>

                                            <input type="radio" class="btn-check" name="gender" id="gender_female" value="female">
                                            <label class="btn btn-outline-primary w-50 shadow-sm rounded-3 py-2" for="gender_female">{{ __('Female') }}</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-4 bloomwell-btn border-0 py-3 fw-semibold">
                                        {{__('Submit Registration')}}
                                    </button>

                                    <div class="text-center pt-3 border-top mt-4">
                                        <p class="text-muted mb-0 small">{{__('Already have an account?')}} 
                                            <a href="{{url('patient-login')}}" class="text-primary text-decoration-none fw-semibold tracking-wide ms-1">{{__('Login')}}</a>
                                        </p>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ url('assets/js/intlTelInput.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.signupDiv').click(function() {
            $('.signupDiv').removeClass('active');
            $(this).addClass('active');
            $(this).find('input[type=radio]').prop('checked', true);
            var radioVal = $(this).find('input[type=radio]').val();
            $('.invalid-feedback').text('');
            if (radioVal == 'doctor') {
                $('.doctorDiv').show();
                $('.patientDiv').hide();
            }
            if (radioVal == 'patient') {
                $('.doctorDiv').hide();
                $('.patientDiv').show();
            }
        });
    });
    
    // Check initial state correctly
    var initialRadioVal = $('input[name=signup_title]:checked').val();
    if (initialRadioVal == 'doctor') {
        $('.doctorDiv').show();
        $('.patientDiv').hide();
    } else {
        $('.doctorDiv').hide();
        $('.patientDiv').show();
    }

    const phoneInputField = document.querySelector(".phone");
    const phoneInput = window.intlTelInput(phoneInputField, {
        preferredCountries: ["us", "co", "in", "de"],
        initialCountry: "in",
        separateDialCode: true,
        utilsScript: "{{url('assets/js/utils.js')}}",
    });
    phoneInputField.addEventListener("countrychange", function() {
        var phone_code = $('.phone').find('.iti__selected-dial-code').text();
        $('input[name=phone_code]').val('+' + phoneInput.getSelectedCountryData().dialCode);
    });

    const DocphoneInputField = document.querySelector(".doc_phone");
    const docphoneInput = window.intlTelInput(DocphoneInputField, {
        preferredCountries: ["us", "co", "in", "de"],
        initialCountry: "in",
        separateDialCode: true,
        utilsScript: "{{url('assets/js/utils.js')}}",
    });
    DocphoneInputField.addEventListener("countrychange", function() {
        $('input[name=phone_code]').val('+' + docphoneInput.getSelectedCountryData().dialCode);
    });
</script>
@endsection
