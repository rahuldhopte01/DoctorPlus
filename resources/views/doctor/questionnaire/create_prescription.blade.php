@extends('layout.mainlayout_admin',['activePage' => 'questionnaire_submissions'])

@section('title', __('Create Prescription'))

@section('content')
<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Create Prescription'),
    ])

    <div class="section-body">
        <!-- Patient Info -->
        <div class="card mb-3">
            <div class="card-header">
                <h4><i class="fas fa-user mr-2"></i>{{ __('Patient Information') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('Name:') }}</strong> {{ $user->name }}</p>
                        <p><strong>{{ __('Email:') }}</strong> {{ $user->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{ __('Phone:') }}</strong> {{ $user->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescription Form -->
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-prescription-bottle-alt mr-2"></i>{{ __('Prescription Details') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('doctor.questionnaire.store-prescription', [
                    'userId' => $userId,
                    'categoryId' => $categoryId,
                    'questionnaireId' => $questionnaireId
                ]) }}" method="POST" id="prescriptionForm">
                    @csrf
                    
                    <!-- Prescription Validity Period -->
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('Prescription Validity (Days)') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="number" name="validity_days" class="form-control @error('validity_days') is-invalid @enderror" 
                                   value="{{ old('validity_days', 30) }}" min="1" max="365" required>
                            <small class="form-text text-muted">{{ __('Prescription will be valid from approval date for the specified number of days.') }}</small>
                            @error('validity_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Medicines -->
                    <div class="form-group">
                        <label class="col-form-label mb-3">{{ __('Medicines') }} <span class="text-danger">*</span></label>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="medicinesTable">
                                <thead>
                                    <tr>
                                        <th width="45%">{{ __('Medicine Name') }}</th>
                                        <th width="50%">{{ __('Strength') }}</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="medicinesBody">
                                    <tr class="medicine-row">
                                        <td>
                                            <select name="medicines[]" class="form-control select2 medicine-select" required>
                                                <option value="">{{ __('Select Medicine') }}</option>
                                                @foreach ($medicines as $medicine)
                                                    <option value="{{ $medicine->id }}" 
                                                            data-name="{{ $medicine->name }}"
                                                            data-strength="{{ $medicine->strength ?? '' }}">
                                                        {{ $medicine->name }}{{ $medicine->strength ? ' (' . $medicine->strength . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="medicine_names[]" class="medicine-name-input">
                                        </td>
                                        <td>
                                            <input type="text" name="strength[]" class="form-control strength-input" 
                                                   placeholder="e.g., 500mg" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-medicine" style="display:none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" id="addMedicine">
                                <i class="fas fa-plus mr-2"></i>{{ __('Add Medicine') }}
                            </button>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <a href="{{ route('doctor.questionnaire.show', [
                            'userId' => $userId,
                            'categoryId' => $categoryId,
                            'questionnaireId' => $questionnaireId
                        ]) }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-arrow-left mr-2"></i>{{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-2"></i>{{ __('Create Prescription') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
$(document).ready(function() {
    let rowIndex = 0;
    
    // Add medicine row
    $('#addMedicine').click(function() {
        rowIndex++;
        const newRow = `
            <tr class="medicine-row">
                <td>
                    <select name="medicines[]" class="form-control select2 medicine-select" required>
                        <option value="">{{ __('Select Medicine') }}</option>
                        @foreach ($medicines as $medicine)
                            <option value="{{ $medicine->id }}" 
                                    data-name="{{ $medicine->name }}"
                                    data-strength="{{ $medicine->strength ?? '' }}">
                                {{ $medicine->name }}{{ $medicine->strength ? ' (' . $medicine->strength . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="medicine_names[]" class="medicine-name-input">
                </td>
                <td>
                    <input type="text" name="strength[]" class="form-control strength-input" 
                           placeholder="e.g., 500mg" required>
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
        $('.select2').select2();
        
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
            $('.remove-medicine').hide();
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
    
    // Initialize select2
    $('.select2').select2();
});
</script>
@endsection
