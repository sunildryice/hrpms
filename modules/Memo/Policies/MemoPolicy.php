<?php

namespace Modules\Memo\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Memo\Models\Memo;
use Modules\Privilege\Models\User;

class MemoPolicy
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
     * Determine if the given memo can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Memo\Models\Memo  $memo
     * @return bool
     */
    public function approve(User $user, Memo $memo)
    {
        return ($memo->status_id == 3 && in_array($user->id, $memo->getThroughUserId())) || (in_array($memo->status_id, [3,4])  && in_array($user->id, $memo->getToUserId()));
    }

    /**
     * Determine if the given memo can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Memo\Models\Memo  $memo
     * @return bool
     */
    public function print(User $user, Memo $memo)
    {
        $userIds = $memo->logs->pluck('user_id')->toArray();
        return (in_array($memo->status_id, [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]) && in_array($user->id, $userIds));
    }


    /**
     * Determine if the given memo can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Memo\Models\Memo  $memo
     * @return bool
     */
    public function delete(User $user, Memo  $memo)
    {
        return $memo->status_id ==1 && in_array($user->id, [$memo->created_by]);
    }

    /**
     * Determine if the given memo can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\Memo\Models\Memo $memo
     * @return bool
     */
    public function update(User $user, Memo  $memo)
    {
        return in_array($memo->status_id, [1, 2]) && in_array($user->id, [ $memo->created_by]);
    }

    public function amend(User $user, Memo  $memo)
    {
        return in_array($memo->status_id, [config('constant.APPROVED_STATUS')])
                    && in_array($user->id, [ $memo->created_by]);
    }
}
