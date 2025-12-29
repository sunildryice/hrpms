<?php

namespace Modules\WorkFromHome\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Modules\WorkFromHome\Requests\Approve\UpdateRequest;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\WorkFromHome\Notifications\WorkFromHomeRequestApproved;
use Modules\WorkFromHome\Notifications\WorkFromHomeRequestRejected;
use Modules\WorkFromHome\Repositories\WorkFromHomeLogRepository;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;

class ApproveController extends Controller
{
    public function __construct(
        protected ProjectCodeRepository $projects,
        protected UserRepository $users,
        protected WorkFromHomeRepository $workFromHomes,
        protected WorkFromHomeLogRepository $workFromHomeLogs,
        protected FiscalYearRepository $fiscalYears
    ) {}



    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = $this->workFromHomes
                ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
                ->where('approver_id', '=', auth()->id())
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('request_date', function ($row) {
                    return $row->getRequestDate();
                })
                ->editColumn('start_date', function ($row) {
                    return $row->getStartDate();
                })
                ->editColumn('end_date', function ($row) {
                    return $row->getEndDate();
                })
                ->addColumn('project', function ($row) {
                    return $row->getProjectNames() ?? '-';
                })
                ->addColumn('employee', function ($row) {
                    return $row->requester->employee->full_name ?? $row->requester->full_name ?? '-';
                })
                ->addColumn('total_days', function ($row) {
                    return $row->getTotalDays();
                })
                ->addColumn('request_id', function ($row) {
                    return $row->getRequestId();
                })
                ->addColumn('status', function ($row) {

                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('approve.wfh.requests.show', $row->id) . '"class="act-btns bt-primary">
                     <i class="bi bi-box-arrow-in-up-right"></i>
                    </a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('WorkFromHome::approve.index');
    }

    public function show($id)
    {
        $wfhRequest = $this->workFromHomes
            ->with('logs')->find($id);

        $deliverables = $wfhRequest->getDeliverablesWithProjectNames();


        return view('WorkFromHome::approve.show', compact('wfhRequest', 'deliverables'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();

        $workFromHome = $this->workFromHomes->update($id, $inputs);


        if ($workFromHome->status_id == config('constant.APPROVED_STATUS')) {
            $workFromHome->requester->notify(new WorkFromHomeRequestApproved($workFromHome));

            $message = 'Work From Home request approved successfully.';
        } elseif ($workFromHome->status_id == config('constant.REJECTED_STATUS')) {
            $workFromHome->requester->notify(new WorkFromHomeRequestRejected($workFromHome));
            $message = 'Work From Home request rejected successfully.';
        }

        $authUser = auth()->user();

        $logInputs = [
            'user_id' => $authUser->id,
            'log_remarks' => $inputs['approver_remarks'] ?? '',
            'original_user_id' => $workFromHome->requester_id,
            'status_id' => $workFromHome->status_id,
            'work_from_home_id' => $workFromHome->id,
        ];

        $this->workFromHomeLogs->create($logInputs);

        return redirect()->route('approve.wfh.requests.index')->with('success_message', $message);
    }
}
