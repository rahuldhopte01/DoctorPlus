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
        'option_behaviors',
        'doctor_notes',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'conditional_logic' => 'array',
        'flagging_rules' => 'array',
        'option_behaviors' => 'array',
        'required' => 'boolean',
    ];

    public const FIELD_TYPES = [
        'text' => 'Text Input',
        'textarea' => 'Text Area',
        'number' => 'Number',
        'dropdown' => 'Dropdown Select',
        'radio' => 'Radio Buttons',
        'checkbox' => 'Checkboxes',
        'file' => 'File Upload',
    ];

    public function section()
    {
        return $this->belongsTo(QuestionnaireSection::class, 'section_id');
    }

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

    public function answers()
    {
        return $this->hasMany(QuestionnaireAnswer::class, 'question_id');
    }

    public function requiresOptions(): bool
    {
        return in_array($this->field_type, ['dropdown', 'radio', 'checkbox']);
    }

    public function getOptionsArray(): array
    {
        if (is_array($this->options)) {
            return $this->options;
        }
        return [];
    }

    /**
     * Find the behavior entry that matches a given answer value.
     * Returns ['condition'=>..., 'flags'=>[...], 'sub_question'=>...] or null.
     */
    public function getMatchingBehavior($answerValue): ?array
    {
        $behaviors = $this->option_behaviors['behaviors'] ?? [];
        foreach ($behaviors as $behavior) {
            if ($this->evaluateCondition($answerValue, $behavior['condition'] ?? [])) {
                return $behavior;
            }
        }
        return null;
    }

    /**
     * Return all flags triggered by the given answer value.
     * Each element: ['flag_type' => 'soft'|'hard', 'flag_message' => string]
     */
    public function evaluateBehaviorsForValue($answerValue): array
    {
        $behavior = $this->getMatchingBehavior($answerValue);
        return $behavior['flags'] ?? [];
    }

    /**
     * Evaluate a single condition array against an answer value.
     */
    public static function evaluateCondition($answerValue, array $condition): bool
    {
        $operator = $condition['operator'] ?? 'equals';
        $value    = $condition['value'] ?? null;

        switch ($operator) {
            case 'equals':       return ($answerValue == $value);
            case 'not_equals':   return ($answerValue != $value);
            case 'contains':     return (stripos((string) $answerValue, (string) $value) !== false);
            case 'greater_than': return (is_numeric($answerValue) && $answerValue > $value);
            case 'less_than':    return (is_numeric($answerValue) && $answerValue < $value);
            case 'in':           return in_array($answerValue, (array) $value);
            default:             return false;
        }
    }

    /**
     * Legacy flag evaluation — kept for backward compatibility with existing flagging_rules data.
     * New questions use option_behaviors; this still works for old records.
     */
    public function evaluateFlag($answerValue): ?array
    {
        // Use new system if available
        if (!empty($this->option_behaviors['behaviors'])) {
            $flags = $this->evaluateBehaviorsForValue($answerValue);
            if (!empty($flags)) {
                return ['flag_type' => $flags[0]['flag_type'], 'flag_message' => $flags[0]['flag_message']];
            }
            return null;
        }

        // Fall back to legacy flagging_rules
        if (empty($this->flagging_rules)) {
            return null;
        }

        $rules      = $this->flagging_rules;
        $flagType   = $rules['flag_type'] ?? 'soft';
        $conditions = $rules['conditions'] ?? [];

        foreach ($conditions as $condition) {
            if (self::evaluateCondition($answerValue, $condition)) {
                return [
                    'flag_type'    => $flagType,
                    'flag_message' => $condition['flag_message'] ?? 'Answer flagged for review',
                ];
            }
        }

        return null;
    }
}
