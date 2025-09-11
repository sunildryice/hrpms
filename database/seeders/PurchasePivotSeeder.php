<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\PurchaseOrder\Models\PurchaseOrder;
use Modules\PurchaseRequest\Models\PurchaseRequest;

class PurchasePivotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $purchaseRequests = PurchaseRequest::all();
        foreach ($purchaseRequests as $pr) {
            $prItemArray = $pr->purchaseRequestItems()->pluck('id');

            $purchaseOrders = PurchaseOrder::select(['id'])
                ->whereHas('purchaseOrderItems', function ($q) use ($prItemArray) {
                    $q->whereIn('purchase_request_item_id', $prItemArray);
                })->get();

            if ($purchaseOrders->isNotEmpty()) {
                foreach ($purchaseOrders as $po) {
                    DB::table('purchase_request_order')->upsert([
                        'pr_id' => $pr->id,
                        'po_id' => $po->id,
                    ],
                    ['pr_id', 'po_id']
                );
                }
            }
        }
    }
}
