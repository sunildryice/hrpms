<?php

namespace Modules\TravelRequest\Requests\LocalTravel;

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
            'title' =>'required|string|max:191',
            'travel_request_id'=>'nullable|exists:travel_requests,id',
            'employee_id'=>'nullable',
            'remarks' =>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Purpose field is required.',
            'title.string'=>'Purpose field must contain text.',
            'title.max'=>'This field takes less than 191 characters.',
        ];
    }
}

