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

        $projects = $this->projects->pluck('short_name', 'id');
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
            $inputs['office_id'] = $authUser->employee->office_id;
            $inputs['department_id'] = $authUser->employee->department_id;
            $inputs['created_by'] = auth()->id();
            $inputs['request_date'] = now();

            DB::beginTransaction();

            if ($inputs['btn'] === 'submit') {


                $inputs['status_id'] = config('constant.SUBMITTED_STATUS');

                $workFromHome = $this->workFromHomes->create($inputs);

                $pivotData = [];
                foreach ($inputs['project_ids'] as $projectId) {
                    $pivotData[$projectId] = [
                        'deliverables' => json_encode($inputs['deliverables'][$projectId] ?? []),
                    ];
                }
                $workFromHome->projects()->sync($pivotData);


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

                $pivotData = [];
                foreach ($inputs['project_ids'] as $projectId) {
                    $pivotData[$projectId] = [
                        'deliverables' => json_encode($inputs['deliverables'][$projectId] ?? []),
                    ];
                }
                $workFromHome->projects()->sync($pivotData);
            }

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

    public function edit($id)
    {
        $workFromHome = $this->workFromHomes->with('projects')->findOrFail($id);

        $selectedProjectIds = $workFromHome->projects->pluck('id')->toArray();

        $deliverables = [];
        $projects = $workFromHome->projects->pluck('short_name', 'id');
        $authUser = auth()->user();
        $supervisors = $this->users->getSupervisors($authUser)->pluck('full_name', 'id');

        foreach ($workFromHome->projects as $project) {
            $deliverables[$project->id] = json_decode($project->pivot->deliverables, true) ?? [];
        }
        return view('WorkFromHome::edit', compact(
            'workFromHome',
            'projects',
            'supervisors',
            'selectedProjectIds',
            'deliverables'
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
            $projectIds   = $inputs['project_ids'] ?? [];
            $deliverables = $inputs['deliverables'] ?? [];

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

            $pivotData = [];
            foreach ($projectIds as $projectId) {
                $pivotData[$projectId] = [
                    'deliverables' => json_encode($deliverables[$projectId] ?? []),
                ];
            }
            $workFromHome->projects()->sync($pivotData);

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
