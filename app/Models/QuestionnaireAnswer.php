<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireAnswer extends Model
{
    use HasFactory;

    protected $table = 'questionnaire_answers';

    protected $fillable = [
        'appointment_id',
        'question_id',
        'questionnaire_version',
        'answer_value',
        'file_path',
        'is_flagged',
        'flag_reason',
    ];

    protected $casts = [
        'is_flagged' => 'boolean',
    ];

    protected $appends = ['display_value', 'full_file_url'];

    /**
     * Get the appointment that owns the answer.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the question that this answer belongs to.
     */
    public function question()
    {
        return $this->belongsTo(QuestionnaireQuestion::class, 'question_id');
    }

    /**
     * Get display-friendly answer value.
     */
    public function getDisplayValueAttribute()
    {
        if (empty($this->answer_value)) {
            return 'Not answered';
        }

        // Try to decode JSON for checkbox answers
        $decoded = json_decode($this->answer_value, true);
        if (is_array($decoded)) {
            return implode(', ', $decoded);
        }

        return $this->answer_value;
    }

    /**
     * Get full file URL for file uploads.
     */
    public function getFullFileUrlAttribute()
    {
        if (empty($this->file_path)) {
            return null;
        }
        return url('questionnaire_uploads/' . $this->file_path);
    }

    /**
     * Boot method to prevent modification of locked answers.
     */
    protected static function booted()
    {
        static::updating(function ($answer) {
            if ($answer->appointment && $answer->appointment->questionnaire_locked) {
                throw new \Exception('Questionnaire answers cannot be modified after doctor decision');
            }
        });

        static::deleting(function ($answer) {
            if ($answer->appointment && $answer->appointment->questionnaire_locked) {
                throw new \Exception('Questionnaire answers cannot be deleted after doctor decision');
            }
        });
    }

    /**
     * Scope to get flagged answers.
     */
    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }
}


