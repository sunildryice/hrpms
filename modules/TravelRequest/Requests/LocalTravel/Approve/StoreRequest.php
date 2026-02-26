<?php

namespace Modules\TravelRequest\Requests\LocalTravel\Approve;

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
            'status_id'=>'required',
            'recommended_to'=>'required_if:status_id,4',
            'log_remarks'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
