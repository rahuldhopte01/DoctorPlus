<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireQuestion extends Model
{
    use HasFactory;

    protected $table = 'questionnaire_questions';

    protected $fillable = [
        'section_id',
        'question_text',
        'field_type',
        'options',
        'required',
        'validation_rules',
        'conditional_logic',
        'flagging_rules',
        'doctor_notes',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'conditional_logic' => 'array',
        'flagging_rules' => 'array',
        'required' => 'boolean',
    ];

    /**
     * Field type options.
     */
    public const FIELD_TYPES = [
        'text' => 'Text Input',
        'textarea' => 'Text Area',
        'number' => 'Number',
        'dropdown' => 'Dropdown Select',
        'radio' => 'Radio Buttons',
        'checkbox' => 'Checkboxes',
        'file' => 'File Upload',
    ];

    /**
     * Get the section that owns the question.
     */
    public function section()
    {
        return $this->belongsTo(QuestionnaireSection::class, 'section_id');
    }

    /**
     * Get the questionnaire through section.
     */
    public function questionnaire()
    {
        return $this->hasOneThrough(
            Questionnaire::class,
            QuestionnaireSection::class,
            'id',
            'id',
            'section_id',
            'questionnaire_id'
        );
    }

    /**
     * Get the answers for this question.
     */
    public function answers()
    {
        return $this->hasMany(QuestionnaireAnswer::class, 'question_id');
    }

    /**
     * Check if this question requires options (dropdown, radio, checkbox).
     */
    public function requiresOptions(): bool
    {
        return in_array($this->field_type, ['dropdown', 'radio', 'checkbox']);
    }

    /**
     * Get options as array.
     */
    public function getOptionsArray(): array
    {
        if (is_array($this->options)) {
            return $this->options;
        }
        return [];
    }

    /**
     * Evaluate if an answer triggers a flag.
     */
    public function evaluateFlag($answerValue): ?array
    {
        if (empty($this->flagging_rules)) {
            return null;
        }

        $rules = $this->flagging_rules;
        $flagType = $rules['flag_type'] ?? 'soft';
        $conditions = $rules['conditions'] ?? [];

        foreach ($conditions as $condition) {
            $operator = $condition['operator'] ?? 'equals';
            $value = $condition['value'] ?? null;
            $flagMessage = $condition['flag_message'] ?? 'Answer flagged for review';

            $triggered = false;

            switch ($operator) {
                case 'equals':
                    $triggered = ($answerValue == $value);
                    break;
                case 'not_equals':
                    $triggered = ($answerValue != $value);
                    break;
                case 'contains':
                    $triggered = (stripos($answerValue, $value) !== false);
                    break;
                case 'greater_than':
                    $triggered = (is_numeric($answerValue) && $answerValue > $value);
                    break;
                case 'less_than':
                    $triggered = (is_numeric($answerValue) && $answerValue < $value);
                    break;
                case 'in':
                    $triggered = in_array($answerValue, (array) $value);
                    break;
            }

            if ($triggered) {
                return [
                    'flag_type' => $flagType,
                    'flag_message' => $flagMessage,
                ];
            }
        }

        return null;
    }
}

