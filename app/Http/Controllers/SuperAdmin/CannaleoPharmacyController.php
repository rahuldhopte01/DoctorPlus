<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CannaleoPharmacy;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CannaleoPharmacyController extends Controller
{
    /**
     * List Cannaleo pharmacies (synced from Curobo catalog API). Read-only.
     */
    public function index()
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharmacies = CannaleoPharmacy::withCount('cannaleoMedicines')
            ->orderBy('name')
            ->get();

        return view('superAdmin.cannaleo.pharmacy_index', compact('pharmacies'));
    }
}
