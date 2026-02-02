@extends('layout.mainlayout',['activePage' => 'user'])

@section('css')
<link rel="stylesheet" href="{{ url('assets/css/intlTelInput.css') }}" />
<style>
    /* Custom Violet Color Styles */
    .text-violet { color: #4A3AFF !important; }
    .bg-violet { background-color: #4A3AFF !important; color: white !important; }
    .btn-violet { background-color: #4A3AFF !important; color: white !important; }
    
    /* Override Primary Colors */
    .text-primary { color: #4A3AFF !important; }
    .bg-primary { background-color: #4A3AFF !important; }
    .border-primary { border-color: #4A3AFF !important; }

    /* Font Enforcement */
    body, .sidebar, input, select, textarea, button, .form-control, .form-select {
        font-family: 'Fira Sans', sans-serif !important;
    }

    .sidebar li.active {
        background: linear-gradient(45deg, #00000000 50%, #f4f2ff);
        border-left: 2px solid #4A3AFF;
    }

    .iti {
        display: block !important;
    }
    .iti__tel-input {
        padding-left: 95px !important;
    }
</style>
@endsection

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 pb-20">
    <div class="pt-10">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-72 flex-shrink-0">
                @include('website.user.userSidebar',['active' => 'profileSetting'])
            </div>
            <div class="flex-grow w-full">
                <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 pb-10">
                    <form action="{{ url('update_user_profile') }}" method="post" class="h-100" enctype="multipart/form-data">
                        @csrf
                        <div class="change-avtar mb-8">
                            <div class="avatar-upload relative mx-auto w-32 h-32">
                                <div class="avatar-edit absolute right-0 bottom-0 z-10">
                                    <input type='file' name="image" id="image" class="hidden" accept=".png, .jpg, .jpeg" />
                                    <label for="image" class="inline-block w-8 h-8 rounded-full bg-violet text-white flex items-center justify-center cursor-pointer hover:bg-opacity-90 transition shadow-sm" data-bs-toggle="tooltip" data-bs-placement="right" title="Select new profile pic">
                                        <i class="fas fa-camera text-xs"></i>
                                    </label>
                                </div>
                                <div class="avatar-preview w-32 h-32 rounded-full overflow-hidden border-4 border-gray-50 shadow-sm relative">
                                    <div id="imagePreview" style="background-image: url({{ 'images/upload/'.auth()->user()->image }});" class="w-full h-full bg-cover bg-center"></div>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <p class="font-medium text-gray-900 font-fira-sans">{{ __('Patient Image') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-700 font-fira-sans">{{ __('Name') }}</label>
                                <input type="text" name="name" value="{{ auth()->user()->name }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-violet focus:border-violet block w-full p-2.5 font-fira-sans transition" id="name" placeholder="{{ __('Name') }}" />
                            </div>
                            <div>
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-700 font-fira-sans">{{ __('Email') }}</label>
                                <input type="email" name="email" value="{{ auth()->user()->email }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-violet focus:border-violet block w-full p-2.5 font-fira-sans transition" id="email" placeholder="{{ __('Email') }}" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="phoneNumber" class="block mb-2 text-sm font-medium text-gray-700 font-fira-sans">{{ __('Phone Number') }}</label>
                                <input type="text" name="phone" required value="{{ auth()->user()->phone_code }}&nbsp;{{ auth()->user()->phone }}" class="phone bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-violet focus:border-violet block w-full p-2.5 font-fira-sans transition" id="phoneNumber" placeholder="{{ __('Phone number') }}" />
                                <input type="hidden" name="phone_code" value={{ "+".env('DEFAULT_DIALING_CODE') }}>
                            </div>
                            <div>
                                <label for="language" class="block mb-2 text-sm font-medium text-gray-700 font-fira-sans">{{ __('Language')}}</label>
                                <select name="language" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-violet focus:border-violet block w-full p-2.5 font-fira-sans transition">
                                    @foreach ($languages as $language)
                                    <option value="{{ $language->name }}" {{ $language->name == auth()->user()->language ? 'selected' : '' }}>{{ $language->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="dob" class="block mb-2 text-sm font-medium text-gray-700 font-fira-sans">{{ __('Date of birth')}}</label>
                                <div class="relative">
                                    <input type="date" name="dob" value="{{ auth()->user()->dob }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-violet focus:border-violet block w-full p-2.5 font-fira-sans transition @error('dob') border-red-500 @enderror" />
                                </div>
                                @error('dob')
                                <div class="mt-1 text-sm text-red-600 font-fira-sans">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div>
                                <label for="gender" class="block mb-2 text-sm font-medium text-gray-700 font-fira-sans">{{ __('Gender') }}</label>
                                <select name="gender" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-violet focus:border-violet block w-full p-2.5 font-fira-sans transition">
                                    <option {{ auth()->user()->gender == 'male' ? 'selected' : '' }} value="male">{{ __('Male') }}</option>
                                    <option {{ auth()->user()->gender == 'female' ? 'selected' : '' }} value="female">{{__('Female') }}</option>
                                    <option {{ auth()->user()->gender == 'other' ? 'selected' : '' }} value="other">{{ __('Other') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 border-t border-gray-100 pt-6">
                            <a class="text-red-500 hover:text-red-600 font-fira-sans font-medium text-sm transition" href="javascript:void(0);" onclick="delete_account()">
                                <i class="fas fa-trash-alt mr-2"></i> {{__("Delete Account")}}
                            </a>
                            <button class="px-8 py-2.5 bg-violet text-white text-sm font-medium rounded-lg hover:opacity-90 transition font-fira-sans shadow-md shadow-violet/20" type="submit">
                                {{__("Update Profile")}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')
<script src="{{ url('assets/js/intlTelInput.min.js') }}"></script>
<script>
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

    $(document).ready(function() {
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var type = $('#imagePreview').attr('data-id');
                    var fileName = document.getElementById("image").value;
                    var idxDot = fileName.lastIndexOf(".") + 1;
                    var extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
                    if (extFile == "jpg" || extFile == "jpeg" || extFile == "png") {
                        $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                        $('#imagePreview').hide();
                        $('#imagePreview').fadeIn(650);
                    } else {
                        $('input[type=file]').val('');
                        alert("Only jpg/jpeg and png files are allowed!");
                        if (type == 'add') {
                            $('#imagePreview').css('background-image', 'url()');
                            $('#imagePreview').hide();
                            $('#imagePreview').fadeIn(650);
                        }
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#image").change(function() {
            readURL(this);
        });
    });
</script>
@endsection
