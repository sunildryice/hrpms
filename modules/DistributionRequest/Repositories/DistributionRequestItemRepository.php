<?php

namespace Modules\DistributionRequest\Repositories;

use App\Repositories\Repository;
use Modules\DistributionRequest\Models\DistributionRequestItem;

use DB;

class DistributionRequestItemRepository extends Repository
{
    public function __construct(
        protected DistributionRequestRepository $distributionRequests,
        DistributionRequestItem       $distributionRequestItem
    )
    {
        $this->model = $distributionRequestItem;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['net_amount'] = $inputs['total_amount'] + $inputs['vat_amount'];
            $distributionRequestItem = $this->model->create($inputs);
            // $distributionRequestItem->inventoryItem->increment('assigned_quantity', $distributionRequestItem->quantity);
            $this->distributionRequests->updateTotalAmount($distributionRequestItem->distribution_request_id);
            DB::commit();
            return $distributionRequestItem;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $distributionRequestItem = $this->model->findOrFail($id);
            // $distributionRequestItem->inventoryItem->decrement('assigned_quantity', $distributionRequestItem->quantity);
            $distributionRequestItem->delete();
            $this->distributionRequests->updateTotalAmount($distributionRequestItem->distribution_request_id);
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
            $distributionRequestItem = $this->model->findOrFail($id);
            // $distributionRequestItem->inventoryItem->decrement('assigned_quantity', $distributionRequestItem->quantity);
            $inputs['net_amount'] = $inputs['total_amount'] + $inputs['vat_amount'];
            $distributionRequestItem->fill($inputs)->save();
            $distributionRequestItem = $this->model->findOrFail($id);
            // $distributionRequestItem->inventoryItem->increment('assigned_quantity', $distributionRequestItem->quantity);
            $this->distributionRequests->updateTotalAmount($distributionRequestItem->distribution_request_id);
            DB::commit();
            return $distributionRequestItem;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
