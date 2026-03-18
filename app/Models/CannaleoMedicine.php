<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CannaleoMedicine extends Model
{
    use HasFactory;

    protected $table = 'cannaleo_medicine';

    protected $fillable = [
        'cannaleo_pharmacy_id',
        'external_id',
        'ansay_id',
        'name',
        'category',
        'is_api_medicine',
        'price',
        'thc',
        'cbd',
        'genetic',
        'strain',
        'country',
        'manufacturer',
        'grower',
        'availability',
        'irradiated',
        'terpenes',
        'raw_data',
        'last_synced_at',
        'image',
        'description',
    ];

    protected $casts = [
        'is_api_medicine' => 'boolean',
        'price' => 'decimal:2',
        'thc' => 'decimal:2',
        'cbd' => 'decimal:2',
        'terpenes' => 'array',
        'raw_data' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function cannaleoPharmacy()
    {
        return $this->belongsTo(CannaleoPharmacy::class, 'cannaleo_pharmacy_id');
    }

    /**
     * Categories this Cannaleo medicine is assigned to (questionnaire flow).
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_cannaleo_medicine', 'cannaleo_medicine_id', 'category_id')
            ->withTimestamps();
    }
}
