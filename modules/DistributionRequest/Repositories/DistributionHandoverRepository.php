<?php

namespace Modules\DistributionRequest\Repositories;

use App\Repositories\Repository;
use Modules\DistributionRequest\Models\DistributionHandover;

use DB;
use Modules\Master\Repositories\FiscalYearRepository;

class DistributionHandoverRepository extends Repository
{
    public function __construct(
        DistributionHandover $distributionHandover,
        FiscalYearRepository $fiscalYears
    )
    {
        $this->model = $distributionHandover;
        $this->fiscalYears = $fiscalYears;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->with(['fiscalYear', 'status', 'projectCode', 'district'])
                    ->select(['*'])
                    ->whereStatusId(config('constant.APPROVED_STATUS'))
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                    });
            }
        }

        return $this->model
            ->with(['fiscalYear', 'status', 'projectCode', 'district'])
            ->select(['*'])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->whereStatusId(config('constant.APPROVED_STATUS'));
    }

    public function getReceived()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->with(['fiscalYear', 'status', 'projectCode', 'district'])
                    ->select(['*'])
                    ->whereIn('status_id',[config('constant.RECEIVED_STATUS'), config('constant.DISTRIBUTED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.RECEIVED_STATUS')]);
                    });
            }
        }

        return $this->model
            ->with(['fiscalYear', 'status', 'projectCode', 'district'])
            ->select(['*'])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->whereIn('status_id',[config('constant.RECEIVED_STATUS'), config('constant.DISTRIBUTED_STATUS')]);
    }

    public function generateDistributionRequestNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'distribution_handover_number'])
                ->where('fiscal_year_id', $fiscalYearId)
                ->max('distribution_handover_number') + 1;
        return $max;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $distributionRequest = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
            }

            $distributionRequest->update($inputs);
            $distributionRequest->logs()->create($inputs);
            DB::commit();
            return $distributionRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function createHandover($distributionRequest, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $inputs['district_id'] = $distributionRequest->district_id;
            $inputs['project_code_id'] = $distributionRequest->project_code_id;
            $inputs['health_facility_name'] = $distributionRequest->getHealthFacility();
            $inputs['total_amount'] = $distributionRequest->total_amount;
            $inputs['fiscal_year_id'] = $distributionRequest->fiscal_year_id;
            $inputs['office_id'] = $distributionRequest->office_id;
            $handoverItems = [];
            foreach ($distributionRequest->distributionRequestItems as $distributionRequestItem) {

                $handoverItem = [
                    'distribution_request_item_id' => $distributionRequestItem->id,
                    'activity_code_id' => $distributionRequestItem->activity_code_id,
                    'account_code_id' => $distributionRequestItem->account_code_id,
                    'donor_code_id' => $distributionRequestItem->donor_code_id,
                    'inventory_item_id' => $distributionRequestItem->inventory_item_id,
                    'item_id' => $distributionRequestItem->item_id,
                    'unit_id' => $distributionRequestItem->unit_id,
                    'specification' => $distributionRequestItem->specification,
                    'quantity' => $distributionRequestItem->quantity,
                    'unit_price' => $distributionRequestItem->unit_price,
                    'total_amount' => $distributionRequestItem->total_amount,
                    'vat_amount' => $distributionRequestItem->vat_amount,
                    'net_amount' => $distributionRequestItem->net_amount,
                ];
                $handoverItems[] = $handoverItem;
            }

            if (count($handoverItems)) {
                $handover = $distributionRequest->distributionHandover()->create($inputs);
                $handover->distributionHandoverItems()->createMany($handoverItems);
                DB::commit();
                return $handover;
            }
            return false;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $distributionHandover = $this->model->findOrFail($id);
            $distributionHandover->logs()->delete();
            $distributionHandover->distributionHandoverItems()->delete();
            $distributionHandover->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $distributionHandover = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            if (!$distributionHandover->distribution_handover_number) {
                $inputs['prefix'] = 'DH';
                $inputs['distribution_handover_number'] = $this->generateDistributionRequestNumber($distributionHandover->fiscal_year_id);
            }
            $distributionHandover->update($inputs);
            $distributionHandover->logs()->create($inputs);
            DB::commit();
            return $distributionHandover;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function distribute($id,$inputs)
    {
        DB::beginTransaction();
        try{
            $distributionHandover = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.DISTRIBUTED_STATUS');
            $distributionHandover->update($inputs);
            $inputs['log_remarks'] = 'Distribution handover is distributed.';
            $distributionHandover->logs()->create($inputs);
            DB::commit();
            return $distributionHandover;
        }catch(\Illuminate\Database\QueryException $e){
            DB::rollBack();
            return false;
        }

    }
    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $distributionHandover = $this->model->find($id);
            $distributionHandover->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Distribution handover is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $distributionHandover = $this->forward($distributionHandover->id, $forwardInputs);
            }
            DB::commit();
            return $distributionHandover;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function receive($id, $inputs)
    {
        DB::beginTransaction();
        try {
            
            $distributionHandover = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.RECEIVED_STATUS');
            $distributionHandover->update($inputs);
            if($inputs['btn'] == 'submit'){
                $distributionHandover = $this->distribute($id, $inputs);
            }else if(!$distributionHandover->receivedLog) {
                $inputs['log_remarks'] = 'Distribution handover is received.';
                $distributionHandover->logs()->create($inputs);
            }
            DB::commit();
            return $distributionHandover;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
