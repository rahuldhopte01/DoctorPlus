<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Pharmacy;
use App\Models\PharmacyInventory;
use App\Models\Setting;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PharmacyInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('medicine_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharmacy = Pharmacy::where('user_id', auth()->user()->id)->first();
        
        if (!$pharmacy) {
            return redirect()->back()->withErrors(__('Pharmacy not found'));
        }
        
        $inventories = PharmacyInventory::with(['medicine.brand'])
            ->where('pharmacy_id', $pharmacy->id)
            ->orderBy('id', 'DESC')
            ->get();
        
        $currency = Setting::first()->currency_symbol ?? '';

        return view('pharmacyAdmin.pharmacy_inventory.index', compact('inventories', 'currency'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('medicine_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharmacy = Pharmacy::where('user_id', auth()->user()->id)->first();
        $medicines = Medicine::active()->orderBy('name')->get();

        return view('pharmacyAdmin.pharmacy_inventory.create', compact('pharmacy', 'medicines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'medicine_id' => 'bail|required|exists:medicine,id',
            'price' => 'bail|required|numeric|min:0',
            'quantity' => 'bail|required|integer|min:0',
            'low_stock_threshold' => 'bail|nullable|integer|min:0',
        ]);
        
        $pharmacy = Pharmacy::where('user_id', auth()->user()->id)->first();
        
        if (!$pharmacy) {
            return redirect()->back()->withErrors(__('Pharmacy not found'));
        }
        
        // Get the medicine to retrieve its brand_id
        $medicine = Medicine::find($request->medicine_id);
        
        if (!$medicine) {
            return redirect()->back()->withErrors(__('Medicine not found'));
        }
        
        $data = $request->all();
        $data['pharmacy_id'] = $pharmacy->id;
        $data['brand_id'] = $medicine->brand_id; // Auto-set brand_id from medicine
        
        PharmacyInventory::create($data);

        return redirect('pharmacy_inventory')->withStatus(__('Pharmacy Inventory created successfully..!!'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pharmacy = Pharmacy::where('user_id', auth()->user()->id)->first();
        $inventory = PharmacyInventory::with(['medicine.brand'])
            ->where('id', $id)
            ->where('pharmacy_id', $pharmacy->id)
            ->firstOrFail();
        
        $currency = Setting::first()->currency_symbol ?? '';

        return view('pharmacyAdmin.pharmacy_inventory.show', compact('inventory', 'currency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_if(Gate::denies('medicine_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharmacy = Pharmacy::where('user_id', auth()->user()->id)->first();
        $inventory = PharmacyInventory::with(['medicine.brand'])
            ->where('id', $id)
            ->where('pharmacy_id', $pharmacy->id)
            ->firstOrFail();
        
        $medicines = Medicine::active()->orderBy('name')->get();

        return view('pharmacyAdmin.pharmacy_inventory.edit', compact('inventory', 'pharmacy', 'medicines'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'medicine_id' => 'bail|required|exists:medicine,id',
            'price' => 'bail|required|numeric|min:0',
            'quantity' => 'bail|required|integer|min:0',
            'low_stock_threshold' => 'bail|nullable|integer|min:0',
        ]);
        
        $pharmacy = Pharmacy::where('user_id', auth()->user()->id)->first();
        $inventory = PharmacyInventory::where('id', $id)
            ->where('pharmacy_id', $pharmacy->id)
            ->firstOrFail();
        
        // Get the medicine to retrieve its brand_id
        $medicine = Medicine::find($request->medicine_id);
        
        if (!$medicine) {
            return redirect()->back()->withErrors(__('Medicine not found'));
        }
        
        $data = $request->all();
        $data['brand_id'] = $medicine->brand_id; // Auto-set brand_id from medicine
        
        $inventory->update($data);

        return redirect('pharmacy_inventory')->withStatus(__('Pharmacy Inventory updated successfully..!!'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('medicine_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharmacy = Pharmacy::where('user_id', auth()->user()->id)->first();
        $inventory = PharmacyInventory::where('id', $id)
            ->where('pharmacy_id', $pharmacy->id)
            ->firstOrFail();
        
        $inventory->delete();

        return response(['success' => true]);
    }
}
