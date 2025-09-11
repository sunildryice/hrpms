<?php

namespace Modules\TravelAuthorization\Requests\Estimate;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
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
            'particulars' => 'required',
            'quantity' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'days' => 'required|numeric',
            'account_code_id' => 'required',
            'activity_code_id' => 'required',
            'donor_code_id' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Department name is required.'
        ];
    }
}

