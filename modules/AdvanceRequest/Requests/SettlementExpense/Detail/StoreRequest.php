<?php

namespace Modules\AdvanceRequest\Requests\SettlementExpense\Detail;

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
            'expense_category_id' =>'nullable|exists:lkup_expense_categories,id',
            'description' =>'nullable',
            'expense_type_id'  => 'nullable|exists:lkup_expense_types,id',
            'expense_date'  => 'required|date',
            'bill_number' => 'required',
            'gross_amount' => 'required|numeric|min:0.01',
            'tax_amount' => 'required|numeric|min:0',
            'attachment'=>'nullable|mimes:jpeg,png,pdf|max:2048',
        ];
    }
}
