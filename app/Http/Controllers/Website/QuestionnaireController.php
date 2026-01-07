<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Questionnaire;
use App\Services\QuestionnaireService;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    protected $questionnaireService;

    public function __construct(QuestionnaireService $questionnaireService)
    {
        $this->questionnaireService = $questionnaireService;
    }

    /**
     * Show questionnaire form for a doctor's treatment.
     */
    public function show($doctorId)
    {
        $doctor = Doctor::with('treatment')->findOrFail($doctorId);
        
        if (!$doctor->treatment) {
            return redirect()->back()->with('error', __('Doctor has no treatment assigned'));
        }

        $questionnaire = $this->questionnaireService->getQuestionnaireForTreatment($doctor->treatment_id);

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
        $questionnaire = $this->questionnaireService->getQuestionnaireForTreatment($doctor->treatment_id);

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
    public function getQuestionnaire($treatmentId)
    {
        $questionnaire = $this->questionnaireService->getQuestionnaireForTreatment($treatmentId);

        if (!$questionnaire) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'No questionnaire found for this treatment',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $questionnaire,
        ]);
    }
}

