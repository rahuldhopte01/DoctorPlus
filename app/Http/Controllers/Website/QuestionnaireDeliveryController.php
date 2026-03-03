<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\QuestionnaireSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionnaireDeliveryController extends Controller
{
    /**
     * Show delivery choice page (Delivery or Pickup)
     */
    public function showDeliveryChoice($categoryId)
    {
        if (!Auth::check()) {
            return redirect('/patient-login')->with('info', __('Please login to continue'));
        }

        $category = Category::with('cannaleoMedicines')->findOrFail($categoryId);
        $user = Auth::user();

        // Cannaleo-only categories: no delivery choice; go straight to Cannaleo pharmacy selection
        if ($category->is_cannaleo_only) {
            $submission = QuestionnaireSubmission::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'category_id' => $categoryId,
                ],
                [
                    'questionnaire_id' => $category->questionnaire->id ?? null,
                    'status' => 'pending',
                    'delivery_type' => 'cannaleo',
                ]
            );
            if ($submission->delivery_type !== 'cannaleo') {
                $submission->update(['delivery_type' => 'cannaleo']);
            }
            return redirect()->route('questionnaire.cannaleo-pharmacy-selection', ['categoryId' => $categoryId]);
        }

        // Get or create submission record
        $submission = QuestionnaireSubmission::firstOrCreate(
            [
                'user_id' => $user->id,
                'category_id' => $categoryId,
            ],
            [
                'questionnaire_id' => $category->questionnaire->id ?? null,
                'status' => 'pending',
            ]
        );

        return view('website.questionnaire.delivery_choice', compact('category', 'submission'));
    }

    /**
     * Save delivery choice (Delivery or Pickup)
     */
    public function saveDeliveryChoice(Request $request, $categoryId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('Please login to continue'),
            ], 401);
        }

        $request->validate([
            'delivery_type' => 'required|in:delivery,pickup',
        ]);

        $category = Category::findOrFail($categoryId);
        $user = Auth::user();

        $submission = QuestionnaireSubmission::updateOrCreate(
            [
                'user_id' => $user->id,
                'category_id' => $categoryId,
            ],
            [
                'questionnaire_id' => $category->questionnaire->id ?? null,
                'delivery_type' => $request->delivery_type,
                'status' => 'pending',
            ]
        );

        // Redirect based on choice (Cannaleo-only categories are redirected before this page)
        if ($request->delivery_type === 'delivery') {
            if (!$submission->hasCompleteDeliveryAddress()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => url('/questionnaire/category/' . $categoryId . '/delivery-address'),
                ]);
            }
            return response()->json([
                'success' => true,
                'redirect_url' => url('/questionnaire/category/' . $categoryId . '/medicine-selection'),
            ]);
        }
        // Pickup
        return response()->json([
            'success' => true,
            'redirect_url' => url('/questionnaire/category/' . $categoryId . '/pharmacy-selection'),
        ]);
    }
}
