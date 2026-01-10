<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineMaster extends Model
{
    use HasFactory;

    protected $table = 'medicines';

    protected $fillable = [
        'name',
        'strength',
        'form',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get all brands for this medicine.
     */
    public function brands()
    {
        return $this->hasMany(MedicineBrand::class, 'medicine_id');
    }

    /**
     * Get active brands only.
     */
    public function activeBrands()
    {
        return $this->hasMany(MedicineBrand::class, 'medicine_id')->where('status', true);
    }

    /**
     * Get inventory items across all pharmacies for this medicine.
     */
    public function inventory()
    {
        return $this->hasManyThrough(
            PharmacyInventory::class,
            MedicineBrand::class,
            'medicine_id',
            'medicine_brand_id',
            'id',
            'id'
        );
    }

    /**
     * Scope to filter active medicines.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
