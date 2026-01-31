<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctor';

    protected $fillable = ['name', 'is_filled', 'custom_timeslot', 'dob', 'gender', 'expertise_id', 'timeslot', 'start_time', 'end_time', 'hospital_id', 'doctor_role', 'image', 'user_id', 'desc', 'education', 'certificate', 'appointment_fees', 'experience', 'since', 'status', 'based_on', 'commission_amount', 'is_popular', 'subscription_status', 'language'];

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

    /**
     * Get the hospital that this doctor belongs to.
     */
    public function hospital()
    {
        return $this->belongsTo('App\Models\Hospital', 'hospital_id');
    }

    /**
     * Get questionnaire answers being reviewed by this doctor.
     */
    public function reviewingQuestionnaireAnswers()
    {
        return $this->hasMany('App\Models\QuestionnaireAnswer', 'reviewing_doctor_id');
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

    /**
     * Check if doctor is an admin doctor.
     */
    public function isAdminDoctor(): bool
    {
        return $this->doctor_role === 'ADMIN_DOCTOR';
    }

    /**
     * Check if doctor is a sub doctor.
     */
    public function isSubDoctor(): bool
    {
        return $this->doctor_role === 'SUB_DOCTOR';
    }

    /**
     * Scope to get only admin doctors.
     */
    public function scopeAdminDoctors($query)
    {
        return $query->where('doctor_role', 'ADMIN_DOCTOR');
    }

    /**
     * Scope to get only sub doctors.
     */
    public function scopeSubDoctors($query)
    {
        return $query->where('doctor_role', 'SUB_DOCTOR');
    }

    /**
     * Scope to get doctors by hospital.
     */
    public function scopeByHospital($query, $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }

    /**
     * Get hospital IDs as array (for backward compatibility with legacy comma-separated format).
     * Returns array of hospital IDs even if single value.
     */
    public function getHospitalIdsAttribute()
    {
        if ($this->hospital_id === null) {
            return [];
        }
        
        // Handle both old format (comma-separated string) and new format (single integer)
        if (is_string($this->hospital_id) && strpos($this->hospital_id, ',') !== false) {
            // Legacy: comma-separated string
            return array_filter(array_map('intval', explode(',', $this->hospital_id)));
        } else {
            // New format: single integer
            return [$this->hospital_id];
        }
    }
}
