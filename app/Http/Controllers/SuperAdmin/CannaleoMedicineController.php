<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
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

        $query = CannaleoMedicine::with(['cannaleoPharmacy', 'categories']);

        if ($request->filled('pharmacy_id')) {
            $query->where('cannaleo_pharmacy_id', $request->pharmacy_id);
        }
        if ($request->filled('category_filter')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category.id', $request->category_filter);
            });
        }

        $medicines = $query->orderBy('name')->get();
        $pharmacies = CannaleoPharmacy::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('superAdmin.cannaleo.medicine_index', compact('medicines', 'pharmacies', 'categories'));
    }
}
