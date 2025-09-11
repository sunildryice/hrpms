<?php

namespace Modules\EmployeeExit\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\EmployeeExit\Notifications\ExitPayable\ExitPayableSubmitted;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Repositories\EmployeeExitPayableRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveTypeRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\EmployeeExit\Requests\EmployeeExitPayable\StoreRequest;
use Modules\EmployeeExit\Requests\EmployeeExitPayable\UpdateRequest;

use DataTables;

class EmployeeExitPayablesController extends Controller
{

    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository       $employees,
     * @param FiscalYearRepository     $fiscalYears,
     * @param ExitHandOverNoteRepository $exitHandOverNote,
     * @param EmployeeExitPayableRepository $employeeExitPayable,
     * @param ExitInterviewRepository $exitinterview,
     * @param LeaveRepository $leaves,
     * @param LeaveTypeRepository $leaveType,
     * @param UserRepository $users
     */
    public function __construct(
        protected EmployeeRepository       $employees,
        protected FiscalYearRepository     $fiscalYears,
        protected ExitHandOverNoteRepository $exitHandOverNote,
        protected EmployeeExitPayableRepository $employeeExitPayable,
        protected ExitInterviewRepository $exitinterview,
        protected LeaveRepository $leaves,
        protected LeaveTypeRepository $leaveType,
        protected UserRepository  $users
    )
    {
        $this->destinationPath = 'payable';
    }

    /**
     * Display a listing of the advance requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->employeeExitPayable->with(['fiscalYear', 'status'])->select(['*'])->orderBy('created_at', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('exit.payable.show', $row->id) . '" rel="tooltip" title="View Employee Payable Request"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-payable-modal-form" href="';
                        $btn .= route('exit.payable.edit', $row->id) . '" rel="tooltip" title="Edit Employee Payable Request"><i class="bi-pencil-square"></i></a>';

                    }
                    // if ($authUser->can('delete', $row)) {
                    //     $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    //     $btn .= 'data-href="' . route('exit.payable.delete', $row->id) . '">';
                    //     $btn .= '<i class="bi-trash"></i></a>';
                    // }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('EmployeeExit::EmployeeExitPayable.index')
        ->withAuthUser($authUser);

    }

    /**
     * Show the form for creating a new advance request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        exit;
        $authUser = auth()->user();
        $employees = $this->employees->getActiveEmployees();
        $exitEmployeeIds = $this->exitHandOverNote->select(['employee_id'])
            ->whereIn('status_id', [3,4,5,6])
            ->pluck('employee_id')->toArray();
        $exitPayableEmployeeIds = $this->employeeExitPayable->select(['employee_id'])->pluck('employee_id')->toArray();
        $employees = $employees->filter(function ($employee) use ($exitEmployeeIds){
           return in_array($employee->id, $exitEmployeeIds);
        })->reject(function($employee) use ($exitPayableEmployeeIds){
            return in_array($employee->id, $exitPayableEmployeeIds);
        });

        return view('EmployeeExit::EmployeeExitPayable.create')
                ->withEmployees($employees);
    }


    /**
     * Store a newly created advance request in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(StoreRequest $request)
    {
        exit;
        $authUser = auth()->user();
        $inputs = $request->validated();
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $inputs['requester_id'] = auth()->id();
        $inputs['created_by'] = auth()->id();
        $inputs['fiscal_year_id'] = $fiscalYear->id;
        $inputs['request_date'] = date('Y-m-d');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $employeeExitPayable = $this->employeeExitPayable->create($inputs);

        if ($employeeExitPayable) {
           return response()->json(['status' => 'ok',
                'employeeExitPayable' => $employeeExitPayable,
                'message' => 'Employee Exit Payable is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Employee Exit Payable can not be added.'], 422);
    }


    /**
     * Show the specified advance request.
     *
     * @param $payableId
     * @return mixed
     */
    public function show($payableId)
    {
        $authUser = auth()->user();
        $employeeExitPayable = $this->employeeExitPayable->find($payableId);
        // dd(date('Y-m-d') > $employeeExitPayable->exitHandOverNote->last_duty_date);
        return view('EmployeeExit::EmployeeExitPayable.show')
            ->withEmployees($this->employees->get())
            ->withEmployeeExitPayable($employeeExitPayable)
            ->withAuthUser($authUser);
    }


    /**
     * Show the form for editing the specified advance request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $employeeExitPayable = $this->employeeExitPayable->find($id);
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                            ->where('end_date', '>=', date('Y-m-d'))
                            ->first();
        // $leaves = $this->leaves->select(['*'])
        //                     ->with(['leaveType'])
        //                     ->where('fiscal_year_id', $fiscalYear->id)
        //                     ->where('employee_id', $employeeExitPayable->employee_id)
        //                     ->whereHas('leaveType', function ($q) {
        //                         $q->whereNotNull('activated_at');
        //                         $q->whereEncashment('1');
        //                     })->get();
        $leaves = $this->leaves->getEmployeeLeaves($employeeExitPayable->employee_id, $fiscalYear->id)->where('leaveType.encashment', 1);
        $total_balance = 0;
        foreach($leaves as $leave){
            // if($leave->leaveType->leave_basis == 2){
            //     $balance =  ($leave->balance) / 8;
            // }else{
                $balance = $leave->balance;
            // }
            $total_balance += $balance;
        }

        $supervisors = collect();
        if ($authUser->employee) {
            $latestTenure = $authUser->employee->latestTenure;
            $supervisors = $this->users->select(['id', 'full_name'])
                ->whereIn('employee_id', [$latestTenure->supervisor_id, $latestTenure->cross_supervisor_id, $latestTenure->next_line_manager_id])
                ->get();
        }

        return view('EmployeeExit::EmployeeExitPayable.edit')
            ->withAuthUser(auth()->user())
            ->withLeaveBalance($total_balance)
            ->withSupervisors($supervisors)
            ->withEmployeeExitPayable($employeeExitPayable);
    }

    /**
     * Update the specified advance request in storage.
     *
     * @param \Modules\AdvanceRequest\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $employeeExitPayable = $this->employeeExitPayable->find($id);
        // $this->authorize('update', $advanceRequest);
        $inputs = $request->validated();

        $input['leave_balance']='';
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $employeeExitPayable = $this->employeeExitPayable->update($id, $inputs);
        if ($employeeExitPayable) {
            $message = 'Employee Exit Payable is successfully updated.';
            if ($employeeExitPayable->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Employee Exit Payable is successfully submitted.';
                $employeeExitPayable->approver->notify(new ExitPayableSubmitted($employeeExitPayable));
            }
            return redirect()->route('exit.payable.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Employee Exit Payable can not be updated.');
    }

    /**
     * Remove the specified advance request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $employeeExitPayable = $this->employeeExitPayable->find($id);
        $this->authorize('delete', $employeeExitPayable);
        $flag = $this->employeeExitPayable->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Employee Payable is  successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Employee Payable can not deleted.',
        ], 422);
    }

}
