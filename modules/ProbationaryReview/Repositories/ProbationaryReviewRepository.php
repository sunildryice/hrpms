<?php
namespace Modules\ProbationaryReview\Repositories;

use App\Repositories\Repository;
use Modules\ProbationaryReview\Models\ProbationaryReview;
use DB;

class ProbationaryReviewRepository extends Repository
{
    public function __construct(ProbationaryReview $probationaryReview)
    {
        $this->model = $probationaryReview;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                ->select(['*'])
                ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
                ->where(function ($q) use ($authUser, $accessibleOfficeIds) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->orwhere('approver_id', $authUser->id);
                    $q->orwhere('created_by', $authUser->id);
                    $q->orWhereHas('employee.latestTenure', function ($q) use ($accessibleOfficeIds) {
                        $q->whereIn('office_id', $accessibleOfficeIds);
                        $q->orWhere(function ($q) {
                            $q->whereNull('office_id');
                            $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                        });
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get();
            }
        }

        return $this->model
        ->select(['*'])
        ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
        ->where(function ($q) use ($authUser, $accessibleOfficeIds) {
            $q->where('reviewer_id', $authUser->id);
            $q->orwhere('approver_id', $authUser->id);
            $q->orwhere('created_by', $authUser->id);
            $q->orWhereHas('employee.latestTenure', function ($q) use ($accessibleOfficeIds) {
                $q->whereIn('office_id', $accessibleOfficeIds);
            });
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }


    public function addApprover($inputs, $id)
    {
        DB::beginTransaction();
        try {
            $probationaryReview = $this->model->find($id);
            $probationaryReview->fill($inputs)->save();
            DB::commit();
            return $probationaryReview;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function approve($inputs, $id)
    {
        DB::beginTransaction();
        try {
            $probationaryReview = $this->model->find($id);
            $probationaryReview->fill($inputs)->save();
            $probationaryReview->logs()->create([
                'log_remarks' => $inputs['director_recommendation'],
                'status_id' => $inputs['status_id'],
                'user_id' => $inputs['updated_by'],
                'original_user_id' => $inputs['original_user_id'],
            ]);
            DB::commit();
            return $probationaryReview;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function addEmployeeRemarks($inputs, $id)
    {
        DB::beginTransaction();
        try {
            $probationaryReview = $this->model->find($id);
            if($inputs['btn'] == 'submit'){
                $inputs['status_id'] = 4;
                $probationaryReview->logs()->create([
                    'log_remarks' => 'Employee comments added.',
                    'status_id' => $inputs['status_id'],
                    'user_id' => $inputs['updated_by'],
                    'original_user_id' => $inputs['original_user_id'],
                ]);
            }
            $probationaryReview->fill($inputs)->save();
            DB::commit();
            return $probationaryReview;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function review($inputs, $id)
    {
       DB::beginTransaction();
       try {
            $probationaryReview = $this->model->find($id);
            if($inputs['btn'] == 'submit'){
                $inputs['status_id'] = 15;
                $probationaryReview->logs()->create([
                    'log_remarks' => 'Probation review request is reviewed and submitted to employee.',
                    'status_id' => $inputs['status_id'],
                    'user_id' => $inputs['updated_by'],
                    'original_user_id' => $inputs['original_user_id'],
                ]);
            }
            $probationaryReview->probationaryReviewIndicator()->delete();
            foreach ($inputs['indicator'] as $key => $value) {
                $probationaryReview->probationaryReviewIndicator()->create([
                    'probationary_review_id'=>$id,
                    'probationary_indicator_id'=>$key,
                    $value => '1',
                ]);
            }
            $probationaryReview->update($inputs);
            DB::commit();
            $probationaryReview = $this->model->find($id);
            return $probationaryReview;
       } catch (\Illuminate\Database\QueryException $e) {
           DB::rollback();
           return false;
       }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $probationaryReview = $this->model->create($inputs);
            $probationaryReview->logs()->create([
                'log_remarks' => 'Probation Review Request is created.',
                'status_id' => $probationaryReview->status_id,
                'user_id' => $probationaryReview->created_by,
                'original_user_id' => $inputs['original_user_id'],
            ]);
            DB::commit();
            return $probationaryReview;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $review = $this->model->findOrFail($id);
            $review->logs()->delete();
            $review->delete();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function recommend($inputs, $id)
    {
        DB::beginTransaction();
        try {
            $probationaryReview = $this->model->find($id);
            $probationaryReview->fill($inputs)->save();
            if($inputs['status_id'] == 15){
                $logRemarks = 'Probation review returned to employee';
            }

            $probationaryReview->logs()->create([
                'log_remarks' => $logRemarks ?? 'Probation review recommended for approval.',
                'status_id' => $inputs['status_id'],
                'user_id' => $inputs['updated_by'],
                'original_user_id' => $inputs['original_user_id'],
            ]);
            DB::commit();
            return $probationaryReview;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $probationaryReview = $this->model->find($id);
            if($inputs['btn'] == 'submit'){
                $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
                $probationaryReview->logs()->create([
                    'log_remarks' => 'Probation Review Request is submitted.',
                    'status_id' => $inputs['status_id'],
                    'user_id' => $inputs['updated_by'],
                    'original_user_id' => $inputs['original_user_id'],
                ]);
            }
            $probationaryReview->fill($inputs)->save();
            DB::commit();
            return $probationaryReview;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
