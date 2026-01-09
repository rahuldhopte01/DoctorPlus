<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\Questionnaire;
use App\Services\QuestionnaireService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $doctor = Doctor::with('category')->findOrFail($doctorId);
        
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
     * Show questionnaire form for a category (Phase 4)
     * Redirects to first section
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

        // Redirect to first section (section index 0)
        return redirect()->to('/questionnaire/category/' . $categoryId . '/section/0');
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

        // Handle file uploads (Issue 3: File upload support)
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
                $answers[$questionId] = $uploadDir . '/' . $filename;
            }
        }

        // Get current section and validate required fields
        $sections = $questionnaire->sections;
        if ($sectionIndex >= 0 && $sectionIndex < $sections->count()) {
            $currentSection = $sections[$sectionIndex];
            $errors = [];
            
            foreach ($currentSection->questions as $question) {
                $answer = $answers[$question->id] ?? null;
                if ($question->required && empty($answer)) {
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

        // Merge with existing answers
        $existingAnswers = session()->get('questionnaire_answers_' . $categoryId, []);
        $mergedAnswers = array_merge($existingAnswers['answers'] ?? [], $answers);
        
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

        if (!$questionnaire) {
            return response()->json([
                'success' => false,
                'message' => __('Questionnaire not found'),
            ], 404);
        }

        // Get answers from session (all sections saved incrementally)
        $savedData = session()->get('questionnaire_answers_' . $categoryId, []);
        $answers = $savedData['answers'] ?? [];
        
        // Also merge any answers from request (in case session was cleared)
        $requestAnswers = $request->input('answers', []);
        if (!empty($requestAnswers)) {
            $answers = array_merge($answers, $requestAnswers);
        }
        $files = $request->file('files', []);

        // Handle file uploads if provided in final submit (Issue 3: File upload)
        if (!empty($files)) {
            $uploadDir = 'questionnaire_uploads/temp/' . Auth::id() . '/' . $categoryId;
            if (!is_dir(public_path($uploadDir))) {
                mkdir(public_path($uploadDir), 0755, true);
            }
            
            foreach ($files as $questionId => $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($uploadDir), $filename);
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

        // Store final answers in session with submission flag
        session()->put('questionnaire_submitted_' . $categoryId, [
            'questionnaire_id' => $questionnaire->id,
            'category_id' => $categoryId,
            'treatment_id' => $category->treatment_id,
            'answers' => $answers,
            'files' => $savedData['files'] ?? [],
            'flags' => $flagCheck['flags'],
            'version' => $questionnaire->version,
            'submitted_at' => now()->toDateTimeString(),
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'has_warnings' => !empty($flagCheck['flags']),
            'flags' => $flagCheck['flags'],
            'redirect_url' => url('/questionnaire/category/' . $categoryId . '/success'),
        ]);
    }
}

