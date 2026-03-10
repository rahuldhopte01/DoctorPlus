<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class QuestionnairePaymentController extends Controller
{
    /**
     * Get questionnaire submission fee from settings.
     */
    protected function getSubmissionFee(): float
    {
        $setting = Setting::first();
        return (float) ($setting->questionnaire_submission_fee ?? $setting->prescription_fee ?? 50.00);
    }

    /**
     * Convert amount to cents for Stripe.
     */
    protected function convertToCents(float $amount, string $currency): int
    {
        $zeroDecimalCurrencies = ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf'];
        if (in_array(strtolower($currency), $zeroDecimalCurrencies)) {
            return (int) $amount;
        }
        return (int) round($amount * 100);
    }

    /**
     * Show the payment page for questionnaire submission.
     */
    public function showPaymentPage($categoryId)
    {
        if (!Auth::check()) {
            return redirect(url('/login'))->with('error', __('Please login to continue.'));
        }

        $pending = session()->get('questionnaire_pending_payment_' . $categoryId);
        if (!$pending) {
            return redirect()->route('questionnaire.category', ['categoryId' => $categoryId])
                ->with('error', __('Your session expired. Please submit the questionnaire again.'));
        }

        $category = Category::find($categoryId);
        if (!$category) {
            session()->forget('questionnaire_pending_payment_' . $categoryId);
            return redirect(route('categories'))->with('error', __('Category not found.'));
        }

        $setting = Setting::first();
        $currency = $setting->currency_symbol ?? '$';
        $currencyCode = strtolower($setting->currency_code ?? 'USD');
        $submissionFee = $this->getSubmissionFee();

        return view('website.questionnaire.payment', compact(
            'category',
            'setting',
            'currency',
            'currencyCode',
            'submissionFee'
        ));
    }

    /**
     * Create Stripe Checkout Session for questionnaire submission payment.
     */
    public function createCheckoutSession(Request $request, $categoryId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $pending = session()->get('questionnaire_pending_payment_' . $categoryId);
        if (!$pending) {
            return response()->json(['error' => __('Session expired. Please submit the questionnaire again.')], 400);
        }

        $setting = Setting::first();
        $stripeSecretKey = $setting->stripe_secret_key ?? null;
        if (empty($stripeSecretKey)) {
            return response()->json(['error' => __('Stripe is not configured.')], 500);
        }

        $currencyCode = strtolower($setting->currency_code ?? 'USD');
        $amount = $this->getSubmissionFee();
        $amountInCents = $this->convertToCents($amount, $currencyCode);

        try {
            $stripe = new StripeClient($stripeSecretKey);
            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currencyCode,
                        'product_data' => [
                            'name' => __('Questionnaire submission fee'),
                            'description' => __('Submit your questionnaire to the doctor for review'),
                        ],
                        'unit_amount' => $amountInCents,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url('/questionnaire/payment/success/' . $categoryId) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => url('/questionnaire/category/' . $categoryId . '/payment'),
                'metadata' => [
                    'category_id' => $categoryId,
                    'user_id' => Auth::id(),
                ],
                'customer_email' => Auth::user()->email ?? null,
            ]);

            return response()->json([
                'sessionId' => $session->id,
                'url' => $session->url,
            ]);
        } catch (\Exception $e) {
            Log::error('Questionnaire payment Stripe session failed: ' . $e->getMessage());
            return response()->json(['error' => __('Payment could not be started. Please try again.')], 500);
        }
    }

    /**
     * Handle successful payment and complete questionnaire submission.
     */
    public function paymentSuccess(Request $request, $categoryId)
    {
        if (!Auth::check()) {
            return redirect(url('/login'))->with('error', __('Please login to continue.'));
        }

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('questionnaire.category', ['categoryId' => $categoryId])
                ->with('error', __('Invalid payment session.'));
        }

        $pending = session()->get('questionnaire_pending_payment_' . $categoryId);
        if (!$pending) {
            return redirect()->route('questionnaire.category', ['categoryId' => $categoryId])
                ->with('error', __('Your session expired. Please submit the questionnaire again.'));
        }

        $setting = Setting::first();
        $stripeSecretKey = $setting->stripe_secret_key ?? null;
        if (empty($stripeSecretKey)) {
            return redirect()->route('questionnaire.category', ['categoryId' => $categoryId])
                ->with('error', __('Payment verification is not configured.'));
        }

        try {
            $stripe = new StripeClient($stripeSecretKey);
            $session = $stripe->checkout->sessions->retrieve($sessionId);
            if ($session->payment_status !== 'paid') {
                return redirect()->route('questionnaire.category', ['categoryId' => $categoryId])
                    ->with('error', __('Payment was not completed.'));
            }
            if ((int) ($session->metadata->user_id ?? 0) !== (int) Auth::id()) {
                return redirect(url('/user_profile'))->with('error', __('Unauthorized.'));
            }
        } catch (\Exception $e) {
            Log::error('Questionnaire payment verification failed: ' . $e->getMessage());
            return redirect()->route('questionnaire.category', ['categoryId' => $categoryId])
                ->with('error', __('Payment verification failed. Please contact support.'));
        }

        $redirectUrl = app(QuestionnaireController::class)->completeSubmissionAfterPayment($categoryId);
        session()->forget('questionnaire_pending_payment_' . $categoryId);

        return redirect($redirectUrl)->with('success', __('Questionnaire submitted successfully.'));
    }
}
