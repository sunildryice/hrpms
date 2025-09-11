<?php

namespace Modules\Master\Requests\Vehicle;

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
            'office_id'=>'required|exists:lkup_offices,id',
            'vehicle_type_id'=>'required|exists:lkup_vehicle_types,id',
            'passenger_capacity'=>'required|numeric|min:1|max:99',
            'vehicle_number'=>[
                'required',
                Rule::unique('lkup_vehicles')->ignore($this->vehicle),
            ]
        ];
    }

    public function messages()
    {
        return [
            'vehicle_number.required'=>'Vehicle number is required.',
            'vehicle_number.unique'=>'Vehicle number is already taken.',
        ];
    }
}
