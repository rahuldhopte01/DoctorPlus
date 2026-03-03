@extends('layout.mainlayout_admin',['activePage' => 'cannaleo_pharmacy'])

@section('title', __('Cannaleo Pharmacies'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Cannaleo Pharmacies'),
        'url' => url('cannaleo/pharmacies'),
        'urlTitle' => __('Cannaleo Pharmacies'),
    ])
    @if (session('status'))
        @include('superAdmin.auth.status',['status' => session('status')])
    @endif
    <div class="section_body">
        <div class="card">
            <div class="card-header">
                <p class="mb-0 text-muted">{{ __('Synced from Curobo catalog API. Assign Cannaleo medicines to categories in Category edit.') }}</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="w-100 display table datatable text-center">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Domain') }}</th>
                                <th>{{ __('Medicines') }}</th>
                                <th>{{ __('Last synced') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pharmacies as $pharmacy)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pharmacy->name }}</td>
                                <td>{{ $pharmacy->domain ?? '—' }}</td>
                                <td>{{ $pharmacy->cannaleo_medicines_count }}</td>
                                <td>{{ $pharmacy->last_synced_at ? $pharmacy->last_synced_at->format('d.m.Y H:i') : '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">{{ __('No Cannaleo pharmacies yet. Run the catalog sync.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
