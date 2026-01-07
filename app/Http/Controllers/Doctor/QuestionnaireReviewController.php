<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\QuestionnaireService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QuestionnaireReviewController extends Controller
{
    protected $questionnaireService;

    public function __construct(QuestionnaireService $questionnaireService)
    {
        $this->questionnaireService = $questionnaireService;
    }

    /**
     * Show questionnaire answers for an appointment.
     */
    public function show($appointmentId)
    {
        $appointment = Appointment::with([
            'user',
            'doctor',
            'questionnaire',
            'questionnaireAnswers.question.section'
        ])->findOrFail($appointmentId);

        // Verify the logged-in doctor owns this appointment
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        if (!$doctor || $appointment->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access to this appointment');
        }

        if (!$appointment->questionnaire_id) {
            return redirect()->back()->with('error', __('No questionnaire was completed for this appointment'));
        }

        $groupedAnswers = $this->questionnaireService->getFormattedAnswersForReview($appointment);
        $hasFlaggedAnswers = $appointment->questionnaireAnswers()->where('is_flagged', true)->exists();

        return view('doctor.questionnaire.review', compact('appointment', 'groupedAnswers', 'hasFlaggedAnswers'));
    }

    /**
     * Get questionnaire summary for appointment list (AJAX).
     */
    public function summary($appointmentId)
    {
        $appointment = Appointment::with(['questionnaireAnswers'])->findOrFail($appointmentId);

        $totalQuestions = $appointment->questionnaireAnswers->count();
        $flaggedCount = $appointment->questionnaireAnswers->where('is_flagged', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'has_questionnaire' => $appointment->questionnaire_id !== null,
                'total_questions' => $totalQuestions,
                'flagged_count' => $flaggedCount,
                'completed_at' => $appointment->questionnaire_completed_at,
            ]
        ]);
    }
}

