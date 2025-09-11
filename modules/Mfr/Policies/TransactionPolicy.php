<?php

namespace Modules\Mfr\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Mfr\Models\Transaction;
use Modules\Privilege\Models\User;

class TransactionPolicy
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

    public function delete(User $user, Transaction $transaction)
    {
        return in_array($transaction->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$transaction->requester_id, $transaction->created_by]);
    }

    public function print(User $user, Transaction $transaction)
    {
        return true;
        return in_array($transaction->status_id, [config('constant.APPROVED_STATUS')]);
    }

    public function review(User $user, Transaction $transaction)
    {
        return $transaction->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $transaction->reviewer_id;
    }

    public function verify(User $user, Transaction $transaction)
    {
        return $transaction->status_id == config('constant.VERIFIED_STATUS') && $user->id == $transaction->verifier_id;
    }

    public function recommend(User $user, Transaction $transaction)
    {
        return $transaction->status_id == config('constant.VERIFIED2_STATUS') && $user->id == $transaction->recommender_id;
    }

    public function approve(User $user, Transaction $transaction)
    {
        return $transaction->status_id == config('constant.RECOMMENDED_STATUS') && $user->id == $transaction->approver_id;
    }

    public function amend(User $user, Transaction $transaction)
    {
        return in_array($transaction->status_id, [config('constant.APPROVED_STATUS')]) &&
            in_array($user->id, [$transaction->requester_id]);
    }

    public function update(User $user, Transaction $transaction)
    {
        return in_array($transaction->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$transaction->requester_id, $transaction->created_by]);
    }
}
