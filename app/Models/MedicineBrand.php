<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineBrand extends Model
{
    use HasFactory;

    protected $table = 'medicine_brands';

    protected $fillable = ['name'];

    /**
     * Get the medicines that belong to this brand.
     */
    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'brand_id');
    }
}
