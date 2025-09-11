<?php

namespace Modules\DistributionRequest\Requests\Handover;

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
            'local_level_id'=>'required',
            'to_name'=>'required',
            'letter_body'=>'required',
            'date_of_handover'=>'required|date',
            'cc_name'=>'nullable',
            'approver_id'=>'required_if:btn,submit',
            'receiver_id' => 'required_if:btn,submit',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'local_level_id.required'=>"The local level is required.",
            'approver_id.required_if'=>"The approver is required when submitted.",
            'receiver_id.required_if' => 'The receiver is required when submitted.'

        ];
    }
}
