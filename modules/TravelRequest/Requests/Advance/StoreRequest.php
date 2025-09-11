<?php

namespace Modules\TravelRequest\Requests\Advance;

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
            // 'travel_request_id' => 'required|exists:travel_requests,id',
            'requested_advance_amount' => 'required|numeric',
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
