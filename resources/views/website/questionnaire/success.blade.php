@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', __('Questionnaire Submitted'))

@section('css')
<style>
    .success-page-ui {
        background: #ffffff;
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4rem 1.5rem;
    }
    .success-card {
        background: #ffffff;
        border-radius: 30px;
        padding: 4rem 3rem;
        text-align: center;
        max-width: 650px;
        width: 100%;
        box-shadow: 0 20px 50px rgba(138, 72, 255, 0.08);
        border: 2px solid rgba(138, 72, 255, 0.1);
        position: relative;
    }
    @keyframes success-pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(138, 72, 255, 0.4); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(138, 72, 255, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(138, 72, 255, 0); }
    }
    .success-icon-wrapper {
        width: 100px;
        height: 100px;
        background: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2.5rem;
        color: #8a48ff;
        border: 2px solid #8a48ff;
        animation: success-pulse 2s infinite;
    }
    .success-icon-wrapper i {
        font-size: 3.5rem;
        line-height: 1;
        color: #8a48ff !important;
    }
    .success-title {
        font-family: 'Clash Display', sans-serif;
        font-weight: 700;
        font-size: 2.75rem;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .success-text {
        font-family: 'Inter', sans-serif;
        color: #666;
        font-size: 1.15rem;
        line-height: 1.6;
        margin-bottom: 3rem;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    .success-actions {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        align-items: center;
    }
    @media (min-width: 640px) {
        .success-actions {
            flex-direction: row;
            justify-content: center;
        }
    }
    .btn-premium-solid {
        background: #8a48ff;
        color: #ffffff;
        padding: 1.15rem 2.5rem;
        border-radius: 100px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(138, 72, 255, 0.25);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }
    .btn-premium-solid:hover {
        background: #7a35fa;
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(138, 72, 255, 0.35);
        color: #fff;
    }
    .btn-premium-outline {
        background: transparent;
        color: #1a1a1a;
        padding: 1.15rem 2.5rem;
        border-radius: 100px;
        font-weight: 700;
        text-decoration: none;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }
    .btn-premium-outline:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        transform: translateY(-2px);
        color: #1a1a1a;
    }
</style>
@endsection

@section('content')
<main class="success-page-ui">
    <div class="success-card">
        <div class="success-icon-wrapper">
            <i class="bi bi-check-lg"></i>
        </div>
        
        <h1 class="success-title">{{ __('Application Submitted!') }}</h1>
        
        <p class="success-text">
            {{ __('Thank you for completing the questionnaire. Your responses have been successfully transmitted and are now being reviewed by our medical team.') }}
        </p>

        <div class="success-actions">
            <a href="{{ route('categories') }}" class="btn-premium-solid">
                <span>{{ __('Browse Treatments') }}</span>
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </a>
            <a href="{{ url('/') }}" class="btn-premium-outline">
                <span>{{ __('Return Home') }}</span>
                <i class="bi bi-house-door-fill"></i>
            </a>
        </div>
    </div>
</main>
@endsection
