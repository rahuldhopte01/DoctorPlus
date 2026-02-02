@extends('layout.mainlayout',['activePage' => 'user'])

@section('css')
<style>
    /* Custom Violet Color Styles */
    .text-violet { color: #4A3AFF !important; }
    .bg-violet { background-color: #4A3AFF !important; color: white !important; }
    .btn-violet { background-color: #4A3AFF !important; color: white !important; }
    .btn-violet:hover { opacity: 0.9; }

    /* Sidebar Active State defaults */
    .sidebar li.active {
        background: linear-gradient(45deg, #00000000 50%, #f4f2ff);
        border-left: 2px solid #4A3AFF;
    }

    /* DataTables & Font overrides */
    .dataTables_wrapper, table.dataTable, table.dataTable thead th, table.dataTable tbody td {
        font-family: 'Fira Sans', sans-serif !important;
    }
    
    /* Fix DataTable Header Alignment */
    table.dataTable thead th {
        display: table-cell !important;
        vertical-align: middle !important;
        text-align: left !important;
        white-space: nowrap !important;
        border-bottom: 1px solid #e5e7eb !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        background-image: none !important;
    }

    /* Inline Sort Icons */
    table.dataTable thead th::before, table.dataTable thead th::after {
        position: static !important;
        display: inline-block !important;
        vertical-align: middle !important;
        opacity: 0.3 !important;
        margin: 0 4px !important;
    }
    table.dataTable thead th.sorting_asc::after, table.dataTable thead th.sorting_desc::after {
        opacity: 1 !important;
        color: #4A3AFF !important;
    }
</style>
@endsection

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 pb-20">
    <div class="pt-10">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-72 flex-shrink-0">
                @include('website.user.userSidebar',['active' => 'testReport'])
            </div>
            <div class="flex-grow w-full">
                <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <div class="inline-block min-w-full align-middle">
                            <table class="min-w-full datatable">
                                <thead class="border-b bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left font-semibold font-fira-sans text-sm">#</th>
                                        <th scope="col" class="px-6 py-4 text-left font-semibold font-fira-sans text-sm">{{ __('Laboratory Name') }}</th>
                                        <th scope="col" class="px-6 py-4 text-left font-semibold font-fira-sans text-sm">{{ __('Prescription') }}</th>
                                        <th scope="col" class="px-6 py-4 text-left font-semibold font-fira-sans text-sm">{{ __('Date & time') }}</th>
                                        <th scope="col" class="px-6 py-4 text-left font-semibold font-fira-sans text-sm">{{ __('Payment Type') }}</th>
                                        <th scope="col" class="px-6 py-4 text-left font-semibold font-fira-sans text-sm">{{ __('Amount') }}</th>
                                        <th scope="col" class="px-6 py-4 text-left font-semibold font-fira-sans text-sm">{{ __('Report') }}</th>
                                        <th scope="col" class="px-6 py-4 text-left font-semibold font-fira-sans text-sm">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($test_reports as $test_report)
                                    <tr class="hover:bg-gray-50 transition duration-200">
                                        <td class="text-sm px-6 py-4 font-fira-sans">{{ $loop->iteration }}</td>
                                        <td class="text-sm px-6 py-4 font-fira-sans">{{ $test_report->lab['name'] }}</td>
                                        <td class="text-sm px-6 py-4 font-fira-sans">
                                            @if ($test_report->prescription != null)
                                            <a href="{{ 'report_prescription/upload/'.$test_report->prescription }}" data-fancybox="gallery2">
                                                <img src="{{ 'report_prescription/upload/'.$test_report->prescription}}" class="rounded-md object-cover" alt="Prescription" width="50px" height="50px">
                                            </a>
                                            @else
                                            <span class="text-gray-400 text-xs">{{__('Not available')}}</span>
                                            @endif
                                        </td>
                                        <td class="text-sm px-6 py-4 font-fira-sans">
                                            <div class="flex flex-col">
                                                <span>{{ $test_report->date }}</span>
                                                <span class="text-xs text-gray-500">{{ $test_report->time }}</span>
                                            </div>
                                        </td>
                                        <td class="text-sm px-6 py-4 font-fira-sans">
                                            <span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $test_report->payment_type }}</span>
                                        </td>
                                        <td class="text-sm px-6 py-4 font-fira-sans font-medium text-violet">{{ $currency }}{{ $test_report->amount }}</td>
                                        <td class="text-sm px-6 py-4 font-fira-sans">
                                            @if ($test_report->upload_report == null)
                                            <span class="text-gray-400 text-xs">{{ __('Pending') }}</span>
                                            @else
                                            <a class="text-violet hover:underline text-sm font-medium" href="{{ 'download_report/'.$test_report->id }}">
                                                <i class="fa fa-download mr-1"></i> {{ __('Download') }}
                                            </a>
                                            @endif
                                        </td>
                                        <td class="text-sm px-6 py-4">
                                            <a onclick="single_report({{ $test_report->id }})" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white btn-violet focus:outline-none" href="javascript:void(0)" data-modal-toggle="exampleModalScrollable" data-modal-target="#exampleModalScrollable">
                                                {{ __('View') }}
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
<div class="fixed top-0 left-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none" id="exampleModalScrollable" tabindex="-1" aria-modal="true">
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 transition-all duration-300 ease-in-out w-full max-w-2xl max-h-full">
        <div class="pointer-events-auto relative flex max-h-[100%] w-full flex-col overflow-hidden rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none dark:bg-neutral-600">
            <div class="flex flex-shrink-0 bg-white-100  items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50">
                <h5 class="text-xl font-medium leading-normal text-neutral-800 dark:text-neutral-200" id="exampleModalScrollableLabel"> {{ __('Appointment Details') }}</h5>
                <button type="button" class="inline-block rounded px-6 pt-2.5 pb-2 text-xs font-medium leading-normal text-primary-700 transition duration-150 ease-in-out hover:bg-primary-accent-100 focus:bg-primary-accent-100 focus:outline-none focus:ring-0 active:bg-primary-accent-200" data-modal-hide="exampleModalScrollable" data-te-ripple-color="light">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="relative overflow-y-auto p-4">
                <table class="table min-w-full mt-4">
                    <tr>
                        <td class="text-sm text-gray-600 px-2 py-2 text-left font-fira-sans">{{ __('Report Id') }}</td>
                        <td class="text-sm font-light px-2 py-2 font-fira-sans report_id"></td>
                    </tr>
                    <tr>
                        <td class="text-sm text-gray-600 px-2 py-2 text-left font-fira-sans">{{ __('patient name') }}</td>
                        <td class="text-sm font-light px-2 py-2 font-fira-sans patient_name"></td>
                    </tr>
                    <tr>
                        <td class="text-sm text-gray-600 px-2 py-2 text-left font-fira-sans">{{ __('patient phone number') }}</td>
                        <td class="text-sm font-light px-2 py-2 font-fira-sans patient_phone"></td>
                    </tr>
                    <tr>
                        <td class="text-sm text-gray-600 px-2 py-2 text-left font-fira-sans">{{ __('patient age') }}</td>
                        <td class="text-sm font-light px-2 py-2 font-fira-sans patient_age"></td>
                    </tr>
                    <tr>
                        <td class="text-sm text-gray-600 px-2 py-2 text-left font-fira-sans">{{ __('patient gender') }}</td>
                        <td class="text-sm font-light px-2 py-2 font-fira-sans patient_gender"></td>
                    </tr>
                    <tr>
                        <td class="text-sm text-gray-600 px-2 py-2 text-left font-fira-sans">{{ __('amount') }}</td>
                        <td class="text-sm font-light px-2 py-2 font-fira-sans amount"></td>
                    </tr>
                    <tr>
                        <td class="text-sm text-gray-600 px-2 py-2 text-left font-fira-sans">{{ __('payment status') }}</td>
                        <td class="text-sm font-light px-2 py-2 font-fira-sans payment_status"></td>
                    </tr>
                    <tr>
                        <td class="text-sm text-gray-600 px-2 py-2 text-left font-fira-sans">{{ __('payment type') }}</td>
                        <td class="text-sm font-light px-2 py-2 font-fira-sans payment_type"></td>
                    </tr>
                    <table class="table types text-left min-w-full mt-8">
                    </table>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
