<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\QuestionnaireSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionnaireMedicineController extends Controller
{
    /**
     * Show medicine selection page.
     * Medicines are filtered by category (category_medicine pivot).
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

        if (!$submission->hasDeliveryType()) {
            return redirect()->route('questionnaire.delivery-choice', ['categoryId' => $categoryId])
                ->with('error', __('Please select delivery method first'));
        }

        if ($submission->delivery_type === 'delivery' && !$submission->hasCompleteDeliveryAddress()) {
            return redirect()->route('questionnaire.delivery-address', ['categoryId' => $categoryId])
                ->with('error', __('Please provide delivery address'));
        }

        if ($submission->delivery_type === 'pickup' && !$submission->hasSelectedPharmacy()) {
            return redirect()->route('questionnaire.pharmacy-selection', ['categoryId' => $categoryId])
                ->with('error', __('Please select a pharmacy'));
        }

        // Medicines for this category only (via category_medicine pivot)
        $medicines = $category->medicines()
            ->with('brand')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $selectedMedicines = $submission->selected_medicines ?? [];

        return view('website.questionnaire.medicine_selection', compact(
            'category',
            'submission',
            'medicines',
            'selectedMedicines'
        ));
    }

    /**
     * Save medicine selection (no type; just medicine_id).
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
            'medicines.*.medicine_id' => 'required|exists:medicine,id',
        ], [
            'medicines.max' => __('You can select a maximum of 3 medicines.'),
        ]);

        $user = Auth::user();
        $submission = QuestionnaireSubmission::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->firstOrFail();

        $category = Category::findOrFail($categoryId);
        // Get allowed medicine IDs for this category (qualify column to avoid ambiguity)
        $allowedIds = $category->medicines()->get()->pluck('id')->toArray();

        // Limit to maximum 3 medicines
        if (count($request->medicines) > 3) {
            return response()->json([
                'success' => false,
                'message' => __('You can select a maximum of 3 medicines.'),
            ], 422);
        }
        
        $normalizedMedicines = [];
        foreach ($request->medicines as $m) {
            $id = (int) ($m['medicine_id'] ?? 0);
            if (in_array($id, $allowedIds, true)) {
                $normalizedMedicines[] = ['medicine_id' => $id];
            }
        }

        if (empty($normalizedMedicines)) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid medicine selection.'),
            ], 422);
        }
        
        // Ensure we don't exceed 3 medicines (additional safety check)
        if (count($normalizedMedicines) > 3) {
            $normalizedMedicines = array_slice($normalizedMedicines, 0, 3);
        }

        $submission->update([
            'selected_medicines' => $normalizedMedicines,
            'status' => 'medicine_selected',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Medicines selected successfully. Doctor will review your selection.'),
            'redirect_url' => url('/questionnaire/category/' . $categoryId . '/success'),
        ]);
    }
}
