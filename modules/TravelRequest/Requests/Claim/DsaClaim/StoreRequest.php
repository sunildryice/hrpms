<?php

namespace Modules\TravelRequest\Requests\Claim\DsaClaim;

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
            'activities' => 'required',
            'departure_date' => 'required|date_format:Y-m-d',
            'arrival_date' => 'required|date_format:Y-m-d',
            'departure_place' => 'required',
            'arrival_place' => 'required',
            
            'breakfast' => 'required|numeric',
            'lunch' => 'required|numeric',
            'dinner' => 'required|numeric',
            'incident_cost' => 'required|numeric',

            'days_spent' => 'nullable|numeric',
            'total_dsa' => 'nullable|numeric',
            'daily_allowance' => 'nullable|numeric',

            'lodging_expense' => 'required|numeric',
            'other_expense' => 'required|numeric',
            'total_amount' => 'nullable|numeric',

            'travel_modes' => 'array',
            'travel_mode' => 'nullable',

            'remarks' => 'nullable',
            'attachment' => 'nullable|mimes:png,jpg,pdf|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'attachment.mimes' => 'Only png,jpg or pdf files are allowed.',
            'attachment.size' => 'Maximum allowed file size is 5MB.',
        ];
    }
}
