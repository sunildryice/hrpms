<?php

namespace Modules\Master\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'title'=>[
                'required',
                Rule::unique('lkup_items')->ignore($this->item),
            ],
            'item_code'=>[
                'required',
                Rule::unique('lkup_items')->ignore($this->item),
            ],
            'units'=>'array',
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Item name is required.',
            'title.unique'=>'Item name is already taken.',
            'item_code.required'=>'Item code is required.',
            'item_code.unique'=>'Item code is already taken.',
        ];
    }
}
