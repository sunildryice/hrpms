<?php

namespace Modules\MaintenanceRequest\Requests;

use Illuminate\Http\Request;
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
        $rules = [
            'request_date'=>'required',
            'reviewer_id'=>'nullable',
            'approver_id'=>'nullable',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'request_date.required'=>'Request date is required.',
        ];
    }
}
