<?php

namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\PaymentDetail;
use Modules\Payroll\Models\PaymentItem;

use DB;

class PaymentDetailRepository extends Repository
{
    public function __construct(
        PaymentDetail $paymentDetail,
        PaymentItem $paymentItem
    )
    {
        $this->model = $paymentDetail;
        $this->paymentItem = $paymentItem;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $detail = $this->model->create($inputs);
            if ($detail->paymentItem->slug == 'basic-salary') {
                $this->updateSsf($detail, $inputs);
            }
            DB::commit();
            return $detail;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $detail = $this->model->findOrFail($id);
            if ($detail->paymentItem->slug == 'basic-salary') {
                $pf = $this->paymentItem->where('slug', 'provident-fund')->first();
                $gratuity = $this->paymentItem->where('slug', 'gratuity')->first();
                $medicalInsurance = $this->paymentItem->where('slug', 'medical-insurance')->first();
                $ssf = $this->paymentItem->where('slug', 'ssf-deduction')->first();

                $paymentDetail = new PaymentDetail();
                $paymentDetail->where('payment_master_id', $detail->payment_master_id)
                    ->where('payment_item_id', $pf->id)
                    ->firstOrFail()->delete();
                $paymentDetail->where('payment_master_id', $detail->payment_master_id)
                    ->where('payment_item_id', $gratuity->id)
                    ->firstOrFail()->delete();
                $paymentDetail->where('payment_master_id', $detail->payment_master_id)
                    ->where('payment_item_id', $medicalInsurance->id)
                    ->firstOrFail()->delete();
                $paymentDetail->where('payment_master_id', $detail->payment_master_id)
                    ->where('payment_item_id', $ssf->id)
                    ->firstOrFail()->delete();
            }
            $detail->delete();
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
            $detail = $this->model->findOrFail($id);
            $detail->fill($inputs)->save();
            if ($detail->paymentItem->slug == 'basic-salary') {
                $this->updateSsf($detail, $inputs);
            }

            DB::commit();
            return $detail;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateSsf($detail, $inputs)
    {
        $pfAmount = 0.1 * $detail->amount;
        $gratuityAmount = 8.33 * $detail->amount/100;
        $medicalInsuranceAmount = 1.67 * $detail->amount/100;
        $ssfAmount = 31 * $detail->amount/100;
        $pf = $this->paymentItem->where('slug', 'provident-fund')->first();
        $gratuity = $this->paymentItem->where('slug', 'gratuity')->first();
        $medicalInsurance = $this->paymentItem->where('slug', 'medical-insurance')->first();
        $ssf = $this->paymentItem->where('slug', 'ssf-deduction')->first();

        $paymentDetail = new PaymentDetail();
        $paymentDetail->updateOrCreate([
            'payment_master_id' => $detail->payment_master_id,
            'payment_item_id' => $pf->id,
        ], [
            'amount'=>$pfAmount,
            'created_by'=>$inputs['created_by'],
            'updated_by'=>$inputs['updated_by'],
        ]);

        $paymentDetail->updateOrCreate([
            'payment_master_id' => $detail->payment_master_id,
            'payment_item_id' => $gratuity->id,
        ], [
            'amount'=>$gratuityAmount,
            'created_by'=>$inputs['created_by'],
            'updated_by'=>$inputs['updated_by'],
        ]);

        $paymentDetail->updateOrCreate([
            'payment_master_id' => $detail->payment_master_id,
            'payment_item_id' => $medicalInsurance->id,
        ], [
            'amount'=>$medicalInsuranceAmount,
            'created_by'=>$inputs['created_by'],
            'updated_by'=>$inputs['updated_by'],
        ]);

        $paymentDetail->updateOrCreate([
            'payment_master_id' => $detail->payment_master_id,
            'payment_item_id' => $ssf->id,
        ], [
            'amount'=>$ssfAmount,
            'created_by'=>$inputs['created_by'],
            'updated_by'=>$inputs['updated_by'],
        ]);
    }
}
