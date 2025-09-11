<?php

namespace Modules\MaintenanceRequest\Requests\Item;

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
            'activity_code_id'=>'required',
            'account_code_id'=>'required',
            'donor_code_id'=>'nullable',
            'item_id'=>'required',
            'problem'=>'required',
            'estimated_cost'=>'required|numeric|max:99999999|min:0',
            'remarks'=>'required',
        ];
    }
}
