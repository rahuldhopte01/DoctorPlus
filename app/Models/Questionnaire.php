<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use HasFactory;

    protected $table = 'questionnaires';

    protected $fillable = [
        'treatment_id',
        'name',
        'description',
        'status',
        'version',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the treatment that owns the questionnaire.
     */
    public function treatment()
    {
        return $this->belongsTo(Treatments::class, 'treatment_id');
    }

    /**
     * Get the sections for the questionnaire.
     */
    public function sections()
    {
        return $this->hasMany(QuestionnaireSection::class)->orderBy('order');
    }

    /**
     * Get all questions through sections.
     */
    public function questions()
    {
        return $this->hasManyThrough(
            QuestionnaireQuestion::class,
            QuestionnaireSection::class,
            'questionnaire_id',
            'section_id'
        );
    }

    /**
     * Get appointments using this questionnaire.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Scope to get only active questionnaires.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get questionnaire with all nested data for rendering.
     */
    public function getFullStructure()
    {
        return $this->load(['sections' => function ($query) {
            $query->orderBy('order')->with(['questions' => function ($q) {
                $q->orderBy('order');
            }]);
        }]);
    }
}

