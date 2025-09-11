<?php

namespace Modules\PurchaseRequest\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Master\Repositories\PackageItemRepository;
use Modules\PurchaseRequest\Models\PurchaseRequestItem;

class PurchaseRequestItemRepository extends Repository
{
    public function __construct(
        PurchaseRequestItem $purchaseRequestItems,
        PurchaseRequestRepository $purchaseRequests,
        PackageItemRepository $packageItems,
    ) {
        $this->model = $purchaseRequestItems;
        $this->purchaseRequests = $purchaseRequests;
        $this->packageItems = $packageItems;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequestItem = $this->model->create($inputs);
            $this->purchaseRequests->updateTotalAmount($purchaseRequestItem->purchase_request_id);
            DB::commit();
            return $purchaseRequestItem;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $purchaseRequestItem = $this->model->findOrFail($id);
            $purchaseRequestItem->delete();
            $this->purchaseRequests->updateTotalAmount($purchaseRequestItem->purchase_request_id);
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroyAll($id)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = $this->purchaseRequests->find($id);
            $purchaseRequest->purchaseRequestItems()->delete();
            $updatedPR = $this->purchaseRequests->updateTotalAmount($purchaseRequest->id);
            DB::commit();
            return $updatedPR;
        } catch (\Illuminate\Database\QueryException $th) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $purchaseRequestItem = $this->model->findOrFail($id);
            $purchaseRequestItem->fill($data)->save();
            $this->purchaseRequests->updateTotalAmount($purchaseRequestItem->purchase_request_id);
            DB::commit();
            return $purchaseRequestItem;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function createFromPackage($inputs)
    {
        DB::beginTransaction();
        try {

            $purchaseRequest = $this->purchaseRequests->find($inputs['purchase_request_id']);
            $packageItems = $this->packageItems->select('*')->with('package')
                ->where('package_id', $inputs['package_id'])
                ->get();
            $package = '';
            foreach ($packageItems->toArray() as $packageItem) {
                $codes = [
                    "office_id" => $inputs['office_id'],
                    "account_code_id" => $inputs['account_code_id'],
                    "activity_code_id" => $inputs['activity_code_id'],
                    "donor_code_id" => $inputs['donor_code_id'],
                    "quantity" => $inputs['quantity'],
                ];
                $packageItem['total_price'] = $inputs['quantity'] * $packageItem['unit_price'];
                $packageItem = array_merge($packageItem, $codes);
                $newPurchaseRequestItem = new PurchaseRequestItem($packageItem);
                $purchaseRequest->purchaseRequestItems()->save($newPurchaseRequestItem);
            }
            if(count($packageItems)){
                $package = $packageItems->first()->package->package_name;
            }
            $updatedPR = $this->purchaseRequests->updateTotalAmount($purchaseRequest->id);
            DB::commit();
            return ['updatedPR'=>$updatedPR, 'package' => $package ];
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
