<?php

namespace Modules\LieuLeave\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\LieuLeave\Models\LieuLeaveRequestLog;
use Modules\LieuLeave\Notifications\LieuLeaveRequestSubmitted;
use Modules\LieuLeave\Repositories\LieuLeaveBalanceRepository;
use Modules\LieuLeave\Repositories\LieuLeaveRequestRepository;
use Modules\LieuLeave\Requests\StoreRequest;
use Modules\LieuLeave\Requests\UpdateRequest;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository as RepositoriesUserRepository;
use Yajra\DataTables\DataTables;

class RequestController extends Controller
{

    public function __construct(
        protected ProjectCodeRepository $projects,
        protected RepositoriesUserRepository $users,
        protected LieuLeaveRequestRepository $lieuLeaveRequests,
        protected LieuLeaveRequestLog $lieuLeaveRequestLogs,
        protected FiscalYearRepository $fiscalYears,
        protected LieuLeaveBalanceRepository $lieuLeaveBalance,
        protected EmployeeRepository $employees,
    ) {}


    public function index(Request $request)
    {
        $month = now()->startOfMonth();
        $userId = auth()->id();

        $appliedLeaveofMonth = $this->lieuLeaveBalance->countAppliedLeave($userId, $month);
        $lieuLeaveBalance =  $this->lieuLeaveBalance->countLieuLeaveBalances($userId, $month);


        if ($appliedLeaveofMonth > 0 || $lieuLeaveBalance == 0) {
            $availableBalanceofMonthStatus = 'Not Available';
        } else {
            $availableBalanceofMonthStatus = 'Available';
        }


        if ($request->ajax()) {
            $query = $this->lieuLeaveRequests
                ->where('requester_id', '=', auth()->id())
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

                    $authUser = auth()->user();
                    $btn = '<a href="' . route('lieu.leave.requests.show', $row->id) . '" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye"></i> 
                    </a>';

                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('lieu.leave.requests.edit', $row->id) . '"  data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="Edit Lieu Leave Request"><i class="bi-pencil-square"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('LieuLeave::index', [
            'appliedLeaveofMonth' => $appliedLeaveofMonth,
            'lieuLeaveBalance' => $lieuLeaveBalance,
            'availableBalanceofMonthStatus' => $availableBalanceofMonthStatus,
        ]);
    }


    public function create()
    {
        $authUser = auth()->user();

        $projects = $this->projects->pluck('title', 'id');
        $supervisors = $this->users->getSupervisors($authUser)->pluck('full_name', 'id');

        $activeStaffs = $this->employees->getActiveEmployees();
        $substitutes = $activeStaffs->reject(function ($staff, $key) use ($authUser) {
            return $staff->id == $authUser->employee_id;
        });

        return view('LieuLeave::create', [
            'projects' => $projects,
            'supervisors' => $supervisors,
            'substitutes' => $substitutes,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();

        $inputs = $request->validated();

        try {
            $inputs['requester_id'] = auth()->id();
            $inputs['approver_id'] = $inputs['send_to'];
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            $inputs['fiscal_year_id'] = $this->fiscalYears->getCurrentFiscalYearId();
            $inputs['office_id'] = $authUser->employee->office_id;
            $inputs['department_id'] = $authUser->employee->department_id;
            $inputs['created_by'] = auth()->id();
            $inputs['request_date'] = now();
            $inputs['start_date'] = $inputs['leave_date'];
            $inputs['end_date'] = $inputs['leave_date'];

            DB::beginTransaction();

            $month  = Carbon::parse($inputs['leave_date']);
            $userId = auth()->id();

            $appliedLeaveofMonth = $this->lieuLeaveBalance->countAppliedLeave($userId, $month);
            $lieuLeaveBalance    = $this->lieuLeaveBalance->countLieuLeaveBalances($userId, $month);

            if ($appliedLeaveofMonth > 0 || $lieuLeaveBalance == 0) {

                return redirect()->back()->withInput()->with('error_message', 'You do not have available Lieu Leave balance for ' . $month->format('F Y') . '.');
            }

            if ($inputs['btn'] === 'submit') {


                $inputs['status_id'] = config('constant.SUBMITTED_STATUS');

                $lieuLeaveRequest = $this->lieuLeaveRequests->create($inputs);

                if (array_key_exists('substitutes', $inputs)) {
                    $lieuLeaveRequest->substitutes()->sync($inputs['substitutes']);
                }

                $logInputs = [
                    'user_id' => $authUser->id,
                    'log_remarks' => 'Lieu Leave request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id' => $lieuLeaveRequest->status_id,
                    'lieu_leave_request_id' => $lieuLeaveRequest->id,
                ];

                $availableLeave = $this->lieuLeaveBalance->getAvailableLeaveForUse($authUser->id, $inputs['leave_date'])->first();
                $availableLeave->lieu_leave_request_id = $lieuLeaveRequest->id;
                $availableLeave->save();

                $lieuLeaveRequest->approver->notify(new LieuLeaveRequestSubmitted($lieuLeaveRequest));
            } else {
                $inputs['status_id'] = config('constant.CREATED_STATUS');

                $lieuLeaveRequest = $this->lieuLeaveRequests->create($inputs);

                if (array_key_exists('substitutes', $inputs)) {
                    $lieuLeaveRequest->substitutes()->sync($inputs['substitutes']);
                }

                $logInputs = [
                    'user_id' => $authUser->id,
                    'log_remarks' => 'Work From Home request is created.',
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id' => $lieuLeaveRequest->status_id,
                    'lieu_leave_request_id' => $lieuLeaveRequest->id,
                ];
            }

            $this->lieuLeaveRequestLogs->create($logInputs);

            DB::commit();

            return redirect()->route('lieu.leave.requests.index')->with('success_message', 'Lieu Leave request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error_message', 'Something went wrong! ' . $e->getMessage());
        }
    }


    public function show($id)
    {
        $lieuLeaveRequest = $this->lieuLeaveRequests->with(['requester', 'approver', 'project', 'logs.user'])->findOrFail($id);

        // $this->authorize('view', $lieuLeaveRequest);

        return view('LieuLeave::show', [
            'lieuLeaveRequest' => $lieuLeaveRequest,
        ]);
    }

    public function edit($id)
    {
        $lieuLeaveRequest = $this->lieuLeaveRequests->find($id);

        // $this->authorize('update', $lieuLeaveRequest);

        $authUser = auth()->user();

        $projects = $this->projects->pluck('title', 'id');
        $supervisors = $this->users->getSupervisors($authUser)->pluck('full_name', 'id');

        $activeStaffs = $this->employees->getActiveEmployees();
        $substitutes = $activeStaffs->reject(function ($staff, $key) use ($authUser) {
            return $staff->id == $authUser->employee_id;
        });

        return view('LieuLeave::edit', [
            'lieuLeaveRequest' => $lieuLeaveRequest,
            'projects' => $projects,
            'supervisors' => $supervisors,
            'substitutes' => $substitutes,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $lieuLeaveRequest = $this->lieuLeaveRequests->find($id);

        // $this->authorize('update', $lieuLeaveRequest);

        $inputs = $request->validated();

        try {
            $inputs['approver_id'] = $inputs['send_to'];
            $inputs['updated_by'] = auth()->id();
            $inputs['start_date'] = $inputs['leave_date'];
            $inputs['end_date'] = $inputs['leave_date'];

            DB::beginTransaction();

            if ($inputs['btn'] === 'submit') {

                $month  = Carbon::parse($inputs['leave_date']);
                $userId = auth()->id();

                $appliedLeaveofMonth = $this->lieuLeaveBalance->countAppliedLeave($userId, $month);
                $lieuLeaveBalance    = $this->lieuLeaveBalance->countLieuLeaveBalances($userId, $month);

                if ($appliedLeaveofMonth > 0 || $lieuLeaveBalance == 0) {

                    return redirect()->back()->withInput()->with('error_message', 'You do not have available Lieu Leave balance for ' . $month->format('F Y') . '.');
                }

                $inputs['status_id'] = config('constant.SUBMITTED_STATUS');

                $this->lieuLeaveRequests->update($id, $inputs);

                if (array_key_exists('substitutes', $inputs)) {
                    $lieuLeaveRequest->substitutes()->sync($inputs['substitutes']);
                } else {
                    $lieuLeaveRequest->substitutes()->sync([]);
                }

                $logInputs = [
                    'user_id' => auth()->id(),
                    'log_remarks' => 'Lieu Leave request is submitted.',
                    'original_user_id' => $lieuLeaveRequest->requester_id,
                    'status_id' => $inputs['status_id'],
                    'lieu_leave_request_id' => $lieuLeaveRequest->id,
                ];

                $this->lieuLeaveRequestLogs->create($logInputs);

                $availableLeave = $this->lieuLeaveBalance->getAvailableLeaveForUse($userId, $inputs['leave_date'])->first();
                $availableLeave->lieu_leave_request_id = $lieuLeaveRequest->id;
                $availableLeave->save();

                $lieuLeaveRequest->approver->notify(new LieuLeaveRequestSubmitted($lieuLeaveRequest));

                $message = 'Lieu Leave request submitted successfully.';
            } else {

                $this->lieuLeaveRequests->update($id, $inputs);

                if (array_key_exists('substitutes', $inputs)) {
                    $lieuLeaveRequest->substitutes()->sync($inputs['substitutes']);
                } else {
                    $lieuLeaveRequest->substitutes()->sync([]);
                }

                $message = 'Lieu Leave request updated successfully.';
            }
            DB::commit();
            return redirect()->route('lieu.leave.requests.index')->with('success_message', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error_message', 'Something went wrong! ' . $e->getMessage());
        }
    }
}
