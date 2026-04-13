@extends('layout.mainlayout_admin',['activePage' => 'language'])

@section('title',__('Edit Translations'))

@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Edit Translations').' ('.$language->name.')',
        'url' => url('language'),
        'urlTitle' => __('Language')
    ])
    @if (session('status'))
    @include('superAdmin.auth.status',[
        'status' => session('status')])
    @endif

    <div class="section_body">
        <div class="card">
            <div class="card-body">
                <form action="{{ url('language/'.$language->id.'/translation') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>{{__('Key')}}</th>
                                    <th>{{__('Value')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($translations as $key => $value)
                                    <tr>
                                        <td style="max-width: 300px; word-wrap: break-word; white-space: normal;">{{ $key }}</td>
                                        <td>
                                            <input type="hidden" name="keys[]" value="{{ $key }}">
                                            <input type="text" name="values[]" class="form-control" value="{{ $value }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <button type="submit" class="btn btn-primary">{{__('Save Translations')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
