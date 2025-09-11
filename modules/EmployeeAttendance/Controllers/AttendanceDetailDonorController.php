<?php

namespace Modules\EmployeeAttendance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\EmployeeAttendance\Models\Attendance;
use Modules\EmployeeAttendance\Models\AttendanceDetailDonor;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailDonorRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Requests\AttendanceDonor\StoreRequest;
use Modules\Master\Repositories\ActivityAreaRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\PriorityRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\WorkLog\Repositories\WorkPlanDailyLogRepository;
use Modules\WorkLog\Repositories\WorkPlanRepository;
use Yajra\DataTables\Facades\DataTables;

class AttendanceDetailDonorController extends Controller
{
    public function __construct(
        protected AttendanceDetailRepository $attendanceDetail,
        protected Attendance $attendance,
        protected OfficeRepository $office,
        protected DonorCodeRepository $donor,
        protected EmployeeRepository $employees,
        protected AttendanceDetailDonorRepository $attendanceDetailDonor,
        protected WorkPlanRepository $workPlan,
        protected WorkPlanDailyLogRepository $workPlanDailyLog,
        protected ActivityAreaRepository $activityArea,
        protected PriorityRepository $priority,
        protected LeaveRepository $leaves,
        protected UserRepository $user,
        protected FiscalYearRepository $fiscalYears,
        protected ProjectCodeRepository $projects
    ) {}

    public function index(Request $request, $attendnaceId)
    {
        $attendance = $this->attendance->find($attendnaceId);

        if ($request->ajax()) {
            $data = AttendanceDetailDonor::with(['attendanceDetail.attendance', 'donor', 'project'])
                ->whereHas('attendanceDetail', function ($query) use ($attendance) {
                    $query->where('attendance_master_id', $attendance->id);
                })
                ->where(function ($q) {
                    $q->where('worked_hours', '>', 0)
                        ->orWhere(function ($q) {
                            $q->whereHas('donor', function ($q) {
                                $q->where('title', '=', config('constant.UNRESTRICTED_DONOR'));
                            });
                            $q->whereNotNull('activities');
                            $q->whereHas('attendanceDetail', function ($q) {
                                $q->where('unrestricted_hours', '>', 0);
                            });
                        });
                })
                ->when($request->get('donor'), function ($q) use ($request) {
                    if (is_array($request->get('donor'))) {
                        return $q->whereIn('donor_id', $request->get('donor'));
                    }

                    return $q->where('donor_id', $request->get('donor'));
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('attendance_date', function ($row) {
                    return $row->attendanceDetail->attendance_date->format('Y-m-d');
                })
                ->addColumn('day', function ($row) {
                    return $row->attendanceDetail->attendance_date->format('l');
                })->addColumn('donor', function ($row) {
                    return $row->donor->description;
                })
                ->addColumn('project', function ($row) {
                    return $row->project->getShortName();
                })
                ->addColumn('worked_hours', function ($row) {
                    return $row->getWorkedHours();
                })
                ->make(true);
        }

        $donors = $this->attendanceDetailDonor->select('*')
            ->select(['donor_id', 'attendance_detail_id'])
            ->with('donor')
            ->whereHas('attendanceDetail', function ($query) use ($attendance) {
                $query->where('attendance_master_id', $attendance->id);
            })->get()->pluck('donor');

        $enabledDonors = $donors->unique();

        return view('EmployeeAttendance::AttendanceDonor.index')
            ->with([
                // 'supervisors' => ($supervisors),
                'attendance' => $attendance,
                'enabledDonors' => $enabledDonors,
            ]);
    }

    public function create(Request $request, $attendanceId, $donor)
    {
        $attendance = $this->attendance->find($attendanceId);
        $attendanceDate = $request->get('attendance_date');
        $donor = $this->donor->find($donor);
        $isUnrestricted = $donor->title == config('constant.UNRESTRICTED_DONOR') ? true : false;
        $attendanceDetail = $this->attendanceDetail->getDetail($attendance->id, $attendanceDate);
        $attendanceDetailDonor = $this->attendanceDetailDonor->getDonorDetail($attendanceDetail?->id, $donor->id);
        $projects = $this->projects->getActiveProjectCodes();

        return view('EmployeeAttendance::AttendanceDonor.create')
            ->with([
                'authUser' => (auth()->user()),
                'donor' => $donor,
                'activityAreas' => ($this->activityArea->get()),
                'attendance' => $attendance,
                'isUnrestricted' => $isUnrestricted,
                'priorities' => ($this->priority->get()),
                'attendanceDate' => ($attendanceDate),
                'projects' => ($projects),
                'attendanceDetail' => ($attendanceDetail),
                'attendanceDetailDonor' => ($attendanceDetailDonor),
            ]);

    }

    public function store(StoreRequest $request, $attendanceId, $donor)
    {
        abort(404);
        try {
            $user = auth()->user();
            $inputs = $request->all();
            $attendance = $this->attendance->find($attendanceId);
            $donor = $this->donor->find($donor);

            $workPlanMonthlyLogs = $this->workPlan->getWorkPlan($attendance->year, $attendance->month, $attendance->employee_id);
            if (! $workPlanMonthlyLogs) {
                $employee_id = $user->employee_id;
                $designation_id = $this->employees->find($employee_id)->designation_id;
                $workPlanInputs['employee_id'] = $employee_id;
                $workPlanInputs['designation_id'] = $designation_id ? $designation_id : 0; // will be controlled through permission
                $workPlanInputs['status_id'] = config('constant.CREATED_STATUS');
                $workPlanInputs['year'] = $attendance->year;
                $workPlanInputs['month'] = $attendance->month;
                $workPlanInputs['requester_id'] = $user->id;

                $workPlanMonthlyLogs = $this->workPlan->create($workPlanInputs);
            }
            if (! $workPlanMonthlyLogs) {
                throw new \Exception('Failed to create work plan master');
            }

            $worklogFlag = false;
            if ($workPlanMonthlyLogs?->status_id == config('constant.CREATED_STATUS')) {
                $workPlanDailyLog = $this->workPlanDailyLog->getDailyLog($workPlanMonthlyLogs->id, $inputs['log_date'], $donor->id);
                if ($workPlanDailyLog) {
                    $worklogFlag = $this->workPlanDailyLog->update($workPlanDailyLog->id, $inputs);
                } else {
                    $inputs['donor_id'] = $donor->id;
                    $inputs['work_plan_id'] = $workPlanMonthlyLogs->id;
                    $worklogFlag = $this->workPlanDailyLog->create($inputs);
                }
                if (! $worklogFlag) {
                    throw new \Exception('Failed to create work plan log');
                }
            } else {
                throw new \Exception('Work Plan is already approved. You cannot add/edit daily log.');
            }

            // update attendance detail donor
            $chargedHours = $inputs['charged_hours'];
            $whole = (int) ($inputs['charged_hours']);
            $fraction = $chargedHours - $whole;
            if ($fraction > 0.59) {
                $fraction = $fraction + 0.4;
            }
            $request->merge([
                'chargedHours' => (float) round($whole + $fraction, 2),
                'attendanceId' => $attendance->id,
                'donorId' => $donor->id,
            ]);

            return app(AttendanceDetailController::class)->store($request);
        } catch (\Exception $th) {
            // throw $th;
            return response()->json(['failure' => $th->getMessage()], 400);
        }
    }

    public function print(Request $request, $attendanceId)
    {
        $authUser = auth()->user();
        $donor = $request->donor;
        $donor = $this->donor->select('*')->find($donor);
        $attendance = $this->attendance
            ->find($attendanceId);
        // $this->authorize('print', $workPlan);
        $logs = AttendanceDetailDonor::with(['attendanceDetail.attendance', 'donor', 'project'])
            ->whereHas('attendanceDetail', function ($query) use ($attendance) {
                $query->where('attendance_master_id', $attendance->id);
            })
            ->where(function ($q) {
                $q->where('worked_hours', '>', 0)
                    ->orWhere(function ($q) {
                        $q->whereHas('donor', function ($q) {
                            $q->where('title', '=', config('constant.UNRESTRICTED_DONOR'));
                        });
                        $q->whereNotNull('activities');
                        $q->whereHas('attendanceDetail', function ($q) {
                            $q->where('unrestricted_hours', '>', 0);
                        });
                    });
            })
            ->when($donor, function ($q) use ($donor) {
                return $q->where('donor_id', $donor->id);
            })->get();
        // dd($logs->toArray());

        $requester = $this->employees->select('*')->where('id', $attendance->requester->employee_id)->first();
        $approver = $this->employees->select('*')->where('id', $attendance->approver->employee_id)->first();
        $date = [];
        foreach ($attendance->logs as $log) {
            if ($log->status_id == 3) {
                $date['submitted_date'] = $log->created_at;
            }
            if ($log->status_id == 6) {
                $date['approved_date'] = $log->created_at;
            }

        }

        return view('EmployeeAttendance::AttendanceDonor.print')
            ->with([
                'approver' => ($approver),
                'dates' => ($date),
                'logData' => ($logs),
                'requester' => ($requester),
                'attendance' => ($attendance),
            ]);
    }
}
