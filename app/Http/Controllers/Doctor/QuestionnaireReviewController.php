<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\QuestionnaireAnswer;
use App\Models\Category;
use App\Models\User;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Services\QuestionnaireService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class QuestionnaireReviewController extends Controller
{
    protected $questionnaireService;

    public function __construct(QuestionnaireService $questionnaireService)
    {
        $this->questionnaireService = $questionnaireService;
    }
    
    /**
     * List all pending questionnaire submissions for doctor review.
     */
    public function index()
    {
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        
        if (!$doctor) {
            return redirect('doctor_home')->with('error', __('Doctor profile not found'));
        }
        
        $submissions = collect([]);
        
        if ($doctor->category_id) {
            // Get all questionnaire answers (pending, approved, rejected) for this doctor's category
            $answers = QuestionnaireAnswer::where('category_id', $doctor->category_id)
                ->whereNull('appointment_id')
                ->whereIn('status', ['pending', 'under_review', 'approved', 'rejected'])
                ->with(['user', 'category', 'questionnaire', 'question.section'])
                ->orderBy('submitted_at', 'desc')
                ->get();
            
            // Group by user_id, category_id, questionnaire_id, and submitted_at (grouping key)
            $submissions = $answers->groupBy(function($answer) {
                $submittedAt = $answer->submitted_at ? $answer->submitted_at->format('Y-m-d H:i:s') : $answer->created_at->format('Y-m-d H:i:s');
                return $answer->user_id . '_' . $answer->category_id . '_' . $answer->questionnaire_id . '_' . $submittedAt;
            })
            ->map(function($group) {
                $first = $group->first();
                return [
                    'user' => $first->user,
                    'category' => $first->category,
                    'questionnaire' => $first->questionnaire,
                    'status' => $first->status,
                    'submitted_at' => $first->submitted_at ?? $first->created_at,
                    'answers' => $group->keyBy('question_id'),
                    'flagged_count' => $group->where('is_flagged', true)->count(),
                ];
            })
            ->values();
        }
        
        return view('doctor.questionnaire.index', compact('submissions', 'doctor'));
    }
    
    /**
     * Show questionnaire answers for review (without appointment).
     */
    public function showSubmission($userId, $categoryId, $questionnaireId)
    {
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        
        if (!$doctor || $doctor->category_id != $categoryId) {
            abort(403, 'Unauthorized access');
        }
        
        // Get all answers for this submission
        $answers = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->whereNull('appointment_id')
            ->with(['user', 'category', 'questionnaire', 'question.section'])
            ->orderBy('question_id')
            ->get();
        
        if ($answers->isEmpty()) {
            return redirect()->route('doctor.questionnaire.index')->with('error', __('Questionnaire submission not found'));
        }
        
        $firstAnswer = $answers->first();
        $groupedAnswers = $this->groupAnswersBySection($answers);
        $hasFlaggedAnswers = $answers->where('is_flagged', true)->isNotEmpty();
        
        // Get prescription for this questionnaire if it exists
        $prescription = Prescription::where('user_id', $userId)
            ->where('doctor_id', $doctor->id)
            ->whereNull('appointment_id')
            ->where('status', '!=', 'expired')
            ->first();
        
        return view('doctor.questionnaire.review_submission', compact('answers', 'firstAnswer', 'groupedAnswers', 'hasFlaggedAnswers', 'doctor', 'prescription'));
    }
    
    /**
     * Update status of questionnaire submission.
     */
    public function updateStatus(Request $request, $userId, $categoryId, $questionnaireId)
    {
        $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected',
        ]);
        
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        
        if (!$doctor || $doctor->category_id != $categoryId) {
            abort(403, 'Unauthorized access');
        }
        
        QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->whereNull('appointment_id')
            ->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', __('Status updated successfully'));
    }
    
    /**
     * Show prescription creation form for approved questionnaire.
     */
    public function createPrescription($userId, $categoryId, $questionnaireId)
    {
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        
        if (!$doctor || $doctor->category_id != $categoryId) {
            abort(403, 'Unauthorized access');
        }
        
        // Get the questionnaire answers to verify status
        $answers = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->whereNull('appointment_id')
            ->with(['user', 'questionnaire'])
            ->first();
            
        if (!$answers || $answers->status !== 'approved') {
            return redirect()->route('doctor.questionnaire.index')
                ->with('error', __('Questionnaire must be approved before creating prescription'));
        }
        
        // Check if prescription already exists for this questionnaire
        $existingPrescription = Prescription::where('user_id', $userId)
            ->where('doctor_id', $doctor->id)
            ->whereNull('appointment_id')
            ->where('status', '!=', 'expired')
            ->first();
            
        if ($existingPrescription) {
            return redirect()->route('doctor.questionnaire.index')
                ->with('info', __('Prescription already exists for this questionnaire.'));
        }
        
        $user = User::find($userId);
        $medicines = Medicine::where('status', 1)->get();
        
        return view('doctor.questionnaire.create_prescription', compact('user', 'doctor', 'medicines', 'answers', 'userId', 'categoryId', 'questionnaireId'));
    }
    
    /**
     * Store prescription for approved questionnaire.
     */
    public function storePrescription(Request $request, $userId, $categoryId, $questionnaireId)
    {
        $request->validate([
            'validity_days' => 'required|integer|min:1|max:365',
            'medicines' => 'required|array|min:1',
            'medicines.*' => 'required|exists:medicine,id',
            'strength' => 'required|array',
            'strength.*' => 'required|string|max:100',
        ]);
        
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        
        if (!$doctor || $doctor->category_id != $categoryId) {
            abort(403, 'Unauthorized access');
        }
        
        // Verify questionnaire is approved
        $answers = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->whereNull('appointment_id')
            ->first();
            
        if (!$answers || $answers->status !== 'approved') {
            return redirect()->route('doctor.questionnaire.index')
                ->with('error', __('Questionnaire must be approved before creating prescription'));
        }
        
        // Check if prescription already exists
        $existingPrescription = Prescription::where('user_id', $userId)
            ->where('doctor_id', $doctor->id)
            ->whereNull('appointment_id')
            ->where('status', '!=', 'expired')
            ->first();
            
        if ($existingPrescription) {
            return redirect()->route('doctor.questionnaire.index')
                ->with('info', __('Prescription already exists for this questionnaire.'));
        }
        
        // Get approval date (from questionnaire submission or now)
        // valid_from = doctor approval date (use now() as approval date)
        $validFrom = now();
        $validityDays = (int) $request->validity_days;
        $validUntil = $validFrom->copy()->addDays($validityDays);
        
        // Build medicines array with strength
        $medicines = [];
        $medicineNames = $request->input('medicine_names', []);
        $strengths = $request->input('strength', []);
        
        for ($i = 0; $i < count($request->medicines); $i++) {
            $medicineId = $request->medicines[$i];
            $medicine = Medicine::find($medicineId);
            $medicineName = $medicine ? $medicine->name : ($medicineNames[$i] ?? '');
            $strength = $strengths[$i] ?? '';
            
            $medicineData = [
                'medicine' => $medicineName,
                'strength' => $strength,
            ];
            
            $medicines[] = $medicineData;
        }
        
        // Create prescription
        $prescriptionData = [
            'appointment_id' => null, // No appointment for questionnaire-based prescriptions
            'user_id' => $userId,
            'doctor_id' => $doctor->id,
            'medicines' => json_encode($medicines),
            'status' => 'approved_pending_payment',
            'validity_days' => $validityDays,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
        ];
        
        $prescription = Prescription::create($prescriptionData);
        
        // Generate PDF (if needed - you can implement this later)
        // For now, just redirect back
        
        return redirect()->route('doctor.questionnaire.index')
            ->with('success', __('Prescription created successfully. Patient will be notified and can pay to download.'));
    }
    
    /**
     * Group answers by section.
     */
    protected function groupAnswersBySection($answers)
    {
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
                'file_name' => $answer->answer_value,
            ];
        }
        
        return $grouped;
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



