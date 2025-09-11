<?php

namespace Modules\AdvanceRequest\Requests\Verify;

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
            'outstanding_advance'=> 'nullable|min:0',
            'log_remarks'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'log_remarks.required'=>'Remarks is required.',
        ];
    }
}
