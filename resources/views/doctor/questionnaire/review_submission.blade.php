@extends('layout.mainlayout_admin',['activePage' => 'questionnaire_submissions'])

@section('title', __('Questionnaire Review'))

@section('content')
<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Questionnaire Review'),
    ])

    <div class="section-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif
        <!-- Patient & Questionnaire Info -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-user mr-2"></i>{{ __('Patient Information') }}</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">{{ __('Name') }}</td>
                                <td><strong>{{ $firstAnswer->user->name ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Email') }}</td>
                                <td>{{ $firstAnswer->user->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Phone') }}</td>
                                <td>{{ $firstAnswer->user->phone ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-clipboard-list mr-2"></i>{{ __('Questionnaire Info') }}</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">{{ __('Questionnaire') }}</td>
                                <td><strong>{{ $firstAnswer->questionnaire->name ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Category') }}</td>
                                <td>{{ $firstAnswer->category->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Version') }}</td>
                                <td>v{{ $firstAnswer->questionnaire_version ?? '1' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Submitted At') }}</td>
                                <td>{{ $firstAnswer->submitted_at ? \Carbon\Carbon::parse($firstAnswer->submitted_at)->format('M d, Y H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ __('Status') }}</td>
                                <td>
                                    @if($firstAnswer->status === 'pending')
                                        <span class="badge badge-warning">{{ __('Pending') }}</span>
                                    @elseif(in_array($firstAnswer->status, ['under_review', 'IN_REVIEW']))
                                        <span class="badge badge-info">{{ __('Under Review') }}</span>
                                    @elseif($firstAnswer->status === 'approved')
                                        <span class="badge badge-success">{{ __('Approved') }}</span>
                                    @elseif($firstAnswer->status === 'rejected')
                                        <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $firstAnswer->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Selections (Delivery, Medicines, Pharmacy) -->
        @if(isset($submission) && $submission)
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-shopping-cart mr-2"></i>{{ __('Patient Selections') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Delivery Type -->
                            <div class="col-md-6 mb-4">
                                <h5 class="font-weight-bold mb-3">{{ __('Delivery Method') }}</h5>
                                @if($submission->delivery_type === 'delivery')
                                    <p><i class="fas fa-truck text-primary mr-2"></i><strong>{{ __('Home Delivery') }}</strong></p>
                                    @if($submission->hasCompleteDeliveryAddress())
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <p class="mb-1"><strong>{{ __('Delivery Address') }}:</strong></p>
                                            <p class="mb-1">{{ $submission->delivery_address }}</p>
                                            <p class="mb-1">{{ $submission->delivery_city }}, {{ $submission->delivery_state }}</p>
                                            <p class="mb-0">{{ __('Postcode') }}: {{ $submission->delivery_postcode }}</p>
                                        </div>
                                    @else
                                        <p class="text-warning">{{ __('Address incomplete') }}</p>
                                    @endif
                                @elseif($submission->delivery_type === 'pickup')
                                    <p><i class="fas fa-store text-primary mr-2"></i><strong>{{ __('Pharmacy Pickup') }}</strong></p>
                                    @if($submission->selectedPharmacy)
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <p class="mb-1"><strong>{{ __('Selected Pharmacy') }}:</strong></p>
                                            <p class="mb-1">{{ $submission->selectedPharmacy->name }}</p>
                                            <p class="mb-1">{{ $submission->selectedPharmacy->address }}</p>
                                            @if($submission->selectedPharmacy->phone)
                                            <p class="mb-0">{{ __('Phone') }}: {{ $submission->selectedPharmacy->phone }}</p>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-warning">{{ __('No pharmacy selected') }}</p>
                                    @endif
                                @else
                                    <p class="text-muted">{{ __('Not selected yet') }}</p>
                                @endif
                            </div>

                            <!-- Selected Medicines -->
                            <div class="col-md-6 mb-4">
                                <h5 class="font-weight-bold mb-3">{{ __('Selected Medicines') }}</h5>
                                @if(isset($selectedMedicines) && count($selectedMedicines) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Medicine') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($selectedMedicines as $selected)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $selected['medicine']->name }}</strong>
                                                        @if($selected['medicine']->strength)
                                                            <br><small class="text-muted">{{ __('Strength') }}: {{ $selected['medicine']->strength }}</small>
                                                        @endif
                                                        @if($selected['medicine']->brand)
                                                            <br><small class="text-primary">{{ $selected['medicine']->brand->name }}</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="text-muted mt-2"><small><i class="fas fa-info-circle"></i> {{ __('You can modify these medicines when creating the prescription.') }}</small></p>
                                @else
                                    <p class="text-muted">{{ __('No medicines selected yet') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Status Update Form -->
        <div class="card mb-3">
            <div class="card-header">
                <h4><i class="fas fa-edit mr-2"></i>{{ __('Update Status') }}</h4>
            </div>
            <div class="card-body">
                @if($canEdit)
                <form id="questionnaire-status-form" action="{{ route('doctor.questionnaire.update-status', [
                    'userId' => $firstAnswer->user_id,
                    'categoryId' => $firstAnswer->category_id,
                    'questionnaireId' => $firstAnswer->questionnaire_id
                ]) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Status') }}</label>
                                <select name="status" class="form-control" required id="status-select">
                                    <option value="pending" {{ $firstAnswer->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="under_review" {{ in_array($firstAnswer->status, ['under_review', 'IN_REVIEW']) ? 'selected' : '' }}>{{ __('Under Review') }}</option>
                                    <option value="approved" {{ $firstAnswer->status === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                    <option value="rejected" {{ $firstAnswer->status === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block" id="status-update-btn">
                                    <i class="fas fa-save mr-2"></i>{{ __('Update Status') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                @else
                <p class="text-muted mb-0">{{ __('You can view this questionnaire but cannot change its status.') }}</p>
                @endif
            </div>
        </div>

        <!-- Medicine Assignment Section (shown when IN_REVIEW or approved) -->
        @if(in_array($firstAnswer->status, ['IN_REVIEW', 'under_review', 'approved']) && $canEdit)
        <div class="card mb-3" id="medicine-assignment-section">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-prescription-bottle-alt mr-2"></i>{{ __('Assign Medicines & Generate Prescription') }}</h4>
            </div>
            <div class="card-body">
                @if($firstAnswer->status === 'approved' && $prescription)
                <div class="alert alert-info">
                    <i class="fas fa-prescription-bottle-alt mr-2"></i>
                    <strong>{{ __('Prescription Already Created') }}</strong>
                    <p class="mb-0 mt-2">{{ __('A prescription has been created for this questionnaire. View details below.') }}</p>
                </div>
                @else
                <!-- Patient Preferences Reminder -->
                @if(isset($submission) && $submission && isset($selectedMedicines) && count($selectedMedicines) > 0)
                <div class="alert alert-info mb-4">
                    <h5 class="mb-2"><i class="fas fa-user-check mr-2"></i>{{ __("Patient's Medicine Preferences") }}</h5>
                    <p class="mb-2">{{ __('The patient has selected the following medicines. You can keep, modify, or replace them:') }}</p>
                    <ul class="mb-0">
                        @foreach($selectedMedicines as $selected)
                        <li>
                            <strong>{{ $selected['medicine']->name }}</strong>
                            @if($selected['medicine']->strength)
                                <span class="text-muted">({{ $selected['medicine']->strength }})</span>
                            @endif
                            @if($selected['medicine']->brand)
                                <span class="badge badge-primary">{{ $selected['medicine']->brand->name }}</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Medicine Assignment Form -->
                <form id="prescription-form" action="{{ route('doctor.questionnaire.store-prescription', [
                    'userId' => $firstAnswer->user_id,
                    'categoryId' => $firstAnswer->category_id,
                    'questionnaireId' => $firstAnswer->questionnaire_id
                ]) }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="col-form-label mb-3">
                            <strong>{{ __('Select Medicines from Category') }}</strong> 
                            <span class="text-danger">*</span>
                            <small class="text-muted d-block">{{ __('Category:') }} {{ $firstAnswer->category->name ?? 'N/A' }}</small>
                        </label>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="medicinesTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="45%">{{ __('Medicine Name') }}</th>
                                        <th width="50%">{{ __('Strength') }}</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="medicinesBody">
                                    @forelse(isset($selectedMedicines) && count($selectedMedicines) > 0 ? $selectedMedicines : [] as $selected)
                                    <tr class="medicine-row">
                                        <td>
                                            <select name="medicines[]" class="form-control select2 medicine-select" required>
                                                <option value="">{{ __('Select Medicine') }}</option>
                                                @foreach (isset($categoryMedicines) ? $categoryMedicines : [] as $medicine)
                                                    <option value="{{ $medicine->id }}" 
                                                            data-name="{{ $medicine->name }}"
                                                            data-strength="{{ $medicine->strength ?? '' }}"
                                                            {{ ($selected['medicine']->id ?? null) == $medicine->id ? 'selected' : '' }}>
                                                        {{ $medicine->name }}{{ $medicine->strength ? ' (' . $medicine->strength . ')' : '' }}
                                                        @if($medicine->brand)
                                                            - {{ $medicine->brand->name }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="medicine_names[]" class="medicine-name-input" value="{{ $selected['medicine']->name ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="text" name="strength[]" class="form-control strength-input" 
                                                   placeholder="e.g., 500mg" value="{{ $selected['medicine']->strength ?? '' }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-medicine">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="medicine-row">
                                        <td>
                                            <select name="medicines[]" class="form-control select2 medicine-select" required>
                                                <option value="">{{ __('Select Medicine') }}</option>
                                                @foreach (isset($categoryMedicines) ? $categoryMedicines : [] as $medicine)
                                                    <option value="{{ $medicine->id }}" 
                                                            data-name="{{ $medicine->name }}"
                                                            data-strength="{{ $medicine->strength ?? '' }}">
                                                        {{ $medicine->name }}{{ $medicine->strength ? ' (' . $medicine->strength . ')' : '' }}
                                                        @if($medicine->brand)
                                                            - {{ $medicine->brand->name }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="medicine_names[]" class="medicine-name-input">
                                        </td>
                                        <td>
                                            <input type="text" name="strength[]" class="form-control strength-input" 
                                                   placeholder="e.g., 500mg">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-medicine" style="display:none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" id="addMedicine">
                                <i class="fas fa-plus mr-2"></i>{{ __('Add Medicine') }}
                            </button>
                        </div>
                    </div>

                    <div class="text-right mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-prescription-bottle-alt mr-2"></i>
                            {{ __('Approve & Generate Prescription') }}
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
        @endif

        <!-- Orders Created -->
        @if(isset($orders) && $orders->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h4><i class="fas fa-shopping-cart mr-2"></i>{{ __('Orders Created') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="15%">{{ __('Order ID') }}</th>
                                <th width="15%">{{ __('Amount') }}</th>
                                <th width="15%">{{ __('Payment Status') }}</th>
                                <th width="15%">{{ __('Delivery Type') }}</th>
                                <th width="20%">{{ __('Pharmacy') }}</th>
                                <th width="20%">{{ __('Shipping Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td><strong>{{ $order->medicine_id }}</strong></td>
                                <td>{{ $order->amount ?? 'N/A' }}</td>
                                <td>
                                    @if($order->payment_status == 1)
                                        <span class="badge badge-success">{{ __('Paid') }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ __('Pending') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->pharmacy_id)
                                        <span class="badge badge-info">{{ __('Pickup') }}</span>
                                    @elseif($order->address_id)
                                        <span class="badge badge-primary">{{ __('Delivery') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('N/A') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->pharmacy_id && $order->pharmacy)
                                        {{ $order->pharmacy->name }}
                                    @else
                                        <span class="text-muted">{{ __('Not assigned') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->shipping_at)
                                        {{ \Carbon\Carbon::parse($order->shipping_at)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">{{ __('Not set') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Prescription Details -->
        @if($prescription)
        <div class="card mb-3">
            <div class="card-header">
                <h4><i class="fas fa-prescription-bottle-alt mr-2"></i>{{ __('Prescription Details') }}</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">{{ __('Status') }}</td>
                                <td>
                                    @if($prescription->status === 'approved_pending_payment')
                                        <span class="badge badge-warning">{{ __('Approved - Pending Payment') }}</span>
                                    @elseif($prescription->status === 'active')
                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                    @elseif($prescription->status === 'approved')
                                        <span class="badge badge-info">{{ __('Approved') }}</span>
                                    @elseif($prescription->status === 'expired')
                                        <span class="badge badge-danger">{{ __('Expired') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $prescription->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <h5 class="mb-3">{{ __('Medicines') }}</h5>
                @php
                    $medicines = json_decode($prescription->medicines, true);
                @endphp
                @if($medicines && count($medicines) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="60%">{{ __('Medicine Name') }}</th>
                                <th width="35%">{{ __('Strength') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicines as $index => $medicine)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $medicine['medicine'] ?? 'N/A' }}</strong></td>
                                <td>{{ $medicine['strength'] ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ __('No medicines found in this prescription.') }}
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Flag Alert -->
        @if($hasFlaggedAnswers)
        <div class="alert alert-warning mb-3">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>{{ __('Attention:') }}</strong> {{ __('This questionnaire contains flagged answers that require your review.') }}
        </div>
        @endif

        <!-- Questionnaire Answers -->
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-file-medical-alt mr-2"></i>{{ __('Questionnaire Answers') }}</h4>
            </div>
            <div class="card-body">
                @forelse($groupedAnswers as $sectionName => $sectionAnswers)
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-folder-open text-primary mr-2"></i>
                        {{ $sectionName }}
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="40%">{{ __('Question') }}</th>
                                    <th width="35%">{{ __('Answer') }}</th>
                                    <th width="20%">{{ __('Notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sectionAnswers as $index => $answer)
                                <tr class="{{ $answer['is_flagged'] ? 'table-warning' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $answer['question'] }}
                                        @if($answer['is_flagged'])
                                            <span class="badge badge-danger ml-2">
                                                <i class="fas fa-flag"></i> {{ __('Flagged') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($answer['field_type'] === 'file' && $answer['file_url'])
                                            <a href="{{ $answer['file_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download mr-1"></i>{{ __('Download File') }}
                                            </a>
                                            @if(isset($answer['file_name']))
                                                <br><small class="text-muted d-block mt-1">
                                                    <i class="fas fa-file mr-1"></i>{{ basename($answer['file_name']) }}
                                                </small>
                                            @endif
                                        @else
                                            <div class="answer-text">
                                                {{ $answer['answer'] }}
                                            </div>
                                        @endif
                                        
                                        @if($answer['is_flagged'] && $answer['flag_reason'])
                                            <div class="mt-2 p-2 bg-danger-light rounded">
                                                <small class="text-danger font-weight-bold">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    <strong>{{ __('Flag Reason:') }}</strong> {{ $answer['flag_reason'] }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($answer['doctor_notes'])
                                            <small class="text-info d-block">
                                                <i class="fas fa-sticky-note mr-1"></i>
                                                {{ $answer['doctor_notes'] }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ __('No questionnaire answers found.') }}</p>
                </div>
                @endforelse
            </div>
            <div class="card-footer">
                <a href="{{ route('doctor.questionnaire.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to List') }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Medicine assignment form functionality
    let rowIndex = 0;
    const categoryMedicines = @json(isset($categoryMedicines) ? $categoryMedicines->map(function($m) {
        return [
            'id' => $m->id,
            'name' => $m->name,
            'strength' => $m->strength ?? '',
            'brand' => ($m->brand ? $m->brand->name : '')
        ];
    })->values() : []);
    
    // Add medicine row
    $('#addMedicine').click(function() {
        rowIndex++;
        // Clone options from the first existing select
        const firstSelect = $('#medicinesBody .medicine-select').first();
        let optionsHtml = '';
        if (firstSelect.length > 0) {
            firstSelect.find('option').each(function() {
                const $option = $(this);
                optionsHtml += `<option value="${$option.val()}" data-name="${$option.data('name') || ''}" data-strength="${$option.data('strength') || ''}">${$option.text()}</option>`;
            });
        } else {
            // Fallback: generate from categoryMedicines array
            optionsHtml = '<option value="">{{ __('Select Medicine') }}</option>';
            categoryMedicines.forEach(function(medicine) {
                const displayName = medicine.name + (medicine.strength ? ' (' + medicine.strength + ')' : '') + (medicine.brand ? ' - ' + medicine.brand : '');
                optionsHtml += `<option value="${medicine.id}" data-name="${medicine.name}" data-strength="${medicine.strength || ''}">${displayName}</option>`;
            });
        }
        
        const newRow = `
            <tr class="medicine-row">
                <td>
                    <select name="medicines[]" class="form-control select2 medicine-select" required>
                        ${optionsHtml}
                    </select>
                    <input type="hidden" name="medicine_names[]" class="medicine-name-input">
                </td>
                <td>
                    <input type="text" name="strength[]" class="form-control strength-input" 
                           placeholder="e.g., 500mg">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-medicine">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#medicinesBody').append(newRow);
        
        // Initialize select2 for new row
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2();
        }
        
        // Show remove button for all rows except first
        updateRemoveButtons();
        
        // Attach medicine select change handler
        attachMedicineSelectHandler();
    });
    
    // Remove medicine row
    $(document).on('click', '.remove-medicine', function() {
        $(this).closest('tr').remove();
        updateRemoveButtons();
    });
    
    function updateRemoveButtons() {
        const rows = $('#medicinesBody tr').length;
        if (rows > 1) {
            $('.remove-medicine').show();
        } else {
            $('#medicinesBody tr:first .remove-medicine').hide();
        }
    }
    
    // Medicine select change handler - populate strength and name
    function attachMedicineSelectHandler() {
        $(document).off('change', '.medicine-select').on('change', '.medicine-select', function() {
            const selectedOption = $(this).find('option:selected');
            const strength = selectedOption.data('strength');
            const name = selectedOption.data('name');
            const row = $(this).closest('tr');
            
            // Set strength if available
            if (strength) {
                row.find('.strength-input').val(strength);
            }
            
            // Set medicine name in hidden input
            row.find('.medicine-name-input').val(name);
        });
    }
    
    // Initialize
    attachMedicineSelectHandler();
    updateRemoveButtons();
    
    // Initialize select2 if available
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2();
    }
});
</script>
@endsection
