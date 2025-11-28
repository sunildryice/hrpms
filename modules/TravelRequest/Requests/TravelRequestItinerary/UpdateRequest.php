<?php

namespace Modules\TravelRequest\Requests\TravelRequestItinerary;

use Illuminate\Foundation\Http\FormRequest;
use Modules\TravelRequest\Models\TravelRequest;

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
        $travelRequest = TravelRequest::find($this->travelRequest);
        return [
            'activity_code_id' => 'required|exists:lkup_activity_codes,id',
            'travel_modes' => 'array',
            'travel_mode' => 'nullable',
            'departure_date' => 'required|date_format:Y-m-d H:i|after_or_equal:' . $travelRequest->departure_date->format('Y-m-d H:i') . '|before_or_equal:arrival_date',//.$this->arrival_date,
            'arrival_date' => 'required|date_format:Y-m-d H:i|before_or_equal: ' . $travelRequest->return_date->format('Y-m-d H:i') . '|after_or_equal:departure_date',//.$this->departure_date,
            'departure_place' => 'required',
            'arrival_place' => 'required',
            'description' => 'required',
            // 'account_code_id' => 'required|exists:lkup_account_codes,id',
            // 'donor_code_id' => 'nullable|exists:lkup_donor_codes,id',
            // 'dsa_category_id' => 'required',
            // 'dsa_unit_price' => 'required|numeric',
            // 'charging_office_id' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [];
    }
}
