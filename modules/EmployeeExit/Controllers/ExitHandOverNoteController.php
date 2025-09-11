<?php

namespace Modules\EmployeeExit\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeExit\Notifications\ExitHandoverNoteSubmitted;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\EmployeeExit\Requests\ExitHandOverNote\UpdateRequest;
use Modules\Privilege\Repositories\UserRepository;

class ExitHandOverNoteController extends Controller
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
     * Show the form for editing the specified employee hand over note.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit()
    {
        $authUser = auth()->user();
        $exitHandOverNote = $this->exitHandOverNote->where('employee_id', '=', $authUser->employee_id)->first();
        $exitInterview = $this->exitInterview->where('employee_id', '=', $authUser->employee_id)->first();
        $exitAssetHandover = $exitHandOverNote->exitAssetHandover;
        $supervisors = $this->users->getSupervisors($authUser);
        $this->authorize('update', $exitHandOverNote);

        return view('EmployeeExit::ExitHandOverNote.edit')
            ->withAuthUser(auth()->user())
            ->withExitInterview($exitInterview)
            ->withExitHandOverNote($exitHandOverNote)
            ->withExitAssetHandover($exitAssetHandover)
            ->withSupervisors($supervisors);
    }

    /**
     * Update the specified employee hand over note in storage.
     *
     * @param \Modules\EmployeeExit\Requests\ExitHandOverNote\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $exitHandOverNote = $this->exitHandOverNote->find($id);
        $this->authorize('update', $exitHandOverNote);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $exitHandOverNote = $this->exitHandOverNote->update($id, $inputs);
        if ($exitHandOverNote) {
            $message = 'Exit HandOver Note is successfully updated.';
            if ($exitHandOverNote->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Exit HandOver Note is successfully submitted.';
                $exitHandOverNote->approver->notify(new ExitHandoverNoteSubmitted($exitHandOverNote));
                return redirect()->route('exit.employee.handover.note.show')
                    ->withSuccessMessage($message);
            }
            return redirect()->route('exit.employee.handover.note.edit')
                ->withSuccessMessage($message);

        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Exit HandOver Note can not be updated.');
    }

    /**
     * Show the specified exit handOver Note.
     *
     * @return mixed
     */
    public function show()
    {
        $authUser = auth()->user();
        $exitHandOverNote = $this->exitHandOverNote->where('employee_id', '=', $authUser->employee_id)->first();
        $exitInterview = $exitHandOverNote->exitInterview;
        $exitAssetHandover = $exitHandOverNote->exitAssetHandover;
        return view('EmployeeExit::ExitHandOverNote.show')
            ->withAuthUser(auth()->user())
            ->withExitInterview($exitInterview)
            ->withExitAssetHandover($exitAssetHandover)
            ->withExitHandOverNote($exitHandOverNote);
    }

    /**
     * Show the specified exit handOver Note.
     *
     * @return mixed
     */
    public function view($id)
    {
        $authUser = auth()->user();
        $exitHandOverNote = $this->exitHandOverNote->find($id);

        $exitInterview = $exitHandOverNote->exitInterview;
        return view('EmployeeExit::ExitHandOverNote.show')
            ->withAuthUser(auth()->user())
            ->withExitInterview($exitInterview)
            ->withExitHandOverNote($exitHandOverNote);
    }
}
