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
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="{{asset('css/new-design.css')}}?v={{ time() }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/website_header.css') }}">
    <link href="{{asset('css/landing_styles.css')}}?v={{ time() }}" rel="stylesheet">
    <link href="{{asset('styles.css')}}?v={{ time() }}" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Global typography: Inter (body) + Clash Display (headings) -->
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fcfcfc;
        }
        .cert-logo {
            max-height: 50px;
            opacity: 1;
            transition: transform 0.3s;
        }
        .cert-logo:hover {
            transform: scale(1.05);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Clash Display', sans-serif;
            color: #1a1a1a;
        }
        h1 span, h2 span, h3 span, h4 span, h5 span, h6 span {
            font-family: inherit;
        }
        
        .hiw-deck { display: flex; justify-content: center; align-items: flex-end; gap: 0; padding: 40px 0 20px; }
        .step-card-tilted {
            background: #faf8ff;
            border-radius: 20px;
            padding: 28px 24px 20px;
            text-align: left;
            position: relative;
            overflow: hidden;
            width: 250px;
            flex-shrink: 0;
            min-height: 320px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease, z-index 0s;
        }
        .step-card-tilted:hover {
            box-shadow: 0 16px 45px rgba(138, 72, 255, 0.22);
            z-index: 10 !important;
            transform: scale(1.05) translateY(-10px);
        }
        .tilt-left   { transform: rotate(-5deg) translateY(10px); z-index: 1; margin-right: -20px; }
        .tilt-center { transform: rotate(0deg) translateY(-10px); z-index: 2; }
        .tilt-right  { transform: rotate(5deg) translateY(10px);  z-index: 1; margin-left: -20px; }
        
        .step-num-tilted { width: 35px; height: 35px; background: #8a48ff; color: #fff; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.9rem; margin-bottom: 16px; }
        
        .doctor-info-box {
            font-size: 0.8rem;
            color: #666;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    @include('layout.partials.skeleton_loader')
<!-- Navigation -->
@include('layout.partials.navbar_website')

<!-- Hero Section -->
<section class="hero-fuxx position-relative overflow-hidden" style="min-height: 50vh; padding-top: 80px; padding-bottom: 80px; background: linear-gradient(135deg, #f3ecff 0%, #ffffff 100%) !important;">
    <div class="container position-relative" style="z-index: 3;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10">
                <div class="text-uppercase fw-bold mb-3 d-inline-block px-3 py-1" style="color: #8a48ff; background-color: #f4effe; border-radius: 20px; letter-spacing: 1.5px; font-size: 0.85rem;">
                    Wer wir sind
                </div>
                <h1 class="display-3 fw-bold mb-4" style="color: #1a1a1a; letter-spacing: -1px;">
                    Über <span style="color: #8a48ff;">{{ $setting->business_name }}</span>
                </h1>
                <p class="lead mb-5 mx-auto" style="color: #4a4a4a; max-width: 800px; font-size: 1.15rem; line-height: 1.7;">
                    {{ $setting->business_name }} ist eine vertrauenswürdige europäische Telemedizinplattform. Sie verbindet Patienten, zugelassene Ärzte und Partnerapotheken, um Online-Konsultationen, Rezepte und die Lieferung von Medikamenten in ganz Europa anzubieten.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-4 mt-2">
                    <div class="d-flex align-items-center text-dark" style="font-size: 0.95rem;">
                        <i class="bi bi-shield-check fs-5 me-2" style="color: #8a48ff;"></i>
                        <span class="fw-medium text-secondary">DSGVO-konform</span>
                    </div>
                    <div class="d-flex align-items-center text-dark" style="font-size: 0.95rem;">
                        <i class="bi bi-check-circle fs-5 me-2" style="color: #8a48ff;"></i>
                        <span class="fw-medium text-secondary">In Deutschland zugelassen</span>
                    </div>
                    <div class="d-flex align-items-center text-dark" style="font-size: 0.95rem;">
                        <i class="bi bi-lock fs-5 me-2" style="color: #8a48ff;"></i>
                        <span class="fw-medium text-secondary">PCI-konform</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-5" style="background-color: #fff;">
    <div class="container">
        
        <div class="row g-5 my-3 align-items-center">
            <div class="col-lg-6 pe-lg-5">
                <div class="text-uppercase fw-bold mb-3 d-inline-block px-3 py-1" style="color: #2A9D8F; background-color: rgba(42, 157, 143, 0.1); border-radius: 20px; letter-spacing: 1px; font-size: 0.75rem;">
                    Qualität & Vertrauen
                </div>
                <h2 class="fw-bold mb-4 display-6" style="color: #1a1a1a;">Höchste Standards in der Telemedizin</h2>
                <p class="text-muted mb-4 fs-5" style="line-height: 1.6;">{{ $setting->business_name }} erfüllt die europäischen und lokalen Bestimmungen und folgt den allgemein anerkannten Standards in der Telemedizinbranche.</p>
                
                <ul class="list-unstyled mb-5">
                    <li class="d-flex mb-4 align-items-start">
                        <div class="bg-primary-light p-2 rounded-circle me-3" style="background-color: #f3ecff; color: #8a48ff;">
                            <i class="bi bi-check2-circle fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #1a1a1a;">In der EU registrierte Ärzte</h5>
                            <p class="text-muted mb-0 small">Die behandelnden Ärzte auf der Plattform sind in der EU registriert und dürfen Leistungen für deutsche Kunden erbringen.</p>
                        </div>
                    </li>
                    <li class="d-flex mb-4 align-items-start">
                        <div class="bg-primary-light p-2 rounded-circle me-3" style="background-color: #f3ecff; color: #8a48ff;">
                            <i class="bi bi-check2-circle fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #1a1a1a;">Lizenzierte Versandapotheken</h5>
                            <p class="text-muted mb-0 small">Medikamente werden zügig und diskret von den lizenzierten Partner-Versandapotheken ausgegeben.</p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start">
                        <div class="bg-primary-light p-2 rounded-circle me-3" style="background-color: #f3ecff; color: #8a48ff;">
                            <i class="bi bi-check2-circle fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #1a1a1a;">Originalpräparate</h5>
                            <p class="text-muted mb-0 small">Die von den Ärzten verschriebenen Medikamente sind in Deutschland zugelassen. So ist gewährleistet, dass Sie originale und qualitativ hochwertige Medikamente erhalten.</p>
                        </div>
                    </li>
                </ul>
                
                <div class="d-flex flex-wrap align-items-center gap-4 p-4 rounded-4" style="background-color: #f8f9fa; border: 1px solid #eee;">
                    <img src="https://images.dmca.com/Badges/dmca-badge-w150-5x1-06.png?ID=00ac2ea2-b835-4e18-af6f-3fb65f3e0497" alt="DMCA" class="cert-logo" style="filter: none; opacity: 1;">
                    <div class="text-muted small border-start ps-4">
                        <strong class="text-dark">Vertrauenswürdig:</strong><br> Geprüft und überwacht von unabhängigen Organisationen.
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card p-4 border-0 rounded-4" style="background-color: #f4f2ff; box-shadow: 0 20px 40px rgba(138, 72, 255, 0.05);">
                    <h3 class="mb-4 fw-bold h4 px-2">Zertifizierungen & Sicherheit</h3>
                    
                    <div class="bg-white p-4 rounded-4 mb-3 shadow-sm border border-light">
                        <div class="d-flex align-items-start">
                            <div class="p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="background-color: #f3ecff; width: 50px; height: 50px; flex-shrink: 0;">
                                <i class="bi bi-shield-lock-fill" style="color: #8a48ff; font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Datenschutz & Sicherheit</h6>
                                <p class="text-muted small mb-0">{{ $setting->business_name }} wird in Übereinstimmung mit der DS-GVO betrieben und permanent überwacht.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-4 mb-3 shadow-sm border border-light">
                        <div class="d-flex align-items-start">
                            <div class="p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="background-color: #e6f7f1; width: 50px; height: 50px; flex-shrink: 0;">
                                <i class="bi bi-credit-card-2-front text-success" style="font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">PCI-konform</h6>
                                <p class="text-muted small mb-0">Unsere Plattform ist PCI-konform. Es werden keine Kreditkartendaten gespeichert.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-4 shadow-sm border border-light">
                        <div class="d-flex align-items-start">
                            <div class="p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="background-color: #eef3ff; width: 50px; height: 50px; flex-shrink: 0;">
                                <i class="bi bi-building text-primary" style="font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Offizieller Sitz</h6>
                                <p class="text-muted small mb-0">Verwaltet von unserem offiziellen Sitz in London (Sky Marketing Ltd.).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center my-5 py-5" style="background-color: #fafdff; border-radius: 40px; border: 1px solid #f0f7ff;">
            <div class="text-uppercase fw-bold mb-3 d-inline-block px-3 py-1" style="color: #6c757d; background-color: rgba(108, 117, 125, 0.1); border-radius: 20px; letter-spacing: 1px; font-size: 0.75rem;">
                Medizinische Fachkräfte
            </div>
            <h2 class="fw-bold display-6 mb-5" style="color: #1a1a1a;">Behandelnde Ärzte</h2>
            <div class="hiw-deck mx-auto flex-wrap gap-4" style="max-width: 1100px;">
                <!-- Doctor 1 -->
                <div class="step-card-tilted tilt-left">
                    <div class="step-num-tilted"><i class="bi bi-person-badge"></i></div>
                    <h4 class="fw-bold mb-2">Hasan Igde</h4>
                    <div class="doctor-info-box mt-3 pt-3 border-top">
                        <p class="mb-2"><i class="bi bi-geo-alt me-1 text-primary"></i> <strong>Berlin, Deutschland</strong></p>
                        <p class="small text-muted mb-0">Aufsichtsbehörde:<br>Kassenärztliche Vereinigung Berlin</p>
                    </div>
                </div>
                
                <!-- Doctor 2 -->
                <div class="step-card-tilted tilt-center">
                    <div class="step-num-tilted" style="background: #20E2D7; color: #333;"><i class="bi bi-person-badge"></i></div>
                    <h4 class="fw-bold mb-2">Dr. med. Roland Ruiken</h4>
                    <div class="doctor-info-box mt-3 pt-3 border-top">
                        <p class="mb-2"><i class="bi bi-geo-alt me-1 text-success"></i> <strong>Hafslundsoy, Norwegen</strong></p>
                        <p class="small text-muted mb-0">Aufsichtsbehörde:<br>Helsedirektoratet</p>
                    </div>
                </div>
                
                <!-- Doctor 3 -->
                <div class="step-card-tilted tilt-center">
                    <div class="step-num-tilted" style="background: #f6d365;"><i class="bi bi-person-badge"></i></div>
                    <h4 class="fw-bold mb-2">Dr. med. Viktor Simunovic</h4>
                    <div class="doctor-info-box mt-3 pt-3 border-top">
                        <p class="mb-2"><i class="bi bi-geo-alt me-1 text-warning"></i> <strong>Zagreb, Kroatien</strong></p>
                        <p class="small text-muted mb-0">Aufsichtsbehörde:<br>Hrvatska Liječnička Komora</p>
                    </div>
                </div>
                
                <!-- Doctor 4 -->
                <div class="step-card-tilted tilt-right">
                    <div class="step-num-tilted" style="background: #ff0844;"><i class="bi bi-person-badge"></i></div>
                    <h4 class="fw-bold mb-2">Dr. med. Salomé Apitz</h4>
                    <div class="doctor-info-box mt-3 pt-3 border-top">
                        <p class="mb-2"><i class="bi bi-geo-alt me-1 text-danger"></i> <strong>Porto, Portugal</strong></p>
                        <p class="small text-muted mb-0">Aufsichtsbehörde:<br>Entidade Reguladora da Saúde</p>
                    </div>
                </div>
            </div>

            <div class="col-12 text-center mt-5">
                <div class="bg-white p-4 rounded-pill d-inline-block shadow-sm border border-light">
                    <p class="text-muted small mb-0 px-4">
                        Haben Sie Interesse, medizinische Dienstleistungen auf der {{ $setting->business_name }}-Plattform online anzubieten? <a href="#" class="text-primary fw-bold text-decoration-none">Kontaktieren Sie uns</a>.
                    </p>
                </div>
            </div>
        </div>
        </div>

        <!-- Pharmacies Section -->
        <div class="p-5 rounded-4 border-0 mb-5" style="background-color: #fff; box-shadow: 0 30px 60px rgba(0,0,0,0.05);">
            <div class="text-center mb-5">
                <div class="text-uppercase fw-bold mb-3 d-inline-block px-3 py-1" style="color: #28a745; background-color: rgba(40, 167, 69, 0.1); border-radius: 20px; letter-spacing: 1px; font-size: 0.75rem;">
                    Sichere Lieferung
                </div>
                <h2 class="fw-bold display-6 mb-3" style="color: #1a1a1a;">Unsere Partnerapotheken</h2>
                <div class="mx-auto text-muted lead" style="max-width: 700px; font-size: 1.1rem;">
                    Sollten Sie die Option einer Medikamentenlieferung beim Bestellvorgang auswählen, werden wir Ihr Rezept automatisch an eine von uns empfohlene Versandapotheke weiterleiten.
                </div>
            </div>
            
            <div class="row justify-content-center g-4">
                <div class="col-lg-6">
                    <div class="card p-4 h-100 border-0 rounded-4" style="background-color: #f8f9ff; transition: transform 0.3s ease;">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-success text-white p-3 rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-shop fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-0" style="color: #1a1a1a;">Die Herz Apotheke-Spandau</h5>
                        </div>
                        <div class="ps-2">
                            <div class="d-flex mb-3">
                                <i class="bi bi-geo-alt text-muted me-3 mt-1"></i>
                                <span class="small text-secondary">Seeburger Str. 8-11, 13581 Berlin</span>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="bi bi-person-check text-muted me-3 mt-1"></i>
                                <span class="small text-secondary"><strong>Verantwortlicher Apotheker:</strong> Mohammad Mohammad</span>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="bi bi-envelope text-muted me-3 mt-1"></i>
                                <a href="mailto:info@herz-apotheke-spandau.de" class="small text-primary text-decoration-none">info@herz-apotheke-spandau.de</a>
                            </div>
                            <div class="d-flex">
                                <i class="bi bi-telephone text-muted me-3 mt-1"></i>
                                <span class="small text-secondary">(030) 33309393</span>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top small text-muted italic">
                            <i class="bi bi-info-circle me-1"></i> Aufsichtsbehörde: Landesamt für Gesundheit und Soziales, Berlin
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card p-4 h-100 border-0 rounded-4" style="background-color: #f8f9ff; transition: transform 0.3s ease;">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary text-white p-3 rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-shop fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-0" style="color: #1a1a1a;">Shop Apotheke B.V.</h5>
                        </div>
                        <div class="ps-2">
                            <div class="d-flex mb-3">
                                <i class="bi bi-geo-alt text-muted me-3 mt-1"></i>
                                <span class="small text-secondary">Erik de Rodeweg 11-13 NL-5975 WD Sevenum</span>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="bi bi-person-check text-muted me-3 mt-1"></i>
                                <span class="small text-secondary"><strong>Verantwortlicher Apotheker:</strong> T.M. Holler</span>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="bi bi-envelope text-muted me-3 mt-1"></i>
                                <a href="mailto:kontakt@shop-apotheke.com" class="small text-primary text-decoration-none">kontakt@shop-apotheke.com</a>
                            </div>
                            <div class="d-flex">
                                <i class="bi bi-telephone text-muted me-3 mt-1"></i>
                                <span class="small text-secondary">0800 - 200 800 300</span>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top small text-muted italic">
                            <i class="bi bi-info-circle me-1"></i> Regulierungsbehörde: Inspectie Gezondheidszorg en Jeugd
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Footer -->
@include('layout.partials.footer')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
