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
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

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
        return $this->status === 'approved_pending_payment';
    }

    /**
     * Check if prescription can be downloaded.
     */
    public function canBeDownloaded(): bool
    {
        return in_array($this->status, ['active', 'approved']) && $this->isValid();
    }
}
