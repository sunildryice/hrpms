<?php

namespace Modules\LeaveRequest\Controllers;

use App\Http\Controllers\Controller;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ApprovedLeaveRequestController extends Controller
{
    private $leaveRequests;
    public function __construct(
        LeaveRequestRepository $leaveRequests
    )
    {
        $this->leaveRequests = $leaveRequests;        
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->leaveRequests->getApproved();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('leave_type', function ($row) {
                return $row->getLeaveType();
            })->addColumn('request_date', function ($row) {
                return $row->getRequestDate();
            })->addColumn('request_days', function ($row) {
                return $row->getLeaveDuration() . ' ' . $row->leaveType->getLeaveBasis();
            })->addColumn('start_date', function ($row) {
                return $row->getStartDate();
            })->addColumn('end_date', function ($row) {
                return $row->getEndDate();
            })->addColumn('leave_number', function ($row) {
                return $row->getLeaveNumber();
            })->addColumn('requester', function ($row) {
                return $row->getRequesterName();
            })->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) {
                $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                $btn .= route('leave.requests.detail', $row->id).'" title="View Leave Request"><i class="bi bi-eye"></i></a>';
                $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                $btn .= route('leave.requests.print', $row->id).'" title="Print Leave Request"><i class="bi-printer"></i></a>';
                return $btn;
            })->rawColumns(['action', 'status'])
            ->make(true);
        }
        return view('LeaveRequest::Approved.index');
    }
}