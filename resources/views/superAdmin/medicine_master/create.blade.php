@extends('layout.mainlayout_admin',['activePage' => 'medicine_master'])

@section('title',__('Add Medicine'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Add Medicine'),
        'url' => url('medicine_master'),
        'urlTitle' => __('Medicine Master')
    ])
    <div class="section-body">
        <div class="card">
            <form action="{{ url('medicine_master') }}" method="post" class="myform">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-10 col-md-6">
                            <div class="form-group">
                                <label class="col-form-label">{{__('Medicine Name')}} <span class="text-danger">*</span></label>
                                <input type="text" required value="{{ old('name') }}" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{__('Enter medicine name')}}">
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{__('Strength')}}</label>
                                <input type="text" value="{{ old('strength') }}" name="strength" class="form-control @error('strength') is-invalid @enderror" placeholder="{{__('e.g., 500mg, 10mg/ml')}}">
                                @error('strength')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{__('Form')}}</label>
                                <input type="text" value="{{ old('form') }}" name="form" class="form-control @error('form') is-invalid @enderror" placeholder="{{__('e.g., Tablet, Capsule, Syrup, Injection')}}">
                                @error('form')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="">{{__('Status')}}</label>
                                <label class="cursor-pointer">
                                    <input type="checkbox" id="status" class="custom-switch-input" name="status" checked>
                                    <span class="custom-switch-indicator"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection
