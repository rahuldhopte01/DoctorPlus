<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class PrescriptionPaymentController extends Controller
{
    /**
     * Get default prescription fee from settings or use fallback
     */
    protected function getDefaultPrescriptionFee(): float
    {
        $setting = Setting::first();
        return $setting->prescription_fee ?? 50.00;
    }

    /**
     * Show the payment page for a prescription.
     */
    public function showPaymentPage($id)
    {
        $prescription = Prescription::with(['doctor.user', 'user'])->findOrFail($id);
        
        // Verify ownership
        if ($prescription->user_id != auth()->user()->id) {
            abort(403, 'Unauthorized');
        }
        
        // Check if payment is required
        if ($prescription->status !== 'approved_pending_payment') {
            return redirect(url('/user_profile'))->with('info', __('Payment is not required for this prescription.'));
        }
        
        // Check if already paid
        if ($prescription->payment_status == 1) {
            return redirect(url('/user_profile'))->with('info', __('This prescription has already been paid.'));
        }
        
        $setting = Setting::first();
        $currency = $setting->currency_symbol ?? '$';
        $currencyCode = strtolower($setting->currency_code ?? 'USD');
        
        // Get prescription fee (use payment_amount if set, otherwise use default from settings)
        $prescriptionFee = $prescription->payment_amount ?? $this->getDefaultPrescriptionFee();
        
        // If payment_amount is not set, set it now
        if (!$prescription->payment_amount) {
            $prescription->payment_amount = $prescriptionFee;
            $prescription->save();
        }
        
        return view('website.prescription.payment', compact(
            'prescription',
            'setting',
            'currency',
            'currencyCode',
            'prescriptionFee'
        ));
    }
    
    /**
     * Create Stripe Checkout Session for prescription payment.
     */
    public function createCheckoutSession(Request $request, $id)
    {
        $prescription = Prescription::with(['doctor.user', 'user'])->findOrFail($id);
        
        // Verify ownership
        if ($prescription->user_id != auth()->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if payment is required
        if ($prescription->status !== 'approved_pending_payment') {
            return response()->json(['error' => 'Payment is not required for this prescription'], 400);
        }
        
        // Check if already paid
        if ($prescription->payment_status == 1) {
            return response()->json(['error' => 'This prescription has already been paid'], 400);
        }
        
        $setting = Setting::first();
        $stripeSecretKey = $setting->stripe_secret_key;
        
        if (empty($stripeSecretKey)) {
            return response()->json(['error' => 'Stripe is not configured'], 500);
        }
        
        $currencyCode = strtolower($setting->currency_code ?? 'USD');
        $prescriptionFee = $prescription->payment_amount ?? $this->getDefaultPrescriptionFee();
        
        // Convert to cents for Stripe (for most currencies)
        $amountInCents = $this->convertToCents($prescriptionFee, $currencyCode);
        
        try {
            $stripe = new StripeClient($stripeSecretKey);
            
            // Get doctor name for line item description
            $doctorName = $prescription->doctor && $prescription->doctor->user 
                ? $prescription->doctor->user->name 
                : 'Doctor';
            
            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currencyCode,
                        'product_data' => [
                            'name' => __('Prescription Fee'),
                            'description' => __('Prescription by :doctor', ['doctor' => $doctorName]),
                        ],
                        'unit_amount' => $amountInCents,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url('/prescription/payment/success/' . $prescription->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => url('/prescription/payment/cancel/' . $prescription->id),
                'metadata' => [
                    'prescription_id' => $prescription->id,
                    'user_id' => $prescription->user_id,
                ],
                'customer_email' => $prescription->user->email ?? null,
            ]);
            
            // Store the session ID in the prescription
            $prescription->stripe_session_id = $session->id;
            $prescription->save();
            
            return response()->json([
                'sessionId' => $session->id,
                'url' => $session->url,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Stripe Checkout Session creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create payment session: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Process direct card payment using Stripe token (legacy method).
     */
    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'stripeToken' => 'required|string',
        ]);
        
        $prescription = Prescription::with(['doctor.user', 'user'])->findOrFail($id);
        
        // Verify ownership
        if ($prescription->user_id != auth()->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if payment is required
        if ($prescription->status !== 'approved_pending_payment') {
            return response()->json(['error' => 'Payment is not required for this prescription'], 400);
        }
        
        // Check if already paid
        if ($prescription->payment_status == 1) {
            return response()->json(['error' => 'This prescription has already been paid'], 400);
        }
        
        $setting = Setting::first();
        $stripeSecretKey = $setting->stripe_secret_key;
        
        if (empty($stripeSecretKey)) {
            return response()->json(['error' => 'Stripe is not configured'], 500);
        }
        
        $currencyCode = strtolower($setting->currency_code ?? 'USD');
        $prescriptionFee = $prescription->payment_amount ?? $this->getDefaultPrescriptionFee();
        
        // Convert to cents for Stripe
        $amountInCents = $this->convertToCents($prescriptionFee, $currencyCode);
        
        try {
            $stripe = new StripeClient($stripeSecretKey);
            
            $charge = $stripe->charges->create([
                'amount' => $amountInCents,
                'currency' => $currencyCode,
                'source' => $request->stripeToken,
                'description' => __('Prescription Payment - ID: :id', ['id' => $prescription->id]),
                'metadata' => [
                    'prescription_id' => $prescription->id,
                    'user_id' => $prescription->user_id,
                ],
            ]);
            
            // Update prescription with payment info
            $this->markPrescriptionAsPaid($prescription, $charge->id, 'STRIPE');
            
            return response()->json([
                'success' => true,
                'message' => __('Payment successful'),
                'charge_id' => $charge->id,
            ]);
            
        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Stripe card error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            Log::error('Stripe payment failed: ' . $e->getMessage());
            return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Handle successful payment redirect from Stripe Checkout.
     */
    public function paymentSuccess(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);
        
        // Verify ownership
        if ($prescription->user_id != auth()->user()->id) {
            abort(403, 'Unauthorized');
        }
        
        $sessionId = $request->query('session_id');
        
        // Verify the payment with Stripe if session_id is provided
        if ($sessionId && $prescription->stripe_session_id === $sessionId) {
            $setting = Setting::first();
            $stripeSecretKey = $setting->stripe_secret_key;
            
            if ($stripeSecretKey) {
                try {
                    $stripe = new StripeClient($stripeSecretKey);
                    $session = $stripe->checkout->sessions->retrieve($sessionId);
                    
                    if ($session->payment_status === 'paid') {
                        // Mark prescription as paid
                        $this->markPrescriptionAsPaid($prescription, $session->payment_intent, 'STRIPE_CHECKOUT');
                    }
                } catch (\Exception $e) {
                    Log::error('Error verifying Stripe session: ' . $e->getMessage());
                }
            }
        }
        
        return redirect(url('/user_profile'))->with('success', __('Payment successful! You can now download your prescription.'));
    }
    
    /**
     * Handle cancelled payment redirect from Stripe Checkout.
     */
    public function paymentCancel($id)
    {
        return redirect(url('/user_profile'))->with('error', __('Payment was cancelled. Please try again.'));
    }
    
    /**
     * Handle Stripe webhook for payment confirmation.
     */
    public function handleWebhook(Request $request)
    {
        $setting = Setting::first();
        $webhookSecret = $setting->stripe_webhook_secret ?? null;
        
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        
        try {
            if ($webhookSecret) {
                // Verify webhook signature
                $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } else {
                // If no webhook secret configured, parse the event directly (less secure)
                $event = json_decode($payload, false, 512, JSON_THROW_ON_ERROR);
            }
            
            // Handle the event
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $this->handleCheckoutSessionCompleted($session);
                    break;
                    
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $this->handlePaymentIntentSucceeded($paymentIntent);
                    break;
                    
                default:
                    // Unexpected event type
                    Log::info('Unhandled Stripe webhook event: ' . $event->type);
            }
            
            return response()->json(['status' => 'success']);
            
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook handler failed'], 500);
        }
    }
    
    /**
     * Handle checkout.session.completed webhook event.
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        $prescriptionId = $session->metadata->prescription_id ?? null;
        
        if (!$prescriptionId) {
            Log::warning('Checkout session completed without prescription_id in metadata');
            return;
        }
        
        $prescription = Prescription::find($prescriptionId);
        
        if (!$prescription) {
            Log::warning('Prescription not found for checkout session: ' . $prescriptionId);
            return;
        }
        
        if ($prescription->payment_status == 1) {
            // Already paid, skip
            return;
        }
        
        if ($session->payment_status === 'paid') {
            $this->markPrescriptionAsPaid($prescription, $session->payment_intent, 'STRIPE_CHECKOUT');
            Log::info('Prescription payment confirmed via webhook: ' . $prescriptionId);
        }
    }
    
    /**
     * Handle payment_intent.succeeded webhook event.
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        $prescriptionId = $paymentIntent->metadata->prescription_id ?? null;
        
        if (!$prescriptionId) {
            // Not a prescription payment
            return;
        }
        
        $prescription = Prescription::find($prescriptionId);
        
        if (!$prescription) {
            Log::warning('Prescription not found for payment intent: ' . $prescriptionId);
            return;
        }
        
        if ($prescription->payment_status == 1) {
            // Already paid, skip
            return;
        }
        
        $this->markPrescriptionAsPaid($prescription, $paymentIntent->id, 'STRIPE');
        Log::info('Prescription payment confirmed via payment_intent webhook: ' . $prescriptionId);
    }
    
    /**
     * Mark a prescription as paid and update its status.
     */
    protected function markPrescriptionAsPaid(Prescription $prescription, string $paymentToken, string $paymentMethod): void
    {
        DB::transaction(function () use ($prescription, $paymentToken, $paymentMethod) {
            $prescription->payment_status = 1;
            $prescription->payment_token = $paymentToken;
            $prescription->payment_method = $paymentMethod;
            $prescription->payment_date = now();
            $prescription->status = 'active'; // Change status to active after payment
            
            // Set validity period if not already set
            if (!$prescription->valid_from) {
                $prescription->valid_from = now();
            }
            if (!$prescription->valid_until && $prescription->validity_days) {
                $prescription->valid_until = now()->addDays($prescription->validity_days);
            } elseif (!$prescription->valid_until) {
                // Default validity: 30 days
                $prescription->validity_days = 30;
                $prescription->valid_until = now()->addDays(30);
            }
            
            $prescription->save();
            
            // Generate PDF if not already generated
            if (!$prescription->pdf) {
                $this->generatePrescriptionPdf($prescription);
            }
        });
    }
    
    /**
     * Generate PDF for the prescription.
     */
    protected function generatePrescriptionPdf(Prescription $prescription): void
    {
        try {
            $prescription->load(['doctor.user', 'user']);
            
            $medicines = json_decode($prescription->medicines, true) ?? [];
            $doctorName = $prescription->doctor && $prescription->doctor->user 
                ? $prescription->doctor->user->name 
                : 'Doctor';
            $patientName = $prescription->user ? $prescription->user->name : 'Patient';
            
            $pdf = \PDF::loadView('prescription_pdf', [
                'prescription' => $prescription,
                'medicines' => $medicines,
                'doctor_name' => $doctorName,
                'patient_name' => $patientName,
                'valid_from' => $prescription->valid_from ? $prescription->valid_from->format('d M Y') : now()->format('d M Y'),
                'valid_until' => $prescription->valid_until ? $prescription->valid_until->format('d M Y') : null,
            ]);
            
            $fileName = 'prescription_' . $prescription->id . '_' . time() . '.pdf';
            $path = public_path('prescription/upload/' . $fileName);
            
            // Ensure directory exists
            if (!file_exists(public_path('prescription/upload'))) {
                mkdir(public_path('prescription/upload'), 0755, true);
            }
            
            $pdf->save($path);
            
            $prescription->pdf = $fileName;
            $prescription->save();
            
        } catch (\Exception $e) {
            Log::error('Failed to generate prescription PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Convert amount to cents for Stripe based on currency.
     * 
     * Most currencies use cents (USD, EUR, etc.), but some don't (JPY).
     */
    protected function convertToCents(float $amount, string $currency): int
    {
        // Zero-decimal currencies (no cents)
        $zeroDecimalCurrencies = ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf'];
        
        if (in_array(strtolower($currency), $zeroDecimalCurrencies)) {
            return (int) $amount;
        }
        
        return (int) round($amount * 100);
    }
}
