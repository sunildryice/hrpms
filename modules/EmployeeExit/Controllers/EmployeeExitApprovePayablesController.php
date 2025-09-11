<?php

namespace Modules\EmployeeExit\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeExit\Notifications\ExitPayable\ExitPayableApproved;
use Modules\EmployeeExit\Notifications\ExitPayable\ExitPayableRecommended;
use Modules\EmployeeExit\Notifications\ExitPayable\ExitPayableRejected;
use Modules\EmployeeExit\Notifications\ExitPayable\ExitPayableReturned;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Repositories\EmployeeExitPayableRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\EmployeeExit\Requests\EmployeeExitPayable\Approve\StoreRequest;

use DataTables;

class EmployeeExitApprovePayablesController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository       $employees,
     * @param ExitHandOverNoteRepository $exitHandOverNote,
     * @param EmployeeExitPayableRepository $employeeExitPayable,
     * @param FiscalYearRepository     $fiscalYears,
     * @param ExitInterviewRepository $exitinterview,
     * @param UserRepository           $users
     */
    public function __construct(
        EmployeeRepository       $employees,
        ExitHandOverNoteRepository $exitHandOverNote,
        EmployeeExitPayableRepository $employeeExitPayable,
        FiscalYearRepository     $fiscalYears,
        ExitInterviewRepository $exitinterview,
        UserRepository           $users
    )
    {
        $this->employees = $employees;
        $this->exitHandOverNote = $exitHandOverNote;
        $this->employeeExitPayable = $employeeExitPayable;
        $this->exitinterview = $exitinterview;
        $this->fiscalYears = $fiscalYears;
        $this->users = $users;
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
        $exitHandOverNote = $this->exitHandOverNote->where('employee_id','=',$authUser->employee_id)->first();
        $exitinterview = $this->exitinterview->where('employee_id','=',$authUser->employee_id)->first();
        if ($request->ajax()) {
            $data = $this->employeeExitPayable->with(['fiscalYear', 'status'])->select(['*'])
            ->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
                });;
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('exit.approve.payable.create', $row->id) . '" rel="tooltip" title="Approve Payable Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('EmployeeExit::EmployeeExitPayable.Approve.index')
        ->withAuthUser($authUser)
        ->withExitHandOverNote($exitHandOverNote)
        ->withExitinterview($exitinterview);

    }

    /**
     * Show the form for creating a new advance request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($payableId)
    {
        $authUser = auth()->user();
        $employeeExitPayable = $this->employeeExitPayable->find($payableId);
        $exitHandOverNote = $this->exitHandOverNote->where('employee_id','=',$authUser->employee_id)->first();
        $exitinterview = $this->exitinterview->where('employee_id','=',$authUser->employee_id)->first();
        $this->authorize('approve', $employeeExitPayable);
        $supervisors = collect();
        if ($authUser->employee) {
            $latestTenure = $authUser->employee->latestTenure;
            $supervisors = $this->users->select(['id', 'full_name'])
                ->whereIn('employee_id', [$latestTenure->supervisor_id, $latestTenure->cross_supervisor_id, $latestTenure->next_line_manager_id])
                ->get();
        }
        return view('EmployeeExit::EmployeeExitPayable.Approve.create')
                ->withEmployees($this->employees->get())
                ->withAuthUser($authUser)
                ->withEmployeeExitPayable($employeeExitPayable)
                ->withExitHandOverNote($exitHandOverNote)
                ->withSupervisors($supervisors)
                ->withExitinterview($exitinterview);
    }


    /**
     * Store a newly created advance request in storage.
     *
     * @param \Modules\AdvanceRequest\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request,$payableId)
    {
        $authUser = auth()->user();
        $employeeExitPayable = $this->employeeExitPayable->find($payableId);
        // $this->authorize('create-advance-request');
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
         $employeeExitPayable = $this->employeeExitPayable->approve($employeeExitPayable->id, $inputs);

        if ($employeeExitPayable) {
            $message = '';
            if ($employeeExitPayable->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Payable request is successfully returned.';
                $employeeExitPayable->createdBy->notify(new ExitPayableReturned($employeeExitPayable));
            } else if ($employeeExitPayable->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Payable request is successfully recommended.';
                $employeeExitPayable->approver->notify(new ExitPayableRecommended($employeeExitPayable));
            } else if ($employeeExitPayable->status_id == config('constant.APPROVED_STATUS')) {
                $this->employees->update($employeeExitPayable->employee_id, [
                    'last_working_date' => now()->toDateTimeString(),
                    'activated_at'      => null
                ]);
                $message = 'Payable request is successfully approved.';
                $employeeExitPayable->createdBy->notify(new ExitPayableApproved($employeeExitPayable));
            }

            return redirect()->route('exit.approve.payable.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Payable request can not be approved.');
    }
}
