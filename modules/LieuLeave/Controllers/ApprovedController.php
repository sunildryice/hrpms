<?php

namespace Modules\LieuLeave\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LieuLeave\Repositories\LieuLeaveRequestLogRepository;
use Modules\LieuLeave\Repositories\LieuLeaveRequestRepository;
use Yajra\DataTables\DataTables;

class ApprovedController extends Controller
{
    public function __construct(
        protected LieuLeaveRequestRepository $lieuLeaveRequests,
        protected LieuLeaveRequestLogRepository $lieuLeaveRequestLog,
    ) {}

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = $this->lieuLeaveRequests
                ->where('status_id', '=', config('constant.APPROVED_STATUS'))
                ->where('approver_id', '=', auth()->id())
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('request_date', function ($row) {
                    return  $row->getRequestDate();
                })
                ->addColumn('request_id', function ($row) {
                    return $row->getRequestId();
                })
                ->editColumn('leave_date', function ($row) {
                    return $row->getStartDate();
                })
                ->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })
                ->addColumn('status', function ($row) {

                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('approved.lieu.leave.requests.show', $row->id) . '" class="btn btn-sm btn-outline-primary"
                     title="View Lieu Leave Request">
                    <i class="bi bi-eye"></i> 
                    </a>';

                    $btn .= '&emsp;<a href="' . route('lieu.leave.requests.print', $row->id) . '" class="btn btn-sm btn-outline-primary" target="_blank">
                     <i class="bi bi-printer"></i>
                    </a>';

                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('LieuLeave::approved.index');
    }

    public function show($id)
    {
        $lieuLeaveRequest = $this->lieuLeaveRequests->find($id);

        return view('LieuLeave::approved.show', compact('lieuLeaveRequest'));
    }
}
