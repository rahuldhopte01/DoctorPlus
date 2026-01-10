@extends('layout.mainlayout_admin',['activePage' => 'medicine_master'])

@section('title',__('Medicine Details'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Medicine Details'),
        'url' => url('medicine_master'),
        'urlTitle' => __('Medicine Master')
    ])
    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>{{ $medicine->name }}</h4>
                <div class="card-header-action">
                    <span class="badge badge-{{ $medicine->status ? 'success' : 'secondary' }}">
                        {{ $medicine->status ? __('Active') : __('Inactive') }}
                    </span>
                    @can('medicine_edit')
                    <a class="btn btn-primary btn-sm ml-2" href="{{ url('medicine_master/'.$medicine->id.'/edit') }}">
                        <i class="far fa-edit"></i> {{__('Edit')}}
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">{{ __('Medicine Name') }}</h6>
                        <p class="mb-0">{{ $medicine->name }}</p>
                    </div>
                    @if($medicine->strength)
                    <div class="col-md-6">
                        <h6 class="text-muted">{{ __('Strength') }}</h6>
                        <p class="mb-0">{{ $medicine->strength }}</p>
                    </div>
                    @endif
                    @if($medicine->form)
                    <div class="col-md-6">
                        <h6 class="text-muted">{{ __('Form') }}</h6>
                        <p class="mb-0">{{ $medicine->form }}</p>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <h6 class="text-muted">{{ __('Status') }}</h6>
                        <p class="mb-0">
                            <span class="badge badge-{{ $medicine->status ? 'success' : 'secondary' }}">
                                {{ $medicine->status ? __('Active') : __('Inactive') }}
                            </span>
                        </p>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">{{ __('Medicine Brands') }} ({{ $medicine->brands->count() }})</h5>
                
                @if($medicine->brands->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{__('Brand Name')}}</th>
                                <th>{{__('Strength')}}</th>
                                <th>{{__('Status')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicine->brands as $brand)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $brand->brand_name }}</td>
                                <td>{{ $brand->strength ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $brand->status ? 'success' : 'secondary' }}">
                                        {{ $brand->status ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">{{__('No brands added for this medicine yet.')}}</p>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection
