@extends('layout.mainlayout',['activePage' => 'user'])

@section('css')
<style>
    /* Custom Violet Color Styles to ensure visibility */
    .text-violet { color: #4A3AFF !important; }
    .bg-violet { background-color: #4A3AFF !important; color: white !important; }
    .border-violet { border-color: #4A3AFF !important; }
    .hover-text-violet:hover { color: #4A3AFF !important; }
    .btn-violet { background-color: #4A3AFF !important; color: white !important; }
    .btn-violet:hover { opacity: 0.9; }

    /* Tab Active State */
    .tab-violet[aria-selected="true"] { 
        color: #4A3AFF !important; 
        border-color: #4A3AFF !important; 
    }
    .tab-violet:hover {
        color: #4A3AFF !important;
    }

    /* DataTables & Font overrides */
    .dataTables_wrapper, 
    .dataTables_info, 
    .dataTables_paginate, 
    .dataTables_filter, 
    .dataTables_filter input, 
    .dataTables_length,
    .dataTables_length select,
    table.dataTable,
    table.dataTable thead th, 
    table.dataTable tbody td {
        font-family: 'Fira Sans', sans-serif !important;
    }
    
    /* Spacing and Alignment inside the content box */
    .tab-content-wrapper {
        padding: 1.5rem; /* Equivalent to p-6 */
    }

    /* Fix DataTable Header Alignment - Restore Table Layout */
    table.dataTable thead th {
        display: table-cell !important;
        vertical-align: middle !important;
        text-align: left !important;
        white-space: nowrap !important;
        border-bottom: 1px solid #e5e7eb !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        background-image: none !important; /* Remove default background icons */
    }

    /* Inline Sort Icons next to text */
    table.dataTable thead th::before,
    table.dataTable thead th::after {
        position: static !important; /* Make them flow inline */
        display: inline-block !important;
        vertical-align: middle !important;
        opacity: 0.3 !important;
        margin: 0 4px !important; /* Small gap around arrows */
    }

    /* Highlight active sort icon */
    table.dataTable thead th.sorting_asc::after,
    table.dataTable thead th.sorting_desc::after {
        opacity: 1 !important;
        color: #4A3AFF !important;
    }
</style>
@endsection

@section('content')
{{-- Main Container: Full Width --}}
<div class="w-full px-4 sm:px-6 lg:px-8 pb-20">
    <div class="pt-10">
        {{-- Flex Layout for Sidebar and Content --}}
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar Column --}}
            <div class="w-full lg:w-72 flex-shrink-0">
                @include('website.user.userSidebar',['active' => 'dashboard'])
            </div>
            
            {{-- Main Content Column --}}
            <div class="flex-grow w-full">
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 overflow-hidden w-full">
                    <div class="p-6">
                        {{-- Tabs --}}
                        <div class="mb-6 border-b border-gray-100">
                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center w-full justify-start" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                                <li class="mr-6" role="presentation">
                                    <button class="inline-block pb-3 px-1 border-b-2 font-fira-sans text-base transition-colors duration-200 tab-violet border-transparent" id="appointments-tab" data-tabs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="false">{{ __('Appointments') }}</button>
                                </li>
                                <li class="mr-6" role="presentation">
                                    <button class="inline-block pb-3 px-1 border-b-2 font-fira-sans text-base transition-colors duration-200 tab-violet border-transparent" id="presentation-tab" data-tabs-target="#presentation" type="button" role="tab" aria-controls="presentation" aria-selected="false">{{ __('Prescriptions') }}</button>
                                </li>
                                <li class="mr-6" role="presentation">
                                    <button class="inline-block pb-3 px-1 border-b-2 font-fira-sans text-base transition-colors duration-200 tab-violet border-transparent" id="purchased-medicines-tab" data-tabs-target="#purchased-medicines" type="button" role="tab" aria-controls="purchased-medicines" aria-selected="false">{{ __('Purchased Medicine') }}</button>
                                </li>
                            </ul>
                        </div>
                        
                        <div id="myTabContent" class="w-full font-fira-sans space-y-6">

                            {{-- Appointments --}}
                            <div class="hidden p-6 bg-white rounded-xl shadow-sm border border-gray-100" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
                                <div class="flex flex-col">
                                    <div class="overflow-x-auto">
                                        <div class="inline-block min-w-full align-middle">
                                            <div class="overflow-hidden rounded-lg">
                                                <table class="min-w-full divide-y divide-gray-100 datatable w-full text-left font-fira-sans">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">#</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Appointment Id') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Report Image') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Date & Time') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Amount') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Status') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-100 font-fira-sans">
                                                        @foreach ($appointments as $appointment)
                                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-fira-sans text-left align-middle">{{ $loop->iteration }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-fira-sans font-medium text-left align-middle">{{ $appointment->appointment_id }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-left align-middle">
                                                                @if ($appointment->report_image != null)
                                                                <div class="flex -space-x-2 overflow-hidden justify-start">
                                                                    @foreach ($appointment->report_image as $item)
                                                                    <a href="{{ $item }}" data-fancybox="gallery{{$appointment->id}}" class="inline-block h-10 w-10 rounded-full ring-2 ring-white">
                                                                        <img src="{{ $item }}" alt="Report" class="h-full w-full object-cover rounded-full">
                                                                    </a>
                                                                    @endforeach
                                                                </div>
                                                                @else
                                                                <span class="text-xs text-gray-400 font-fira-sans">{{__('No Image')}}</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-fira-sans text-left align-middle">
                                                                <div class="flex flex-col">
                                                                    <span class="font-medium">{{ $appointment->date }}</span>
                                                                    <span class="text-xs text-violet">{{ $appointment->time }}</span>
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 font-fira-sans text-left align-middle">{{ $currency }}{{ $appointment->amount }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-left align-middle">
                                                                @php
                                                                    $statusColor = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                                                    $statusText = __('Pending');
                                                                    
                                                                    if(in_array(strtoupper($appointment->appointment_status), ['APPROVE'])) {
                                                                        $statusColor = 'bg-green-100 text-green-800 border-green-200';
                                                                        $statusText = __('Approved');
                                                                    } elseif(in_array(strtoupper($appointment->appointment_status), ['CANCEL'])) {
                                                                        $statusColor = 'bg-red-100 text-red-800 border-red-200';
                                                                        $statusText = __('Cancelled');
                                                                    } elseif(in_array(strtoupper($appointment->appointment_status), ['COMPLETE'])) {
                                                                        $statusColor = 'bg-blue-100 text-blue-800 border-blue-200';
                                                                        $statusText = __('Completed');
                                                                    }
                                                                @endphp
                                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $statusColor }} font-fira-sans">
                                                                    {{ $statusText }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-left align-middle">
                                                                <div class="flex items-center space-x-2">
                                                                    <a onclick="show_appointment({{ $appointment->id }})" class="p-2 text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors" href="javascript:void(0)" data-modal-toggle="exampleModalCenter" data-modal-target="#exampleModalCenter">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                    @if ($appointment->appointment_status == 'complete' && $appointment->isReview == false)
                                                                    <a onclick="appointId({{ $appointment->id }})" class="p-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors" href="javascript:void(0)" data-modal-toggle="addReview" data-modal-target="#addReview">
                                                                        <i class="fa fa-star"></i>
                                                                    </a>
                                                                    @endif
                                                                    @if ($appointment->appointment_status != 'cancel' && $appointment->appointment_status != 'complete')
                                                                    <a onclick="appointId({{ $appointment->id }})" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors" href="javascript:void(0)" data-modal-toggle="cancel_reason" data-modal-target="#cancel_reason">
                                                                        <i class="fa-solid fa-trash-can"></i>
                                                                    </a>
                                                                    @endif
                                                                    <a href="{{ url('add-to-calendar/' . $appointment->id) }}" class="p-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors tooltip" title="Add to Google Calendar">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </a>
                                                                    @if ($appointment->zoom_url)
                                                                    <a href="{{ $appointment->zoom_url }}" target="_blank" class="p-2 text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors tooltip"> 
                                                                        <i class="fa fa-video"></i>
                                                                    </a>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Prescriptions --}}
                            <div class="hidden p-6 bg-white rounded-xl shadow-sm border border-gray-100" id="presentation" role="tabpanel" aria-labelledby="presentation-tab">
                                <div class="flex flex-col">
                                    <div class="overflow-x-auto">
                                        <div class="inline-block min-w-full align-middle">
                                            <div class="overflow-hidden rounded-lg">
                                                <table class="min-w-full divide-y divide-gray-100 datatable w-full text-left font-fira-sans">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">#</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Reference') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Date') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Created By') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Status') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-100 font-fira-sans">
                                                        @foreach ($prescriptions as $prescription)
                                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-fira-sans text-left align-middle">{{ $loop->iteration }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 font-fira-sans text-left align-middle">
                                                                @if($prescription->appointment_id && $prescription->appointment)
                                                                    {{ $prescription->appointment->appointment_id }}
                                                                @else
                                                                    <span class="text-gray-500">{{ __('Questionnaire') }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-fira-sans text-left align-middle">{{ \Carbon\Carbon::parse($prescription->created_at)->format('d F Y') }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-left align-middle">
                                                                @if($prescription->doctor)
                                                                <div class="flex items-center justify-start">
                                                                    <div class="flex-shrink-0 h-8 w-8">
                                                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $prescription->doctor->fullImage }}" alt="">
                                                                    </div>
                                                                    <div class="ml-3">
                                                                        <div class="text-sm font-medium text-gray-900">{{ $prescription->doctor->name }}</div>
                                                                    </div>
                                                                </div>
                                                                @else
                                                                <span class="text-sm text-gray-500">{{ __('N/A') }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-left align-middle">
                                                                @if($prescription->status === 'approved_pending_payment')
                                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200 font-fira-sans">{{ __('Pending Payment') }}</span>
                                                                @elseif($prescription->status === 'active')
                                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200 font-fira-sans">{{ __('Active') }}</span>
                                                                @elseif($prescription->status === 'approved')
                                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200 font-fira-sans">{{ __('Approved') }}</span>
                                                                @elseif($prescription->status === 'expired')
                                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200 font-fira-sans">{{ __('Expired') }}</span>
                                                                @else
                                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200 font-fira-sans">{{ $prescription->status }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-left align-middle">
                                                                @if($prescription->status === 'approved_pending_payment')
                                                                    <a href="{{ url('prescription/pay/' . $prescription->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white btn-violet focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4A3AFF]">
                                                                        <i class="fas fa-credit-card mr-1.5"></i>
                                                                        {{ __('Pay') }} {{ $prescription->getFormattedPaymentAmount() }}
                                                                    </a>
                                                                @elseif(in_array($prescription->status, ['active', 'approved']) && $prescription->isValid())
                                                                    <a href="{{ url('downloadPDF/' . $prescription->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4A3AFF]">
                                                                        <i class="fas fa-download mr-1.5"></i>
                                                                        {{ __('Download') }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-xs text-gray-400 font-fira-sans">{{ __('Not Available') }}</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Medicine Orders --}}
                            <div class="hidden p-6 bg-white rounded-xl shadow-sm border border-gray-100" id="purchased-medicines" role="tabpanel" aria-labelledby="purchased-medicines-tab">
                                <div class="flex flex-col">
                                    <div class="overflow-x-auto">
                                        <div class="inline-block min-w-full align-middle">
                                            <div class="overflow-hidden rounded-lg">
                                                <table class="min-w-full divide-y divide-gray-100 datatable w-full text-left font-fira-sans">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">#</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Order Id') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Amount') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Attachment') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Payment Type') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Status') }}</th>
                                                            <th scope="col" class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider font-fira-sans align-middle">{{__('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-100 font-fira-sans">
                                                        @foreach ($purchaseMedicines as $purchaseMedicine)
                                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-fira-sans text-left align-middle">{{ $loop->iteration }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 font-fira-sans text-left align-middle">{{ $purchaseMedicine->medicine_id }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-fira-sans text-left align-middle">{{ $currency }}{{ $purchaseMedicine->amount }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-violet text-left align-middle">
                                                                @if (isset($purchaseMedicine->pdf) || $purchaseMedicine->pdf != null)
                                                                <a href="{{ url('prescription/upload/' . $purchaseMedicine->pdf) }}" target="_blank" class="hover:underline font-fira-sans flex items-center justify-start">
                                                                    <i class="fas fa-paperclip mr-1"></i> {{ __('View') }}
                                                                </a>
                                                                @else
                                                                <span class="text-gray-400 font-fira-sans">{{ __('N/A') }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-fira-sans text-left align-middle">{{ $purchaseMedicine->payment_type }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-left align-middle">
                                                                @if ($purchaseMedicine->payment_status == 1)
                                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200 font-fira-sans">{{ __('Paid') }}</span>
                                                                @else
                                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200 font-fira-sans">{{ __('Unpaid') }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-left align-middle">
                                                                <a onclick="show_medicines({{ $purchaseMedicine->id }})" class="p-2 text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors" href="javascript:void(0)" data-modal-toggle="purchased_medicine" data-modal-target="#purchased_medicine">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Appointment Details Modal --}}
<div class="fixed top-0 left-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none" id="exampleModalCenter" tabindex="-1" aria-modal="true">
    <div class="relative w-full max-w-2xl max-h-full m-auto mt-20">
        <div class="relative bg-white rounded-xl shadow-2xl border-0 overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50">
                <h5 class="text-xl font-bold text-gray-800 font-fira-sans">{{ __('Appointment Details') }}</h5>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-hide="exampleModalCenter">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <div class="bg-gray-50 rounded-lg p-5 mb-6">
                    <h5 class="font-bold text-lg text-gray-800 font-fira-sans mb-3 border-b border-gray-200 pb-2">{{ __('Hospital Details') }}</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><span class="text-gray-500 text-sm block">{{ __('Appointment ID')}}</span> <span class="font-medium text-gray-900 appointment_id font-fira-sans"></span></div>
                        <div><span class="text-gray-500 text-sm block">{{ __('Hospital') }}</span> <span class="font-medium text-gray-900 hospital font-fira-sans"></span></div>
                        <div><span class="text-gray-500 text-sm block">{{ __('Doctor') }}</span> <span class="font-medium text-gray-900 doctor_name font-fira-sans"></span></div>
                        <div><span class="text-gray-500 text-sm block">{{ __('Date') }}</span> <span class="font-medium text-gray-900 date font-fira-sans"></span></div>
                        <div><span class="text-gray-500 text-sm block">{{ __('Time') }}</span> <span class="font-medium text-gray-900 time font-fira-sans"></span></div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-5 mb-6">
                    <h5 class="font-bold text-lg text-gray-800 font-fira-sans mb-3 border-b border-gray-200 pb-2">{{ __('Patient Details') }}</h5>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><span class="text-gray-500 text-sm block">{{ __('Name') }}</span> <span class="font-medium text-gray-900 patient_name font-fira-sans"></span></div>
                        <div><span class="text-gray-500 text-sm block">{{ __('Age') }}</span> <span class="font-medium text-gray-900 patient_age font-fira-sans"></span></div>
                        <div class="md:col-span-2"><span class="text-gray-500 text-sm block">{{ __('Illness Info') }}</span> <span class="font-medium text-gray-900 illness_info font-fira-sans"></span></div>
                        <div class="md:col-span-2"><span class="text-gray-500 text-sm block">{{ __('Address') }}</span> <span class="font-medium text-gray-900 patient_address font-fira-sans"></span></div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-5">
                    <h5 class="font-bold text-lg text-gray-800 font-fira-sans mb-3 border-b border-gray-200 pb-2">{{ __('Payment Details') }}</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div><span class="text-gray-500 text-sm block">{{ __('Amount') }}</span> <span class="font-bold text-violet amount font-fira-sans"></span></div>
                        <div><span class="text-gray-500 text-sm block">{{ __('Status') }}</span> <span class="font-medium text-gray-900 payment_status font-fira-sans"></span></div>
                         <div><span class="text-gray-500 text-sm block">{{ __('Type') }}</span> <span class="font-medium text-gray-900 payment_type font-fira-sans"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Review Modal --}}
<div data-te-modal-init class="fixed top-0 left-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none" id="addReview" tabindex="-1" aria-labelledby="addReviewLabel" aria-hidden="true">
    <div class="relative w-full max-w-lg max-h-full m-auto mt-20">
        <div class="relative bg-white rounded-xl shadow-2xl border-0 overflow-hidden">
             <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50">
                <h5 class="text-xl font-bold text-gray-800 font-fira-sans" id="addReviewLabel"> {{ __('Write a Review') }}</h5>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-hide="addReview">
                     <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <form action="{{ url('/addReview') }}" method="post" id="reviewForm">
                @csrf
                <div class="p-6">
                    <input type="hidden" name="appointment_id" value="">
                    
                    <div class="mb-4 text-center">
                        <label class="block text-gray-700 font-medium mb-2 font-fira-sans">{{ __('How was your experience?') }}</label>
                        <div id="full-stars-example-two" class="flex justify-center">
                            <div class="rating-group">
                                <input disabled checked class="rating__input rating__input--none" name="rate" id="rating3-none" value="0" type="radio">
                                <label aria-label="1 star" class="rating__label" for="rating3-1"><i class="rating__icon rating__icon--star fa fa-star text-2xl"></i></label>
                                <input class="rating__input" name="rate" id="rating3-1" value="1" type="radio">
                                <label aria-label="2 stars" class="rating__label" for="rating3-2"><i class="rating__icon rating__icon--star fa fa-star text-2xl"></i></label>
                                <input class="rating__input" name="rate" id="rating3-2" value="2" type="radio">
                                <label aria-label="3 stars" class="rating__label" for="rating3-3"><i class="rating__icon rating__icon--star fa fa-star text-2xl"></i></label>
                                <input class="rating__input" name="rate" id="rating3-3" value="3" type="radio">
                                <label aria-label="4 stars" class="rating__label" for="rating3-4"><i class="rating__icon rating__icon--star fa fa-star text-2xl"></i></label>
                                <input class="rating__input" name="rate" id="rating3-4" value="4" type="radio">
                                <label aria-label="5 stars" class="rating__label" for="rating3-5"><i class="rating__icon rating__icon--star fa fa-star text-2xl"></i></label>
                                <input class="rating__input" name="rate" id="rating3-5" value="5" type="radio">
                            </div>
                        </div>
                        <span class="invalid-div text-red-500 text-sm block mt-1"><span class="rate"></span></span>
                    </div>
                    
                    <div>
                        <label for="exampleFormControlTextarea1" class="block text-gray-700 font-medium mb-2 font-fira-sans">{{ __('Your Feedback') }}</label>
                        <textarea name="review" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-[#4A3AFF] focus:border-[#4A3AFF] font-fira-sans" id="exampleFormControlTextarea1" rows="4" placeholder="{{ __('Tell us about your appointment...') }}"></textarea>
                        <span class="invalid-div text-red-500 text-sm"><span class="review"></span></span>
                    </div>
                </div>

                <div class="flex items-center justify-end p-6 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                    <button type="button" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 mr-2" data-modal-hide="addReview">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" onclick="addReview()" class="px-5 py-2.5 text-sm font-medium text-white btn-violet rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4A3AFF]">
                        {{ __('Submit Review') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div data-te-modal-init class="fixed top-0 left-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none" id="purchased_medicine" tabindex="-1" aria-labelledby="purchased_medicineLabel" aria-hidden="true">
    <div class="relative w-full max-w-2xl max-h-full m-auto mt-20">
        <div class="relative bg-white rounded-xl shadow-2xl border-0 overflow-hidden">
             <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50">
                <h5 class="text-xl font-bold text-gray-800 font-fira-sans" id="purchased_medicineLabel"> {{ __('Order Details') }}</h5>
                 <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-hide="purchased_medicine">
                     <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <div class="bg-gray-50 rounded-lg p-5 mb-5">
                    <h5 class="font-bold text-gray-800 font-fira-sans mb-3 flex items-center">
                        <i class="fas fa-shipping-fast text-violet mr-2"></i> {{ __('Shipping Details') }}
                    </h5>
                    <div class="space-y-3">
                        <div class="flex justify-between border-b border-gray-200 pb-2">
                            <span class="text-gray-500">{{__('Shipped At')}}</span>
                            <span class="font-medium text-gray-900 shippingAt font-fira-sans"></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-2 shippingAddressTr">
                            <span class="text-gray-500">{{__('Address')}}</span>
                            <span class="font-medium text-gray-900 shippingAddress font-fira-sans text-right max-w-[60%]"></span>
                        </div>
                        <div class="flex justify-between shippingAddressTr">
                            <span class="text-gray-500">{{__('Delivery Charge')}}</span>
                            <span class="font-bold text-violet deliveryCharge font-fira-sans"></span>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-100 rounded-lg overflow-hidden">
                    <div class="bg-gray-100 px-5 py-3 border-b border-gray-200">
                        <h5 class="font-bold text-gray-800 font-fira-sans">{{ __('Items') }}</h5>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-fira-sans">{{__('Medicine')}}</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-fira-sans">{{__('Qty')}}</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider font-fira-sans">{{__('Price')}}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 tbody font-fira-sans">
                           {{-- Items populated by JS --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div data-te-modal-init class="fixed top-0 left-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none" id="cancel_reason" tabindex="-1" aria-labelledby="cancel_reasonLabel" aria-hidden="true">
    <div class="relative w-full max-w-md max-h-full m-auto mt-20">
         <div class="relative bg-white rounded-xl shadow-2xl border-0 overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-red-50">
                <h5 class="text-xl font-bold text-red-600 font-fira-sans" id="cancel_reasonLabel"> {{ __('Cancel Appointment') }}</h5>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-hide="cancel_reason">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <div class="p-6">
                 <p class="text-gray-600 mb-4">{{ __('Please select a reason for cancellation:') }}</p>
                <form id="cancelForm">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="cancel_by" value="user">
                    
                    <div class="space-y-3 mb-6">
                        @foreach (json_decode($cancel_reason) as $reason)
                        <div class="flex items-center">
                            <input type="radio" id="reason{{$loop->iteration}}" name="payment" value="{{$reason}}" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 focus:ring-red-500" checked>
                            <label for="reason{{$loop->iteration}}" class="ml-2 text-sm font-medium text-gray-900 font-fira-sans">{{$reason}}</label>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="button" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 mr-2" data-modal-hide="cancel_reason">
                            {{ __('Keep Appointment') }}
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="cancelAppointment(id,event)">
                             {{ __('Confirm Cancellation') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="{{url('assets/js/custom.js')}}"></script>
@endsection
