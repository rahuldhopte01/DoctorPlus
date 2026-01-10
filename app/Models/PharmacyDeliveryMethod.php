<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyDeliveryMethod extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_delivery_methods';

    protected $fillable = [
        'pharmacy_id',
        'delivery_method',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Delivery method constants.
     */
    public const METHOD_STANDARD = 'standard';
    public const METHOD_EXPRESS = 'express';
    public const METHOD_SAME_DAY = 'same_day';

    /**
     * Get the pharmacy that owns this delivery method.
     */
    public function pharmacy()
    {
        return $this->belongsTo(PharmacyRegistration::class, 'pharmacy_id');
    }

    /**
     * Scope to filter active methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
