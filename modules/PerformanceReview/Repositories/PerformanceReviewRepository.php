<?php

namespace Modules\PerformanceReview\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\PerformanceReview\Models\PerformanceReview;

class PerformanceReviewRepository extends Repository
{
    public function __construct(PerformanceReview $performanceReview,
    FiscalYearRepository $fiscalYear,
    PerformanceReviewKeyGoalRepository $performanceReviewKeyGoal,
    PerformanceReviewAnswerRepository $performanceReviewAnswers
    )
    {
        $this->model = $performanceReview;
        $this->fiscalYear = $fiscalYear;
        $this->performanceReviewKeyGoal = $performanceReviewKeyGoal;
        $this->performanceReviewAnswers = $performanceReviewAnswers;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = config('constant.CREATED_STATUS');
            $inputs['created_by'] = auth()->user()->id;
            $performanceReview = $this->model->create($inputs);
            $performanceReview->logs()->create([
                'user_id'       => auth()->user()->id,
                'log_remarks'   => 'Performance review created.',
                'status_id'     => config('constant.CREATED_STATUS')
            ]);
            if($inputs['review_type_id'] == config('constant.KEY_GOALS_REVIEW')) {
                $this->createKeyGoalsandDevelopmentPlans($performanceReview->id);
            }
            DB::commit();
            return $performanceReview;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $performanceReview = $this->model->findOrFail($id);
            $performanceReview->fill($inputs)->save();
            DB::commit();
            return $performanceReview;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $performanceReview = $this->model->findOrFail($id);
            $performanceReview->logs()->delete();
            $performanceReview->answers()->delete();
            $performanceReview->keyGoals()->delete();
            $performanceReview->delete();
            DB::commit();
            return $performanceReview;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function verify($performanceReviewId, $inputs)
    {
        DB::beginTransaction();
        try {
            $performanceReview = $this->model->findOrFail($performanceReviewId);
            $performanceReview->update($inputs);
            $performanceReview->logs()->create($inputs);
            DB::commit();
            return $performanceReview;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function recommend($performanceReviewId, $inputs)
    {
        DB::beginTransaction();
        try {
            $performanceReview = $this->model->findOrFail($performanceReviewId);
            $performanceReview->update($inputs);
            $performanceReview->logs()->create($inputs);
            DB::commit();
            return $performanceReview;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }


    public function approve($performanceReviewId, $inputs)
    {
        DB::beginTransaction();
        try {
            $performanceReview = $this->model->findOrFail($performanceReviewId);

            if ($performanceReview->review_type_id == config('constant.ANNUAL_REVIEW')) {
                $inputs['final_per_date'] = now();
            } elseif ($performanceReview->review_type_id == config('constant.MID_TERM_REVIEW')) {
                $inputs['mid_term_per_date'] = now();
            } elseif ($performanceReview->review_type_id == config('constant.KEY_GOALS_REVIEW')) {
                $inputs['goal_setting_date'] = now();
            }

            $performanceReview->update($inputs);
            $performanceReview->logs()->create($inputs);
            DB::commit();
            return $performanceReview;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function getPendingPerformanceReview($authUser)
    {
        return $this->model->where('requester_id', '=', $authUser->id)
                                ->whereIn('status_id',[config('constant.CREATED_STATUS'),config('constant.RETURNED_STATUS')])
                                ->orderBy('deadline_date', 'desc')->get();
    }

    public function getPerformanceReviewsForApproval($authUser)
    {
        return  $this->model
            ->whereIn('status_id',[ config('constant.RECOMMENDED_STATUS'),config('constant.SUBMITTED_STATUS')])
            ->where(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->orwhere('reviewer_id', $authUser->id);
            })
            ->get();
    }

    public function createKeyGoalsandDevelopmentPlans($performanceReviewId)
    {
        $perfomancereview = $this->model->findOrFail($performanceReviewId);

        $previousFY = $this->fiscalYear->where('title','=', $perfomancereview->fiscalYear->title - 1)->first();

        $previousPerformanceReview = $this->model->where('employee_id', $perfomancereview->employee_id)
                                                ->where('review_type_id', config('constant.ANNUAL_REVIEW'))
                                                ->where('fiscal_year_id', $previousFY->id)
                                                ->first();
        $previousKeyGoalReview = $this->model->where('employee_id', $perfomancereview->employee_id)
                                                ->where('review_type_id', config('constant.KEY_GOALS_REVIEW'))
                                                ->where('fiscal_year_id', $previousFY->id)
                                                ->first();

        if ($previousPerformanceReview) {
            foreach ($previousPerformanceReview->keyGoals->where('type', '=', 'future') as $keyGoal) {
                $inputs = array(
                    'performance_review_id'     => $performanceReviewId,
                    'title'                     => $keyGoal->title,
                    'description_employee'      => $keyGoal->description_employee,
                    'description_supervisor'    => $keyGoal->description_supervisor,
                    'type'                      => 'current',
                    'created_by'                => $previousPerformanceReview->employee->user->id
                );
                $this->performanceReviewKeyGoal->createFromPrevious($inputs);
            }
        }

        if($previousKeyGoalReview){
            $professionalDevelopmentPlan = $previousKeyGoalReview->getAnswer(10);
            $this->performanceReviewAnswers->create([
                'performance_review_id' => $perfomancereview->id,
                'question_id'           => 10,
                'answer'                => $professionalDevelopmentPlan
            ]);
        }

    }
}
