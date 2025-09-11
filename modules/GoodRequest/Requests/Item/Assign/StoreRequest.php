<?php

namespace Modules\GoodRequest\Requests\Item\Assign;

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
            'assigned_inventory_item_id'=>'required',
            'assigned_quantity'=>'required_without:assigned_asset_ids',
            'assigned_asset_ids'=>'required_without:assigned_quantity|array',
        ];
    }

    public function messages()
    {
        return [
            'assigned_inventory_item_id.required'=>'Item is required.',
            'assigned_quantity.required_without'=>'Quantity is required.',
            'assigned_asset_ids.required_without'=>'Asset is required.',
        ];
    }
}
