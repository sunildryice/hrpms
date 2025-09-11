<?php

namespace Modules\GoodRequest\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\GoodRequest\Models\GoodRequestAsset;
use Modules\Inventory\Repositories\AssetRepository;

class GoodRequestAssetRepository extends Repository
{
    public function __construct(
        protected AssetRepository $assets,
        GoodRequestAsset $goodRequestAsset
    ) {
        $this->model = $goodRequestAsset;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $goodRequestAsset = $this->model->with('asset')->find($id);

            if ($inputs['handover_status_id'] == config('constant.APPROVED_STATUS')) {
                $this->assets->update($goodRequestAsset->asset->id, [
                    'status' => config('constant.ASSET_ON_STORE'),
                    'assigned_user_id' => null,
                    // 'assigned_office_id' => null,
                    'assigned_department_id' => null,
                ]);
                $goodRequestAsset->asset->inventoryItem->decrement('assigned_quantity');
                $inputs['status'] = config('constant.ASSET_ON_STORE');
            }

            $goodRequestAsset->update($inputs);
            $goodRequestAsset->logs()->create($inputs);
            $goodRequestAsset->asset->update([
                'assigned_user_id' => null,
            ]);
            $goodRequestAsset->asset->assetAssignLogs()->create([
                'assigned_office_id' => $goodRequestAsset->asset->assigned_office_id,
                'assigned_department_id' => $goodRequestAsset->asset->assigned_department_id,
                'condition_id' => $goodRequestAsset->asset->latestConditionLog->condition_id,
                'remarks' => 'Asset Handover',
                'created_by' => $inputs['user_id'],
            ]);
            DB::commit();

            return $goodRequestAsset;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $goodRequestAsset = $this->model->findOrFail($id);
            $inputs['handover_status_id'] = config('constant.SUBMITTED_STATUS');
            $inputs['reviewer_id'] = $inputs['approver_id'];
            $goodRequestAsset->update($inputs);
            $goodRequestAsset->logs()->create($inputs);
            DB::commit();

            return $goodRequestAsset;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $goodRequestAsset = $this->model->find($id);
            $goodRequestAsset->fill($inputs)->save();
            $forwardInputs = [
                'user_id' => $inputs['updated_by'],
                'log_remarks' => $inputs['log_remarks'] ?: 'Asset handover is submitted.',
                'original_user_id' => $inputs['original_user_id'],
                'approver_id' => $inputs['approver_id'],
            ];
            $goodRequestAsset = $this->forward($goodRequestAsset->id, $forwardInputs);

            DB::commit();

            return $goodRequestAsset;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }
}
