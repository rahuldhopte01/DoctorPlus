<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SuperAdmin\CustomController;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\QuestionnaireAnswer;
use App\Models\Category;
use App\Models\User;
use App\Models\CannaleoMedicine;
use App\Models\CannaleoPharmacy;
use App\Models\CannaleoPrescriptionLog;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\QuestionnaireSubmission;
use App\Models\PurchaseMedicine;
use App\Models\MedicineChild;
use App\Models\PharmacySettle;
use App\Services\Curobo\CuroboPrescriptionApi;
use App\Services\Curobo\CuroboPrescriptionPayloadBuilder;
use App\Services\PrescriptionPdfService;
use App\Services\QuestionnaireService;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class QuestionnaireReviewController extends Controller
{
    protected $questionnaireService;

    public function __construct(QuestionnaireService $questionnaireService)
    {
        $this->questionnaireService = $questionnaireService;
    }
    
    /**
     * List questionnaire submissions for doctor review.
     *
     * Visibility:
     * - Pending: visible to ALL doctors (sub + admin) of that category.
     * - When any doctor opens a pending questionnaire, status → IN_REVIEW and reviewing_doctor_id is set.
     * - IN_REVIEW: only the doctor who opened it and the admin doctor can see it; hidden from other sub doctors.
     *
     * - SUB_DOCTOR: Sees (a) questionnaires they are reviewing, OR (b) pending (unlocked) in their categories.
     * - ADMIN_DOCTOR: Sees all questionnaires in their hospital.
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
        $reviewSubmissions = collect([]);
        $pendingSubmissions = collect([]);
        $reviewDoctors = collect([]);
        $selectedReviewDoctorId = null;
        
        $reviewStatuses = ['under_review', 'IN_REVIEW', 'approved', 'rejected', 'REVIEW_COMPLETED'];
        $baseQuery = QuestionnaireAnswer::whereNull('questionnaire_answers.appointment_id')
            ->whereIn('questionnaire_answers.status', array_merge(['pending'], $reviewStatuses))
            ->with(['user', 'category', 'questionnaire', 'question.section', 'reviewingDoctor']);
        
        $baseQuery->where(function ($query) use ($doctor) {
            $query->where('questionnaire_answers.hospital_id', $doctor->hospital_id)
                ->orWhereNull('questionnaire_answers.hospital_id');
        });
        
        if ($doctor->isSubDoctor()) {
            $doctorCategoryIds = $doctor->categories->pluck('id')->toArray();
            
            $reviewAnswers = (clone $baseQuery)
                ->where('questionnaire_answers.reviewing_doctor_id', $doctor->id)
                ->whereIn('questionnaire_answers.status', $reviewStatuses)
                ->orderBy('questionnaire_answers.submitted_at', 'desc')
                ->get();
            
            $pendingAnswers = (clone $baseQuery)
                ->where('questionnaire_answers.status', 'pending')
                ->whereNull('questionnaire_answers.reviewing_doctor_id')
                ->whereIn('questionnaire_answers.category_id', $doctorCategoryIds)
                ->orderBy('questionnaire_answers.submitted_at', 'desc')
                ->get();
        } else {
            $reviewDoctors = Doctor::byHospital($doctor->hospital_id)
                ->subDoctors()
                ->orderBy('name')
                ->get();
            
            $selectedReviewDoctorId = (int) request()->get('review_doctor_id', $doctor->id);
            $selectedDoctor = Doctor::byHospital($doctor->hospital_id)
                ->where('id', $selectedReviewDoctorId)
                ->first();
            if (!$selectedDoctor) {
                $selectedReviewDoctorId = $doctor->id;
            }
            
            $reviewAnswers = (clone $baseQuery)
                ->where('questionnaire_answers.reviewing_doctor_id', $selectedReviewDoctorId)
                ->whereIn('questionnaire_answers.status', $reviewStatuses)
                ->orderBy('questionnaire_answers.submitted_at', 'desc')
                ->get();
            
            $pendingAnswers = (clone $baseQuery)
                ->where('questionnaire_answers.status', 'pending')
                ->whereNull('questionnaire_answers.reviewing_doctor_id')
                ->orderBy('questionnaire_answers.submitted_at', 'desc')
                ->get();
        }
        
        $buildSubmissions = function ($answers) use ($doctor) {
            if ($answers->isEmpty()) {
                return collect([]);
            }
            
            $submissionRecords = QuestionnaireSubmission::whereIn('user_id', $answers->pluck('user_id')->unique())
                ->whereIn('category_id', $answers->pluck('category_id')->unique())
                ->whereIn('questionnaire_id', $answers->pluck('questionnaire_id')->unique())
                ->orderBy('created_at', 'desc')
                ->get();
            
            $submissionMap = $submissionRecords->groupBy(function ($submission) {
                return $submission->user_id . '_' . $submission->category_id . '_' . $submission->questionnaire_id;
            })->map(function ($group) {
                return $group->first();
            });
            
            $medicineIds = $submissionRecords->flatMap(function ($submission) {
                return collect($submission->selected_medicines ?? [])->pluck('medicine_id')->filter();
            })->unique()->values();
            $cannaleoMedicineIds = $submissionRecords->flatMap(function ($submission) {
                return collect($submission->selected_medicines ?? [])->pluck('cannaleo_medicine_id')->filter();
            })->unique()->values();

            $medicineMap = $medicineIds->isNotEmpty()
                ? Medicine::with('brand')->whereIn('id', $medicineIds)->get()->keyBy('id')
                : collect([]);
            $cannaleoMedicineMap = $cannaleoMedicineIds->isNotEmpty()
                ? \App\Models\CannaleoMedicine::with('cannaleoPharmacy')->whereIn('id', $cannaleoMedicineIds)->get()->keyBy('id')
                : collect([]);

            return $answers->groupBy(function ($answer) {
                $submittedAt = $answer->submitted_at ? $answer->submitted_at->format('Y-m-d H:i:s') : $answer->created_at->format('Y-m-d H:i:s');
                return $answer->user_id . '_' . $answer->category_id . '_' . $answer->questionnaire_id . '_' . $submittedAt;
            })->map(function ($group) use ($doctor, $submissionMap, $medicineMap, $cannaleoMedicineMap) {
                $first = $group->first();
                $isLocked = $first->isLocked();
                $isLockedByMe = $first->isLockedBy($doctor->id);
                $canEdit = $doctor->isAdminDoctor() ? $isLockedByMe : $isLockedByMe;

                $submissionKey = $first->user_id . '_' . $first->category_id . '_' . $first->questionnaire_id;
                $submission = $submissionMap->get($submissionKey);
                $selectedMedicines = [];
                if ($submission && $submission->selected_medicines) {
                    foreach ($submission->selected_medicines as $selected) {
                        if (!empty($selected['medicine_id'])) {
                            $medicine = $medicineMap->get($selected['medicine_id']);
                            if ($medicine) {
                                $selectedMedicines[] = [
                                    'name' => $medicine->name,
                                    'strength' => $medicine->strength ?? '',
                                    'brand' => $medicine->brand->name ?? null,
                                ];
                            }
                        }
                        if (!empty($selected['cannaleo_medicine_id'])) {
                            $cm = $cannaleoMedicineMap->get($selected['cannaleo_medicine_id']);
                            if ($cm) {
                                $selectedMedicines[] = [
                                    'name' => $cm->name,
                                    'strength' => ($cm->thc !== null || $cm->cbd !== null) ? ('THC ' . ($cm->thc ?? 0) . '% / CBD ' . ($cm->cbd ?? 0) . '%') : '',
                                    'brand' => $cm->cannaleoPharmacy ? $cm->cannaleoPharmacy->name . ' (Cannaleo)' : 'Cannaleo',
                                ];
                            }
                        }
                    }
                }
                
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
                    'submission' => $submission,
                    'selected_medicines' => $selectedMedicines,
                ];
            })->values();
        };
        
        $reviewSubmissions = $buildSubmissions($reviewAnswers ?? collect([]));
        $pendingSubmissions = $buildSubmissions($pendingAnswers ?? collect([]));
        
        return view('doctor.questionnaire.index', compact(
            'reviewSubmissions',
            'pendingSubmissions',
            'doctor',
            'reviewDoctors',
            'selectedReviewDoctorId'
        ));
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
        
        // Get all answers for this user/category/questionnaire (all submission dates)
        // Query fresh to ensure we get the latest data, especially after status updates
        // Handle both hospital_id match and null hospital_id (for backward compatibility)
        $allAnswers = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->where(function($query) use ($doctor) {
                $query->where('hospital_id', $doctor->hospital_id)
                      ->orWhereNull('hospital_id');
            })
            ->whereNull('appointment_id')
            ->with(['user', 'category', 'questionnaire', 'question.section', 'reviewingDoctor'])
            ->orderBy('submitted_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('question_id')
            ->get();
        
        if ($allAnswers->isEmpty()) {
            return redirect()->route('doctor.questionnaire.index')->with('error', __('Questionnaire submission not found'));
        }
        
        // Ensure all answers have hospital_id set (for consistency)
        $needsHospitalIdUpdate = false;
        foreach ($allAnswers as $answer) {
            if (!$answer->hospital_id) {
                $answer->hospital_id = $doctor->hospital_id;
                $needsHospitalIdUpdate = true;
            }
        }
        if ($needsHospitalIdUpdate) {
            DB::transaction(function () use ($userId, $categoryId, $questionnaireId, $doctor) {
                QuestionnaireAnswer::where('user_id', $userId)
                    ->where('category_id', $categoryId)
                    ->where('questionnaire_id', $questionnaireId)
                    ->whereNull('appointment_id')
                    ->whereNull('hospital_id')
                    ->update(['hospital_id' => $doctor->hospital_id]);
            });
            $allAnswers = QuestionnaireAnswer::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('questionnaire_id', $questionnaireId)
                ->where(function($query) use ($doctor) {
                    $query->where('hospital_id', $doctor->hospital_id)
                          ->orWhereNull('hospital_id');
                })
                ->whereNull('appointment_id')
                ->with(['user', 'category', 'questionnaire', 'question.section', 'reviewingDoctor'])
                ->orderBy('submitted_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('question_id')
                ->get();
        }
        
        // Group answers by submission date (patient history by date)
        $submissionsByDate = $allAnswers->groupBy(function ($answer) {
            $dt = $answer->submitted_at ?? $answer->created_at;
            return $dt->format('Y-m-d H:i:s');
        })->map(function ($batch, $dateKey) {
            $batch = $batch->sortBy('question_id')->values();
            $first = $batch->first();
            $submittedAt = $first->submitted_at ?? $first->created_at;
            return [
                'submitted_at' => $submittedAt,
                'answers' => $batch,
                'groupedAnswers' => $this->groupAnswersBySection($batch),
                'firstAnswer' => $first,
                'hasFlagged' => $batch->where('is_flagged', true)->isNotEmpty(),
            ];
        })->sortByDesc(function ($item) {
            return $item['submitted_at']->getTimestamp();
        })->values();
        
        // Which submission is "current" (for status, prescription, locking): from query param or latest
        $requestedSubmittedAt = request()->get('submitted_at');
        $currentBatch = null;
        if ($requestedSubmittedAt) {
            foreach ($submissionsByDate as $batch) {
                if ($batch['submitted_at']->format('Y-m-d H:i:s') === $requestedSubmittedAt) {
                    $currentBatch = $batch;
                    break;
                }
            }
        }
        if (!$currentBatch) {
            $currentBatch = $submissionsByDate->first();
        }
        
        $answers = $currentBatch['answers'];
        $firstAnswer = $currentBatch['firstAnswer'];
        
        // SUB_DOCTOR: Must be assigned to this category. If locked by another sub-doctor, deny access.
        if ($doctor->isSubDoctor()) {
            $doctorCategoryIds = $doctor->categories->pluck('id')->toArray();
            if (!in_array((int) $categoryId, $doctorCategoryIds)) {
                return redirect()->route('doctor.questionnaire.index')
                    ->with('error', __('You are not assigned to this category'));
            }
            // Under review by another sub-doctor -> not visible; deny access
            if ($firstAnswer->isLocked() && !$firstAnswer->isLockedBy($doctor->id)) {
                return redirect()->route('doctor.questionnaire.index')
                    ->with('error', __('This questionnaire is under review by another doctor and is not available to you.'));
            }
        }
        
        // Lock when any doctor (sub or admin) opens an unlocked questionnaire: status -> IN_REVIEW, set reviewer
        // Only lock if status is 'pending' - don't lock if already approved/rejected. Lock only the current batch.
        // Admin doctors must not steal a questionnaire that already has a sub-doctor assigned
        // (e.g. the questionnaire was set back to 'pending' but reviewing_doctor_id was not cleared).
        $adminViewingOthersQuestionnaire = $doctor->isAdminDoctor()
            && $firstAnswer->reviewing_doctor_id !== null
            && $firstAnswer->reviewing_doctor_id != $doctor->id;

        if (!$firstAnswer->isLocked() && $firstAnswer->status === 'pending' && !$adminViewingOthersQuestionnaire) {
            $currentSubmittedAtKey = $currentBatch['submitted_at']->format('Y-m-d H:i:s');
            DB::transaction(function () use ($userId, $categoryId, $questionnaireId, $doctor, $currentSubmittedAtKey) {
                $lockQuery = QuestionnaireAnswer::where('user_id', $userId)
                    ->where('category_id', $categoryId)
                    ->where('questionnaire_id', $questionnaireId)
                    ->whereNull('appointment_id')
                    ->where(function($query) use ($doctor) {
                        $query->where('hospital_id', $doctor->hospital_id)
                              ->orWhereNull('hospital_id');
                    });
                $dt = \Carbon\Carbon::parse($currentSubmittedAtKey);
                $lockQuery->where(function ($q) use ($dt) {
                    $q->where(function ($q2) use ($dt) {
                        $q2->where('submitted_at', '>=', $dt->copy()->startOfSecond())
                           ->where('submitted_at', '<=', $dt->copy()->endOfSecond());
                    })->orWhere(function ($q2) use ($dt) {
                        $q2->whereNull('submitted_at')
                           ->where('created_at', '>=', $dt->copy()->startOfSecond())
                           ->where('created_at', '<=', $dt->copy()->endOfSecond());
                    });
                });
                $lockQuery->update([
                    'status' => 'IN_REVIEW',
                    'reviewing_doctor_id' => $doctor->id,
                    'hospital_id' => $doctor->hospital_id,
                ]);
            });
            // Refresh all answers and re-build current batch after locking
            $allAnswers = QuestionnaireAnswer::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('questionnaire_id', $questionnaireId)
                ->where(function($query) use ($doctor) {
                    $query->where('hospital_id', $doctor->hospital_id)
                          ->orWhereNull('hospital_id');
                })
                ->whereNull('appointment_id')
                ->with(['user', 'category', 'questionnaire', 'question.section', 'reviewingDoctor'])
                ->orderBy('submitted_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('question_id')
                ->get();
            $submissionsByDate = $allAnswers->groupBy(function ($answer) {
                $dt = $answer->submitted_at ?? $answer->created_at;
                return $dt->format('Y-m-d H:i:s');
            })->map(function ($batch, $dateKey) {
                $batch = $batch->sortBy('question_id')->values();
                $first = $batch->first();
                $submittedAt = $first->submitted_at ?? $first->created_at;
                return [
                    'submitted_at' => $submittedAt,
                    'answers' => $batch,
                    'groupedAnswers' => $this->groupAnswersBySection($batch),
                    'firstAnswer' => $first,
                    'hasFlagged' => $batch->where('is_flagged', true)->isNotEmpty(),
                ];
            })->sortByDesc(function ($item) {
                return $item['submitted_at']->getTimestamp();
            })->values();
            $currentBatch = $submissionsByDate->firstWhere(function ($b) use ($currentSubmittedAtKey) {
                return $b['submitted_at']->format('Y-m-d H:i:s') === $currentSubmittedAtKey;
            }) ?? $submissionsByDate->first();
            $answers = $currentBatch['answers'];
            $firstAnswer = $currentBatch['firstAnswer'];
            $groupedAnswers = $currentBatch['groupedAnswers'];
            $hasFlaggedAnswers = $currentBatch['hasFlagged'];
        }
        
        $groupedAnswers = $currentBatch['groupedAnswers'];
        $hasFlaggedAnswers = $currentBatch['hasFlagged'];
        
        // Determine if doctor can edit:
        // - Admin doctors can always edit questionnaires in their hospital
        // - Sub-doctors can edit if: they are the reviewer, it's unlocked, OR it's approved (and they have category access)
        $canEdit = false;
        if ($doctor->isAdminDoctor()) {
            // Admin can edit only questionnaires assigned to them
            $canEdit = $firstAnswer->reviewing_doctor_id == $doctor->id;
        } elseif ($doctor->isSubDoctor()) {
            // Sub-doctor can edit if:
            // 1. They are the reviewer (locked by them or approved by them)
            // 2. It's not locked (unlocked/pending)
            // 3. It's approved and they have access to this category (reviewing_doctor_id is kept for approved)
            if ($firstAnswer->isLockedBy($doctor->id) || !$firstAnswer->isLocked()) {
                $canEdit = true;
            } elseif ($firstAnswer->status === 'approved') {
                // For approved questionnaires, reviewing_doctor_id is kept, so check if this doctor is the reviewer
                // OR if they have access to this category (fallback)
                if ($firstAnswer->reviewing_doctor_id == $doctor->id) {
                    $canEdit = true;
                } else {
                    // Fallback: allow if doctor has access to this category
                    $doctorCategoryIds = $doctor->categories->pluck('id')->toArray();
                    if (in_array((int) $categoryId, $doctorCategoryIds)) {
                        $canEdit = true;
                    }
                }
            }
        }
        
        // Get prescription for this questionnaire review only if it was generated for THIS answer batch.
        // Do not show a prescription from a previous questionnaire as "generated" for this review.
        $submittedAt = $firstAnswer->submitted_at ?? $firstAnswer->created_at;
        // Find prescription for this questionnaire regardless of which doctor created it.
        // This ensures the "Prescription Already Created" message shows correctly even when
        // a different doctor (e.g. a sub-doctor) created the prescription.
        $prescription = Prescription::where('user_id', $userId)
            ->whereNull('appointment_id')
            ->where('status', '!=', 'expired')
            ->whereNotNull('questionnaire_submitted_at')
            ->where('questionnaire_submitted_at', '<=', $submittedAt->copy()->addSecond())
            ->where('questionnaire_submitted_at', '>=', $submittedAt->copy()->subSecond())
            ->first();
        
        // Get questionnaire submission data (delivery choice, medicines, pharmacy, address)
        $submission = \App\Models\QuestionnaireSubmission::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->with(['selectedPharmacy', 'deliveryAddress'])
            ->first();
        
        // Get selected medicines with details (internal medicine_id or cannaleo_medicine_id)
        $selectedMedicines = [];
        $selectedCannaleoMedicines = [];
        if ($submission && $submission->selected_medicines) {
            foreach ($submission->selected_medicines as $selected) {
                if (!empty($selected['medicine_id'])) {
                    $medicine = Medicine::with('brand')->find($selected['medicine_id']);
                    if ($medicine) {
                        $selectedMedicines[] = ['medicine' => $medicine];
                    }
                }
                if (!empty($selected['cannaleo_medicine_id'])) {
                    $cannaleoMedicine = \App\Models\CannaleoMedicine::with('cannaleoPharmacy')->find($selected['cannaleo_medicine_id']);
                    if ($cannaleoMedicine) {
                        $selectedCannaleoMedicines[] = [
                            'cannaleo_medicine' => $cannaleoMedicine,
                            'pharmacy' => $cannaleoMedicine->cannaleoPharmacy,
                        ];
                    }
                }
            }
        }

        // Get medicines from the category for medicine assignment
        $categoryMedicines = collect([]);
        if ($firstAnswer->category_id) {
            // Load medicines through the category relationship
            // Use join to ensure we get medicines from the pivot table
            $categoryMedicines = Medicine::join('category_medicine', 'medicine.id', '=', 'category_medicine.medicine_id')
                ->where('category_medicine.category_id', $firstAnswer->category_id)
                ->where('medicine.status', 1)
                ->with('brand')
                ->select('medicine.*')
                ->orderBy('medicine.name')
                ->get();
        }

        // For Cannaleo flow: get available Cannaleo medicines so doctor can add/remove when building prescription
        $availableCannaleoMedicines = collect([]);
        $category = $firstAnswer->category;
        if ($submission && $submission->delivery_type === 'cannaleo' && $submission->selected_cannaleo_pharmacy_id && $category) {
            if ($category->is_cannaleo_only) {
                $availableCannaleoMedicines = \App\Models\CannaleoMedicine::where('cannaleo_pharmacy_id', $submission->selected_cannaleo_pharmacy_id)
                    ->with('cannaleoPharmacy')
                    ->orderBy('name')
                    ->get();
            } else {
                $availableCannaleoMedicines = $category->cannaleoMedicines()
                    ->where('cannaleo_pharmacy_id', $submission->selected_cannaleo_pharmacy_id)
                    ->with('cannaleoPharmacy')
                    ->orderBy('name')
                    ->get();
            }
        }

        // Get PurchaseMedicine orders for this user (related to this questionnaire submission)
        // Orders created after approval will be for this user
        $orders = collect([]);
        if ($submission) {
            $orders = PurchaseMedicine::where('user_id', $userId)
                ->with(['address', 'user', 'pharmacy'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->filter(function($order) use ($submission) {
                    // Filter orders that were likely created from this questionnaire
                    // If order has same address as submission delivery address
                    if ($submission->delivery_type === 'delivery' && $submission->delivery_address_id) {
                        return $order->address_id == $submission->delivery_address_id;
                    }
                    
                    // If order has same pharmacy as submission selected pharmacy
                    if ($submission->delivery_type === 'pickup' && $submission->selected_pharmacy_id) {
                        return $order->pharmacy_id == $submission->selected_pharmacy_id;
                    }
                    
                    // Otherwise, include recent orders (created in last hour) for this user
                    return $order->created_at && $order->created_at->isAfter(now()->subHour());
                })
                ->take(5); // Limit to 5 most recent matching orders
        }
        
        $currentSubmittedAtKey = $currentBatch['submitted_at']->format('Y-m-d H:i:s');
        return view('doctor.questionnaire.review_submission', compact(
            'answers',
            'firstAnswer',
            'groupedAnswers',
            'hasFlaggedAnswers',
            'doctor',
            'prescription',
            'canEdit',
            'submission',
            'selectedMedicines',
            'selectedCannaleoMedicines',
            'availableCannaleoMedicines',
            'orders',
            'categoryMedicines',
            'submissionsByDate',
            'currentSubmittedAtKey'
        ));
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
            'doctor_notes' => 'nullable|string|max:2000',
            'rejection_reason' => 'nullable|string|max:2000',
        ]);
        
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        
        if (!$doctor || !$doctor->hospital_id) {
            abort(403, 'Unauthorized access');
        }
        
        // Get the submission to check permissions
        // Check with hospital_id first, then fallback to null hospital_id (for backward compatibility)
        $firstAnswer = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->where(function($query) use ($doctor) {
                $query->where('hospital_id', $doctor->hospital_id)
                      ->orWhereNull('hospital_id');
            })
            ->whereNull('appointment_id')
            ->first();
        
        if (!$firstAnswer) {
            abort(404, 'Questionnaire submission not found');
        }
        
        // Ensure hospital_id is set if it was null
        if (!$firstAnswer->hospital_id) {
            $firstAnswer->hospital_id = $doctor->hospital_id;
            $firstAnswer->save();
        }
        
        // Sub-doctor: can update only if they are the reviewer. Admin: can update only if assigned.
        if ($doctor->isSubDoctor() || $doctor->isAdminDoctor()) {
            if (!$firstAnswer->isLockedBy($doctor->id)) {
                abort(403, 'You can only update questionnaires you are reviewing');
            }
        }
        
        // Only unlock (clear reviewing_doctor_id) for rejected or REVIEW_COMPLETED
        // Keep reviewing_doctor_id for approved so the reviewing doctor can still access it to create prescription
        $shouldUnlock = in_array($request->status, ['rejected', 'REVIEW_COMPLETED']);
        $hospitalId = $firstAnswer->hospital_id ?? $doctor->hospital_id;
        $isApproved = $request->status === 'approved';

        $submittedAtFilter = $request->input('submitted_at');

        DB::transaction(function () use ($userId, $categoryId, $questionnaireId, $request, $shouldUnlock, $hospitalId, $doctor, $isApproved, $submittedAtFilter) {
            // Always ensure hospital_id is set in update data
            $updateData = [
                'status' => $request->status,
                'hospital_id' => $hospitalId, // Ensure hospital_id is always set
            ];
            if ($shouldUnlock) {
                $updateData['reviewing_doctor_id'] = null;
            }
            
            $query = QuestionnaireAnswer::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('questionnaire_id', $questionnaireId)
                ->whereNull('appointment_id')
                ->where(function($query) use ($hospitalId) {
                    $query->where('hospital_id', $hospitalId)
                          ->orWhereNull('hospital_id');
                });
            // When multiple submissions exist (same user/category/questionnaire), only update the batch for this submitted_at
            if ($submittedAtFilter) {
                $dt = \Carbon\Carbon::parse($submittedAtFilter);
                $query->where(function ($q) use ($dt) {
                    $q->where(function ($q2) use ($dt) {
                        $q2->where('submitted_at', '>=', $dt->copy()->startOfSecond())
                           ->where('submitted_at', '<=', $dt->copy()->endOfSecond());
                    })->orWhere(function ($q2) use ($dt) {
                        $q2->whereNull('submitted_at')
                           ->where('created_at', '>=', $dt->copy()->startOfSecond())
                           ->where('created_at', '<=', $dt->copy()->endOfSecond());
                    });
                });
            }
            $query->update($updateData);

            // Doctor selects medicines and creates prescription/orders via Create Prescription flow (no auto-create on approve)
        });

        $message = __('Status updated successfully');
        if ($shouldUnlock) {
            $message = __('Review completed and questionnaire unlocked');
        }
        if ($isApproved) {
            $message = __('Questionnaire approved. Create prescription to select medicines and generate prescription and orders.');
        }

        // Send approval or rejection email to patient (same trigger logic as OTP)
        $patient = User::find($userId);
        if ($patient && $patient->email) {
            $doctorUser = User::find($doctor->user_id);
            $doctorName = $doctorUser ? $doctorUser->name : $doctor->name ?? __('Doctor');
            $submissionId = 'REF-' . $userId . '-' . $categoryId;
            $reviewDate = now()->format('F j, Y');
            $category = Category::find($categoryId);
            $categorySlug = $category ? $category->id : $categoryId;
            $customController = new CustomController;
            if ($isApproved) {
                $customController->sendQuestionnaireApprovedMail($patient->email, [
                    'customer_name' => $patient->name,
                    'doctor_name' => $doctorName,
                    'doctor_notes' => $request->input('doctor_notes', ''),
                    'review_date' => $reviewDate,
                    'submission_id' => $submissionId,
                ], true);
            } else {
                $customController->sendQuestionnaireRejectedMail($patient->email, [
                    'customer_name' => $patient->name,
                    'doctor_name' => $doctorName,
                    'rejection_reason' => $request->input('rejection_reason', __('Please contact us for more details.')),
                    'review_date' => $reviewDate,
                    'submission_id' => $submissionId,
                ], true);
            }
        }

        $url = route('doctor.questionnaire.show', [
            'userId' => $userId,
            'categoryId' => $categoryId,
            'questionnaireId' => $questionnaireId,
        ]);
        if ($request->input('submitted_at')) {
            $url .= '?submitted_at=' . urlencode($request->input('submitted_at'));
        }
        return redirect($url)->with('success', $message);
    }
    
    /**
     * Automatically create prescription and orders when questionnaire is approved.
     */
    protected function createPrescriptionAndOrders($userId, $categoryId, $questionnaireId, $doctor)
    {
        // Get questionnaire submission with selected medicines
        $submission = QuestionnaireSubmission::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->first();

        if (!$submission || !$submission->hasSelectedMedicines()) {
            return; // No medicines selected, skip auto-creation
        }

        // Check if prescription already exists
        $existingPrescription = Prescription::where('user_id', $userId)
            ->where('doctor_id', $doctor->id)
            ->whereNull('appointment_id')
            ->where('status', '!=', 'expired')
            ->first();

        

        // Build prescription medicines array from selected medicines
        $prescriptionMedicines = [];
        $selectedMedicines = $submission->selected_medicines ?? [];

        foreach ($selectedMedicines as $selected) {
            $medicineId = $selected['medicine_id'] ?? null;
            if (!$medicineId) continue;

            $medicine = Medicine::with('brand')->find($medicineId);
            if (!$medicine) continue;

            $prescriptionMedicines[] = [
                'medicine' => $medicine->name,
                'strength' => $medicine->strength ?? '',
            ];
        }

        if (empty($prescriptionMedicines)) {
            return; // No valid medicines found
        }

        // Create prescription
        $validFrom = now();
        $validityDays = 30; // Default 30 days validity
        $validUntil = $validFrom->copy()->addDays($validityDays);
        
        // Get prescription fee from settings
        $setting = \App\Models\Setting::first();
        $prescriptionFee = $setting->prescription_fee ?? 50.00;

        $prescription = Prescription::create([
            'appointment_id' => null,
            'user_id' => $userId,
            'doctor_id' => $doctor->id,
            'medicines' => json_encode($prescriptionMedicines),
            'status' => 'active',
            'validity_days' => $validityDays,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
            'payment_amount' => $prescriptionFee,
            'payment_status' => 0,
        ]);

        // Create orders based on patient's delivery type choice from questionnaire submission
        if ($submission->delivery_type === 'pickup' && $submission->selected_pharmacy_id) {
            // Patient selected PICKUP: Use the pharmacy they selected, no delivery address
            $this->createPurchaseMedicineOrder($submission, $selectedMedicines, $submission->selected_pharmacy_id, null);
        } elseif ($submission->delivery_type === 'delivery' && $submission->hasCompleteDeliveryAddress()) {
            // Patient selected DELIVERY: Find a shipping-enabled pharmacy and use their delivery address
            $pharmacy = \App\Models\Pharmacy::where('status', 'approved')
                ->where('is_shipping', 1)
                ->first();
            
            // If no shipping pharmacy found, get any approved pharmacy as fallback
            if (!$pharmacy) {
                $pharmacy = \App\Models\Pharmacy::where('status', 'approved')->first();
            }
            
            // Only create order if pharmacy is found
            if ($pharmacy) {
                // Pass the patient's delivery address to create a delivery order
                $this->createPurchaseMedicineOrder($submission, $selectedMedicines, $pharmacy->id, $submission->delivery_address_id);
            }
            // If no pharmacy found, skip order creation (prescription is already created)
        }
    }

    /**
     * Create PurchaseMedicine order for selected medicines.
     * 
     * The delivery type is determined by the patient's choice in the questionnaire submission:
     * - If delivery_type is 'pickup': addressId will be null, order is for pharmacy pickup
     * - If delivery_type is 'delivery': addressId will be set, order is for home delivery
     * 
     * Returns the created PurchaseMedicine model or null on failure.
     */
    protected function createPurchaseMedicineOrder($submission, $selectedMedicines, $pharmacyId, $addressId)
    {
        // Ensure pharmacy_id is provided (required by database)
        if (!$pharmacyId) {
            \Log::warning('Cannot create PurchaseMedicine order: pharmacy_id is required', [
                'submission_id' => $submission->id ?? null,
                'delivery_type' => $submission->delivery_type ?? null,
            ]);
            return null;
        }
        
        // Note: The delivery type is determined by the patient's choice in the questionnaire submission
        // - For pickup: addressId is null, order will be for pharmacy pickup
        // - For delivery: addressId is set, order will be for home delivery

        $totalAmount = 0;
        $medicineItems = [];

        // Calculate total amount and prepare medicine items
        foreach ($selectedMedicines as $selected) {
            $medicineId = $selected['medicine_id'] ?? null;
            if (!$medicineId) continue;

            $medicine = Medicine::find($medicineId);
            if (!$medicine) continue;

            // Get price from pharmacy inventory if pharmacy is selected
            $price = 0;
            $qty = 1; // Default quantity

            if ($pharmacyId) {
                $inventory = \App\Models\PharmacyInventory::where('pharmacy_id', $pharmacyId)
                    ->where('medicine_id', $medicineId)
                    ->first();
                if ($inventory && $inventory->price > 0) {
                    $price = $inventory->price;
                }
            }

            // If no price found, skip this medicine (or use a default - adjust as needed)
            if ($price == 0) {
                // For delivery without pharmacy, you might want to set a default price
                // or skip medicines without pricing. For now, use a default.
                $price = 100; // Default price - adjust based on your business logic
            }

            $totalAmount += $price * $qty;
            $medicineItems[] = [
                'medicine_id' => $medicineId,
                'price' => $price,
                'qty' => $qty,
            ];
        }

        if (empty($medicineItems)) {
            return null; // No valid medicine items
        }

        // Calculate commissions (assuming 10% admin commission - adjust as needed)
        $adminCommissionPercent = 10;
        $adminCommission = ($totalAmount * $adminCommissionPercent) / 100;
        $pharmacyCommission = $totalAmount - $adminCommission;
        
        // Apply delivery charge only if patient selected delivery (respecting patient's delivery type choice)
        $deliveryCharge = $submission->delivery_type === 'delivery' ? 50 : 0;

        // Set shipping date: default to next day (can be adjusted based on business logic)
        // For delivery: ship next day; for pickup: ready for pickup next day
        $shippingAt = now()->addDay(); // Default: next day for both delivery and pickup

        // Create PurchaseMedicine order
        $purchaseData = [
            'medicine_id' => '#' . rand(100000, 999999), // Order ID
            'user_id' => $submission->user_id,
            'amount' => $totalAmount + $deliveryCharge,
            'payment_type' => 'COD', // Cash on Delivery by default
            'payment_status' => 0, // Pending payment
            'admin_commission' => $adminCommission,
            'pharmacy_commission' => $pharmacyCommission,
            'pharmacy_id' => $pharmacyId, // Now guaranteed to be non-null
            'address_id' => $addressId,
            'delivery_charge' => $deliveryCharge,
            'shipping_at' => $shippingAt,
        ];

        $purchase = PurchaseMedicine::create($purchaseData);

        // Create MedicineChild records for each medicine
            foreach ($medicineItems as $item) {
                MedicineChild::create([
                    'purchase_medicine_id' => $purchase->id,
                    'medicine_id' => $item['medicine_id'],
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                ]);

                // Update pharmacy inventory stock if pharmacy is selected
                if ($pharmacyId) {
                    $inventory = \App\Models\PharmacyInventory::where('pharmacy_id', $pharmacyId)
                        ->where('medicine_id', $item['medicine_id'])
                        ->first();
                    if ($inventory) {
                        $availableStock = max(0, ($inventory->quantity ?? 0) - $item['qty']);
                        $inventory->update(['quantity' => $availableStock]);
                    }
                }
            }

        // Create PharmacySettle record if pharmacy is selected
        if ($pharmacyId) {
            PharmacySettle::create([
                'purchase_medicine_id' => $purchase->id,
                'pharmacy_id' => $pharmacyId,
                'pharmacy_amount' => $pharmacyCommission,
                'admin_amount' => $adminCommission,
                'payment' => 0, // COD = 0, online = 1
                'pharmacy_status' => 0, // Pending settlement
            ]);
        }

        return $purchase;
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
            
        //if ($existingPrescription) {
        //    return redirect()->route('doctor.questionnaire.index')
        //        ->with('info', __('Prescription already exists for this questionnaire.'));
        //}
        
        $user = User::find($userId);
        $category = Category::find($categoryId);
        $medicines = $category ? $category->medicines()->where('status', 1)->orderBy('name')->get() : collect([]);
        if ($medicines->isEmpty()) {
            return redirect()->route('doctor.questionnaire.show', ['userId' => $userId, 'categoryId' => $categoryId, 'questionnaireId' => $questionnaireId])
                ->with('error', __('No medicines are assigned to this category. Add medicines in admin first.'));
        }
        $submission = QuestionnaireSubmission::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->first();
        $patientSuggestedMedicines = [];
        if ($submission && $submission->hasSelectedMedicines()) {
            foreach ($submission->selected_medicines ?? [] as $s) {
                $m = Medicine::with('brand')->find($s['medicine_id'] ?? null);
                if ($m) $patientSuggestedMedicines[] = $m;
            }
        }
        
        return view('doctor.questionnaire.create_prescription', compact(
            'user', 'doctor', 'medicines', 'answers', 'userId', 'categoryId', 'questionnaireId',
            'patientSuggestedMedicines', 'submission'
        ));
    }
    
    /**
     * Store prescription for approved questionnaire.
     * Handles both regular (category medicines) and Cannaleo (patient-selected partner medicines) flows.
     */
    public function storePrescription(Request $request, $userId, $categoryId, $questionnaireId)
    {
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();

        if (!$doctor || !$doctor->hospital_id) {
            abort(403, 'Unauthorized access - Doctor not assigned to hospital');
        }

        $submission = QuestionnaireSubmission::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->first();

        $isCannaleoPrescription = $request->boolean('cannaleo_prescription')
            && $submission
            && $submission->delivery_type === 'cannaleo';

        if ($isCannaleoPrescription) {
            $request->validate([
                'cannaleo_medicine_ids' => 'required|array|min:1|max:5',
                'cannaleo_medicine_ids.*' => 'required|exists:cannaleo_medicine,id',
            ]);
        } else {
            $request->validate([
                'medicines' => 'required|array|min:1|max:5',
                'medicines.*' => 'required|exists:medicine,id',
                'strength' => 'required|array',
                'strength.*' => 'nullable|string|max:100',
            ]);
        }

        $answers = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->where('hospital_id', $doctor->hospital_id)
            ->whereNull('appointment_id')
            ->first();

        if (!$answers) {
            return redirect()->route('doctor.questionnaire.index')
                ->with('error', __('Questionnaire submission not found'));
        }

        if (!in_array($answers->status, ['approved', 'IN_REVIEW', 'under_review'])) {
            return redirect()->route('doctor.questionnaire.index')
                ->with('error', __('Questionnaire must be under review or approved before creating prescription'));
        }

        $existingPrescription = Prescription::where('user_id', $userId)
            ->where('doctor_id', $doctor->id)
            ->whereNull('appointment_id')
            ->where('status', '!=', 'expired')
            ->first();

        //if ($existingPrescription) {
        //    return redirect()->route('doctor.questionnaire.index')
        //        ->with('info', __('Prescription already exists for this questionnaire.'));
        //}

        $prescriptionMedicines = [];
        $selectedForOrders = [];

        if ($isCannaleoPrescription) {
            $category = \App\Models\Category::find($categoryId);
            $pharmacyId = $submission->selected_cannaleo_pharmacy_id;
            if (!$pharmacyId) {
                return redirect()->back()->with('error', __('Cannaleo pharmacy not selected.'));
            }
            // Restrict to medicines from the same pharmacy (and category when not cannaleo_only)
            if ($category && $category->is_cannaleo_only) {
                $allowedIds = \App\Models\CannaleoMedicine::where('cannaleo_pharmacy_id', $pharmacyId)->pluck('id')->toArray();
            } else {
                $allowedIds = $category
                    ? $category->cannaleoMedicines()->where('cannaleo_pharmacy_id', $pharmacyId)->pluck('id')->toArray()
                    : [];
            }
            $requestedIds = array_map('intval', $request->input('cannaleo_medicine_ids', []));
            foreach ($requestedIds as $id) {
                if (!in_array($id, $allowedIds, true)) {
                    return redirect()->back()->with('error', __('One or more selected Cannaleo medicines are not allowed for this category/pharmacy.'));
                }
            }
            foreach ($requestedIds as $id) {
                $cannaleoMedicine = \App\Models\CannaleoMedicine::find($id);
                if ($cannaleoMedicine) {
                    $strength = null;
                    if ($cannaleoMedicine->thc !== null || $cannaleoMedicine->cbd !== null) {
                        $strength = 'THC ' . ($cannaleoMedicine->thc ?? 0) . '% / CBD ' . ($cannaleoMedicine->cbd ?? 0) . '%';
                    }
                    $prescriptionMedicines[] = [
                        'medicine' => $cannaleoMedicine->name,
                        'strength' => $strength ?? '',
                    ];
                }
            }
            if (empty($prescriptionMedicines)) {
                return redirect()->back()->with('error', __('No valid Cannaleo medicines selected.'));
            }
            // Update submission so the doctor-approved list is the approved prescription for the customer
            $submission->update([
                'selected_medicines' => array_map(fn ($id) => ['cannaleo_medicine_id' => $id], $requestedIds),
            ]);
        } else {
            $medicineNames = $request->input('medicine_names', []);
            $strengths = $request->input('strength', []);
            for ($i = 0; $i < count($request->medicines); $i++) {
                $medicineId = $request->medicines[$i];
                $medicine = Medicine::find($medicineId);
                $medicineName = $medicine ? $medicine->name : ($medicineNames[$i] ?? '');
                $strength = $strengths[$i] ?? '';
                $prescriptionMedicines[] = ['medicine' => $medicineName, 'strength' => $strength];
                $selectedForOrders[] = ['medicine_id' => (int) $medicineId];
            }
        }

        $setting = \App\Models\Setting::first();
        $prescriptionFee = $setting->prescription_fee ?? 50.00;

        $prescription = null;
        DB::transaction(function () use ($userId, $categoryId, $questionnaireId, $doctor, $prescriptionMedicines, $submission, $selectedForOrders, $answers, $prescriptionFee, $isCannaleoPrescription, &$prescription) {
            if (in_array($answers->status, ['IN_REVIEW', 'under_review'])) {
                QuestionnaireAnswer::where('user_id', $userId)
                    ->where('category_id', $categoryId)
                    ->where('questionnaire_id', $questionnaireId)
                    ->whereNull('appointment_id')
                    ->where('hospital_id', $doctor->hospital_id)
                    ->update(['status' => 'approved']);
            }

            $questionnaireSubmittedAt = $answers->submitted_at ?? $answers->created_at;

            $createData = [
                'appointment_id' => null,
                'questionnaire_submitted_at' => $questionnaireSubmittedAt,
                'user_id' => $userId,
                'doctor_id' => $doctor->id,
                'medicines' => json_encode($prescriptionMedicines),
                'status' => 'active',
                'valid_from' => null,
                'valid_until' => null,
                'validity_days' => null,
                'payment_amount' => $prescriptionFee,
                'payment_status' => 0,
            ];
            if ($isCannaleoPrescription) {
                $createData['is_cannaleo'] = true;
                $prescription = Prescription::create($createData);
            } else {
                Prescription::create($createData);
            }

            // Do not create internal orders for Cannaleo (fulfilment via partner)
            if (!$isCannaleoPrescription && $submission) {
                if ($submission->delivery_type === 'pickup' && $submission->selected_pharmacy_id) {
                    $this->createPurchaseMedicineOrder($submission, $selectedForOrders, $submission->selected_pharmacy_id, null);
                } elseif ($submission->delivery_type === 'delivery' && $submission->hasCompleteDeliveryAddress()) {
                    $pharmacy = \App\Models\Pharmacy::where('status', 'approved')
                        ->where('is_shipping', 1)
                        ->first();
                    if (!$pharmacy) {
                        $pharmacy = \App\Models\Pharmacy::where('status', 'approved')->first();
                    }
                    if ($pharmacy) {
                        $this->createPurchaseMedicineOrder($submission, $selectedForOrders, $pharmacy->id, $submission->delivery_address_id);
                    }
                }
            }
        });

        // Cannaleo: generate PDF, then call Curobo prescription API and log
        if ($isCannaleoPrescription && $prescription) {
            $pdfResult = app(PrescriptionPdfService::class)->generate($prescription);
            if ($pdfResult !== true) {
                Log::warning('Cannaleo prescription PDF generation failed', ['prescription_id' => $prescription->id, 'error' => $pdfResult]);
                CannaleoPrescriptionLog::create([
                    'prescription_id' => $prescription->id,
                    'questionnaire_submission_id' => $submission->id,
                    'called_at' => now(),
                    'request_payload' => null,
                    'response_status' => null,
                    'response_body' => null,
                    'external_order_id' => null,
                    'products_snapshot' => [],
                    'total_medicine_cost' => null,
                    'prescription_fee' => $prescription->payment_amount,
                    'error_message' => 'PDF generation failed',
                ]);
            } else {
                $prescription->refresh();
                $pdfPath = storage_path('prescription-upload/' . $prescription->pdf);
                $prescriptionUrl = base64_encode(file_get_contents($pdfPath));
                $customer = User::find($prescription->user_id);
                if (! $customer) {
                    Log::warning('Cannaleo prescription: customer not found', ['prescription_id' => $prescription->id]);
                    CannaleoPrescriptionLog::create([
                        'prescription_id' => $prescription->id,
                        'questionnaire_submission_id' => $submission->id,
                        'called_at' => now(),
                        'request_payload' => null,
                        'response_status' => null,
                        'response_body' => null,
                        'external_order_id' => null,
                        'products_snapshot' => [],
                        'total_medicine_cost' => null,
                        'prescription_fee' => $prescription->payment_amount,
                        'error_message' => 'Customer not found',
                    ]);
                } else {
                $prescription->load(['doctor.user', 'doctor.hospital']);
                $doctorLoaded = $prescription->doctor;
                $pharmacy = $submission->selectedCannaleoPharmacy;
                $cannaleoMedicineIds = collect($submission->selected_medicines ?? [])->pluck('cannaleo_medicine_id')->filter()->values()->all();
                $products = CannaleoMedicine::whereIn('id', $cannaleoMedicineIds)->get();
                $payload = CuroboPrescriptionPayloadBuilder::build($prescription, $submission, $customer, $doctorLoaded, $products, $prescriptionUrl, $pharmacy);
                $productsSnapshot = [];
                $totalMedicineCost = 0;
                foreach ($products as $med) {
                    $qty = 1;
                    $price = (float) $med->price;
                    $productsSnapshot[] = [
                        'cannaleo_medicine_id' => $med->id,
                        'name' => $med->name,
                        'price' => $price,
                        'quantity' => $qty,
                        'category' => $med->category ?? 'flower',
                    ];
                    $totalMedicineCost += $price * $qty;
                }
                $prescriptionFee = (float) $prescription->payment_amount;
                // Strip the base64-encoded signature before logging to avoid exceeding max_allowed_packet
                $loggablePayload = $payload;
                if (isset($loggablePayload['doctor']['signature'])) {
                    $loggablePayload['doctor']['signature'] = '[redacted]';
                }
                try {
                    $api = new CuroboPrescriptionApi();
                    $response = $api->submitPrescription($payload);
                    CannaleoPrescriptionLog::create([
                        'prescription_id' => $prescription->id,
                        'questionnaire_submission_id' => $submission->id,
                        'called_at' => now(),
                        'request_payload' => $loggablePayload,
                        'response_status' => 200,
                        'response_body' => is_array($response) ? json_encode($response) : (string) $response,
                        'external_order_id' => $response['order_id'] ?? $response['id'] ?? null,
                        'products_snapshot' => $productsSnapshot,
                        'total_medicine_cost' => $totalMedicineCost,
                        'prescription_fee' => $prescriptionFee,
                        'error_message' => null,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('Curobo prescription API call failed', ['prescription_id' => $prescription->id, 'error' => $e->getMessage()]);
                    CannaleoPrescriptionLog::create([
                        'prescription_id' => $prescription->id,
                        'questionnaire_submission_id' => $submission->id,
                        'called_at' => now(),
                        'request_payload' => $loggablePayload,
                        'response_status' => null,
                        'response_body' => $e->getMessage(),
                        'external_order_id' => null,
                        'products_snapshot' => $productsSnapshot,
                        'total_medicine_cost' => $totalMedicineCost,
                        'prescription_fee' => $prescriptionFee,
                        'error_message' => $e->getMessage(),
                    ]);
                    return redirect()->route('doctor.questionnaire.show', [
                        'userId' => $userId,
                        'categoryId' => $categoryId,
                        'questionnaireId' => $questionnaireId,
                    ])->with('success', __('Prescription and orders created successfully.'))
                        ->with('warning', __('Prescription saved but partner API could not be notified; our team will follow up.'));
                }
                }
            }
        }

        return redirect()->route('doctor.questionnaire.show', [
            'userId' => $userId,
            'categoryId' => $categoryId,
            'questionnaireId' => $questionnaireId,
        ])->with('success', __('Prescription and orders created successfully.'));
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
                'sub_answers' => $answer->sub_answers ?? [],
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

    /**
     * Bulk approve selected questionnaires and auto-generate prescriptions.
     * Each submission key is encoded as "userId|categoryId|questionnaireId|submittedAt".
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'submissions'   => 'required|array|min:1',
            'submissions.*' => 'required|string',
        ]);

        $doctor = Doctor::where('user_id', auth()->user()->id)->first();

        if (!$doctor || !$doctor->hospital_id) {
            abort(403, 'Unauthorized access');
        }

        $successCount = 0;
        $skipped      = [];
        $errors       = [];

        foreach ($request->submissions as $key) {
            $parts = explode('|', $key, 4);
            if (count($parts) < 3) {
                continue;
            }

            [$userId, $categoryId, $questionnaireId] = $parts;
            $submittedAt = $parts[3] ?? null;

            try {
                $this->processBulkApprovalAndPrescription(
                    (int) $userId,
                    (int) $categoryId,
                    (int) $questionnaireId,
                    $submittedAt,
                    $doctor
                );
                $successCount++;
            } catch (\RuntimeException $e) {
                // Known, skippable issues (already approved, locked by another doctor, etc.)
                $skipped[] = $e->getMessage();
            } catch (\Throwable $e) {
                Log::error('Bulk approve failed for submission key ' . $key, ['error' => $e->getMessage()]);
                $errors[] = $e->getMessage();
            }
        }

        $message = __(':count questionnaire(s) approved and prescription(s) generated.', ['count' => $successCount]);
        if (!empty($skipped)) {
            $message .= ' ' . count($skipped) . ' ' . __('skipped') . ' (' . implode('; ', $skipped) . ').';
        }
        if (!empty($errors)) {
            $message .= ' ' . count($errors) . ' ' . __('failed due to errors.');
        }

        return redirect()->route('doctor.questionnaire.index')
            ->with($errors ? 'warning' : 'success', $message);
    }

    /**
     * Approve a single questionnaire submission and auto-create the prescription
     * using the medicines the patient already selected in their submission.
     *
     * @throws \RuntimeException for known/skippable issues
     * @throws \Throwable        for unexpected failures
     */
    protected function processBulkApprovalAndPrescription(
        int $userId,
        int $categoryId,
        int $questionnaireId,
        ?string $submittedAt,
        Doctor $doctor
    ): void {
        // ------------------------------------------------------------------
        // 1. Load the first answer record to inspect status / permissions
        // ------------------------------------------------------------------
        $firstAnswer = $this->findFirstAnswer($userId, $categoryId, $questionnaireId, $submittedAt, $doctor);

        if (!$firstAnswer) {
            throw new \RuntimeException("Submission not found (u:{$userId} c:{$categoryId} q:{$questionnaireId})");
        }

        if ($firstAnswer->status === 'approved') {
            throw new \RuntimeException("Already approved (u:{$userId})");
        }

        // If locked by a different doctor, skip (sub-doctors cannot override)
        if (
            $doctor->isSubDoctor()
            && $firstAnswer->reviewing_doctor_id
            && $firstAnswer->reviewing_doctor_id !== $doctor->id
        ) {
            throw new \RuntimeException("Locked by another doctor (u:{$userId})");
        }

        $hospitalId = $firstAnswer->hospital_id ?? $doctor->hospital_id;

        // ------------------------------------------------------------------
        // 2. Approve status and assign reviewing doctor
        // ------------------------------------------------------------------
        DB::transaction(function () use ($userId, $categoryId, $questionnaireId, $submittedAt, $doctor, $hospitalId) {
            $this->buildAnswerBaseQuery($userId, $categoryId, $questionnaireId, $submittedAt, $hospitalId)
                ->update([
                    'status'              => 'approved',
                    'hospital_id'         => $hospitalId,
                    'reviewing_doctor_id' => $doctor->id,
                ]);
        });

        // ------------------------------------------------------------------
        // 3. Load the questionnaire submission (patient's medicine choices)
        // ------------------------------------------------------------------
        $submission = QuestionnaireSubmission::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->first();

        if (!$submission || !$submission->hasSelectedMedicines()) {
            // No medicines selected – prescription cannot be auto-generated, but approval stands
        } else {
            // ------------------------------------------------------------------
            // 4. Create prescription (Cannaleo or standard)
            // ------------------------------------------------------------------
            if ($submission->delivery_type === 'cannaleo') {
                $this->bulkCreateCannaleoPrescription($submission, $doctor, $firstAnswer);
            } else {
                $this->createPrescriptionAndOrders($userId, $categoryId, $questionnaireId, $doctor);
            }
        }

        // ------------------------------------------------------------------
        // 5. Send approval email to patient
        // ------------------------------------------------------------------
        $patient = User::find($userId);
        if ($patient && $patient->email) {
            $doctorUser  = User::find($doctor->user_id);
            $doctorName  = $doctorUser ? $doctorUser->name : ($doctor->name ?? __('Doctor'));
            $submissionId = 'REF-' . $userId . '-' . $categoryId;

            (new CustomController)->sendQuestionnaireApprovedMail($patient->email, [
                'customer_name' => $patient->name,
                'doctor_name'   => $doctorName,
                'doctor_notes'  => '',
                'review_date'   => now()->format('F j, Y'),
                'submission_id' => $submissionId,
            ], true);
        }
    }

    /**
     * Build the QuestionnaireAnswer query scoped to the given submission batch.
     */
    protected function buildAnswerBaseQuery(
        int $userId,
        int $categoryId,
        int $questionnaireId,
        ?string $submittedAt,
        int $hospitalId
    ) {
        $query = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->whereNull('appointment_id')
            ->where(function ($q) use ($hospitalId) {
                $q->where('hospital_id', $hospitalId)->orWhereNull('hospital_id');
            });

        if ($submittedAt) {
            $dt = \Carbon\Carbon::parse($submittedAt);
            $query->where(function ($q) use ($dt) {
                $q->where(function ($q2) use ($dt) {
                    $q2->where('submitted_at', '>=', $dt->copy()->startOfSecond())
                       ->where('submitted_at', '<=', $dt->copy()->endOfSecond());
                })->orWhere(function ($q2) use ($dt) {
                    $q2->whereNull('submitted_at')
                       ->where('created_at', '>=', $dt->copy()->startOfSecond())
                       ->where('created_at', '<=', $dt->copy()->endOfSecond());
                });
            });
        }

        return $query;
    }

    /**
     * Find the first QuestionnaireAnswer for permission/status checks.
     */
    protected function findFirstAnswer(
        int $userId,
        int $categoryId,
        int $questionnaireId,
        ?string $submittedAt,
        Doctor $doctor
    ): ?QuestionnaireAnswer {
        // Use a temporary hospitalId of 0 to allow the orWhereNull fallback
        return $this->buildAnswerBaseQuery(
            $userId, $categoryId, $questionnaireId, $submittedAt, $doctor->hospital_id
        )->first();
    }

    /**
     * Create a Cannaleo prescription for a bulk-approved submission,
     * using the medicines the patient already selected.
     */
    protected function bulkCreateCannaleoPrescription(
        QuestionnaireSubmission $submission,
        Doctor $doctor,
        QuestionnaireAnswer $firstAnswer
    ): void {
        $cannaleoMedicineIds = collect($submission->selected_medicines ?? [])
            ->pluck('cannaleo_medicine_id')
            ->filter()
            ->values()
            ->all();

        if (empty($cannaleoMedicineIds)) {
            return; // Nothing to prescribe
        }

        $prescriptionMedicines = [];
        foreach ($cannaleoMedicineIds as $id) {
            $med = CannaleoMedicine::find($id);
            if (!$med) {
                continue;
            }
            $strength = ($med->thc !== null || $med->cbd !== null)
                ? 'THC ' . ($med->thc ?? 0) . '% / CBD ' . ($med->cbd ?? 0) . '%'
                : '';
            $prescriptionMedicines[] = ['medicine' => $med->name, 'strength' => $strength];
        }

        if (empty($prescriptionMedicines)) {
            return;
        }

        $setting         = \App\Models\Setting::first();
        $prescriptionFee = $setting->prescription_fee ?? 50.00;
        $submittedAt     = $firstAnswer->submitted_at ?? $firstAnswer->created_at;

        $prescription = null;
        DB::transaction(function () use ($submission, $doctor, $prescriptionMedicines, $submittedAt, $prescriptionFee, &$prescription) {
            $prescription = Prescription::create([
                'appointment_id'           => null,
                'questionnaire_submitted_at' => $submittedAt,
                'user_id'                  => $submission->user_id,
                'doctor_id'                => $doctor->id,
                'medicines'                => json_encode($prescriptionMedicines),
                'status'                   => 'active',
                'valid_from'               => null,
                'valid_until'              => null,
                'validity_days'            => null,
                'payment_amount'           => $prescriptionFee,
                'payment_status'           => 0,
                'is_cannaleo'              => true,
            ]);
        });

        if (!$prescription) {
            return;
        }

        // Generate PDF
        $pdfResult = app(PrescriptionPdfService::class)->generate($prescription);
        if ($pdfResult !== true) {
            Log::warning('Bulk Cannaleo prescription PDF failed', ['prescription_id' => $prescription->id]);
            CannaleoPrescriptionLog::create([
                'prescription_id'            => $prescription->id,
                'questionnaire_submission_id' => $submission->id,
                'called_at'                  => now(),
                'request_payload'            => null,
                'response_status'            => null,
                'response_body'              => null,
                'external_order_id'          => null,
                'products_snapshot'          => [],
                'total_medicine_cost'        => null,
                'prescription_fee'           => $prescription->payment_amount,
                'error_message'              => 'PDF generation failed',
            ]);
            return;
        }

        // Call Curobo API
        $prescription->refresh();
        $customer = User::find($prescription->user_id);
        if (!$customer) {
            CannaleoPrescriptionLog::create([
                'prescription_id'            => $prescription->id,
                'questionnaire_submission_id' => $submission->id,
                'called_at'                  => now(),
                'request_payload'            => null,
                'response_status'            => null,
                'response_body'              => null,
                'external_order_id'          => null,
                'products_snapshot'          => [],
                'total_medicine_cost'        => null,
                'prescription_fee'           => $prescription->payment_amount,
                'error_message'              => 'Customer not found',
            ]);
            return;
        }

        $prescription->load(['doctor.user', 'doctor.hospital']);
        $pharmacy        = $submission->selectedCannaleoPharmacy;
        $pdfPath         = storage_path('prescription-upload/' . $prescription->pdf);
        $prescriptionUrl = base64_encode(file_get_contents($pdfPath));
        $products        = CannaleoMedicine::whereIn('id', $cannaleoMedicineIds)->get();
        $payload         = CuroboPrescriptionPayloadBuilder::build(
            $prescription, $submission, $customer, $prescription->doctor, $products, $prescriptionUrl, $pharmacy
        );

        $productsSnapshot   = [];
        $totalMedicineCost  = 0;
        foreach ($products as $med) {
            $price               = (float) $med->price;
            $productsSnapshot[]  = [
                'cannaleo_medicine_id' => $med->id,
                'name'                 => $med->name,
                'price'                => $price,
                'quantity'             => 1,
                'category'             => $med->category ?? 'flower',
            ];
            $totalMedicineCost  += $price;
        }

        try {
            $api      = new CuroboPrescriptionApi();
            $response = $api->submitPrescription($payload);
            CannaleoPrescriptionLog::create([
                'prescription_id'            => $prescription->id,
                'questionnaire_submission_id' => $submission->id,
                'called_at'                  => now(),
                'request_payload'            => $payload,
                'response_status'            => 200,
                'response_body'              => is_array($response) ? json_encode($response) : (string) $response,
                'external_order_id'          => $response['order_id'] ?? $response['id'] ?? null,
                'products_snapshot'          => $productsSnapshot,
                'total_medicine_cost'        => $totalMedicineCost,
                'prescription_fee'           => (float) $prescription->payment_amount,
                'error_message'              => null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Bulk Curobo API failed', ['prescription_id' => $prescription->id, 'error' => $e->getMessage()]);
            CannaleoPrescriptionLog::create([
                'prescription_id'            => $prescription->id,
                'questionnaire_submission_id' => $submission->id,
                'called_at'                  => now(),
                'request_payload'            => $payload,
                'response_status'            => null,
                'response_body'              => $e->getMessage(),
                'external_order_id'          => null,
                'products_snapshot'          => $productsSnapshot,
                'total_medicine_cost'        => $totalMedicineCost,
                'prescription_fee'           => (float) $prescription->payment_amount,
                'error_message'              => $e->getMessage(),
            ]);
            // Do not rethrow – prescription is saved; admin will follow up
        }
    }
}


