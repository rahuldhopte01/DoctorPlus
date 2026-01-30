@extends('layout.mainlayout_admin',['activePage' => 'clinic_doctors'])

@section('title', __('Manage Sub-Doctors'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Clinic Sub-Doctors'),
    ])
    @if (session('status'))
        @include('superAdmin.auth.status',[
            'status' => session('status')])
    @endif
    <div class="section_body">
        <div class="card">
            <div class="card-header w-100 text-right d-flex justify-content-between">
                <div>
                    <span class="badge badge-primary p-2">
                        <i class="fas fa-clinic-medical"></i> 
                        {{ $clinic->name ?? __('No Clinic Assigned') }}
                    </span>
                </div>
                <div>
                    <a href="{{ route('clinic.doctors.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Add Sub-Doctor') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(!$clinic)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ __('You are not assigned to any clinic. Please contact the administrator.') }}
                    </div>
                @elseif($doctors->count() == 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        {{ __('No sub-doctors found for your clinic. Click "Add Sub-Doctor" to create one.') }}
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Doctor Name') }}</th>
                                    <th>{{ __('email') }}</th>
                                    <th>{{ __('Speciality') }}</th>
                                    <th>{{ __('Based On') }}</th>
                                    <th>{{ __('Member Since') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($doctors as $doctor)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="avatar avatar-sm mr-2">
                                                <img class="avatar-img rounded-circle" src="{{ $doctor->fullImage }}" alt="doctor Image">
                                            </a>
                                            {{ $doctor->name }}
                                        </td>
                                        <td>
                                            <a href="mailto:{{ $doctor->user['email'] ?? '' }}">
                                                <span class="text_transform_none">{{ $doctor->user['email'] ?? '' }}</span>
                                            </a>
                                        </td>
                                        <td>
                                            @if ($doctor->expertise != null)
                                                {{ $doctor->expertise['name'] }}
                                            @else
                                                {{ __('Not define') }}
                                            @endif
                                        </td>
                                        <td>{{ $doctor->based_on }}</td>
                                        @php
                                            $since = explode(" , ", $doctor->since)
                                        @endphp
                                        <td>{{ $since[0] ?? '' }}<br><small>{{ $since[1] ?? '' }}</small></td>
                                        <td>
                                            <label class="cursor-pointer">
                                                <input type="checkbox" id="status_{{ $doctor->id }}" class="custom-switch-input" onchange="changeSubDoctorStatus({{ $doctor->id }})" {{ $doctor->status == 1 ? 'checked' : '' }}>
                                                <span class="custom-switch-indicator"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <a class="text-success" href="{{ route('clinic.doctors.edit', $doctor->id) }}" title="{{ __('Edit') }}">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <a class="text-danger" href="javascript:void(0);" onclick="deleteSubDoctor({{ $doctor->id }})" title="{{ __('Delete') }}">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
    function changeSubDoctorStatus(id) {
        $.ajax({
            type: "POST",
            url: "{{ route('clinic.doctors.status') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                "id": id
            },
            success: function(result) {
                if (result.success) {
                    toastr.success("{{ __('Status changed successfully') }}");
                } else {
                    toastr.error("{{ __('Failed to change status') }}");
                }
            },
            error: function(error) {
                toastr.error("{{ __('Something went wrong') }}");
            }
        });
    }

    function deleteSubDoctor(id) {
        if (confirm("{{ __('Are you sure you want to delete this sub-doctor?') }}")) {
            $.ajax({
                type: "DELETE",
                url: "{{ url('doctor/clinic-doctors') }}/" + id,
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(result) {
                    if (result.success) {
                        toastr.success("{{ __('Sub-doctor deleted successfully') }}");
                        location.reload();
                    } else {
                        toastr.error(result.data || "{{ __('Failed to delete sub-doctor') }}");
                    }
                },
                error: function(error) {
                    toastr.error("{{ __('Something went wrong') }}");
                }
            });
        }
    }
</script>
@endsection
