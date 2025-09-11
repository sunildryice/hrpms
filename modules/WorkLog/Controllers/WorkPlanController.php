<?php

namespace Modules\WorkLog\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\WorkLog\Repositories\WorkPlanRepository;
use Modules\WorkLog\Notifications\WorkPlanSubmitted;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\WorkLog\Requests\StoreRequest;
use Modules\WorkLog\Requests\SubmitRequest;
use Yajra\DataTables\DataTables;

class WorkPlanController extends Controller
{
    /**
     * Create a new controller instance.
     *
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
        $this->months = ['1' => 'January',
            '2' => 'Febuary',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];
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
        $authUser = auth()->user();
        if ($request->ajax()) {
            $superviseeIds = $authUser->employee->id ? $this->employees->getSupervisees($authUser->employee)->pluck('id')->toArray() : [];
            $data = $this->workPlan->with(['workPlanDailyLog', 'status', 'employee'])
                ->where('employee_id', $authUser->employee_id)
                ->orWhereIn('employee_id', $superviseeIds)
                ->orWhereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                    $q->orWhere('original_user_id', $authUser->id);
                })->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('year_month', function ($row) {
                    return $row->getYearMonth();
                })->addColumn('employee', function ($row) {
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
                    if ($authUser->can('update', $row)) {
                        $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('daily.work.logs.index', $row->id) . '" rel="tooltip" title="Daily Log"><i class="bi-list-columns-reverse"></i></a>';
                    } else {
                        $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('monthly.work.logs.show', $row->id) . '" rel="tooltip" title="View Work Plan"><i class="bi-eye"></i></a>';
                    }

                    if($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('monthly.work.log.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('monthly.work.logs.destroy', $row->id) . '" rel="tooltip" title="Delete">';
                        $btn .= '<i class="bi-trash3-fill"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('WorkLog::index');
    }

    /**
     * Show the form for creating a new monthly work log by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        return view('WorkLog::create')
            ->withMonths($this->months)
            ->withYears($this->years);

    }

    /**
     * Store a newly created monthly work log in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $userId = auth()->id();
        $inputs = $request->validated();
        $employee_id = auth()->user()->employee_id;
        $designation_id = $this->employees->find($employee_id)->designation_id;
        $inputs['employee_id'] = $employee_id;
        $inputs['designation_id'] = $designation_id ? $designation_id : 0;// will be controlled through permission
        $inputs['status_id'] = 1;
        $inputs['requester_id'] = $userId;
        $requester = $this->user->find($userId);
        $supervisor = collect();
        if ($requester->employee) {
            if ($requester->employee->latestTenure) {
                $supervisor = $this->user->select('id')
                    ->whereIn('employee_id', [$requester->employee->latestTenure->supervisor_id])
                    ->first();
            }
        }
        if($supervisor) {
            $inputs['reviewer_id'] = $supervisor['id'];
            $inputs['approver_id'] = $supervisor['id'];
        }

        $workPlan = $this->workPlan->updateOrCreate($inputs);

        if ($workPlan) {
            return response()->json(['status' => 'ok',
                'workPlan' => $workPlan,
                'message' => 'Monthly work log is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Monthly work log can not be added.'], 422);
    }

    /**
     * View the details the specified work plan.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        // dd($id);
        $workPlan = $this->workPlan->with(['workPlanDailyLog'])->find($id);
        return view('WorkLog::show')
            ->withAuthUser(auth()->user())
            ->withWorkPlan($workPlan);
    }

    /**
     * Show the form for editing a new travel report by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $workPlan = $this->workPlan
                    ->with(['workPlanDailyLog'])
                    ->find($id);
        $this->authorize('edit', $workPlan);
        return view('WorkLog::edit')
            ->withMonths($this->months)
            ->withWorkPlan($workPlan)
            ->withYears($this->years);
    }

    /**
     * Store a newly created monthly work log in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $workPlan = $this->workPlan->update($id, $inputs);
        if ($workPlan) {
            return response()->json(['status' => 'ok',
                'workPlan' => $workPlan,
                'message' => 'Monthly Work Log is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Monthly Work Log can not be updated.'], 422);
    }

    /**
     * Submit the specified monthly report in storage.
     *
     * @param SubmitRequest $request
     * @param $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function submit(SubmitRequest $request, $id)
    {
        $workPlan = $this->workPlan->with(['workPlanDailyLog'])->find($id);
        $inputs = $request->validated();

        if($inputs['btn'] == 'submit'){
            $this->authorize('submit', $workPlan);
            $inputs['reviewer_id'] = $inputs['approver_id'];
        }
        $requester = $this->user->find($workPlan->requester_id);
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $workPlan = $this->workPlan->submit($id, $inputs);
        if ($workPlan) {
            $message = 'Monthly Work Log successfully saved.';
            if($workPlan->status_id == 3){
                $message = 'Monthly Work Log successfully submitted.';
                $workPlan->approver->notify(new WorkPlanSubmitted($workPlan));
            }
            return redirect()->route('monthly.work.logs.index', $workPlan->id)
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Monthly Work Log can not be submitted.');
    }

    /**
     * Remove the specified monthly work log from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $workPlan = $this->workPlan
            ->with(['workPlanDailyLog'])
            ->find($id);
        $this->authorize('delete', $workPlan);
        $flag = $this->workPlan->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Monthly Work Log is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Monthly Work Log can not deleted.',
        ], 422);
    }
}
