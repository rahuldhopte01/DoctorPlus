<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SuperAdmin\CustomController;
use App\Models\Medicine;
use App\Models\MedicineBrand;
use App\Models\Pharmacy;
use App\Models\PharmacyInventory;
use App\Models\Setting;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('medicine_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharmacy = Pharmacy::where('user_id', auth()->user()->id)->first();
        $medicines = PharmacyInventory::with('medicine')
            ->where('pharmacy_id', $pharmacy->id)
            ->orderBy('id', 'DESC')
            ->get()
            ->map(function ($inventory) {
                return $inventory->medicine;
            });
        $currency = Setting::first()->currency_symbol;

        return view('pharmacyAdmin.medicine.medicine', compact('medicines', 'currency'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('medicine_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharmacy = Pharmacy::where('user_id', auth()->user()->id)->first();
        $brands = MedicineBrand::where('status', 1)->orderBy('name')->get();

        return view('pharmacyAdmin.medicine.create_medicine', compact('pharmacy', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'bail|required|max:255|unique:medicine',
            'brand_id' => 'nullable|exists:medicine_brands,id',
            'description' => 'bail|required',
        ]);
        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;
        Medicine::create($data);

        return redirect('medicines')->withStatus(__('Medicines created successfully..!!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('medicine_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $medicine = Medicine::find($id);
        $pharmacy = Pharmacy::find($medicine->pharmacy_id);
        $brands = MedicineBrand::where('status', 1)->orderBy('name')->get();

        return view('pharmacyAdmin.medicine.edit_medicine', compact('medicine', 'pharmacy', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'bail|required|max:255|unique:medicine,name,'.$id.',id',
            'brand_id' => 'nullable|exists:medicine_brands,id',
            'description' => 'bail|required',
        ]);
        $medicine = Medicine::find($id);
        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;
        $medicine->update($data);

        return redirect('medicines')->withStatus(__('Medicines updated successfully..!!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('medicine_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $medicine = Medicine::find($id);
        $medicine->delete();

        return response(['success' => true]);
    }
}
