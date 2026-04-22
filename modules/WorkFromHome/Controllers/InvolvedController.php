<?php

namespace Modules\WorkFromHome\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;

class InvolvedController extends Controller
{
    public function __construct(
        protected WorkFromHomeRepository $workFromHomes
    ) {}

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->workFromHomes->with(['logs', 'status', 'requester.employee'])
                ->whereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                })->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_id', function ($row) {
                    return $row->getRequestId();
                })->addColumn('type', function ($row) {
                    return $row->getTypeName();
                })->editColumn('request_date', function ($row) {
                    return $row->getRequestDate();
                })->editColumn('start_date', function ($row) {
                    return $row->getStartDate();
                })->editColumn('end_date', function ($row) {
                    return $row->getEndDate();
                })->addColumn('total_days', function ($row) {
                    return $row->getTotalDays();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('wfh.requests.show', $row->id) . '" title="View Request"><i class="bi bi-eye"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('WorkFromHome::involved.index');
    }
}
