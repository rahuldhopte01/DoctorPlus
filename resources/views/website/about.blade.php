<!DOCTYPE html>
<html lang="en">
<head>
    @php
    $setting = App\Models\Setting::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - {{ $setting->business_name }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link href="{{asset('css/new-design.css')}}" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        body {
            font-family: 'Inter', 'Fira Sans', sans-serif;
            background-color: #fcfcfc;
        }
        .hero-section {
            background-color: #f8f9fa;
            padding: 80px 0;
            margin-bottom: 40px;
        }
        .bg-primary-green-gradient {
            background: linear-gradient(90deg, #2A9D8F, #457B9D);
        }
        .bg-clip-text {
            -webkit-background-clip: text;
        }
        .info-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            background-color: #fff;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        }
        .icon-box {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        /* Refined Colors */
        .icon-box.bg-primary {
            background: linear-gradient(135deg, #4481eb 0%, #04befe 100%) !important;
        }
        .icon-box.bg-success {
            background: linear-gradient(135deg, #20E2D7 0%, #F9FEA5 100%) !important;
            color: #333 !important;
        }
        .icon-box.bg-warning {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%) !important;
            color: #fff !important;
        }
        .icon-box.bg-danger {
            background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%) !important;
        }
        .cert-logo {
            max-height: 50px;
            opacity: 0.8;
            transition: opacity 0.3s, transform 0.3s;
            filter: grayscale(100%);
        }
        .cert-logo:hover {
            opacity: 1;
            filter: grayscale(0%);
            transform: scale(1.05);
        }
        
        .doctor-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        
        h1, h2, h3, h4, h5 {
            font-family: 'Fira Sans', sans-serif;
            color: #2b2b2b;
        }
        
        .badge.shadow-sm {
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    @include('layout.partials.skeleton_loader')
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            @if($setting->company_logo && file_exists(public_path('images/upload/'.$setting->company_logo)))
                <img src="{{ $setting->logo }}" alt="{{ $setting->business_name }}" style="max-height: 40px">
            @else
                <img src="{{ url('/images/upload_empty/fuxxlogo.png') }}" alt="{{ $setting->business_name }}" style="max-height: 40px">
            @endif
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories') }}">Treatments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}#how-it-works">How it works</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active fw-bold text-primary" href="{{ url('/about-us') }}">About us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}#faq">Help</a>
                </li>
            </ul>
            
            <div class="d-flex gap-2">
                @if(auth()->check())
                    <a href="{{ url('user_profile') }}" class="btn btn-link text-dark text-decoration-none">
                        <i class="bi bi-person"></i> {{ auth()->user()->name }}
                    </a>
                @else
                    <a href="{{ url('patient-login') }}" class="btn btn-link text-dark text-decoration-none">
                        <i class="bi bi-person"></i> Sign in
                    </a>
                @endif
                <a href="{{ route('categories') }}" class="btn btn-primary rounded-pill px-4">Start treatment</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section text-center position-relative overflow-hidden" style="background-image: url('https://images.unsplash.com/photo-1516549655169-df83a0774514?q=80&w=1920&auto=format&fit=crop'); background-size: cover; background-position: center 30%; min-height: 500px; display: flex; align-items: center; justify-content: center; margin-bottom: 40px; padding: 0;">
    <!-- Dark Overlay to make text legible -->
    <div class="position-absolute w-100 h-100" style="top: 0; left: 0; background-color: rgba(0, 0, 0, 0.65); z-index: 1;"></div>
    
    <div class="container position-relative z-index-2 py-5" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <span class="badge rounded-pill px-3 py-2 mb-3 bg-white" style="color: #4A3AFF; background-color: rgba(255, 255, 255, 0.9);">Wer wir sind</span>
                <h1 class="display-3 fw-bold mb-4 text-white">Über <span style="color: #00D9C0;">{{ $setting->business_name }}</span></h1>
                <p class="lead mb-5 px-md-5 text-white" style="line-height: 1.8; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">
                    {{ $setting->business_name }} ist eine vertrauenswürdige europäische Telemedizinplattform. Sie verbindet Patienten, zugelassene Ärzte und Partnerapotheken, um Online-Konsultationen, Rezepte und die Lieferung von Medikamenten in ganz Europa anzubieten.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-2">
                    <span class="badge text-dark border p-2 px-3 rounded-pill shadow-sm" style="background-color: rgba(255, 255, 255, 0.95);"><i class="bi bi-shield-check text-success fs-5 me-2 align-middle"></i>DSGVO-konform</span>
                    <span class="badge text-dark border p-2 px-3 rounded-pill shadow-sm" style="background-color: rgba(255, 255, 255, 0.95);"><i class="bi bi-check-circle text-primary fs-5 me-2 align-middle"></i>In Deutschland zugelassen</span>
                    <span class="badge text-dark border p-2 px-3 rounded-pill shadow-sm" style="background-color: rgba(255, 255, 255, 0.95);"><i class="bi bi-lock text-warning fs-5 me-2 align-middle"></i>PCI-konform</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        
        <div class="row g-5 my-3 align-items-center">
            <div class="col-lg-6 pe-lg-5">
                <h2 class="fw-bold mb-4 fs-1">Höchste Standards in der Telemedizin</h2>
                <div class="mb-4" style="width: 60px; height: 4px; background: #0d6efd; border-radius: 2px;"></div>
                <p class="text-muted mb-4 fs-5">{{ $setting->business_name }} erfüllt die europäischen und lokalen Bestimmungen und folgt den allgemein anerkannten Standards in der Telemedizinbranche.</p>
                
                <ul class="list-unstyled mb-5">
                    <li class="d-flex mb-4">
                        <i class="bi bi-check2-circle text-primary fs-3 me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-1">In der EU registrierte Ärzte</h5>
                            <p class="text-muted mb-0">Die behandelnden Ärzte auf der Plattform sind in der EU registriert und dürfen Leistungen für deutsche Kunden erbringen.</p>
                        </div>
                    </li>
                    <li class="d-flex mb-4">
                        <i class="bi bi-check2-circle text-primary fs-3 me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Lizenzierte Versandapotheken</h5>
                            <p class="text-muted mb-0">Medikamente werden zügig und diskret von den lizenzierten Partner-Versandapotheken ausgegeben.</p>
                        </div>
                    </li>
                    <li class="d-flex">
                        <i class="bi bi-check2-circle text-primary fs-3 me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Originalpräparate</h5>
                            <p class="text-muted mb-0">Die von den Ärzten verschriebenen Medikamente sind in Deutschland zugelassen. So ist gewährleistet, dass Sie originale und qualitativ hochwertige Medikamente erhalten.</p>
                        </div>
                    </li>
                </ul>
                
                <div class="d-flex flex-wrap align-items-center gap-4 p-4 bg-light rounded-4">
                    <img src="https://images.dmca.com/Badges/dmca-badge-w150-5x1-06.png?ID=00ac2ea2-b835-4e18-af6f-3fb65f3e0497" alt="DMCA" class="cert-logo">
                    <div class="text-muted small border-start ps-4">
                        <strong class="text-dark">Vertrauenswürdig:</strong><br> Geprüft und überwacht von unabhängigen Organisationen.
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card info-card p-5 border-0" style="background-color: rgba(74, 58, 255, 0.05);">
                    <h3 class="mb-4 fw-bold">Zertifizierungen & Sicherheit</h3>
                    
                    <div class="bg-white p-4 rounded-4 mb-3 shadow-sm">
                        <div class="d-flex align-items-start">
                            <div class="p-4 rounded-circle me-4 d-flex align-items-center justify-content-center" style="background-color: rgba(74, 58, 255, 0.1); width: 64px; height: 64px;">
                                <i class="bi bi-shield-lock-fill text-primary fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold fs-5 mb-2">Datenschutz & Sicherheit</h6>
                                <p class="text-muted small mb-0">{{ $setting->business_name }} wird in Übereinstimmung mit der DS-GVO betrieben und permanent überwacht, um die höchsten Datenschutz- und Nutzerschutzstandards zu gewährleisten.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-4 mb-3 shadow-sm">
                        <div class="d-flex align-items-start">
                            <div class="p-4 rounded-circle me-4 d-flex align-items-center justify-content-center" style="background-color: rgba(40, 167, 69, 0.1); width: 64px; height: 64px;">
                                <i class="bi bi-credit-card-2-front text-success fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold fs-5 mb-2">PCI-konform</h6>
                                <p class="text-muted small mb-0">Unsere Plattform ist PCI-konform (Data Security-Standards für die Zahlungskartenbranche). Es werden keine Kreditkartendaten gespeichert.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-4 shadow-sm">
                        <div class="d-flex align-items-start">
                            <div class="p-4 rounded-circle me-4 d-flex align-items-center justify-content-center" style="background-color: rgba(23, 162, 184, 0.1); width: 64px; height: 64px;">
                                <i class="bi bi-building text-info fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold fs-5 mb-2">Offizieller Sitz</h6>
                                <p class="text-muted small mb-0">Die Plattform gehört zur Sky Marketing Ltd. und wird von unserem offiziellen Sitz in London verwaltet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center my-6 py-5">
            <span class="badge rounded-pill px-3 py-2 mb-3" style="background-color: rgba(108, 117, 125, 0.1); color: #6c757d;">Medizinische Fachkräfte</span>
            <h2 class="fw-bold display-6 mb-5">Behandelnde Ärzte</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-xl-3">
                    <div class="card info-card p-4 text-center">
                        <div class="icon-box mx-auto bg-primary text-white">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h5 class="fw-bold fs-5">Hasan Igde</h5>
                        <hr class="w-25 mx-auto my-3 text-muted">
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i> Geschäfts-/Praxissitz:<br><strong>Berlin, Deutschland</strong></p>
                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">Aufsichtsbehörde:<br>Kassenärztliche Vereinigung Berlin</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-3">
                    <div class="card info-card p-4 text-center">
                        <div class="icon-box mx-auto bg-success text-dark">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h5 class="fw-bold fs-5">Dr. med. Roland Ruiken</h5>
                        <hr class="w-25 mx-auto my-3 text-muted">
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i> Geschäfts-/Praxissitz:<br><strong>Hafslundsoy, Norwegen</strong></p>
                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">Aufsichtsbehörde:<br>Helsedirektoratet</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-3">
                    <div class="card info-card p-4 text-center">
                        <div class="icon-box mx-auto bg-warning text-white">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h5 class="fw-bold fs-5">Dr. med. Viktor Simunovic</h5>
                        <hr class="w-25 mx-auto my-3 text-muted">
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i> Geschäfts-/Praxissitz:<br><strong>Zagreb, Kroatien</strong></p>
                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">Aufsichtsbehörde:<br>Hrvatska Liječnička Komora</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-3">
                    <div class="card info-card p-4 text-center">
                        <div class="icon-box mx-auto bg-danger text-white">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h5 class="fw-bold fs-5">Dr. med. Salomé Apitz</h5>
                        <hr class="w-25 mx-auto my-3 text-muted">
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i> Geschäfts-/Praxissitz:<br><strong>Porto, Portugal</strong></p>
                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">Aufsichtsbehörde:<br>Entidade Reguladora da Saúde</p>
                    </div>
                </div>
                
                <div class="col-12 text-center mt-5">
                    <div class="bg-light p-4 rounded-pill d-inline-block">
                        <p class="text-muted small mb-0">
                            Haben Sie Interesse, medizinische Dienstleistungen auf der {{ $setting->business_name }}-Plattform online anzubieten? Kontaktieren Sie uns für mehr Informationen.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pharmacies Section -->
        <div class="bg-white p-5 rounded-4 shadow-sm border mb-5">
            <div class="text-center mb-5">
                <span class="badge rounded-pill px-3 py-2 mb-3" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745;">Sichere Lieferung</span>
                <h2 class="fw-bold display-6 mb-3">Unsere Partnerapotheken</h2>
                <div class="mx-auto text-muted lead" style="max-width: 700px;">
                    Sollten Sie die Option einer Medikamentenlieferung beim Bestellvorgang auswählen, werden wir Ihr Rezept automatisch an eine von uns empfohlene Versandapotheke weiterleiten.
                </div>
            </div>
            
            <div class="row justify-content-center g-4">
                <div class="col-lg-5">
                    <div class="card info-card p-4 border h-100 shadow-none hover-shadow">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-success text-white p-3 rounded-3 me-3">
                                <i class="bi bi-shop fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Die Herz Apotheke-Spandau</h5>
                        </div>
                        <table class="table table-borderless table-sm small mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 30px"><i class="bi bi-geo-alt"></i></td>
                                    <td>Seeburger Str. 8-11, 13581 Berlin</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="bi bi-person-check"></i></td>
                                    <td>Verantwortlicher Apotheker:<br>Mohammad Mohammad</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="bi bi-envelope"></i></td>
                                    <td><a href="mailto:info@herz-apotheke-spandau.de" class="text-decoration-none text-primary">info@herz-apotheke-spandau.de</a></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="bi bi-telephone"></i></td>
                                    <td>(030) 33309393</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-4 pt-3 border-top small text-muted">
                            <i class="bi bi-info-circle me-1"></i> <strong>Aufsichtsbehörde:</strong> Landesamt für Gesundheit und Soziales, Turmstraße 21, 10559 Berlin
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="card info-card p-4 border h-100 shadow-none hover-shadow">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary text-white p-3 rounded-3 me-3">
                                <i class="bi bi-shop fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Shop Apotheke B.V.</h5>
                        </div>
                        <table class="table table-borderless table-sm small mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 30px"><i class="bi bi-geo-alt"></i></td>
                                    <td>Erik de Rodeweg 11-13 NL-5975 WD Sevenum</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="bi bi-person-check"></i></td>
                                    <td>Verantwortlicher Apotheker:<br>T.M. Holler</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="bi bi-envelope"></i></td>
                                    <td><a href="mailto:kontakt@shop-apotheke.com" class="text-decoration-none text-primary">kontakt@shop-apotheke.com</a></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="bi bi-telephone"></i></td>
                                    <td>0800 - 200 800 300</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-4 pt-3 border-top small text-muted">
                            <i class="bi bi-info-circle me-1"></i> <strong>Regulierungsbehörde:</strong> Inspectie Gezondheidszorg en Jeugd
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Footer -->
<footer class="footer-dark text-light py-5 bg-dark" style="color: rgba(255,255,255,0.7) !important;">
    <div class="container pb-3">
        <div class="row g-4 mb-5">
            <!-- Company Info -->
            <div class="col-md-6 col-lg-4 pe-lg-5">
                @if($setting->company_logo && file_exists(public_path('images/upload/'.$setting->company_logo)))
                    <img src="{{ $setting->logo }}" alt="{{ $setting->business_name }}" class="footer-logo mb-4" height="45">
                @else
                    <img src="{{ url('/images/upload_empty/fuxxlogo.png') }}" alt="{{ $setting->business_name }}" class="footer-logo mb-4" height="45">
                @endif
                <p class="small mb-4 lh-lg">Ihre vertrauenswürdige europäische Telemedizinplattform für diskrete und sichere medizinische Beratung, Diagnose und Behandlung.</p>
                <div class="d-flex gap-3">
                    @if($setting->facebook_link)
                        <a href="{{ $setting->facebook_link }}" class="text-light bg-secondary bg-opacity-25 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" target="_blank"><i class="bi bi-facebook"></i></a>
                    @endif
                    @if($setting->twitter_link)
                        <a href="{{ $setting->twitter_link }}" class="text-light bg-secondary bg-opacity-25 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" target="_blank"><i class="bi bi-twitter"></i></a>
                    @endif
                    @if($setting->instagram_link)
                        <a href="{{ $setting->instagram_link }}" class="text-light bg-secondary bg-opacity-25 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" target="_blank"><i class="bi bi-instagram"></i></a>
                    @endif
                    @if($setting->linkedin_link)
                        <a href="{{ $setting->linkedin_link }}" class="text-light bg-secondary bg-opacity-25 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" target="_blank"><i class="bi bi-linkedin"></i></a>
                    @endif
                </div>
            </div>

            <!-- Treatments -->
            <div class="col-md-6 col-lg-2 offset-lg-1">
                <h5 class="text-white mb-4 fw-bold">Behandlungen</h5>
                <ul class="list-unstyled small lh-lg">
                    <li class="mb-2"><a href="{{ route('categories') }}" class="text-decoration-none" style="color: rgba(255,255,255,0.7);">Alle Behandlungen</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div class="col-md-6 col-lg-2">
                <h5 class="text-white mb-4 fw-bold">Unternehmen</h5>
                <ul class="list-unstyled small lh-lg">
                    <li class="mb-2"><a href="{{ url('/about-us') }}" class="text-decoration-none" style="color: rgba(255,255,255,0.7);">Über uns</a></li>
                    <li class="mb-2"><a href="{{ url('/') }}#how-it-works" class="text-decoration-none" style="color: rgba(255,255,255,0.7);">So funktioniert es</a></li>
                    <li class="mb-2"><a href="{{ url('show-doctors') }}" class="text-decoration-none" style="color: rgba(255,255,255,0.7);">Unsere Ärzte</a></li>
                    <li class="mb-2"><a href="{{ url('our_blogs') }}" class="text-decoration-none" style="color: rgba(255,255,255,0.7);">Blog</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div class="col-md-6 col-lg-3">
                <h5 class="text-white mb-4 fw-bold">Support</h5>
                <ul class="list-unstyled small lh-lg">
                    <li class="mb-2"><a href="#" class="text-decoration-none" style="color: rgba(255,255,255,0.7);">Hilfe Center</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none" style="color: rgba(255,255,255,0.7);">Kontakt</a></li>
                    <li class="mb-2"><a href="{{ url('/') }}#faq" class="text-decoration-none" style="color: rgba(255,255,255,0.7);">FAQ</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none" style="color: rgba(255,255,255,0.7);">Versand & Lieferung</a></li>
                </ul>
            </div>
        </div>

        <div class="border-top border-secondary border-opacity-50 pt-4 mt-2">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="small mb-0">© {{ date('Y') }} {{ $setting->business_name }} Alle Rechte vorbehalten.</p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-4 small">
                        <a href="{{ url('/privacy-policy') }}" class="text-decoration-none" style="color: rgba(255,255,255,0.7);">Datenschutz</a>
                        <a href="#" class="text-decoration-none" style="color: rgba(255,255,255,0.7);">AGB</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
