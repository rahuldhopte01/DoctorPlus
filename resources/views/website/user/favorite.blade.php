@extends('layout.mainlayout',['activePage' => 'user'])

@section('css')
<style>
    /* Custom Violet Color Styles */
    .text-violet { color: #4A3AFF !important; }
    .bg-violet { background-color: #4A3AFF !important; color: white !important; }
    .btn-violet { background-color: #4A3AFF !important; color: white !important; }
    
    /* Override Primary Colors for this page to match Violet theme */
    .text-primary { color: #4A3AFF !important; }
    .bg-primary { background-color: #4A3AFF !important; }
    .border-primary { border-color: #4A3AFF !important; }
    .hover\:bg-primary:hover { background-color: #3b2ecc !important; }

    /* Sidebar Active State defaults */
    .sidebar li.active {
        background: linear-gradient(45deg, #00000000 50%, #f4f2ff);
        border-left: 2px solid #4A3AFF;
    }
</style>
@endsection

<div class="w-full px-4 sm:px-6 lg:px-8 pb-20">
    <div class="pt-10">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-72 flex-shrink-0">
                @include('website.user.userSidebar',['active' => 'favirote'])
            </div>
            <div class="flex-grow w-full">
                <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    @if(count($doctors) > 0)
                    @foreach ($doctors as $doctor)
                    <div class="hoverDoc h-auto mt-6 p-6 border border-gray-100 rounded-xl transition-all duration-200 hover:shadow-md hover:border-gray-200 bg-white">
                        <div class="flex flex-col lg:flex-row w-full gap-6">
                            <div class="flex-shrink-0 flex flex-col items-center justify-center lg:w-48">
                                <img class="w-24 h-24 rounded-full p-1 border border-primary object-cover" src="{{ url($doctor['fullImage']) }}" alt="" />
                                <h5 class="font-fira-sans font-medium text-lg text-gray-900 mt-4 text-center">{{ $doctor['name'] }}</h5>
                                <p class="text-sm text-violet font-fira-sans mt-1 text-center">{{ $doctor['treatment']['name'] }}</p>
                                <p class="text-sm text-gray-500 mt-2 text-center flex items-center gap-1">
                                    <i class="fa-solid fa-star text-yellow-400"></i> {{ $doctor['rate'] }} ({{ $doctor['review'] }}{{ __(' reviews') }})
                                </p>
                            </div>
                            <div class="flex-grow flex flex-col relative border-t lg:border-t-0 lg:border-l border-gray-100 lg:pl-6 pt-6 lg:pt-0">
                                <div data-id="{{ $doctor['id'] }}" class="absolute top-0 right-0 cursor-pointer add-favourite text-primary bg-gray-50 p-2 rounded-full hover:bg-gray-100 transition">
                                    <i class="{{ $doctor['is_fav'] == 'true' ? 'fa fa-bookmark' : 'fa-regular fa-bookmark' }}"></i>
                                </div>
                                <div class="flex-grow">
                                    <p class="font-fira-sans text-sm text-gray-500 mb-2">{{ $doctor['treatment']['name'] }}</p>
                                    @if(isset($doctor['hospital']) && is_array($doctor['hospital']) && count($doctor['hospital']) > 0)
                                    @foreach ($doctor['hospital'] as $hospital)
                                    <p class="font-fira-sans font-medium text-base text-gray-900">{{ $hospital['name'] }}</p>
                                    <p class="font-fira-sans text-sm text-gray-500 mt-1 flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot"></i> {{ $hospital['address'] }}
                                    </p>
                                    @endforeach
                                    @endif
                                    <h1 class="font-fira-sans font-semibold text-xl text-violet mt-4">{{ $currency }}{{ $doctor['appointment_fees'] }}</h1>
                                </div>
                                <div class="flex gap-4 mt-6">
                                    <a href="{{ url('booking/'.$doctor['id'].'/'.Str::slug($doctor['name'])) }}" class="flex-1 py-2 px-4 bg-violet text-white text-center rounded-lg font-fira-sans text-sm font-medium hover:opacity-90 transition">{{__('Make Appointment')}}</a>
                                    <a href="{{ url('doctor-profile/'.$doctor['id'].'/'.Str::slug($doctor['name'])) }}" class="flex-1 py-2 px-4 border border-violet text-violet text-center rounded-lg font-fira-sans text-sm font-medium hover:bg-violet hover:text-white transition">{{__('View Profile')}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="flex flex-col items-center justify-center py-20">
                         <div class="bg-gray-50 p-6 rounded-full mb-4">
                            <i class="fas fa-heart-broken text-4xl text-gray-300"></i>
                         </div>
                        <p class="font-fira-sans text-gray-500 text-lg">{{__('No Favourite Doctors Found')}}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
