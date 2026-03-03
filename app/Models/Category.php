<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';

    protected $fillable = ['name', 'description', 'image', 'treatment_id', 'price', 'status', 'is_cannaleo_only'];

    protected $appends = ['fullImage'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_cannaleo_only' => 'boolean',
    ];

    protected function getFullImageAttribute()
    {
        return url('images/upload').'/'.$this->image;
    }

    public function treatment()
    {
        return $this->belongsTo('App\Models\Treatments');
    }

    public function expertise()
    {
        return $this->hasOne('App\Models\Expertise');
    }

    public function doctors()
    {
        return $this->belongsToMany('App\Models\Doctor', 'doctor_category', 'category_id', 'doctor_id');
    }

    // Keep backward compatibility
    public function doctor()
    {
        return $this->belongsToMany('App\Models\Doctor', 'doctor_category', 'category_id', 'doctor_id');
    }

    /**
     * Get the questionnaire for this category.
     */
    public function questionnaire()
    {
        return $this->hasOne(Questionnaire::class, 'category_id');
    }

    /**
     * Check if category has an active questionnaire.
     */
    public function hasActiveQuestionnaire(): bool
    {
        return $this->questionnaire()->where('status', 1)->exists();
    }

    /**
     * Medicines available for this category (questionnaire medicine selection).
     */
    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'category_medicine', 'category_id', 'medicine_id')
            ->withTimestamps();
    }

    /**
     * Cannaleo (API) medicines assigned to this category (questionnaire medicine selection).
     */
    public function cannaleoMedicines()
    {
        return $this->belongsToMany(CannaleoMedicine::class, 'category_cannaleo_medicine', 'category_id', 'cannaleo_medicine_id')
            ->withTimestamps();
    }
}
