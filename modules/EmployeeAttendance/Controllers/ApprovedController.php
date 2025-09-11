<?php

namespace Modules\EmployeeAttendance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Yajra\DataTables\DataTables;

class ApprovedController extends Controller
{
    private $attendances;
    public function __construct(
        AttendanceRepository $attendances
    )
    {
        $this->attendances = $attendances;        
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->attendances->getApproved();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('employee_name', function ($row) {
                return $row->employee->getFullName();
            })->addColumn('year', function ($row) {
                return $row->year;
            })->addColumn('month', function ($row) {
                return date('F', mktime(0, 0, 0, $row->month, 10));
            })->addColumn('status', function ($row) {
                return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
            })->addColumn('action', function ($row) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('attendance.detail.view', $row->id).'" rel="tooltip" title="View Attendance"><i class="bi bi-eye"></i></a>';
                $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('attendance.detail.print', $row->id).'" target="_blank" rel="tooltip" title="Print Attendance"><i class="bi bi-printer"></i></a>';
                return $btn;
            })->rawColumns(['action', 'status'])
            ->make(true);
        }
        return view('EmployeeAttendance::Approved.index');
    }
}