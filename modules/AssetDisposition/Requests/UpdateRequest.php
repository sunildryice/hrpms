<?php

namespace Modules\AssetDisposition\Requests;

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
            'office_id' => 'required|exists:lkup_offices,id',
            'disposition_type_id'=>'required|exists:lkup_disposition_types,id',
            'disposition_date'=>'required|date',
            'approver_id' => 'required_if:btn,submit',
            'asset_dispose.asset.*' => 'nullable',
            'asset_dispose.reason.*' => 'nullable',
            'btn' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'approver_id.required_if' => 'The approver field is required.',
        ];
    }
}
