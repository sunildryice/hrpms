<?php

namespace Modules\PurchaseOrder\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Master\Models\FiscalYear;
use Modules\PurchaseOrder\Models\PurchaseOrder;
use Modules\PurchaseRequest\Models\PurchaseRequestItem;

class PurchaseOrderRepository extends Repository
{
    public function __construct(
        FiscalYear $fiscalYears,
        PurchaseOrder $purchaseOrder,
        PurchaseRequestItem $purchaseRequestItems
    ) {
        $this->fiscalYears = $fiscalYears;
        $this->model = $purchaseOrder;
        $this->purchaseRequestItems = $purchaseRequestItems;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model->with(['fiscalYear', 'status', 'createdBy'])
                    ->whereStatusId(config('constant.APPROVED_STATUS'))
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                    })
                    ->orderBy('fiscal_year_id', 'desc')
                    ->orderBy('order_number', 'desc')
                    ->get();
            }
        }

        return $this->model->with(['fiscalYear', 'status', 'createdBy'])
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('fiscal_year_id', 'desc')
            ->orderBy('order_number', 'desc')
            ->get();
    }

    public function getCancelled()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model->with(['fiscalYear', 'status', 'createdBy'])
                    ->whereStatusId(config('constant.CANCELLED_STATUS'))
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.CANCELLED_STATUS')]);
                    })
                    ->orderBy('fiscal_year_id', 'desc')
                    ->orderBy('order_number', 'desc')
                    ->get();
            }
        }

        return $this->model->with(['fiscalYear', 'status', 'createdBy'])
            ->whereStatusId(config('constant.CANCELLED_STATUS'))
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('fiscal_year_id', 'desc')
            ->orderBy('order_number', 'desc')
            ->get();
    }

    public function getPurchaseOrderNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'order_number'])->where('fiscal_year_id', $fiscalYearId)
            ->max('order_number') + 1;

        return $max;
    }

    public function getPurchaseOrdersForReviewAndApproval($authUser)
    {
        return $this->model->select(['*'])
            ->where(function ($q) use ($authUser) {
                $q->where('status_id', config('constant.SUBMITTED_STATUS'))
                    ->where('reviewer_id', $authUser->id);
            })->orWhere(function ($q) use ($authUser) {
                $q->whereIn('status_id', [config('constant.VERIFIED_STATUS')])
                    ->where('approver_id', $authUser->id);
            })->orderBy('order_date', 'desc')
            ->take(5)->get();
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = $this->model->find($id);
            $purchaseOrder->update($inputs);
            $purchaseOrder->logs()->create($inputs);
            $this->updateTotalAmount($purchaseOrder->id);
            DB::commit();

            return $purchaseOrder;
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
            $orderItems = [];
            $purchaseRequestItems = $this->purchaseRequestItems->select(['*'])
                ->where('purchase_request_id', $inputs['purchase_request_id'])->get();
            foreach ($purchaseRequestItems as $index => $purchaseItem) {
                $remainingQty = $purchaseItem->quantity - $purchaseItem->purchaseOrderItems()->sum('quantity');
                if (! empty($inputs['purchase_request_item_ids'][$purchaseItem->id]) && $remainingQty > 0) {
                    $vatAmount = 0;
                    $quantity = $inputs['order_quantity'][$purchaseItem->id];
                    $unitPrice = $inputs['unit_price'][$purchaseItem->id];
                    $subTotal = $inputs['order_quantity'][$purchaseItem->id] * $inputs['unit_price'][$purchaseItem->id];
                    if (array_key_exists('vat_applicable', $inputs)) {
                        if (array_key_exists($purchaseItem->id, $inputs['vat_applicable'])) {
                            $vatAmount = $subTotal * config('constant.VAT_PERCENTAGE') / 100;
                        }
                    }

                    $orderItems[] = [
                        'purchase_request_item_id' => $purchaseItem->id,
                        'item_id' => $purchaseItem->item_id,
                        'unit_id' => $purchaseItem->unit_id,
                        'account_code_id' => $purchaseItem->account_code_id,
                        'activity_code_id' => $purchaseItem->activity_code_id,
                        'donor_code_id' => $purchaseItem->donor_code_id,
                        'specification' => $purchaseItem->specification,
                        'quantity' => $inputs['order_quantity'][$purchaseItem->id],
                        'unit_price' => $inputs['unit_price'][$purchaseItem->id],
                        'total_price' => $subTotal,
                        'vat_amount' => $vatAmount,
                        'total_amount' => $subTotal + $vatAmount,
                    ];
                }
            }

            if (count($orderItems)) {
                $purchaseOrder = $this->model->create($inputs);
                $purchaseOrder->purchaseOrderItems()->createMany($orderItems);
                $this->updateTotalAmount($purchaseOrder->id);
                if (array_key_exists('district_ids', $inputs)) {
                    $purchaseOrder->districts()->sync($inputs['district_ids']);
                }
                $purchaseOrder->purchaseRequests()->attach($inputs['purchase_request_id']);

                DB::commit();

                return $purchaseOrder;
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
            $purchaseOrder = $this->model->findOrFail($id);
            $purchaseOrder->purchaseOrderItems()->delete();
            $purchaseOrder->logs()->delete();
            $purchaseOrder->purchaseOrderItems()->delete();
            $purchaseOrder->delete();

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');

            if (! $purchaseOrder->order_number) {
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();
                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['order_date'] = date('Y-m-d');
                $inputs['prefix'] = 'PO';
                $inputs['order_number'] = $this->getPurchaseOrderNumber($fiscalYear->id);
            }

            $purchaseOrder->update($inputs);
            $purchaseOrder->logs()->create($inputs);
            DB::commit();

            return $purchaseOrder;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function review($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = $this->model->find($id);
            $purchaseOrder->update($inputs);
            $purchaseOrder->logs()->create($inputs);
            DB::commit();

            return $purchaseOrder;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = $this->model->find($id);
            $purchaseOrder->fill($inputs)->save();
            if (array_key_exists('district_ids', $inputs)) {
                $purchaseOrder->districts()->sync($inputs['district_ids']);
            } else {
                $purchaseOrder->districts()->sync([]);
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Purchase order is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $purchaseOrder = $this->forward($purchaseOrder->id, $forwardInputs);
            }
            $this->updateTotalAmount($purchaseOrder->id);
            DB::commit();

            return $purchaseOrder;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function updateFromPr($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $orderItems = [];
            $purchaseRequestItems = $this->purchaseRequestItems->select(['*'])
                ->where('purchase_request_id', $inputs['purchase_request_id'])->get();
            foreach ($purchaseRequestItems as $purchaseItem) {
                $remainingQty = $purchaseItem->quantity - $purchaseItem->purchaseOrderItems()->sum('quantity');
                if (! empty($inputs['purchase_request_item_ids'][$purchaseItem->id]) && $remainingQty > 0) {
                    $vatAmount = 0;
                    $subTotal = $inputs['order_quantity'][$purchaseItem->id] * $inputs['unit_price'][$purchaseItem->id];
                    if (array_key_exists('vat_applicable', $inputs)) {
                        if (array_key_exists($purchaseItem->id, $inputs['vat_applicable'])) {
                            $vatAmount = $subTotal * config('constant.VAT_PERCENTAGE') / 100;
                        }
                    }

                    $orderItems[] = [
                        'purchase_request_item_id' => $purchaseItem->id,
                        'item_id' => $purchaseItem->item_id,
                        'unit_id' => $purchaseItem->unit_id,
                        'account_code_id' => $purchaseItem->account_code_id,
                        'activity_code_id' => $purchaseItem->activity_code_id,
                        'donor_code_id' => $purchaseItem->donor_code_id,
                        'specification' => $purchaseItem->specification,
                        'quantity' => $inputs['order_quantity'][$purchaseItem->id],
                        'unit_price' => $inputs['unit_price'][$purchaseItem->id],
                        'total_price' => $subTotal,
                        'vat_amount' => $vatAmount,
                        'total_amount' => $subTotal + $vatAmount,
                    ];
                }
            }

            if (count($orderItems)) {
                $purchaseOrder = $this->model->find($id);
                $purchaseOrder->purchaseOrderItems()->createMany($orderItems);
                $this->updateTotalAmount($purchaseOrder->id);
                DB::commit();

                return $purchaseOrder;
            }

            return false;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function updateTotalAmount($purchaseOrderId)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = $this->model->find($purchaseOrderId);
            $subTotal = $purchaseOrder->purchaseOrderItems->sum('total_price');
            $vatAmount = $purchaseOrder->purchaseOrderItems->sum('vat_amount');
            $updateInputs = [
                'sub_total' => $subTotal,
                'vat_amount' => $vatAmount,
                'total_amount' => $subTotal + $vatAmount + $purchaseOrder->other_charge_amount,
            ];
            $purchaseOrder->update($updateInputs);
            DB::commit();

            return $purchaseOrder;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function updateItems($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = $this->model->findOrFail($id);
            $orderItems = [];
            $purchaseRequestItems = $this->purchaseRequestItems->select(['*'])
                ->where('purchase_request_id', $inputs['purchase_request_id'])->get();
            foreach ($purchaseRequestItems as $purchaseItem) {
                $remainingQty = $purchaseItem->quantity - $purchaseItem->purchaseOrderItems()->sum('quantity');
                if (! empty($inputs['purchase_request_item_ids'][$purchaseItem->id]) && $remainingQty > 0) {
                    $vatAmount = 0;
                    $subTotal = $inputs['order_quantity'][$purchaseItem->id] * $inputs['unit_price'][$purchaseItem->id];
                    if (array_key_exists('vat_applicable', $inputs)) {
                        if (array_key_exists($purchaseItem->id, $inputs['vat_applicable'])) {
                            $vatAmount = $subTotal * config('constant.VAT_PERCENTAGE') / 100;
                        }
                    }

                    $orderItems[] = [
                        'purchase_request_item_id' => $purchaseItem->id,
                        'item_id' => $purchaseItem->item_id,
                        'unit_id' => $purchaseItem->unit_id,
                        'account_code_id' => $purchaseItem->account_code_id,
                        'activity_code_id' => $purchaseItem->activity_code_id,
                        'donor_code_id' => $purchaseItem->donor_code_id,
                        'specification' => $purchaseItem->specification,
                        'quantity' => $inputs['order_quantity'][$purchaseItem->id],
                        'unit_price' => $inputs['unit_price'][$purchaseItem->id],
                        'total_price' => $subTotal,
                        'vat_amount' => $vatAmount,
                        'total_amount' => $subTotal + $vatAmount,
                    ];
                }
            }

            if (count($orderItems)) {
                $purchaseOrder->purchaseOrderItems()->createMany($orderItems);
                $this->updateTotalAmount($purchaseOrder->id);
                $purchaseOrder->purchaseRequests()->attach($inputs['purchase_request_id']);
                DB::commit();

                return $purchaseOrder;
            }

            return false;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function cancel($id, $inputs)
    {
        DB::beginTransaction();
        try {
            if ($inputs['status_id'] == config('constant.REJECTED_STATUS')) {
                $inputs['status_id'] = config('constant.APPROVED_STATUS');
            }
            $inputs['user_id'] = auth()->id();
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            $purchaseOrder = $this->model->find($id);
            $purchaseOrder->update($inputs);
            $purchaseOrder->logs()->create($inputs);
            if ($purchaseOrder->status_id == config('constant.CANCELLED_STATUS')) {
                $clone = $purchaseOrder->replicate();
                unset($clone->reviewer_id);
                unset($clone->approver_id);
                unset($clone->prefix);
                unset($clone->order_number);
                $clone->status_id = 1;
                $clone->save();
                foreach ($purchaseOrder->purchaseOrderItems as $item) {
                    unset($item->id);
                    unset($item->purchase_order_id);
                    $clone->purchaseOrderItems()->create($item->toArray());
                }
                if ($purchaseOrder->districts) {
                    $districts = $purchaseOrder->districts->pluck('id')->toArray();
                    $clone->districts()->sync($districts);
                }
            }
            DB::commit();

            return $purchaseOrder;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function requestCancel($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = config('constant.INIT_CANCEL_STATUS');
            $inputs['user_id'] = auth()->id();
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            $purchaseOrder = $this->model->find($id);
            $purchaseOrder->update($inputs);
            $purchaseOrder->logs()->create($inputs);
            DB::commit();

            return $purchaseOrder;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            dd($e);

            return false;
        }
    }

    public function reverseApprove($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = config('constant.RETURNED_STATUS');
            $inputs['user_id'] = auth()->id();
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            $purchaseOrder = $this->model->find($id);
            $purchaseOrder->update($inputs);
            $purchaseOrder->logs()->create($inputs);
            DB::commit();
            return $purchaseOrder;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            dd($e);
            return false;
        }
    }
}
