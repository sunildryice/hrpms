<?php

namespace Modules\EmployeeExit\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeExit\Notifications\EmployeeExitCreated;
use Modules\EmployeeExit\Notifications\EmployeeExitCreatedForSupervisor;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\EmployeeExit\Requests\StoreRequest;
use Modules\EmployeeExit\Requests\UpdateRequest;
use Modules\ExitStaffClearance\Notifications\StaffClearanceCreated;
use Modules\Privilege\Repositories\UserRepository;

class EmployeeExitController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees ,
     * @param ExitHandOverNoteRepository $exitHandOverNote ,
     * @param ExitInterviewRepository $exitInterview ,
     * @param UserRepository $users
     */
    public function __construct(
        protected EmployeeRepository         $employees,
        protected ExitHandOverNoteRepository $exitHandOverNote,
        protected ExitInterviewRepository    $exitInterview,
        protected UserRepository             $users
    )
    {
        $this->destinationPath = 'employeeExit';
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

        $this->authorize('manage-employee-exit');
        if ($request->ajax()) {
            $data = $this->exitHandOverNote->select(['*'])->with(['employee'])->orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('last_duty_date', function ($row) {
                    return $row->getLastDutyDate();
                })->addColumn('insurance', function ($row) {
                    return $row->getInsuranceStatus();
                })->addColumn('resignation_date', function ($row) {
                    return $row->getResignationDate();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('employee.exits.edit', $row->id) . '"  data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="Edit Employee Exit"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('employee.exits.destroy', $row->id) . '" rel="tooltip" title="Delete Employee Exit">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    if ($authUser->can('printExitPayable', $row)) {
                        $btn .= '&emsp;<a href = "' . route('employee.exits.print', $row->id) . '" target="_blank" class="btn btn-secondary btn-sm" rel="tooltip" title="Clearance Print">';
                        $btn .= '<i class="bi bi-printer"></i></a>';
                    }

                    if ($authUser->can('printExitInterview', $row)) {
                        $btn .= '&emsp;<a href = "' . route('exit.employee.interview.print', $row->exitInterview?->id) . '" target="_blank" class="btn btn-outline-secondary btn-sm" rel="tooltip" title="Exit Interview Print">';
                        $btn .= '<i class="bi bi-printer"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('EmployeeExit::index');

    }

    /**
     * Show the form for creating a new hand over note by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $employees = $this->employees->activeEmployees();
        $employeeId = array('0' => $authUser->employee_id);
        $employeeIds = $this->exitHandOverNote->select(['employee_id'])->pluck('employee_id')->toArray();
        $employeeIds = array_merge($employeeId, $employeeIds);
        $employees = $employees->reject(function ($employee) use ($employeeIds) {
            return in_array($employee->id, $employeeIds);
        });

        return view('EmployeeExit::create')
            ->withEmployee($authUser)
            ->withEmployees($employees);
    }


    /**
     * Store a newly created advance request in storage.
     *
     * @param \Modules\EmployeeExit\Requests\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['is_insurance'] = $request->is_insurance ? 1 : NULL;
        $employee = $this->employees->find($inputs['employee_id']);
        $exitHandOverNote = $this->exitHandOverNote->create($inputs);
        if ($exitHandOverNote) {
            $exitHandOverNote->employee->user->notify(new EmployeeExitCreated($exitHandOverNote));
            $exitHandOverNote->employee->supervisor?->user->notify(new EmployeeExitCreatedForSupervisor($exitHandOverNote));
            foreach($this->users->multiPermissionBasedUsers('hr-staff-clearance', 'finance-staff-clearance', 'logistic-staff-clearance') as $user) {
                $user->notify(new StaffClearanceCreated($exitHandOverNote->staffClearance));
            }

            return response()->json(['status' => 'ok',
                'exitHandOverNote' => $exitHandOverNote,
                'message' => 'Exit Hand Over Note  is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit Hand Over Note  can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified hand over note.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $exitHandOverNote = $this->exitHandOverNote->find($id);
        $this->authorize('update', $exitHandOverNote);

        return view('EmployeeExit::edit')
            ->withAuthUser(auth()->user())
            ->withExitHandOverNote($exitHandOverNote);
    }

    /**
     * Update the specified employee hand over note in storage.
     *
     * @param \Modules\EmployeeExit\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $exitHandOverNote = $this->exitHandOverNote->find($id);
        $this->authorize('update', $exitHandOverNote);
        $inputs = $request->validated();
        $inputs['is_insurance'] = $request->is_insurance ? 1 : NULL;
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $exitHandOverNote = $this->exitHandOverNote->update($id, $inputs);
        if ($exitHandOverNote) {
            $message = 'Employee exit is successfully updated.';
            return redirect()->route('employee.exits.index')
                ->withSuccessMessage($message);

        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Employee exit can not be updated.');
    }

    /**
     * Print the specified employee hand over note in storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */

    public function print($id)
    {
        $handOverNote = $this->exitHandOverNote->select(['*'])
            ->with(['employee', 'exitInterview', 'employeeExitPayable', 'logs'])
            ->find($id);

        return view('EmployeeExit::ExitHandOverNote.print')
            ->withHandOverNote($handOverNote);
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
        $exitHandOverNote = $this->exitHandOverNote->find($id);
        $this->authorize('delete', $exitHandOverNote);

        $flag = $this->exitHandOverNote->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Exit HandOver request is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Exit HandOver request can not deleted.',
        ], 422);
    }
}
