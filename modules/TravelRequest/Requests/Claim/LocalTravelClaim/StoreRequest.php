<?php

namespace Modules\TravelRequest\Requests\Claim\LocalTravelClaim;

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
            'purpose' => 'required',
            'travel_date' => 'required|date_format:Y-m-d',
            'departure_place' => 'required',
            'arrival_place' => 'required',
            'travel_fare' => 'nullable|numeric',
            'remarks' => 'nullable',
            'attachment' => 'nullable|mimes:png,jpg,pdf|max:2048',
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
