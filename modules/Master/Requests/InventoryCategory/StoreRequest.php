<?php

namespace Modules\Master\Requests\InventoryCategory;

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
            'inventory_type_id'=>'required|exists:lkup_inventory_types,id',
            'title' => 'required|unique:lkup_inventory_categories',
            'description'=>'nullable'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required'=>'Inventory category is required.',
            'title.unique'=>'Inventory category is already taken.',
        ];
    }
}
