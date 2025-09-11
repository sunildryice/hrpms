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

class AllWorkPlanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param WorkPlanRepository $workPlan,
     * @param RoleRepository $roles,
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
            $data = $this->workPlan->with(['status', 'employee'])
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('year_month', function ($row) {
                    return $row->getYearMonth();
                })->addColumn('employee', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('planned', function ($row) {
                    return Str::limit($row->planned);
                })->addColumn('completed', function ($row) {
                    return Str::limit($row->completed);
                })->addColumn('summary', function ($row) {
                    return Str::limit($row->summary);
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('all.monthly.work.logs.show', $row->id) . '" rel="tooltip" title="View Work Plan"><i class="bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                    $btn .= route('all.monthly.work.log.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('WorkLog::All.index');
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
        $workPlan = $this->workPlan->with(['workPlanDailyLog'])->find($id);
        return view('WorkLog::All.show')
            ->withAuthUser(auth()->user())
            ->withWorkPlan($workPlan);
    }

    public function print($id)
    {
        $authUser = auth()->user();
        $workPlan = $this->workPlan
            ->with(['workPlanDailyLog', 'logs', 'requester', 'approver'])
            ->find($id);
        $start_date = date('Y-m-01', strtotime($workPlan->getYearMonth()));
        $end_date = date('Y-m-t', strtotime($workPlan->getYearMonth()));
        $date_array = $this->getBetweenDates($start_date, $end_date);
        $data = array();
        $i = 0;
        foreach ($date_array as $date) {
            $logdata[$i] = ['day' => date('l', strtotime($date)),
                'date' => $date,
                'major_activities' => '',
                'activity_area' => '',
                'priority' => '',
                'status' => '',
                'other_activities' => '',
                'remarks' => '',
            ];
            foreach ($workPlan->workPlanDailyLog as $plan) {
                if ($date == $plan->log_date) {
                    $logdata[$i] = ['day' => date('l', strtotime($date)),
                        'date' => $plan->log_date,
                        'major_activities' => $plan->major_activities,
                        'activity_area' => $plan->getActivityArea(),
                        'priority' => $plan->getPriority(),
                        'status' => $plan->status,
                        'other_activities' => $plan->other_activities,
                        'remarks' => $plan->remarks,
                    ];
                }
            }
            $i++;
        }
        $requester = $this->employees->select('*')->where('id', $workPlan->requester->employee_id)->first();
        $approver = $this->employees->select('*')->where('id', $workPlan->approver->employee_id)->first();
        $date = array();
        foreach ($workPlan->logs as $log) {
            if ($log->status_id == 3) {
                $date['submitted_date'] = $log->created_at;
            }
            if ($log->status_id == 6) {
                $date['approved_date'] = $log->created_at;
            }

        }
        return view('WorkLog::print')
            ->withApprover($approver)
            ->withDates($date)
            ->withLogData($logdata)
            ->withRequester($requester)
            ->withWorkPlan($workPlan);
    }

    public function getBetweenDates($startDate, $endDate)
    {
        $rangArray = [];

        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        for ($currentDate = $startDate; $currentDate <= $endDate; $currentDate += (86400)) {
            $date = date('Y-m-d', $currentDate);
            $rangArray[] = $date;
        }
        return $rangArray;
    }
}
