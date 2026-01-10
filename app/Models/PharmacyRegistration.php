<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyRegistration extends Model
{
    use HasFactory;

    protected $table = 'pharmacies';

    protected $fillable = [
        'name',
        'owner_user_id',
        'email',
        'phone',
        'address',
        'postcode',
        'latitude',
        'longitude',
        'is_priority',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_priority' => 'boolean',
    ];

    /**
     * Get the owner/admin user.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * Get delivery settings for this pharmacy.
     */
    public function deliverySettings()
    {
        return $this->hasOne(PharmacyDeliverySetting::class, 'pharmacy_id');
    }

    /**
     * Get delivery methods for this pharmacy.
     */
    public function deliveryMethods()
    {
        return $this->hasMany(PharmacyDeliveryMethod::class, 'pharmacy_id');
    }

    /**
     * Get inventory items for this pharmacy.
     */
    public function inventory()
    {
        return $this->hasMany(PharmacyInventory::class, 'pharmacy_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter priority pharmacies.
     */
    public function scopePriority($query)
    {
        return $query->where('is_priority', true);
    }

    /**
     * Scope to filter approved pharmacies.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
