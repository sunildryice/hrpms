<?php

namespace Modules\TransportationBill\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\TransportationBill\Models\TransportationBill;
use Modules\Privilege\Models\User;

class TransportationBillPolicy
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
     * Determine if the given fund request can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TransportationBill\Models\TransportationBill  $transportationBill
     * @return bool
     */
    public function approve(User $user, TransportationBill $transportationBill)
    {
        return ($transportationBill->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $transportationBill->reviewer_id) ||
            ($transportationBill->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $transportationBill->approver_id);
    }

    /**
     * Determine if the given fund request can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TransportationBill\Models\TransportationBill  $transportationBill
     * @return bool
     */
    public function delete(User $user, TransportationBill $transportationBill)
    {
        return in_array($transportationBill->status_id, [config('constant.CREATED_STATUS')]) &&
            in_array($user->id, [$transportationBill->requester_id, $transportationBill->created_by]);
    }

    /**
     * Determine if the given fund request can be submitted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TransportationBill\Models\TransportationBill  $transportationBill
     * @return bool
     */
    public function submit(User $user, TransportationBill $transportationBill)
    {
        return in_array($transportationBill->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$transportationBill->requester_id, $transportationBill->created_by]) &&
            $transportationBill->transportationBillDetails->count();
    }

    /**
     * Determine if the given fund request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TransportationBill\Models\TransportationBill  $transportationBill
     * @return bool
     */
    public function update(User $user, TransportationBill $transportationBill)
    {
        return in_array($transportationBill->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$transportationBill->created_by]);
    }

    /**
     * Determine if the given approved fund request can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TransportationBill\Models\TransportationBill  $transportationBill
     * @return bool
     */
    public function viewApproved(User $user, TransportationBill $transportationBill)
    {
        return in_array($transportationBill->status_id, [config('constant.APPROVED_STATUS'), config('constant.RECEIVED_STATUS')]);
    }
}
