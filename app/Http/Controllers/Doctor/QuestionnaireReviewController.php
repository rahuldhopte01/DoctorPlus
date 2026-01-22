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
     * 
     * Visibility Rules:
     * - SUB_DOCTOR: Sees questionnaires where:
     *   - Hospital matches (or NULL for legacy data)
     *   - Category is in doctor's assigned categories
     *   - Treatment (through category) is in doctor's assigned treatments
     *   - Status is "pending" (unlocked, not yet reviewed) OR status is "IN_REVIEW" (any sub doctor in same hospital is reviewing it)
     *   - NOTE: Approved/rejected questionnaires are only visible to the reviewing doctor, not other sub doctors
     *   - NOTE: When a questionnaire is IN_REVIEW, ALL sub doctors in the same hospital can see it
     * - ADMIN_DOCTOR: Sees all questionnaires in their hospital (regardless of status or assignment)
     */
    public function index()
    {
        $doctor = Doctor::where('user_id', auth()->user()->id)
            ->with(['hospital', 'categories', 'treatments'])
            ->first();
        
        if (!$doctor) {
            return redirect('doctor_home')->with('error', __('Doctor profile not found'));
        }
        
        if (!$doctor->hospital_id) {
            return redirect('doctor_home')->with('error', __('Doctor is not assigned to a hospital'));
        }
        
        $submissions = collect([]);
        
        // Build base query - no appointment_id, valid statuses
        $baseQuery = QuestionnaireAnswer::whereNull('questionnaire_answers.appointment_id')
            ->whereIn('questionnaire_answers.status', ['pending', 'under_review', 'IN_REVIEW', 'approved', 'rejected', 'REVIEW_COMPLETED'])
            ->with(['user', 'category', 'questionnaire', 'question.section', 'reviewingDoctor']);
        
        // Apply role-based visibility
        if ($doctor->isSubDoctor()) {
            // SUB_DOCTOR: Must match category AND treatment assignments
            // Get doctor's assigned category IDs and treatment IDs
            $doctorCategoryIds = $doctor->categories->pluck('id')->toArray();
            $doctorTreatmentIds = $doctor->treatments->pluck('id')->toArray();
            
            if (empty($doctorCategoryIds) || empty($doctorTreatmentIds)) {
                // Doctor must have BOTH category and treatment assignments to see questionnaires
                $answers = collect([]);
            } else {
                // Filter by category and treatment assignments
                // Use join with category table to check treatment_id efficiently
                $baseQuery->join('category', 'questionnaire_answers.category_id', '=', 'category.id')
                    ->where(function($query) use ($doctor, $doctorCategoryIds, $doctorTreatmentIds) {
                        // Show if already assigned to this doctor (they're reviewing it) - regardless of status
                        $query->where('questionnaire_answers.reviewing_doctor_id', $doctor->id)
                            ->orWhere(function($q) use ($doctor, $doctorCategoryIds, $doctorTreatmentIds) {
                                // OR if hospital matches AND:
                                // - Status is pending (unlocked) AND matches category/treatment
                                // - OR Status is IN_REVIEW (any sub doctor in same hospital is reviewing - no category/treatment requirement)
                                $q->where(function($hospitalQuery) use ($doctor) {
                                    // Hospital must match OR be NULL (for legacy data)
                                    $hospitalQuery->where('questionnaire_answers.hospital_id', $doctor->hospital_id)
                                        ->orWhereNull('questionnaire_answers.hospital_id');
                                })
                                ->where(function($statusQuery) use ($doctor, $doctorCategoryIds, $doctorTreatmentIds) {
                                    // Pending status: Must match category AND treatment
                                    $statusQuery->where(function($pendingQuery) use ($doctorCategoryIds, $doctorTreatmentIds) {
                                        $pendingQuery->where('questionnaire_answers.status', 'pending')
                                            ->whereNull('questionnaire_answers.reviewing_doctor_id')
                                            ->whereIn('questionnaire_answers.category_id', $doctorCategoryIds)
                                            ->whereIn('category.treatment_id', $doctorTreatmentIds);
                                    })
                                    // IN_REVIEW status: All sub doctors in same hospital can see it (no category/treatment requirement)
                                    ->orWhere(function($inReviewQuery) use ($doctor) {
                                        $inReviewQuery->where('questionnaire_answers.status', 'IN_REVIEW')
                                            ->where('questionnaire_answers.hospital_id', $doctor->hospital_id)
                                            ->whereNotNull('questionnaire_answers.reviewing_doctor_id');
                                    });
                                });
                            });
                    })
                    ->select('questionnaire_answers.*') // Select only questionnaire_answers columns
                    ->distinct(); // Avoid duplicates from join
                
                $answers = $baseQuery->orderBy('questionnaire_answers.submitted_at', 'desc')->get();
            }
        } else {
            // ADMIN_DOCTOR: See all questionnaires in hospital (or NULL hospital_id for legacy)
            $baseQuery->where(function($query) use ($doctor) {
                $query->where('questionnaire_answers.hospital_id', $doctor->hospital_id)
                    ->orWhereNull('questionnaire_answers.hospital_id');
            });
            $answers = $baseQuery->orderBy('questionnaire_answers.submitted_at', 'desc')->get();
        }
        
        // Group by user_id, category_id, questionnaire_id, and submitted_at (grouping key)
        $submissions = $answers->groupBy(function($answer) {
            $submittedAt = $answer->submitted_at ? $answer->submitted_at->format('Y-m-d H:i:s') : $answer->created_at->format('Y-m-d H:i:s');
            return $answer->user_id . '_' . $answer->category_id . '_' . $answer->questionnaire_id . '_' . $submittedAt;
        })
        ->map(function($group) use ($doctor) {
            $first = $group->first();
            $isLocked = $first->isLocked();
            $isLockedByMe = $first->isLockedBy($doctor->id);
            $canEdit = $doctor->isAdminDoctor() ? false : $isLockedByMe; // Admin can't edit, sub-doctor can only edit if locked by them
            
            return [
                'user' => $first->user,
                'category' => $first->category,
                'questionnaire' => $first->questionnaire,
                'status' => $first->status,
                'submitted_at' => $first->submitted_at ?? $first->created_at,
                'answers' => $group->keyBy('question_id'),
                'flagged_count' => $group->where('is_flagged', true)->count(),
                'is_locked' => $isLocked,
                'is_locked_by_me' => $isLockedByMe,
                'reviewing_doctor' => $first->reviewingDoctor,
                'can_edit' => $canEdit,
            ];
        })
        ->values();
        
        return view('doctor.questionnaire.index', compact('submissions', 'doctor'));
    }
    
    /**
     * Show questionnaire answers for review (without appointment).
     * 
     * Locks the questionnaire when opened by a sub-doctor.
     * Admin doctors can view but not edit locked questionnaires.
     */
    public function showSubmission($userId, $categoryId, $questionnaireId)
    {
        $doctor = Doctor::where('user_id', auth()->user()->id)
            ->with(['hospital', 'categories', 'treatments'])
            ->first();
        
        if (!$doctor || !$doctor->hospital_id) {
            abort(403, 'Unauthorized access - Doctor not assigned to hospital');
        }
        
        // Get all answers for this submission - must be from same hospital
        $answers = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->where('hospital_id', $doctor->hospital_id) // Hospital-scoped
            ->whereNull('appointment_id')
            ->with(['user', 'category', 'questionnaire', 'question.section', 'reviewingDoctor'])
            ->orderBy('question_id')
            ->get();
        
        if ($answers->isEmpty()) {
            return redirect()->route('doctor.questionnaire.index')->with('error', __('Questionnaire submission not found'));
        }
        
        $firstAnswer = $answers->first();
        $questionnaireCategory = $firstAnswer->category;
        $questionnaireTreatment = $questionnaireCategory ? $questionnaireCategory->treatment : null;
        
        // For SUB_DOCTOR: Validate category and treatment assignments
        if ($doctor->isSubDoctor()) {
            $doctorCategoryIds = $doctor->categories->pluck('id')->toArray();
            $doctorTreatmentIds = $doctor->treatments->pluck('id')->toArray();
            
            // Check if doctor is assigned to this category
            if (!in_array($categoryId, $doctorCategoryIds)) {
                return redirect()->route('doctor.questionnaire.index')
                    ->with('error', __('You are not assigned to this category'));
            }
            
            // Check if doctor is assigned to this treatment (through category)
            if ($questionnaireTreatment && !in_array($questionnaireTreatment->id, $doctorTreatmentIds)) {
                return redirect()->route('doctor.questionnaire.index')
                    ->with('error', __('You are not assigned to this treatment'));
            }
        }
        
        // Check if locked by another doctor
        if ($firstAnswer->isLocked() && !$firstAnswer->isLockedBy($doctor->id)) {
            if ($doctor->isSubDoctor()) {
                // Sub-doctor can view questionnaires under review by other sub doctors in same hospital
                // But cannot edit them (read-only access)
                // Continue to view mode (read-only)
            } else {
                // Admin can view but not edit
                // Continue to view mode
            }
        }
        
        // Lock mechanism: If sub-doctor opens an unlocked questionnaire, lock it
        if ($doctor->isSubDoctor() && !$firstAnswer->isLocked()) {
            // Lock all answers in this submission
            DB::transaction(function() use ($answers, $doctor, $userId, $categoryId, $questionnaireId) {
                QuestionnaireAnswer::where('user_id', $userId)
                    ->where('category_id', $categoryId)
                    ->where('questionnaire_id', $questionnaireId)
                    ->whereNull('appointment_id')
                    ->update([
                        'status' => 'IN_REVIEW',
                        'reviewing_doctor_id' => $doctor->id,
                        'hospital_id' => $doctor->hospital_id, // Ensure hospital_id is set
                    ]);
            });
            
            // Reload answers to get updated status
            $answers = QuestionnaireAnswer::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('questionnaire_id', $questionnaireId)
                ->whereNull('appointment_id')
                ->with(['user', 'category', 'questionnaire', 'question.section', 'reviewingDoctor'])
                ->orderBy('question_id')
                ->get();
            
            $firstAnswer = $answers->first();
        }
        
        $groupedAnswers = $this->groupAnswersBySection($answers);
        $hasFlaggedAnswers = $answers->where('is_flagged', true)->isNotEmpty();
        
        // Determine if doctor can edit:
        // - Sub-doctor can only edit if they locked it (reviewing_doctor_id = their id) or if unlocked
        // - If another sub doctor is reviewing it, this doctor can view but not edit (read-only)
        // - Admin doctor cannot edit (view-only)
        $canEdit = $doctor->isSubDoctor() && ($firstAnswer->isLockedBy($doctor->id) || !$firstAnswer->isLocked());
        
        // Get prescription for this questionnaire if it exists
        $prescription = Prescription::where('user_id', $userId)
            ->where('doctor_id', $doctor->id)
            ->whereNull('appointment_id')
            ->where('status', '!=', 'expired')
            ->first();
        
        return view('doctor.questionnaire.review_submission', compact('answers', 'firstAnswer', 'groupedAnswers', 'hasFlaggedAnswers', 'doctor', 'prescription', 'canEdit'));
    }
    
    /**
     * Update status of questionnaire submission.
     * 
     * When status is set to approved/rejected, unlock the questionnaire (REVIEW_COMPLETED).
     */
    public function updateStatus(Request $request, $userId, $categoryId, $questionnaireId)
    {
        $request->validate([
            'status' => 'required|in:pending,under_review,IN_REVIEW,approved,rejected,REVIEW_COMPLETED',
        ]);
        
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        
        if (!$doctor || !$doctor->hospital_id) {
            abort(403, 'Unauthorized access');
        }
        
        // Get the submission to check permissions
        $firstAnswer = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->where('hospital_id', $doctor->hospital_id) // Hospital-scoped
            ->whereNull('appointment_id')
            ->first();
        
        if (!$firstAnswer) {
            abort(404, 'Questionnaire submission not found');
        }
        
        // Check permissions
        if ($doctor->isSubDoctor()) {
            // Sub-doctor can only update if they locked it
            if (!$firstAnswer->isLockedBy($doctor->id)) {
                abort(403, 'You can only update questionnaires you are reviewing');
            }
        } else {
            // Admin doctor cannot edit locked questionnaires (only view)
            if ($firstAnswer->isLocked() && !$firstAnswer->isLockedBy($doctor->id)) {
                abort(403, 'Cannot edit questionnaire being reviewed by another doctor');
            }
        }
        
        // Determine if we should unlock
        $shouldUnlock = in_array($request->status, ['approved', 'rejected', 'REVIEW_COMPLETED']);
        
        DB::transaction(function() use ($userId, $categoryId, $questionnaireId, $request, $shouldUnlock, $doctor) {
            $updateData = ['status' => $request->status];
            
            if ($shouldUnlock) {
                $updateData['reviewing_doctor_id'] = null; // Unlock if completed
            }
            // If not unlocking, keep the current reviewing_doctor_id (don't update it)
            
            QuestionnaireAnswer::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('questionnaire_id', $questionnaireId)
                ->whereNull('appointment_id')
                ->update($updateData);
        });
        
        $message = __('Status updated successfully');
        if ($shouldUnlock) {
            $message = __('Review completed and questionnaire unlocked');
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Show prescription creation form for approved questionnaire.
     */
    public function createPrescription($userId, $categoryId, $questionnaireId)
    {
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        
        if (!$doctor || !$doctor->hospital_id) {
            abort(403, 'Unauthorized access - Doctor not assigned to hospital');
        }
        
        // Get the questionnaire answers to verify status - hospital-scoped
        $answers = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->where('hospital_id', $doctor->hospital_id) // Hospital-scoped
            ->whereNull('appointment_id')
            ->with(['user', 'questionnaire'])
            ->first();
            
        if (!$answers) {
            return redirect()->route('doctor.questionnaire.index')
                ->with('error', __('Questionnaire submission not found'));
        }
        
        if ($answers->status !== 'approved') {
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
        
        if (!$doctor || !$doctor->hospital_id) {
            abort(403, 'Unauthorized access - Doctor not assigned to hospital');
        }
        
        // Verify questionnaire is approved - hospital-scoped
        $answers = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->where('hospital_id', $doctor->hospital_id) // Hospital-scoped
            ->whereNull('appointment_id')
            ->first();
            
        if (!$answers) {
            return redirect()->route('doctor.questionnaire.index')
                ->with('error', __('Questionnaire submission not found'));
        }
        
        if ($answers->status !== 'approved') {
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



