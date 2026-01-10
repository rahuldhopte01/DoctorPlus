<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Events\Saving;

class PharmacyInventory extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_inventory';

    protected $fillable = [
        'pharmacy_id',
        'medicine_id',
        'medicine_brand_id',
        'price',
        'quantity',
        'low_stock_threshold',
        'stock_status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    /**
     * Stock status constants.
     */
    public const STATUS_IN_STOCK = 'in_stock';
    public const STATUS_LOW_STOCK = 'low_stock';
    public const STATUS_OUT_OF_STOCK = 'out_of_stock';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($inventory) {
            $inventory->updateStockStatus();
        });
    }

    /**
     * Get the pharmacy that owns this inventory item.
     */
    public function pharmacy()
    {
        return $this->belongsTo(PharmacyRegistration::class, 'pharmacy_id');
    }

    /**
     * Get the medicine.
     */
    public function medicine()
    {
        return $this->belongsTo(MedicineMaster::class, 'medicine_id');
    }

    /**
     * Get the medicine brand.
     */
    public function brand()
    {
        return $this->belongsTo(MedicineBrand::class, 'medicine_brand_id');
    }

    /**
     * Update stock status based on quantity and threshold.
     */
    public function updateStockStatus()
    {
        if ($this->quantity <= 0) {
            $this->stock_status = self::STATUS_OUT_OF_STOCK;
        } elseif ($this->quantity <= $this->low_stock_threshold) {
            $this->stock_status = self::STATUS_LOW_STOCK;
        } else {
            $this->stock_status = self::STATUS_IN_STOCK;
        }
    }

    /**
     * Scope to filter by stock status.
     */
    public function scopeStockStatus($query, $status)
    {
        return $query->where('stock_status', $status);
    }

    /**
     * Scope to filter low stock items.
     */
    public function scopeLowStock($query)
    {
        return $query->where('stock_status', self::STATUS_LOW_STOCK)
            ->orWhere(function ($q) {
                $q->whereColumn('quantity', '<=', 'low_stock_threshold')
                  ->where('quantity', '>', 0);
            });
    }

    /**
     * Scope to filter out of stock items.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock_status', self::STATUS_OUT_OF_STOCK)
            ->orWhere('quantity', '<=', 0);
    }

    /**
     * Scope to filter in stock items.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_status', self::STATUS_IN_STOCK)
            ->where('quantity', '>', 0);
    }
}
