<?php

namespace Modules\LieuLeave\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LieuLeave\Repositories\LieuLeaveRequestLogRepository;
use Modules\LieuLeave\Repositories\LieuLeaveRequestRepository;
use Yajra\DataTables\DataTables;

class RejectedController extends Controller
{
    public function __construct(
        protected LieuLeaveRequestRepository $lieuLeaveRequests,
        protected LieuLeaveRequestLogRepository $lieuLeaveRequestLog,
    ) {}

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = $this->lieuLeaveRequests
                ->where('status_id', '=', config('constant.REJECTED_STATUS'))
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
                    $btn = '<a href="' . route('rejected.lieu.leave.requests.show', $row->id) . '" class="btn btn-sm btn-primary">
                      <i class="bi bi-eye"></i> 
                    </a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('LieuLeave::rejected.index');
    }


    public function show($id)
    {
        $lieuLeaveRequest = $this->lieuLeaveRequests->find($id);

        return view('LieuLeave::rejected.show', compact('lieuLeaveRequest'));
    }
}
