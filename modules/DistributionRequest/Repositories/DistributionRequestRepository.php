<?php

namespace Modules\DistributionRequest\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\DistributionRequest\Models\DistributionRequest;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;

class DistributionRequestRepository extends Repository
{
    private $fiscalYears;

    public function __construct(
        DistributionRequest $distributionRequest,
        FiscalYearRepository $fiscalYears,
        PurchaseRequestRepository $purchaseRequests,
    ) {
        $this->model = $distributionRequest;
        $this->fiscalYears = $fiscalYears;
        $this->purchaseRequests = $purchaseRequests;
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
                    })->orderBy('distribution_request_number', 'desc');
            }
        }

        return $this->model
            ->with(['fiscalYear', 'status', 'projectCode', 'district'])
            ->select(['*'])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->orderBy('distribution_request_number', 'desc');
    }

    public function generateDistributionRequestNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'distribution_request_number'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->max('distribution_request_number') + 1;

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

            if ($inputs['status_id'] == config('constant.APPROVED_STATUS')) {
                $distributionRequestItems = $distributionRequest->distributionRequestItems;
                foreach ($distributionRequestItems as $distributionRequestItem) {
                    $distributionRequestItem->inventoryItem->increment('assigned_quantity', $distributionRequestItem->quantity);
                }
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

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $distributionRequest = $this->model->create($inputs);
            if (isset($inputs['purchase_request_ids'])) {

                $purchaseRequests = $this->purchaseRequests
                    ->whereIn('id', $inputs['purchase_request_ids'])
                    // ->with('purchaseRequestItems.purchaseOrderItem.grnItems.inventoryItem')
                    ->get();
                $purchaseRequestsAlt = $this->purchaseRequests
                    ->whereIn('id', $inputs['purchase_request_ids'])
                    ->with('purchaseRequestItems.grnItems.inventoryItem')
                    ->get();
                $inventoryItems = [];
                foreach ($purchaseRequests as $purchaseRequest) {
                    foreach ($purchaseRequest->purchaseRequestItems as $prItem) {
                        foreach ($prItem->purchaseOrderItems as $purchaseOrderItem) { // Compensated for hasmany rel'n
                            foreach ($purchaseOrderItem->grnItems as $grnItem) {
                                $inventoryItem = $grnItem->inventoryItem;
                                if ($inventoryItem?->quantity > $inventoryItem?->assigned_quantity && $inventoryItem?->distribution_type_id == 2) {
                                    $inventoryItems[] = $inventoryItem;
                                }
                            }
                        }
                    }
                }
                foreach ($purchaseRequestsAlt as $purchaseRequest) {
                    foreach ($purchaseRequest->purchaseRequestItems as $prItem) {
                        foreach ($prItem->grnItems as $grnItem) {
                            $inventoryItem = $grnItem->inventoryItem;
                            if ($inventoryItem?->quantity > $inventoryItem?->assigned_quantity && $inventoryItem?->distribution_type_id == 2) {
                                $inventoryItems[] = ($inventoryItem);
                            }
                        }
                    }
                }
                foreach ($inventoryItems as $item) {
                    $item->total_amount = $item->unit_price * $item->quantity;
                    $item->vat_amount = $item->total_amount * config('constant.VAT_PERCENTAGE') / 100;
                    $item->net_amount = $item->total_amount + $item->vat_amount;
                    $item->inventory_item_id = $item->id;
                    $distributionRequest->distributionRequestItems()->create($item->toArray());
                }
                $this->updateTotalAmount($distributionRequest->id);
            }
            DB::commit();

            return $distributionRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $distributionRequest = $this->model->findOrFail($id);
            $distributionRequest->logs()->delete();
            $distributionRequest->distributionRequestItems()->delete();
            $distributionRequest->delete();

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $distributionRequest = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $inputs['reviewer_id'] = $distributionRequest->approver_id;
            if (! $distributionRequest->distribution_request_number) {
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['prefix'] = 'DR';
                $inputs['distribution_request_number'] = $this->generateDistributionRequestNumber($fiscalYear->id);
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

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $distributionRequest = $this->model->find($id);
            $distributionRequest->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Distribution request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $distributionRequest = $this->forward($distributionRequest->id, $forwardInputs);
            }
            $this->updateTotalAmount($distributionRequest->id);
            DB::commit();

            return $distributionRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function updateTotalAmount($distributionRequestId)
    {
        DB::beginTransaction();
        try {
            $distributionRequest = $this->model->findOrFail($distributionRequestId);
            $budgetAmount = $distributionRequest->distributionRequestItems->sum('net_amount');
            $updateInputs = [
                'total_amount' => $budgetAmount,
            ];
            $distributionRequest->update($updateInputs);
            DB::commit();

            return $distributionRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }
}
