@extends('layout.mainlayout_admin',['activePage' => 'medicineBrand'])

@section('title',__('Edit Medicine Brand'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Edit Medicine Brand'),
        'url' => url('medicineBrand'),
        'urlTitle' => __('Medicine Brand')
    ])
    <div class="section_body">
        <div class="card">
            <form action="{{ url('medicineBrand/'.$medicineBrand->id) }}" method="post" class="myform">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Brand Name')}}</label>
                        <input type="text" required value="{{ old('name', $medicineBrand->name) }}" name="name" class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection
