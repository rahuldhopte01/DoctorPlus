<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineBrand extends Model
{
    use HasFactory;

    protected $table = 'medicine_brands';

    protected $fillable = [
        'medicine_id',
        'brand_name',
        'strength',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the medicine that owns this brand.
     */
    public function medicine()
    {
        return $this->belongsTo(MedicineMaster::class, 'medicine_id');
    }

    /**
     * Get inventory items for this brand across all pharmacies.
     */
    public function inventory()
    {
        return $this->hasMany(PharmacyInventory::class, 'medicine_brand_id');
    }

    /**
     * Scope to filter active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
