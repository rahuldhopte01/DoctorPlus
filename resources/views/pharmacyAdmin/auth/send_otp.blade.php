@extends('layout.mainlayout', ['activePage' => 'login'])

@section('title', __('Verify Account'))

@section('content')
<div class="container py-5 mt-5">
    <div class="row min-vh-75 align-items-center justify-content-center">
        <div class="col-xl-9">
            <div class="card border-0 shadow-bloomwell rounded-4 overflow-hidden">
                <div class="row g-0">
                    <!-- Left side: Image or Graphic -->
                    <div class="col-md-5 d-none d-lg-block bg-light position-relative">
                        <div class="position-absolute top-50 start-50 translate-middle w-100 p-5 text-center z-index-2">
                            <h2 class="display-6 fw-bold mb-4" style="color: var(--primary-color);">Verification.</h2>
                            <img src="{{asset('assets/image/login.png')}}" class="img-fluid custom-login-image" alt="OTP Verification Graphic" style="max-height: 250px; object-fit: contain;">
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(242, 239, 234, 0.9) 0%, rgba(255, 255, 255, 0.4) 100%);"></div>
                    </div>

                    <!-- Right side: Content -->
                    <div class="col-md-12 col-lg-7 p-4 p-lg-5">
                        <div class="mb-5 text-center text-lg-start">
                            <h2 class="fw-bold fs-3 text-dark mb-1">{{__('Verify Account')}}</h2>
                            <h3 class="fw-medium text-muted fs-5">{{__('Enter the verification code sent to your pharmacy account.')}}</h3>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger rounded-3 p-2 mb-4 d-flex align-items-center small" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>{{session('error')}}</div>
                            </div>
                        @endif

                        @if (!session('error') && $status)
                            <div class="alert alert-success rounded-3 p-2 mb-4 d-flex align-items-center small" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <div>{{ $status }}</div>
                            </div>
                        @endif

                        <form action="{{ url('pharmacy/verify_otp') }}" method="post" id="otpForm">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            
                            <div class="digit-group mb-5 justify-content-center justify-content-lg-start" data-group-name="digits" data-autosubmit="false" autocomplete="off">
                                <div class="otp-digit-wrapper"><input type="tel" required id="digit-1" name="digit_1" data-next="digit-2" class="form-control otp-input" autocomplete="new-password" maxlength="1" data-lpignore="true" data-1p-ignore /></div>
                                <div class="otp-digit-wrapper"><input type="tel" required id="digit-2" name="digit_2" data-next="digit-3" data-previous="digit-1" class="form-control otp-input" autocomplete="new-password" maxlength="1" data-lpignore="true" data-1p-ignore /></div>
                                <div class="otp-digit-wrapper"><input type="tel" required id="digit-3" name="digit_3" data-next="digit-4" data-previous="digit-2" class="form-control otp-input" autocomplete="new-password" maxlength="1" data-lpignore="true" data-1p-ignore /></div>
                                <div class="otp-digit-wrapper"><input type="tel" required id="digit-4" name="digit_4" data-next="digit-5" data-previous="digit-3" class="form-control otp-input" autocomplete="new-password" maxlength="1" data-lpignore="true" data-1p-ignore /></div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-4 bloomwell-btn border-0 py-3 fw-semibold">
                                {{ __('Verify') }}
                            </button>

                            <div class="mt-4 text-center">
                                <p class="text-muted small">
                                    {{__("Didn't receive a code?")}} 
                                    <a href="{{ url('pharmacy/send_otp/'.$user->id) }}" class="text-primary text-decoration-none fw-semibold ms-1 transition-all hover-primary">
                                        {{__('Resend Code')}}
                                    </a>
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
    .digit-group {
        display: grid !important;
        grid-template-columns: repeat(4, 55px) !important;
        gap: 12px;
        justify-content: center;
    }
    .otp-digit-wrapper {
        width: 55px;
        height: 65px;
        overflow: hidden !important;
        position: relative;
    }
    .digit-group input.otp-input {
        width: 100% !important;
        height: 100% !important;
        background-color: #f8f9fa;
        line-height: normal;
        text-align: center;
        font-size: 24px;
        font-weight: 600;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease-in-out;
        outline: none;
        color: var(--primary-color);
        padding: 0 !important;
        margin: 0;
        box-shadow: none !important;
        -webkit-appearance: none;
        -moz-appearance: textfield;
        appearance: none;
    }
    .digit-group input.otp-input::-webkit-outer-spin-button,
    .digit-group input.otp-input::-webkit-inner-spin-button,
    .digit-group input.otp-input::-webkit-calendar-picker-indicator {
        -webkit-appearance: none;
        display: none !important;
        margin: 0;
    }
    .digit-group input.otp-input:-webkit-autofill {
        -webkit-box-shadow: 0 0 0 30px #f8f9fa inset !important;
    }
    .digit-group input.otp-input:focus {
        background-color: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(0, 166, 81, 0.15);
    }
    .hover-primary:hover {
        color: var(--primary-color) !important;
    }
</style>
@endsection

@section('js')
<script>
    $(function() {
        "use strict";
        $('.digit-group').find('input').each(function() {
            $(this).attr('maxlength', 1);
            $(this).on('keyup', function(e) {
                var parent = $($(this).parent());
                if (e.keyCode === 8 || e.keyCode === 37) {
                    var prev = parent.find('input#' + $(this).data('previous'));
                    if (prev.length) {
                        $(prev).select();
                    }
                } else if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39) {
                    var next = parent.find('input#' + $(this).data('next'));
                    if (next.length) {
                        $(next).select();
                    } else {
                        if (parent.data('autosubmit')) {
                            $('#otpForm').submit();
                        }
                    }
                }
            });
        });
    });
</script>
@endsection
