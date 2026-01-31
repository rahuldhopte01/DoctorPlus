<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\Questionnaire;
use App\Models\QuestionnaireAnswer;
use App\Services\QuestionnaireService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class QuestionnaireController extends Controller
{
    protected $questionnaireService;

    public function __construct(QuestionnaireService $questionnaireService)
    {
        $this->questionnaireService = $questionnaireService;
    }

    /**
     * Show questionnaire form for a doctor's category.
     */
    public function show($doctorId)
    {
        $doctor = Doctor::with('categories')->findOrFail($doctorId);
        
        if (!$doctor->category) {
            return redirect()->back()->with('error', __('Doctor has no category assigned'));
        }

        $questionnaire = $this->questionnaireService->getQuestionnaireForCategory($doctor->category_id);

        if (!$questionnaire) {
            // No questionnaire required, proceed to booking
            return redirect()->route('booking.show', ['id' => $doctorId, 'name' => $doctor->name]);
        }

        return view('website.questionnaire.form', compact('doctor', 'questionnaire'));
    }

    /**
     * Validate questionnaire answers (AJAX).
     */
    public function validateAnswers(Request $request, $doctorId)
    {
        $doctor = Doctor::findOrFail($doctorId);
        $questionnaire = $this->questionnaireService->getQuestionnaireForCategory($doctor->category_id);

        if (!$questionnaire) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $answers = $request->input('answers', []);
        $errors = $this->questionnaireService->validateAnswers($questionnaire, $answers);

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'errors' => $errors,
            ]);
        }

        // Check for blocking flags
        $flagCheck = $this->questionnaireService->checkForBlockingFlags($questionnaire, $answers);

        if ($flagCheck['has_hard_block']) {
            return response()->json([
                'success' => false,
                'blocked' => true,
                'message' => __('Based on your answers, you are not eligible for this treatment. Please consult with a healthcare provider.'),
                'flags' => $flagCheck['flags'],
            ]);
        }

        // Store answers in session for later use during booking
        session()->put('questionnaire_answers', [
            'questionnaire_id' => $questionnaire->id,
            'answers' => $answers,
            'flags' => $flagCheck['flags'],
            'version' => $questionnaire->version,
        ]);

        return response()->json([
            'success' => true,
            'has_warnings' => !empty($flagCheck['flags']),
            'flags' => $flagCheck['flags'],
        ]);
    }

    /**
     * Get questionnaire data for AJAX rendering.
     */
    public function getQuestionnaire($categoryId)
    {
        $questionnaire = $this->questionnaireService->getQuestionnaireForCategory($categoryId);

        if (!$questionnaire) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'No questionnaire found for this category',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $questionnaire,
        ]);
    }

    /**
     * Show questionnaire form for a category (Single page - all sections)
     */
    public function showByCategory($categoryId)
    {
        // Ensure user is authenticated (middleware should handle this, but double-check)
        if (!Auth::check()) {
            // Store intent in session for post-login redirect
            session()->put('questionnaire_intent', [
                'category_id' => $categoryId,
                'redirect_to' => url('/questionnaire/category/' . $categoryId),
            ]);
            return redirect('/patient-login')->with('info', __('Please login to continue with the questionnaire'));
        }

        $category = Category::with(['treatment', 'questionnaire.sections.questions'])->findOrFail($categoryId);
        
        if (!$category->questionnaire || !$category->questionnaire->status) {
            return redirect()->route('category.detail', ['id' => $categoryId])
                ->with('error', __('No questionnaire available for this category'));
        }

        $questionnaire = $this->questionnaireService->getQuestionnaireForCategory($categoryId);
        $treatment = $category->treatment;

        // Load saved answers from session - ensure it's always an array with proper structure
        $savedAnswers = session()->get('questionnaire_answers_' . $categoryId, []);
        if (!is_array($savedAnswers) || !isset($savedAnswers['answers'])) {
            $savedAnswers = ['answers' => [], 'files' => []];
        }
        
        // Normalize saved answers to ensure question IDs are integers
        if (isset($savedAnswers['answers']) && is_array($savedAnswers['answers'])) {
            $normalizedSavedAnswers = [];
            foreach ($savedAnswers['answers'] as $questionId => $answer) {
                $normalizedSavedAnswers[(int) $questionId] = $answer;
            }
            $savedAnswers['answers'] = $normalizedSavedAnswers;
        }

        // Check if patient can submit (has active submission)
        $submissionCheck = \App\Models\QuestionnaireSubmission::canPatientSubmit(Auth::id(), $categoryId);

        return view('website.questionnaire.category_form', compact(
            'category', 
            'questionnaire', 
            'treatment', 
            'savedAnswers',
            'submissionCheck'
        ));
    }

    /**
     * Show questionnaire section (Issue 2: Section-wise navigation)
     * Each section is displayed on a separate page/step
     */
    public function showSection($categoryId, $sectionIndex)
    {
        if (!Auth::check()) {
            session()->put('questionnaire_intent', [
                'category_id' => $categoryId,
                'redirect_to' => url('/questionnaire/category/' . $categoryId . '/section/' . $sectionIndex),
            ]);
            return redirect('/patient-login')->with('info', __('Please login to continue with the questionnaire'));
        }

        $category = Category::with(['treatment', 'questionnaire.sections.questions'])->findOrFail($categoryId);
        
        if (!$category->questionnaire || !$category->questionnaire->status) {
            return redirect()->route('category.detail', ['id' => $categoryId])
                ->with('error', __('No questionnaire available for this category'));
        }

        $questionnaire = $this->questionnaireService->getQuestionnaireForCategory($categoryId);
        $treatment = $category->treatment;
        $sections = $questionnaire->sections;
        
        // Validate section index
        $sectionIndex = (int) $sectionIndex;
        if ($sectionIndex < 0 || $sectionIndex >= $sections->count()) {
            return redirect()->route('questionnaire.section', ['categoryId' => $categoryId, 'sectionIndex' => 0])
                ->with('error', __('Invalid section'));
        }

        $currentSection = $sections[$sectionIndex];
        $totalSections = $sections->count();

        // Load saved answers from session - ensure it's always an array with proper structure
        $savedAnswers = session()->get('questionnaire_answers_' . $categoryId, []);
        if (!is_array($savedAnswers) || !isset($savedAnswers['answers'])) {
            $savedAnswers = ['answers' => [], 'files' => []];
        }
        
        // Normalize saved answers to ensure question IDs are integers
        if (isset($savedAnswers['answers']) && is_array($savedAnswers['answers'])) {
            $normalizedSavedAnswers = [];
            foreach ($savedAnswers['answers'] as $questionId => $answer) {
                $normalizedSavedAnswers[(int) $questionId] = $answer;
            }
            $savedAnswers['answers'] = $normalizedSavedAnswers;
        }

        return view('website.questionnaire.category_section', compact(
            'category', 
            'questionnaire', 
            'treatment', 
            'savedAnswers',
            'currentSection',
            'sectionIndex',
            'totalSections',
            'sections'
        ));
    }

    /**
     * Save questionnaire answers (autosave/progressive save) - Phase 5
     * Saves answers to session for persistence within session
     * Handles file uploads temporarily (Issue 3: File upload support)
     */
    public function saveAnswers(Request $request, $categoryId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('Please login to save answers'),
            ], 401);
        }

        $category = Category::findOrFail($categoryId);
        $questionnaire = $this->questionnaireService->getQuestionnaireForCategory($categoryId);

        if (!$questionnaire) {
            return response()->json([
                'success' => false,
                'message' => __('Questionnaire not found'),
            ], 404);
        }

        $answers = $request->input('answers', []);
        $files = $request->file('files', []);
        
        // Handle file uploads (Issue 3: File upload support - Birth Certificate and other files)
        $uploadedFiles = [];
        if (!empty($files)) {
            $uploadDir = 'questionnaire_uploads/temp/' . Auth::id() . '/' . $categoryId;
            $fullPath = public_path($uploadDir);
            
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
            
            foreach ($files as $questionId => $file) {
                // Validate file type and size
                $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
                $extension = strtolower($file->getClientOriginalExtension());
                $maxSize = 5242880; // 5MB
                
                if (!in_array($extension, $allowedTypes)) {
                    return response()->json([
                        'success' => false,
                        'errors' => [$questionId => __('File type not allowed. Allowed types: PDF, JPG, PNG')],
                    ]);
                }
                
                if ($file->getSize() > $maxSize) {
                    return response()->json([
                        'success' => false,
                        'errors' => [$questionId => __('File size exceeds 5MB limit')],
                    ]);
                }
                
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $file->move($fullPath, $filename);
                $uploadedFiles[$questionId] = $uploadDir . '/' . $filename;
                // Store file path in answers
                $answers[$questionId] = $uploadDir . '/' . $filename;
            }
        }

        // Get existing saved answers and merge
        $existingAnswers = session()->get('questionnaire_answers_' . $categoryId, []);
        $mergedAnswers = array_merge($existingAnswers['answers'] ?? [], $answers);
        
        // Validate answers (only validate provided answers, not all questions)
        $tempQuestionnaire = clone $questionnaire;
        $errors = [];
        foreach ($answers as $questionId => $answer) {
            $question = $questionnaire->questions->firstWhere('id', $questionId);
            if ($question && $question->required && empty($answer)) {
                $errors[$questionId] = __('This question is required');
            }
        }
        
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'errors' => $errors,
            ]);
        }

        // Store answers in session
        session()->put('questionnaire_answers_' . $categoryId, [
            'questionnaire_id' => $questionnaire->id,
            'category_id' => $categoryId,
            'answers' => $mergedAnswers,
            'files' => array_merge($existingAnswers['files'] ?? [], $uploadedFiles),
            'user_id' => Auth::id(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Answers saved successfully'),
        ]);
    }

    /**
     * Save section answers (Issue 2: Section-wise navigation)
     * Saves answers for current section and navigates to next/previous
     */
    public function saveSectionAnswers(Request $request, $categoryId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('Please login to save answers'),
            ], 401);
        }

        $category = Category::findOrFail($categoryId);
        $questionnaire = $this->questionnaireService->getQuestionnaireForCategory($categoryId);

        if (!$questionnaire) {
            return response()->json([
                'success' => false,
                'message' => __('Questionnaire not found'),
            ], 404);
        }

        $sectionIndex = (int) $request->input('section_index', 0);
        $answers = $request->input('answers', []);
        $files = $request->file('files', []);
        $action = $request->input('action', 'next'); // 'next' or 'previous'

        // Normalize answers - ensure question IDs are used as keys and handle checkbox arrays
        $normalizedAnswers = [];
        foreach ($answers as $questionId => $answer) {
            // Convert string keys to integers for consistency
            $questionId = (int) $questionId;
            
            // Normalize answer values: trim strings, convert empty strings to null
            if (is_string($answer)) {
                $answer = trim($answer);
                if ($answer === '') {
                    $answer = null;
                }
            } elseif (is_array($answer)) {
                // For checkbox arrays, filter out empty values and trim strings
                $answer = array_filter(array_map(function($item) {
                    if (is_string($item)) {
                        $item = trim($item);
                        return $item === '' ? null : $item;
                    }
                    return $item;
                }, $answer), function($item) {
                    return $item !== null;
                });
                // Re-index array after filtering
                $answer = array_values($answer);
                if (empty($answer)) {
                    $answer = null;
                }
            }
            
            $normalizedAnswers[$questionId] = $answer;
        }

        // Handle file uploads (Issue 3: File upload support)
        $uploadedFiles = [];
        if (!empty($files)) {
            $uploadDir = 'questionnaire_uploads/temp/' . Auth::id() . '/' . $categoryId;
            $fullPath = public_path($uploadDir);
            
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
            
            foreach ($files as $questionId => $file) {
                $questionId = (int) $questionId;
                
                // Validate file type and size
                $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
                $extension = strtolower($file->getClientOriginalExtension());
                $maxSize = 5242880; // 5MB
                
                if (!in_array($extension, $allowedTypes)) {
                    return response()->json([
                        'success' => false,
                        'errors' => [$questionId => __('File type not allowed. Allowed types: PDF, JPG, PNG')],
                    ]);
                }
                
                if ($file->getSize() > $maxSize) {
                    return response()->json([
                        'success' => false,
                        'errors' => [$questionId => __('File size exceeds 5MB limit')],
                    ]);
                }
                
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $file->move($fullPath, $filename);
                $uploadedFiles[$questionId] = $uploadDir . '/' . $filename;
                $normalizedAnswers[$questionId] = $uploadDir . '/' . $filename;
            }
        }

        // Get current section and validate required fields
        $sections = $questionnaire->sections;
        if ($sectionIndex >= 0 && $sectionIndex < $sections->count()) {
            $currentSection = $sections[$sectionIndex];
            $errors = [];
            
            foreach ($currentSection->questions as $question) {
                $answer = $normalizedAnswers[$question->id] ?? null;
                
                // Check if answer is empty (null, empty string, or empty array)
                $isEmpty = ($answer === null || $answer === '' || (is_array($answer) && empty($answer)));
                
                if ($question->required && $isEmpty) {
                    $errors[$question->id] = __('This question is required');
                }
            }
            
            if (!empty($errors) && $action === 'next') {
                return response()->json([
                    'success' => false,
                    'errors' => $errors,
                ]);
            }
        }

        // Merge with existing answers - preserve all existing answers and update with new ones
        $existingAnswers = session()->get('questionnaire_answers_' . $categoryId, []);
        $existingAnswersArray = $existingAnswers['answers'] ?? [];
        
        // Merge answers - new answers override existing ones for the same question IDs
        $mergedAnswers = array_merge($existingAnswersArray, $normalizedAnswers);
        
        // Store in session
        session()->put('questionnaire_answers_' . $categoryId, [
            'questionnaire_id' => $questionnaire->id,
            'category_id' => $categoryId,
            'answers' => $mergedAnswers,
            'files' => array_merge($existingAnswers['files'] ?? [], $uploadedFiles),
            'user_id' => Auth::id(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        // Calculate next/previous section index
        $totalSections = $sections->count();
        $nextSectionIndex = $action === 'next' ? min($sectionIndex + 1, $totalSections - 1) : max($sectionIndex - 1, 0);
        $isLastSection = ($sectionIndex === $totalSections - 1);

        return response()->json([
            'success' => true,
            'message' => __('Section saved successfully'),
            'next_section_index' => $nextSectionIndex,
            'is_last_section' => $isLastSection,
            'redirect_url' => route('questionnaire.section', ['categoryId' => $categoryId, 'sectionIndex' => $nextSectionIndex]),
        ]);
    }

    /**
     * Get saved answers for a category - Phase 5
     */
    public function getSavedAnswers($categoryId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'data' => [],
            ], 401);
        }

        $savedAnswers = session()->get('questionnaire_answers_' . $categoryId, []);

        return response()->json([
            'success' => true,
            'data' => $savedAnswers,
        ]);
    }

    /**
     * Final submit questionnaire - Phase 6
     * Validates, checks flags, stores in session for later use
     */
    public function submitQuestionnaire(Request $request, $categoryId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('Please login to submit'),
            ], 401);
        }

        $category = Category::findOrFail($categoryId);
        $questionnaire = $this->questionnaireService->getQuestionnaireForCategory($categoryId);

        // Check if patient already has an active submission for this category
        $canSubmit = \App\Models\QuestionnaireSubmission::canPatientSubmit(Auth::id(), $categoryId);
        if (!$canSubmit['can_submit']) {
            return response()->json([
                'success' => false,
                'blocked' => true,
                'message' => $canSubmit['message'],
                'status' => $canSubmit['status'] ?? null,
            ], 422);
        }

        if (!$questionnaire) {
            return response()->json([
                'success' => false,
                'message' => __('Questionnaire not found'),
            ], 404);
        }

        // Get answers directly from request (single-page form submits all at once)
        $answers = $request->input('answers', []);
        $submissionFlow = $request->input('submission_flow', 'with_medicine');
        
        // Normalize answers to ensure question IDs are integers and values are clean
        $normalizedAnswers = [];
        foreach ($answers as $questionId => $answer) {
            $questionId = (int) $questionId;
            
            // Normalize answer values: trim strings, convert empty strings to null
            if (is_string($answer)) {
                $answer = trim($answer);
                if ($answer === '') {
                    $answer = null;
                }
            } elseif (is_array($answer)) {
                // For checkbox arrays, filter out empty values and trim strings
                $answer = array_filter(array_map(function($item) {
                    if (is_string($item)) {
                        $item = trim($item);
                        return $item === '' ? null : $item;
                    }
                    return $item;
                }, $answer), function($item) {
                    return $item !== null;
                });
                // Re-index array after filtering
                $answer = array_values($answer);
                if (empty($answer)) {
                    $answer = null;
                }
            }
            
            $normalizedAnswers[$questionId] = $answer;
        }
        $answers = $normalizedAnswers;
        $files = $request->file('files', []);
        
        // Get any existing files from session (in case files were uploaded earlier)
        $savedData = session()->get('questionnaire_answers_' . $categoryId, []);
        $uploadedFiles = $savedData['files'] ?? [];

        // Handle file uploads if provided in final submit (Issue 3: File upload)
        if (!empty($files)) {
            $uploadDir = 'questionnaire_uploads/temp/' . Auth::id() . '/' . $categoryId;
            if (!is_dir(public_path($uploadDir))) {
                mkdir(public_path($uploadDir), 0755, true);
            }
            
            foreach ($files as $questionId => $file) {
                $questionId = (int) $questionId;
                
                // Validate file type and size
                $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
                $extension = strtolower($file->getClientOriginalExtension());
                $maxSize = 5242880; // 5MB
                
                if (!in_array($extension, $allowedTypes)) {
                    return response()->json([
                        'success' => false,
                        'errors' => [$questionId => __('File type not allowed. Allowed types: PDF, JPG, PNG')],
                    ]);
                }
                
                if ($file->getSize() > $maxSize) {
                    return response()->json([
                        'success' => false,
                        'errors' => [$questionId => __('File size exceeds 5MB limit')],
                    ]);
                }
                
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $file->move(public_path($uploadDir), $filename);
                $uploadedFiles[$questionId] = $uploadDir . '/' . $filename;
                $answers[$questionId] = $uploadDir . '/' . $filename;
            }
        }

        // Validate all answers
        $errors = $this->questionnaireService->validateAnswers($questionnaire, $answers);
        if (!empty($errors)) {
            // Enrich errors with section and question information
            $enrichedErrors = [];
            $errorDetails = [];
            
            $sections = $questionnaire->sections()->with('questions')->orderBy('order')->get();
            foreach ($sections as $sectionIndex => $section) {
                foreach ($section->questions as $question) {
                    if (isset($errors[$question->id])) {
                        $enrichedErrors[$question->id] = $errors[$question->id];
                        $errorDetails[$question->id] = [
                            'section_index' => $sectionIndex,
                            'section_name' => $section->name,
                            'question_text' => $question->question_text,
                            'error_message' => $errors[$question->id],
                        ];
                    }
                }
            }
            
            return response()->json([
                'success' => false,
                'errors' => $enrichedErrors,
                'error_details' => $errorDetails,
            ]);
        }

        // Check for blocking flags
        $flagCheck = $this->questionnaireService->checkForBlockingFlags($questionnaire, $answers);

        if ($flagCheck['has_hard_block']) {
            return response()->json([
                'success' => false,
                'blocked' => true,
                'message' => __('Based on your answers, you are not eligible for this treatment. Please consult with a healthcare provider.'),
                'flags' => $flagCheck['flags'],
            ]);
        }

        // Delete any existing pending answers for this user/category/questionnaire combination
        // Delete any existing pending answers for this user/category/questionnaire
        // Only if the migration has been run (user_id column exists)
        if (Schema::hasColumn('questionnaire_answers', 'user_id')) {
            QuestionnaireAnswer::whereNull('appointment_id')
                ->where('user_id', Auth::id())
                ->where('category_id', $categoryId)
                ->where('questionnaire_id', $questionnaire->id)
                ->where('status', 'pending')
                ->delete();
        }

        // Move files from temp to permanent location (user-specific folder)
        $permanentFiles = [];
        foreach ($uploadedFiles as $questionId => $filePath) {
            $questionId = (int) $questionId;
            if (strpos($filePath, 'temp/') === false) {
                // Already in permanent location
                $permanentFiles[$questionId] = str_replace('questionnaire_uploads/', '', $filePath);
            } else {
                // Move from temp to user's questionnaire folder
                $tempPath = public_path('questionnaire_uploads/' . $filePath);
                if (file_exists($tempPath)) {
                    $userDir = 'questionnaire_uploads/user/' . Auth::id() . '/' . $categoryId;
                    $fullUserDir = public_path($userDir);
                    if (!is_dir($fullUserDir)) {
                        mkdir($fullUserDir, 0755, true);
                    }
                    $filename = basename($filePath);
                    $newPath = $userDir . '/' . $filename;
                    rename($tempPath, public_path($newPath));
                    $permanentFiles[$questionId] = str_replace('questionnaire_uploads/', '', $newPath);
                }
            }
        }

        // Save answers immediately to database with status 'pending'
        // Only if the migration has been run (user_id column exists)
        if (Schema::hasColumn('questionnaire_answers', 'user_id')) {
            try {
                $this->questionnaireService->saveAnswersImmediate(
                    Auth::id(),
                    $categoryId,
                    $questionnaire,
                    $answers,
                    $permanentFiles,
                    'pending'
                );
            } catch (\Exception $e) {
                \Log::error('Error saving questionnaire answers: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to save questionnaire. Please try again.'),
                ], 500);
            }
        } else {
            \Log::warning('Questionnaire answers table does not have user_id column. Migration may not have run successfully.');
        }

        // Also store in session for backward compatibility (for appointment booking)
        session()->put('questionnaire_submitted_' . $categoryId, [
            'questionnaire_id' => $questionnaire->id,
            'category_id' => $categoryId,
            'treatment_id' => $category->treatment_id,
            'answers' => $answers,
            'files' => $permanentFiles,
            'flags' => $flagCheck['flags'],
            'version' => $questionnaire->version,
            'submitted_at' => now()->toDateTimeString(),
            'user_id' => Auth::id(),
        ]);

        // Create or update questionnaire submission record
        $submission = \App\Models\QuestionnaireSubmission::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'category_id' => $categoryId,
                'questionnaire_id' => $questionnaire->id,
            ],
            [
                'status' => 'pending',
                'delivery_type' => $submissionFlow === 'prescription_only' ? 'prescription_only' : null,
            ]
        );

        return response()->json([
            'success' => true,
            'has_warnings' => !empty($flagCheck['flags']),
            'flags' => $flagCheck['flags'],
            'message' => $submissionFlow === 'prescription_only'
                ? __('Questionnaire submitted successfully. Please select your medicines.')
                : __('Questionnaire submitted successfully. Please choose your delivery method.'),
            'redirect_url' => $submissionFlow === 'prescription_only'
                ? url('/questionnaire/category/' . $categoryId . '/medicine-selection')
                : url('/questionnaire/category/' . $categoryId . '/delivery-choice'),
        ]);
    }

    /**
     * Check if patient can submit questionnaire for a category
     * Returns submission status and whether submission is allowed
     */
    public function checkSubmissionStatus($categoryId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'can_submit' => false,
                'message' => __('Please login to check submission status'),
            ], 401);
        }

        $canSubmit = \App\Models\QuestionnaireSubmission::canPatientSubmit(Auth::id(), $categoryId);
        $status = \App\Models\QuestionnaireSubmission::getSubmissionStatus(Auth::id(), $categoryId);

        return response()->json([
            'success' => true,
            'can_submit' => $canSubmit['can_submit'],
            'status' => $status,
            'message' => $canSubmit['message'],
            'submitted_at' => $canSubmit['existing_submission']?->submitted_at?->toDateTimeString(),
        ]);
    }
}

