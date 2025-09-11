<?php

namespace Modules\Master\Requests\Item;

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
            'inventory_category_id'=>'required|exists:lkup_inventory_categories,id',
            'title'=>'required|unique:lkup_items',
            'item_code'=>'required|unique:lkup_items',
            'units'=>'array',
        ];
    }

    public function messages()
    {
        return [
            'inventory_category_id.required'=>'Category is required.',
            'inventory_category_id.exists'=>'Category does not exists.',
            'title.required'=>'Item name is required.',
            'title.unique'=>'Item name is already taken.',
        ];
    }
}
