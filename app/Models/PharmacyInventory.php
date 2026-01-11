<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyInventory extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_inventory';

    protected $fillable = [
        'pharmacy_id',
        'medicine_id',
        'brand_id',
        'price',
        'quantity',
        'low_stock_threshold',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    /**
     * Get the pharmacy that owns this inventory.
     */
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    /**
     * Get the medicine for this inventory.
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get the brand for this inventory.
     */
    public function brand()
    {
        return $this->belongsTo(MedicineBrand::class, 'brand_id');
    }

    /**
     * Get stock status (derived, not stored).
     * Returns: 'in_stock', 'low_stock', or 'out_of_stock'
     */
    public function getStockStatusAttribute()
    {
        if ($this->quantity == 0) {
            return 'out_of_stock';
        }
        if ($this->quantity <= $this->low_stock_threshold) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    /**
     * Check if stock is low.
     */
    public function isLowStock(): bool
    {
        return $this->quantity > 0 && $this->quantity <= $this->low_stock_threshold;
    }

    /**
     * Check if out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->quantity == 0;
    }
}
