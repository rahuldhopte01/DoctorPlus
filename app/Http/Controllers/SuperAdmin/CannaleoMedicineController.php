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
     * List Cannaleo medicines (synced from Curobo catalog API).
     * Each unique external_id is shown once; pharmacies carrying it are listed per row.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Representative record = the one with the lowest id per external_id.
        $representativeIds = CannaleoMedicine::selectRaw('MIN(id) as id')
            ->groupBy('external_id')
            ->pluck('id');

        $columns = ['id', 'cannaleo_pharmacy_id', 'external_id', 'name', 'category', 'price', 'thc', 'cbd', 'image', 'last_synced_at'];

        $query = CannaleoMedicine::select($columns)
            ->whereIn('id', $representativeIds)
            ->with(['categories:id,name']);

        if ($request->filled('pharmacy_id')) {
            // Show medicines available at this pharmacy (any sibling row)
            $matchingExternalIds = CannaleoMedicine::where('cannaleo_pharmacy_id', $request->pharmacy_id)
                ->pluck('external_id');
            $query->whereIn('external_id', $matchingExternalIds);
        }
        if ($request->filled('category_filter')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category.id', $request->category_filter);
            });
        }

        $medicines = $query->orderBy('name')->paginate(100)->withQueryString();

        // For the current page, load all pharmacies grouped by external_id.
        $externalIds = $medicines->pluck('external_id')->filter()->unique();
        $pharmaciesByExternalId = CannaleoMedicine::whereIn('external_id', $externalIds)
            ->select('external_id', 'cannaleo_pharmacy_id')
            ->with('cannaleoPharmacy:id,name')
            ->get()
            ->groupBy('external_id')
            ->map(fn($group) => $group
                ->map(fn($m) => $m->cannaleoPharmacy)
                ->filter()
                ->unique('id')
                ->values()
            );

        $pharmacies = CannaleoPharmacy::orderBy('name')->get(['id', 'name']);
        $categories = \App\Models\Category::orderBy('name')->get(['id', 'name']);

        return view('superAdmin.cannaleo.medicine_index', compact('medicines', 'pharmacies', 'categories', 'pharmaciesByExternalId'));
    }

    /**
     * Show the edit form for image and description of a synced medicine.
     */
    public function edit($id)
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $medicine = CannaleoMedicine::with('cannaleoPharmacy')->findOrFail($id);

        // All pharmacies that carry this medicine (same external_id).
        $siblingPharmacies = CannaleoMedicine::where('external_id', $medicine->external_id)
            ->with('cannaleoPharmacy:id,name')
            ->get()
            ->map(fn($m) => $m->cannaleoPharmacy)
            ->filter()
            ->unique('id')
            ->values();

        return view('superAdmin.cannaleo.medicine_edit', compact('medicine', 'siblingPharmacies'));
    }

    /**
     * Update image and description for a synced medicine.
     * Changes are propagated to ALL records sharing the same external_id.
     * API-synced fields (name, price, thc, cbd, etc.) are never modified here.
     */
    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $medicine = CannaleoMedicine::findOrFail($id);

        $request->validate([
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description'  => 'nullable|string',
            'remove_image' => 'nullable|boolean',
        ]);

        // Collect all sibling rows (same external_id) so we can delete their old images.
        $siblings = CannaleoMedicine::where('external_id', $medicine->external_id)->get();

        $data = [
            'description' => $request->input('description'),
        ];

        if ($request->boolean('remove_image')) {
            // Delete every unique image file used across sibling rows.
            $siblings->pluck('image')->filter()->unique()->each(function ($img) {
                (new CustomController)->deleteFile($img);
            });
            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            // Delete existing images from all siblings before uploading the new one.
            $siblings->pluck('image')->filter()->unique()->each(function ($img) {
                (new CustomController)->deleteFile($img);
            });
            $data['image'] = (new CustomController)->imageUpload($request->file('image'));
        }

        // Apply changes to every sibling so they all stay in sync.
        CannaleoMedicine::where('external_id', $medicine->external_id)->update($data);

        return redirect()->route('cannaleo.medicines.index')
            ->with('status', __('Medicine updated successfully.'));
    }
}
