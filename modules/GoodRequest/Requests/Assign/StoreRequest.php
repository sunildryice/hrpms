<?php

namespace Modules\GoodRequest\Requests\Assign;

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
            'status_id'=>'required',
            'assigned_inventory_item_id'=>'required_if:status_id,10|array',
            'assigned_quantity'=>'nullable|array',
            'assigned_asset_ids'=>'nullable|array',
            'log_remarks'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'assigned_inventory_item_id.required_if'=>'Item is required when approve option is selected.',
            'log_remarks.required'=>'Remarks is required.'
        ];
    }
}
