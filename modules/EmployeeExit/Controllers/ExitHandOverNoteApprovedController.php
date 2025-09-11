<?php

namespace Modules\EmployeeExit\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

class ExitHandOverNoteApprovedController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param UserRepository $users
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected ExitHandOverNoteRepository $exitHandOverNote,
        protected ExitInterviewRepository $exitInterview,
        protected FiscalYearRepository $fiscalYears,
        protected UserRepository $users
    ) {
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
                    $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.CLOSED_STATUS')]);
                })->orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('last_duty_date', function ($row) {
                return $row->getLastDutyDate();
            })->addColumn('resignation_date', function ($row) {
                return $row->getResignationDate();
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('approved.exit.handover.note.show', $row->id) . '" rel="tooltip" title="View Handover Note"><i class="bi bi-eye"></i></a>';
                $btn .= '&emsp;<a href = "' . route('approved.exit.handover.note.print', $row->id) . '" target="_blank" class="btn btn-outline-primary btn-sm" rel="tooltip" title="Print Handover">';
                $btn .= '<i class="bi bi-printer"></i></a>';
                return $btn;
            })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('EmployeeExit::ExitHandOverNote.Approved.index');
    }

    public function show(Request $request, $id)
    {
        $authUser = auth()->user();
        $exitHandOverNote = $this->exitHandOverNote->find($id);
        $exitInterview = $exitHandOverNote->exitInterview;

        return view('EmployeeExit::ExitHandOverNote.Approved.show')
            ->withAuthUser($authUser)
            ->withExitInterview($exitInterview)
            ->withExitHandOverNote($exitHandOverNote);
    }

    public function print($id)
    {
        $handOverNote = $this->exitHandOverNote->select(['*'])
            ->with(['employee', 'exitInterview', 'employeeExitPayable', 'logs', 'handoverProjects', 'handoverActivities'])
            ->find($id);
        return view('EmployeeExit::ExitHandOverNote.Approved.print')
            ->withHandOverNote($handOverNote);
    }

}
