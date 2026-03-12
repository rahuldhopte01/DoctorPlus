@extends('layout.mainlayout', ['activePage' => 'questionnaire', 'title' => __('Questionnaire Submission Payment')])

@section('css')
<style>
    .payment-container { max-width: 600px; margin: 40px auto; padding: 0 20px; font-family: var(--font-body, 'Inter', sans-serif); }
    .payment-card { background: #ffffff; border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(74, 58, 255, 0.15); overflow: hidden; }
    .payment-header { background: linear-gradient(135deg, var(--primary-color, #4A3AFF) 0%, var(--secondary-color, #00167A) 100%); color: white; padding: 40px 30px; text-align: center; }
    .payment-header h2 { margin: 0; font-size: 28px; font-weight: 700; font-family: var(--font-heading, 'Clash Display', sans-serif); }
    .payment-header p { margin: 10px 0 0; opacity: 0.9; font-size: 15px; }
    .payment-body { padding: 30px; }
    .prescription-info { background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 24px; border: 1px solid #e9ecef; }
    .prescription-info-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px dashed #dee2e6; }
    .prescription-info-item:last-child { border-bottom: none; padding-bottom: 0; }
    .prescription-info-item:first-child { padding-top: 0; }
    .info-label { color: #6c757d; font-size: 14px; }
    .info-value { font-weight: 600; color: #212529; }
    .total-row { background: var(--purple-light, #EFEBFF); margin: -20px -20px 0; padding: 16px 20px; border-radius: 0 0 12px 12px; border-top: 1px solid rgba(74, 58, 255, 0.1); }
    .total-row .info-label { font-weight: 600; color: var(--primary-color, #4A3AFF); }
    .total-row .info-value { font-size: 24px; color: var(--primary-color, #4A3AFF); font-weight: 700; }
    .secure-badge { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px; font-size: 13px; color: #6c757d; border: 1px solid #e9ecef; }
    .secure-badge i { color: #198754; }
    #checkout-btn { width: 100%; padding: 16px 24px; font-size: 16px; font-weight: 600; border-radius: 12px; border: none; background: #00bda6; color: white; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 10px; }
    #checkout-btn:hover:not(:disabled) { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0, 189, 166, 0.3); background-color: #00a893; }
    #checkout-btn:disabled { background: #6c757d; cursor: not-allowed; transform: none; box-shadow: none; }
    .spinner { width: 20px; height: 20px; border: 2px solid #ffffff; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite; display: none; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .loading .spinner { display: inline-block; }
    .loading .btn-text { display: none; }
    .error-message { background: #ffebee; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: none; border: 1px solid #ffcdd2; }
    .back-link { display: flex; align-items: center; gap: 8px; color: #6c757d; text-decoration: none; font-size: 15px; margin-bottom: 24px; transition: color 0.2s; font-weight: 500; }
    .back-link:hover { color: var(--primary-color, #4A3AFF); }
    .payment-methods { display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e9ecef; }
    .payment-methods img { height: 24px; opacity: 0.7; transition: opacity 0.2s; }
    .payment-methods img:hover { opacity: 1; }
    .stripe-badge { display: flex; align-items: center; justify-content: center; gap: 4px; font-size: 12px; color: #6c757d; margin-top: 16px; }
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
            <h2>{{ __('Submit Questionnaire to Doctor') }}</h2>
            <p>{{ __('Pay the submission fee to send your questionnaire for review') }}</p>
        </div>

        <div class="payment-body">
            <div id="error-message" class="error-message"></div>

            <div class="prescription-info">
                <div class="prescription-info-item">
                    <span class="info-label">{{ __('Category') }}</span>
                    <span class="info-value">{{ $category->name }}</span>
                </div>
                <div class="prescription-info-item total-row">
                    <span class="info-label">{{ __('Submission fee') }}</span>
                    <span class="info-value">{{ $currency }}{{ number_format($submissionFee, 2) }}</span>
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
                    {{ __('Pay') }} {{ $currency }}{{ number_format($submissionFee, 2) }}
                </span>
            </button>

            <div class="payment-methods">
                <img src="https://cdn.jsdelivr.net/gh/lipis/payment-icons@1.0.0/svg/visa.svg" alt="Visa">
                <img src="https://cdn.jsdelivr.net/gh/lipis/payment-icons@1.0.0/svg/mastercard.svg" alt="Mastercard">
                <img src="https://cdn.jsdelivr.net/gh/lipis/payment-icons@1.0.0/svg/amex.svg" alt="Amex">
            </div>

            <div class="stripe-badge">
                <span>{{ __('Payments secured by Stripe') }}</span>
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
