@extends('layout.mainlayout',['activePage' => 'login'])

@section('title', 'Admin Login')

@section('content')
<div class="container py-5 mt-5">
    <div class="row min-vh-75 align-items-center justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card border-0 shadow-bloomwell rounded-4 overflow-hidden">
                <div class="row g-0">
                    <!-- Left side: Image -->
                    <div class="col-md-6 d-none d-md-block bg-light position-relative">
                        <div class="position-absolute top-50 start-50 translate-middle w-100 p-5 text-center z-index-2">
                            <h2 class="display-6 fw-bold mb-4" style="color: var(--primary-color);">Plattform-Verwaltung.</h2>
                            <img src="{{asset('assets/image/login.png')}}" class="img-fluid custom-login-image" alt="Admin Login Graphic" style="max-height: 250px; object-fit: contain;">
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(242, 239, 234, 0.9) 0%, rgba(255, 255, 255, 0.4) 100%);"></div>
                    </div>

                    <!-- Right side: Form -->
                    <div class="col-md-6 p-4 p-lg-5">
                        <div class="mb-5">
                            <h2 class="fw-bold fs-3 text-dark mb-1">Admin-Portal</h2>
                            <h3 class="fw-medium text-muted fs-5">Melden Sie sich an, um die Plattform zu verwalten.</h3>
                        </div>

                        @if ($errors->any())
                            @foreach ($errors->all() as $item)
                                <div class="alert alert-danger rounded-3 p-2 mb-4 d-flex align-items-center small" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>{{ $item }}</div>
                                </div>
                            @endforeach
                        @endif

                        <form method="POST" action="{{ url('admin/verify_admin') }}" class="needs-validation" novalidate="">
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

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <a href="{{url('admin_forgot_password')}}" class="text-decoration-none small text-muted hover-primary transition-all">Passwort vergessen?</a>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-4 bloomwell-btn border-0 py-3 fw-semibold">
                                Anmelden <i class="fas fa-sign-in-alt ms-2"></i>
                            </button>

                            <div class="mt-5 text-center">
                                <p class="text-muted mb-3 font-weight-600 small">Andere Portale</p>
                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    <a href="{{ url('doctor/doctor_login') }}" class="badge bg-light text-dark border p-2 text-decoration-none hover-primary transition-all"><i class="fas fa-user-md me-1"></i> Arzt</a>
                                    <a href="{{ url('pharmacy_login') }}" class="badge bg-light text-dark border p-2 text-decoration-none hover-primary transition-all"><i class="fas fa-pills me-1"></i> Apotheke</a>
                                    <a href="{{ url('pathologist_login') }}" class="badge bg-light text-dark border p-2 text-decoration-none hover-primary transition-all"><i class="fas fa-microscope me-1"></i> Labor</a>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ url('patient-login') }}" class="text-muted small text-decoration-none hover-primary"><i class="fas fa-arrow-left me-1"></i> Zurück zum Patienten-Login</a>
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
