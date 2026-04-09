<!DOCTYPE html>
<html lang="de">
<head>
    @php
    $setting = App\Models\Setting::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Behandlung von Erektionsstörungen - {{ $setting->business_name }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link href="{{asset('css/new-design.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/website_header.css') }}">
    <link href="{{asset('styles.css')}}?v={{ time() }}" rel="stylesheet">
    
    <link rel="shortcut icon" type="image/x-icon" href="{{$setting->favicon}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Global typography: Inter (body) + Clash Display (headings) -->
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6, .display-4, .display-5 { font-family: 'Clash Display', sans-serif; }
        h1 span, h2 span, h3 span, h4 span, h5 span, h6 span, .display-4 span, .display-5 span { font-family: inherit; }

        /* ed-hero variables */
        :root {
            --ed-radius-lg: 20px;
            --ed-dark: #1a1a1a;
            --ed-text-light: #555;
            --ed-text-muted: #888;
            --ed-max-width: 1280px;
            --primary-color: #3b6fd4;
        }
        .ed-hero {
            position: relative;
            width: 100%;
            min-height: 520px;
            overflow: hidden;
            background-color: #f3f0ff;
        }
        .ed-hero-bg {
            position: absolute; inset: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            object-position: center top;
            opacity: 0.8;
        }
        .ed-hero-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to right, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.7) 45%, transparent 70%);
        }
        .ed-hero-inner {
            position: relative; z-index: 2;
            max-width: var(--ed-max-width);
            margin: 0 auto;
            padding: 48px 24px;
            display: flex;
            align-items: center;
            min-height: 520px;
        }
        .ed-hero-text {
            max-width: 480px;
            background: rgba(255,255,255,0.4);
            backdrop-filter: blur(16px);
            border-radius: var(--ed-radius-lg);
            padding: 40px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.06);
        }
        .ed-hero-text h1 {
            font-size: 2.8rem; font-weight: 800; line-height: 1.1;
            color: var(--ed-dark); margin-bottom: 20px;
        }
        .ed-hero-text > p {
            font-size: 1rem; color: var(--ed-text-light); line-height: 1.6; margin-bottom: 24px;
        }
        .hero-cta {
            display: inline-flex; align-items: center;
            padding: 16px 36px; background: var(--primary-color);
            color: #fff; border-radius: 50px;
            font-size: 1.1rem; font-weight: 700;
            box-shadow: 0 6px 20px rgba(59,111,212,0.35);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .hero-cta:hover { background: #2a52a8; color: #fff; transform: translateY(-2px); }
        .hero-pricing { margin-top: 18px; font-size: 0.9rem; color: var(--ed-text-light); line-height: 1.5; }
        .hero-rating { margin-top: 14px; display: flex; align-items: center; gap: 8px; font-size: 0.9rem; }
        .hero-rating .stars { color: #f59e0b; font-size: 1.1rem; }
        .ed-hero-badge {
            position: absolute;
            bottom: 48px; right: 80px;
            background: linear-gradient(135deg, rgba(59, 111, 212, 0.9), rgba(30,30,120,0.95));
            backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 28px 32px;
            color: #fff;
            text-align: center;
            box-shadow: 0 8px 30px rgba(59,111,212,0.3);
            z-index: 3;
            max-width: 250px;
        }
        .ed-hero-badge .badge-big { font-size: 3.5rem; font-weight: 900; line-height: 1; }
        .ed-hero-badge .badge-big span { font-size: 2rem; }
        .ed-hero-badge .badge-sub { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 8px; }

        /* Sticky CTA Bar */
        .sticky-cta-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
            padding: 12px 0;
            z-index: 1000;
            display: none;
        }
        .sticky-cta-bar.show { display: block; }

        /* Steps */
        .steps-section { padding: 80px 0; background: #f8faff; text-align: center; }
        .steps-title { font-size: 2.8rem; font-weight: 800; margin-bottom: 10px; }
        .steps-subtitle { font-size: 2.2rem; font-weight: 800; color: var(--primary-color); font-style: italic; margin-bottom: 50px; }
        .step-card { background: #fff; border-radius: 20px; padding: 40px 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); height: 100%; transition: transform 0.3s ease; }
        .step-card:hover { transform: translateY(-10px); }
        .step-num { width: 40px; height: 40px; background: var(--primary-color); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-weight: 800; }
        .step-card h3 { font-size: 1.3rem; font-weight: 800; margin-bottom: 15px; min-height: 3em; }
        .step-card p { color: #666; font-size: 0.95rem; line-height: 1.6; }

        /* Content */
        .content-section { padding: 80px 0; }
        .toc-box { background: #f8f9fa; border-radius: 15px; padding: 30px; margin-bottom: 40px; }
        .toc-box h3 { font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; }
        .toc-list { list-style: none; padding: 0; margin: 0; }
        .toc-list li { margin-bottom: 12px; }
        .toc-list a { color: var(--primary-color); text-decoration: none; font-weight: 500; font-size: 0.95rem; border-bottom: 1px solid transparent; transition: all 0.2s; }
        .toc-list a:hover { border-bottom-color: var(--primary-color); }
        
        .article-content h2 { font-size: 2.2rem; font-weight: 800; margin: 50px 0 25px; }
        .article-content h3 { font-size: 1.6rem; font-weight: 700; margin: 35px 0 20px; }
        .article-content h4 { font-size: 1.25rem; font-weight: 700; margin: 25px 0 15px; }
        .article-content p { font-size: 1.05rem; line-height: 1.8; color: #444; margin-bottom: 20px; }
        .article-content ul { margin-bottom: 25px; }
        .article-content li { font-size: 1.05rem; line-height: 1.7; color: #444; margin-bottom: 10px; }

        /* Comparison Table */
        .med-table-wrapper { overflow-x: auto; margin: 30px 0 40px; }
        .med-table { width: 100%; border-collapse: collapse; min-width: 600px; }
        .med-table th, .med-table td { padding: 18px; border: 1px solid #eee; text-align: center; }
        .med-table th { background: #f8faff; font-weight: 700; color: var(--ed-dark); }
        .med-table td:first-child { text-align: left; font-weight: 700; background: #fcfcfc; }

        /* Info Box */
        .info-box { background: #f0f7ff; border-left: 5px solid var(--primary-color); padding: 30px; border-radius: 0 15px 15px 0; margin: 40px 0; }
        .info-box h4 { margin-top: 0; color: var(--primary-color); }
        .info-box p { margin-bottom: 0; }

        /* FAQ */
        .accordion-item { border: none; margin-bottom: 10px; border-radius: 12px !important; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .accordion-button { font-weight: 700; padding: 20px; font-size: 1rem; }
        .accordion-button:not(.collapsed) { background-color: #f0f4ff; color: var(--primary-color); }
    </style>
</head>
<body>
    @include('layout.partials.skeleton_loader')
@include('layout.partials.navbar_website')

<!-- Breadcrumb -->
<div class="bg-light border-bottom">
    <div class="container py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Startseite</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categories') }}" class="text-decoration-none text-muted">Männergesundheit</a></li>
                <li class="breadcrumb-item active" aria-current="page">Erektionsstörungen</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Hero Section -->
<section class="ed-hero">
    <img class="ed-hero-bg" src="https://images.unsplash.com/photo-1511130558040-bb3396b42b79?auto=format&fit=crop&w=1920&q=80" alt="Hintergrund">
    <div class="ed-hero-overlay"></div>
    <div class="ed-hero-inner container">
        <div class="ed-hero-text">
            <h1>Behandlung von Erektionsstörungen</h1>
            <p>Führen Sie einfach unsere Online-Beratung durch, um ein Rezept zu erhalten und das Potenzmittel wird Ihnen in 1-2 Werktagen geliefert.</p>
            
            <a href="{{ $consultationUrl }}" class="hero-cta">
                Zu den medizinischen Fragen
            </a>

            <div class="hero-pricing">
                Behandlungsgebühr 29 € +<br>
                Medikament ab 41,58 €
            </div>
            
            <div class="hero-rating">
                <span class="stars">★★★★★</span>
                <strong>4,79</strong> Hervorragend
                <span class="text-muted">14.082 Bewertungen</span>
            </div>
        </div>
    </div>
    <div class="ed-hero-badge">
        <div class="badge-big">85<span>%</span></div>
        <div class="badge-sub">DER MÄNNER BERICHTEN VON EINER BESSERUNG</div>
    </div>
</section>

<!-- Features Bar (Sticky CTA style in simplified form) -->
<section class="py-4 bg-white border-bottom shadow-sm d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-4">
            <span class="fw-bold">Jetzt online Beratung starten</span>
            <div class="vr"></div>
            <div class="text-muted small">Original Medikamente | Diskrete Lieferung | Deutsche Ärzte</div>
        </div>
        <a href="{{ $consultationUrl }}" class="btn btn-primary rounded-pill px-4 fw-bold">Rezept anfragen</a>
    </div>
</section>

<!-- 3 Steps Section -->
<section class="steps-section">
    <div class="container">
        <h2 class="steps-title">3 einfache Schritte</h2>
        <p class="steps-subtitle">100 % online</p>
        
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-num">1</div>
                    <h3>Füllen Sie den <span class="text-primary">medizinischen Fragebogen</span> aus</h3>
                    <p>Starten Sie die Online-Konsultation und beantworten Sie die medizinischen Fragen zu Ihrem Gesundheitszustand.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-num">2</div>
                    <h3>Wählen Sie die <span class="text-primary">gewünschte Behandlung</span></h3>
                    <p>Ein in Deutschland zugelassener Arzt prüft Ihre Angaben und stellt Ihnen bei Bedarf ein Rezept aus.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-num">3</div>
                    <h3>Lieferung in <span class="text-primary">1–2 Werktagen</span></h3>
                    <p>Ihre Bestellung wird neutral verpackt und diskret per Express-Versand an Ihre Wunschadresse geliefert.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="content-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="toc-box sticky-top" style="top: 100px;">
                    <h3>Themenliste</h3>
                    <ul class="toc-list">
                        <li><a href="#was-ist-ed">Was ist eine erektile Dysfunktion?</a></li>
                        <li><a href="#wann-behandeln">Wann sollte ED behandelt werden?</a></li>
                        <li><a href="#wie-behandeln">Wie kann ich ED behandeln?</a></li>
                        <li><a href="#pde5-hemmer">PDE5-Hemmer Medikation</a></li>
                        <li><a href="#vergleich">Verfügbare Potenztabletten</a></li>
                        <li><a href="#nebenwirkungen">Können Nebenwirkungen auftreten?</a></li>
                        <li><a href="#kontraindikationen">Wann nicht einnehmen?</a></li>
                        <li><a href="#alternative">Alternative Möglichkeiten</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-8 article-content">
                <section id="was-ist-ed">
                    <h2>Was ist eine erektile Dysfunktion?</h2>
                    <p>Zwischen 3 und 5 Millionen Männer leiden alleine in Deutschland an einer erektilen Dysfunktion. Das entspricht in etwa jedem fünften Mann und zeigt deutlich, dass es sich dabei um ein sehr häufig auftretendes Krankheitsbild handelt. Unter einer erektilen Dysfunktion versteht man die anhaltende oder immer wiederkehrende Unfähigkeit eine Erektion dauerhaft und stark genug aufrechtzuerhalten, um Geschlechtsverkehr zu haben.</p>
                    <p>Männer jeden Alters sind davon betroffen und suchen nach Gründen und Lösungen für ihre Potenzprobleme. Häufig ist die erektile Dysfunktion aber gut behandelbar. Es gibt eine Bandbreite an organischen aber auch psychologischen Ursachen, die eine erektile Dysfunktion auslösen können.</p>
                </section>

                <section id="wann-behandeln">
                    <h2>Wann sollte ED behandelt werden?</h2>
                    <p>Wenn ein Mann über einen längeren Zeitraum hinweg oder immer wieder mit Erektionsstörungen konfrontiert ist und unter diesem Zustand leidet, sollte die erektile Dysfunktion behandelt werden. Erektionsstörungen und Potenzprobleme können unterschiedlicher Natur sein. Oft ist die Erektion entweder nicht stark genug oder aber sie dauert nicht lange genug für den Sexualakt an. Dieses Problem kann jedoch behandelt werden. Bei dr.fuxx haben Sie die Möglichkeit Online Ärzte zu konsultieren und eine erektile Dysfunktion rasch und effektiv zu behandeln.</p>
                </section>

                <section id="wie-behandeln">
                    <h2>Wie kann ich eine erektile Dysfunktion behandeln?</h2>
                    <p>Unter dem Begriff erektile Dysfunktion (ED) versteht man das Unvermögen, eine Erektion zu erreichen oder diese lang genug für den Sexualakt aufrechtzuerhalten. Für ED gibt es verschiedene Ursachen, doch letztendlich tritt eine erektile Dysfunktion dann auf, wenn zu wenig Blut in den Penis fließt bzw. zu schnell wieder abfließt.</p>
                    <p>Moderne medizinische Behandlungen, insbesondere PDE5-Hemmer, setzen genau hier an. Sie entspannen die Muskulatur im Schwellkörper und ermöglichen so einen verbesserten Bluteinstrom bei sexueller Erregung.</p>
                </section>

                <section id="pde5-hemmer">
                    <h2>PDE5-Hemmer Medikation</h2>
                    <p>Phosphodiesterase-5-Hemmer, kurz PDE5-Hemmer, bezeichnen eine Wirkstoffgruppe, die in Medikamenten zur Behandlung der erektilen Dysfunktion zum Einsatz kommt. In Deutschland sind vier verschiedene PDE5-Hemmer zugelassen. Medikamente wie Viagra®, Cialis®, Levitra® und Spedra® gehören zu dieser Gruppe.</p>
                    <p>Erektile Dysfunktion kann dadurch zustande kommen, dass nicht genügend Blut in das Glied fließt. Dieser Umstand lässt sich häufig auf ein Enzym namens Phosphodiesterase-5 zurückführen. PDE5-Hemmer blockieren dieses Enzym, wodurch der Blutfluss verbessert wird.</p>
                </section>

                <section id="vergleich">
                    <h2>Verfügbare Potenztabletten</h2>
                    <div class="med-table-wrapper">
                        <table class="med-table">
                            <thead>
                                <tr>
                                    <th>Eigenschaft</th>
                                    <th>Viagra (Sildenafil)</th>
                                    <th>Cialis (Tadalafil)</th>
                                    <th>Levitra (Vardenafil)</th>
                                    <th>Spedra (Avanafil)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Wirkung nach</td>
                                    <td>30-60 Min</td>
                                    <td>30-60 Min</td>
                                    <td>25-60 Min</td>
                                    <td>15-30 Min</td>
                                </tr>
                                <tr>
                                    <td>Wirkungsdauer</td>
                                    <td>Bis zu 4-5 Std.</td>
                                    <td>Bis zu 36 Std.</td>
                                    <td>Bis zu 4-5 Std.</td>
                                    <td>Bis zu 6 Std.</td>
                                </tr>
                                <tr>
                                    <td>Besonderheit</td>
                                    <td>Der Klassiker, bewährt</td>
                                    <td>"Wochenendpille"</td>
                                    <td>Wirkt oft bei Diabetes</td>
                                    <td>Soll besonders schnell wirken</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="nebenwirkungen">
                    <h2>Können Nebenwirkungen auftreten?</h2>
                    <p>Wie jedes verschreibungspflichtige Arzneimittel können auch PDE5-Hemmer mit möglichen Nebenwirkungen einhergehen. Die meisten Anwender vertragen die Medikamente jedoch gut.</p>
                    <ul>
                        <li>Kopfschmerzen</li>
                        <li>Gesichtsrötung (Flushing)</li>
                        <li>Verstopfte Nase</li>
                        <li>Verdauungsprobleme</li>
                        <li>Schwindel</li>
                    </ul>
                    <p>Sollten Sie ungewöhnliche Symptome bemerken, wenden Sie sich bitte an einen Arzt.</p>
                </section>

                <section id="kontraindikationen">
                    <h2>Wann man PDE5-Hemmer nicht einnehmen sollte</h2>
                    <p>In gewissen Fällen sollten PDE5-Hemmer nicht eingenommen werden. Dazu gehören unter anderem:</p>
                    <ul>
                        <li>Einnahme von nitrathaltigen Medikamenten (Herzmedikamente)</li>
                        <li>Schwere Herz-Kreislauf-Erkrankungen</li>
                        <li>Kürzlich erlittener Schlaganfall oder Herzinfarkt</li>
                        <li>Schwere Leber- oder Nierenerkrankungen</li>
                    </ul>
                    <div class="info-box">
                        <h4>Wichtig: Ärztliche Prüfung</h4>
                        <p>Bei dr.fuxx prüfen erfahrene Ärzte Ihre medizinischen Angaben im Fragebogen sorgfältig, um sicherzustellen, dass die gewählte Behandlung für Sie sicher und geeignet ist.</p>
                    </div>
                </section>

                <section id="alternative">
                    <h2>Alternative Behandlungsmöglichkeiten</h2>
                    <h3>Psychologische Unterstützung</h3>
                    <p>Häufig spielen psychologische Faktoren wie Stress, Leistungsdruck oder Beziehungsprobleme eine Rolle. In diesen Fällen kann eine Sexualtherapie oder ein Gespräch mit einem Psychologen sehr hilfreich sein.</p>
                    
                    <h3>Lebensstil-Änderungen</h3>
                    <p>Ein gesunder Lebensstil kann die Potenz positiv beeinflussen:</p>
                    <ul>
                        <li>Regelmäßige Bewegung und Sport</li>
                        <li>Gewichtsreduktion bei Übergewicht</li>
                        <li>Verzicht auf Nikotin und übermäßigen Alkoholkonsum</li>
                        <li>Ausgewogene Ernährung</li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold display-6">Sie haben Fragen? <br><span class="text-primary">Hier gibt es Antworten!</span></h2>
        <div class="max-width-800 mx-auto">
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Wie viel kostet das Behandlungspaket und was ist darin enthalten?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Die Behandlungsgebühr beträgt 29 €. Medikamente beginnen ab 41,58 €. Diskrete Lieferung innerhalb von 1-2 Werktagen ist inklusive.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Wie hoch sind die Lieferkosten?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Der Standard-Expressversand ist in der Servicegebühr bereits enthalten. Es fallen keine versteckten Zusatzkosten an.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Muss ich mit einem Arzt sprechen?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Die Beratung erfolgt asynchron über einen medizinischen Fragebogen. Ein persönliches Gespräch oder Telefonat ist in den meisten Fällen nicht erforderlich, es sei denn, der Arzt hat Rückfragen zu Ihrem Gesundheitszustand.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            Ist Ihr Service diskret?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Ja, absolut. Der Versand erfolgt in einer neutralen Verpackung ohne Hinweis auf den Inhalt oder dr.fuxx. Auch auf Ihrem Kontoauszug erscheint ein neutraler Buchungstext.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Payment Options -->
<section class="py-5 bg-dark text-white text-center">
    <div class="container">
        <p class="mb-4 opacity-75">Akzeptierte Zahlungsmethoden:</p>
        <div class="d-flex flex-wrap justify-content-center gap-4 align-items-center opacity-75">
            <span class="fs-4 fw-bold">Klarna.</span>
            <span class="fs-4 fw-bold">VISA</span>
            <span class="fs-4 fw-bold">Maestro</span>
            <span class="fs-5 fw-bold">Google Pay</span>
            <span class="fs-5 fw-bold">Apple Pay</span>
            <span class="fs-4 fw-bold">PayPal</span>
        </div>
    </div>
</section>

<!-- Footer -->
@include('layout.partials.footer')

<!-- Sticky CTA Phone Only -->
<div class="sticky-cta-bar d-md-none border-top">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <div class="fw-bold small">Rezept online anfragen</div>
            <div class="text-primary fw-bold">ab 29 € Service</div>
        </div>
        <a href="{{ $consultationUrl }}" class="btn btn-primary rounded-pill px-4 fw-bold">Starten</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.addEventListener('scroll', function() {
        const stickyBar = document.querySelector('.sticky-cta-bar');
        if (window.scrollY > 400) {
            stickyBar.classList.add('show');
        } else {
            stickyBar.classList.remove('show');
        }
    });
</script>
</body>
</html>
