@extends('layout.mainlayout_admin',['activePage' => 'medicine'])

@section('title',__('Add Medicine'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Add Medicine'),
        'url' => url('medicine'),
        'urlTitle' => __('Medicine')
    ])
    <div class="section_body">
        <form action="{{ url('medicine') }}" method="post" class="myform">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Name')}}</label>
                        <input type="text" value="{{ old('name') }}" name="name" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{__('Strength')}}</label>
                        <input type="text" value="{{ old('strength') }}" name="strength" class="form-control @error('strength') is-invalid @enderror" placeholder="e.g., 500mg, 10mg">
                        @error('strength')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{__('Form')}}</label>
                        <select name="form" class="form-control @error('form') is-invalid @enderror">
                            <option value="">{{__('Select Form')}}</option>
                            <option value="tablet" {{ old('form') == 'tablet' ? 'selected' : '' }}>{{__('Tablet')}}</option>
                            <option value="syrup" {{ old('form') == 'syrup' ? 'selected' : '' }}>{{__('Syrup')}}</option>
                            <option value="injection" {{ old('form') == 'injection' ? 'selected' : '' }}>{{__('Injection')}}</option>
                            <option value="capsule" {{ old('form') == 'capsule' ? 'selected' : '' }}>{{__('Capsule')}}</option>
                            <option value="drops" {{ old('form') == 'drops' ? 'selected' : '' }}>{{__('Drops')}}</option>
                            <option value="ointment" {{ old('form') == 'ointment' ? 'selected' : '' }}>{{__('Ointment')}}</option>
                            <option value="other" {{ old('form') == 'other' ? 'selected' : '' }}>{{__('Other')}}</option>
                        </select>
                        @error('form')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{__('Brand')}}</label>
                        <select name="brand_id" class="form-control @error('brand_id') is-invalid @enderror">
                            <option value="">{{__('Select Brand')}}</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{__('Categories')}}</label>
                        <select name="category_ids[]" class="form-control select2 @error('category_ids') is-invalid @enderror" multiple data-placeholder="{{ __('Select categories for questionnaire medicine selection') }}">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ in_array($cat->id, old('category_ids', [])) ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{__('Assign this medicine to categories. Patients see only these medicines when filling that category\'s questionnaire.')}}</small>
                        @error('category_ids')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{__('Description')}}</label>
                        <textarea name="description" class="form-control summernote @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">{{__('Status')}}</label>
                        <label class="cursor-pointer">
                            <input type="checkbox" id="status" class="custom-switch-input" name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                        <small class="form-text text-muted">{{__('Active medicines appear in pharmacy dropdowns')}}</small>
                    </div>
                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
