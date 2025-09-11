<?php

namespace Modules\TransportationBill\Requests\Detail;

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
            'item_description'=>'required',
            'quantity'=>'required|numeric|min:1',
            'remarks'=>'nullable',
        ];
    }
}
