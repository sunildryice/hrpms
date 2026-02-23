<?php

namespace Modules\EmployeeExit\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EmployeeExit\Models\ExitHandOverNote;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Yajra\DataTables\DataTables;

class PendingEmployeeExitController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param ExitHandOverNoteRepository $exitHandOverNote ,
     */
    public function __construct(
        protected ExitHandOverNoteRepository $exitHandoverNotes,
    ) {}


    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->exitHandoverNotes
                ->whereHas('employeeExitPayable', function ($q) {
                    $q->where('status_id', '!=', config('constant.APPROVED_STATUS'));
                })->orderBy('created_at', 'desc')
                ->get();

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
                ->addColumn('handovernote_status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('exit_interview_status', function ($row) {
                    return '<span class="' . $row->exitInterview?->getStatusClass() . '">' . $row->exitInterview?->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('update', $row->employeeExitPayable)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm open-pending-employee-exit-update-modal-form" href="';
                        $btn .= route('employee.exit.pending.edit', $row->id) . '"  data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="Edit Employee Exit"><i class="bi-pencil-square"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'handovernote_status', 'exit_interview_status'])
                ->make(true);
        }

        return view('EmployeeExit::Pending.index');
    }

    public function edit(Request $request, $id)
    {
        $exitHandoverNote = $this->exitHandoverNotes->find($id);

        $array = [
            'exitHandoverNote' => $exitHandoverNote,
        ];

        return view('EmployeeExit::Pending.edit', $array);
    }

    public function update(Request $request, $id)
    {
        $exitHandOverNote = $this->exitHandoverNotes->find($id);

        if ($request->filled('skip_exit_handover_note')) {
            $exitHandOverNote->update([
                'status_id' => config('constant.CLOSED_STATUS'),
                'remarks'   => $request->skip_exit_handover_note_remarks
            ]);
            $exitHandOverNote->logs()->create([
                'user_id'       => auth()->user()->id,
                'log_remarks'   => 'Employee exit handover note closed.',
                'status_id'     => config('constant.CLOSED_STATUS')
            ]);
        }
        if ($request->filled('skip_exit_interview')) {
            if ($exitHandOverNote->exitInterview) {
                $exitHandOverNote->exitInterview->update([
                    'status_id' => config('constant.CLOSED_STATUS'),
                    'remarks'   => $request->skip_exit_interview_remarks
                ]);
                $exitHandOverNote->exitInterview->logs()->create([
                    'user_id'       => auth()->user()->id,
                    'log_remarks'   => 'Employee exit interview closed.',
                    'status_id'     => config('constant.CLOSED_STATUS')
                ]);
            }
        }

        return response()->json([
            'type'      => 'success',
            'message'   => 'Updated Successfully!'
        ], 200);
    }
}
