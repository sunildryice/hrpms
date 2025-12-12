<?php

namespace Modules\LieuLeave\Controllers;


use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\LieuLeave\Requests\Approve\UpdateRequest;
use Modules\LieuLeave\Notifications\LieuLeaveRequestApproved;
use Modules\LieuLeave\Notifications\LieuLeaveRequestRejected;
use Modules\LieuLeave\Repositories\LieuLeaveBalanceRepository;
use Modules\LieuLeave\Repositories\LieuLeaveRequestLogRepository;
use Modules\LieuLeave\Repositories\LieuLeaveRequestRepository;
use Yajra\DataTables\DataTables;

class ApproveController extends Controller
{

    public function __construct(
        protected LieuLeaveRequestRepository $lieuLeaveRequests,
        protected LieuLeaveRequestLogRepository $lieuLeaveRequestLog,
        protected LieuLeaveBalanceRepository $lieuLeaveBalance,
    ) {}

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = $this->lieuLeaveRequests
                ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
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
                    $btn = '<a href="' . route('approve.lieu.leave.requests.show', $row->id) . '" class="act-btns bt-primary">
                     <i class="bi bi-box-arrow-in-up-right"></i>
                    </a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('LieuLeave::approve.index');
    }


    public function show($id)
    {
        $lieuLeaveRequest = $this->lieuLeaveRequests->find($id);

        return view('LieuLeave::approve.show', compact('lieuLeaveRequest'));
    }


    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();

        try {
            $lieuLeaveRequest = $this->lieuLeaveRequests->update($id, $inputs);

            $authUser = auth()->user();


            if ($lieuLeaveRequest->status_id == config('constant.APPROVED_STATUS')) {
                $lieuLeaveRequest->requester->notify(new LieuLeaveRequestApproved($lieuLeaveRequest));
                $message = 'Lieu leave request approved successfully.';
            } elseif ($lieuLeaveRequest->status_id == config('constant.REJECTED_STATUS')) {
                $lieuLeaveRequest->requester->notify(new LieuLeaveRequestRejected($lieuLeaveRequest));

                $leaveBalance = $lieuLeaveRequest->leaveBalance;
                if ($leaveBalance) {
                    $leaveBalance->lieu_leave_request_id = null;
                    $leaveBalance->save();
                }

                $message = 'Lieu leave request rejected successfully.';
            }

            $authUser = auth()->user();

            $logInputs = [
                'user_id' => $authUser->id,
                'log_remarks' => $inputs['approver_remarks'] ?? '',
                'original_user_id' => $lieuLeaveRequest->requester_id,
                'status_id' => $lieuLeaveRequest->status_id,
                'lieu_leave_request_id' => $lieuLeaveRequest->id,
            ];

            $this->lieuLeaveRequestLog->create($logInputs);
        } catch (Exception $e) {
            return redirect()->back()->with('error_message', 'An error occurred while processing the request.');
        }

        return redirect()->route('approve.lieu.leave.requests.index')->with('success_message', $message);
    }
}
