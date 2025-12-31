<?php

namespace Modules\OffDayWork\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\LieuLeave\Repositories\LieuLeaveBalanceRepository;
use Modules\OffDayWork\Notifications\OffDayWorkApproved;
use Modules\OffDayWork\Notifications\OffDayWorkRejected;
use Modules\OffDayWork\Repositories\OffDayWorkLogRepository;
use Modules\OffDayWork\Repositories\OffDayWorkRepository;
use Modules\OffDayWork\Requests\Approve\UpdateRequest;
use Yajra\DataTables\DataTables;

class ApproveController extends Controller
{

    public function __construct(
        protected OffDayWorkRepository $offDayWork,
        protected OffDayWorkLogRepository $offDayWorkLog,
        protected LieuLeaveBalanceRepository $lieuLeaveBalance
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->offDayWork
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
                ->addColumn('employee', function ($row) {
                    return $row->requester->employee->full_name ?? $row->requester->full_name ?? '-';
                })
                ->editColumn('date', function ($row) {
                    return $row->getOffDayWorkDate();
                })
                ->addColumn('project', function ($row) {
                    return $row->getProjectNames() ?? '-';
                })
                ->addColumn('status', function ($row) {

                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {

                    $authUser = auth()->user();
                    $btn = '<a href="' . route('approve.off.day.work.show', $row->id) . '" class="act-btns bt-primary">
                     <i class="bi bi-box-arrow-in-up-right"></i>
                    </a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('OffDayWork::approve.index');
    }

    public function show($offDayWork)
    {
        $offDayWork = $this->offDayWork->find($offDayWork);
        $deliverables = $offDayWork->getDeliverablesWithProjectNames();
        return view('OffDayWork::approve.show', compact('offDayWork', 'deliverables'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();

        try {
            $offDayWork = $this->offDayWork->update($id, $inputs);

            $this->lieuLeaveBalance->addBalance($offDayWork->requester_id, $offDayWork->id);

            if ($offDayWork->status_id == config('constant.APPROVED_STATUS')) {
                $offDayWork->requester->notify(new OffDayWorkApproved($offDayWork));
                $message = 'Off Day Work request approved successfully.';
            } elseif ($offDayWork->status_id == config('constant.REJECTED_STATUS')) {
                $offDayWork->requester->notify(new OffDayWorkRejected($offDayWork));
                $message = 'Off Day Work request rejected successfully.';
            }

            $authUser = auth()->user();

            $logInputs = [
                'user_id' => $authUser->id,
                'log_remarks' => $inputs['approver_remarks'] ?? '',
                'original_user_id' => $offDayWork->requester_id,
                'status_id' => $offDayWork->status_id,
                'off_day_work_id' => $offDayWork->id,
            ];

            $this->offDayWorkLog->create($logInputs);
        } catch (Exception $e) {
            return redirect()->back()->with('error_message', 'An error occurred while processing the request.');
        }

        return redirect()->route('approve.off.day.work.index')->with('success_message', $message);
    }
}
