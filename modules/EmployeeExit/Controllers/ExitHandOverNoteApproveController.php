<?php

namespace Modules\EmployeeExit\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeExit\Notifications\EmployeeExitCreated;
use Modules\EmployeeExit\Notifications\ExitHandoverNoteApproved;
use Modules\EmployeeExit\Notifications\ExitHandoverNoteApprovedToEmployee;
use Modules\EmployeeExit\Notifications\ExitHandoverNoteRejectedToEmployee;
use Modules\EmployeeExit\Notifications\ExitHandoverNoteReturnedToEmployee;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\EmployeeExit\Requests\ExitHandOverNote\Approve\StoreRequest;

use DataTables;

class ExitHandOverNoteApproveController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param UserRepository $users
     */
    public function __construct(
        protected EmployeeRepository         $employees,
        protected ExitHandOverNoteRepository $exitHandOverNote,
        protected ExitInterviewRepository    $exitInterview,
        protected FiscalYearRepository       $fiscalYears,
        protected UserRepository             $users
    )
    {
        $this->destinationPath = 'employeeExit';
    }

    /**
     * Display a listing of the Exit Handover Notes
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->exitHandOverNote->with(['employee', 'status'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    return $row->getEmployeeName();
                })
                ->addColumn('last_duty_date', function ($row) {
                    return $row->getLastDutyDate();
                })
                ->addColumn('resignation_date', function ($row) {
                    return $row->getResignationDate();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if($authUser->can('approve', $row)){
                        $btn .= '<a href = "'.route('approve.exit.handover.note.create', $row->id).'" class="btn btn-secondary btn-sm" rel="tooltip" title="Approve Handover">';
                        $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('EmployeeExit::ExitHandOverNote.Approve.index');

    }

    /**
     * Show the form for creating a new hand over note by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $exitHandOverNote = $this->exitHandOverNote->find($id);
        $this->authorize('approve', $exitHandOverNote);
        return view('EmployeeExit::ExitHandOverNote.Approve.create')
            ->withAuthUser(auth()->user())
            ->withExitHandOverNote($exitHandOverNote);
    }


    /**
     * Store a newly created Exit Handover Note in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $exitHandOverNote = $this->exitHandOverNote->find($id);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $exitHandOverNote = $this->exitHandOverNote->approve($exitHandOverNote->id, $inputs);
        if ($exitHandOverNote) {
            $message = '';
            if ($exitHandOverNote->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Exit Handover Note is successfully returned.';
                $exitHandOverNote->employee->user->notify(new ExitHandoverNoteReturnedToEmployee($exitHandOverNote));
            } else if ($exitHandOverNote->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Exit Handover Note is successfully rejected.';
                $exitHandOverNote->employee->user->notify(new ExitHandoverNoteRejectedToEmployee($exitHandOverNote));
            } else {
                $message = 'Exit Handover Note is successfully approved.';
                    $exitHandOverNote->employee->user->notify(new ExitHandoverNoteApprovedToEmployee($exitHandOverNote));
                    $exitHandOverNote->createdBy->notify(new ExitHandOverNoteApproved($exitHandOverNote));
            }
            return redirect()->route('approve.exit.handover.note.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Exit Handover Note can not be approved.');
    }

}
