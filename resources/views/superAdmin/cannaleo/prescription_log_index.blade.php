@extends('layout.mainlayout_admin',['activePage' => 'cannaleo_order_list'])

@section('title', __('Cannaleo Order List'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Cannaleo Order List'),
        'url' => url('cannaleo/prescription-logs'),
        'urlTitle' => __('Cannaleo Order List'),
    ])
    @if (session('status'))
        @include('superAdmin.auth.status',['status' => session('status')])
    @endif
    <div class="section_body">
        <div class="card">
            <div class="card-header">
                <p class="mb-0 text-muted">{{ __('Logs of Cannaleo/Curobo prescription API calls. Shows when each order was requested, costs and response status.') }}</p>
            </div>
            <div class="card-body">
                <form method="get" class="mb-4 row g-3 align-items-end">
                    <div class="col-auto">
                        <label class="form-label">{{ __('From date') }}</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-auto">
                        <label class="form-label">{{ __('To date') }}</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-auto">
                        <label class="form-label">{{ __('Prescription ID') }}</label>
                        <input type="number" name="prescription_id" class="form-control" placeholder="ID" value="{{ request('prescription_id') }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                        <a href="{{ url('cannaleo/prescription-logs') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="w-100 table text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Date requested') }}</th>
                                <th>{{ __('Prescription ID') }}</th>
                                <th>{{ __('Submission ID') }}</th>
                                <th>{{ __('Total medicine cost') }}</th>
                                <th>{{ __('Prescription fee') }}</th>
                                <th>{{ __('Response status') }}</th>
                                <th>{{ __('External order ID') }}</th>
                                <th>{{ __('Error') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->called_at ? $log->called_at->format('d.m.Y H:i') : '—' }}</td>
                                <td>{{ $log->prescription_id ?? '—' }}</td>
                                <td>{{ $log->questionnaire_submission_id ?? '—' }}</td>
                                <td>{{ $log->total_medicine_cost !== null ? number_format($log->total_medicine_cost, 2) : '—' }}</td>
                                <td>{{ $log->prescription_fee !== null ? number_format($log->prescription_fee, 2) : '—' }}</td>
                                <td>
                                    @if($log->response_status !== null)
                                        <span class="badge {{ $log->response_status >= 200 && $log->response_status < 300 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $log->response_status }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $log->external_order_id ?? '—' }}</td>
                                <td>
                                    @if($log->error_message)
                                        <span class="text-danger" title="{{ Str::limit($log->error_message, 100) }}">{{ Str::limit($log->error_message, 30) }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('cannaleo/prescription-logs/' . $log->id) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">{{ __('No Cannaleo prescription logs yet.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($logs->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
