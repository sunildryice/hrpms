<?php

namespace Modules\PerformanceReview\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\PerformanceReview\Models\PerformanceReview;
use Modules\Privilege\Models\User;
use Modules\Privilege\Repositories\UserRepository;

class PerformanceReviewPolicy
{
    use HandlesAuthorization;

    private $users;
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->users = app(UserRepository::class);
    }

    public function submit(User $user, PerformanceReview $performanceReview)
    {
        return in_array($performanceReview->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$performanceReview->requester_id]);
    }

    public function review(User $user, PerformanceReview $performanceReview)
    {
        return ($performanceReview->status_id == config('constant.SUBMITTED_STATUS') &&
        $user->id == $performanceReview->reviewer_id);
    }

    public function recommend(User $user, PerformanceReview $performanceReview)
    {
        return ($performanceReview->status_id == config('constant.VERIFIED_STATUS') &&
        $user->id == $performanceReview->recommender_id);
    }

    public function approve(User $user, PerformanceReview $performanceReview)
    {
        return ($performanceReview->status_id == config('constant.RECOMMENDED_STATUS') &&
        $user->id == $performanceReview->approver_id);
    }

    public function delete(User $user, PerformanceReview $performanceReview)
    {
        return (($performanceReview->status_id == config('constant.CREATED_STATUS')) &&
        ($user->id == $performanceReview->created_by)) &&
        $user->can('manage-performance-review');
    }

    public function employeeFill(User $user, PerformanceReview $performanceReview)
    {
        return in_array($performanceReview->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]);
    }

    public function supervisorFill(User $user, PerformanceReview $performanceReview)
    {
        return in_array($performanceReview->status_id, [config('constant.SUBMITTED_STATUS')]);
    }

    public function print(User $user, PerformanceReview $performanceReview)
    {
        return ($performanceReview->status_id == config('constant.APPROVED_STATUS')) &&
        $user->can('manage-performance-review');
    }

    public function edit(User $user, PerformanceReview $performanceReview)
    {
        return $user->can('manage-performance-review');
    }

    public function view(User $user, PerformanceReview $performanceReview)
    {
        $supervisors = $this->users->getSupervisor($performanceReview->employee->user)->map(fn($user) => $user->id)->toArray();
        return in_array($user->id, $supervisors) || $user->can('manage-performance-review');

//        if ($performanceReview->getReviewType() == 'Annual Review') {
//            return $user->can('manage-performance-review');
//        } else {
//            return in_array($user->id, $supervisors);
//        }
    }

    public function managePerformance(User $user, PerformanceReview $performanceReview)
    {
        return $user->can('manage-performance-review');
    }

    public function selfView(User $user, PerformanceReview $performanceReview)
    {
        return $performanceReview->employee->user->id == $user->id;
    }
}
