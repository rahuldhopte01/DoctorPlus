@extends('layout.mainlayout_admin',['activePage' => 'cannaleo_medicine'])

@section('title', __('Edit Cannaleo Medicine'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Edit Cannaleo Medicine'),
        'url' => url('cannaleo/medicines'),
        'urlTitle' => __('Cannaleo Medicines'),
    ])

    <div class="section_body">
        {{-- Read-only info card --}}
        <div class="card mb-3">
            <div class="card-header font-weight-bold">{{ __('Synced Medicine Info') }} <small class="text-muted">({{ __('managed by API sync — read only') }})</small></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1"><strong>{{ __('Name') }}:</strong> {{ $medicine->name }}</p>
                        <p class="mb-1">
                            <strong>{{ __('Pharmacies') }}:</strong>
                            @if ($siblingPharmacies->isNotEmpty())
                                @foreach ($siblingPharmacies as $ph)
                                    <span class="badge badge-info mr-1">{{ $ph->name }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </p>
                        <p class="mb-1"><strong>{{ __('API Category') }}:</strong> {{ $medicine->category ?? '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>{{ __('Price') }}:</strong> {{ $medicine->price !== null ? number_format($medicine->price, 2).' €' : '—' }}</p>
                        <p class="mb-1"><strong>{{ __('THC') }}:</strong> {{ $medicine->thc !== null ? number_format($medicine->thc, 1).'%' : '—' }}</p>
                        <p class="mb-1"><strong>{{ __('CBD') }}:</strong> {{ $medicine->cbd !== null ? number_format($medicine->cbd, 1).'%' : '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>{{ __('Genetic') }}:</strong> {{ $medicine->genetic ?? '—' }}</p>
                        <p class="mb-1"><strong>{{ __('Strain') }}:</strong> {{ $medicine->strain ?? '—' }}</p>
                        <p class="mb-1"><strong>{{ __('Last Synced') }}:</strong> {{ $medicine->last_synced_at ? $medicine->last_synced_at->format('d.m.Y H:i') : '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Editable fields --}}
        <div class="card">
            <div class="card-header font-weight-bold">
                {{ __('Image & Description') }}
                <small class="text-muted">({{ __('these fields are preserved across syncs') }})</small>
                @if ($siblingPharmacies->count() > 1)
                    <div class="alert alert-info mt-2 mb-0 py-2 px-3 small">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Saving will apply these changes to all') }} <strong>{{ $siblingPharmacies->count() }}</strong> {{ __('pharmacies carrying this medicine.') }}
                    </div>
                @endif
            </div>
            <form action="{{ route('cannaleo.medicines.update', $medicine->id) }}" method="post" class="myform" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="card-body">

                    {{-- Image --}}
                    <div class="form-group">
                        <label class="col-form-label font-weight-bold">{{ __('Image') }}</label>
                        @if ($medicine->image)
                            <div class="mb-2">
                                <img src="{{ asset('images/upload/'.$medicine->image) }}" alt="{{ $medicine->name }}" class="img-thumbnail" style="max-height:140px;">
                            </div>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="remove_image" name="remove_image" value="1">
                                <label class="custom-control-label text-danger" for="remove_image">{{ __('Remove current image') }}</label>
                            </div>
                        @endif
                        <input type="file" name="image" id="image_upload" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                        <small class="form-text text-muted">{{ __('Upload a new image. Leave empty to keep the current one.') }}</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="form-group">
                        <label class="col-form-label font-weight-bold">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control summernote @error('description') is-invalid @enderror" rows="6">{{ old('description', $medicine->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-right mt-4">
                        <a href="{{ route('cannaleo.medicines.index') }}" class="btn btn-secondary mr-2">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
