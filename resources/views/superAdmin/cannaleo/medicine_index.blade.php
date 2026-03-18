@extends('layout.mainlayout_admin',['activePage' => 'cannaleo_medicine'])

@section('title', __('Cannaleo Medicines'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Cannaleo Medicines'),
        'url' => url('cannaleo/medicines'),
        'urlTitle' => __('Cannaleo Medicines'),
    ])
    @if (session('status'))
        @include('superAdmin.auth.status',['status' => session('status')])
    @endif
    <div class="section_body">
        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center gap-2">
                <form method="get" action="{{ url('cannaleo/medicines') }}" class="form-inline gap-2">
                    <select name="pharmacy_id" class="form-control form-control-sm">
                        <option value="">{{ __('All pharmacies') }}</option>
                        @foreach ($pharmacies as $p)
                            <option value="{{ $p->id }}" {{ request('pharmacy_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <select name="category_filter" class="form-control form-control-sm">
                        <option value="">{{ __('All categories') }}</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}" {{ request('category_filter') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">{{ __('Filter') }}</button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="w-100 display table datatable text-center">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Pharmacy') }}</th>
                                <th>{{ __('API category') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('THC / CBD') }}</th>
                                <th>{{ __('Assigned to categories') }}</th>
                                <th>{{ __('Image') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($medicines as $medicine)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $medicine->name }}</td>
                                <td>{{ $medicine->cannaleoPharmacy->name ?? '—' }}</td>
                                <td>{{ $medicine->category ?? '—' }}</td>
                                <td>{{ $medicine->price !== null ? number_format($medicine->price, 2) . ' €' : '—' }}</td>
                                <td>{{ ($medicine->thc !== null || $medicine->cbd !== null) ? (number_format($medicine->thc ?? 0, 1) . ' / ' . number_format($medicine->cbd ?? 0, 1)) : '—' }}</td>
                                <td>
                                    @if ($medicine->categories->isNotEmpty())
                                        @foreach ($medicine->categories as $cat)
                                            <span class="badge badge-secondary">{{ $cat->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($medicine->image)
                                        <img src="{{ asset('images/upload/'.$medicine->image) }}" alt="{{ $medicine->name }}" style="max-height:40px;max-width:60px;object-fit:cover;" class="img-thumbnail">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cannaleo.medicines.edit', $medicine->id) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">{{ __('No Cannaleo medicines yet. Run the catalog sync and assign in Category edit.') }}</td>
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
