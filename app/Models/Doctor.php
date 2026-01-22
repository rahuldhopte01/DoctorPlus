<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctor';

    protected $fillable = ['name', 'is_filled', 'custom_timeslot', 'dob', 'gender', 'expertise_id', 'timeslot', 'start_time', 'end_time', 'hospital_id', 'image', 'user_id', 'desc', 'education', 'certificate', 'appointment_fees', 'experience', 'since', 'status', 'based_on', 'commission_amount', 'is_popular', 'subscription_status', 'language'];

    protected $appends = ['fullImage', 'rate', 'review', 'treatment_id', 'category_id'];

    protected function getFullImageAttribute()
    {
        return url('images/upload').'/'.$this->image;
    }

    public function expertise()
    {
        return $this->belongsTo('App\Models\Expertise');
    }

    public function treatments()
    {
        return $this->belongsToMany('App\Models\Treatments', 'doctor_treatment', 'doctor_id', 'treatment_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'doctor_category', 'doctor_id', 'category_id');
    }

    public function DoctorSubscription()
    {
        return $this->hasOne('App\Models\DoctorSubscription');
    }

    public function Doctor()
    {
        return $this->hasOne('App\Models\Doctor');
    }

    public function getRateAttribute()
    {
        $review = Review::where('doctor_id', $this->attributes['id'])->get();
        if (count($review) > 0) {
            $totalRate = 0;
            foreach ($review as $r) {
                $totalRate = $totalRate + $r->rate;
            }

            return round($totalRate / count($review), 1);
        } else {
            return 0;
        }
    }

    public function getReviewAttribute()
    {
        return Review::where('doctor_id', $this->attributes['id'])->count();
    }

    // Backward compatibility accessors
    public function getTreatmentIdAttribute()
    {
        // Only return if not already loaded as relationship
        if (array_key_exists('treatment_id', $this->attributes)) {
            return $this->attributes['treatment_id'];
        }
        return $this->treatments->first()?->id;
    }

    public function getCategoryIdAttribute()
    {
        // Only return if not already loaded as relationship
        if (array_key_exists('category_id', $this->attributes)) {
            return $this->attributes['category_id'];
        }
        return $this->categories->first()?->id;
    }

    // Accessor methods for backward compatibility
    public function getTreatmentAttribute()
    {
        return $this->treatments->first();
    }

    public function getCategoryAttribute()
    {
        return $this->categories->first();
    }
}
