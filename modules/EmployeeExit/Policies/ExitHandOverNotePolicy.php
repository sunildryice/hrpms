<?php

namespace Modules\EmployeeExit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\EmployeeExit\Models\ExitHandOverNote;
use Modules\Privilege\Models\User;

class ExitHandOverNotePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given exit handover note can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeExit\Models\ExitHandOverNote  $exitHandOverNote
     * @return bool
     */
    public function amend(User $user, ExitHandOverNote $exitHandOverNote)
    {
        return $exitHandOverNote->status_id == 6 && !$exitHandOverNote->modification_advance_request_id && !$exitHandOverNote->childExitHandOverNote
            && in_array($user->id, [$exitHandOverNote->requester_id, $exitHandOverNote->created_by]);
    }

    /**
     * Determine if the given exit handover note can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeExit\Models\ExitHandOverNote  $exitHandOverNote
     * @return bool
     */
    public function approve(User $user, ExitHandOverNote $exitHandOverNote)
    {
        return ($exitHandOverNote->status_id == 3 && $user->id == $exitHandOverNote->approver_id);
    }

      /**
     * Determine if the given approved approve request can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeExit\Models\ExitHandOverNote  $exitHandOverNote
     * @return bool
     */
    public function viewApproved(User $user, ExitHandOverNote $exitHandOverNote)
    {
        return in_array($exitHandOverNote->status_id, [config('constant.APPROVED_STATUS')]);
    }

    /**
     * Determine if the given exit handover note can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeExit\Models\ExitHandOverNote  $exitHandOverNote
     * @return bool
     */
    public function delete(User $user, ExitHandOverNote $exitHandOverNote)
    {
        $interview = true;
        $payable = true;
        if($exitHandOverNote->exitInterview){
            $interview = $exitHandOverNote->exitInterview->updated_by == NULL;
        }
        if($exitHandOverNote->employeeExitPayable){
            $payable = $exitHandOverNote->employeeExitPayable->updated_by == NULL;
        }
        return in_array($exitHandOverNote->status_id, [config('constant.CREATED_STATUS')]) && 
                in_array($user->id, [$exitHandOverNote->requester_id, $exitHandOverNote->created_by])
                && $exitHandOverNote->updated_by == NULL && $interview && $payable;
    }

    /**
     * Determine if the given exit handover note can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeExit\Models\ExitHandOverNote  $exitHandOverNote
     * @return bool
     */
    public function print(User $user, ExitHandOverNote $exitHandOverNote)
    {
        return true;
        return in_array($exitHandOverNote->status_id, [config('constant.APPROVED_STATUS')]) && 
                in_array($user->id, [$exitHandOverNote->created_by]) && 
                in_array($exitHandOverNote->exitInterview->status_id, [config('constant.APPROVED_STATUS')]) && 
                in_array($exitHandOverNote->employeeExitPayable->status_id, [config('constant.APPROVED_STATUS')]);
    }

    public function printExitInterview(User $user, ExitHandOverNote $exitHandOverNote)
    {
        return in_array($exitHandOverNote->status_id, [config('constant.APPROVED_STATUS')]) && 
                in_array($user->id, [$exitHandOverNote->created_by]) && 
                in_array($exitHandOverNote->exitInterview->status_id, [config('constant.APPROVED_STATUS')]);
    }

    public function printExitPayable(User $user, ExitHandOverNote $exitHandOverNote)
    {
        return in_array($exitHandOverNote->status_id, [config('constant.APPROVED_STATUS'), config('constant.CLOSED_STATUS')]) && 
                in_array($user->id, [$exitHandOverNote->created_by]) && 
                in_array($exitHandOverNote->employeeExitPayable->status_id, [config('constant.APPROVED_STATUS')]);
    }


    /**
     * Determine if the given exit handover note can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeExit\Models\ExitHandOverNote  $exitHandOverNote
     * @return bool
     */
    public function update(User $user, ExitHandOverNote $exitHandOverNote)
    {
        return in_array($exitHandOverNote->status_id, [1, 2]) &&
            in_array($user->id, [$exitHandOverNote->requester_id, $exitHandOverNote->created_by, $exitHandOverNote->employee->user->id]);
    }
}
