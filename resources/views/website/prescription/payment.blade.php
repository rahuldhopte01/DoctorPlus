@extends('layout.mainlayout', ['activePage' => 'user', 'title' => __('Prescription Payment')])

@section('css')
<style>
    .payment-container {
        max-width: 600px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .payment-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .payment-header {
        background: linear-gradient(135deg, var(--site_color) 0%, var(--site_color_hover) 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    
    .payment-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }
    
    .payment-header p {
        margin: 10px 0 0;
        opacity: 0.9;
        font-size: 14px;
    }
    
    .payment-body {
        padding: 30px;
    }
    
    .prescription-info {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .prescription-info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .prescription-info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .prescription-info-item:first-child {
        padding-top: 0;
    }
    
    .info-label {
        color: #6c757d;
        font-size: 14px;
    }
    
    .info-value {
        font-weight: 500;
        color: #212529;
    }
    
    .total-row {
        background: #e8f5e9;
        margin: -20px -20px 0;
        padding: 16px 20px;
        border-radius: 0 0 12px 12px;
    }
    
    .total-row .info-label {
        font-weight: 600;
        color: #2e7d32;
    }
    
    .total-row .info-value {
        font-size: 22px;
        color: #2e7d32;
        font-weight: 700;
    }
    
    .secure-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #6c757d;
    }
    
    .secure-badge i {
        color: #28a745;
    }
    
    #checkout-btn {
        width: 100%;
        padding: 16px 24px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 12px;
        border: none;
        background: var(--site_color);
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    #checkout-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    #checkout-btn:disabled {
        background: #6c757d;
        cursor: not-allowed;
    }
    
    .spinner {
        width: 20px;
        height: 20px;
        border: 2px solid #ffffff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        display: none;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading .spinner {
        display: inline-block;
    }
    
    .loading .btn-text {
        display: none;
    }
    
    .error-message {
        background: #ffebee;
        color: #c62828;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        display: none;
    }
    
    .back-link {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        text-decoration: none;
        font-size: 14px;
        margin-bottom: 20px;
        transition: color 0.2s;
    }
    
    .back-link:hover {
        color: var(--site_color);
    }
    
    .medicine-list {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 16px;
        margin-top: 12px;
    }
    
    .medicine-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        font-size: 14px;
    }
    
    .medicine-item:not(:last-child) {
        border-bottom: 1px solid #f0f0f0;
    }
    
    .medicine-bullet {
        width: 6px;
        height: 6px;
        background: var(--site_color);
        border-radius: 50%;
    }
    
    .payment-methods {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }
    
    .payment-methods img {
        height: 24px;
        opacity: 0.7;
    }
    
    .stripe-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        font-size: 12px;
        color: #6c757d;
        margin-top: 16px;
    }
    
    .stripe-badge svg {
        height: 20px;
    }
</style>
@endsection

@section('content')
<div class="payment-container">
    <a href="{{ url('/user_profile') }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        {{ __('Back to Dashboard') }}
    </a>
    
    <div class="payment-card">
        <div class="payment-header">
            <h2>{{ __('Complete Your Payment') }}</h2>
            <p>{{ __('Pay securely to access your prescription') }}</p>
        </div>
        
        <div class="payment-body">
            <div id="error-message" class="error-message"></div>
            
            <div class="prescription-info">
                <div class="prescription-info-item">
                    <span class="info-label">{{ __('Prescription ID') }}</span>
                    <span class="info-value">#{{ $prescription->id }}</span>
                </div>
                
                @if($prescription->doctor && $prescription->doctor->user)
                <div class="prescription-info-item">
                    <span class="info-label">{{ __('Doctor') }}</span>
                    <span class="info-value">{{ $prescription->doctor->user->name }}</span>
                </div>
                @endif
                
                <div class="prescription-info-item">
                    <span class="info-label">{{ __('Date') }}</span>
                    <span class="info-value">{{ $prescription->created_at->format('d M Y') }}</span>
                </div>
                
                @php
                    $medicines = json_decode($prescription->medicines, true) ?? [];
                @endphp
                
                @if(!empty($medicines))
                <div class="prescription-info-item" style="flex-direction: column; align-items: flex-start;">
                    <span class="info-label" style="margin-bottom: 8px;">{{ __('Prescribed Medicines') }} ({{ count($medicines) }})</span>
                    <div class="medicine-list" style="width: 100%;">
                        @foreach($medicines as $medicine)
                        <div class="medicine-item">
                            <span class="medicine-bullet"></span>
                            <span>{{ $medicine['medicine'] ?? 'N/A' }} 
                                @if(!empty($medicine['strength']))
                                    <small class="text-muted">{{ $medicine['strength'] }}</small>
                                @endif
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="prescription-info-item total-row">
                    <span class="info-label">{{ __('Total Amount') }}</span>
                    <span class="info-value">{{ $currency }}{{ number_format($prescriptionFee, 2) }}</span>
                </div>
            </div>
            
            <div class="secure-badge">
                <i class="fas fa-lock"></i>
                {{ __('Secure payment powered by Stripe') }}
            </div>
            
            <button type="button" id="checkout-btn" onclick="proceedToCheckout()">
                <span class="spinner"></span>
                <span class="btn-text">
                    <i class="fas fa-credit-card"></i>
                    {{ __('Pay') }} {{ $currency }}{{ number_format($prescriptionFee, 2) }}
                </span>
            </button>
            
            <div class="payment-methods">
                <img src="https://cdn.jsdelivr.net/gh/lipis/payment-icons@1.0.0/svg/visa.svg" alt="Visa">
                <img src="https://cdn.jsdelivr.net/gh/lipis/payment-icons@1.0.0/svg/mastercard.svg" alt="Mastercard">
                <img src="https://cdn.jsdelivr.net/gh/lipis/payment-icons@1.0.0/svg/amex.svg" alt="Amex">
            </div>
            
            <div class="stripe-badge">
                <span>{{ __('Payments secured by') }}</span>
                <svg viewBox="0 0 60 25" xmlns="http://www.w3.org/2000/svg"><path fill="#635BFF" d="M59.64 14.28h-8.06c.19 1.93 1.6 2.55 3.2 2.55 1.64 0 2.96-.37 4.05-.95v3.32a8.33 8.33 0 0 1-4.56 1.1c-4.01 0-6.83-2.5-6.83-7.48 0-4.19 2.39-7.52 6.3-7.52 3.92 0 5.96 3.28 5.96 7.5 0 .4-.02 1.04-.06 1.48zm-6.3-5.63c-1.03 0-1.87.72-2.1 2.24h4.06c-.07-1.37-.82-2.24-1.96-2.24z"/><path fill="#635BFF" d="M24.92 5.57c2.47 0 4.26.88 4.26.88L28.06 9.9s-1.63-.72-3.26-.72c-1.95 0-2.7.93-2.7 1.71 0 1.14 1.44 1.58 3.03 2.14 2.14.76 4.58 1.7 4.58 4.86 0 3.52-2.87 5.66-6.92 5.66-2.71 0-4.95-.89-4.95-.89l1.14-3.58s2.08.95 3.93.95c1.4 0 2.38-.56 2.38-1.54 0-1.01-1.26-1.44-2.75-1.96-2.23-.77-4.87-1.82-4.87-4.97 0-3.23 2.66-5.79 7.25-5.79z"/><path fill="#635BFF" d="M3.57 5.8h4.02l-.1 2.06h.06c.76-1.28 2.31-2.34 4.31-2.34.5 0 .97.05.97.05v4.06s-.6-.1-1.3-.1c-1.67 0-3.26.79-3.77 2.42-.06.2-.1.43-.1.7v10.29H3.57V5.8z"/><path fill="#635BFF" d="M13.89 5.8h4.14v17.14h-4.14z"/><path fill="#635BFF" d="M43.74 5.57c-3.83 0-6.77 3.34-6.77 7.53 0 5.23 3.41 7.44 7.17 7.44 1.57 0 2.97-.29 4.2-.87v-3.4c-1.01.55-2.17.85-3.5.85-2.07 0-3.9-1.04-3.9-3.29h8.18c0-.46.08-1.21.08-1.7 0-4.06-2.14-6.56-5.46-6.56zm-2.65 5.93c.13-1.41.97-2.6 2.4-2.6 1.54 0 2.18 1.1 2.24 2.6h-4.64z"/><path fill="#635BFF" d="M0 10.63h4v12.31H0z"/><circle fill="#635BFF" cx="2" cy="3.5" r="2.4"/></svg>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const prescriptionId = {{ $prescription->id }};
    const stripePublicKey = '{{ $setting->stripe_public_key ?? '' }}';
    
    @if(!$setting->stripe_public_key)
        document.getElementById('error-message').textContent = '{{ __("Stripe is not configured. Please contact support.") }}';
        document.getElementById('error-message').style.display = 'block';
        document.getElementById('checkout-btn').disabled = true;
    @endif
    
    function proceedToCheckout() {
        const btn = document.getElementById('checkout-btn');
        const errorDiv = document.getElementById('error-message');
        
        // Show loading state
        btn.classList.add('loading');
        btn.disabled = true;
        errorDiv.style.display = 'none';
        
        // Create checkout session
        fetch('{{ url("/prescription/create-checkout-session/" . $prescription->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Redirect to Stripe Checkout
            if (data.url) {
                window.location.href = data.url;
            } else if (data.sessionId && stripePublicKey) {
                const stripe = Stripe(stripePublicKey);
                stripe.redirectToCheckout({ sessionId: data.sessionId });
            } else {
                throw new Error('{{ __("Failed to create payment session") }}');
            }
        })
        .catch(error => {
            errorDiv.textContent = error.message || '{{ __("An error occurred. Please try again.") }}';
            errorDiv.style.display = 'block';
            btn.classList.remove('loading');
            btn.disabled = false;
        });
    }
</script>
@endsection
