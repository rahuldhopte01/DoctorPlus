<!-- Global typography: Inter (body) + Clash Display (headings) -->
<link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link href="{{ url('assets/css/bootstrap.min.css') }}" rel="stylesheet">

<link rel="stylesheet" href="{{ url('assets/css/boxicons.min.css') }}">

<link rel="stylesheet" href="{{ url('assets/css/slick-theme.min.css') }}" />

<link rel="stylesheet" href="{{ url('assets/css/slick.css') }}" />

<link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">

<link rel="stylesheet" type="text/css" href="{{ asset('assets_admin/css/datatables.min.css') }}" />

<link rel="stylesheet" href="{{url('assets/plugins/fancybox/jquery.fancybox.min.css')}}">

<script type="text/javascript" src="{{ url('assets_admin/js/sweetalert2@10.js') }}"></script>

<link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ url('css/website_header.css') }}">


@yield('css')

<input type="hidden" name="rtl_direction" class="rtl_direction" value="{{ session('direction') == 'rtl' ? 'true' : 'false' }}">

<style>
    :root{
        --site_color : <?php echo $setting->website_color; ?>;
        --site_color_hover : <?php echo $setting->website_color.'e8'; ?>;
        --font-body: 'Inter', sans-serif;
        --font-heading: 'Clash Display', sans-serif;
    }
    body { font-family: var(--font-body); }
    h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 { font-family: var(--font-heading); }
</style>

@if (session('direction') == 'rtl')
    <link rel="stylesheet" href="{{ url('assets/css/rtl_direction.css')}}" type="text/css">
@endif
