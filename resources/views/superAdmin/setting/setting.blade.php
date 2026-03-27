@extends('layout.mainlayout_admin',['activePage' => 'setting'])

@section('title',__('Admin Setting'))

@section('setting')

<section class="section">
    @include('layout.breadcrumb',[
    'title' => __('Setting'),
    ])
    @if (session('status'))
    @include('superAdmin.auth.status',[
    'status' => session('status')])
    @endif
    @if (session('error_msg'))
    @include('superAdmin.auth.status',[
    'status' => session('error_msg')])
    @endif

    @if(isset($errors) && is_object($errors) && $errors->any())
    @foreach ($errors->all() as $error)
        @include('superAdmin.auth.status',[
        'status' => $error, 'icon' => 'warning'])
    @endforeach
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs">
                        @if($setting->license_verify == 1)
                        <li class="nav-item"><a class="nav-link active" href="#solid-justified-tab1" data-toggle="tab">{{__('General Settings')}}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#solid-justified-tab2" data-toggle="tab">{{__('Payment setting')}}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#solid-justified-tab3" data-toggle="tab">{{__('verification')}}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#solid-justified-tab5" data-toggle="tab">{{__('Website Setting')}}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#solid-justified-tab6" data-toggle="tab">{{__('Notification Setting')}}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#solid-justified-tab8" data-toggle="tab">{{__('Static Pages')}}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#solid-justified-tab9" data-toggle="tab">{{__('Video Call Setting')}}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#solid-justified-tab10" data-toggle="tab">{{__('Zoom Integration')}}</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link {{ $setting->license_verify == 0 ? 'active' : ''  }}" href="#solid-justified-tab7" data-toggle="tab">{{__('License Setting')}}</a></li>
                    </ul>
                    <div class="tab-content mt-3">
                        @if($setting->license_verify == 1)
                        <div class="tab-pane show active" id="solid-justified-tab1">
                            <form action="{{url('update_general_setting')}}" method="POST" enctype="multipart/form-data" class="myform">
                                @csrf

                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="app_id" class="ul-form__label"> {{__('Company white logo')}}</label>
                                        <div class="avatar-upload avatar-box avatar-box-left">
                                            <div class="avatar-edit">
                                                <input type='file' id="image" name="company_white_logo" accept=".png, .jpg, .jpeg" />
                                                <label for="image"></label>
                                            </div>
                                            <div class="avatar-preview">
                                                <div id="imagePreview" style="background-image: url({{ $setting->companyWhite }});"></div>
                                            </div>
                                        </div>
                                        @error('company_white_logo')
                                        <div class="custom_error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="app_id" class="col-form-label"> {{__('Company logo')}}</label>
                                        <div class="avatar-upload avatar-box avatar-box-left">
                                            <div class="avatar-edit">
                                                <input type='file' id="image2" name="company_logo" accept=".png, .jpg, .jpeg" />
                                                <label for="image2"></label>
                                            </div>
                                            <div class="avatar-preview">
                                                <div id="imagePreview2" style="background-image: url({{ $setting->logo }});"></div>
                                            </div>
                                        </div>
                                        @error('company_logo')
                                        <div class="custom_error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="app_id" class="col-form-label"> {{__('Company favicon')}}</label>
                                        <div class="avatar-upload avatar-box avatar-box-left">
                                            <div class="avatar-edit">
                                                <input type='file' id="image3" name="company_favicon" accept=".png, .jpg, .jpeg" />
                                                <label for="image3"></label>
                                            </div>
                                            <div class="avatar-preview">
                                                <div id="imagePreview3" style="background-image: url({{ $setting->favicon }});"></div>
                                            </div>
                                        </div>
                                        @error('company_favicon')
                                        <div class="custom_error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-md-4 form-group">
                                        <label for="business_name" class="col-form-label"> {{__('Business Name')}}</label>
                                        <input type="text" required name="business_name" value="{{ $setting->business_name }}" class="form-control @error('business_name') is-invalid @enderror">
                                        @error('business_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="email" class="col-form-label"> {{__('Email')}}</label>
                                        <input type="email" required name="email" value="{{ $setting->email }}" class="form-control @error('email') is-invalid @enderror">
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="phone" class="col-form-label"> {{__('Phone number')}}</label>
                                        <input type="number" min="1" required name="phone" value="{{ $setting->phone }}" class="form-control @error('phone') is-invalid @enderror">
                                        @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="col-md-6 form-group">
                                        <label for="app_id" class="col-form-label"> {{__('Admin Color')}}</label>
                                        <input type="color" required value="{{ $setting->color }}" name="color" class="form-control @error('color') is-invalid @enderror">
                                        @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="app_id" class="col-form-label"> {{__('Website Color')}}</label>
                                        <input type="color" required value="{{ $setting->website_color }}" name="website_color" class="form-control @error('website_color') is-invalid @enderror">
                                        @error('website_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="app_id" class="col-form-label"> {{__('Order Cancel Thresold By Doctor(In Minutes)')}}</label>
                                    <input type="number" min=1 required value="{{ $setting->auto_cancel }}" name="auto_cancel" class="form-control @error('auto_cancel') is-invalid @enderror">
                                    @error('auto_cancel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mt-5">
                                    <div class="col-md-6 form-group">
                                        <label for="app_id" class="col-form-label"> {{__('Timezone')}}</label>
                                        <select name="timezone" class="select2">
                                            @foreach ($timezones as $timezone)
                                            <option value="{{ $timezone->TimeZone }}" {{ $timezone->TimeZone == $setting->timezone ? 'selected' : '' }}>
                                                {{ $timezone->UTC_DST_offset }}&nbsp;&nbsp;{{ $timezone->TimeZone }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="app_id" class="col-form-label"> {{__('Currency')}}</label>
                                        <select name="currency_code" class="select2">
                                            @foreach ($currencies as $currency)
                                            <option value="{{$currency->id}}" {{ $currency->id == $setting->currency_id ? 'selected' : ''}}>
                                                {{$currency->country}}&nbsp;&nbsp;({{$currency->currency}})&nbsp;&nbsp;({{$currency->code}})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-md-6 form-group">
                                        <label for="radius" class="col-form-label"> {{__("Radius")}}</label>
                                        <input type="number" min="1" name="radius" class="radius form-control" value="{{ $setting->radius }}">
                                        @error('radius')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="language" class="col-form-label"> {{__("Language")}}</label>
                                        <select name="language" class="form-control">
                                            @foreach ($languages as $language)
                                            <option value="{{ $language->name }}" {{ $setting->language == $language->name ? 'selected' : '' }}>
                                                {{ $language->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="default_base_on" class="col-form-label"> {{__("doctor register with app it's based on commission or subscription?")}}</label>
                                    <select name="default_base_on" class="form-control">
                                        <option value="subscription" {{ $setting->default_base_on == 'subscription' ? 'selected' : ''}}>{{__('subscription')}}</option>
                                        <option value="commission" {{ $setting->default_base_on == 'commission' ? 'selected' : ''}}>{{__('commission')}}</option>
                                    </select>
                                    @error('default_base_on')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="pharmacy_commission" class="col-form-label">{{__("pharmacy admin register with app it's commission ?")}}</label>
                                        <input type="number" min="1" name="pharmacy_commission" required value="{{ $setting->pharmacy_commission }}" class="form-control @error('pharmacy_commission') is-invalid @enderror">
                                        @error('pharmacy_commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="pathologist_commission" class="col-form-label">{{__("pathologist register with app it's commission ?")}}</label>
                                        <input type="number" min="1" name="pathologist_commission" required value="{{ $setting->pathologist_commission }}" class="form-control @error('pathologist_commission') is-invalid @enderror">
                                        @error('pathologist_commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <div class="form-group default_base_on_com {{$setting->default_base_on != 'commission' ? 'hide' : ''}}">
                                            <label for="default_commission" class="col-form-label"> {{__("commission (in %)")}}</label>
                                            <input type="number" min="1" name="default_commission" {{$setting->default_base_on == 'commission' ? 'required' : ''}} value="{{ $setting->default_commission }}" class="form-control @error('default_commission') is-invalid @enderror default_base_on_com_text">
                                            @error('default_commission')
                                            <div class="invalid-feedback"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <div class="form-group">
                                            <label for="map_key" class="col-form-label"> {{__('Google map key')}}</label>
                                            <a href="https://developers.google.com/maps/documentation" target="_blank" class="" data-toggle="tooltip" data-placement="top" title="Help">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </a>
                                            <input type="text" required name="map_key" value="{{ $setting->map_key }}" class="form-control @error('map_key') is-invalid @enderror">
                                            @error('map_key')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="address" class="col-form-label">{{__("Search for the location")}}</label>
                                        <input id="pac-input" type="text" name="address" class="form-control" placeholder="{{__('Location')}}" />
                                    </div>
                                    <div class="col-lg-12">
                                        <div id="map" class="mapClass"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="lat" class="col-form-label">{{__("Latitude")}}</label>
                                        <input type="text" name="lat" required value="{{ $setting->lat }}" id="lat" class="form-control @error('lat') is-invalid @enderror">
                                        @error('lat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="lang" class="col-form-label">{{__("Longitude")}}</label>
                                        <input type="text" name="lang" required value="{{ $setting->lang }}" id="lng" class="form-control @error('lang') is-invalid @enderror">
                                        @error('lang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <table class="table table-bordered cancel_reason">
                                        <thead>
                                            <tr>
                                                <td> {{__('Add reason')}} </td>
                                                <td>
                                                    <button type="button" class="btn btn-primary" onclick="add_cancel_reason()">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($setting->cancel_reason != null)
                                            @php
                                            $cancel_reasons = json_decode($setting->cancel_reason)
                                            @endphp
                                            @foreach ($cancel_reasons as $cancel_reason)
                                            <tr>
                                                <td>
                                                    <input type="text" name="cancel_reason[]" value="{{ $cancel_reason }}" class="form-control" required>
                                                </td>
                                                @if ($loop->iteration != 1)
                                                <td>
                                                    <button type="button" class="btn btn-danger removebtn"><i class="fas fa-times"></i></button>
                                                </td>
                                                @endif
                                            </tr>

                                            @endforeach
                                            @else
                                            <tr>
                                                <td>
                                                    <input type="text" name="cancel_reason[]" class="form-control" required>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-md-12 text-center">
                                        <input type="submit" class="btn btn-primary" value="{{__('Submit')}}">
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="solid-justified-tab2">
                            <form action="{{url('update_payment_setting')}}" method="POST" enctype="multipart/form-data" class="myform">
                                @csrf
                                <div class="row">
                                    <div class="col-md-2">
                                        <label class="col-form-label">{{__('COD')}}</label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="cod" class="custom-switch-input" value="1" {{ $setting->cod == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label">{{__('PAYPAL')}}</label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="paypal" class="custom-switch-input" value="1" {{ $setting->paypal == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label">{{__('STRIPE')}}</label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="stripe" class="custom-switch-input" value="1" {{ $setting->stripe == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label">{{__('RAZORPAY')}}</label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="razor" class="custom-switch-input" value="1" {{$setting->razor == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label">{{__('Flutterwave')}}</label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="flutterwave" class="custom-switch-input" value="1" {{ $setting->flutterwave == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label">{{__('PAYSTACK')}}</label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="paystack" class="custom-switch-input" value="1" {{ $setting->paystack == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label">{{__('Stripe public key')}}</label>
                                    <a href="https://stripe.com/docs/keys#obtain-api-keys" target="_blank" class="" data-toggle="tooltip" data-placement="top" title="Help">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </a>
                                    <input type="text" value="{{ $setting->stripe_public_key }}" name="stripe_public_key" class="hide_value form-control @error('stripe_public_key') is-invalid @enderror">
                                    @error('stripe_public_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Stripe secret key')}}</label>
                                    <input type="text" value="{{ $setting->stripe_secret_key }}" name="stripe_secret_key" class="hide_value form-control @error('stripe_secret_key') is-invalid @enderror">
                                    @error('stripe_secret_key')
                                    <div class="invalid-feedback"> {{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('These keys are used for: prescription payment, questionnaire payment, doctor subscription, and app bookings.') }}</small>
                                    <div class="mt-2">
                                        <button type="button" id="test-stripe-btn" class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-plug"></i> {{ __('Test Stripe connection') }}
                                        </button>
                                        <span id="test-stripe-result" class="ml-2"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label">{{__('Paypal client ID (either Sandbox or Live)')}}</label>
                                    <a href="https://www.appinvoice.com/en/s/documentation/how-to-get-paypal-client-id-and-secret-key-22" target="_blank" class="" data-toggle="tooltip" data-placement="top" title="Help">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </a>
                                    <input type="text" value="{{ $setting->paypal_client_id }}" name="paypal_client_id" class="hide_value form-control @error('paypal_client_id') is-invalid @enderror">
                                    @error('paypal_client_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('paypal Secret key (either Sandbox or Live)')}}</label>
                                    <input type="text" value="{{ $setting->paypal_secret_key }}" name="paypal_secret_key" class="hide_value form-control @error('paypal_secret_key') is-invalid @enderror">
                                    @error('paypal_secret_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label">{{__('Razorpay key')}}</label>
                                    <a href="https://razorpay.com/docs/payments/dashboard/settings/api-keys/" target="_blank" class="" data-toggle="tooltip" data-placement="top" title="Help">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </a>
                                    <input type="text" value="{{ $setting->razor_key }}" name="razor_key" class="hide_value form-control @error('razor_key') is-invalid @enderror">
                                    @error('razor_key')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Flutterwave Key')}}</label>
                                    <a href="https://developer.flutterwave.com/docs/quickstart/" target="_blank" class="" data-toggle="tooltip" data-placement="top" title="Help">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </a>
                                    <input type="text" value="{{ $setting->flutterwave_key }}" name="flutterwave_key" class="hide_value form-control @error('flutterwave_key') is-invalid @enderror">
                                    @error('flutterwave_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Flutterwave Encryption Key')}}</label>
                                    <input type="text" value="{{ $setting->flutterwave_encryption_key }}" name="flutterwave_encryption_key" class="hide_value form-control @error('flutterwave_encryption_key') is-invalid @enderror">
                                    @error('flutterwave_encryption_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Is Live Key?')}}</label>
                                    <select name="isLiveKey" class="form-control">
                                        <option value="1" {{ $setting->isLiveKey == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                        <option value="0" {{ $setting->isLiveKey == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                    </select>
                                    @error('flutterwave_encryption_key')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label">{{__('Paystack Key')}}</label>
                                    <a href="https://support.paystack.com/hc/en-us/articles/360011508199-How-do-I-generate-new-API-keys" target="_blank" class="" data-toggle="tooltip" data-placement="top" title="Help">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </a>
                                    <input type="text" value="{{ $setting->paystack_public_key }}" name="paystack_public_key" class="hide_value form-control @error('paystack_public_key') is-invalid @enderror">
                                    @error('paystack_public_key')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12 text-right">
                                        <input type="submit" value="{{__('submit')}}" class="btn btn-primary">
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="solid-justified-tab3">
                            <form action="{{url('update_verification_setting')}}" method="POST" enctype="multipart/form-data" class="myform">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label class="col-form-label">{{__('User and doctor verification')}}</label>
                                        <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="{{ __('Verification Global Switch') }}"></i>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="verification" class="custom-switch-input" value="1" {{ $setting->verification == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="col-form-label">{{__('Verification using email ?')}}</label>
                                            <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="{{ __('Choose either email or message for verification') }}"></i>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="verification_method" class="custom-switch-input" value="email" {{ $setting->using_mail == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="col-form-label">{{__('Verification using message ?')}}</label>
                                        <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="{{ __('Choose either email or message for verification') }}"></i>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="verification_method" class="custom-switch-input" value="sms" {{ $setting->using_msg == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Twilio auth token')}}</label>
                                    <a href="https://www.twilio.com/docs/glossary/what-is-an-api-key" target="_blank" class="" data-toggle="tooltip" data-placement="top" title="Help">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </a>
                                    <input type="text" value="{{ $setting->twilio_auth_token }}" name="twilio_auth_token" class="hide_value form-control @error('twilio_auth_token') is-invalid @enderror">
                                    @error('twilio_auth_token')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('twilio account id')}}</label>
                                    <input type="text" value="{{ $setting->twilio_acc_id }}" name="twilio_acc_id" class="hide_value form-control @error('twilio_acc_id') is-invalid @enderror">
                                    @error('twilio_acc_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label">{{__('twilio phone number')}}</label>
                                    <input type="text" value="{{ $setting->twilio_phone_no }}" name="twilio_phone_no" class="hide_value form-control @error('twilio_phone_no') is-invalid @enderror">
                                    @error('twilio_phone_no')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('mail mailer')}}</label>
                                    <a href="https://sendgrid.com/blog/what-is-an-smtp-server/" target="_blank" class="" data-toggle="tooltip" data-placement="top" title="Help">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </a>
                                    <input type="text" value="{{ $setting->mail_mailer }}" name="mail_mailer" class="hide_value form-control @error('mail_mailer') is-invalid @enderror">
                                    @error('mail_mailer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('mail host')}}</label>
                                    <input type="text" value="{{ $setting->mail_host }}" name="mail_host" class="hide_value form-control @error('mail_host') is-invalid @enderror">
                                    @error('mail_host')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label">{{__('mail port')}}</label>
                                    <input type="text" value="{{ $setting->mail_port }}" name="mail_port" class="hide_value form-control @error('mail_port') is-invalid @enderror">
                                    @error('mail_port')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('mail username')}}</label>
                                    <input type="text" value="{{ $setting->mail_username }}" name="mail_username" class="hide_value form-control @error('mail_username') is-invalid @enderror">
                                    @error('mail_username')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('mail password')}}</label>
                                    <input type="text" value="{{ $setting->mail_password }}" name="mail_password" class="hide_value form-control @error('mail_password') is-invalid @enderror">
                                    @error('mail_password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('mail encryption')}}</label>
                                    <select name="mail_encryption" class="hide_value form-control @error('mail_encryption') is-invalid @enderror">
                                        <option value="ssl" {{ $setting->mail_encryption == 'ssl' ? 'selected' : '' }}>ssl</option>
                                        <option value="tls" {{ $setting->mail_encryption == 'tls' ? 'selected' : '' }}>tls</option>
                                    </select>
                                    @error('mail_encryption')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('mail from address')}}</label>
                                    <input type="text" value="{{ $setting->mail_from_address }}" name="mail_from_address" class="hide_value form-control @error('mail_from_address') is-invalid @enderror">
                                    @error('mail_from_address')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('mail from name')}}</label>
                                    <input type="text" value="{{ $setting->mail_from_name }}" name="mail_from_name" class="hide_value form-control @error('mail_from_name') is-invalid @enderror">
                                    @error('mail_from_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="row mt-4">
                                    <div class="div text-right">
                                        <input type="submit" value="{{__('submit')}}" class="btn btn-primary">
                                    </div>
                                    <div class="div text-left mx-1">
                                        <input type="button" value="{{__('Test Mail')}}" data-toggle="modal" data-target="#exampleModalCenter" class=" btn btn-primary ">
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="solid-justified-tab5">
                            <form action="{{url('update_content')}}" method="POST" enctype="multipart/form-data" class="myform">
                                @csrf

                                <!-- Sub-tabs for Website Setting -->
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-header-tab" data-toggle="pill" href="#pills-header" role="tab" aria-controls="pills-header" aria-selected="true">{{__('Header')}}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="false">{{__('Home Page')}}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-footer-tab" data-toggle="pill" href="#pills-footer" role="tab" aria-controls="pills-footer" aria-selected="false">{{__('Footer')}}</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="pills-tabContent">
                                    <!-- Header Settings -->
                                    <div class="tab-pane fade show active" id="pills-header" role="tabpanel" aria-labelledby="pills-header-tab">
                                    <div class="tab-pane fade show active" id="pills-header" role="tabpanel" aria-labelledby="pills-header-tab">
                                        <h5 class="mb-4">{{__('Promo Bar Settings')}}</h5>
                                        @php
                                            $promo = json_decode($setting->website_header_promo_bar, true) ?: [];
                                        @endphp
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-2">
                                                <label>{{__('Enable Promo Bar')}}</label>
                                                <div>
                                                    <label class="switch">
                                                        <input type="checkbox" name="promo_status" {{ ($promo['status'] ?? 0) == 1 ? 'checked' : '' }}>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <label>{{__('Promo Text (Italic)')}}</label>
                                                <input type="text" name="promo_text_italic" value="{{ $promo['text_italic'] ?? 'Erfrischen Sie im März Ihre Gesundheit:' }}" class="form-control" placeholder="Erfrischen Sie im März Ihre Gesundheit:">
                                            </div>
                                            <div class="col-md-5">
                                                <label>{{__('Promo Text (Bold)')}}</label>
                                                <input type="text" name="promo_text_bold" value="{{ $promo['text_bold'] ?? 'Mit dem Rabattcode M4RZ sparen Sie 10 €.' }}" class="form-control" placeholder="Mit dem Rabattcode M4RZ sparen Sie 10 €">
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col-md-4">
                                                <label>{{__('Countdown End Date')}}</label>
                                                <input type="datetime-local" name="promo_end_date" value="{{ $promo['end_date'] ?? '' }}" class="form-control">
                                                <small class="text-muted">{{__('Leave empty to hide countdown timer.')}}</small>
                                            </div>
                                        </div>

                                        <hr>
                                        <h5 class="mb-4">{{__('Top Marquee Settings')}}</h5>
                                        <div id="marquee-container">
                                            @php
                                                $marquees = json_decode($setting->website_header_top_marquee, true) ?: [];
                                            @endphp
                                            @if(count($marquees) > 0)
                                                @foreach($marquees as $index => $marquee)
                                                    <div class="row marquee-item mb-3 align-items-end">
                                                        <div class="col-md-5">
                                                            <label>{{__('Text')}}</label>
                                                            <input type="text" name="marquee_text[]" value="{{ $marquee['text'] }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>{{__('Icon')}}</label>
                                                            <input type="file" name="marquee_icon[]" class="form-control mb-1">
                                                            <input type="hidden" name="marquee_icon_current[]" value="{{ $marquee['icon'] }}">
                                                            @if($marquee['icon'])
                                                                <img src="{{ url('images/upload/'.$marquee['icon']) }}" style="height: 20px;">
                                                            @endif
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-marquee"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="row marquee-item mb-3 align-items-end">
                                                    <div class="col-md-5">
                                                        <label>{{__('Text')}}</label>
                                                        <input type="text" name="marquee_text[]" class="form-control">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label>{{__('Icon')}}</label>
                                                        <input type="file" name="marquee_icon[]" class="form-control">
                                                        <input type="hidden" name="marquee_icon_current[]" value="">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger btn-sm remove-marquee"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" id="add-marquee" class="btn btn-info btn-sm mb-4"><i class="fas fa-plus"></i> {{__('Add Marquee Item')}}</button>

                                        <hr>
                                        <h5 class="my-4">{{__('Main Header Settings')}}</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="col-form-label"> {{__('Website Header Logo')}}</label>
                                                <div class="avatar-upload avatar-box">
                                                    <div class="avatar-edit">
                                                        <input type='file' id="website_header_logo" name="website_header_logo" accept=".png, .jpg, .jpeg" />
                                                        <label for="website_header_logo"></label>
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <div id="websiteHeaderLogoPreview" style="background-image: url({{ $setting->website_header_logo ? url('images/upload/'.$setting->website_header_logo) : url('/images/upload_empty/fuxxlogo.png') }});"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="col-form-label d-block">{{__('Search Icon')}}</label>
                                                        <label class="cursor-pointer">
                                                            <input type="checkbox" name="website_header_search" class="custom-switch-input" value="1" {{ $setting->website_header_search == 1 ? 'checked' : '' }}>
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="col-form-label d-block">{{__('User Icon')}}</label>
                                                        <label class="cursor-pointer">
                                                            <input type="checkbox" name="website_header_user" class="custom-switch-input" value="1" {{ $setting->website_header_user == 1 ? 'checked' : '' }}>
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="col-form-label d-block">{{__('Hamburger Menu')}}</label>
                                                        <label class="cursor-pointer">
                                                            <input type="checkbox" name="website_header_hamburger" class="custom-switch-input" value="1" {{ $setting->website_header_hamburger == 1 ? 'checked' : '' }}>
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <label>{{__('Button Text')}}</label>
                                                        <input type="text" name="website_header_btn_text" value="{{ $setting->website_header_btn_text }}" class="form-control">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>{{__('Button URL')}}</label>
                                                        <input type="text" name="website_header_btn_url" value="{{ $setting->website_header_btn_url }}" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <label>{{__('Button BG Color')}}</label>
                                                        <input type="color" name="website_header_btn_bg_color" value="{{ $setting->website_header_btn_bg_color ?: '#7b42f6' }}" class="form-control">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>{{__('Button Text Color')}}</label>
                                                        <input type="color" name="website_header_btn_text_color" value="{{ $setting->website_header_btn_text_color ?: '#ffffff' }}" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <h5 class="my-4">{{__('Sidebar Menu Items')}}</h5>
                                        <div id="menu-container">
                                            @php
                                                $menus = json_decode($setting->website_header_sidebar_menu, true) ?: [];
                                            @endphp
                                            @if(count($menus) > 0)
                                                @foreach($menus as $index => $item)
                                                    <div class="row menu-item mb-3 align-items-end">
                                                        <div class="col-md-5">
                                                            <label>{{__('Label')}}</label>
                                                            <input type="text" name="menu_label[]" value="{{ $item['label'] }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label>{{__('URL')}}</label>
                                                            <input type="text" name="menu_url[]" value="{{ $item['url'] }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-menu"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="row menu-item mb-3 align-items-end">
                                                    <div class="col-md-5">
                                                        <label>{{__('Label')}}</label>
                                                        <input type="text" name="menu_label[]" class="form-control">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label>{{__('URL')}}</label>
                                                        <input type="text" name="menu_url[]" class="form-control">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger btn-sm remove-menu"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" id="add-menu" class="btn btn-info btn-sm mb-4"><i class="fas fa-plus"></i> {{__('Add Menu Item')}}</button>
                                    </div>

                                     <!-- Home Page Settings -->
                                     <div class="tab-pane fade" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                        @php
                                            $home = json_decode($setting->website_home_settings, true) ?: [];
                                            $hero = $home['hero'] ?? [];
                                            $how = $home['how_it_works'] ?? [];
                                            $about = $home['about'] ?? [];
                                        @endphp

                                        <h5 class="mb-4">{{__('Hero Section')}}</h5>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="col-form-label"> {{__('Hero Product Image (Center)')}}</label>
                                                <div class="avatar-upload avatar-box">
                                                    <div class="avatar-edit">
                                                        <input type='file' id="hero_image" name="hero_image" accept=".png, .jpg, .jpeg" />
                                                        <label for="hero_image"></label>
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <div id="heroImagePreview" style="background-image: url({{ !empty($hero['image']) ? url('images/upload/'.$hero['image']) : url('/images/upload_empty/hero.png') }});"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    <label>{{__('Hero Top Badge')}}</label>
                                                    <input type="text" name="hero_badge" value="{{ $hero['badge'] ?? '' }}" class="form-control" placeholder="e.g. MED. CANNABIS">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{__('Hero Main Title')}}</label>
                                                    <textarea name="hero_title" class="form-control" rows="2">{{ $hero['title'] ?? '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{__('Hero Typing Keywords (Comma separated)')}}</label>
                                                    <input type="text" name="hero_typing_keywords" value="{{ $hero['typing_keywords'] ?? 'MED. CANNABIS, EREKTIONSSTÖRUNGEN, TESTOSTERON, HAARAUSFALL, ÜBERGEWICHT' }}" class="form-control" placeholder="e.g. MED. CANNABIS, TESTOSTERON">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{__('Hero Description')}}</label>
                                                    <textarea name="hero_description" class="form-control" rows="3">{{ $hero['description'] ?? '' }}</textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label>{{__('Button Text')}}</label>
                                                        <input type="text" name="hero_btn_text" value="{{ $hero['btn_text'] ?? '' }}" class="form-control" placeholder="e.g. Jetzt Rezept anfragen">
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label>{{__('Button URL')}}</label>
                                                        <input type="text" name="hero_btn_url" value="{{ $hero['btn_url'] ?? '' }}" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-3">
                                                <label class="col-form-label">{{__('Hero Section Background Image')}}</label>
                                                <div class="avatar-upload avatar-box">
                                                    <div class="avatar-edit">
                                                        <input type='file' id="hero_bg_image" name="hero_bg_image" accept=".png, .jpg, .jpeg" />
                                                        <label for="hero_bg_image"></label>
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <div id="heroBgImagePreview" style="background-image: url({{ !empty($hero['bg_image']) ? url('images/upload/'.$hero['bg_image']) : url('/images/upload_empty/hero.png') }});"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 d-flex flex-column justify-content-end pb-2">
                                                <label class="col-form-label d-block">{{__('Hero Section Background Color')}}</label>
                                                <input type="color" name="hero_bg_color" value="{{ $hero['bg_color'] ?? '#f3ecff' }}" class="form-control form-control-color" style="height: 42px; width: 100%;">
                                                <small class="text-muted mt-1">Tint color for the gradient background</small>
                                            </div>
                                        </div>

                                        <hr>
                                        <h6 class="mt-3">{{__('Hero Ratings & Live Counter')}}</h6>
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <label>{{__('Stars Amount (1-5)')}}</label>
                                                <input type="number" step="0.1" name="hero_rating_stars" value="{{ $hero['rating_stars'] ?? '5' }}" class="form-control">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>{{__('Score Text')}}</label>
                                                <input type="text" name="hero_rating_score" value="{{ $hero['rating_score'] ?? '4,79' }}" class="form-control">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>{{__('Rating Description')}}</label>
                                                <input type="text" name="hero_rating_text" value="{{ $hero['rating_text'] ?? 'Hervorragend aus 13.764 Bewertungen' }}" class="form-control">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label>{{__('Live Viewers Text')}}</label>
                                                <input type="text" name="hero_live_viewers" value="{{ $hero['live_viewers'] ?? 'Personen schauen sich gerade Behandlungen an' }}" class="form-control">
                                            </div>
                                        </div>

                                        <hr>
                                        <label class="mt-3">{{__('Hero Trust Items')}}</label>
                                        <div id="hero-trust-container">
                                            @foreach($hero['trust_items'] ?? [] as $trust)
                                            <div class="row mb-2 trust-item align-items-end">
                                                <div class="col-md-4">
                                                    <label>{{__('Icon Class or Badge (e.g. bi-shield-check or DE)')}}</label>
                                                    <input type="text" name="hero_trust_icon_class[]" value="{{ $trust['icon_class'] ?? '' }}" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>{{__('Text')}}</label>
                                                    <input type="text" name="hero_trust_text[]" value="{{ $trust['text'] ?? '' }}" class="form-control">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger btn-sm remove-trust"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <button type="button" id="add-trust" class="btn btn-info btn-sm mb-4"><i class="fas fa-plus"></i> {{__('Add Trust Item')}}</button>

                                        <hr>
                                        <label class="mt-3">{{__('Hero Quick Links (Cards)')}}</label>
                                        <div id="hero-quick-link-container">
                                            @foreach($hero['quick_links'] ?? [] as $index => $qlink)
                                            <div class="card mb-3 quick-link-item">
                                                <div class="card-body">
                                                    <div class="row align-items-end">
                                                        <div class="col-md-3">
                                                            <label>{{__('Background Image')}}</label>
                                                            <input type="file" name="hero_quick_link_image[{{ $index }}]" class="form-control mb-1">
                                                            <input type="hidden" name="hero_quick_link_image_current[{{ $index }}]" value="{{ $qlink['image'] ?? '' }}">
                                                            @if(!empty($qlink['image']))
                                                                <img src="{{ url('images/upload/'.$qlink['image']) }}" style="height: 30px;">
                                                            @endif
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>{{__('Title')}}</label>
                                                            <input type="text" name="hero_quick_link_title[{{ $index }}]" value="{{ $qlink['title'] ?? '' }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>{{__('Subtitle')}}</label>
                                                            <input type="text" name="hero_quick_link_subtitle[{{ $index }}]" value="{{ $qlink['subtitle'] ?? '' }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>{{__('Badge (e.g. NEU)')}}</label>
                                                            <input type="text" name="hero_quick_link_badge[{{ $index }}]" value="{{ $qlink['badge'] ?? '' }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-quick-link"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                        <div class="col-md-4 mt-2">
                                                            <label>{{__('URL')}}</label>
                                                            <input type="text" name="hero_quick_link_url[{{ $index }}]" value="{{ $qlink['url'] ?? '' }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-4 mt-2">
                                                            <label>{{__('Small Icon Class')}}</label>
                                                            <input type="text" name="hero_quick_link_icon_class[{{ $index }}]" value="{{ $qlink['icon_class'] ?? '' }}" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <button type="button" id="add-quick-link" class="btn btn-info btn-sm mb-4"><i class="fas fa-plus"></i> {{__('Add Quick Link Card')}}</button>


                                        <hr>
                                        <h5 class="my-4">{{__('How it Works Section')}}</h5>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label>{{__('Section Title')}}</label>
                                                <input type="text" name="how_it_works_title" value="{{ $how['title'] ?? '' }}" class="form-control" placeholder="e.g. 3 einfache Schritte">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{__('Section Subtitle (purple)')}}</label>
                                                <input type="text" name="how_it_works_subtitle" value="{{ $how['subtitle'] ?? '' }}" class="form-control" placeholder="e.g. 100 % online">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{__('Live Badge Text')}}</label>
                                                <input type="text" name="how_it_works_badge" value="{{ $how['badge'] ?? '' }}" class="form-control" placeholder="e.g. 5 Ärzte online | täglich 8–18 Uhr">
                                            </div>
                                        </div>
                                        <div id="steps-container">
                                            @foreach($how['steps'] ?? [] as $index => $step)
                                            <div class="card mb-3 step-item">
                                                <div class="card-body">
                                                    <div class="row align-items-end">
                                                        <div class="col-md-3">
                                                            <label>{{__('Step Icon')}}</label>
                                                            <input type="file" name="step_icon[]" class="form-control mb-1">
                                                            <input type="hidden" name="step_icon_current[]" value="{{ $step['icon'] ?? '' }}">
                                                            @if(!empty($step['icon']))
                                                                <img src="{{ url('images/upload/'.$step['icon']) }}" style="height: 30px;">
                                                            @endif
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>{{__('Title')}}</label>
                                                            <input type="text" name="step_title[]" value="{{ $step['title'] ?? '' }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>{{__('Description')}}</label>
                                                            <input type="text" name="step_text[]" value="{{ $step['text'] ?? '' }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-sm remove-step"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <button type="button" id="add-step" class="btn btn-info btn-sm mb-4"><i class="fas fa-plus"></i> {{__('Add Step')}}</button>

                                        <hr>
                                        <h5 class="my-4">{{__('Natural Relief Section')}}</h5>
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <label>{{__('Badge Text')}}</label>
                                                <input type="text" name="natural_relief_badge" value="{{ $relief['badge'] ?? '' }}" class="form-control" placeholder="e.g. REZEPT WIRD ONLINE AUSGESTELLT">
                                            </div>
                                            <div class="col-md-5 form-group">
                                                <label>{{__('Section Title')}}</label>
                                                <input type="text" name="natural_relief_title" value="{{ $relief['title'] ?? '' }}" class="form-control" placeholder="e.g. Finden Sie | Linderung (use | for green)">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{__('Center Background Image')}}</label>
                                                <input type="file" name="natural_relief_image" class="form-control" accept="image/*">
                                                @if(!empty($relief['image']))
                                                    <img src="{{ url('images/upload/'.$relief['image']) }}" style="height: 40px; margin-top: 5px;">
                                                @endif
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>{{__('Button 1 (White) Text')}}</label>
                                                <input type="text" name="natural_relief_btn1_text" value="{{ $relief['btn1_text'] ?? '' }}" class="form-control" placeholder="e.g. Berechtigung prüfen">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>{{__('Button 1 URL')}}</label>
                                                <input type="text" name="natural_relief_btn1_url" value="{{ $relief['btn1_url'] ?? '' }}" class="form-control" placeholder="#">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>{{__('Button 2 (Green) Text')}}</label>
                                                <input type="text" name="natural_relief_btn2_text" value="{{ $relief['btn2_text'] ?? '' }}" class="form-control" placeholder="e.g. Gratis Beratung starten">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>{{__('Button 2 URL')}}</label>
                                                <input type="text" name="natural_relief_btn2_url" value="{{ $relief['btn2_url'] ?? '' }}" class="form-control" placeholder="#">
                                            </div>
                                        </div>
                                        
                                        <h6 class="mt-4 mb-3">{{__('Split Cards (Overlapping bottom edge)')}}</h6>
                                        <div id="relief-cards-container">
                                            @foreach($relief['cards'] ?? [] as $index => $rcard)
                                            <div class="card mb-3 relief-card-item">
                                                <div class="card-body">
                                                    <div class="row align-items-end">
                                                        <div class="col-md-3">
                                                            <label>{{__('Card Main Image')}}</label>
                                                            <input type="file" name="relief_card_icon[]" class="form-control mb-1">
                                                            <input type="hidden" name="relief_card_icon_current[]" value="{{ $rcard['icon'] ?? '' }}">
                                                            @if(!empty($rcard['icon']))
                                                                <img src="{{ url('images/upload/'.$rcard['icon']) }}" style="height: 30px;">
                                                            @endif
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>{{__('Card Title')}}</label>
                                                            <input type="text" name="relief_card_title[]" value="{{ $rcard['title'] ?? '' }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>{{__('Button Text')}}</label>
                                                            <input type="text" name="relief_card_btn_text[]" value="{{ $rcard['btn_text'] ?? '' }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label>{{__('Button URL')}}</label>
                                                            <input type="text" name="relief_card_btn_url[]" value="{{ $rcard['btn_url'] ?? '' }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-1">
                                                            <button type="button" class="btn btn-danger btn-sm remove-relief-card"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <button type="button" id="add-relief-card" class="btn btn-info btn-sm mb-4"><i class="fas fa-plus"></i> {{__('Add Split Card')}}</button>

                                        <hr>
                                        <h5 class="my-4">{{__('About Section')}}</h5>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="col-form-label"> {{__('About Image')}}</label>
                                                <div class="avatar-upload avatar-box">
                                                    <div class="avatar-edit">
                                                        <input type='file' id="about_image" name="about_image" accept=".png, .jpg, .jpeg" />
                                                        <label for="about_image"></label>
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <div id="aboutImagePreview" style="background-image: url({{ !empty($about['image']) ? url('images/upload/'.$about['image']) : url('/images/upload_empty/about.png') }});"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    <label>{{__('Badge Text')}}</label>
                                                    <input type="text" name="about_badge" value="{{ $about['badge'] ?? '' }}" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{__('About Title')}}</label>
                                                    <textarea name="about_title" class="form-control" rows="2">{{ $about['title'] ?? '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{__('Description')}}</label>
                                                    <textarea name="about_description" class="form-control" rows="4">{{ $about['description'] ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="mt-3">{{__('Feature List')}}</label>
                                        <div id="about-features-container">
                                            @foreach($about['features'] ?? [] as $feature)
                                            <div class="row mb-2 feature-item">
                                                <div class="col-md-10">
                                                    <input type="text" name="about_features[]" value="{{ $feature }}" class="form-control">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger btn-sm remove-feature"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <button type="button" id="add-feature" class="btn btn-info btn-sm mb-4"><i class="fas fa-plus"></i> {{__('Add Feature')}}</button>
                                     </div>

                                     <!-- Footer Settings -->
                                     <div class="tab-pane fade" id="pills-footer" role="tabpanel" aria-labelledby="pills-footer-tab">
                                        @php
                                            $footer = json_decode($setting->website_footer_settings, true) ?: [];
                                        @endphp
                                        <div class="form-group">
                                            <label>{{__('Copyright Text')}}</label>
                                            <input type="text" name="footer_copy" value="{{ $footer['copy'] ?? '' }}" class="form-control">
                                        </div>

                                        <h5 class="my-4">{{__('Social Links')}}</h5>
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label class="col-form-label">{{__('Facebook URL')}}</label>
                                                <input type="url" name="facebook_url" class="form-control" value="{{ $setting->facebook_url }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="col-form-label">{{__('Twitter URL')}}</label>
                                                <input type="url" name="twitter_url" class="form-control" value="{{ $setting->twitter_url }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="col-form-label">{{__('Instagram URL')}}</label>
                                                <input type="url" name="instagram_url" class="form-control" value="{{ $setting->instagram_url }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="col-form-label">{{__('Linkedin URL')}}</label>
                                                <input type="url" name="linkdin_url" class="form-control" value="{{ $setting->linkdin_url }}">
                                            </div>
                                        </div>

                                        <hr>
                                        <h5 class="my-4">{{__('Footer Columns (Links)')}}</h5>
                                        <div id="footer-cols-container">
                                            @foreach($footer['columns'] ?? [] as $col)
                                            <div class="card mb-3 footer-col-item">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>{{__('Column Title')}}</label>
                                                            <input type="text" name="footer_col_title[]" value="{{ $col['title'] ?? '' }}" class="form-control">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>{{__('Links (Format: Label|URL per line)')}}</label>
                                                            <textarea name="footer_col_links[]" class="form-control" rows="3">@foreach($col['links'] ?? [] as $link){{ $link['label'] }}|{{ $link['url'] }}&#10;@endforeach</textarea>
                                                        </div>
                                                        <div class="col-md-2 align-self-end">
                                                            <button type="button" class="btn btn-danger btn-sm remove-footer-col"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <button type="button" id="add-footer-col" class="btn btn-info btn-sm mb-4"><i class="fas fa-plus"></i> {{__('Add Footer Column')}}</button>
                                     </div>
                                </div>

                                <div class="text-center mt-5">
                                    <button type="submit" class="btn btn-primary">{{__('Save All Website Settings')}}</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="solid-justified-tab6">
                            <form action="{{url('update_notification')}}" method="POST" class="myform">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="col-form-label">{{__('Send Mail To Patient?')}}</label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="patient_mail" class="custom-switch-input" value="1" {{ $setting->patient_mail == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label">{{__('Send Mail To Doctor?')}}</label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="doctor_mail" class="custom-switch-input" value="1" {{ $setting->doctor_mail == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label">{{__('Send Push Notification To Patient?')}}</label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="patient_notification" class="custom-switch-input" value="1" {{ $setting->patient_notification
                                            == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label">{{__('Send Push Notification To Doctor?')}}</label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="doctor_notification" class="custom-switch-input" value="1" {{ $setting->doctor_notification == 1 ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                </div>
                                <label class="mt-5 text-primary" class="col-form-label">{{__('For Patient :: ')}}</label>

                                <div class="form-group">
                                    <label class="col-form-label">{{__('App ID')}}</label>
                                    <a href="https://documentation.onesignal.com/docs/accounts-and-keys" target="_blank" class="" data-toggle="tooltip" data-placement="top" title="Help">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </a>
                                    <input type="text" value="{{ $setting->patient_app_id }}" name="patient_app_id" class="hide_value form-control @error('patient_app_id') is-invalid @enderror">
                                    @error('patient_app_id')
                                    <div class="invalid-feedback"> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Auth key')}}</label>
                                    <input type="text" value="{{ $setting->patient_auth_key }}" name="patient_auth_key" class="hide_value form-control @error('patient_auth_key') is-invalid @enderror">
                                    @error('patient_auth_key')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('API key')}}</label>
                                    <input type="text" value="{{ $setting->patient_api_key }}" name="patient_api_key" class="hide_value form-control @error('patient_api_key') is-invalid @enderror">
                                    @error('patient_api_key')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <label class="mt-5 text-primary" class="col-form-label">{{__('For Doctor :: ')}}</label>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('App ID')}}</label>
                                    <a href="https://documentation.onesignal.com/docs/accounts-and-keys" target="_blank" class="" data-toggle="tooltip" data-placement="top" title="Help">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </a>
                                    <input type="text" value="{{ $setting->doctor_app_id }}" name="doctor_app_id" class="hide_value form-control @error('doctor_app_id') is-invalid @enderror">
                                    @error('doctor_app_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Auth key')}}</label>
                                    <input type="text" value="{{ $setting->doctor_auth_key }}" name="doctor_auth_key" class="hide_value form-control @error('doctor_auth_key') is-invalid @enderror">
                                    @error('doctor_auth_key')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label">{{__('API key')}}</label>
                                    <input type="text" value="{{ $setting->doctor_api_key }}" name="doctor_api_key" class="hide_value form-control @error('doctor_api_key') is-invalid @enderror">
                                    @error('doctor_api_key')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary mt-5">{{__('save')}}</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="solid-justified-tab8">
                            <form action="{{url('update_static_page')}}" method="POST" class="myform">
                                @csrf
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Privacy Policy')}}</label>
                                    <textarea name="privacy_policy" class="form-control summernote @error('privacy_policy') is-invalid @enderror">{{ $setting->privacy_policy }}</textarea>
                                    @error('privacy_policy')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('About Us')}}</label>
                                    <textarea name="about_us" class="form-control summernote @error('about_us') is-invalid @enderror">{{ $setting->about_us }}</textarea>
                                    @error('about_us')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Home Content')}}</label>
                                    <input name="home_content" class="form-control @error('home_content') is-invalid @enderror" value=" {{$setting->home_content }}">
                                    @error('home_content')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Home Content Description')}}</label>
                                    <textarea name="home_content_desc" class="form-control summernote @error('home_content_desc') is-invalid @enderror">{{ $setting->home_content_desc }}</textarea>
                                    @error('home_content_desc')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary mt-5">{{__('save')}}</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="solid-justified-tab9">
                            <form action="{{url('update_video_call_setting')}}" method="POST" class="myform">
                                @csrf
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Agora App Id')}}</label>
                                    <a href="https://docs.agora.io/en/voice-calling/reference/manage-agora-account?platform=android" target="_blank" class="" data-toggle="tooltip" data-placement="top" title="Help">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </a>
                                    <input name="agora_app_id" class="hide_value form-control @error('agora_app_id') is-invalid @enderror" value="{{ $setting->agora_app_id }}">
                                    @error('agora_app_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Agora App Certificate')}}</label>
                                    <input name="agora_app_certificate" class="hide_value form-control @error('agora_app_certificate') is-invalid @enderror" value="{{ $setting->agora_app_certificate }}">
                                    @error('agora_app_certificate')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary mt-5">{{__('save')}}</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="solid-justified-tab10">
                            <form action="{{url('update_zoom_setting')}}" method="POST" class="myform">
                                @csrf
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Zoom Switch')}}</label>
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="zoom_switch" class="custom-switch-input" id="zoom_switch" value="1" {{ $setting->zoom_switch == 1 ? 'checked' : '' }}>
                                        <span class="custom-switch-indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Zoom Client ID')}}</label>
                                    <span data-toggle="tooltip" data-placement="top" title="{{__('Steps are mentioned in the provided documentation')}}">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </span>
                                    <input type="text" name="zoom_client_id" class="hide_value form-control @error('zoom_client_id') is-invalid @enderror" value="{{ $setting->zoom_client_id }}">
                                    @error('zoom_client_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Zoom Client Secret')}}</label>
                                    <input type="text" name="zoom_client_secret" class="hide_value form-control @error('zoom_client_secret') is-invalid @enderror" value="{{ $setting->zoom_client_secret }}">
                                    @error('zoom_client_secret')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Zoom Redirect URL')}}</label>
                                    <input type="url" name="zoom_redirect_url" class="hide_value form-control @error('zoom_redirect_url') is-invalid @enderror" value="{{ $setting->zoom_redirect_url }}" placeholder="https://YOUR-DOMAIN.XYZ/zoom-oauth-callback">
                                    @error('zoom_redirect_url')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Zoom Page Content (optional)')}}</label>
                                    <br>
                                    <span>{{ __('Enter Zoom details as per your Website & Brand, link will be there in the website footer. This will be required while making the zoom feature accessible for live users.') }}</span>
                                    <textarea name="zoom_page_content" class="form-control summernote @error('zoom_page_content') is-invalid @enderror">{{ $setting->zoom_page_content }}</textarea>
                                    @error('zoom_page_content')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary mt-5">{{__('save')}}</button>
                                </div>
                            </form>
                        </div>
                        @endif

                        <div class="tab-pane {{ $setting->license_verify == 0 ? ' show active' : ''  }}" id="solid-justified-tab7">
                            <form action="{{url('update_licence_setting')}}" method="POST" class="myform">
                                @csrf
                                <div class="form-group">
                                    <label class="col-form-label">{{__('License Code')}}</label>
                                    <input type="text" required {{ $setting->license_verify == 1 ? 'disabled' : '' }} value="{{ $setting->license_code }}" name="license_code" class="hide_value form-control
                                    @error('license_code') is-invalid @enderror">
                                    @error('license_code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Client Name')}}</label>
                                    <input type="text" required {{ $setting->license_verify == 1 ? 'disabled' : '' }} value="{{ $setting->client_name }}" name="client_name" class="hide_value form-control
                                    @error('client_name') is-invalid @enderror">
                                    @error('client_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" {{ $setting->license_verify == 1 ? 'disabled' : '' }} class="btn btn-primary mt-5">{{__('save')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{__('Test Mail')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label class="col-form-label">{{__('Recipient Email for SMTP Testing')}}</label>
                <input type="text" name="mail_to" id="to" value="{{auth()->user()->email}}" required class="form-control @error('mail_to') is-invalid @enderror">
                <span class="text-danger" id="validate"></span>
                @error('mail_to')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                <button type="button" class="btn btn-primary" id="TestMail" onclick="testMail()">{{__('Send')}}</button>
            </div>
            <div class="emailstatus text-right mr-3"></div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        function readURL4(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview4').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview4').hide();
                    $('#imagePreview4').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#image4").change(function() {
            readURL4(this);
        });

        // Header Logo Preview
        function readHeaderLogo(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#websiteHeaderLogoPreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#websiteHeaderLogoPreview').hide();
                    $('#websiteHeaderLogoPreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#website_header_logo").change(function() {
            readHeaderLogo(this);
        });

        // Marquee Repeater
        $('#add-marquee').click(function() {
            var html = `<div class="row marquee-item mb-3 align-items-end">
                            <div class="col-md-5">
                                <label>{{__('Text')}}</label>
                                <input type="text" name="marquee_text[]" class="form-control">
                            </div>
                            <div class="col-md-5">
                                <label>{{__('Icon')}}</label>
                                <input type="file" name="marquee_icon[]" class="form-control">
                                <input type="hidden" name="marquee_icon_current[]" value="">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-marquee"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>`;
            $('#marquee-container').append(html);
        });
        $(document).on('click', '.remove-marquee', function() {
            $(this).closest('.marquee-item').remove();
        });

        // Menu Repeater
        $('#add-menu').click(function() {
            var html = `<div class="row menu-item mb-3 align-items-end">
                            <div class="col-md-5">
                                <label>{{__('Label')}}</label>
                                <input type="text" name="menu_label[]" class="form-control">
                            </div>
                            <div class="col-md-5">
                                <label>{{__('URL')}}</label>
                                <input type="text" name="menu_url[]" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-menu"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>`;
            $('#menu-container').append(html);
        });

        // Add Trust Item
        $(document).on('click', '#add-trust', function() {
            var html = '<div class="row mb-2 trust-item align-items-end"><div class="col-md-4"><label>{{__("Icon Class or Badge (e.g. bi-shield-check or DE)")}}</label><input type="text" name="hero_trust_icon_class[]" class="form-control"></div><div class="col-md-6"><label>{{__("Text")}}</label><input type="text" name="hero_trust_text[]" class="form-control"></div><div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-trust"><i class="fas fa-trash"></i></button></div></div>';
            $('#hero-trust-container').append(html);
        });
        $(document).on('click', '.remove-trust', function() {
            $(this).closest('.trust-item').remove();
        });

        // Add Quick Link Card
        $(document).on('click', '#add-quick-link', function() {
            var idx = $('.quick-link-item').length;
            var html = '<div class="card mb-3 quick-link-item"><div class="card-body"><div class="row align-items-end"><div class="col-md-3"><label>{{__("Background Image")}}</label><input type="file" name="hero_quick_link_image['+idx+']" class="form-control mb-1"><input type="hidden" name="hero_quick_link_image_current['+idx+']" value=""></div><div class="col-md-3"><label>{{__("Title")}}</label><input type="text" name="hero_quick_link_title['+idx+']" class="form-control"></div><div class="col-md-2"><label>{{__("Subtitle")}}</label><input type="text" name="hero_quick_link_subtitle['+idx+']" class="form-control"></div><div class="col-md-2"><label>{{__("Badge (e.g. NEU)")}}</label><input type="text" name="hero_quick_link_badge['+idx+']" class="form-control"></div><div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-quick-link"><i class="fas fa-trash"></i></button></div><div class="col-md-4 mt-2"><label>{{__("URL")}}</label><input type="text" name="hero_quick_link_url['+idx+']" class="form-control"></div><div class="col-md-4 mt-2"><label>{{__("Small Icon Class")}}</label><input type="text" name="hero_quick_link_icon_class['+idx+']" class="form-control"></div></div></div></div>';
            $('#hero-quick-link-container').append(html);
        });
        $(document).on('click', '.remove-quick-link', function() {
            $(this).closest('.quick-link-item').remove();
        });

        // Add Step
        $(document).on('click', '#add-step', function() {
            var html = '<div class="card mb-3 step-item"><div class="card-body"><div class="row align-items-end"><div class="col-md-3"><label>{{__("Step Icon")}}</label><input type="file" name="step_icon[]" class="form-control"><input type="hidden" name="step_icon_current[]" value=""></div><div class="col-md-3"><label>{{__("Title")}}</label><input type="text" name="step_title[]" class="form-control"></div><div class="col-md-4"><label>{{__("Description")}}</label><input type="text" name="step_text[]" class="form-control"></div><div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-step"><i class="fas fa-trash"></i></button></div></div></div></div>';
            $('#steps-container').append(html);
        });
        $(document).on('click', '.remove-step', function() {
            $(this).closest('.step-item').remove();
        });

        // Add Feature
        $(document).on('click', '#add-feature', function() {
            var html = '<div class="row mb-2 feature-item"><div class="col-md-10"><input type="text" name="about_features[]" class="form-control"></div><div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-feature"><i class="fas fa-trash"></i></button></div></div>';
            $('#about-features-container').append(html);
        });
        $(document).on('click', '.remove-feature', function() {
            $(this).closest('.feature-item').remove();
        });

        // Add Relief Card
        $(document).on('click', '#add-relief-card', function() {
            var html = '<div class="card mb-3 relief-card-item"><div class="card-body"><div class="row align-items-end"><div class="col-md-3"><label>{{__("Card Main Image")}}</label><input type="file" name="relief_card_icon[]" class="form-control mb-1"><input type="hidden" name="relief_card_icon_current[]" value=""></div><div class="col-md-3"><label>{{__("Card Title")}}</label><input type="text" name="relief_card_title[]" class="form-control"></div><div class="col-md-3"><label>{{__("Button Text")}}</label><input type="text" name="relief_card_btn_text[]" class="form-control"></div><div class="col-md-2"><label>{{__("Button URL")}}</label><input type="text" name="relief_card_btn_url[]" class="form-control"></div><div class="col-md-1"><button type="button" class="btn btn-danger btn-sm remove-relief-card"><i class="fas fa-trash"></i></button></div></div></div></div>';
            $('#relief-cards-container').append(html);
        });
        $(document).on('click', '.remove-relief-card', function() {
            $(this).closest('.relief-card-item').remove();
        });

        // Add Footer Column
        $(document).on('click', '#add-footer-col', function() {
            var html = '<div class="card mb-3 footer-col-item"><div class="card-body"><div class="row"><div class="col-md-4"><label>{{__("Column Title")}}</label><input type="text" name="footer_col_title[]" class="form-control"></div><div class="col-md-6"><label>{{__("Links (Format: Label|URL per line)")}}</label><textarea name="footer_col_links[]" class="form-control" rows="3"></textarea></div><div class="col-md-2 align-self-end"><button type="button" class="btn btn-danger btn-sm remove-footer-col"><i class="fas fa-trash"></i></button></div></div></div></div>';
            $('#footer-cols-container').append(html);
        });
        $(document).on('click', '.remove-footer-col', function() {
            $(this).closest('.footer-col-item').remove();
        });

        // Image Previews
        function readURL(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + previewId).css('background-image', 'url(' + e.target.result + ')');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#hero_image").change(function() { readURL(this, 'heroImagePreview'); });
        $("#about_image").change(function() { readURL(this, 'aboutImagePreview'); });
        $(document).on('click', '.remove-menu', function() {
            $(this).closest('.menu-item').remove();
        });
        $('#zoom_switch').change(function() {
            if($(this).is(':checked')) {
                $('#zoom_client_id').attr('required', true);
                $('#zoom_client_secret').attr('required', true);
                $('#zoom_redirect_url').attr('required', true);
            } else {
                $('#zoom_client_id').removeAttr('required');
                $('#zoom_client_secret').removeAttr('required');
                $('#zoom_redirect_url').removeAttr('required');
            }
        });
        $('#test-stripe-btn').on('click', function() {
            var btn = $(this);
            var result = $('#test-stripe-result');
            btn.prop('disabled', true);
            result.removeClass('text-success text-danger').html('<span class="text-muted">...</span>');
            $.ajax({
                url: '{{ url("test_stripe_connection") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        result.removeClass('text-danger').addClass('text-success').html('<i class="fa fa-check-circle"></i> ' + res.message);
                    } else {
                        result.removeClass('text-success').addClass('text-danger').html('<i class="fa fa-times-circle"></i> ' + res.message);
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Request failed.';
                    result.removeClass('text-success').addClass('text-danger').html('<i class="fa fa-times-circle"></i> ' + msg);
                },
                complete: function() { btn.prop('disabled', false); }
            });
        });
    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{App\Models\Setting::first()->map_key}}&callback=initAutocomplete&libraries=places&v=weekly" async></script>
<script src="{{ url('assets_admin/js/hospital_map.js') }}"></script>
@endsection
