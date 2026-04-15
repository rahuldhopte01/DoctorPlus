<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SuperAdmin\CustomController;
use App\Models\CannaleoMedicine;
use App\Models\CannaleoPharmacy;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CannaleoMedicineController extends Controller
{
    /**
     * List Cannaleo medicines (synced from Curobo catalog API). Shows pharmacy, categories assigned.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Exclude raw_data — it's a large JSON blob only needed on the edit form.
        $columns = ['id', 'cannaleo_pharmacy_id', 'external_id', 'name', 'category', 'price', 'thc', 'cbd', 'image', 'last_synced_at'];

        $query = CannaleoMedicine::select($columns)
            ->with([
                'cannaleoPharmacy:id,name',
                'categories:id,name',
            ]);

        if ($request->filled('pharmacy_id')) {
            $query->where('cannaleo_pharmacy_id', $request->pharmacy_id);
        }
        if ($request->filled('category_filter')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category.id', $request->category_filter);
            });
        }

        $medicines = $query->orderBy('name')->paginate(100)->withQueryString();
        $pharmacies = CannaleoPharmacy::orderBy('name')->get(['id', 'name']);
        $categories = \App\Models\Category::orderBy('name')->get(['id', 'name']);

        return view('superAdmin.cannaleo.medicine_index', compact('medicines', 'pharmacies', 'categories'));
    }

    /**
     * Show the edit form for image and description of a synced medicine.
     */
    public function edit($id)
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $medicine = CannaleoMedicine::with('cannaleoPharmacy')->findOrFail($id);

        return view('superAdmin.cannaleo.medicine_edit', compact('medicine'));
    }

    /**
     * Update only image and description for a synced medicine.
     * API-synced fields (name, price, thc, cbd, etc.) are never modified here.
     */
    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $medicine = CannaleoMedicine::findOrFail($id);

        $request->validate([
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string',
            'remove_image' => 'nullable|boolean',
        ]);

        $data = [
            'description' => $request->input('description'),
        ];

        if ($request->boolean('remove_image') && $medicine->image) {
            (new CustomController)->deleteFile($medicine->image);
            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($medicine->image) {
                (new CustomController)->deleteFile($medicine->image);
            }
            $data['image'] = (new CustomController)->imageUpload($request->file('image'));
        }

        $medicine->update($data);

        return redirect()->route('cannaleo.medicines.index')
            ->with('status', __('Medicine updated successfully.'));
    }
}
