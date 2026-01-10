@extends('layout.mainlayout_admin',['activePage' => 'pharmacy_registrations'])

@section('title',__('Pharmacy Registrations'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Pharmacy Registrations'),
    ])
    <div class="section-body">
        @if (session('status'))
            @include('superAdmin.auth.status',['status' => session('status')])
        @endif
        <div class="card">
            <div class="card-header w-100 text-right d-flex justify-content-between">
                @include('superAdmin.auth.exportButtons')
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="w-100 display table datatable">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>{{__('Pharmacy Name')}}</th>
                                <th>{{__('Owner/Admin')}}</th>
                                <th>{{__('Email')}}</th>
                                <th>{{__('Phone')}}</th>
                                <th>{{__('Address')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Priority')}}</th>
                                <th> {{__('Action')}} </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pharmacies as $pharmacy)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$pharmacy->name}}</td>
                                    <td>{{$pharmacy->owner->name ?? 'N/A'}}</td>
                                    <td class="text_transform_none">{{$pharmacy->email ?? 'N/A'}}</td>
                                    <td>{{$pharmacy->phone ?? 'N/A'}}</td>
                                    <td>{{$pharmacy->address}}</td>
                                    <td>
                                        @if($pharmacy->status == 'approved')
                                            <span class="badge badge-success">{{__('Approved')}}</span>
                                        @elseif($pharmacy->status == 'rejected')
                                            <span class="badge badge-danger">{{__('Rejected')}}</span>
                                        @else
                                            <span class="badge badge-warning">{{__('Pending')}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pharmacy->is_priority)
                                            <span class="badge badge-primary">{{__('Priority')}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="text-info" href="{{url('pharmacy_registrations/'.$pharmacy->id)}}">
                                            <i class="far fa-eye"></i>
                                        </a>
                                        @if($pharmacy->status == 'pending')
                                            @can('pharmacy_edit')
                                            <a class="text-success ml-2" href="javascript:void(0);" onclick="approvePharmacy({{ $pharmacy->id }})">
                                                <i class="far fa-check-circle"></i>
                                            </a>
                                            <a class="text-danger ml-2" href="javascript:void(0);" onclick="rejectPharmacy({{ $pharmacy->id }})">
                                                <i class="far fa-times-circle"></i>
                                            </a>
                                            @endcan
                                        @endif
                                        @can('pharmacy_edit')
                                        <a class="text-primary ml-2" href="javascript:void(0);" onclick="togglePriority({{ $pharmacy->id }})">
                                            <i class="far fa-star"></i>
                                        </a>
                                        @endcan
                                        @can('pharmacy_delete')
                                        <a class="text-danger ml-2" href="javascript:void(0);" onclick="deleteData('pharmacy_registrations',{{ $pharmacy->id }})">
                                            <i class="far fa-trash-alt"></i>
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function approvePharmacy(id) {
    if (confirm('{{ __("Are you sure you want to approve this pharmacy?") }}')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("pharmacy_registrations") }}/' + id + '/approve';
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectPharmacy(id) {
    if (confirm('{{ __("Are you sure you want to reject this pharmacy?") }}')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("pharmacy_registrations") }}/' + id + '/reject';
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}

function togglePriority(id) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ url("pharmacy_registrations") }}/' + id + '/toggle-priority';
    form.innerHTML = '@csrf';
    document.body.appendChild(form);
    form.submit();
}
</script>

@endsection
