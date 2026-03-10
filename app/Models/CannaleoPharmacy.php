<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CannaleoPharmacy extends Model
{
    use HasFactory;

    protected $table = 'cannaleo_pharmacy';

    protected $fillable = [
        'external_id',
        'name',
        'domain',
        'email',
        'phone_number',
        'street',
        'plz',
        'city',
        'shipping',
        'shipping_cost_standard',
        'shipping_cost_reduced',
        'express',
        'express_cost_standard',
        'express_cost_reduced',
        'local_courier',
        'local_courier_cost_standard',
        'local_courier_cost_reduced',
        'pickup',
        'pickup_branches',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'shipping_cost_standard' => 'float',
        'express_cost_standard' => 'float',
        'local_courier_cost_standard' => 'float',
        'shipping_cost_reduced' => 'array',
        'express_cost_reduced' => 'array',
        'local_courier_cost_reduced' => 'array',
        'pickup_branches' => 'array',
    ];

    public function cannaleoMedicines()
    {
        return $this->hasMany(CannaleoMedicine::class, 'cannaleo_pharmacy_id');
    }
}
