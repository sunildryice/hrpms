<?php

namespace Modules\Inventory\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Inventory\Models\InventoryItem;

class InventoryItemRepository extends Repository
{
    public function __construct(
        AssetRepository $assets,
        InventoryItem $inventoryItem
    ) {
        $this->assets = $assets;
        $this->model = $inventoryItem;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $vatAmount = 0;
            $totalPrice = $inputs['total_price'];
            if ($inputs['vat_flag']) {
                $vatPercentage = config('constant.VAT_PERCENTAGE');
                $vatAmount = $totalPrice * $vatPercentage / 100;
            }
            $inputs['vat_amount'] = $vatAmount;
            $inputs['total_amount'] = $totalPrice + $vatAmount;
            $inputs['batch_number'] = $this->getBatchNumber($inputs['item_id'], $inputs['fiscal_year_id']);
            $inventory = $this->model->create($inputs);

            if ($inputs['asset_flag']) {
                $this->assets->generateAssets($inventory);
            }
            DB::commit();

            return $inventory;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $inventory = $this->model->findOrFail($id);
            foreach ($inventory->assets as $asset) {
                $asset->assetConditionLogs()->delete();
                foreach($asset->goodRequestAsset as $goodRequestAsset) {
                    $goodRequestAsset->logs()->delete();
                }
                $asset->goodRequestAsset()->delete();
            }
            $inventory->assets()->delete();
            $inventory->delete();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function getBatchNumber($itemId, $fiscalYearId)
    {
        $max = $this->model->where('item_id', $itemId)
            ->where('fiscal_year_id', $fiscalYearId)
            ->max('batch_number') + 1;

        return $max;
    }
}
