<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $table = 'medicine';

    // Updated fillable - removed pharmacy-specific fields, kept global fields
    protected $fillable = [
        'name',
        'strength',
        'form',
        'brand_id',
        'status',
        'description',
    ];

    /**
     * Get the brand for this medicine.
     */
    public function brand()
    {
        return $this->belongsTo(MedicineBrand::class, 'brand_id');
    }

    /**
     * Get pharmacy inventory entries for this medicine.
     */
    public function pharmacyInventories()
    {
        return $this->hasMany(PharmacyInventory::class);
    }


    /**
     * Scope to get only active medicines.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
