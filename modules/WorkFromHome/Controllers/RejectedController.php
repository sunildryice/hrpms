<?php

namespace Modules\WorkFromHome\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Modules\WorkFromHome\Requests\Approve\UpdateRequest;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\WorkFromHome\Repositories\WorkFromHomeLogRepository;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;

class RejectedController extends Controller
{
    public function __construct(
        protected ProjectCodeRepository $projects,
        protected UserRepository $users,
        protected WorkFromHomeRepository $workFromHomes,
        protected WorkFromHomeLogRepository $workFromHomeLogs,
        protected FiscalYearRepository $fiscalYears
    ) {}



    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = $this->workFromHomes
                ->where('status_id', '=', config('constant.REJECTED_STATUS'))
                ->where('approver_id', '=', auth()->id())
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('request_date', function ($row) {
                    return $row->getRequestDate();
                })
                ->editColumn('start_date', function ($row) {
                    return $row->getStartDate();
                })
                ->editColumn('end_date', function ($row) {
                    return $row->getEndDate();
                })
                ->addColumn('project', function ($row) {
                    return $row->project->title ?? '-';
                })
                ->addColumn('employee', function ($row) {
                    return $row->requester->employee->full_name ?? $row->requester->full_name ?? '-';
                })
                ->addColumn('request_id', function ($row) {
                    return $row->getRequestId();
                })
                ->addColumn('total_days', function ($row) {
                    return $row->getTotalDays();
                })
                ->addColumn('status', function ($row) {

                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('rejected.wfh.requests.show', $row->id) . '" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye"></i> 
                    </a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('WorkFromHome::rejected.index');
    }

    public function show($id)
    {
        $wfhRequest = $this->workFromHomes
            ->with('logs')->find($id);


        return view('WorkFromHome::rejected.show', compact('wfhRequest'));
    }
}
