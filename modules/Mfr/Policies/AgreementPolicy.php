<?php

namespace Modules\Mfr\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Mfr\Models\Agreement;
use Modules\Privilege\Models\User;

class AgreementPolicy
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

    public function create(User $user, Agreement $agreement)
    {
        $incompleteTransaction = $agreement->transactions()->whereNot('status_id', config('constant.APPROVED_STATUS'))->first();
        if($incompleteTransaction){
            $this->deny('You have incomplete transaction');
        }
        return true;
    }

    public function delete(User $user, Agreement $agreement)
    {
        return $user->id == $agreement->created_by && $agreement->transactions()->count() < 1;
    }

    public function print(User $user, Agreement $agreement)
    {
        return $agreement->transactions()->select('id')->where('status_id', config('constant.APPROVED_STATUS'))->count();
    }

    public function review(User $user, Agreement $agreement)
    {
        return $agreement->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $agreement->reviewer_id;
    }

    public function approve(User $user, Agreement $agreement)
    {
        return $agreement->status_id == config('constant.VERIFIED_STATUS') && $user->id == $agreement->approver_id;
    }

    public function amend(User $user, Agreement $agreement)
    {
        return in_array($agreement->status_id, [config('constant.APPROVED_STATUS')]) &&
            in_array($user->id, [$agreement->requester_id]);
    }
}
