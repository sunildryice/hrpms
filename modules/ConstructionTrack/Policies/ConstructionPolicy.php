<?php

namespace Modules\ConstructionTrack\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\ConstructionTrack\Models\Construction;
use Modules\ConstructionTrack\Models\ConstructionInstallment;
use Modules\Privilege\Models\User;

class ConstructionPolicy
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
     * Determine if the given advance request can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\ConstructionTrack\Models\Construction  $construction
     * @return bool
     */
    public function approve(User $user, Construction $construction)
    {
        return ($construction->status_id ==3 && $user->id == $construction->reviewer_id) || ($construction->status_id == 4 && $user->id == $construction->approver_id);
    }

      /**
     * Determine if the given approved approve request can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\ConstructionTrack\Models\Construction  $construction
     * @return bool
     */
    public function viewApproved(User $user, Construction $construction)
    {
        return in_array($construction->status_id, [config('constant.APPROVED_STATUS')]);
    }

    /**
     * Determine if the given construction request can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\ConstructionTrack\Models\Construction  $construction
     * @return bool
     */
    public function delete(User $user, Construction $construction)
    {
        $progressCount      = $construction->constructionProgresses()->count();
        $installmentCount   = $construction->constructionInstallments()->count();
        $childrenEmpty      = ($progressCount + $installmentCount) == 0 ? true : false;

        return in_array($construction->status_id, [1]) && $user->can('manage-construction') && $childrenEmpty;
    }

    /**
     * Determine if the given construction request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\ConstructionTrack\Models\Construction  $construction
     * @return bool
     */
    public function update(User $user, Construction $construction)
    {
        // return in_array($construction->status_id, [1]);
        return in_array($construction->status_id, [1,3]) && $user->can('manage-construction');
    }


     /**
     * Determine if the given construction request can be settled by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\ConstructionTrack\Models\Construction  $construction
     * @return bool
     */
    public function settlement(User $user, Construction $construction)
    {
        // return in_array($construction->status_id, [3]);
        return in_array($construction->status_id, [1,3]) && $user->can('manage-settlement');
    }


     /**
     * Determine if the given construction request can be settled by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\ConstructionTrack\Models\Construction  $construction
     * @return bool
     */
    public function hideAction(User $user, Construction $construction)
    {
        return false;
    }

    public function addInstallment(User $user, Construction $construction)
    {
        // return ($construction->constructionInstallments->where('status_id', '!=', config('constant.APPROVED_STATUS'))->count() == 0) && $user->can('manage-construction');
        return $user->can('manage-settlement');
    }

    public function addProgress(User $user, Construction $construction)
    {
        return true;
        // return ($construction->engineer_id == $user->employee?->id);
        // || $user->can('manage-construction');
    }

    public function view(User $user, Construction $construction)
    {
        return $user->can('construction');
    }
}
