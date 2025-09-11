<?php

namespace Modules\EventCompletion\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\EventCompletion\Models\EventCompletion;
use Modules\Privilege\Models\User;

class EventCompletionPolicy
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
     * Determine if the given event completion report can be approved by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\EventCompletion\Models\EventCompletion $eventCompletion
     * @return bool
     */
    public function approve(User $user, EventCompletion $eventCompletion)
    {
        return $user->id == $eventCompletion->approver_id &&
            in_array($eventCompletion->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')]);
    }

    /**
     * Determine if the given ECR cancelled by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EventCompletion\Models\EventCompletion  $eventCompletion
     * @return bool
     */
    public function cancel(User $user, EventCompletion $eventCompletion)
    {
        return in_array($eventCompletion->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')]) &&
            in_array($user->id, [$eventCompletion->requester_id, $eventCompletion->created_by]);
            
    }


    /**
     * Determine if the given event completion report can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\EventCompletion\Models\EventCompletion $eventCompletion
     * @return bool
     */
    public function delete(User $user, EventCompletion $eventCompletion)
    {
        return in_array($eventCompletion->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) && 
            in_array($user->id, [$eventCompletion->requester_id, $eventCompletion->created_by]);
    }

    /**
     * Determine if the given event completion can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EventCompletion\Models\EventCompletion $eventCompletion
     * @return bool
     */
    public function print(User $user, EventCompletion $eventCompletion)
    {
        return in_array($eventCompletion->status_id, [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]);
    }


    /**
     * Determine if the given event completion report  can be submitted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\EventCompletion\Models\EventCompletion $eventCompletion
     * @return bool
     */
    public function submit(User $user, EventCompletion $eventCompletion)
    {
        return in_array($eventCompletion->status_id, [1, 2]) && in_array($user->id, [$eventCompletion->requester_id, $eventCompletion->created_by]);
    }

    /**
     * Determine if the given evnet completion can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\EventCompletion\Models\EventCompletion $eventCompletion
     * @return bool
     */
    public function update(User $user, EventCompletion $eventCompletion)
    {
        return in_array($eventCompletion->status_id, [1, 2]) && in_array($user->id, [$eventCompletion->requester_id, $eventCompletion->created_by]);
    }

    /**
     * Determine if the given event completion can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\EventCompletion\Models\EventCompletion $eventCompletion
     * @return bool
     */
    public function view(User $user, EventCompletion $eventCompletion)
    {
        return in_array($user->id, [$eventCompletion->requester_id, $eventCompletion->created_by]);
    }
}
