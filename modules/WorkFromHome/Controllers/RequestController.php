<?php

namespace Modules\WorkFromHome\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\WorkFromHome\Requests\UpdateRequest;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Repositories\ProjectRepository;
use Modules\WorkFromHome\Enums\WorkFromHomeDays;
use Modules\WorkFromHome\Enums\WorkFromHomeTypes;
use Modules\WorkFromHome\Notifications\WorkFromHomeRequestSubmitted;
use Modules\WorkFromHome\Repositories\WorkFromHomeLogRepository;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;
use Modules\WorkFromHome\Requests\StoreRequest;

class RequestController extends Controller
{

    public function __construct(
        protected ProjectRepository $projects,
        protected UserRepository $users,
        protected WorkFromHomeRepository $workFromHomes,
        protected WorkFromHomeLogRepository $workFromHomeLogs,
        protected FiscalYearRepository $fiscalYears
    ) {}



    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = $this->workFromHomes->where('requester_id', '=', auth()->id())
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('request_date', function ($row) {
                    return  $row->getRequestDate();
                })
                ->addColumn('request_id', function ($row) {
                    return $row->getRequestId();
                })
                ->addColumn('type', function ($row) {
                    return \Modules\WorkFromHome\Enums\WorkFromHomeTypes::options()[$row->type] ?? ucfirst(str_replace('_', ' ', $row->type));
                })
                ->editColumn('start_date', function ($row) {
                    return $row->getStartDate();
                })
                ->editColumn('end_date', function ($row) {
                    return $row->getEndDate();
                })
                ->addColumn('total_days', function ($row) {
                    return $row->getTotalDays();
                })
                ->addColumn('project', function ($row) {
                    return $row->getProjectNames() ?? '-';
                })
                ->addColumn('status', function ($row) {

                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {

                    $authUser = auth()->user();
                    $btn = '<a href="' . route('wfh.requests.show', $row->id) . '" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye"></i> 
                    </a>';



                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('wfh.requests.edit', $row->id) . '"  data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="Edit Work From Home Request"><i class="bi-pencil-square"></i></a>';
                    }
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

        $projects = $this->projects
            ->getAssignedProjects($authUser);

        $supervisors = $this->users->getSupervisors($authUser)->pluck('full_name', 'id');

        $typeOptions = WorkFromHomeTypes::options();
        $WorkFromHomeDayOptions = WorkFromHomeDays::options();

 

        return view('WorkFromHome::create', [
            'projects' => $projects,
            'supervisors' => $supervisors,
            'typeOptions' => $typeOptions,
            'WorkFromHomeDayOptions' => $WorkFromHomeDayOptions,
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
            $inputs['office_id'] = $authUser->employee->office_id;
            $inputs['department_id'] = $authUser->employee->department_id;
            $inputs['created_by'] = auth()->id();
            $inputs['request_date'] = now();

            DB::beginTransaction();

            if ($inputs['btn'] === 'submit') {


                $inputs['status_id'] = config('constant.SUBMITTED_STATUS');

                $workFromHome = $this->workFromHomes->create($inputs);
             


                $logInputs = [
                    'user_id' => $authUser->id,
                    'log_remarks' => 'Work From Home request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id' => $workFromHome->status_id,
                    'work_from_home_id' => $workFromHome->id,
                ];

                $this->workFromHomeLogs->create($logInputs);


                $inputs['fiscal_year_id'] = $this->fiscalYears->getCurrentFiscalYearId();

                $fiscalYear = $this->fiscalYears->find($inputs['fiscal_year_id']);
                $workFromHome->fiscal_year_id =  $this->fiscalYears->getCurrentFiscalYearId();
                $workFromHome->work_from_home_number = $this->workFromHomes->getWorkFromHomeRequestNumber($fiscalYear);
                $workFromHome->save();

                $workFromHome->approver->notify(new WorkFromHomeRequestSubmitted($workFromHome));
            } else {
                $inputs['status_id'] = config('constant.CREATED_STATUS');

                $workFromHome = $this->workFromHomes->create($inputs);
            }

               $workFromHome->WorkFromHomeDays()->createMany($inputs['date_types'] ?? []);

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

        $deliverables = $wfhRequest->getDeliverablesWithProjectNames();

        $typeOptions = WorkFromHomeTypes::options();
        $typeLabel = $typeOptions[$wfhRequest->type] ?? ucfirst(str_replace('_', ' ', $wfhRequest->type));

        return view('WorkFromHome::show', [
            'wfhRequest' => $wfhRequest,
            'deliverables' => $deliverables,
            'typeLabel' => $typeLabel,
        ]);
    }

    public function edit($id)
    {
        $workFromHome = $this->workFromHomes->find($id);

        $authUser = auth()->user();
        $projects = $this->projects->getAssignedProjects($authUser);
        $supervisors = $this->users->getSupervisors($authUser)->pluck('full_name', 'id');
        $typeOptions = \Modules\WorkFromHome\Enums\WorkFromHomeTypes::options();
        $WorkFromHomeDayOptions = WorkFromHomeDays::options();

        return view('WorkFromHome::edit', compact(
            'workFromHome',
            'projects',
            'supervisors',
            'typeOptions',
            'WorkFromHomeDayOptions',
        ));
    }

    public function update(UpdateRequest $request, $id)
    {
        $authUser = auth()->user();
        $inputs   = $request->validated();

        try {
            DB::beginTransaction();

            $inputs['requester_id']     = auth()->id();
            $inputs['approver_id']      = $inputs['send_to'];
            $inputs['original_user_id'] = session()->get('original_user');
            $inputs['office_id']        = $authUser->employee->office_id;
            $inputs['department_id']    = $authUser->employee->department_id;
            $inputs['updated_by']       = auth()->id();

            if ($inputs['btn'] === 'submit') {
                $inputs['status_id']    = config('constant.SUBMITTED_STATUS');

                $workFromHome = $this->workFromHomes->update($id, $inputs);

                $logInputs = [
                    'user_id'          => $authUser->id,
                    'log_remarks'      => 'Work From Home request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id'        => $workFromHome->status_id,
                    'work_from_home_id' => $workFromHome->id,
                ];


                $this->workFromHomeLogs->create($logInputs);

                $inputs['fiscal_year_id'] = $this->fiscalYears->getCurrentFiscalYearId();

                $fiscalYear = $this->fiscalYears->find($inputs['fiscal_year_id']);
                $workFromHome->fiscal_year_id =  $this->fiscalYears->getCurrentFiscalYearId();
                $workFromHome->work_from_home_number = $this->workFromHomes->getWorkFromHomeRequestNumber($fiscalYear);

              

                $workFromHome->save();

                $workFromHome->approver->notify(
                    new WorkFromHomeRequestSubmitted($workFromHome)
                );
            } else {
                $inputs['status_id'] = config('constant.CREATED_STATUS');

                $workFromHome = $this->workFromHomes->update($id, $inputs);
            }
            
            $workFromHome->WorkFromHomeDays()->delete();
            $workFromHome->WorkFromHomeDays()->createMany($inputs['date_types'] ?? []);
              
                
            DB::commit();

            return redirect()
                ->route('wfh.requests.index')
                ->with('success_message', 'Work From Home request updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error_message', 'Something went wrong! ' . $e->getMessage());
        }
    }
}
