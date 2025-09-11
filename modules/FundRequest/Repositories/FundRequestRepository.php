<?php

namespace Modules\FundRequest\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\FundRequest\Models\FundRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FundRequestRepository extends Repository
{
    public function __construct(
        FundRequest $fundRequest
    ) {
        $this->model = $fundRequest;
    }

    public function getApproved()
    {
        $authUser = Auth::user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();
        $query = $this->model->with(['fiscalYear', 'status', 'projectCode', 'district', 'requestForOffice', 'office', 'requester', 'approvedLog'])
            ->select(['*'])
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');
        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $query->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                    })->get();
            }
        }

        return $query->whereIn('office_id', $accessibleOfficeIds)->get();
    }

    public function generateFundRequestNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'fund_request_number'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->max('fund_request_number') + 1;

        return $max;
    }

    public function review($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $fundRequest = $this->model->find($id);
            $fundRequest->update($inputs);
            $fundRequest->logs()->create($inputs);
            DB::commit();

            return $fundRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $fundRequest = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['reviewer_id'] = $fundRequest->approver_id;
            }
            $fundRequest->update($inputs);
            $fundRequest->logs()->create($inputs);
            DB::commit();

            return $fundRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $fundRequest = $this->model->create($inputs);
            DB::commit();

            return $fundRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $fundRequest = $this->model->findOrFail($id);

            if ($parentFundRequest = $fundRequest->parentFundRequest) {
                $parentFundRequest->update(['status_id' => config('constant.APPROVED_STATUS')]);
            }

            $fundRequest->logs()->delete();
            $fundRequest->fundRequestActivities()->delete();
            $fundRequest->delete();

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $fundRequest = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            // $inputs['reviewer_id'] = $fundRequest->approver_id;
            if (! $fundRequest->fund_request_number) {
                $inputs['prefix'] = 'FR';
                $inputs['fund_request_number'] = $this->generateFundRequestNumber($fundRequest->fiscal_year_id);
            }
            $fundRequest->update($inputs);
            $fundRequest->logs()->create($inputs);
            DB::commit();

            return $fundRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $fundRequest = $this->model->find($id);
            $fundRequest->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Fund request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $fundRequest = $this->forward($fundRequest->id, $forwardInputs);
            }
            $this->updateTotalAmount($fundRequest->id);
            DB::commit();

            return $fundRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function verify($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $fundRequest = $this->model->find($id);
            if ($inputs['btn'] == 'submit') {
                $fundRequest->fill($inputs)->save();
                $fundRequest->logs()->create($inputs);
            } else {
                unset($inputs['status_id']);
                $fundRequest->fill($inputs)->save();
            }
            $this->updateTotalAmount($fundRequest->id);
            DB::commit();

            return $fundRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function updateTotalAmount($fundRequestId)
    {
        DB::beginTransaction();
        try {
            $fundRequest = $this->model->findOrFail($fundRequestId);
            $requiredAmount = $fundRequest->fundRequestActivities->sum('estimated_amount');
            if ($fundRequest->surplus_deficit == 1) {
                $net_amount = $requiredAmount - $fundRequest->estimated_surplus;
            } else {
                $net_amount = $requiredAmount + $fundRequest->estimated_surplus;
            }
            $updateInputs = [
                'required_amount' => $requiredAmount,
                'net_amount' => $net_amount,
            ];
            $fundRequest->update($updateInputs);
            DB::commit();

            return $fundRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $fundRequest = $this->model->find($id);
            $fundRequest->update(['status_id' => config('constant.AMENDED_STATUS')]);
            $clone = $fundRequest->replicate();
            unset($clone->checker_id);
            unset($clone->certifier_id);
            unset($clone->reviewer_id);
            unset($clone->approver_id);
            $clone->status_id = config('constant.CREATED_STATUS');
            $clone->created_by = $inputs['created_by'];
            $clone->modification_fund_request_id = $fundRequest->id;
            $parentFundRequestId = $fundRequest->modification_fund_request_id ?: $fundRequest->id;
            $clone->modification_number = $this->model->where('modification_fund_request_id', $parentFundRequestId)
                ->max('modification_number') + 1;
            $clone->save();

            foreach ($fundRequest->fundRequestActivities as $activity) {
                unset($activity->id);
                unset($activity->fund_request_id);
                $activityInput = $activity->toArray();
                $activityInput['created_by'] = $inputs['created_by'];
                $clone->fundRequestActivities()->create($activityInput);
            }

            DB::commit();

            return $clone;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function requestCancel($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = config('constant.INIT_CANCEL_STATUS');
            $inputs['user_id'] = auth()->id();
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            $fund = $this->model->find($id);
            $fund->update($inputs);
            $fund->logs()->create($inputs);
            DB::commit();

            return $fund;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function replicate($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $fund = $this->model->find($id);
            $clone = $fund->replicate();
            unset($clone->checker_id);
            unset($clone->certifier_id);
            unset($clone->reviewer_id);
            unset($clone->approver_id);
            unset($clone->attachment);
            unset($clone->prefix);
            unset($clone->fund_request_number);
            unset($clone->modification_number);
            unset($clone->modification_remarks);
            unset($clone->modification_fund_request_id);
            $clone->status_id = config('constant.CREATED_STATUS');
            $clone->created_by = $inputs['created_by'];
            $clone->save();

            foreach ($fund->fundRequestActivities as $activity) {
                unset($activity->id);
                unset($activity->fund_request_id);
                $activityInput = $activity->toArray();
                $activityInput['created_by'] = $inputs['created_by'];
                $clone->fundRequestActivities()->create($activityInput);
            }

            DB::commit();
            return $clone;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
