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
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function cannaleoMedicines()
    {
        return $this->hasMany(CannaleoMedicine::class, 'cannaleo_pharmacy_id');
    }
}
