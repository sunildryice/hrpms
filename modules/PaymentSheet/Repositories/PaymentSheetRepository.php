<?php

namespace Modules\PaymentSheet\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\PaymentSheet\Models\PaymentSheet;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Illuminate\Support\Facades\Auth;

class PaymentSheetRepository extends Repository
{
    public function __construct(
        protected FiscalYearRepository $fiscalYears,
        protected PurchaseOrderRepository $purchaseOrders,
        protected PaymentBillRepository $paymentBills,
        PaymentSheet $paymentSheet
    ) {
        $this->model = $paymentSheet;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        // $authUser = Auth::user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->with(['fiscalYear', 'status', 'supplier', 'requester', 'submittedLog', 'approvedLog'])
                    ->select(['*'])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')])
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')]);
                    })
                    ->orderBy('fiscal_year_id', 'desc')
                    ->orderBy('sheet_number', 'desc')->get();
            }
        }

        return $this->model
            ->with(['fiscalYear', 'status', 'supplier', 'requester'])
            ->select(['*'])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')])
            ->orderBy('fiscal_year_id', 'desc')
            ->orderBy('sheet_number', 'desc')->get();
    }

    public function getPaid()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->with(['fiscalYear', 'status'])
                    ->select(['*'])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->whereStatusId(config('constant.PAID_STATUS'))
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.PAID_STATUS')]);
                    })
                    ->orderBy('fiscal_year_id', 'desc')
                    ->orderBy('sheet_number', 'desc')->get();
            }
        }

        return $this->model
            ->with(['fiscalYear', 'status'])
            ->select(['*'])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->whereStatusId(config('constant.PAID_STATUS'))
            ->orderBy('fiscal_year_id', 'desc')
            ->orderBy('sheet_number', 'desc')->get();
    }

    public function generatePaymentSheetNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'sheet_number'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->max('sheet_number') + 1;

        return $max;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $paymentSheet = $this->model->find($id);
            // if($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')){
            //     $inputs['approver_id'] = $inputs['recommended_to'];
            //     $inputs['reviewer_id'] = $paymentSheet->approver_id;
            // }
            $paymentSheet->update($inputs);
            $paymentSheet->logs()->create($inputs);
            DB::commit();

            return $paymentSheet;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function pay($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $paymentSheet = $this->model->find($id);
            $inputs['log_remarks'] = $inputs['payment_remarks'];
            $inputs['status_id'] = config('constant.PAID_STATUS');
            $paymentSheet->update($inputs);
            $paymentSheet->logs()->create($inputs);
            DB::commit();

            return $paymentSheet;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function reviewRecommended($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $paymentSheet = $this->model->find($id);
            $paymentSheet->update($inputs);
            $paymentSheet->logs()->create($inputs);
            DB::commit();

            return $paymentSheet;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function approveRecommended($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $paymentSheet = $this->model->find($id);
            $paymentSheet->update($inputs);
            $paymentSheet->logs()->create($inputs);
            DB::commit();

            return $paymentSheet;
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
            $paymentSheet = $this->model->create($inputs);
            if (array_key_exists('purchase_order_ids', $inputs)) {
                $paymentSheet->purchaseOrders()->sync($inputs['purchase_order_ids']);
            }
            DB::commit();

            return $paymentSheet;
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
            $inputs['is_from_po'] = 1;
            $paymentSheet = $this->model->create($inputs);
            $this->createDetails($paymentSheet, $inputs);
            if (array_key_exists('purchase_order_ids', $inputs)) {
                $paymentSheet->purchaseOrders()->sync($inputs['purchase_order_ids']);
            }
            // dd($paymentSheet->paymentSheetDetails->toArray());
            DB::commit();

            return $paymentSheet;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $paymentSheet = $this->model->findOrFail($id);
            $paymentSheet->logs()->delete();
            foreach ($paymentSheet->paymentSheetDetails as $paymentSheetDetail) {
                $paymentSheetDetail->paymentBill->update(['paid_percentage' => 0, 'settled_amount' => 0]);
            }
            $paymentSheet->paymentSheetDetails()->delete();
            $paymentSheet->delete();

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $paymentSheet = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            if (! $paymentSheet->sheet_number) {
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['prefix'] = 'PS';
                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['sheet_number'] = $this->generatePaymentSheetNumber($fiscalYear->id);
            }
            $inputs['paid_amount'] = $paymentSheet->net_amount - $paymentSheet->deduction_amount;
            $paymentSheet->update($inputs);
            $paymentSheet->logs()->create($inputs);
            DB::commit();

            return $paymentSheet;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $paymentSheet = $this->model->find($id);
            $paymentSheet->fill($inputs)->save();
            if (array_key_exists('purchase_order_ids', $inputs)) {
                $paymentSheet->purchaseOrders()->sync($inputs['purchase_order_ids']);
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Payment sheet is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $paymentSheet = $this->forward($paymentSheet->id, $forwardInputs);
            }
            DB::commit();

            return $paymentSheet;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function updatePaymentSheetAmount($paymentSheetId)
    {
        DB::beginTransaction();
        try {
            $paymentSheet = $this->model->find($paymentSheetId);
            $details = $paymentSheet->paymentSheetDetails;

            $updateInputs = [
                'total_amount' => $details->sum('total_amount'),
                'vat_amount' => $details->sum('vat_amount'),
                'tds_amount' => $details->sum('tds_amount'),
                'net_amount' => $details->sum('net_amount'),
            ];
            $paymentSheet->update($updateInputs);
            DB::commit();

            return $paymentSheet;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function verify($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $paymentSheet = $this->model->find($id);
            $paymentSheet->update($inputs);
            $paymentSheet->logs()->create($inputs);
            DB::commit();

            return $paymentSheet;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $paymentSheet = $this->model->findOrFail($id);
            $paymentSheet->update($inputs);
            $paymentSheet->logs()->create($inputs);
            DB::commit();

            return $paymentSheet;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function createDetails($paymentSheet, $inputs)
    {
        $paymentBill = $this->paymentBills->find($inputs['payment_bill_id']);
        $summaries = $this->purchaseOrders->find($inputs['purchase_order_ids'][0])->getSummary();
        // $summaries = collect();
        // foreach ($inputs['purchase_order_ids'] as $index => $poId) {
        //     $po = $this->purchaseOrders->find($poId);
        //     $summaries = $summaries->merge($po->getSummary());
        // }
        // $summaries = $summaries->groupBy(['purchase_request_item_office_id', 'activity_code_id', 'donor_code_id', 'account_code_id'])
        //     ->flatten(3)->map(function ($summary) {
        //         $item = $summary->first();
        //         $item->total_amount = $summary->sum('total_amount');
        //         $item->vat_amount = $summary->sum('vat_amount');
        //         $item->total_price = $summary->sum('total_price');
        //         $item->specification = $summary->implode('specification', ', ');
        //         return $item;
        //     });

        foreach ($summaries as $detail) {
            $paymentSheetDetail = app(PaymentSheetDetailRepository::class)->create([
                'payment_sheet_id' => $paymentSheet->id,
                'payment_bill_id' => $paymentBill->id,
                'processed_by_office_id' => auth()->user()->getCurrentOffice()?->id,
                'charged_office_id' => $detail->purchase_request_item_office_id,
                'activity_code_id' => $detail->activity_code_id,
                'account_code_id' => $detail->account_code_id,
                'donor_code_id' => $detail->donor_code_id,
                'tds_percentage' => $inputs['tds_percentage'],
                'total_amount' => $detail->total_price,
                'description' => $detail->specification,
            ]);
            $paymentSheetDetail->purchaseOrderItems()->sync(explode(', ', $detail->po_item_ids));
        }
    }
}
