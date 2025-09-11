<?php

namespace Modules\DistributionRequest\Requests\Receive;

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
            'received_date'=>'required',
            'handover_date'=>'required_if:btn,submit',
            'receiver_remarks'=>'required',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'log_remarks.required'=>'Remarks is required.',
            'handover_date.required_if'=>'Handover date is required.',
        ];
    }
}
