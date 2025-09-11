<?php

namespace Modules\PaymentSheet\Repositories;

use App\Repositories\Repository;
use Modules\PaymentSheet\Models\PaymentSheet;
use Modules\PaymentSheet\Models\PaymentSheetDetail;

use DB;

class PaymentSheetDetailRepository extends Repository
{
    public function __construct(
        protected PaymentSheetRepository $paymentSheets,
        PaymentSheetDetail $paymentSheetDetail
    ){
        $this->model = $paymentSheetDetail;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $paymentSheetDetail = $this->model->create($inputs);
            $this->updateNetAmount($paymentSheetDetail, $inputs);
            $this->paymentSheets->updatePaymentSheetAmount($paymentSheetDetail->payment_sheet_id);
            $this->updatePaidAmount($paymentSheetDetail);
            DB::commit();
            return $paymentSheetDetail;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $paymentSheetDetail = $this->model->findOrFail($id);
            $paymentSheetDetail->delete();
            $this->paymentSheets->updatePaymentSheetAmount($paymentSheetDetail->payment_sheet_id);
            $this->updatePaidAmount($paymentSheetDetail);
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function reconcile($id)
    {
        DB::beginTransaction();
        try {
            $paymentSheetDetail = $this->model->findOrFail($id);
            $paymentBill = $paymentSheetDetail->paymentBill;
            $vatFlag = $paymentBill->vat_flag;
            $totalAmount  = $paymentSheetDetail->total_amount;
            if(!$vatFlag){
                $tdsPercentage = $paymentSheetDetail->tds_percentage;
                $tdsAmount = $totalAmount *$tdsPercentage/100;
                $updateInputs['tds_percentage'] = $tdsPercentage;
                $updateInputs['vat_percentage'] = 0;
                $updateInputs['vat_amount'] = 0;
                $updateInputs['tds_amount'] = round($tdsAmount, 2);
                $updateInputs['amount_with_vat'] = $totalAmount;
                $updateInputs['net_amount'] = $totalAmount-$updateInputs['tds_amount'];
            } else {
                $vatPercentage = config('constant.VAT_PERCENTAGE');
                $vatTdsPercentage = config('constant.VAT_TDS_PERCENTAGE');
                $vatAmount = $totalAmount*$vatPercentage/100;
                $amountWithVat = $totalAmount ? $totalAmount+$vatAmount : 0;
                $tdsAmount = $totalAmount*$vatTdsPercentage/100;
                $updateInputs['vat_percentage'] = $vatPercentage;
                $updateInputs['tds_percentage'] = $vatTdsPercentage;
                $updateInputs['vat_amount'] = round($vatAmount, 2);
                $updateInputs['tds_amount'] = round($tdsAmount, 2);
                $updateInputs['amount_with_vat'] = round($amountWithVat,2);
                $updateInputs['net_amount'] = $updateInputs['amount_with_vat']-$updateInputs['tds_amount'];
            }
            $paymentSheetDetail->update($updateInputs);
            $this->paymentSheets->updatePaymentSheetAmount($paymentSheetDetail->payment_sheet_id);
            $this->updatePaidAmount($paymentSheetDetail);
            DB::commit();
            return $paymentSheetDetail;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $paymentSheetDetail = $this->model->findOrFail($id);
            $paymentSheetDetail->fill($inputs)->save();
            $this->updateNetAmount($paymentSheetDetail, $inputs);
            $this->paymentSheets->updatePaymentSheetAmount($paymentSheetDetail->payment_sheet_id);
            $this->updatePaidAmount($paymentSheetDetail);
            DB::commit();
            return $paymentSheetDetail;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateNetAmount($paymentSheetDetail, $inputs)
    {
        DB::beginTransaction();
        try {
            $paymentBill = $paymentSheetDetail->paymentBill;
            $vatFlag = $paymentBill->vat_flag;
            $totalAmount = $inputs['total_amount'];

            $updateInputs = ['total_amount' => $totalAmount];
            if(!$vatFlag){
                $tdsPercentage = $inputs['tds_percentage'];
                $tdsAmount = $totalAmount *$tdsPercentage/100;
                $updateInputs['tds_percentage'] = $tdsPercentage;
                $updateInputs['vat_percentage'] = 0;
                $updateInputs['vat_amount'] = 0;
                $updateInputs['tds_amount'] = round($tdsAmount, 2);
                $updateInputs['amount_with_vat'] = $updateInputs['total_amount'];
                $updateInputs['net_amount'] = $updateInputs['total_amount']-$updateInputs['tds_amount'];
            } else {
                $vatPercentage = config('constant.VAT_PERCENTAGE');
                $vatTdsPercentage = config('constant.VAT_TDS_PERCENTAGE');
                $vatAmount = $totalAmount*$vatPercentage/100;
                $amountWithVat = $totalAmount ? $totalAmount+$vatAmount : 0;
                $tdsAmount = $totalAmount*$vatTdsPercentage/100;
                $updateInputs['vat_percentage'] = $vatPercentage;
                $updateInputs['tds_percentage'] = $vatTdsPercentage;
                $updateInputs['vat_amount'] = round($vatAmount, 2);
                $updateInputs['tds_amount'] = round($tdsAmount, 2);
                $updateInputs['amount_with_vat'] = round($amountWithVat,2);
                $updateInputs['net_amount'] = $updateInputs['amount_with_vat']-$updateInputs['tds_amount'];
            }

            $paymentSheetDetail = $paymentSheetDetail->update($updateInputs);
            DB::commit();
            return $paymentSheetDetail;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updatePercentage($paymentSheetDetail)
    {
        $percentage = $paymentSheetDetail->paymentSheet->paymentSheetDetails->filter(function($detail) use ($paymentSheetDetail){
            return $detail->payment_bill_id == $paymentSheetDetail->payment_bill_id;
        })->sum('percentage');
        return $paymentSheetDetail->paymentBill()->update(['paid_percentage'=>$percentage]);
    }

    public function updatePaidAmount($paymentSheetDetail)
    {
        $totalAmount = $paymentSheetDetail->paymentSheet->paymentSheetDetails->filter(function($detail) use ($paymentSheetDetail){
            return $detail->payment_bill_id == $paymentSheetDetail->payment_bill_id;
        })->sum('total_amount');
        return $paymentSheetDetail->paymentBill()->update(['settled_amount'=>$totalAmount]);
    }

}
