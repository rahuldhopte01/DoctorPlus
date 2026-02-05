<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SuperAdmin\CustomController;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineBrand;
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
        abort_if(Gate::denies('admin_medicine_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $medicines = Medicine::with('brand')->orderBy('id', 'DESC')->get();

        return view('superAdmin.medicine.medicine', compact('medicines'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('admin_medicine_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $brands = MedicineBrand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('superAdmin.medicine.create_medicine', compact('brands', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $brandId = $request->input('brand_id');
        if (is_string($brandId)) {
            $brandId = trim($brandId);
        }
        if ($brandId === null || $brandId === '' || $brandId === '0' || $brandId === 0) {
            $request->merge(['brand_id' => null]);
        }
        $description = $request->input('description');
        if (is_string($description) && trim($description) === '') {
            $request->merge(['description' => null]);
        }

        $request->validate([
            'name' => 'bail|required|max:255|unique:medicine',
            'strength' => 'nullable|max:100',
            'form' => 'nullable|max:100',
            'brand_id' => 'nullable|exists:medicine_brands,id',
            'description' => 'nullable',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:category,id',
        ]);
        $data = $request->only(['name', 'strength', 'form', 'brand_id', 'description']);
        $data['status'] = $request->has('status') ? 1 : 0;
        $medicine = Medicine::create($data);
        $medicine->categories()->sync($request->input('category_ids', []));

        return redirect('medicine')->withStatus(__('Medicines created successfully..!!'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Medicine $medicine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('admin_medicine_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $medicine = Medicine::with(['brand', 'categories'])->find($id);
        $brands = MedicineBrand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('superAdmin.medicine.edit_medicine', compact('medicine', 'brands', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $brandId = $request->input('brand_id');
        if (is_string($brandId)) {
            $brandId = trim($brandId);
        }
        if ($brandId === null || $brandId === '' || $brandId === '0' || $brandId === 0) {
            $request->merge(['brand_id' => null]);
        }
        $description = $request->input('description');
        if (is_string($description) && trim($description) === '') {
            $request->merge(['description' => null]);
        }

        $request->validate([
            'name' => 'bail|required|max:255|unique:medicine,name,'.$id.',id',
            'strength' => 'nullable|max:100',
            'form' => 'nullable|max:100',
            'brand_id' => 'nullable|exists:medicine_brands,id',
            'description' => 'nullable',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:category,id',
        ]);
        $medicine = Medicine::find($id);
        $data = $request->only(['name', 'strength', 'form', 'brand_id', 'description']);
        $data['status'] = $request->has('status') ? 1 : 0;
        $medicine->update($data);
        $medicine->categories()->sync($request->input('category_ids', []));

        return redirect('medicine')->withStatus(__('Medicines updated successfully..!!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('admin_medicine_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $medicine = Medicine::find($id);
        $medicine->delete();

        return response(['success' => true]);
    }

    public function change_status(Request $reqeust)
    {
        $treat = Medicine::find($reqeust->id);
        $data['status'] = $treat->status == 1 ? 0 : 1;
        $treat->update($data);

        return response(['success' => true]);
    }

    public function display_stock($id)
    {
        $medicine = Medicine::find($id);

        return response(['success' => true, 'data' => $medicine]);
    }

}
