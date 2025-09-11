<?php

namespace Modules\EmployeeExit\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Repositories\EmployeeExitPayableRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use DataTables;

class EmployeeExitApprovedPayablesController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param AdvanceRequestRepository $advanceRequests
     * @param UserRepository $users
     */
    public function __construct(
        DistrictRepository       $districts,
        EmployeeRepository       $employees,
        FiscalYearRepository     $fiscalYears,
        ExitHandOverNoteRepository $exitHandOverNote,
        EmployeeExitPayableRepository $employeeExitPayable,
        ExitInterviewRepository $exitinterview,
        ProjectCodeRepository    $projects,
        UserRepository           $users
    )
    {
        $this->districts = $districts;
        $this->projects = $projects;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->exitHandOverNote = $exitHandOverNote;
        $this->employeeExitPayable = $employeeExitPayable;
        $this->exitinterview = $exitinterview;
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
        // $this->authorize('advance-request');
        if ($request->ajax()) {
            $data = $this->employeeExitPayable->with(['fiscalYear', 'status'])->select(['*'])
            ->where(function ($q) use ($authUser) {
                    $q->where('created_by', $authUser->id);
                    $q->where('status_id', config('constant.APPROVED_STATUS'));
                });
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('exit.approved.payable.show', $row->id) . '" rel="tooltip" title="Approved Payable Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('EmployeeExit::EmployeeExitPayable.Approved.index')
        ->withAuthUser($authUser)
        ->withExitHandOverNote($exitHandOverNote)
        ->withExitinterview($exitinterview);

    }


    /**
     * Show the specified approved payable.
     *
     * @param $payableId
     * @return mixed
     */
    public function show($payableId)
    {
        $authUser = auth()->user();
        $employeeExitPayable = $this->employeeExitPayable->find($payableId);

        return view('EmployeeExit::EmployeeExitPayable.Approved.show')
              ->withEmployeeExitPayable($employeeExitPayable)
              ->withEmployees($this->employees->get())
              ->withAuthUser($authUser);
    }


}
