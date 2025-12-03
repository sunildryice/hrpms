<?php

namespace Modules\WorkFromHome\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\WorkFromHome\Repositories\WorkFromHomeLogRepository;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;

class ApprovedController extends Controller
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
                ->where('status_id', '=', config('constant.APPROVED_STATUS'))
                ->where('approver_id', '=', auth()->id());

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('request_date', function ($row) {
                    return $row->request_date ? Carbon::parse($row->request_date)->format('Y-m-d') : '';
                })
                ->editColumn('start_date', function ($row) {
                    return $row->start_date ? Carbon::parse($row->start_date)->format('Y-m-d') : '';
                })
                ->editColumn('end_date', function ($row) {
                    return $row->end_date ? Carbon::parse($row->end_date)->format('Y-m-d') : '';
                })
                ->addColumn('project', function ($row) {
                    return $row->project->short_name ?? '-';
                })
                ->addColumn('request_id', function ($row) {
                    return $row->getRequestId();
                })
                ->addColumn('status', function ($row) {

                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('approved.wfh.requests.show', $row->id) . '" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye"></i> 
                    </a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('WorkFromHome::approved.index');
    }

    public function show($id)
    {
        $wfhRequest = $this->workFromHomes
            ->with('logs')->find($id);


        return view('WorkFromHome::approved.show', compact('wfhRequest'));
    }
}
