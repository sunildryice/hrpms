<?php

namespace Modules\GoodRequest\Requests\Item;

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
            'item_name'=>'required',
            'unit_id'=>'required|exists:lkup_measurement_units,id',
            'specification'=>'nullable',
            'quantity'=>'required|numeric',
        ];
    }
}
