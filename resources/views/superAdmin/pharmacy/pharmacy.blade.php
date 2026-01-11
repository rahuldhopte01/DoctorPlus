@extends('layout.mainlayout_admin',['activePage' => 'pharmacy'])

@section('title',__('All pharmacy'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Pharmacy'),
    ])
    @if (session('status'))
        @include('superAdmin.auth.status',[
            'status' => session('status')])
        @endif
        <div class="card">
            <div class="card-body">
                <div id="pharmacies-map" style="width: 100%; height: 300px;"></div>
                <script defer
                    src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\Setting::select('map_key')->first()->map_key }}&callback=initMap&loading=async">
                </script>
                <script>
                    function initMap() {
                        var locations = @json($pharamacies);
                        var center = {
                            lat: locations.length ? locations.reduce((sum, loc) => sum + parseFloat(loc.lat), 0) / locations.length : {{ $center_coords->lat }},
                            lng: locations.length ? locations.reduce((sum, loc) => sum + parseFloat(loc.lang), 0) / locations.length : {{ $center_coords->lang }}
                        };
                        var map = new google.maps.Map(document.getElementById('pharmacies-map'), {
                            zoom: locations.length > 1 ? Math.min(15, Math.max(8, 12 - Math.log(locations.length))) : 13,
                            center: center
                        });
                        var infowindow = new google.maps.InfoWindow({});
                        var marker;
                        for (i = 0; i < locations.length; i++) {
                            marker = new google.maps.Marker({
                                position: new google.maps.LatLng(locations[i].lat, locations[i].lang),
                                map: map,
                                title: locations[i].name
                            });
                            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                                return function() {
                                    infowindow.setContent(
                                        '<div style="padding: 10px;">' +
                                        '<h4 style="margin: 0 0 5px 0;">' + locations[i].name + '</h4>' +
                                        '<p style="margin: 0 0 5px 0;"><strong>Description:</strong> ' + locations[i].description + '</p>' +
                                        '<p style="margin: 0 0 5px 0;"><strong>Address:</strong> ' + locations[i].address + '</p>' +
                                        '<p style="margin: 0;"><strong>Phone:</strong> ' + locations[i].phone + '</p>' +
                                        '<a href="https://www.google.com/maps/@' + locations[i].lat + ',' + locations[i].lang + ',10z" target="_blank">View in Google Maps</a>' +
                                        '</div>'
                                    );
                                    infowindow.open(map, marker);
                                }
                            })(marker, i));
                        }
                    }
                </script>
            </div>
            <div class="card-header w-100 text-right d-flex justify-content-between">
                @include('superAdmin.auth.exportButtons')
                @can('pharmacy_add')
                    <a href="{{url('pharmacy/create')}}">{{__('Add New')}}</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="w-100 display table datatable text-center">
                        <thead>
                            <tr>
                                <th>
                                    <input name="select_all" value="1" id="master" type="checkbox" />
                                    <label for="master"></label>
                                </th>
                                <th> # </th>
                                <th>{{__('Image')}}</th>
                                <th>{{__('Pharmacy name')}}</th>
                                <th>{{__('email')}}</th>
                                <th>{{__('phone')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Priority')}}</th>
                                @if (Gate::check('pharmacy_edit') || Gate::check('pharmacy_delete'))
                                    <th> {{__('Action')}} </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pharamacies as $pharmacy)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="id[]" value="{{$pharmacy->id}}" id="{{$pharmacy->id}}" data-id="{{ $pharmacy->id }}" class="sub_chk">
                                        <label for="{{$pharmacy->id}}"></label>
                                    </td>
                                    <td>{{$loop->iteration}}</td>
                                    <td>
                                        <a href="{{ $pharmacy->fullImage }}" data-fancybox="gallery2">
                                            <img class="avatar-img rounded-circle" alt="Pharamcy Image" src="{{ $pharmacy->fullImage }}" height="50" width="50">
                                        </a>
                                    </td>
                                    <td>{{$pharmacy->name}}</td>
                                    <td>
                                        <span class="text_transform_none">{{$pharmacy->email}}</span>
                                    </td>
                                    <td>{{$pharmacy->phone}}</td>

                                    <td>
                                        @if($pharmacy->status == 'pending')
                                            <span class="badge badge-warning">{{__('Pending')}}</span>
                                        @elseif($pharmacy->status == 'approved')
                                            <span class="badge badge-success">{{__('Approved')}}</span>
                                        @elseif($pharmacy->status == 'rejected')
                                            <span class="badge badge-danger">{{__('Rejected')}}</span>
                                        @else
                                            <span class="badge badge-secondary">{{$pharmacy->status}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('pharmacy_edit')
                                        <label class="cursor-pointer">
                                            <input type="checkbox" id="priority{{$pharmacy->id}}" class="custom-switch-input" name="is_priority" onchange="toggle_priority({{ $pharmacy->id }})" {{ $pharmacy->is_priority ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                        @else
                                        {{ $pharmacy->is_priority ? __('Yes') : __('No') }}
                                        @endcan
                                    </td>
                                    @if (Gate::check('pharmacy_edit') || Gate::check('pharmacy_delete'))
                                        <td>
                                            <a class="text-primary" href="{{url('pharmacy/'.$pharmacy->id)}}" title="{{__('View')}}">
                                                <i class="far fa-eye"></i>
                                            </a>
                                            @can('pharmacy_edit')
                                            @if($pharmacy->status == 'pending')
                                                <a class="text-success" href="javascript:void(0);" onclick="approve_pharmacy({{ $pharmacy->id }})" title="{{__('Approve')}}">
                                                    <i class="far fa-check-circle"></i>
                                                </a>
                                                <a class="text-danger" href="javascript:void(0);" onclick="reject_pharmacy({{ $pharmacy->id }})" title="{{__('Reject')}}">
                                                    <i class="far fa-times-circle"></i>
                                                </a>
                                            @endif
                                            <a class="text-success" href="{{url('pharmacy/'.$pharmacy->id.'/edit/')}}" title="{{__('Edit')}}">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('pharmacy_delete')
                                                <a class="text-danger" href="javascript:void(0);"  onclick="deleteData('pharmacy',{{ $pharmacy->id }})" title="{{__('Delete')}}">
                                                    <i class="far fa-trash-alt"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-fotter">
                <input type="button" value="delete selected" onclick="deleteAll('pharmacy_all_delete')" class="btn btn-primary">
            </div>
        </div>
</section>

@endsection

@section('js')
<script>
function approve_pharmacy(id) {
    Swal.fire({
        title: '{{__("Are you sure?")}}',
        text: '{{__("Do you want to approve this pharmacy?")}}',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{__("Yes, approve it!")}}'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: base_url + '/pharmacy/approve',
                data: {
                    id: id,
                },
                success: function (result) {
                    if (result.success) {
                        Swal.fire('{{__("Approved!")}}', result.message || '{{__("Pharmacy approved successfully.")}}', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('{{__("Error!")}}', result.message || '{{__("Something went wrong.")}}', 'error');
                    }
                },
                error: function (err) {
                    Swal.fire('{{__("Error!")}}', '{{__("Something went wrong.")}}', 'error');
                }
            });
        }
    });
}

function reject_pharmacy(id) {
    Swal.fire({
        title: '{{__("Are you sure?")}}',
        text: '{{__("Do you want to reject this pharmacy?")}}',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{__("Yes, reject it!")}}'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: base_url + '/pharmacy/reject',
                data: {
                    id: id,
                },
                success: function (result) {
                    if (result.success) {
                        Swal.fire('{{__("Rejected!")}}', result.message || '{{__("Pharmacy rejected successfully.")}}', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('{{__("Error!")}}', result.message || '{{__("Something went wrong.")}}', 'error');
                    }
                },
                error: function (err) {
                    Swal.fire('{{__("Error!")}}', '{{__("Something went wrong.")}}', 'error');
                }
            });
        }
    });
}

function toggle_priority(id) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "POST",
        url: base_url + '/pharmacy/toggle_priority',
        data: {
            id: id,
        },
        success: function (result) {
            if (!result.success) {
                $('#priority' + id).prop('checked', !$('#priority' + id).prop('checked'));
                Swal.fire('{{__("Error!")}}', result.message || '{{__("Something went wrong.")}}', 'error');
            }
        },
        error: function (err) {
            $('#priority' + id).prop('checked', !$('#priority' + id).prop('checked'));
            Swal.fire('{{__("Error!")}}', '{{__("Something went wrong.")}}', 'error');
        }
    });
}
</script>
@endsection
