<?php

namespace Modules\TravelRequest\Requests\LocalTravelItinerary;

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
            'travel_mode'=>'required',
            'activity_code_id'=>'required|exists:lkup_activity_codes,id',
            'account_code_id'=>'required|exists:lkup_account_codes,id',
            'donor_code_id'=>'nullable',
            'travel_date'=>'required|date',
            'purpose'=>'required|string|max:255',
            'departure_place'=>'nullable|string|max:255',
            'arrival_place'=>'nullable|string|max:255',
            'total_distance'=>'nullable|numeric|min:0.01',
            'total_fare'=>'required|numeric|min:0.01',
            'remarks'=>'nullable',
        ];
    }
}
