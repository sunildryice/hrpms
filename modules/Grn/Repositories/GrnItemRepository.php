<?php

namespace Modules\Grn\Repositories;

use App\Repositories\Repository;
use Modules\Grn\Models\GrnItem;

use DB;
use Modules\Inventory\Repositories\AssetRepository;
use Modules\Inventory\Repositories\InventoryItemRepository;
use ReflectionClass;

class GrnItemRepository extends Repository
{
    public function __construct(
        protected AssetRepository $assets,
        protected GrnRepository $grns,
        protected InventoryItemRepository $inventoryItem,
        GrnItem $grnItem,
    )
    {
        $this->assets = $assets;
        $this->grns = $grns;
        $this->model = $grnItem;
        $this->inventoryItem = $inventoryItem;
    }

    public function createInventory($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $grnItem = $this->model->find($id);
            $inputs['batch_number'] = $this->inventoryItem->getBatchNumber($inputs['item_id'], $inputs['fiscal_year_id']);
            $inventory = $grnItem->inventoryItem()->create($inputs);
            if($inputs['asset_flag']) {
                $this->assets->generateAssets($inventory);
            }
            DB::commit();
            return $inventory;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $vatAmount = $tdsAmount = 0;
            $vatAbleAmount = $inputs['total_price'] - $inputs['discount_amount'];
            if($inputs['vat_flag']){
                $vatPercentage = config('constant.VAT_PERCENTAGE');
                $vatTdsPercentage = config('constant.VAT_TDS_PERCENTAGE');

                $vatAmount = $vatAbleAmount * $vatPercentage / 100;
                $tdsAmount = $vatAbleAmount * $vatTdsPercentage / 100;
            }
            $inputs['vat_amount'] = $vatAmount;
            $inputs['tds_amount'] = $tdsAmount;
            $inputs['total_amount'] = $vatAbleAmount+$vatAmount;
            $grnItem = $this->model->create($inputs);
            $this->grns->updateTotalAmount($grnItem->grn_id);
            DB::commit();
            return $grnItem;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $grnItem = $this->model->findOrFail($id);
            $grnItem->delete();
            $this->grns->updateTotalAmount($grnItem->grn_id);
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
            $grnItem = $this->model->findOrFail($id);
            $vatAmount = $tdsAmount = 0;
            $vatAbleAmount = $inputs['total_price'] - $inputs['discount_amount'];

            $check = $grnItem->purchaseOrderItem->vat_amount || $inputs['vat_flag'];
            // $grnitemableType = $grnItem->grnitemable_type;
            // if ($grnitemableType) {
            //     $reflection = new ReflectionClass($grnitemableType);
            //     $modelName = $reflection->getShortName();
            //     if ($modelName == "PurchaseRequestItem") {
            //         $check = $grnItem->vat_amount && $grnItem->vat_amount > 0;
            //     }
            // }
            if($check){
                $vatPercentage = config('constant.VAT_PERCENTAGE');
                $vatTdsPercentage = config('constant.VAT_TDS_PERCENTAGE');

                $vatAmount = $vatAbleAmount * $vatPercentage / 100;
                $tdsAmount = $vatAbleAmount * $vatTdsPercentage / 100;
            }
            $inputs['vat_amount'] = $vatAmount;
            $inputs['tds_amount'] = $tdsAmount;
            $inputs['total_amount'] = $vatAbleAmount+$vatAmount;
            $grnItem->fill($inputs)->save();
            $this->grns->updateTotalAmount($grnItem->grn_id);
            DB::commit();
            return $grnItem;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
