<?php

namespace Modules\TravelAuthorization\Requests;

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
        $valid_date = date('Y-m-d');
        return [
            'travel_type_id'=>'required',
            'purpose_of_travel'=>'required',
            'project_code_id'=>'required|exists:lkup_project_codes,id',
            'departure_date'=>'required|date|after_or_equal:'.$valid_date,
            'return_date'=>'required|date|after_or_equal:departure_date',
            'accompanying_staff'=>'nullable',
            'substitutes'=>'nullable|array',
            'final_destination'=>'required',
            'remarks'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'departure_date.after_or_equal'=>'Departure Date should not be before than today.',
            'return_date.after'=>'Return Date should not be before departure date.'
        ];
    }
}
