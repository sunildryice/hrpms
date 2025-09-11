<?php

namespace Modules\EmployeeExit\Controllers;
use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\EmployeeExit\Notifications\ExitAssetHandover\ExitAssetHandoverSubmitted;
use Modules\Privilege\Repositories\UserRepository;

use Modules\Employee\Repositories\EmployeeRepository;

use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\GoodRequest\Repositories\GoodRequestAssetRepository;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Requests\ExitAssetHandover\StoreRequest;
use Modules\EmployeeExit\Repositories\ExitAssetHandoverRepository;

class ExitAssetHandOverController extends Controller
{
    protected $exitAssetHandovers;
    protected $exitHandOverNote;
    protected $exitInterview;

    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository     $employees,
     * @param ExitHandOverNoteRepository     $exitHandOverNote,
     * @param ExitAssetHandoverRepository     $exitAssetHandovers,
     * @param ExitInterviewRepository    $exitInterview,
     * @param UserRepository         $users
     * @param GoodRequestAssetRepository  $goodRequestAssets,
     */
    public function __construct(
        EmployeeRepository     $employees,
        ExitHandOverNoteRepository     $exitHandOverNote,
        ExitAssetHandoverRepository     $exitAssetHandovers,
        ExitInterviewRepository    $exitInterview,
        UserRepository         $users,
        GoodRequestAssetRepository  $goodRequestAssets,
    )
    {
        $this->employees = $employees;
        $this->exitHandOverNote = $exitHandOverNote;
        $this->exitAssetHandovers = $exitAssetHandovers;
        $this->exitInterview = $exitInterview;
        $this->users = $users;
        $this->goodRequestAssets = $goodRequestAssets;
        $this->destinationPath = 'employeeExit';
    }

    /**
     * Show the specified exit interview.
     *
     * @param $advanceRequestId
     * @return mixed
     */
    public function show()
    {
        $authUser = auth()->user();
        $exitAssetHandovers = $this->exitAssetHandovers->where('employee_id','=',$authUser->employee_id)->first();
        $exitHandOverNote = $this->exitHandOverNote->where('employee_id','=',$authUser->employee_id)->first();
        $exitInterview = $this->exitInterview->where('employee_id','=',$authUser->employee_id)->first();

        return view('EmployeeExit::ExitAssetHandOver.show')
            ->withAuthUser(auth()->user())
            ->withExitHandOverNote($exitHandOverNote)
            ->withExitAssetHandover($exitAssetHandovers)
            ->withExitInterview($exitInterview);
    }


    /**
     * Show the form for editing the specified exit asset handover.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit()
    {
        $authUser = auth()->user();
        $exitAssetHandover = $this->exitAssetHandovers->where('employee_id','=',$authUser->employee_id)->first();
        $exitInterview = $this->exitInterview->where('employee_id','=',$authUser->employee_id)->first();
        $this->authorize('update', $exitAssetHandover);
        $exitHandOverNote = $this->exitHandOverNote->where('employee_id','=',$authUser->employee_id)->first();
        $supervisors = $this->users->getSupervisors($authUser);
        $approvers = $this->users->permissionBasedUsers('approve-exit-interview');
        $handoverCount = $this->goodRequestAssets->select(['handover_status_id'])->where('assigned_user_id','=',$exitInterview->employee->user->id)
                                                ->where('handover_status_id','<>',config('constant.APPROVED_STATUS'))
                                                ->count();

        return view('EmployeeExit::ExitAssetHandOver.edit')
            ->withApprovers($approvers)
            ->withAuthUser(auth()->user())
            ->withExitHandOverNote($exitHandOverNote)
            ->withExitInterview($exitInterview)
            ->withExitAssetHandover($exitAssetHandover)
            ->withHandoverCount($handoverCount)
            ->withSupervisors($supervisors);
    }

    /**
     * Update the specified exit asset handover in storage.
     *
     * @param \Modules\AdvanceRequest\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(StoreRequest $request,$id)
    {
        $inputs = $request->validated();
        $exitAssetHandovers = $this->exitAssetHandovers->where('employee_id','=',$id)->first();
        $this->authorize('update', $exitAssetHandovers);
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $exitAssetHandovers = $this->exitAssetHandovers->update($exitAssetHandovers->id, $inputs);
        if ($exitAssetHandovers) {
            $message = 'Exit Interview is successfully updated.';
            if ($exitAssetHandovers->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Exit HandOver Note is successfully submitted.';
                $exitAssetHandovers->approver->notify(new ExitAssetHandoverSubmitted($exitAssetHandovers));
                return redirect()->route('exit.employee.handover.asset.show')
                ->withSuccessMessage($message);
            }
            return redirect()->route('exit.employee.handover.asset.edit')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Exit Interview can not be updated.');
    }

    /**
     * Remove the specified exit asset handover from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $exitHandOverNote = $this->exitHandOverNote->find($id);
        // $this->authorize('delete', $exitHandOverNote);
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


    public function print($id)
    {
        $data = $this->exitAssetHandovers->select('*')->findOrFail($id);
        $assets = $this->goodRequestAssets->with(['asset','submittedLog'])
        ->where('assigned_user_id', $data->employee->getUserId())
        ->get();

        return view('EmployeeExit::ExitAssetHandOver.print', compact('data', 'assets'));
    }

}
