@extends('layout.mainlayout_admin',['activePage' => 'cannaleo_order_list'])

@section('title', __('Cannaleo Log') . ' #' . $log->id)
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Cannaleo Log') . ' #' . $log->id,
        'url' => url('cannaleo/prescription-logs'),
        'urlTitle' => __('Cannaleo Order List'),
    ])
    <div class="section_body">
        <div class="card">
            <div class="card-header">
                <h5>{{ __('Log') }} #{{ $log->id }}</h5>
                <a href="{{ url('cannaleo/prescription-logs') }}" class="btn btn-outline-primary btn-sm">{{ __('Back to list') }}</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">{{ __('Date requested') }}</th>
                        <td>{{ $log->called_at ? $log->called_at->format('d.m.Y H:i:s') : '—' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Prescription ID') }}</th>
                        <td>{{ $log->prescription_id ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Questionnaire submission ID') }}</th>
                        <td>{{ $log->questionnaire_submission_id ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Total medicine cost') }}</th>
                        <td>{{ $log->total_medicine_cost !== null ? number_format($log->total_medicine_cost, 2) : '—' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Prescription fee') }}</th>
                        <td>{{ $log->prescription_fee !== null ? number_format($log->prescription_fee, 2) : '—' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Response status') }}</th>
                        <td>
                            @if($log->response_status !== null)
                                <span class="badge {{ $log->response_status >= 200 && $log->response_status < 300 ? 'badge-success' : 'badge-danger' }}">{{ $log->response_status }}</span>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('External order ID') }}</th>
                        <td>{{ $log->external_order_id ?? '—' }}</td>
                    </tr>
                    @if($log->error_message)
                    <tr>
                        <th>{{ __('Error message') }}</th>
                        <td class="text-danger">{{ $log->error_message }}</td>
                    </tr>
                    @endif
                </table>

                @if($log->products_snapshot && count($log->products_snapshot) > 0)
                <h6 class="mt-4">{{ __('Products snapshot') }}</h6>
                <pre class="bg-light p-3 rounded small">{{ json_encode($log->products_snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif

                @if($log->request_payload)
                <h6 class="mt-4">{{ __('Request payload') }}</h6>
                <pre class="bg-light p-3 rounded small" style="max-height: 300px; overflow: auto;">{{ json_encode($log->request_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif

                @if($log->response_body)
                <h6 class="mt-4">{{ __('Response body') }}</h6>
                <pre class="bg-light p-3 rounded small" style="max-height: 300px; overflow: auto;">{{ is_string($log->response_body) ? $log->response_body : json_encode($log->response_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
