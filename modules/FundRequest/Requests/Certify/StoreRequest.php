<?php

namespace Modules\FundRequest\Requests\Certify;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
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
            'btn' => 'required',
            'surplus_deficit' => 'required',
            'estimated_surplus' => 'nullable|numeric',
            'status_id' => 'required_if:btn,submit',
            'reviewer_id' => 'required_if:status_id,' . config('constant.VERIFIED2_STATUS'),
            'log_remarks' => 'required_if:btn,submit',
        ];
    }

    public function messages()
    {
        return [
            'log_remarks.required'=>'Remarks is required.'
        ];
    }
}
