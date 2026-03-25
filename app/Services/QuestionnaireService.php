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
     *
     * @param  array $answers          [question_id => value]
     * @param  array $subAnswers       [question_id => nested sub-answer array] (optional)
     * @param  array|null $visibleIds  Question IDs the patient actually saw (null = all)
     */
    public function validateAnswers(Questionnaire $questionnaire, array $answers, array $subAnswers = [], ?array $visibleIds = null): array
    {
        $errors = [];
        $questions = $questionnaire->questions;

        foreach ($questions as $question) {
            // Skip questions the patient never saw (conditionally hidden)
            if ($visibleIds !== null && !in_array($question->id, $visibleIds)) {
                continue;
            }

            $answer = $answers[$question->id] ?? null;

            if (is_string($answer)) {
                $answer = trim($answer);
                if ($answer === '') {
                    $answer = null;
                }
            }

            $isEmpty = ($answer === null || $answer === '' || (is_array($answer) && empty($answer)));

            if ($question->required && $isEmpty) {
                $errors[$question->id] = __('This question is required');
                continue;
            }

            if ($isEmpty) {
                continue;
            }

            $validationErrors = $this->validateSingleAnswer($question, $answer);
            if (!empty($validationErrors)) {
                $errors[$question->id] = $validationErrors;
            }

            // Validate sub-answers for this question
            if (!empty($subAnswers[$question->id])) {
                $behavior = $question->getMatchingBehavior($answer);
                if ($behavior && !empty($behavior['sub_question'])) {
                    $subErrors = $this->validateSubAnswers(
                        $subAnswers[$question->id],
                        $behavior['sub_question'],
                        $question->id
                    );
                    $errors = array_merge($errors, $subErrors);
                }
            }
        }

        return $errors;
    }

    /**
     * Recursively validate sub-answers against their sub-question definitions.
     *
     * @param  array  $subAnswerTree  The nested sub-answer array for this parent value
     * @param  array  $subQuestionDef The sub_question definition from option_behaviors
     * @param  string $parentKey      Used to build error keys like "42_sq_abc1"
     * @param  int    $depth
     */
    protected function validateSubAnswers(array $subAnswerTree, array $subQuestionDef, $parentKey, int $depth = 1): array
    {
        if ($depth > 3) {
            return [];
        }

        $errors = [];
        $tempId = $subQuestionDef['temp_id'] ?? null;
        if (!$tempId) {
            return [];
        }

        // Find this sub-question's value in the tree
        $value = null;
        foreach ($subAnswerTree as $entry) {
            if (($entry['temp_id'] ?? null) === $tempId) {
                $value = $entry['value'] ?? null;
                break;
            }
        }

        $required = $subQuestionDef['required'] ?? false;
        $isEmpty  = ($value === null || $value === '' || (is_array($value) && empty($value)));
        $errorKey = $parentKey . '_' . $tempId;

        if ($required && $isEmpty) {
            $errors[$errorKey] = __('This question is required');
            return $errors;
        }

        if ($isEmpty) {
            return [];
        }

        // Check nested behaviors of this sub-question
        $behaviors = $subQuestionDef['behaviors'] ?? [];
        foreach ($behaviors as $behavior) {
            if (QuestionnaireQuestion::evaluateCondition($value, $behavior['condition'] ?? []) && !empty($behavior['sub_question'])) {
                $nestedErrors = $this->validateSubAnswers(
                    $subAnswerTree,
                    $behavior['sub_question'],
                    $errorKey,
                    $depth + 1
                );
                $errors = array_merge($errors, $nestedErrors);
                break;
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
                $answer  = is_string($answer) ? trim($answer) : $answer;
                $options = $question->getOptionsArray();
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
                $options  = $question->getOptionsArray();
                $selected = is_array($answer) ? $answer : [$answer];
                foreach ($selected as $item) {
                    $item    = is_string($item) ? trim($item) : $item;
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
     * Check for blocking flags, including sub-answer flags.
     */
    public function checkForBlockingFlags(Questionnaire $questionnaire, array $answers, array $subAnswers = []): array
    {
        $flags       = [];
        $hasHardBlock = false;
        $questions   = $questionnaire->questions;

        foreach ($questions as $question) {
            $answer = $answers[$question->id] ?? null;
            if (empty($answer)) {
                continue;
            }

            // Evaluate flags from option_behaviors (new system)
            $triggeredFlags = $question->evaluateBehaviorsForValue($answer);
            foreach ($triggeredFlags as $flag) {
                $flags[$question->id][] = $flag;
                if ($flag['flag_type'] === 'hard') {
                    $hasHardBlock = true;
                }
            }

            // Also check sub-answer flags
            if (!empty($subAnswers[$question->id])) {
                $behavior = $question->getMatchingBehavior($answer);
                if ($behavior && !empty($behavior['sub_question'])) {
                    $subFlags = $this->checkSubAnswerFlags(
                        $subAnswers[$question->id],
                        $behavior['sub_question']
                    );
                    foreach ($subFlags as $subFlag) {
                        $flags['sub_' . $question->id][] = $subFlag;
                        if ($subFlag['flag_type'] === 'hard') {
                            $hasHardBlock = true;
                        }
                    }
                }
            }
        }

        return [
            'flags'         => $flags,
            'has_hard_block' => $hasHardBlock,
        ];
    }

    /**
     * Recursively collect flags from visible sub-answers.
     */
    protected function checkSubAnswerFlags(array $subAnswerTree, array $subQuestionDef, int $depth = 1): array
    {
        if ($depth > 3) {
            return [];
        }

        $flags  = [];
        $tempId = $subQuestionDef['temp_id'] ?? null;
        if (!$tempId) {
            return [];
        }

        // Find value in tree
        $entry = null;
        foreach ($subAnswerTree as $item) {
            if (($item['temp_id'] ?? null) === $tempId) {
                $entry = $item;
                break;
            }
        }
        if (!$entry || empty($entry['value'])) {
            return [];
        }

        $value     = $entry['value'];
        $behaviors = $subQuestionDef['behaviors'] ?? [];

        foreach ($behaviors as $behavior) {
            if (!QuestionnaireQuestion::evaluateCondition($value, $behavior['condition'] ?? [])) {
                continue;
            }
            // Collect flags for this behavior
            foreach ($behavior['flags'] ?? [] as $flag) {
                $flags[] = $flag;
            }
            // Recurse into sub-sub-question
            if (!empty($behavior['sub_question'])) {
                $nested = $this->checkSubAnswerFlags($subAnswerTree, $behavior['sub_question'], $depth + 1);
                $flags  = array_merge($flags, $nested);
            }
            break;
        }

        return $flags;
    }

    /**
     * Save questionnaire answers immediately (without appointment).
     */
    public function saveAnswersImmediate($userId, $categoryId, Questionnaire $questionnaire, array $answers, array $files = [], $status = 'pending', array $subAnswers = []): void
    {
        $questions   = $questionnaire->questions;
        $submittedAt = now();

        if ($questions->isEmpty()) {
            \Log::warning('No questions found for questionnaire ID: ' . $questionnaire->id);
            return;
        }

        // Resolve hospital_id once for all answers
        $hospitalId = $this->resolveHospitalId($categoryId);

        foreach ($questions as $question) {
            $answer   = $answers[$question->id] ?? null;
            $filePath = null;

            // Handle file uploads
            if ($question->field_type === 'file' && isset($files[$question->id])) {
                $file = $files[$question->id];
                if (is_string($file)) {
                    $filePath = str_replace('questionnaire_uploads/', '', $file);
                    $answer   = basename($file);
                } else {
                    $userDir     = 'questionnaire_uploads/user/' . $userId . '/' . $categoryId;
                    $fullUserDir = public_path($userDir);
                    if (!is_dir($fullUserDir)) {
                        mkdir($fullUserDir, 0755, true);
                    }
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                    $file->move($fullUserDir, $filename);
                    $filePath = 'user/' . $userId . '/' . $categoryId . '/' . $filename;
                    $answer   = $file->getClientOriginalName();
                }
            }

            // Handle checkbox arrays
            if ($question->field_type === 'checkbox' && is_array($answer)) {
                $answer = json_encode($answer);
            }

            // Evaluate flags (new system first, falls back to legacy)
            $flagResult = $question->evaluateFlag($answer);

            // Build sub-answers with flags for this question
            $builtSubAnswers = null;
            if (!empty($subAnswers[$question->id]) && $answer) {
                $behavior = $question->getMatchingBehavior($answer);
                if ($behavior && !empty($behavior['sub_question'])) {
                    $builtSubAnswers = $this->buildSubAnswersWithFlags(
                        $subAnswers[$question->id],
                        $behavior['sub_question']
                    );
                }
            }

            QuestionnaireAnswer::create([
                'appointment_id'       => null,
                'user_id'              => $userId,
                'category_id'          => $categoryId,
                'questionnaire_id'     => $questionnaire->id,
                'question_id'          => $question->id,
                'questionnaire_version'=> $questionnaire->version,
                'answer_value'         => $answer,
                'file_path'            => $filePath,
                'is_flagged'           => $flagResult !== null,
                'flag_reason'          => $flagResult['flag_message'] ?? null,
                'sub_answers'          => $builtSubAnswers,
                'status'               => $status,
                'submitted_at'         => $submittedAt,
                'hospital_id'          => $hospitalId,
            ]);
        }
    }

    /**
     * Recursively build the sub-answer tree with evaluated flags.
     */
    protected function buildSubAnswersWithFlags(array $subAnswerTree, array $subQuestionDef, int $depth = 1): array
    {
        if ($depth > 3) {
            return $subAnswerTree;
        }

        $tempId = $subQuestionDef['temp_id'] ?? null;
        if (!$tempId) {
            return $subAnswerTree;
        }

        $result = [];
        foreach ($subAnswerTree as $entry) {
            if (($entry['temp_id'] ?? null) !== $tempId) {
                $result[] = $entry;
                continue;
            }

            $value     = $entry['value'] ?? null;
            $behaviors = $subQuestionDef['behaviors'] ?? [];
            $flagged   = false;
            $flagReason = null;
            $nestedSubAnswers = $entry['sub_answers'] ?? [];

            // Evaluate flags on this sub-answer
            foreach ($behaviors as $behavior) {
                if (!QuestionnaireQuestion::evaluateCondition($value, $behavior['condition'] ?? [])) {
                    continue;
                }
                foreach ($behavior['flags'] ?? [] as $flag) {
                    $flagged    = true;
                    $flagReason = $flag['flag_message'] ?? null;
                    break;
                }
                // Recurse into nested sub-question
                if (!empty($behavior['sub_question']) && !empty($nestedSubAnswers)) {
                    $nestedSubAnswers = $this->buildSubAnswersWithFlags(
                        $nestedSubAnswers,
                        $behavior['sub_question'],
                        $depth + 1
                    );
                }
                break;
            }

            $result[] = array_merge($entry, [
                'is_flagged'  => $flagged,
                'flag_reason' => $flagReason,
                'sub_answers' => $nestedSubAnswers,
            ]);
        }

        return $result;
    }

    /**
     * Resolve the hospital_id for a category by finding the relevant doctor.
     */
    protected function resolveHospitalId($categoryId): ?int
    {
        if (!Schema::hasColumn('doctor_category', 'category_id')) {
            return null;
        }

        $doctor = \App\Models\Doctor::whereHas('categories', function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })->where('doctor_role', 'SUB_DOCTOR')->whereNotNull('hospital_id')->first();

        if (!$doctor) {
            $doctor = \App\Models\Doctor::whereHas('categories', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })->where('doctor_role', 'ADMIN_DOCTOR')->whereNotNull('hospital_id')->first();
        }

        if (!$doctor) {
            $doctor = \App\Models\Doctor::whereHas('categories', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })->whereNotNull('hospital_id')->first();
        }

        return $doctor?->hospital_id;
    }

    /**
     * Save questionnaire answers for an appointment (legacy appointment-based flow).
     */
    public function saveAnswers(Appointment $appointment, Questionnaire $questionnaire, array $answers, array $files = []): void
    {
        $questions = $questionnaire->questions;

        foreach ($questions as $question) {
            $answer   = $answers[$question->id] ?? null;
            $filePath = null;

            if ($question->field_type === 'file' && isset($files[$question->id])) {
                $file = $files[$question->id];
                if (is_string($file)) {
                    $filePath = str_replace('questionnaire_uploads/', '', $file);
                    $answer   = basename($file);
                } else {
                    $filePath = $this->handleFileUpload($file, $appointment->id);
                    $answer   = $file->getClientOriginalName();
                }
            }

            if ($question->field_type === 'checkbox' && is_array($answer)) {
                $answer = json_encode($answer);
            }

            $flagResult  = $question->evaluateFlag($answer);
            $answerData  = [
                'appointment_id'       => $appointment->id,
                'question_id'          => $question->id,
                'questionnaire_version'=> $questionnaire->version,
                'answer_value'         => $answer,
                'file_path'            => $filePath,
                'is_flagged'           => $flagResult !== null,
                'flag_reason'          => $flagResult['flag_message'] ?? null,
            ];

            if (Schema::hasColumn('questionnaire_answers', 'user_id')) {
                $answerData['user_id']      = $appointment->user_id;
                $answerData['category_id']  = $questionnaire->category_id;
                $answerData['questionnaire_id'] = $questionnaire->id;
                $answerData['status']       = 'approved';
                $answerData['submitted_at'] = $appointment->questionnaire_completed_at ?? now();
            }

            QuestionnaireAnswer::create($answerData);
        }

        $appointment->update([
            'questionnaire_id'          => $questionnaire->id,
            'questionnaire_completed_at' => now(),
        ]);
    }

    /**
     * Handle file upload for appointment-based flow.
     */
    protected function handleFileUpload($file, $appointmentId): string
    {
        $directory = 'questionnaire_uploads/' . $appointmentId;
        $filename  = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path($directory), $filename);
        return $appointmentId . '/' . $filename;
    }

    /**
     * Get formatted answers for doctor review (appointment-based).
     */
    public function getFormattedAnswersForReview(Appointment $appointment): array
    {
        $answers = $appointment->questionnaireAnswers()
            ->with(['question.section'])
            ->get();

        $grouped         = [];
        $sectionMetadata = [];

        foreach ($answers as $answer) {
            $section     = $answer->question->section;
            $sectionName = $section->name ?? 'General';
            $sectionOrder = $section->order ?? 999;

            if (!isset($grouped[$sectionName])) {
                $grouped[$sectionName] = [];
                $sectionMetadata[$sectionName] = [
                    'order'       => $sectionOrder,
                    'description' => $section->description ?? null,
                ];
            }

            $grouped[$sectionName][] = [
                'question'    => $answer->question->question_text,
                'answer'      => $answer->display_value,
                'field_type'  => $answer->question->field_type,
                'is_flagged'  => $answer->is_flagged,
                'flag_reason' => $answer->flag_reason,
                'doctor_notes'=> $answer->question->doctor_notes,
                'file_url'    => $answer->full_file_url,
                'file_name'   => $answer->answer_value,
                'sub_answers' => $answer->sub_answers ?? [],
            ];
        }

        uksort($grouped, function ($a, $b) use ($sectionMetadata) {
            return ($sectionMetadata[$a]['order'] ?? 999) <=> ($sectionMetadata[$b]['order'] ?? 999);
        });

        return $grouped;
    }
}
