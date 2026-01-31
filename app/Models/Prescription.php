<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'prescription';

    protected $fillable = [
        'appointment_id',
        'medicines',
        'doctor_id',
        'user_id',
        'pdf',
        'status',
        'valid_from',
        'valid_until',
        'validity_days',
        'payment_amount',
        'payment_status',
        'payment_token',
        'payment_method',
        'payment_date',
        'stripe_session_id',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'payment_date' => 'datetime',
        'payment_status' => 'boolean',
        'payment_amount' => 'decimal:2',
    ];

    /**
     * Default prescription fee
     */
    const DEFAULT_FEE = 50.00;

    public function doctor()
    {
        return $this->belongsTo('App\Models\Doctor');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function appointment()
    {
        return $this->belongsTo('App\Models\Appointment');
    }

    /**
     * Check if prescription is valid (not expired).
     */
    public function isValid(): bool
    {
        if (!$this->valid_until) {
            return true; // If no expiry, consider valid
        }
        return Carbon::now()->lte($this->valid_until);
    }

    /**
     * Check if prescription has expired.
     */
    public function isExpired(): bool
    {
        return !$this->isValid();
    }

    /**
     * Check if payment is required.
     */
    public function requiresPayment(): bool
    {
        return $this->status === 'approved_pending_payment' && !$this->isPaid();
    }

    /**
     * Check if prescription has been paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status == 1;
    }

    /**
     * Check if prescription can be downloaded.
     */
    public function canBeDownloaded(): bool
    {
        // Must be active/approved AND not expired AND paid (if payment was required)
        if ($this->status === 'approved_pending_payment') {
            return false; // Not paid yet
        }
        
        return in_array($this->status, ['active', 'approved']) && $this->isValid();
    }

    /**
     * Check if prescription can be viewed (shows info but not download).
     */
    public function canBeViewed(): bool
    {
        // Can view if paid or if status is active/approved
        if ($this->status === 'approved_pending_payment') {
            return false; // Cannot view until paid
        }
        
        return in_array($this->status, ['active', 'approved']);
    }

    /**
     * Get the payment amount or default fee.
     */
    public function getPaymentFee(): float
    {
        return $this->payment_amount ?? self::DEFAULT_FEE;
    }

    /**
     * Get formatted payment amount with currency.
     */
    public function getFormattedPaymentAmount(): string
    {
        $setting = \App\Models\Setting::first();
        $currency = $setting->currency_symbol ?? '$';
        return $currency . number_format($this->getPaymentFee(), 2);
    }

    /**
     * Get status label for display.
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'approved_pending_payment' => __('Pending Payment'),
            'active' => __('Active'),
            'approved' => __('Approved'),
            'expired' => __('Expired'),
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status badge class for display.
     */
    public function getStatusBadgeClass(): string
    {
        $classes = [
            'approved_pending_payment' => 'badge-warning',
            'active' => 'badge-success',
            'approved' => 'badge-info',
            'expired' => 'badge-danger',
        ];

        return $classes[$this->status] ?? 'badge-secondary';
    }

    /**
     * Get remaining days until expiry.
     */
    public function getRemainingDays(): ?int
    {
        if (!$this->valid_until) {
            return null;
        }

        $now = Carbon::now();
        if ($now->gt($this->valid_until)) {
            return 0;
        }

        return $now->diffInDays($this->valid_until);
    }
}
