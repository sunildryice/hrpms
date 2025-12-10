<?php

namespace Modules\TravelRequest\Requests\LocalTravel;

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
        return [
            'project_code_id'=>'required|exists:lkup_project_codes,id',
            'title' =>'required|string|max:191',
            'employee_id' => 'nullable',
            'travel_request_id'=>'nullable|exists:travel_requests,id',
            'remarks' =>'nullable',
            'approver_id'=>'required_if:btn,submit',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Purpose field is required.',
            'title.string'=>'Purpose field must contain text.',
            'title.max'=>'This field takes less than 191 characters.',
            'approver_id.required_if'=>'Approver is required.',
        ];
    }
}

