<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\MedicineMaster;
use App\Models\MedicineBrand;
use App\Models\PharmacyInventory;
use App\Models\PharmacyRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get pharmacy using owner_user_id (new system)
        $pharmacy = PharmacyRegistration::where('owner_user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();
        
        $inventory = PharmacyInventory::with(['medicine', 'brand'])
            ->where('pharmacy_id', $pharmacy->id)
            ->orderBy('created_at', 'DESC')
            ->get();
        
        return view('pharmacyAdmin.inventory.index', compact('inventory', 'pharmacy'));
    }

    /**
     * Show the form for creating a new inventory item.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pharmacy = PharmacyRegistration::where('owner_user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();
        
        $medicines = MedicineMaster::active()->with('activeBrands')->get();
        
        return view('pharmacyAdmin.inventory.create', compact('pharmacy', 'medicines'));
    }

    /**
     * Store a newly created inventory item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'medicine_brand_id' => 'required|exists:medicine_brands,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);
        
        $pharmacy = PharmacyRegistration::where('owner_user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();
        
        // Check if this combination already exists
        $existing = PharmacyInventory::where('pharmacy_id', $pharmacy->id)
            ->where('medicine_id', $request->medicine_id)
            ->where('medicine_brand_id', $request->medicine_brand_id)
            ->first();
        
        if ($existing) {
            return redirect()->back()
                ->withErrors(['medicine_brand_id' => __('This medicine and brand combination already exists in inventory. Please update the existing entry instead.')])
                ->withInput();
        }
        
        $data = $request->all();
        $data['pharmacy_id'] = $pharmacy->id;
        
        PharmacyInventory::create($data);
        
        return redirect()->route('pharmacy.inventory.index')
            ->withStatus(__('Inventory item added successfully.'));
    }

    /**
     * Display the specified inventory item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pharmacy = PharmacyRegistration::where('owner_user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();
        
        $inventory = PharmacyInventory::with(['medicine', 'brand', 'pharmacy'])
            ->where('pharmacy_id', $pharmacy->id)
            ->findOrFail($id);
        
        return view('pharmacyAdmin.inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified inventory item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pharmacy = PharmacyRegistration::where('owner_user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();
        
        $inventory = PharmacyInventory::where('pharmacy_id', $pharmacy->id)
            ->findOrFail($id);
        
        $medicines = MedicineMaster::active()->with('activeBrands')->get();
        
        return view('pharmacyAdmin.inventory.edit', compact('inventory', 'medicines', 'pharmacy'));
    }

    /**
     * Update the specified inventory item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);
        
        $pharmacy = PharmacyRegistration::where('owner_user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();
        
        $inventory = PharmacyInventory::where('pharmacy_id', $pharmacy->id)
            ->findOrFail($id);
        
        $inventory->update($request->only(['price', 'quantity', 'low_stock_threshold']));
        
        return redirect()->route('pharmacy.inventory.index')
            ->withStatus(__('Inventory item updated successfully.'));
    }

    /**
     * Remove the specified inventory item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pharmacy = PharmacyRegistration::where('owner_user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();
        
        $inventory = PharmacyInventory::where('pharmacy_id', $pharmacy->id)
            ->findOrFail($id);
        
        $inventory->delete();
        
        return response(['success' => true]);
    }
}
