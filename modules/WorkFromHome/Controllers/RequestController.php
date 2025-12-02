<?php

namespace Modules\WorkFromHome\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\WorkFromHome\Notifications\WorkFromHomeRequestSubmitted;
use Modules\WorkFromHome\Repositories\WorkFromHomeLogRepository;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;
use Modules\WorkFromHome\Requests\StoreRequest;

class RequestController extends Controller
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
            $query = $this->workFromHomes->where('requester_id', '=', auth()->id());

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
                ->addColumn('status', function ($row) {

                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('wfh.requests.show', $row->id) . '" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye"></i> 
                    </a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('WorkFromHome::index');
    }

    public function create()
    {

        $authUser = auth()->user();

        $projects = $this->projects->pluck('title', 'id');
        $supervisors = $this->users->getSupervisors($authUser)->pluck('full_name', 'id');

        return view('WorkFromHome::create', [
            'projects' => $projects,
            'supervisors' => $supervisors,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();

        $inputs = $request->validated();

        try {
            $inputs['requester_id'] = auth()->id();
            $inputs['approver_id'] = $inputs['send_to'];
            $inputs['deliverables'] = $inputs['deliverables'];
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            $inputs['fiscal_year_id'] = $this->fiscalYears->getCurrentFiscalYearId();
            $inputs['office_id'] = $authUser->employee->office_id;
            $inputs['department_id'] = $authUser->employee->department_id;
            $inputs['created_by'] = auth()->id();

            DB::beginTransaction();

            if ($inputs['btn'] === 'submit') {

                $inputs['request_date'] = now();
                $inputs['status_id'] = config('constant.SUBMITTED_STATUS');

                $workFromHome = $this->workFromHomes->create($inputs);
                $logInputs = [
                    'user_id' => $authUser->id,
                    'log_remarks' => 'Work From Home request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id' => $workFromHome->status_id,
                    'work_from_home_id' => $workFromHome->id,
                ];

                $workFromHome->approver->notify(new WorkFromHomeRequestSubmitted($workFromHome));
            } else {
                $inputs['status_id'] = config('constant.CREATED_STATUS');

                $workFromHome = $this->workFromHomes->create($inputs);

                $logInputs = [
                    'user_id' => $authUser->id,
                    'log_remarks' => 'Work From Home request is created.',
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id' => $workFromHome->status_id,
                    'work_from_home_id' => $workFromHome->id,
                ];
            }

            $this->workFromHomeLogs->create($logInputs);

            DB::commit();

            return redirect()->route('wfh.requests.index')->with('success_message', 'Work From Home request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error_message', 'Something went wrong! ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $wfhRequest = $this->workFromHomes
            ->with('logs')->find($id);



        return view('WorkFromHome::show', [
            'wfhRequest' => $wfhRequest,
        ]);
    }
}
