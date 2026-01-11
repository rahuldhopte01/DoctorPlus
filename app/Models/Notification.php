<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';

    protected $fillable = ['user_id', 'doctor_id', 'pharmacy_id', 'pharmacy_inventory_id', 'title', 'user_type', 'message', 'notification_type'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Models\Doctor');
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class, 'pharmacy_id');
    }

    // public function pharmacyInventory()
    // {
    //     return $this->belongsTo(PharmacyInventory::class, 'pharmacy_inventory_id');
    // }

    /**
     * Notification type constants.
     */
    public const TYPE_LOW_STOCK = 'low_stock';
}
