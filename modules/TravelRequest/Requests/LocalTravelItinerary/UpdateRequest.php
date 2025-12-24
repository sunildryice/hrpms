<?php

namespace Modules\TravelRequest\Requests\LocalTravelItinerary;

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
            'travel_mode' => 'required',
            'travel_date' => 'required|date',
            'number_of_travelers' => 'nullable|integer|min:0',
            'names_of_travelers.*.name' => 'required_if:number_of_travelers,>,0|string|max:255',
            'pickup_location' => 'nullable|string|max:255',
            'total_fare'=>'required|numeric|min:0.01',
            'remarks' => 'nullable',
            'attachment' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',

            // 'activity_code_id'=>'required|exists:lkup_activity_codes,id',
            // 'account_code_id'=>'required|exists:lkup_account_codes,id',
            // 'donor_code_id'=>'nullable',
            // 'purpose'=>'required|string|max:255',
            'departure_place'=>'nullable|string|max:255',
            'arrival_place'=>'nullable|string|max:255',
            // 'total_distance'=>'nullable|numeric|min:0.01',
        ];
    }

    public function messages()
    {
        return [
            'attachment.mimes' => 'Only png,jpg or pdf files are allowed.',
            'attachment.size' => 'Maximum allowed file size is 2MB.',
        ];
    }
}
