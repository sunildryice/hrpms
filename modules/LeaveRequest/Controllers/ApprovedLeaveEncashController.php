<?php

namespace Modules\LeaveRequest\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LeaveRequest\Repositories\LeaveEncashRepository;
use Yajra\DataTables\DataTables;

class ApprovedLeaveEncashController extends Controller
{
    private $leaveEncash;
    public function __construct(
        LeaveEncashRepository $leaveEncash
    ) {
        $this->leaveEncash = $leaveEncash;
    }

    public function index(Request $request)
    {
        $data = $this->leaveEncash->getApproved();

        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->leaveEncash->getApproved();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('leave_type', function ($row) {
                    return $row->getLeaveType();
                })->addColumn('request_date', function ($row) {
                return $row->getRequestDate();
            })->addColumn('encash_balance', function ($row) {
                return $row->encash_balance;
            })->addColumn('encash_number', function ($row) {
                return $row->getEncashNumber();
            })->addColumn('requester', function ($row) {
                return $row->getRequesterName();
            })->addColumn('employee', function ($row) {
                return $row->getEmployeeName();
            })->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                $btn .= route('approved.leave.encash.show', $row->id) . '" title="View Leave Request"><i class="bi bi-eye"></i></a>';
                $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                $btn .= route('leave.encash.print', $row->id) . '" title="Print Leave Request"><i class="bi-printer"></i></a>';
                if ($authUser->can('pay', $row)) {
                    $btn .= '&emsp;<button class="btn btn-outline-success btn-sm open-payment-modal-form" href="';
                    $btn .= route('approved.leave.encash.pay.create', $row->id) . '" rel="tooltip" title="Pay"><i class="bi bi-cash-stack"></i></button>';
                }
                return $btn;
            })->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('LeaveRequest::LeaveEncash.Approved.index');
    }

    public function show($id)
    {
        $leaveEncash = $this->leaveEncash->find($id);
        return view('LeaveRequest::LeaveEncash.Approved.show', compact('leaveEncash'));
    }
}
