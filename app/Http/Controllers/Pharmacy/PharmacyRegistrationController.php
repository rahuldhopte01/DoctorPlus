<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\PharmacyDeliveryMethod;
use App\Models\PharmacyDeliverySetting;
use App\Models\PharmacyRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PharmacyRegistrationController extends Controller
{
    /**
     * Display delivery settings form.
     *
     * @return \Illuminate\Http\Response
     */
    public function deliverySettings()
    {
        $pharmacy = PharmacyRegistration::where('owner_user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();
        
        $deliverySetting = $pharmacy->deliverySettings;
        $deliveryMethods = $pharmacy->deliveryMethods;
        
        return view('pharmacyAdmin.delivery_settings.index', compact('pharmacy', 'deliverySetting', 'deliveryMethods'));
    }

    /**
     * Update delivery settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDeliverySettings(Request $request)
    {
        $pharmacy = PharmacyRegistration::where('owner_user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();
        
        $request->validate([
            'delivery_type' => 'required|in:pickup_only,delivery_only,pickup_delivery',
            'delivery_radius' => 'nullable|numeric|min:0',
            'delivery_methods' => 'nullable|array',
            'delivery_methods.*' => 'in:standard,express,same_day',
        ]);
        
        // Update or create delivery settings
        $deliverySetting = PharmacyDeliverySetting::updateOrCreate(
            ['pharmacy_id' => $pharmacy->id],
            [
                'delivery_type' => $request->delivery_type,
                'delivery_radius' => $request->delivery_radius,
            ]
        );
        
        // Update delivery methods
        if ($request->has('delivery_methods')) {
            // Deactivate all methods first
            PharmacyDeliveryMethod::where('pharmacy_id', $pharmacy->id)->update(['is_active' => false]);
            
            // Activate selected methods
            foreach ($request->delivery_methods as $method) {
                PharmacyDeliveryMethod::updateOrCreate(
                    ['pharmacy_id' => $pharmacy->id, 'delivery_method' => $method],
                    ['is_active' => true]
                );
            }
        } else {
            // If no methods selected, deactivate all
            PharmacyDeliveryMethod::where('pharmacy_id', $pharmacy->id)->update(['is_active' => false]);
        }
        
        return redirect()->back()->with('status', __('Delivery settings updated successfully.'));
    }
}
