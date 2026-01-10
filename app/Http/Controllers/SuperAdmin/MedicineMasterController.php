<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MedicineMaster;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MedicineMasterController extends Controller
{
    /**
     * Display a listing of medicines.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('medicine_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $medicines = MedicineMaster::with('brands')
            ->orderBy('name', 'ASC')
            ->get();
        
        return view('superAdmin.medicine_master.index', compact('medicines'));
    }

    /**
     * Show the form for creating a new medicine.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('medicine_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        return view('superAdmin.medicine_master.create');
    }

    /**
     * Store a newly created medicine.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('medicine_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'strength' => 'nullable|string|max:100',
            'form' => 'nullable|string|max:100',
        ]);
        
        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;
        
        MedicineMaster::create($data);
        
        return redirect('medicine_master')->withStatus(__('Medicine created successfully.'));
    }

    /**
     * Display the specified medicine.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('medicine_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $medicine = MedicineMaster::with('brands')->findOrFail($id);
        
        return view('superAdmin.medicine_master.show', compact('medicine'));
    }

    /**
     * Get brands for a medicine (AJAX).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBrands($id)
    {
        $medicine = MedicineMaster::with('activeBrands')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'brands' => $medicine->activeBrands
        ]);
    }

    /**
     * Show the form for editing the specified medicine.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('medicine_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $medicine = MedicineMaster::findOrFail($id);
        
        return view('superAdmin.medicine_master.edit', compact('medicine'));
    }

    /**
     * Update the specified medicine.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('medicine_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'strength' => 'nullable|string|max:100',
            'form' => 'nullable|string|max:100',
        ]);
        
        $medicine = MedicineMaster::findOrFail($id);
        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;
        
        $medicine->update($data);
        
        return redirect('medicine_master')->withStatus(__('Medicine updated successfully.'));
    }

    /**
     * Remove the specified medicine.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('medicine_delete'), Response::HTTP_FORBIDDEN, '403_FORBIDDEN');
        
        $medicine = MedicineMaster::findOrFail($id);
        $medicine->delete();
        
        return response(['success' => true]);
    }
}
