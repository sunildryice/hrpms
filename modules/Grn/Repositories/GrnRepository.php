<?php

namespace Modules\Grn\Repositories;

use App\Repositories\Repository;
use Modules\Grn\Models\Grn;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\PurchaseOrder\Models\PurchaseOrderItem;

use DB;
use Modules\Grn\Models\GrnItem;
use Modules\PurchaseOrder\Models\PurchaseOrder;
use Modules\PurchaseRequest\Models\PurchaseRequest;
use Modules\PurchaseRequest\Models\PurchaseRequestItem;

class GrnRepository extends Repository
{
    private $fiscalYears;
    private $purchaseOrderItems;
    private $purchaseOrders;
    private $purchaseRequests;
    private $purchaseRequestItems;

    public function __construct(
        FiscalYearRepository    $fiscalYears,
        Grn                     $grn,
        PurchaseOrderItem       $purchaseOrderItems,
        PurchaseOrder           $purchaseOrders,
        PurchaseRequest         $purchaseRequests,
        PurchaseRequestItem     $purchaseRequestItems,
    )
    {
        $this->fiscalYears          = $fiscalYears;
        $this->model                = $grn;
        $this->purchaseOrderItems   = $purchaseOrderItems;
        $this->purchaseOrders       = $purchaseOrders;
        $this->purchaseRequests     = $purchaseRequests;
        $this->purchaseRequestItems = $purchaseRequestItems;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model->with(['fiscalYear', 'status', 'createdBy', 'supplier', 'grnable'])->select(['*'])
                ->whereStatusId(config('constant.APPROVED_STATUS'))
                ->whereIn('office_id', $accessibleOfficeIds)
                ->orWhere(function ($q) {
                    $q->whereNull('office_id');
                    $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                })
                ->orderBy('fiscal_year_id', 'desc')
                ->orderBy('grn_number', 'desc')->get();
            }
        }

        return $this->model->with(['fiscalYear', 'status', 'createdBy'])->select(['*'])
        ->whereStatusId(config('constant.APPROVED_STATUS'))
        ->whereIn('office_id', $accessibleOfficeIds)
        ->orderBy('fiscal_year_id', 'desc')
        ->orderBy('grn_number', 'desc')->get();
    }

    public function generateGrnNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'grn_number'])
                ->where('fiscal_year_id', $fiscalYearId)
                ->max('grn_number') + 1;
        return $max;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $grn = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
            }
            $grn->update($inputs);
            $grn->logs()->create($inputs);
            $this->updateTotalAmount($grn->id);
            DB::commit();
            return $grn;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function createFromPo($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $grnItems = [];

            $purchaseOrder = $this->purchaseOrders->find($inputs['purchase_order_id']);
            $grn = new Grn($inputs);
            $purchaseOrder->grns()->save($grn);

            $purchaseOrderItems = $this->purchaseOrderItems->select(['*'])
            ->where('purchase_order_id', $inputs['purchase_order_id'])->get();

            foreach ($purchaseOrderItems as $purchaseItem) {
                $maxQuantity = $purchaseItem->quantity - $purchaseItem->grnItems->sum('quantity');
                if (!empty($inputs['purchase_order_item_ids'][$purchaseItem->id]) &&
                    $inputs['received_quantity'][$purchaseItem->id] && $maxQuantity >= $inputs['received_quantity'][$purchaseItem->id]) {
                    $grnItem = [
                        'grn_id' => $grn->id,
                        'item_id' => $purchaseItem->item_id,
                        'unit_id' => $purchaseItem->unit_id,
                        'account_code_id' => $purchaseItem->account_code_id,
                        'activity_code_id' => $purchaseItem->activity_code_id,
                        'donor_code_id' => $purchaseItem->donor_code_id,
                        'quantity' => $inputs['received_quantity'][$purchaseItem->id],
                        'unit_price' => $purchaseItem->unit_price,
                        'total_price' => $inputs['received_quantity'][$purchaseItem->id] * $purchaseItem->unit_price,
                        'vat_amount'=>0,
                        'tds_amount'=>0,
                    ];
                    if (array_key_exists('vat_applicable', $inputs)) {
                        if (array_key_exists($purchaseItem->id, $inputs['vat_applicable'])) {
                            $vatPercentage = config('constant.VAT_PERCENTAGE');
                            $vatTdsPercentage = config('constant.VAT_TDS_PERCENTAGE');
                            $grnItem['vat_amount'] =  $grnItem['total_price'] * $vatPercentage / 100;
                            $grnItem['tds_amount'] =  $grnItem['total_price'] * $vatTdsPercentage / 100;
                        }
                    }
                    $grnItem['total_amount'] =  $grnItem['total_price'] + $grnItem['vat_amount'];
                    $grnItems[] = $grnItem;

                    $grnItem = new GrnItem($grnItem);
                    $purchaseItem->grnItems()->save($grnItem);
                }
            }

            if (count($grnItems)) {
                $this->updateTotalAmount($grn->id);
                DB::commit();
                return $grn;
            }
            return false;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function createFromPr($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $grnItems = [];

            $purchaseRequest = $this->purchaseRequests->find($inputs['purchase_request_id']);
            $grn = new Grn($inputs);
            $purchaseRequest->grns()->save($grn);

            $purchaseRequestItems = $this->purchaseRequestItems
                                    ->select(['*'])
                                    ->where('purchase_request_id', $inputs['purchase_request_id'])
                                    ->get();

            foreach ($purchaseRequestItems as $purchaseItem) {
                $maxQuantity = $purchaseItem->quantity - $purchaseItem->grnItems->sum('quantity');
                if (!empty($inputs['purchase_request_item_ids'][$purchaseItem->id]) &&
                    $inputs['received_quantity'][$purchaseItem->id] &&
                    $inputs['unit_price'][$purchaseItem->id] &&
                    $maxQuantity >= $inputs['received_quantity'][$purchaseItem->id]) {

                    $grnItem = [
                        'grn_id' => $grn->id,
                        'item_id' => $purchaseItem->item_id,
                        'unit_id' => $purchaseItem->unit_id,
                        'account_code_id' => $purchaseItem->account_code_id,
                        'activity_code_id' => $purchaseItem->activity_code_id,
                        'donor_code_id' => $purchaseItem->donor_code_id,
                        'quantity' => $inputs['received_quantity'][$purchaseItem->id],
                        'unit_price' => $inputs['unit_price'][$purchaseItem->id],
                        'total_price' => $inputs['received_quantity'][$purchaseItem->id] * $inputs['unit_price'][$purchaseItem->id],
                        'vat_amount'=>0,
                        'tds_amount'=>0,
                    ];

                    if (array_key_exists('vat_applicable', $inputs)) {
                        if (array_key_exists($purchaseItem->id, $inputs['vat_applicable'])) {
                            $vatPercentage = config('constant.VAT_PERCENTAGE');
                            $vatTdsPercentage = config('constant.VAT_TDS_PERCENTAGE');
                            $grnItem['vat_amount'] =  $grnItem['total_price'] * $vatPercentage / 100;
                            $grnItem['tds_amount'] =  $grnItem['total_price'] * $vatTdsPercentage / 100;
                        }
                    }
                    $grnItem['total_amount'] =  $grnItem['total_price'] + $grnItem['vat_amount'];
                    $grnItems[] = $grnItem;

                    $grnItem = new GrnItem($grnItem);
                    $purchaseItem->grnItems()->save($grnItem);
                }
            }

            if (count($grnItems)) {
                $this->updateTotalAmount($grn->id);
                DB::commit();
                return $grn;
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
            $grn = $this->model->findOrFail($id);
            $grn->logs()->delete();
            $grn->grnItems()->delete();
            $grn->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $grn = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.APPROVED_STATUS');
            $inputs['reviewer_id'] = $inputs['approver_id'] = $inputs['user_id'];
            if(!$grn->grn_number){
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['grn_date'] = date('Y-m-d');
                $inputs['prefix'] = 'GRN';
                $inputs['grn_number'] = $this->generateGrnNumber($fiscalYear->id);
            }
            $grn->update($inputs);
            $this->updateTotalAmount($grn->id);
            $grn->logs()->create($inputs);
            DB::commit();
            return $grn;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $grn = $this->model->find($id);
            $grn->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Grn is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $grn = $this->forward($grn->id, $forwardInputs);
            }
            $this->updateTotalAmount($grn->id);
            DB::commit();
            return $grn;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateTotalAmount($grnId)
    {
        DB::beginTransaction();
        try {
            $grn = $this->model->findOrFail($grnId);
            $subTotal = $grn->grnItems->sum('total_price');
            $vatAmount = $grn->grnItems->sum('vat_amount');
            $discountAmount = $grn->grnItems->sum('discount_amount');
            $tdsAmount = $grn->grnItems->sum('tds_amount');
            $grnAmount = $subTotal + $vatAmount - $discountAmount;

            $updateInputs = [
                'sub_total' => $subTotal,
                'discount_amount' => $discountAmount,
                'vat_amount' => $vatAmount,
                'tds_amount' => $tdsAmount,
                'total_amount' => $grnAmount,
                'grn_amount' => $grnAmount - $tdsAmount
            ];
            $grn->update($updateInputs);
            DB::commit();
            return $grn;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateFromPr($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $grnItems = [];
            $purchaseRequestItems = $this->purchaseRequestItems
                                    ->select(['*'])
                                    ->where('purchase_request_id', $inputs['purchase_request_id'])
                                    ->get();
            $grn = $this->model->find($id);
            foreach ($purchaseRequestItems as $purchaseItem) {
                $maxQuantity = $purchaseItem->quantity - $purchaseItem->grnItems->sum('quantity');
                if (!empty($inputs['purchase_request_item_ids'][$purchaseItem->id]) &&
                    $inputs['received_quantity'][$purchaseItem->id] &&
                    $inputs['unit_price'][$purchaseItem->id] &&
                    $maxQuantity >= $inputs['received_quantity'][$purchaseItem->id]) {

                    $grnItem = [
                        'grn_id' => $grn->id,
                        'item_id' => $purchaseItem->item_id,
                        'unit_id' => $purchaseItem->unit_id,
                        'account_code_id' => $purchaseItem->account_code_id,
                        'activity_code_id' => $purchaseItem->activity_code_id,
                        'donor_code_id' => $purchaseItem->donor_code_id,
                        'quantity' => $inputs['received_quantity'][$purchaseItem->id],
                        'unit_price' => $inputs['unit_price'][$purchaseItem->id],
                        'total_price' => $inputs['received_quantity'][$purchaseItem->id] * $inputs['unit_price'][$purchaseItem->id],
                        'vat_amount'=>0,
                        'tds_amount'=>0,
                    ];

                    if (array_key_exists('vat_applicable', $inputs)) {
                        if (array_key_exists($purchaseItem->id, $inputs['vat_applicable'])) {
                            $vatPercentage = config('constant.VAT_PERCENTAGE');
                            $vatTdsPercentage = config('constant.VAT_TDS_PERCENTAGE');
                            $grnItem['vat_amount'] =  $grnItem['total_price'] * $vatPercentage / 100;
                            $grnItem['tds_amount'] =  $grnItem['total_price'] * $vatTdsPercentage / 100;
                        }
                    }
                    $grnItem['total_amount'] =  $grnItem['total_price'] + $grnItem['vat_amount'];
                    $grnItems[] = $grnItem;
                    $grnItem = new GrnItem($grnItem);
                    $purchaseItem->grnItems()->save($grnItem);
                }
            }
            if (count($grnItems)) {
                $this->updateTotalAmount($grn->id);
                DB::commit();
                return $grn;
            }
            return false;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateFromPo($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $grnItems = [];
            $grn = $this->model->find($id);
            $purchaseOrderItems = $this->purchaseOrderItems
                                    ->select(['*'])
                                    ->where('purchase_order_id', $grn->grnable_id)
                                    ->get();
            foreach ($purchaseOrderItems as $purchaseItem) {
                $maxQuantity = $purchaseItem->quantity - $purchaseItem->grnItems->sum('quantity');
                if (!empty($inputs['purchase_request_item_ids'][$purchaseItem->id]) &&
                    $inputs['received_quantity'][$purchaseItem->id] &&
                    $inputs['unit_price'][$purchaseItem->id] &&
                    $maxQuantity >= $inputs['received_quantity'][$purchaseItem->id]) {

                    $grnItem = [
                        'grn_id' => $grn->id,
                        'item_id' => $purchaseItem->item_id,
                        'unit_id' => $purchaseItem->unit_id,
                        'account_code_id' => $purchaseItem->account_code_id,
                        'activity_code_id' => $purchaseItem->activity_code_id,
                        'donor_code_id' => $purchaseItem->donor_code_id,
                        'quantity' => $inputs['received_quantity'][$purchaseItem->id],
                        'unit_price' => $inputs['unit_price'][$purchaseItem->id],
                        'total_price' => $inputs['received_quantity'][$purchaseItem->id] * $inputs['unit_price'][$purchaseItem->id],
                        'vat_amount'=>0,
                        'tds_amount'=>0,
                    ];

                    if (array_key_exists('vat_applicable', $inputs)) {
                        if (array_key_exists($purchaseItem->id, $inputs['vat_applicable'])) {
                            $vatPercentage = config('constant.VAT_PERCENTAGE');
                            $vatTdsPercentage = config('constant.VAT_TDS_PERCENTAGE');
                            $grnItem['vat_amount'] =  $grnItem['total_price'] * $vatPercentage / 100;
                            $grnItem['tds_amount'] =  $grnItem['total_price'] * $vatTdsPercentage / 100;
                        }
                    }
                    $grnItem['total_amount'] =  $grnItem['total_price'] + $grnItem['vat_amount'];
                    $grnItems[] = $grnItem;
                    $grnItem = new GrnItem($grnItem);
                    $purchaseItem->grnItems()->save($grnItem);
                }
            }
            if (count($grnItems)) {
                $this->updateTotalAmount($grn->id);
                DB::commit();
                return $grn;
            }
            return false;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
