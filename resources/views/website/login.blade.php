@extends('layout.mainlayout',['activePage' => 'login'])

@section('content')
<div class="container py-5 mt-5">
    <div class="row min-vh-75 align-items-center justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card border-0 shadow-bloomwell rounded-4 overflow-hidden">
                <div class="row g-0">
                    <!-- Left side: Image -->
                    <div class="col-md-6 d-none d-md-block bg-light position-relative">
                        <div class="position-absolute top-50 start-50 translate-middle w-100 p-5 text-center z-index-2">
                            <h2 class="display-6 fw-bold mb-4" style="color: var(--primary-color);">Sprechen Sie mit Tausenden von Fachärzten.</h2>
                            <img src="{{asset('assets/image/login.png')}}" class="img-fluid custom-login-image" alt="Login Graphic" style="max-height: 250px; object-fit: contain;">
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(242, 239, 234, 0.9) 0%, rgba(255, 255, 255, 0.4) 100%);"></div>
                    </div>

                    <!-- Right side: Form -->
                    <div class="col-md-6 p-4 p-lg-5">
                        <div class="mb-5">
                            <h2 class="fw-bold fs-3 text-dark mb-1">Willkommen zurück,</h2>
                            <h3 class="fw-medium text-muted fs-5">Anmelden, um loszulegen!</h3>
                        </div>

                        <form action="{{ url('patient-login') }}" method="post">
                            @csrf
                            
                            <div class="form-floating mb-3">
                                <input type="email" name="email" id="email" class="form-control rounded-3 border-light shadow-sm @error('email') is-invalid @enderror" placeholder="E-Mail eingeben" value="{{ old('email') }}" required autofocus>
                                <label for="email" class="text-muted"><i class="bi bi-envelope me-2"></i>E-Mail-Adresse</label>
                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-floating mb-4">
                                <input type="password" name="password" id="password" class="form-control rounded-3 border-light shadow-sm @error('password') is-invalid @enderror" placeholder="Passwort eingeben" required>
                                <label for="password" class="text-muted"><i class="bi bi-lock me-2"></i>Passwort</label>
                                @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            @if (session('error'))
                            <div class="alert alert-danger rounded-3 p-3 mb-4 d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <a href="{{url('/forgot_password')}}" class="text-decoration-none small text-muted hover-primary transition-all">Passwort vergessen?</a>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-4 bloomwell-btn border-0 py-3 fw-semibold">
                                Anmelden
                            </button>

                            <div class="text-center mt-4 border-top pt-4">
                                <p class="text-muted small mb-3">Haben Sie noch kein Konto? <a href="{{url('/signup')}}" class="text-primary text-decoration-none fw-semibold ms-1">Registrieren</a></p>
                                
                                <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
                                   <a href="{{url('/doctor/doctor_login')}}" class="badge bg-light text-dark border p-2 text-decoration-none hover-primary transition-all"><i class="bi bi-stethoscope me-1"></i> Arzt-Login</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .shadow-bloomwell {
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
    }
    .hover-primary:hover {
        color: var(--primary-color) !important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(0, 166, 81, 0.25);
    }
</style>
@endsection