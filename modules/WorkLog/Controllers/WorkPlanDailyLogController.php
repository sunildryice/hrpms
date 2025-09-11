<?php

namespace Modules\WorkLog\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Models\DonorCode;
use Modules\Master\Repositories\ActivityAreaRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\PriorityRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\WorkLog\Repositories\WorkPlanDailyLogRepository;
use Modules\WorkLog\Repositories\WorkPlanRepository;
use Modules\WorkLog\Requests\WorkPlanDailyLog\StoreRequest;
use Modules\WorkLog\Requests\WorkPlanDailyLog\UpdateRequest;

class WorkPlanDailyLogController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected ActivityAreaRepository $activityArea,
        protected PriorityRepository $priority,
        protected WorkPlanDailyLogRepository $workPlanDailyLog,
        protected WorkPlanRepository $workPlan,
        protected DonorCodeRepository $donors,
        protected RoleRepository $roles,
        protected UserRepository $user
    ) {
        $this->destinationPath = 'worklog';
    }

    /**
     * Display a listing of the meeting hall booking request by employee id.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $worklog)
    {
        $authUser = auth()->user();
        $workPlan = $this->workPlan->find($worklog);

        if ($request->ajax()) {
            $data = $this->workPlanDailyLog->with(['workPlan'])
                ->where('work_plan_id', '=', $worklog)
                ->orderBy('log_date', 'asc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()->addColumn('log_date', function ($row) {
                    return $row->log_date;
                })->addIndexColumn()->addColumn('major_activities', function ($row) {
                    return $row->major_activities;
                })->addColumn('activity_area', function ($row) {
                    return $row->getActivityArea();
                })->addColumn('priority', function ($row) {
                    return $row->getPriority();
                })
                ->addColumn('donor', function ($row) {
                    return '';
                })
                ->addColumn('status', function ($row) {
                    return $row->status;
                })->addColumn('other_activities', function ($row) {
                    return $row->other_activities;
                })->addColumn('remarks', function ($row) {
                    return $row->remarks;
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('update', $row)) {
                        $btn .= '<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('daily.work.logs.edit', $row->id).'" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('daily.work.logs.destroy', $row->id).'" rel="tooltip" title="Delete">';
                        $btn .= '<i class="bi-trash3-fill"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        $supervisors = $this->user->getSupervisors($authUser);

        return view('WorkLog::WorkPlanDailyLog.index')
            ->with([
                'authUser' => ($authUser),
                'supervisors' => ($supervisors),
                'workPlan' => ($workPlan),
                'workLog' => ($worklog),
            ]);
    }

    /**
     * Show the form for creating a new travel report by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($worklog)
    {
        $workPlanMonthlyLogs = $this->workPlan->find($worklog);
        $this->authorize('addEditDailyLog', $workPlanMonthlyLogs);
        $start_date = date('Y-m-01', strtotime($workPlanMonthlyLogs->getYearMonth()));
        $end_date = date('Y-m-t', strtotime($workPlanMonthlyLogs->getYearMonth()));
        $workPlanDailyLog = $this->workPlanDailyLog
            ->select('*')
            ->where('work_plan_id', $worklog)
            ->orderBy('log_date', 'desc')
            ->first();
        if ($workPlanDailyLog) {
            $logDate = date('Y-m-d', strtotime($workPlanDailyLog->log_date.'+1 day'));
            $log_date = $logDate > $end_date ? $end_date : $logDate;
        } else {
            $log_date = $start_date;
        }

        return view('WorkLog::WorkPlanDailyLog.create')
            ->with([
                'authUser' => (auth()->user()),
                'activityAreas' => ($this->activityArea->get()),
                'priorities' => ($this->priority->get()),
                // 'donors' => ($this->donors->getActiveDonorCodes()),
                'logDate' => ($log_date),
                'startDate' => ($start_date),
                'endDate' => ($end_date),
                'workPlanDailyLog' => ($workPlanDailyLog),
                'worklog' => ($worklog),
            ]);

    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param  \Modules\Employee\Requests\StoreRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $worklog)
    {
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['work_plan_id'] = $worklog;
        // dd($inputs);
        $workPlanDailyLog = $this->workPlanDailyLog->create($inputs);

        if ($workPlanDailyLog) {
            if ($inputs['btn'] == 'saveandnext') {
                return redirect()->route('daily.work.logs.create', $worklog)
                    ->withSuccessMessage('Daily Work Log successfully added.');
            } else {
                return redirect()->route('daily.work.logs.index', $worklog)
                    ->withSuccessMessage('Daily Work Log successfully added.');
            }

        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Daliy Work Log can not be added.');
    }

    /**
     * Show the form for editing the specified travel request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $workPlanDailyLog = $this->workPlanDailyLog
            ->with(['workPlan'])
            ->find($id);
        $this->authorize('update', $workPlanDailyLog);
        $start_date = date('Y-m-01', strtotime($workPlanDailyLog->workPlan->getYearMonth()));
        $end_date = date('Y-m-t', strtotime($workPlanDailyLog->workPlan->getYearMonth()));

        return view('WorkLog::WorkPlanDailyLog.edit')
            ->with([
                'authUser' => (auth()->user()),
                'donors' => ($this->donors->getActiveDonorCodes()),
                'activityAreas' => ($this->activityArea->get()),
                'priorities' => ($this->priority->get()),
                'startDate' => ($start_date),
                'endDate' => ($end_date),
                'workPlanDailyLog' => ($workPlanDailyLog),
            ]);
    }

    /**
     * Update the specified employee in storage.
     *
     * @param  \Modules\Employee\Requests\UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $workPlanDailyLog = $this->workPlanDailyLog
            ->with(['workPlan'])
            ->find($id);
        $this->authorize('update', $workPlanDailyLog);
        $inputs = $request->validated();
        $workPlanDailyLog = $this->workPlanDailyLog->update($id, $inputs);
        if ($workPlanDailyLog) {
            return redirect()->route('daily.work.logs.index', $workPlanDailyLog->work_plan_id)
                ->withSuccessMessage('Daily Work Log successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Daily Work Log can not be updated.');
    }

    /**
     * Remove the specified travel request from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $workPlanDailyLog = $this->workPlanDailyLog
            ->with(['workPlan'])
            ->find($id);
        $this->authorize('delete', $workPlanDailyLog);
        $flag = $this->workPlanDailyLog->destroy($id);
        if ($flag) {
            $workPlan = $this->workPlan->find($workPlanDailyLog->work_plan_id);

            return response()->json([
                'type' => 'success',
                'workPlanDailyLogCount' => $workPlan->workPlanDailyLog()->count(),
                'message' => 'Daily Work Log is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Daily Work Log can not deleted.',
        ], 422);
    }
}
