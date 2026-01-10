<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PharmacyRegistration;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PharmacyRegistrationController extends Controller
{
    /**
     * Display a listing of pharmacy registrations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('pharmacy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $pharmacies = PharmacyRegistration::with('owner')
            ->orderBy('created_at', 'DESC')
            ->get();
        
        return view('superAdmin.pharmacy_registration.index', compact('pharmacies'));
    }

    /**
     * Display the specified pharmacy registration.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('pharmacy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $pharmacy = PharmacyRegistration::with(['owner', 'deliverySettings', 'deliveryMethods', 'inventory'])
            ->findOrFail($id);
        
        return view('superAdmin.pharmacy_registration.show', compact('pharmacy'));
    }

    /**
     * Approve a pharmacy registration.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        abort_if(Gate::denies('pharmacy_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $pharmacy = PharmacyRegistration::findOrFail($id);
        $pharmacy->update(['status' => 'approved']);
        
        return redirect()->back()->withStatus(__('Pharmacy approved successfully.'));
    }

    /**
     * Reject a pharmacy registration.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject($id)
    {
        abort_if(Gate::denies('pharmacy_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $pharmacy = PharmacyRegistration::findOrFail($id);
        $pharmacy->update(['status' => 'rejected']);
        
        return redirect()->back()->withStatus(__('Pharmacy rejected successfully.'));
    }

    /**
     * Toggle priority status (Mark as "My Pharmacy").
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function togglePriority($id)
    {
        abort_if(Gate::denies('pharmacy_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $pharmacy = PharmacyRegistration::findOrFail($id);
        $pharmacy->update(['is_priority' => !$pharmacy->is_priority]);
        
        $message = $pharmacy->is_priority 
            ? __('Pharmacy marked as priority.') 
            : __('Pharmacy priority removed.');
        
        return redirect()->back()->withStatus($message);
    }

    /**
     * Remove the specified pharmacy registration.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('pharmacy_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $pharmacy = PharmacyRegistration::findOrFail($id);
        $pharmacy->delete();
        
        return response(['success' => true]);
    }
}
