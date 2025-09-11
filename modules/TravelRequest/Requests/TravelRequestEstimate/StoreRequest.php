<?php

namespace Modules\TravelRequest\Requests\TravelRequestEstimate;

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
            'estimated_dsa'=>'required|numeric',
            'estimated_air_fare'=>'nullable|numeric',
            'estimated_vehicle_fare'=>'nullable|numeric',
            'advance_amount'=>'nullable|numeric',
            'miscellaneous_amount'=>'nullable|numeric',
            'miscellaneous_remarks'=>'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Department name is required.'
        ];
    }
}

