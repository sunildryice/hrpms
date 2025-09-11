<?php

namespace Modules\MaintenanceRequest\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

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
        $rules = [
            'remarks'=>'nullable',
            'request_date'=>'required',
            'reviewer_id'=>'nullable',
            'approver_id'=>'required',
            'btn'=>'required',
        ];
        return $rules;
    }

    public function messages()
    {
        return [];
    }
}
