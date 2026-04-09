@extends('layout.mainlayout', ['activePage' => 'questionnaire', 'title' => __('Questionnaire Submission Payment')])

@section('css')
<style>
    :root {
        --primary-brand: #8a48ff;
        --secondary-brand: #6e2feb;
        --light-brand: #f4effe;
        --success-brand: #22c55e;
        --font-heading: 'Clash Display', sans-serif;
        --font-body: 'Inter', sans-serif;
    }

    body { font-family: var(--font-body) !important; background-color: #f8f9fa; }
    h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading) !important; font-weight: 700; color: #1a1a1a; letter-spacing: -0.5px; }

    .payment-container { max-width: 600px; margin: 60px auto; padding: 0 20px; }
    
    .payment-card { 
        background: #ffffff; 
        border-radius: 24px; 
        border: none; 
        box-shadow: 0 15px 35px rgba(138, 72, 255, 0.1); 
        overflow: hidden; 
        transition: transform 0.3s ease;
    }

    .payment-header { 
        background: linear-gradient(135deg, var(--primary-brand) 0%, var(--secondary-brand) 100%); 
        color: white; 
        padding: 50px 30px; 
        text-align: center; 
        position: relative;
    }

    .payment-header h2 { 
        margin: 0; 
        font-size: 30px; 
        color: white !important;
        text-transform: none;
    }

    .payment-header p { 
        margin: 15px 0 0; 
        opacity: 0.9; 
        font-size: 16px; 
        font-weight: 500;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    .payment-body { padding: 40px 30px; }

    .q-info-box { 
        background: #ffffff; 
        border-radius: 16px; 
        padding: 24px; 
        margin-bottom: 30px; 
        border: 1px solid #f0f0f0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    
    .q-info-item { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        padding: 16px 0; 
        border-bottom: 1px solid #f8f9fa;
    }
    
    .q-info-item:last-child { border-bottom: none; }
    .q-info-item:first-child { padding-top: 0; }
    
    .q-label { color: #6e6e6e; font-size: 15px; font-weight: 500; }
    .q-value { font-weight: 700; color: #1a1a1a; font-size: 16px; }

    .fee-highlight { 
        background: var(--light-brand); 
        margin: 10px -24px -24px; 
        padding: 20px 24px; 
        border-radius: 0 0 16px 16px; 
        border-top: 1px solid rgba(138, 72, 255, 0.1); 
    }
    
    .fee-highlight .q-label { color: var(--primary-brand); font-weight: 700; font-family: var(--font-heading); font-size: 16px; }
    .fee-highlight .q-value { font-size: 26px; color: var(--primary-brand); font-family: var(--font-heading); }

    .secure-badge { 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        gap: 10px; 
        padding: 14px; 
        background: #fdfdfd; 
        border-radius: 12px; 
        margin-bottom: 25px; 
        font-size: 14px; 
        color: #777; 
        border: 1px solid #eee;
        font-weight: 500;
    }
    .secure-badge i { color: var(--success-brand); font-size: 18px; }

    #checkout-btn { 
        width: 100%; 
        padding: 18px 30px; 
        font-size: 18px; 
        font-weight: 700; 
        border-radius: 50px; 
        border: none; 
        background: var(--primary-brand); 
        color: white; 
        cursor: pointer; 
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        gap: 12px; 
        box-shadow: 0 8px 25px rgba(138, 72, 255, 0.2);
        animation: btnPulse 2.5s ease-in-out infinite;
    }

    @keyframes btnPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(138, 72, 255, 0.4); }
        50%       { box-shadow: 0 0 0 15px rgba(138, 72, 255, 0); }
    }

    #checkout-btn:hover:not(:disabled) { 
        transform: translateY(-5px); 
        box-shadow: 0 12px 30px rgba(138, 72, 255, 0.3); 
        background-color: var(--secondary-brand); 
    }
    
    #checkout-btn:active { transform: translateY(-2px); }
    
    #checkout-btn:disabled { 
        background: #ced4da; 
        cursor: not-allowed; 
        transform: none; 
        box-shadow: none; 
        animation: none;
    }

    .spinner { width: 22px; height: 22px; border: 3px solid rgba(255,255,255,0.3); border-top: 3px solid #ffffff; border-radius: 50%; animation: spin 1s linear infinite; display: none; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    
    .loading .spinner { display: inline-block; }
    .loading .btn-text { display: none; }

    .error-msg-box { 
        background: #fff5f5; 
        color: #d63031; 
        padding: 16px; 
        border-radius: 12px; 
        margin-bottom: 25px; 
        font-size: 15px; 
        display: none; 
        border: 1px solid #fab1a0;
        font-weight: 500;
        text-align: center;
    }

    .back-link { 
        display: inline-flex; 
        align-items: center; 
        gap: 10px; 
        color: #777; 
        text-decoration: none; 
        font-size: 16px; 
        margin-bottom: 30px; 
        transition: all 0.3s ease; 
        font-weight: 600; 
        padding: 8px 16px;
        border-radius: 10px;
        background: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }
    .back-link:hover { color: var(--primary-brand); transform: translateX(-5px); background: #fdfdfd; }
    .back-link i { font-size: 14px; }

    .payment-trust-footer { margin-top: 40px; text-align: center; }
    .pm-icons { display: flex; align-items: center; justify-content: center; gap: 20px; margin-bottom: 20px; }
    .pm-icons img { height: 22px; opacity: 0.6; filter: grayscale(100%); transition: all 0.3s; }
    .pm-icons img:hover { opacity: 0.9; filter: grayscale(0%); transform: scale(1.1); }
    
    .pm-secure-text { font-size: 13px; color: #999; font-weight: 500; }
</style>
@endsection

@section('content')
<div class="payment-container">
    <a href="{{ route('questionnaire.category', ['categoryId' => $category->id]) }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        {{ __('Back to Questionnaire') }}
    </a>

    <div class="payment-card">
        <div class="payment-header">
            <h2>{{ __('Submit Questionnaire') }}</h2>
            <p>{{ __('Send your medical answers to our doctors for clinical review') }}</p>
        </div>

        <div class="payment-body">
            <div id="error-message" class="error-msg-box"></div>

            <div class="q-info-box">
                <div class="q-info-item">
                    <span class="q-label">{{ __('Medical Category') }}</span>
                    <span class="q-value">{{ $category->name }}</span>
                </div>
                <div class="q-info-item fee-highlight">
                    <span class="q-label">{{ __('Consultation Fee') }}</span>
                    <span class="q-value">{{ number_format($submissionFee, 2, ',', '.') }} {{ $currency }}</span>
                </div>
            </div>

            <div class="secure-badge">
                <i class="fas fa-shield-check"></i>
                {{ __('Secured by Stripe Bank-level encryption') }}
            </div>

            <button type="button" id="checkout-btn" onclick="proceedToCheckout()">
                <span class="spinner"></span>
                <span class="btn-text">
                    <i class="fas fa-lock"></i>
                    {{ __('Secure Payment') }} - {{ number_format($submissionFee, 2, ',', '.') }} {{ $currency }}
                </span>
            </button>

            <div class="payment-trust-footer">
                <div class="pm-icons">
                    <img src="https://cdn.jsdelivr.net/gh/lipis/payment-icons@1.0.0/svg/visa.svg" alt="Visa">
                    <img src="https://cdn.jsdelivr.net/gh/lipis/payment-icons@1.0.0/svg/mastercard.svg" alt="Mastercard">
                    <img src="https://cdn.jsdelivr.net/gh/lipis/payment-icons@1.0.0/svg/amex.svg" alt="Amex">
                    <img src="https://cdn.jsdelivr.net/gh/lipis/payment-icons@1.0.0/svg/paypal.svg" alt="PayPal">
                </div>
                <div class="pm-secure-text">
                    <i class="fas fa-lock-alt me-1"></i> {{ __('Your transaction is safe and secure') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const categoryId = {{ $category->id }};
    const stripePublicKey = '{{ $setting->stripe_public_key ?? '' }}';

    @if(empty($setting->stripe_public_key))
        document.getElementById('error-message').textContent = '{{ __("Stripe is not configured. Please contact support.") }}';
        document.getElementById('error-message').style.display = 'block';
        document.getElementById('checkout-btn').disabled = true;
    @endif

    function proceedToCheckout() {
        const btn = document.getElementById('checkout-btn');
        const errorDiv = document.getElementById('error-message');

        btn.classList.add('loading');
        btn.disabled = true;
        errorDiv.style.display = 'none';

        fetch('{{ url("/questionnaire/category/" . $category->id . "/create-checkout-session") }}', {
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

