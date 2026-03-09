<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CannaleoPrescriptionLog;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CannaleoPrescriptionLogController extends Controller
{
    /**
     * List Cannaleo prescription API logs (order list). Read-only.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = CannaleoPrescriptionLog::with(['prescription', 'questionnaireSubmission'])
            ->orderByDesc('called_at');

        if ($request->filled('prescription_id')) {
            $query->where('prescription_id', $request->prescription_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('called_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('called_at', '<=', $request->to_date);
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('superAdmin.cannaleo.prescription_log_index', compact('logs'));
    }

    /**
     * Show a single Cannaleo prescription log (request/response details).
     */
    public function show($id)
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $log = CannaleoPrescriptionLog::with(['prescription', 'questionnaireSubmission'])->findOrFail($id);

        return view('superAdmin.cannaleo.prescription_log_show', compact('log'));
    }
}
