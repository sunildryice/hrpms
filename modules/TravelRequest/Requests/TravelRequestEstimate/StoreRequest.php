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
            'estimated_hotel_accommodation'=>'nullable|numeric',
            'estimated_airport_taxi'=>'nullable|numeric',
            'miscellaneous_amount'=>'nullable|numeric',
            'estimated_event_activities_cost'=>'nullable|numeric',
            'miscellaneous_remarks'=>'nullable|string',
            'total_amount'=>'nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Department name is required.'
        ];
    }
}

