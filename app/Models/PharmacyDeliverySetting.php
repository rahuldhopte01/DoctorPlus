<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyDeliverySetting extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_delivery_settings';

    protected $fillable = [
        'pharmacy_id',
        'delivery_type',
        'delivery_radius',
    ];

    protected $casts = [
        'delivery_radius' => 'decimal:2',
    ];

    /**
     * Delivery type constants.
     */
    public const DELIVERY_TYPE_PICKUP_ONLY = 'pickup_only';
    public const DELIVERY_TYPE_DELIVERY_ONLY = 'delivery_only';
    public const DELIVERY_TYPE_PICKUP_DELIVERY = 'pickup_delivery';

    /**
     * Get the pharmacy that owns this delivery setting.
     */
    public function pharmacy()
    {
        return $this->belongsTo(PharmacyRegistration::class, 'pharmacy_id');
    }

    /**
     * Check if pickup is enabled.
     */
    public function isPickupEnabled()
    {
        return in_array($this->delivery_type, [
            self::DELIVERY_TYPE_PICKUP_ONLY,
            self::DELIVERY_TYPE_PICKUP_DELIVERY,
        ]);
    }

    /**
     * Check if delivery is enabled.
     */
    public function isDeliveryEnabled()
    {
        return in_array($this->delivery_type, [
            self::DELIVERY_TYPE_DELIVERY_ONLY,
            self::DELIVERY_TYPE_PICKUP_DELIVERY,
        ]);
    }
}
