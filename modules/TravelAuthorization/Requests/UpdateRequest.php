<?php

namespace Modules\TravelAuthorization\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\TravelAuthorization\Models\TravelAuthorization;

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
            'office_id' => 'required',
            'objectives' => 'required',
            'outcomes' => 'required',
            'remarks' => 'nullable',
            'btn'=>'required',
            'approver_id' => 'required_if:btn,submit',
        ];
    }

    public function messages()
    {
        return [
            'departure_date.after_or_equal'=>'Departure Date should not be before than today.',
            'return_date.after'=>'Return Date should not be before departure date.',
            'approver_id.required'=>'Approver is required.',
        ];
    }
}
