<?php

namespace Modules\ProbationaryReview\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Privilege\Models\User;
use Modules\ProbationaryReview\Models\ProbationaryReview;

class ProbationaryReviewPolicy
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
     * Determine if the given probation review can be approved by the user.
     *
     * @return bool
     */
    public function approve(User $user, ProbationaryReview $probationaryReview)
    {
        return in_array($probationaryReview->status_id, [config('constant.VERIFIED2_STATUS')]) && in_array($user->id, [$probationaryReview->approver_id]);
    }

    /**
     * Determine if the given probation review can be deleted by the user.
     *
     * @return bool
     */
    public function delete(User $user, ProbationaryReview $probationaryReview)
    {
        return in_array($probationaryReview->status_id, [1]);
    }

    /**
     * Determine if the given probation review can be updated by the (employee) user.
     *
     * @return bool
     */
    public function employeeRemarks(User $user, ProbationaryReview $probationaryReview)
    {
        return in_array($probationaryReview->status_id, [15]) && in_array($user->employee_id, [$probationaryReview->employee_id]);
    }

    public function sendTo(User $user, ProbationaryReview $probationaryReview)
    {
        return $user->id == $probationaryReview->created_by && in_array($probationaryReview->status_id, [11]);
    }

    /**
     * Determine if the given Probationary Review can be printed by the user.
     *
     * @return bool
     */
    public function print(User $user, ProbationaryReview $probationaryReview)
    {
        return $probationaryReview->status_id == 6 && (in_array($user->id, [$probationaryReview->created_by,
            $probationaryReview->reviewer_id, $probationaryReview->review_id, $probationaryReview->approver_id]) ||
                $user->can('view-approved-probation-review-request'));
    }

    /**
     * Determine if the given probation review can be submitted by the user.
     *
     * @return bool
     */
    public function review(User $user, ProbationaryReview $probationaryReview)
    {
        return in_array($probationaryReview->status_id, [3, 4]) && in_array($user->id, [$probationaryReview->reviewer_id]);
    }

    /**
     * Determine if the given probation review can be submitted by the user.
     *
     * @return bool
     */
    public function recommend(User $user, ProbationaryReview $probationaryReview)
    {
        return in_array($probationaryReview->status_id, [4]) && in_array($user->id, [$probationaryReview->reviewer_id])
            && $probationaryReview->employee_remarks;
    }

    /**
     * Determine if the given probation review can be updated by the user.
     *
     * @return bool
     */
    public function update(User $user, ProbationaryReview $probationaryReview)
    {
        return in_array($probationaryReview->status_id, [1, 2]);
    }

    /**
     * Determine if the given probation review can be updated by the user.
     *
     * @return bool
     */
    public function view(User $user, ProbationaryReview $probationaryReview)
    {
        return ! in_array($probationaryReview->status_id, [1, 2]) &&
            (in_array($user->id, [$probationaryReview->created_by, $probationaryReview->approver_id, $probationaryReview->reviewer_id])
            || $user->can('view-approved-probation-review-request'));
    }
}
