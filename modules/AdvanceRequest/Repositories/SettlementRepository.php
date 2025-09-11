<?php

namespace Modules\AdvanceRequest\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\AdvanceRequest\Models\AdvanceRequest;
use Modules\AdvanceRequest\Models\Settlement;

class SettlementRepository extends Repository
{
    public function __construct(
        AdvanceRequest $advanceRequest,
        Settlement $settlement
    ) {
        $this->model = $settlement;
        $this->advanceRequest = $advanceRequest;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();
        $query = $this->model->with(['status', 'advanceRequest'])
            ->select(['*'])
            ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')]);
        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $query->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')]);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return $query->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPaid()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();
        $query = $this->model->with(['status', 'advanceRequest'])
            ->select(['*'])
            ->whereIn('status_id', [config('constant.PAID_STATUS')]);
        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $query->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.PAID_STATUS')]);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return $query->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function generateSettlementNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'settlement_number'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->max('settlement_number') + 1;
        return $max;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $settlement = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['recommender_id'] = $settlement->approver_id;
            }
            $settlement->update($inputs);
            $settlement->logs()->create($inputs);
            DB::commit();
            return $settlement;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function pay($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $settlement = $this->model->find($id);
            $inputs['log_remarks'] = $inputs['payment_remarks'];
            $inputs['status_id'] = config('constant.PAID_STATUS');
            $settlement->update($inputs);
            $settlement->logs()->create($inputs);
            DB::commit();
            return $settlement;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $advanceRequest = $this->advanceRequest->find($inputs['advance_request_id']);
            $settlement = $this->model->create($inputs);
            $expenseInputs = [];
            foreach ($advanceRequest->advanceRequestDetails as $detail) {
                $expenseInputs[] = [
                    'advance_request_detail_id' => $detail->id,
                    'activity_code_id' => $detail->activity_code_id,
                    'account_code_id' => $detail->account_code_id,
                    'donor_code_id' => $detail->donor_code_id,
                    'narration' => $detail->description,
                    'created_by' => $inputs['created_by'],
                ];
            }
            $settlement->settlementExpenses()->createMany($expenseInputs);
            DB::commit();
            return $settlement;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $settlement = $this->model->findOrFail($id);
            $settlement->settlementExpenses()->delete();
            $settlement->logs()->delete();
            $settlement->delete();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $settlement = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            if (!$settlement->settlement_number) {
                $inputs['prefix'] = 'AD-ST';
                $inputs['settlement_number'] = $this->generateSettlementNumber($settlement->fiscal_year_id);
            }
            $settlement->update($inputs);
            $settlement->logs()->create($inputs);
            DB::commit();
            return $settlement;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function review($id, $inputs)
    {
        DB::beginTransaction();
        try {
            if (!isset($inputs['verifier_id']) && $inputs['status_id'] == config('constant.VERIFIED_STATUS')) {
                $inputs['status_id'] = config('constant.VERIFIED2_STATUS');
            }
            $settlement = $this->model->find($id);
            $settlement->update($inputs);
            $settlement->logs()->create($inputs);
            DB::commit();
            return $settlement;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function verify($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $settlement = $this->model->find($id);
            $settlement->update($inputs);
            $settlement->logs()->create($inputs);
            DB::commit();
            return $settlement;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $settlement = $this->model->find($id);
            $settlement->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Settlement request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $settlement = $this->forward($settlement->id, $forwardInputs);
            }
            DB::commit();
            return $settlement;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $settlement = $this->model->findOrFail($id);
            $settlement->update($inputs);
            $settlement->logs()->create($inputs);
            DB::commit();
            return $settlement;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }

    }
}
