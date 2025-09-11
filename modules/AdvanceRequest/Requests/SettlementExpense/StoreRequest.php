<?php

namespace Modules\AdvanceRequest\Requests\SettlementExpense;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'advance_request_detail_id'=>'required|exists:advance_request_details,id',
            'district_id'=>'nullable',
            'location'=>'nullable',
            'expense_date'  => 'required|date',
            'expense_category_id' =>'nullable|exists:lkup_expense_categories,id',
            'bill_number' => 'required',
            'gross_amount' => 'required|numeric|min:0.01',
            'tax_amount' => 'required|numeric|min:0',
            'expense_type_id'  => 'nullable|exists:lkup_expense_types,id'
        ];
    }
}
