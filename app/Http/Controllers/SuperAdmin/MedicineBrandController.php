<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MedicineBrand;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MedicineBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('medicine_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $medicineBrands = MedicineBrand::with('medicines')->orderBy('id', 'DESC')->get();

        return view('superAdmin.medicine_brand.medicine_brand', compact('medicineBrands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('medicine_category_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('superAdmin.medicine_brand.create_medicine_brand');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'bail|required|max:255',
        ]);
        $data = $request->all();
        MedicineBrand::create($data);

        return redirect('medicineBrand')->withStatus(__('Medicine Brand Created Successfully...!!'));
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
        abort_if(Gate::denies('medicine_category_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $medicineBrand = MedicineBrand::findOrFail($id);

        return view('superAdmin.medicine_brand.edit_medicine_brand', compact('medicineBrand'));
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
            'name' => 'bail|required|max:255',
        ]);
        $data = $request->all();
        MedicineBrand::findOrFail($id)->update($data);

        return redirect('medicineBrand')->withStatus(__('Medicine Brand Updated Successfully...!!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $medicineBrand = MedicineBrand::findOrFail($id);
        $medicineBrand->delete();

        return response(['success' => true]);
    }

}
