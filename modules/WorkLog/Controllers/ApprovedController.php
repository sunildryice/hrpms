<?php

namespace Modules\WorkLog\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\WorkLog\Repositories\WorkPlanRepository;

class ApprovedController extends Controller
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
        RoleRepository $roles,
        UserRepository $user
    ) {
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
     * Display a listing of the payment sheets
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->workPlan
                ->select(['*'])
                ->where('status_id', '6')
            // ->whereIn('office_id', auth()->user()->getAccessibleOfficesIds())
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()->addColumn('year_month', function ($row) {
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
            })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.monthly.work.log.show', $row->id) . '" rel="tooltip" title="View Work Plan">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                    $btn .= route('monthly.work.log.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('WorkLog::Approved.index');
    }

    /**
     * Show the work log detail.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $userId = auth()->id();
        $workPlan = $this->workPlan
            ->with(['workPlanDailyLog'])
            ->find($id);
        return view('WorkLog::Approved.view')
            ->withAuthUser(auth()->user())
            ->withWorkPlan($workPlan);

    }

    /**
     * Show the specified training request in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $authUser = auth()->user();
        $workPlan = $this->workPlan
            ->with(['workPlanDailyLog', 'logs', 'requester', 'approver'])
            ->find($id);
        $this->authorize('print', $workPlan);
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
