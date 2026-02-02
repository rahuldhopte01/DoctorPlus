@extends('layout.mainlayout',['activePage' => 'user'])

@section('css')
<style>
  .sidebar li.active {
    background: linear-gradient(45deg, #00000000 50%, #f4f2ff);
    border-left: 2px solid #4A3AFF;
  }

  .mapClass {
    height: 200px;
    border-radius: 12px;
  }
  
  /* Custom Violet Color Styles */
  .text-violet { color: #4A3AFF !important; }
  .bg-violet { background-color: #4A3AFF !important; color: white !important; }
  .btn-violet { background-color: #4A3AFF !important; color: white !important; }

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
                @include('website.user.userSidebar',['active' => 'patientAddress'])
            </div>
            <div class="flex-grow w-full">
                <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex justify-end mb-6">
                        <a class="inline-flex items-center px-4 py-2 bg-violet text-white text-sm font-medium rounded-lg hover:opacity-90 transition font-fira-sans cursor-pointer shadow-sm shadow-violet/30" type="button" data-from="add_new" data-modal-target="exampleModalScrollableLabel" data-modal-toggle="exampleModalScrollableLabel" data-te-ripple-color="light">
                            <i class="fas fa-plus mr-2"></i> {{ __('Add New') }}
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <div class="inline-block min-w-full align-middle">
                            <table class="min-w-full datatable">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left font-semibold font-fira-sans text-sm text-gray-700">#</th>
                                        <th scope="col" class="px-6 py-4 text-left font-semibold font-fira-sans text-sm text-gray-700">{{ __('Address')}}</th>
                                        <th scope="col" class="px-6 py-4 text-left font-semibold font-fira-sans text-sm text-gray-700">{{ __('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($addresses as $address)
                                    <tr class="hover:bg-gray-50 transition duration-200">
                                        <td class="text-sm px-6 py-4 font-fira-sans">{{ $loop->iteration }}</td>
                                        <td class="text-sm px-6 py-4 font-fira-sans">{{ $address->address }}</td>
                                        <td class="text-sm px-6 py-4 flex gap-3">
                                            <a href="javascript:void(0)" onclick="editAddress({{ $address->id }})" type="button" data-modal-target="editAddress" data-modal-toggle="editAddress" data-te-ripple-color="light" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 rounded hover:bg-green-100 transition border border-green-100">
                                                <i class="fas fa-edit mr-1.5"></i> <span class="font-fira-sans font-medium text-xs uppercase">{{ __('Edit') }}</span>
                                            </a>
                                            <a href="javascript:void(0)" onclick="deleteData({{ $address->id }})" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded hover:bg-red-100 transition border border-red-100">
                                                <i class="fas fa-trash-alt mr-1.5"></i> <span class="font-fira-sans font-medium text-xs uppercase">{{ __('Delete') }}</span>
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

{{-- add address --}}
<div id="exampleModalScrollableLabel" class="fixed top-0 left-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none" tabindex="-1">
  <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 transition-all duration-300 ease-in-out w-full max-w-2xl max-h-full">
    <div class="pointer-events-auto relative flex max-h-[100%] w-auto flex-col overflow-hidden rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none dark:bg-neutral-600">
      <div class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50">
        <h5 class="text-xl font-medium leading-normal text-neutral-800 dark:text-neutral-200" id="exampleModalScrollableLabel"> {{ __('Add Address') }}</h5>
        <button type="button" class="box-content rounded-none font-fira-sans border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none" data-modal-hide="exampleModalScrollableLabel" aria-label="Close">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="relative overflow-y-auto p-4">
        <form class="addAddress" method="post">
          <input type="hidden" name="from" value="add_new">
          <div class="w-auto border border-white-light" id="map" style="height: 200px">{{ __('Rajkot') }}</div>
          <input type="hidden" name="lat" class="lat" value="{{ $setting->lat }}">
          <input type="hidden" name="lang" class="lng" value="{{ $setting->lang }}">
          <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
          <textarea name="address" class="mt-2 form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white-50 bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="exampleFormControlTextarea1" rows="3" placeholder="{{ __('Your message') }}"></textarea>
          <span class="invalid-div text-red"><span class="address text-sm  text-red-600 font-fira-sans"></span></span>
        </form>
      </div>
      <div class="flex flex-shrink-0 flex-wrap items-center justify-end rounded-b-md border-t-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50">
        <button type="button" class="inline-block rounded bg-white-100 font-fira-sans px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-primary-700 transition duration-150 ease-in-out hover:bg-primary-accent-100 focus:bg-primary-accent-100 focus:outline-none focus:ring-0 active:bg-primary-accent-200" data-modal-hide="exampleModalScrollableLabel" data-te-ripple-color="light">
        {{ __('Close') }}
        </button>
        <button type="button" onclick="addAddress()" class="ml-1 inline-block rounded bg-primary font-fira-sans px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]"> {{ __('Save changes') }}
        </button>
      </div>
    </div>
  </div>
</div>
</div>

{{-- edit address --}}
<div id="editAddress" class="fixed top-0 left-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none" tabindex="-1">
  <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 transition-all duration-300 ease-in-out w-full max-w-2xl max-h-full">
    <div class="pointer-events-auto relative flex max-h-[100%] w-full flex-col overflow-hidden rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none dark:bg-neutral-600">
      <div class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50">
        <h5 class="text-xl font-medium font-fira-sans leading-normal text-neutral-800 dark:text-neutral-200" id="editAddressLabel"> {{ __('Edit Address') }}</h5>
        <button type="button" class="box-content rounded-none border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none" data-modal-hide="editAddress" aria-label="Close">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="relative overflow-y-auto p-4">
        <form class="updateAddress" method="post">
          <input type="hidden" name="from" value="edit">
          <input type="hidden" name="id" id="address_id" value="">
          <div class="w-auto border border-white-light" id="map2" style="height: 200px">{{ __('Rajkot') }}</div>
          <input type="hidden" name="lat" class="lat" value="{{ $setting->lat }}">
          <input type="hidden" name="lang" class="lng" value="{{ $setting->lang }}">
          <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
          <textarea name="address" class="mt-2 form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white-50 bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="exampleFormControlTextarea1" rows="3" placeholder="{{ __('Your message') }}"></textarea>
          <span class="invalid-div text-red"><span class="address text-sm  text-red-600 font-fira-sans"></span></span>
        </form>
      </div>
      <div class="flex flex-shrink-0 flex-wrap items-center justify-end rounded-b-md border-t-2 border-neutral-100 border-opacity-100 p-4 dark:border-opacity-50">
        <button type="button" class="inline-block rounded bg-white-100 px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-primary-700 transition duration-150 ease-in-out hover:bg-primary-accent-100 focus:bg-primary-accent-100 focus:outline-none focus:ring-0 active:bg-primary-accent-200" data-modal-hide="editAddress" data-te-ripple-color="light">
        {{ __('Close') }}
        </button>
        <button type="button" onclick="updateAddress()" class="ml-1 inline-block rounded bg-primary px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]"> {{ __('Save changes') }}
        </button>
      </div>
    </div>
  </div>
</div>
</div>

@endsection
@section('js')
<script src="{{url('assets/js/address.js')}}"></script>
@if (App\Models\Setting::first()->map_key)
<script src="https://maps.googleapis.com/maps/api/js?key={{App\Models\Setting::first()->map_key}}&callback=initAutocomplete&libraries=places&v=weekly" async></script>
@endif
@endsection
