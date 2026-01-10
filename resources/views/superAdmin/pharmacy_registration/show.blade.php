@extends('layout.mainlayout_admin',['activePage' => 'pharmacy_registrations'])

@section('title',__('Pharmacy Details'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Pharmacy Details'),
        'url' => url('pharmacy_registrations'),
        'urlTitle' => __('Pharmacy Registrations')
    ])
    <div class="section-body">
        @if (session('status'))
            @include('superAdmin.auth.status',['status' => session('status')])
        @endif
        
        <div class="card profile-widget mt-5">
            <div class="profile-widget-header">
                <div class="btn-group mb-2 dropleft float-right p-3">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ __('Actions') }}
                    </button>
                    <div class="dropdown-menu" x-placement="bottom-start">
                        @if($pharmacy->status == 'pending')
                            @can('pharmacy_edit')
                            <a class="dropdown-item" href="javascript:void(0);" onclick="approvePharmacy({{ $pharmacy->id }})">{{__('Approve')}}</a>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="rejectPharmacy({{ $pharmacy->id }})">{{__('Reject')}}</a>
                            @endcan
                        @endif
                        @can('pharmacy_edit')
                        <a class="dropdown-item" href="javascript:void(0);" onclick="togglePriority({{ $pharmacy->id }})">
                            {{ $pharmacy->is_priority ? __('Remove Priority') : __('Mark as Priority') }}
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="profile-widget-description">
                <div class="profile-widget-name">{{ $pharmacy->name }}</div>
                <div class="mt-3">
                    @if($pharmacy->status == 'approved')
                        <span class="badge badge-success">{{__('Approved')}}</span>
                    @elseif($pharmacy->status == 'rejected')
                        <span class="badge badge-danger">{{__('Rejected')}}</span>
                    @else
                        <span class="badge badge-warning">{{__('Pending')}}</span>
                    @endif
                    @if($pharmacy->is_priority)
                        <span class="badge badge-primary ml-2">{{__('Priority Pharmacy')}}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>{{__('Pharmacy Information')}}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">{{__('Pharmacy Name')}}</label>
                            <p>{{ $pharmacy->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">{{__('Owner/Admin')}}</label>
                            <p>{{ $pharmacy->owner->name ?? 'N/A' }}</p>
                            @if($pharmacy->owner)
                                <small class="text-muted">{{ $pharmacy->owner->email }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">{{__('Email')}}</label>
                            <p class="text_transform_none">{{ $pharmacy->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">{{__('Phone')}}</label>
                            <p>{{ $pharmacy->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold">{{__('Address')}}</label>
                            <p>{{ $pharmacy->address }}</p>
                            @if($pharmacy->postcode)
                                <p><strong>{{__('Postcode')}}:</strong> {{ $pharmacy->postcode }}</p>
                            @endif
                            @if($pharmacy->latitude && $pharmacy->longitude)
                                <p><strong>{{__('Coordinates')}}:</strong> {{ $pharmacy->latitude }}, {{ $pharmacy->longitude }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($pharmacy->deliverySettings)
        <div class="card">
            <div class="card-header">
                <h4>{{__('Delivery Settings')}}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">{{__('Delivery Type')}}</label>
                            <p>
                                @if($pharmacy->deliverySettings->delivery_type == 'pickup_only')
                                    {{__('Pickup Only')}}
                                @elseif($pharmacy->deliverySettings->delivery_type == 'delivery_only')
                                    {{__('Delivery Only')}}
                                @else
                                    {{__('Pickup + Delivery')}}
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($pharmacy->deliverySettings->delivery_radius)
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">{{__('Delivery Radius')}}</label>
                            <p>{{ $pharmacy->deliverySettings->delivery_radius }} km</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($pharmacy->deliveryMethods->count() > 0)
        <div class="card">
            <div class="card-header">
                <h4>{{__('Delivery Methods')}}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($pharmacy->deliveryMethods as $method)
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ucfirst(str_replace('_', ' ', $method->delivery_method))}}</label>
                            <p>
                                @if($method->is_active)
                                    <span class="badge badge-success">{{__('Active')}}</span>
                                @else
                                    <span class="badge badge-secondary">{{__('Inactive')}}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4>{{__('Registration Date')}}</h4>
            </div>
            <div class="card-body">
                <p><strong>{{__('Created')}}:</strong> {{ $pharmacy->created_at->format('d M Y, h:i A') }}</p>
                <p><strong>{{__('Updated')}}:</strong> {{ $pharmacy->updated_at->format('d M Y, h:i A') }}</p>
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
