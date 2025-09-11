<?php

namespace Modules\PaymentSheet\Requests\Detail;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\PaymentSheet\Models\PaymentBill;
use Modules\PaymentSheet\Models\PaymentSheetDetail;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $paymentBill = PaymentBill::find(request()->payment_bill_id);

        $paidAmount = PaymentSheetDetail::where('payment_bill_id', request()->payment_bill_id)->sum('total_amount');
        $billAmount = $paymentBill->bill_amount;

        $maxAmount = floatval($billAmount-$paidAmount);

        return [
            'payment_bill_id'=>'required|exists:payment_bills,id',
            'activity_code_id'=>'required|exists:lkup_activity_codes,id',
            'account_code_id'=>'required|exists:lkup_account_codes,id',
            'donor_code_id'=>'nullable|exists:lkup_donor_codes,id',
            'processed_by_office_id'=>'required|exists:lkup_offices,id',
            'charged_office_id'=>'required|exists:lkup_offices,id',
            'percentage'=>'nullable',
            'tds_percentage' => 'required|numeric',
            'total_amount'=> 'required|lte:'.$maxAmount,
            'description'=>'nullable',
            'tds_applicable'=>'nullable',
        ];
    }
}
