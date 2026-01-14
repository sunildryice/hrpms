<?php

namespace Modules\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'office_id'=>'required|exists:lkup_offices,id',
            'supplier_id'=>'required|exists:suppliers,id',
            'purchase_date'=>'required|date',
            'item_id'=>'required|exists:lkup_items,id',
            'unit_id'=>'required|exists:lkup_measurement_units,id',
            'distribution_type_id'=>'required|exists:lkup_distribution_types,id',
            'execution_id'=>'required|exists:lkup_executions,id',
            'specification'=>'nullable',
            'model_name'=>'nullable',
            'quantity'=>'required|numeric|min:0.01',
            'unit_price'=>'required|numeric|min:0.01',
            'activity_code_id'=>'nullable|exists:lkup_activity_codes,id',
            'account_code_id'=>'nullable|exists:lkup_account_codes,id',
            'donor_code_id'=>'nullable|exists:lkup_donor_codes,id',
        ];
    }

    public function messages()
    {
        return [];
    }
}
