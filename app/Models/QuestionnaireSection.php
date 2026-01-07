<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireSection extends Model
{
    use HasFactory;

    protected $table = 'questionnaire_sections';

    protected $fillable = [
        'questionnaire_id',
        'name',
        'description',
        'order',
    ];

    /**
     * Get the questionnaire that owns the section.
     */
    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    /**
     * Get the questions for the section.
     */
    public function questions()
    {
        return $this->hasMany(QuestionnaireQuestion::class, 'section_id')->orderBy('order');
    }
}

