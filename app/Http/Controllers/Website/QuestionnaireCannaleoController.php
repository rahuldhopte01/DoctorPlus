<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SuperAdmin\CustomController;
use App\Models\Category;
use App\Models\CannaleoMedicine;
use App\Models\CannaleoPharmacy;
use App\Models\QuestionnaireSubmission;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionnaireCannaleoController extends Controller
{
    /**
     * Show Cannaleo pharmacy selection (pharmacies that have medicines assigned to this category).
     */
    public function showPharmacySelection($categoryId)
    {
        if (!Auth::check()) {
            return redirect('/patient-login')->with('info', __('Please login to continue'));
        }

        $category = Category::findOrFail($categoryId);
        $user = Auth::user();

        $submission = QuestionnaireSubmission::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->firstOrFail();

        // Cannaleo-only category: no delivery choice; ensure delivery_type is set
        if ($category->is_cannaleo_only) {
            if ($submission->delivery_type !== 'cannaleo') {
                $submission->update(['delivery_type' => 'cannaleo']);
            }
        } elseif ($submission->delivery_type !== 'cannaleo') {
            return redirect()->route('questionnaire.delivery-choice', ['categoryId' => $categoryId])
                ->with('error', __('Invalid flow. Please choose delivery method first.'));
        }

        // Cannaleo-only category: show all partner pharmacies; otherwise only those with medicines assigned to this category
        if ($category->is_cannaleo_only) {
            $pharmacies = CannaleoPharmacy::has('cannaleoMedicines')->orderBy('name')->get();
        } else {
            $pharmacyIds = $category->cannaleoMedicines()->pluck('cannaleo_pharmacy_id')->unique()->filter()->values();
            $pharmacies = CannaleoPharmacy::whereIn('id', $pharmacyIds)->orderBy('name')->get();
        }

        $selectedPharmacyId = $submission->selected_cannaleo_pharmacy_id;
        $isCannaleoOnly = $category->is_cannaleo_only;

        return view('website.questionnaire.cannaleo_pharmacy_selection', compact(
            'category',
            'submission',
            'pharmacies',
            'selectedPharmacyId',
            'isCannaleoOnly'
        ));
    }

    /**
     * Save Cannaleo pharmacy selection.
     */
    public function savePharmacySelection(Request $request, $categoryId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('Please login to continue'),
            ], 401);
        }

        $request->validate([
            'cannaleo_pharmacy_id' => 'required|exists:cannaleo_pharmacy,id',
        ]);

        $category = Category::findOrFail($categoryId);
        $user = Auth::user();
        $submission = QuestionnaireSubmission::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->firstOrFail();

        if ($submission->delivery_type !== 'cannaleo') {
            return response()->json(['success' => false, 'message' => __('Invalid flow.')], 422);
        }

        // Cannaleo-only: any pharmacy with medicines is allowed; otherwise verify pharmacy has medicines for this category
        if ($category->is_cannaleo_only) {
            $allowedPharmacyIds = CannaleoPharmacy::has('cannaleoMedicines')->pluck('id')->toArray();
        } else {
            $allowedPharmacyIds = $category->cannaleoMedicines()->pluck('cannaleo_pharmacy_id')->unique()->toArray();
        }
        if (!in_array((int) $request->cannaleo_pharmacy_id, $allowedPharmacyIds, true)) {
            return response()->json(['success' => false, 'message' => __('Selected pharmacy is not available for this category.')], 422);
        }

        $submission->update([
            'selected_cannaleo_pharmacy_id' => $request->cannaleo_pharmacy_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Pharmacy selected successfully'),
            'redirect_url' => url('/questionnaire/category/' . $categoryId . '/cannaleo-medicine-selection'),
        ]);
    }

    /**
     * Show Cannaleo medicine selection (for selected pharmacy + category).
     */
    public function showMedicineSelection($categoryId)
    {
        if (!Auth::check()) {
            return redirect('/patient-login')->with('info', __('Please login to continue'));
        }

        $category = Category::findOrFail($categoryId);
        $user = Auth::user();

        $submission = QuestionnaireSubmission::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->firstOrFail();

        if ($submission->delivery_type !== 'cannaleo' || !$submission->hasSelectedCannaleoPharmacy()) {
            return redirect()->route('questionnaire.cannaleo-pharmacy-selection', ['categoryId' => $categoryId])
                ->with('error', __('Please select a Cannaleo pharmacy first.'));
        }

        // Cannaleo-only category: show all medicines from selected pharmacy; otherwise only those assigned to this category
        if ($category->is_cannaleo_only) {
            $medicines = CannaleoMedicine::where('cannaleo_pharmacy_id', $submission->selected_cannaleo_pharmacy_id)
                ->with('cannaleoPharmacy')
                ->orderBy('name')
                ->get();
        } else {
            $medicines = $category->cannaleoMedicines()
                ->where('cannaleo_pharmacy_id', $submission->selected_cannaleo_pharmacy_id)
                ->with('cannaleoPharmacy')
                ->orderBy('name')
                ->get();
        }

        $selectedMedicines = $submission->selected_medicines ?? [];
        $selectedCannaleoIds = array_filter(array_column($selectedMedicines, 'cannaleo_medicine_id'));

        return view('website.questionnaire.cannaleo_medicine_selection', compact(
            'category',
            'submission',
            'medicines',
            'selectedCannaleoIds'
        ));
    }

    /**
     * Save Cannaleo medicine selection.
     */
    public function saveMedicineSelection(Request $request, $categoryId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('Please login to continue'),
            ], 401);
        }

        $medicines = $request->input('medicines');
        if (is_string($medicines)) {
            $medicines = json_decode($medicines, true);
        }
        $request->merge(['medicines' => $medicines ?? []]);

        $request->validate([
            'medicines' => 'required|array|min:1|max:3',
            'medicines.*.cannaleo_medicine_id' => 'required|exists:cannaleo_medicine,id',
        ], [
            'medicines.max' => __('You can select a maximum of 3 medicines.'),
        ]);

        $user = Auth::user();
        $submission = QuestionnaireSubmission::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->firstOrFail();

        if ($submission->delivery_type !== 'cannaleo' || !$submission->hasSelectedCannaleoPharmacy()) {
            return response()->json(['success' => false, 'message' => __('Invalid flow.')], 422);
        }

        $category = Category::findOrFail($categoryId);
        if ($category->is_cannaleo_only) {
            $allowedIds = CannaleoMedicine::where('cannaleo_pharmacy_id', $submission->selected_cannaleo_pharmacy_id)->pluck('id')->toArray();
        } else {
            $allowedIds = $category->cannaleoMedicines()
                ->where('cannaleo_pharmacy_id', $submission->selected_cannaleo_pharmacy_id)
                ->pluck('id')
                ->toArray();
        }

        $normalized = [];
        foreach ($request->medicines as $m) {
            $id = (int) ($m['cannaleo_medicine_id'] ?? 0);
            if (in_array($id, $allowedIds, true)) {
                $normalized[] = ['cannaleo_medicine_id' => $id];
            }
        }
        if (count($normalized) > 3) {
            $normalized = array_slice($normalized, 0, 3);
        }
        if (empty($normalized)) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid medicine selection.'),
            ], 422);
        }

        $submission->update([
            'selected_medicines' => $normalized,
            'status' => 'medicine_selected',
        ]);

        // Send questionnaire submitted email with amount paid (after successful medicine selection)
        $this->sendQuestionnaireSubmittedEmailWithAmount($user, $submission, $category);

        return response()->json([
            'success' => true,
            'message' => __('Medicines selected successfully. Doctor will review your selection.'),
            'redirect_url' => url('/questionnaire/category/' . $categoryId . '/success'),
        ]);
    }

    /**
     * Send questionnaire submitted email to user with amount paid (after medicine selection).
     */
    protected function sendQuestionnaireSubmittedEmailWithAmount($user, $submission, $category): void
    {
        $submissionId = 'REF-' . $submission->id . '-' . $category->id;
        $data = [
            'customer_name' => $user->name,
            'submission_id' => $submissionId,
            'submission_date' => $submission->updated_at?->format('F j, Y H:i') ?? now()->format('F j, Y H:i'),
            'questionnaire_category' => $category->name ?? ($category->treatment ? $category->treatment->name : __('Questionnaire')),
            'review_timeframe' => __('24-48 hours'),
        ];
        $setting = Setting::first();
        $fee = (float) ($setting->questionnaire_submission_fee ?? $setting->prescription_fee ?? 50.00);
        $symbol = $setting->currency_symbol ?? '€';
        $data['base_price'] = number_format($fee, 2) . ' ' . $symbol;
        $data['total_amount_paid'] = number_format($fee, 2) . ' ' . $symbol;
        $data['payment_date'] = now()->format('F j, Y H:i');
        (new CustomController)->sendQuestionnaireSubmittedMail($user->email, $data, true);
    }
}
