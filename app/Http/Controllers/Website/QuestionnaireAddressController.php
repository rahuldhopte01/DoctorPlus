<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\QuestionnaireSubmission;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionnaireAddressController extends Controller
{
    /**
     * Show address form for delivery
     */
    public function showAddressForm($categoryId)
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

        if ($submission->delivery_type !== 'delivery') {
            return redirect()->route('questionnaire.delivery-choice', ['categoryId' => $categoryId])
                ->with('error', __('Please select delivery option first'));
        }

        // Get existing addresses
        $existingAddresses = UserAddress::where('user_id', $user->id)->get();

        return view('website.questionnaire.delivery_address', compact('category', 'submission', 'existingAddresses'));
    }

    /**
     * Save delivery address
     */
    public function saveAddress(Request $request, $categoryId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('Please login to continue'),
            ], 401);
        }

        $request->validate([
            'address' => 'required|string|max:500',
            'postcode' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'lat' => 'nullable|numeric',
            'lang' => 'nullable|numeric',
            'address_id' => 'nullable|exists:user_address,id',
        ]);

        $user = Auth::user();
        $submission = QuestionnaireSubmission::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->firstOrFail();

        // If using existing address
        if ($request->has('address_id') && $request->address_id) {
            $address = UserAddress::where('id', $request->address_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $submission->update([
                'delivery_address_id' => $address->id,
                'delivery_address' => $address->address,
                'delivery_postcode' => $request->postcode,
                'delivery_city' => $request->city,
                'delivery_state' => $request->state,
            ]);
        } else {
            // Create new address or update existing
            $address = UserAddress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'id' => $request->address_id ?? null,
                ],
                [
                    'address' => $request->address,
                    'lat' => $request->lat ?? '0',
                    'lang' => $request->lang ?? '0',
                    'label' => 'Delivery Address',
                ]
            );

            $submission->update([
                'delivery_address_id' => $address->id,
                'delivery_address' => $request->address,
                'delivery_postcode' => $request->postcode,
                'delivery_city' => $request->city,
                'delivery_state' => $request->state,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Address saved successfully'),
            'redirect_url' => url('/questionnaire/category/' . $categoryId . '/medicine-selection'),
        ]);
    }
}
