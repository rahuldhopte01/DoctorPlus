<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Questionnaire;
use App\Models\QuestionnaireAnswer;
use App\Models\QuestionnaireQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QuestionnaireService
{
    /**
     * Get questionnaire for a treatment.
     */
    public function getQuestionnaireForTreatment($treatmentId): ?Questionnaire
    {
        return Questionnaire::where('treatment_id', $treatmentId)
            ->where('status', 1)
            ->with(['sections' => function ($query) {
                $query->orderBy('order')->with(['questions' => function ($q) {
                    $q->orderBy('order');
                }]);
            }])
            ->first();
    }

    /**
     * Validate questionnaire answers.
     */
    public function validateAnswers(Questionnaire $questionnaire, array $answers): array
    {
        $errors = [];
        $questions = $questionnaire->questions;

        foreach ($questions as $question) {
            $answer = $answers[$question->id] ?? null;

            // Check required
            if ($question->required && empty($answer)) {
                $errors[$question->id] = __('This question is required');
                continue;
            }

            // Skip validation if empty and not required
            if (empty($answer)) {
                continue;
            }

            // Validate based on field type and rules
            $validationErrors = $this->validateSingleAnswer($question, $answer);
            if (!empty($validationErrors)) {
                $errors[$question->id] = $validationErrors;
            }
        }

        return $errors;
    }

    /**
     * Validate a single answer.
     */
    protected function validateSingleAnswer(QuestionnaireQuestion $question, $answer): ?string
    {
        $rules = $question->validation_rules ?? [];

        switch ($question->field_type) {
            case 'number':
                if (!is_numeric($answer)) {
                    return __('Please enter a valid number');
                }
                if (isset($rules['min']) && $answer < $rules['min']) {
                    return __('Value must be at least :min', ['min' => $rules['min']]);
                }
                if (isset($rules['max']) && $answer > $rules['max']) {
                    return __('Value must not exceed :max', ['max' => $rules['max']]);
                }
                break;

            case 'text':
            case 'textarea':
                if (isset($rules['min']) && strlen($answer) < $rules['min']) {
                    return __('Must be at least :min characters', ['min' => $rules['min']]);
                }
                if (isset($rules['max']) && strlen($answer) > $rules['max']) {
                    return __('Must not exceed :max characters', ['max' => $rules['max']]);
                }
                if (isset($rules['regex']) && !preg_match($rules['regex'], $answer)) {
                    return __('Invalid format');
                }
                break;

            case 'dropdown':
            case 'radio':
                $options = $question->getOptionsArray();
                if (!in_array($answer, $options)) {
                    return __('Please select a valid option');
                }
                break;

            case 'checkbox':
                $options = $question->getOptionsArray();
                $selected = is_array($answer) ? $answer : [$answer];
                foreach ($selected as $item) {
                    if (!in_array($item, $options)) {
                        return __('Invalid option selected');
                    }
                }
                break;
        }

        return null;
    }

    /**
     * Process and check for blocking flags.
     */
    public function checkForBlockingFlags(Questionnaire $questionnaire, array $answers): array
    {
        $flags = [];
        $hasHardBlock = false;
        $questions = $questionnaire->questions;

        foreach ($questions as $question) {
            $answer = $answers[$question->id] ?? null;
            if (empty($answer)) {
                continue;
            }

            $flagResult = $question->evaluateFlag($answer);
            if ($flagResult) {
                $flags[$question->id] = $flagResult;
                if ($flagResult['flag_type'] === 'hard') {
                    $hasHardBlock = true;
                }
            }
        }

        return [
            'flags' => $flags,
            'has_hard_block' => $hasHardBlock,
        ];
    }

    /**
     * Save questionnaire answers for an appointment.
     */
    public function saveAnswers(Appointment $appointment, Questionnaire $questionnaire, array $answers, array $files = []): void
    {
        $questions = $questionnaire->questions;

        foreach ($questions as $question) {
            $answer = $answers[$question->id] ?? null;

            // Handle file uploads
            $filePath = null;
            if ($question->field_type === 'file' && isset($files[$question->id])) {
                $filePath = $this->handleFileUpload($files[$question->id], $appointment->id);
                $answer = $files[$question->id]->getClientOriginalName();
            }

            // Handle checkbox arrays
            if ($question->field_type === 'checkbox' && is_array($answer)) {
                $answer = json_encode($answer);
            }

            // Evaluate flags
            $flagResult = $question->evaluateFlag($answer);

            QuestionnaireAnswer::create([
                'appointment_id' => $appointment->id,
                'question_id' => $question->id,
                'questionnaire_version' => $questionnaire->version,
                'answer_value' => $answer,
                'file_path' => $filePath,
                'is_flagged' => $flagResult !== null,
                'flag_reason' => $flagResult['flag_message'] ?? null,
            ]);
        }

        // Update appointment
        $appointment->update([
            'questionnaire_id' => $questionnaire->id,
            'questionnaire_completed_at' => now(),
        ]);
    }

    /**
     * Handle file upload.
     */
    protected function handleFileUpload($file, $appointmentId): string
    {
        $directory = 'questionnaire_uploads/' . $appointmentId;
        $filename = time() . '_' . $file->getClientOriginalName();
        
        // Store in public directory
        $file->move(public_path($directory), $filename);
        
        return $appointmentId . '/' . $filename;
    }

    /**
     * Get formatted answers for doctor review.
     */
    public function getFormattedAnswersForReview(Appointment $appointment): array
    {
        $answers = $appointment->questionnaireAnswers()
            ->with(['question.section'])
            ->get();

        $grouped = [];
        foreach ($answers as $answer) {
            $sectionName = $answer->question->section->name ?? 'General';
            if (!isset($grouped[$sectionName])) {
                $grouped[$sectionName] = [];
            }
            $grouped[$sectionName][] = [
                'question' => $answer->question->question_text,
                'answer' => $answer->display_value,
                'field_type' => $answer->question->field_type,
                'is_flagged' => $answer->is_flagged,
                'flag_reason' => $answer->flag_reason,
                'doctor_notes' => $answer->question->doctor_notes,
                'file_url' => $answer->full_file_url,
            ];
        }

        return $grouped;
    }
}


