<?php

namespace Modules\FundRequest\Repositories;

use App\Repositories\Repository;
use Modules\FundRequest\Models\FundRequestActivity;

use DB;

class FundRequestActivityRepository extends Repository
{
    public function __construct(
        FundRequestRepository $fundRequests,
        FundRequestActivity   $fundRequestActivity
    )
    {
        $this->fundRequests = $fundRequests;
        $this->model = $fundRequestActivity;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['variance_budget_amount'] = $inputs['budget_amount'] - $inputs['estimated_amount'];
            $inputs['variance_target_unit'] = $inputs['dip_target_unit'] - $inputs['project_target_unit'];
            $fundRequestActivity = $this->model->create($inputs);
            $this->fundRequests->updateTotalAmount($fundRequestActivity->fund_request_id);
            DB::commit();
            return $fundRequestActivity;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $fundRequestActivity = $this->model->findOrFail($id);
            $fundRequestActivity->delete();
            $this->fundRequests->updateTotalAmount($fundRequestActivity->fund_request_id);
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['variance_budget_amount'] = $inputs['budget_amount'] - $inputs['estimated_amount'];
            $inputs['variance_target_unit'] = $inputs['dip_target_unit'] - $inputs['project_target_unit'];
            $fundRequestActivity = $this->model->findOrFail($id);
            $fundRequestActivity->fill($inputs)->save();
            $this->fundRequests->updateTotalAmount($fundRequestActivity->fund_request_id);
            DB::commit();
            return $fundRequestActivity;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function getFundForOffice($activityId, $officeId, $year, $month)
    {
        return $this->model->whereHas('fundRequest', function($q) use($officeId, $year, $month) {
                                $q->where('office_id', $officeId)
                                ->where('year', $year)
                                ->where('month', $month)
                                ->where('status_id', config('constant.APPROVED_STATUS'));
                            })
                            ->where('activity_code_id', $activityId)
                            ->select('estimated_amount')
                            ->sum('estimated_amount');
    }

    public function getFund($year, $month)
    {
        return $this->model->whereHas('fundRequest', function($q) use($year, $month) {
                                $q->where('year', $year)
                                ->where('month', $month)
                                ->where('status_id', config('constant.APPROVED_STATUS'));
                            })
                            ->with('fundRequest')
                            ->get();
    }
}
