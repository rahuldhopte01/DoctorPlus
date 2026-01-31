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
use App\Models\QuestionnaireSubmission;
use App\Models\PurchaseMedicine;
use App\Models\MedicineChild;
use App\Models\PharmacySettle;
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
        
        $baseQuery = QuestionnaireAnswer::whereNull('questionnaire_answers.appointment_id')
            ->whereIn('questionnaire_answers.status', ['pending', 'under_review', 'IN_REVIEW', 'approved', 'rejected', 'REVIEW_COMPLETED'])
            ->with(['user', 'category', 'questionnaire', 'question.section', 'reviewingDoctor']);
        
        if ($doctor->isSubDoctor()) {
            $doctorCategoryIds = $doctor->categories->pluck('id')->toArray();
            
            $baseQuery->where(function ($query) use ($doctor, $doctorCategoryIds) {
                // (a) I am the reviewer -> always show
                $query->where('questionnaire_answers.reviewing_doctor_id', $doctor->id)
                    ->orWhere(function ($q) use ($doctor, $doctorCategoryIds) {
                        // (b) Pending, unlocked, in my categories, same hospital
                        $q->where(function ($h) use ($doctor) {
                            $h->where('questionnaire_answers.hospital_id', $doctor->hospital_id)
                                ->orWhereNull('questionnaire_answers.hospital_id');
                        })
                        ->where('questionnaire_answers.status', 'pending')
                        ->whereNull('questionnaire_answers.reviewing_doctor_id')
                        ->whereIn('questionnaire_answers.category_id', $doctorCategoryIds);
                    });
            });
            $answers = $baseQuery->orderBy('questionnaire_answers.submitted_at', 'desc')->get();
        } else {
            $baseQuery->where(function ($query) use ($doctor) {
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
        // Query fresh to ensure we get the latest data, especially after status updates
        // Handle both hospital_id match and null hospital_id (for backward compatibility)
        $answers = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->where(function($query) use ($doctor) {
                $query->where('hospital_id', $doctor->hospital_id)
                      ->orWhereNull('hospital_id');
            })
            ->whereNull('appointment_id')
            ->with(['user', 'category', 'questionnaire', 'question.section', 'reviewingDoctor'])
            ->orderBy('question_id')
            ->get();
        
        if ($answers->isEmpty()) {
            return redirect()->route('doctor.questionnaire.index')->with('error', __('Questionnaire submission not found'));
        }
        
        // Ensure all answers have hospital_id set (for consistency)
        $needsHospitalIdUpdate = false;
        foreach ($answers as $answer) {
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
            // Refresh answers after update
            $answers = QuestionnaireAnswer::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('questionnaire_id', $questionnaireId)
                ->where(function($query) use ($doctor) {
                    $query->where('hospital_id', $doctor->hospital_id)
                          ->orWhereNull('hospital_id');
                })
                ->whereNull('appointment_id')
                ->with(['user', 'category', 'questionnaire', 'question.section', 'reviewingDoctor'])
                ->orderBy('question_id')
                ->get();
        }
        
        $firstAnswer = $answers->first();
        
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
        // Only lock if status is 'pending' - don't lock if already approved/rejected
        if (!$firstAnswer->isLocked() && $firstAnswer->status === 'pending') {
            DB::transaction(function () use ($userId, $categoryId, $questionnaireId, $doctor) {
                QuestionnaireAnswer::where('user_id', $userId)
                    ->where('category_id', $categoryId)
                    ->where('questionnaire_id', $questionnaireId)
                    ->whereNull('appointment_id')
                    ->where(function($query) use ($doctor) {
                        $query->where('hospital_id', $doctor->hospital_id)
                              ->orWhereNull('hospital_id');
                    })
                    ->update([
                        'status' => 'IN_REVIEW',
                        'reviewing_doctor_id' => $doctor->id,
                        'hospital_id' => $doctor->hospital_id,
                    ]);
            });
            // Refresh answers after locking
            $answers = QuestionnaireAnswer::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('questionnaire_id', $questionnaireId)
                ->where(function($query) use ($doctor) {
                    $query->where('hospital_id', $doctor->hospital_id)
                          ->orWhereNull('hospital_id');
                })
                ->whereNull('appointment_id')
                ->with(['user', 'category', 'questionnaire', 'question.section', 'reviewingDoctor'])
                ->orderBy('question_id')
                ->get();
            $firstAnswer = $answers->first();
        }
        
        $groupedAnswers = $this->groupAnswersBySection($answers);
        $hasFlaggedAnswers = $answers->where('is_flagged', true)->isNotEmpty();
        
        // Determine if doctor can edit:
        // - Admin doctors can always edit questionnaires in their hospital
        // - Sub-doctors can edit if: they are the reviewer, it's unlocked, OR it's approved (and they have category access)
        $canEdit = false;
        if ($doctor->isAdminDoctor()) {
            // Admin can edit any questionnaire in their hospital
            $canEdit = true;
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
        
        // Get prescription for this questionnaire if it exists
        $prescription = Prescription::where('user_id', $userId)
            ->where('doctor_id', $doctor->id)
            ->whereNull('appointment_id')
            ->where('status', '!=', 'expired')
            ->first();
        
        // Get questionnaire submission data (delivery choice, medicines, pharmacy, address)
        $submission = \App\Models\QuestionnaireSubmission::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->with(['selectedPharmacy', 'deliveryAddress'])
            ->first();
        
        // Get selected medicines with details (no type; category-based selection only)
        $selectedMedicines = [];
        if ($submission && $submission->selected_medicines) {
            foreach ($submission->selected_medicines as $selected) {
                $medicine = Medicine::with('brand')->find($selected['medicine_id'] ?? null);
                if ($medicine) {
                    $selectedMedicines[] = ['medicine' => $medicine];
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
            'orders',
            'categoryMedicines'
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
        
        // Sub-doctor: can update only if they are the reviewer. Admin: can update any questionnaire in their hospital.
        if ($doctor->isSubDoctor()) {
            if (!$firstAnswer->isLockedBy($doctor->id)) {
                abort(403, 'You can only update questionnaires you are reviewing');
            }
        }
        // Admin doctor for this hospital can always update status (submission is already hospital-scoped above).
        
        // Only unlock (clear reviewing_doctor_id) for rejected or REVIEW_COMPLETED
        // Keep reviewing_doctor_id for approved so the reviewing doctor can still access it to create prescription
        $shouldUnlock = in_array($request->status, ['rejected', 'REVIEW_COMPLETED']);
        $hospitalId = $firstAnswer->hospital_id ?? $doctor->hospital_id;
        $isApproved = $request->status === 'approved';

        DB::transaction(function () use ($userId, $categoryId, $questionnaireId, $request, $shouldUnlock, $hospitalId, $doctor, $isApproved) {
            // Always ensure hospital_id is set in update data
            $updateData = [
                'status' => $request->status,
                'hospital_id' => $hospitalId, // Ensure hospital_id is always set
            ];
            if ($shouldUnlock) {
                $updateData['reviewing_doctor_id'] = null;
            }
            
            // Update ALL answers for this submission (user/category/questionnaire combination)
            // Filter by hospital_id to ensure we only update the correct hospital's records
            // Also handle backward compatibility with null hospital_id
            $updated = QuestionnaireAnswer::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('questionnaire_id', $questionnaireId)
                ->whereNull('appointment_id')
                ->where(function($query) use ($hospitalId) {
                    $query->where('hospital_id', $hospitalId)
                          ->orWhereNull('hospital_id');
                })
                ->update($updateData);

            // Doctor selects medicines and creates prescription/orders via Create Prescription flow (no auto-create on approve)
        });

        $message = __('Status updated successfully');
        if ($shouldUnlock) {
            $message = __('Review completed and questionnaire unlocked');
        }
        if ($isApproved) {
            $message = __('Questionnaire approved. Create prescription to select medicines and generate prescription and orders.');
        }

        return redirect()->route('doctor.questionnaire.show', [
            'userId' => $userId,
            'categoryId' => $categoryId,
            'questionnaireId' => $questionnaireId,
        ])->with('success', $message);
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

        if ($existingPrescription) {
            return; // Prescription already exists
        }

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
            'status' => 'approved_pending_payment',
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
            
        if ($existingPrescription) {
            return redirect()->route('doctor.questionnaire.index')
                ->with('info', __('Prescription already exists for this questionnaire.'));
        }
        
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
     */
    public function storePrescription(Request $request, $userId, $categoryId, $questionnaireId)
    {
        $request->validate([
            'medicines' => 'required|array|min:1',
            'medicines.*' => 'required|exists:medicine,id',
            'strength' => 'required|array',
            'strength.*' => 'nullable|string|max:100',
        ]);
        
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        
        if (!$doctor || !$doctor->hospital_id) {
            abort(403, 'Unauthorized access - Doctor not assigned to hospital');
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
        
        // Allow prescription creation if status is IN_REVIEW or approved
        // If IN_REVIEW, we'll auto-approve it when creating prescription
        if (!in_array($answers->status, ['approved', 'IN_REVIEW', 'under_review'])) {
            return redirect()->route('doctor.questionnaire.index')
                ->with('error', __('Questionnaire must be under review or approved before creating prescription'));
        }
        
        $existingPrescription = Prescription::where('user_id', $userId)
            ->where('doctor_id', $doctor->id)
            ->whereNull('appointment_id')
            ->where('status', '!=', 'expired')
            ->first();
            
        if ($existingPrescription) {
            return redirect()->route('doctor.questionnaire.index')
                ->with('info', __('Prescription already exists for this questionnaire.'));
        }
        
        $medicineNames = $request->input('medicine_names', []);
        $strengths = $request->input('strength', []);
        $prescriptionMedicines = [];
        $selectedForOrders = [];
        
        for ($i = 0; $i < count($request->medicines); $i++) {
            $medicineId = $request->medicines[$i];
            $medicine = Medicine::find($medicineId);
            $medicineName = $medicine ? $medicine->name : ($medicineNames[$i] ?? '');
            $strength = $strengths[$i] ?? '';
            $prescriptionMedicines[] = ['medicine' => $medicineName, 'strength' => $strength];
            $selectedForOrders[] = ['medicine_id' => (int) $medicineId];
        }
        
        $submission = QuestionnaireSubmission::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('questionnaire_id', $questionnaireId)
            ->first();
        
        // Get prescription fee from settings
        $setting = \App\Models\Setting::first();
        $prescriptionFee = $setting->prescription_fee ?? 50.00;
        
        DB::transaction(function () use ($userId, $categoryId, $questionnaireId, $doctor, $prescriptionMedicines, $submission, $selectedForOrders, $answers, $prescriptionFee) {
            // Auto-approve questionnaire if status is IN_REVIEW
            if (in_array($answers->status, ['IN_REVIEW', 'under_review'])) {
                QuestionnaireAnswer::where('user_id', $userId)
                    ->where('category_id', $categoryId)
                    ->where('questionnaire_id', $questionnaireId)
                    ->whereNull('appointment_id')
                    ->where('hospital_id', $doctor->hospital_id)
                    ->update(['status' => 'approved']);
            }
            
            Prescription::create([
                'appointment_id' => null,
                'user_id' => $userId,
                'doctor_id' => $doctor->id,
                'medicines' => json_encode($prescriptionMedicines),
                'status' => 'approved_pending_payment',
                'valid_from' => null,
                'valid_until' => null,
                'validity_days' => null,
                'payment_amount' => $prescriptionFee,
                'payment_status' => 0,
            ]);
            
            if ($submission) {
                // Respect patient's delivery type choice from questionnaire submission
                if ($submission->delivery_type === 'pickup' && $submission->selected_pharmacy_id) {
                    // Patient selected PICKUP: Use the pharmacy they selected, no delivery address
                    $this->createPurchaseMedicineOrder($submission, $selectedForOrders, $submission->selected_pharmacy_id, null);
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
                        $this->createPurchaseMedicineOrder($submission, $selectedForOrders, $pharmacy->id, $submission->delivery_address_id);
                    }
                    // If no pharmacy found, skip order creation (prescription is already created)
                }
            }
        });
        
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



