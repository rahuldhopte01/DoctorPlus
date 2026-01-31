<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Pharmacy;
use App\Models\QuestionnaireSubmission;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionnairePharmacyController extends Controller
{
    /**
     * Show pharmacy selection page
     */
    public function showPharmacySelection($categoryId)
    {
        if (!Auth::check()) {
            return redirect('/patient-login')->with('info', __('Please login to continue'));
        }

        $category = Category::findOrFail($categoryId);
        $user = Auth::user();

        // Get submission record
        $submission = QuestionnaireSubmission::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->firstOrFail();

        if ($submission->delivery_type !== 'pickup') {
            return redirect()->route('questionnaire.delivery-choice', ['categoryId' => $categoryId])
                ->with('error', __('Please select pickup option first'));
        }

        // Get user's address to calculate distance
        $userAddress = UserAddress::where('user_id', $user->id)->first();
        
        // Get active pharmacies
        $pharmacies = Pharmacy::where('status', 'approved')
            ->get();

        // Calculate distance and sort if user has address
        if ($userAddress && !empty($userAddress->lat) && !empty($userAddress->lang)) {
            $lat = (float) $userAddress->lat;
            $lng = (float) $userAddress->lang;
            
            // Get pharmacies with distance calculation
            $pharmacies = Pharmacy::select('pharmacy.*')
                ->selectRaw('(3959 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lang) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance', [$lat, $lng, $lat])
                ->where('status', 'approved')
                ->having('distance', '<', 50) // Within 50 miles
                ->orderBy('distance')
                ->get();
        } else {
            // No address, just show active pharmacies
            $pharmacies = Pharmacy::where('status', 'approved')
                ->orderBy('name')
                ->get();
        }

        // Group by city if possible
        $pharmaciesByCity = $pharmacies->groupBy(function ($pharmacy) {
            return $pharmacy->postcode ? substr($pharmacy->postcode, 0, 2) : 'other';
        });

        $selectedPharmacyId = $submission->selected_pharmacy_id;

        return view('website.questionnaire.pharmacy_selection', compact(
            'category',
            'submission',
            'pharmacies',
            'pharmaciesByCity',
            'selectedPharmacyId'
        ));
    }

    /**
     * Save pharmacy selection
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
            'pharmacy_id' => 'required|exists:pharmacy,id',
        ]);

        $user = Auth::user();
        $submission = QuestionnaireSubmission::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->firstOrFail();

        // Verify pharmacy is active
        $pharmacy = Pharmacy::where('id', $request->pharmacy_id)
            ->where('status', 'approved')
            ->firstOrFail();

        $submission->update([
            'selected_pharmacy_id' => $request->pharmacy_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Pharmacy selected successfully'),
            'redirect_url' => url('/questionnaire/category/' . $categoryId . '/medicine-selection'),
        ]);
    }
}
