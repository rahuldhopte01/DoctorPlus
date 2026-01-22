<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Questionnaire;
use App\Models\QuestionnaireAnswer;
use App\Models\QuestionnaireQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QuestionnaireService
{
    /**
     * Get questionnaire for a category.
     */
    public function getQuestionnaireForCategory($categoryId): ?Questionnaire
    {
        return Questionnaire::where('category_id', $categoryId)
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

            // Normalize answer: trim strings, convert empty strings to null
            if (is_string($answer)) {
                $answer = trim($answer);
                if ($answer === '') {
                    $answer = null;
                }
            }

            // Check if answer is empty (null, empty string, or empty array for checkboxes)
            $isEmpty = ($answer === null || $answer === '' || (is_array($answer) && empty($answer)));

            // Check required
            if ($question->required && $isEmpty) {
                $errors[$question->id] = __('This question is required');
                continue;
            }

            // Skip validation if empty and not required
            if ($isEmpty) {
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
                // Convert to numeric value for validation
                $numericValue = is_numeric($answer) ? (float)$answer : null;
                if ($numericValue === null) {
                    return __('Please enter a valid number');
                }
                if (isset($rules['min']) && $numericValue < $rules['min']) {
                    return __('Value must be at least :min', ['min' => $rules['min']]);
                }
                if (isset($rules['max']) && $numericValue > $rules['max']) {
                    return __('Value must not exceed :max', ['max' => $rules['max']]);
                }
                break;

            case 'text':
            case 'textarea':
                // Trim whitespace for validation
                $answer = trim($answer);
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
                // Trim and normalize answer for comparison
                $answer = is_string($answer) ? trim($answer) : $answer;
                $options = $question->getOptionsArray();
                
                // Check if answer matches any option (case-insensitive, trimmed)
                $matched = false;
                foreach ($options as $option) {
                    $normalizedOption = is_string($option) ? trim($option) : $option;
                    if ($answer === $normalizedOption || (is_string($answer) && is_string($normalizedOption) && strcasecmp($answer, $normalizedOption) === 0)) {
                        $matched = true;
                        break;
                    }
                }
                
                if (!$matched && !empty($answer)) {
                    return __('Please select a valid option');
                }
                break;

            case 'checkbox':
                $options = $question->getOptionsArray();
                $selected = is_array($answer) ? $answer : [$answer];
                
                foreach ($selected as $item) {
                    $item = is_string($item) ? trim($item) : $item;
                    $matched = false;
                    foreach ($options as $option) {
                        $normalizedOption = is_string($option) ? trim($option) : $option;
                        if ($item === $normalizedOption || (is_string($item) && is_string($normalizedOption) && strcasecmp($item, $normalizedOption) === 0)) {
                            $matched = true;
                            break;
                        }
                    }
                    if (!$matched && !empty($item)) {
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
     * Save questionnaire answers immediately (without appointment) - NEW METHOD
     */
    public function saveAnswersImmediate($userId, $categoryId, Questionnaire $questionnaire, array $answers, array $files = [], $status = 'pending'): void
    {
        // Ensure questions are loaded
        $questions = $questionnaire->questions;
        $submittedAt = now();

        if ($questions->isEmpty()) {
            \Log::warning('No questions found for questionnaire ID: ' . $questionnaire->id);
            return;
        }

        foreach ($questions as $question) {
            $answer = $answers[$question->id] ?? null;

            // Handle file uploads
            $filePath = null;
            if ($question->field_type === 'file' && isset($files[$question->id])) {
                $file = $files[$question->id];
                // Files are already processed and stored in permanent location
                if (is_string($file)) {
                    // File path (already moved to user folder)
                    $filePath = str_replace('questionnaire_uploads/', '', $file);
                    $answer = basename($file);
                } else {
                    // File upload object - should not happen here (files processed before calling this)
                    $userDir = 'questionnaire_uploads/user/' . $userId . '/' . $categoryId;
                    $fullUserDir = public_path($userDir);
                    if (!is_dir($fullUserDir)) {
                        mkdir($fullUserDir, 0755, true);
                    }
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                    $file->move($fullUserDir, $filename);
                    $filePath = 'user/' . $userId . '/' . $categoryId . '/' . $filename;
                    $answer = $file->getClientOriginalName();
                }
            }

            // Handle checkbox arrays
            if ($question->field_type === 'checkbox' && is_array($answer)) {
                $answer = json_encode($answer);
            }

            // Evaluate flags
            $flagResult = $question->evaluateFlag($answer);

            // Determine hospital_id: Find first doctor handling this category with a hospital
            // Prefer SUB_DOCTOR over ADMIN_DOCTOR to assign to the right hospital
            $hospitalId = null;
            if (Schema::hasColumn('doctor_category', 'category_id')) {
                // First try to find a SUB_DOCTOR with this category
                $doctor = \App\Models\Doctor::whereHas('categories', function($query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                })
                ->where('doctor_role', 'SUB_DOCTOR')
                ->whereNotNull('hospital_id')
                ->first();
                
                // If no SUB_DOCTOR found, try ADMIN_DOCTOR
                if (!$doctor) {
                    $doctor = \App\Models\Doctor::whereHas('categories', function($query) use ($categoryId) {
                        $query->where('category_id', $categoryId);
                    })
                    ->where('doctor_role', 'ADMIN_DOCTOR')
                    ->whereNotNull('hospital_id')
                    ->first();
                }
                
                // If still no doctor, try any doctor with this category
                if (!$doctor) {
                    $doctor = \App\Models\Doctor::whereHas('categories', function($query) use ($categoryId) {
                        $query->where('category_id', $categoryId);
                    })
                    ->whereNotNull('hospital_id')
                    ->first();
                }
                
                if ($doctor) {
                    $hospitalId = $doctor->hospital_id;
                }
            }

            QuestionnaireAnswer::create([
                'appointment_id' => null, // Will be set when appointment is created
                'user_id' => $userId,
                'category_id' => $categoryId,
                'questionnaire_id' => $questionnaire->id,
                'question_id' => $question->id,
                'questionnaire_version' => $questionnaire->version,
                'answer_value' => $answer,
                'file_path' => $filePath,
                'is_flagged' => $flagResult !== null,
                'flag_reason' => $flagResult['flag_message'] ?? null,
                'status' => $status,
                'submitted_at' => $submittedAt,
                'hospital_id' => $hospitalId, // Set hospital_id for hospital scoping
            ]);
        }
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
                $file = $files[$question->id];
                // Handle both file objects and file paths (strings from session)
                if (is_string($file)) {
                    // File path from session - extract just the relative path
                    $filePath = str_replace('questionnaire_uploads/', '', $file);
                    $answer = basename($file);
                } else {
                    // File upload object
                    $filePath = $this->handleFileUpload($file, $appointment->id);
                    $answer = $file->getClientOriginalName();
                }
            }

            // Handle checkbox arrays
            if ($question->field_type === 'checkbox' && is_array($answer)) {
                $answer = json_encode($answer);
            }

            // Evaluate flags
            $flagResult = $question->evaluateFlag($answer);

            $answerData = [
                'appointment_id' => $appointment->id,
                'question_id' => $question->id,
                'questionnaire_version' => $questionnaire->version,
                'answer_value' => $answer,
                'file_path' => $filePath,
                'is_flagged' => $flagResult !== null,
                'flag_reason' => $flagResult['flag_message'] ?? null,
            ];
            
            // Add new fields if migration has been run
            if (\Schema::hasColumn('questionnaire_answers', 'user_id')) {
                $answerData['user_id'] = $appointment->user_id;
                $answerData['category_id'] = $questionnaire->category_id;
                $answerData['questionnaire_id'] = $questionnaire->id;
                $answerData['status'] = 'approved'; // Answers linked to appointment are considered approved
                $answerData['submitted_at'] = $appointment->questionnaire_completed_at ?? now();
            }
            
            QuestionnaireAnswer::create($answerData);
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

        // Group by section with order information
        $grouped = [];
        $sectionMetadata = [];
        
        foreach ($answers as $answer) {
            $section = $answer->question->section;
            $sectionName = $section->name ?? 'General';
            $sectionOrder = $section->order ?? 999;
            
            if (!isset($grouped[$sectionName])) {
                $grouped[$sectionName] = [];
                $sectionMetadata[$sectionName] = [
                    'order' => $sectionOrder,
                    'description' => $section->description ?? null,
                ];
            }
            
            $grouped[$sectionName][] = [
                'question' => $answer->question->question_text,
                'answer' => $answer->display_value,
                'field_type' => $answer->question->field_type,
                'is_flagged' => $answer->is_flagged,
                'flag_reason' => $answer->flag_reason,
                'doctor_notes' => $answer->question->doctor_notes,
                'file_url' => $answer->full_file_url,
                'file_name' => $answer->answer_value, // Store original filename for file uploads
            ];
        }

        // Sort sections by order and rebuild array to preserve order
        uksort($grouped, function($a, $b) use ($sectionMetadata) {
            $orderA = $sectionMetadata[$a]['order'] ?? 999;
            $orderB = $sectionMetadata[$b]['order'] ?? 999;
            return $orderA <=> $orderB;
        });

        // Add section metadata to each section's first element for view access
        $result = [];
        foreach ($grouped as $sectionName => $answers) {
            $result[$sectionName] = $answers;
        }

        return $result;
    }
}



