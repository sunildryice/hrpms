<?php

namespace Modules\WorkLog\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\WorkLog\Repositories\WorkPlanRepository;
use Modules\WorkLog\Notifications\WorkPlanApproved;
use Modules\WorkLog\Notifications\WorkPlanReturned;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\WorkLog\Requests\WorkPlanReview\StoreRequest;

use DataTables;
use DB;

class ApproveController extends Controller
{
    /**
     * Create a new controller instance.
     * @param EmployeeRepository $employees ,
     * @param WorkPlanRepository $workPlan ,
     * @param RoleRepository $roles ,
     * @param UserRepository $user
     *
     */
    public function __construct(
        EmployeeRepository $employees,
        WorkPlanRepository $workPlan,
        RoleRepository     $roles,
        UserRepository     $user
    )
    {
        $this->employees = $employees;
        $this->workPlan = $workPlan;
        $this->roles = $roles;
        $this->user = $user;
        $this->years = range(date('Y'), 2020);
        $this->destinationPath = 'worklog';

    }

    /**
     * Display a listing of the Monthly Work Log by employee id.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->workPlan->with(['workPlanDailyLog'])
                ->where(function ($q) use ($userId) {
                    $q->where('approver_id', $userId);
                    $q->where('status_id', 3);
                })
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->orderBy('id', 'asc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('year_month', function ($row) {
                    return $row->getYearMonth();
                })->addIndexColumn()->addColumn('employee', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('planned', function ($row) {
                    return Str::limit($row->planned, 100);
                })->addColumn('completed', function ($row) {
                    return Str::limit($row->completed, 100);
                })->addColumn('summary', function ($row) {
                    return Str::limit($row->summary, 100);
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('approve', $row)) {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-success" href="';
                        $btn .= route('approve.work.logs.create', $row->id) . '" rel="tooltip" title="Approve"><i class="bi bi-box-arrow-in-up-right"></i></a>';
                    } else {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-success" href="';
                        $btn .= route('approve.work.logs.show', $row->id) . '" rel="tooltip" title="View"><i class="bi bi-eye"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('WorkLog::Approve.index');
    }

    /**
     * Show the form for creating a new travel report by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $userId = auth()->id();
        $employee_id = auth()->user()->employee_id;
        $workPlan = $this->workPlan
            ->with(['workPlanDailyLog', 'status', 'logs'])
            ->find($id);
        $this->authorize('approve', $workPlan);
        return view('WorkLog::Approve.create')
            ->withAuthUser(auth()->user())
            ->withWorkPlan($workPlan);

    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $workPlan = $this->workPlan->find($id);
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $workPlan = $this->workPlan->approve($workPlan->id, $inputs);

        if ($workPlan) {
            $message = '';

            if ($workPlan->status_id == 2) {
                $message = 'Worklog is successfully returned.';
                $workPlan->requester->notify(new WorkPlanReturned($workPlan));

            } elseif ($workPlan->status_id == 6) {
                $message = 'Worklog is successfully approved.';
                $workPlan->requester->notify(new WorkPlanApproved($workPlan));
            } else {
                $message = 'Worklog is successfully rejected.';
            }

            return redirect()->route('approve.work.logs.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Work log can not be reviewed/approved.');
    }
}
