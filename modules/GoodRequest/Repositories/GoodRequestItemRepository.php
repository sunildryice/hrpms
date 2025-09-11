<?php

namespace Modules\GoodRequest\Repositories;

use App\Repositories\Repository;
use Modules\GoodRequest\Models\GoodRequestAsset;
use Modules\GoodRequest\Models\GoodRequestItem;

use DB;

class GoodRequestItemRepository extends Repository
{
    public function __construct(
        GoodRequestAsset $goodRequestAssets,
        GoodRequestItem $goodRequestItem
    )
    {
        $this->goodRequestAssets = $goodRequestAssets;
        $this->model = $goodRequestItem;
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $goodRequestItem = $this->model->findOrFail($id);
            if(array_key_exists('assigned_asset_ids', $inputs)){
                foreach($inputs['assigned_asset_ids'] as $goodRequestAssetId){
                    $assetInputs = [
                        'good_request_id'=>$goodRequestItem->good_request_id,
                        'good_request_item_id'=>$goodRequestItem->id,
                        'assign_asset_id'=>$goodRequestAssetId,
                        'handover_status_id'=>1,
                    ];
                    $this->goodRequestAssets->create($assetInputs);
                }
                $inputs['assigned_quantity'] = count($inputs['assigned_asset_ids']);
            }
            $goodRequestItem->fill($inputs)->save();
            DB::commit();
            return $goodRequestItem;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
