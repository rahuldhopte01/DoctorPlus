@extends('layout.mainlayout',['activePage' => 'user'])

@section('css')
<style>
    /* Standard Violet Theme & Font */
    .text-violet { color: #4A3AFF !important; }
    .bg-violet { background-color: #4A3AFF !important; color: white !important; }
    .btn-violet { background-color: #4A3AFF !important; color: white !important; }
    
    .text-primary { color: #4A3AFF !important; }
    .bg-primary { background-color: #4A3AFF !important; }
    .border-primary { border-color: #4A3AFF !important; }

    body, .sidebar, input, select, textarea, button, .form-control {
        font-family: 'Fira Sans', sans-serif !important;
    }

    .sidebar li.active {
        background: linear-gradient(45deg, #00000000 50%, #f4f2ff);
        border-left: 2px solid #4A3AFF;
    }

    [multiple]:focus,
    [type=date]:focus,
    [type=datetime-local]:focus,
    [type=email]:focus,
    [type=month]:focus,
    [type=number]:focus,
    [type=password]:focus,
    [type=search]:focus,
    [type=tel]:focus,
    [type=text]:focus,
    [type=time]:focus,
    [type=url]:focus,
    [type=week]:focus,
    select:focus,
    textarea:focus {
        --tw-ring-color: #4A3AFF !important;
        border-color: #4A3AFF !important;
    }
</style>
@endsection

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 pb-20">
    <div class="pt-10">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-72 flex-shrink-0">
                @include('website.user.userSidebar',['active' => 'changePassword'])
            </div>
            <div class="flex-grow w-full">
                <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 pb-10">
                    @if(Session::has('status'))
                        <div class="mb-6 rounded-lg bg-green-50 text-green-700 text-center p-4 font-fira-sans border border-green-200">
                            {{ Session::get('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-50 text-red-600 p-4 text-center font-fira-sans border border-red-200">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('update_change_password') }}" method="post" class="max-w-2xl">
                        @csrf
                        <h3 class="font-fira-sans font-medium text-xl text-gray-900 mb-6">{{__('Change Password')}}</h3>
                        
                        <div class="mb-6">
                            <label for="current_password" class="block mb-2 text-sm font-medium text-gray-700 font-fira-sans">{{__('Current Password')}}</label>
                            <div class="relative">
                                <input type="password" name="old_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-violet focus:border-violet block w-full p-2.5 pr-10 font-fira-sans transition" placeholder="{{__('Old password')}}">
                                <button type="button" class="eye absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="new_password" class="block mb-2 text-sm font-medium text-gray-700 font-fira-sans">{{__('New Password')}}</label>
                            <div class="relative">
                                <input type="password" name="new_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-violet focus:border-violet block w-full p-2.5 pr-10 font-fira-sans transition" placeholder="{{__('New password')}}">
                                <button type="button" class="eye absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label for="confirm_password" class="block mb-2 text-sm font-medium text-gray-700 font-fira-sans">{{__('Confirm Password')}}</label>
                            <div class="relative">
                                <input type="password" name="confirm_new_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-violet focus:border-violet block w-full p-2.5 pr-10 font-fira-sans transition" placeholder="{{__('Confirm Password')}}">
                                <button type="button" class="eye absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex">
                            <button class="px-8 py-2.5 bg-violet text-white text-sm font-medium rounded-lg hover:opacity-90 transition font-fira-sans shadow-md shadow-violet/20" type="submit">
                                {{__("Update Password")}}
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
<script>
    $(".eye").on('click', function() {
        $(this).find('i').toggleClass("fa-eye fa-eye-slash");
        if ($(this).parent('div').find('input').attr('type') == "password") {
            $(this).parent('div').find('input').attr('type', "text");
        } else {
            $(this).parent('div').find('input').attr('type', "password");
        }
    });
</script>
@endsection
