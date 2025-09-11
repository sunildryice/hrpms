<?php

namespace Modules\PurchaseOrder\Repositories;

use App\Repositories\Repository;
use Modules\PurchaseOrder\Models\PurchaseOrderItem;

use DB;

class PurchaseOrderItemRepository extends Repository
{
    public function __construct(
        PurchaseOrderRepository $purchaseOrders,
        PurchaseOrderItem $purchaseOrderItem
    )
    {
        $this->purchaseOrders = $purchaseOrders;
        $this->model = $purchaseOrderItem;
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $purchaseOrderItem = $this->model->findOrFail($id);
            $purchaseOrderItem->delete();
            $this->purchaseOrders->updateTotalAmount($purchaseOrderItem->purchase_order_id);
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
            $purchaseOrderItem = $this->model->findOrFail($id);
            $vatAmount = 0;
            if(array_key_exists('vat_applicable', $inputs)){
                $vatAmount = $inputs['total_price'] * config('constant.VAT_PERCENTAGE') / 100;
            }
            $inputs['vat_amount'] = $vatAmount;
            $inputs['total_amount'] = $inputs['total_price']+$vatAmount;
            $purchaseOrderItem->fill($inputs)->save();
            $this->purchaseOrders->updateTotalAmount($purchaseOrderItem->purchase_order_id);
            DB::commit();
            return $purchaseOrderItem;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
