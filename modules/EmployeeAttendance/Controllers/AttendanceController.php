<?php

namespace Modules\EmployeeAttendance\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employee\Models\Employee;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeAttendance\Imports\AttendanceImport;
use Modules\EmployeeAttendance\Requests\Attendance\StoreRequest;
use Modules\EmployeeAttendance\Notifications\AttendanceSubmitted;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\EmployeeAttendance\Requests\Attendance\SubmitRequest;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailDonorRepository;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceRepository $attendance,
        protected AttendanceDetailRepository $attendanceDetail,
        protected AttendanceDetailDonorRepository $attendanceDonor,
        protected EmployeeRepository $employee,
        protected UserRepository $user
    ) {
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->employee->activeEmployees();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_code', function ($row) {
                    return $row->employee_code;
                })
                ->addColumn('full_name', function ($row) {
                    return $row->getFullName();
                })
                ->addColumn('designation', function ($row) {
                    return $row->latestTenure->getDesignationName();
                })
                ->addColumn('department', function ($row) {
                    return $row->latestTenure->getDepartmentName();
                })
                ->addColumn('supervisor', function ($row) {
                    return $row->latestTenure->getSupervisorName();
                })
                ->addColumn('duty_station', function ($row) {
                    return $row->latestTenure->getDutyStation();
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('attendance.view', $row->id) . '" rel="tooltip" title="View Attendance"><i class="bi bi-eye"></i></a>';

                    return $btn;
                })
                ->make(true);
        }

        return view('EmployeeAttendance::Attendance.index');
    }

    public function create(Request $request)
    {
        return view('EmployeeAttendance::Attendance.create');
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();

        // Checking for duplicate attendance entry.
        if (isset($request->year) && isset($request->month)) {
            $attendance_exists = $this->attendance->where('year', '=', $request->year)
                ->where('month', '=', $request->month)
                ->where('requester_id', '=', auth()->id())
                ->first();
            if ($attendance_exists != null) {
                return redirect()->back()->withInput()->withWarningMessage('Attendance already exists.');
            }
        }

        // Creating a new attendence.
        $inputs['employee_id'] = auth()->user()->employee->id;
        $inputs['department_id'] = auth()->user()->employee->latestTenure->department_id;
        $inputs['designation_id'] = auth()->user()->employee->latestTenure->designation_id;
        $inputs['office_id'] = auth()->user()->employee->latestTenure->office_id;
        $inputs['duty_station_id'] = auth()->user()->employee->latestTenure->duty_station_id;
        $inputs['year'] = $request->year;
        $inputs['month'] = $request->month;
        $inputs['requester_id'] = auth()->user()->id;
        $inputs['updated_by'] = auth()->user()->id;
        $attendance = $this->attendance->create($inputs);

        if ($attendance) {
            return redirect()->back()->withSuccessMessage('Attendence created successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Attendance could not be created.');
        }
    }

    public function show(Request $request, $employeeId)
    {
        if ($request->ajax()) {
            $data = $this->attendance->where('employee_id', '=', $employeeId)->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('employee_name', function ($row) {
                //     return $row->employee->getFullName();
                // })
                ->addColumn('year', function ($row) {
                    return $row->year;
                })
                ->addColumn('month', function ($row) {
                    return date('F', mktime(0, 0, 0, $row->month, 10));
                })
                ->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('attendance.detail.show', $row->id) . '" rel="tooltip" title="View Attendance Detail"><i class="bi bi-eye"></i></a>';

                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('attendance.detail.print', $row->id) . '" target="_blank" rel="tooltip" title="Print Attendance Detail"><i class="bi bi-printer"></i></a>';

                    if (auth()->user()->can('submit', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('attendance.detail.edit', $row->id) . '" rel="tooltip" title="Edit Attendance Detail"><i class="bi bi-pencil-square"></i></a>';
                    }
                    if (auth()->user()->can('amend', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-sm btn-outline-danger amend-attendance"';
                        $btn .= 'data-href = "' . route('attendance.amend', $row->id) . '" data-month="' . $row->getMonth() . '" data-year="' . $row->getYear() . '"  title="Reverse Attendance">';
                        $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    }

                    $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary"';
                    $btn .= 'href = "' . route('attendance.detail.worklogs', $row->id) . '" data-month="' . $row->getMonth() . '" data-year="' . $row->getYear() . '"  title="View Worklogs">';
                    $btn .= '<i class="bi bi-file-ruled" ></i></a>';

                    $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary"';
                    $btn .= 'href = "' . route('attendance.detail.worklogs.print', $row->id) . '" data-month="' . $row->getMonth() . '" data-year="' . $row->getYear() . '"  title="Print all Worklogs">';
                    $btn .= '<i class="bi bi-printer-fill" ></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('EmployeeAttendance::Attendance.show', compact('employeeId'));
    }

    public function view(Request $request, $employeeId)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->attendance->where('employee_id', '=', $employeeId)->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('employee_name', function ($row) {
                //     return $row->employee->getFullName();
                // })
                ->addColumn('year', function ($row) {
                    return $row->year;
                })
                ->addColumn('month', function ($row) {
                    return date('F', mktime(0, 0, 0, $row->month, 10));
                })
                ->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('attendance.detail.view', $row->id) . '" rel="tooltip" title="View Attendance Detail"><i class="bi bi-eye"></i></a>';

                    if ($row->status_id == config('constant.APPROVED_STATUS')) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('attendance.detail.print', $row->id) . '" target="_blank" rel="tooltip" title="Print Attendance Detail"><i class="bi bi-printer"></i></a>';

                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary"';
                        $btn .= 'href = "' . route('attendance.detail.worklogs', $row->id) . '" data-month="' . $row->getMonth() . '" data-year="' . $row->getYear() . '"  title="View Worklogs">';
                        $btn .= '<i class="bi bi-file-ruled" ></i></a>';

                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary"';
                        $btn .= 'href = "' . route('attendance.detail.worklogs.print', $row->id) . '" data-month="' . $row->getMonth() . '" data-year="' . $row->getYear() . '"  title="Print all Worklogs">';
                        $btn .= '<i class="bi bi-printer" ></i></a>';
                    }

                    // if ($authUser->can('delete', $row)) {
                    //     $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    //     $btn .= 'data-href="' . route('attendance.delete', $row->id) . '">';
                    //     $btn .= '<i class="bi-trash"></i></a>';
                    // }
    
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $employeeName = $this->employee->find($employeeId)->getFullName();

        return view('EmployeeAttendance::Attendance.view', compact('employeeId', 'employeeName'));
    }

    public function submit(SubmitRequest $request)
    {
        $id = $request->attendance_id;
        $attendance = $this->attendance->find($id);

        $this->authorize('submit', $attendance);

        // TODO: check if user added worklogs on all unrestricted fileds
        // $workingDaysCount = $this->attendanceDetail->getAttendanceDetail($id)->filter(fn ($item) => ! $item['holiday'])->count();
        // $unrestrictedWorklogCount = $this->attendanceDonor->select('*')
        //     ->whereHas('attendanceDetail', function ($query) use ($id) {
        //         $query->where('attendance_master_id', $id);
        //     })
        //     ->whereHas('donor', function ($query) {
        //         $query->where('title', config('constant.UNRESTRICTED_DONOR'));
        //     })
        //     ->whereNotNull('activities')
        //     ->count();

        // if (!($unrestrictedWorklogCount >= $workingDaysCount)) {
        //     return redirect()->back()->withWarningMessage('Please add worklogs on all unrestricted days.');
        // }
        // dd('test');

        $inputs = $request->validated();
        $inputs['status_id'] = config('constant.SUBMITTED_STATUS');

        $attendance = $this->attendance->update($id, $inputs);

        $log = [];
        $log['user_id'] = auth()->id();
        $log['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $log['log_remarks'] = 'Attendance submitted. ' . $inputs['remarks'];
        $log['status_id'] = config('constant.SUBMITTED_STATUS');
        $attendance->logs()->create($log);

        if ($attendance) {
            $attendance->reviewer->notify(new AttendanceSubmitted($attendance));

            return redirect()->route('attendance.show', $attendance->employee_id)
                ->withSuccessMessage('Attendance successfully submitted.');
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Attendance could not be submitted.');
    }

    public function import(Request $request)
    {
        if (!$request->hasFile('attendance_file')) {
            return redirect()->back()->withWarningMessage('Please upload the file.');
        }

        $request->validate([
            'attendance_file' => 'required|max:5120|mimes:xlsx',
        ]);

        $this->authorize('import-attendance');

        try {
            $spreadsheet = IOFactory::load(request()->file('attendance_file'));
            $totalSheet = count($spreadsheet->getAllSheets());

            Excel::import(new AttendanceImport($totalSheet), request()->file('attendance_file'), null, \Maatwebsite\Excel\Excel::XLSX);

            return redirect()->back()->withSuccessMessage('Attendance imported successfully.');
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->back()->withWarningMessage('Please upload attendance sheet as per prescribed format.');
        }
    }

    public function destroy($attendanceId)
    {
        $deleted = $this->attendance->destroy($attendanceId);
        if ($deleted) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Attendance deleted successfully.',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance cannot be deleted.',
            ], 422);
        }
    }

    public function amend(Request $request, $attendanceId)
    {
        $attendance = $this->attendance->find($attendanceId);
        $this->authorize('amend', $this->attendance->find($attendanceId));
        $inputs = $request->validate([
            'log_remarks' => 'required|string',
        ]);
        $inputs['status_id'] = config('constant.RETURNED_STATUS');
        $inputs['user_id'] = $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $flag = $this->attendance->amend($attendance->id, $inputs);

        if ($flag) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Attendance reversed successfully.',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Attendance cannot be reversed.',
        ], 422);
    }

    public function checkInToday(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        $now = now();
        $employeeId = auth()->user()->employee->id;

        $attendance = $this->attendance->getAttendanceObject($employeeId, $now->year, $now->month);
        if (!$attendance) {
            $inputs = [
                'employee_id' => $employeeId,
                'department_id' => auth()->user()->employee->latestTenure->department_id,
                'designation_id' => auth()->user()->employee->latestTenure->designation_id,
                'office_id' => auth()->user()->employee->latestTenure->office_id,
                'duty_station_id' => auth()->user()->employee->latestTenure->duty_station_id,
                'year' => $now->year,
                'month' => $now->month,
                'requester_id' => auth()->id(),
                'updated_by' => auth()->id(),
                'status_id' => config('constant.CREATED_STATUS') ?? 1,
                'donor_codes' => '', 
            ];
            $attendance = $this->attendance->create($inputs);
            if (!$attendance) {
                return response()->json(['message' => 'Failed to create monthly attendance record.'], 500);
            }
        }

        $detail = $this->attendanceDetail->getDetail($attendance->id, $date);
        if ($detail && $detail->checkin) {
            return response()->json(['message' => 'Already checked in today.'], 400);
        }
        if (!$detail) {
            $this->attendanceDetail->create([
                'attendance_master_id' => $attendance->id,
                'attendance_date' => $date,
                'checkin' => $now,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'worked_hours' => 0,
                'unrestricted_hours' => 0,
                'charged_hours' => 0,
            ]);
        } else {
            $this->attendanceDetail->update($detail->id, [
                'checkin' => $now,
                'updated_by' => auth()->id(),
            ]);
        }

        return response()->json([
            'time' => $now->format('h:i A'),
            'message' => 'Checked in successfully.'
        ]);
    }

    public function checkOutToday(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        $now = now();
        $employeeId = auth()->user()->employee->id;

        $attendance = $this->attendance->getAttendanceObject($employeeId, $now->year, $now->month);

        if (!$attendance) {
            return response()->json(['message' => 'Attendance record not found.'], 400);
        }

        $detail = $this->attendanceDetail->getDetail($attendance->id, $date);

        if (!$detail || !$detail->checkin) {
            return response()->json(['message' => 'You must check in first.'], 400);
        }

        if ($detail->checkout) {
            return response()->json(['message' => 'Already checked out today.'], 400);
        }

        $this->attendanceDetail->update($detail->id, [
            'checkout' => $now,
            'updated_by' => auth()->id(),
        ]);

        $checkIn = Carbon::parse($detail->checkin);
        $checkOut = Carbon::parse($now);

        $checkIn->startOfMinute();
        $checkOut->startOfMinute();

        // $workedHours = round($checkIn->floatDiffInHours($checkOut), 2);
        $workedHours = $checkIn->diff($checkOut)->format('%H.%I');

        $this->attendanceDetail->update($detail->id, [
            'worked_hours' => $workedHours,
            'unrestricted_hours' => $workedHours,
            'charged_hours' => 0,
        ]);

        return response()->json([
            'time' => $now->format('h:i A'),
            'worked_hours' => $workedHours,
            'message' => 'Checked out successfully.'
        ]);
    }
}
