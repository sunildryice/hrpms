<?php

namespace Modules\Contract\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AmendStoreRequest extends FormRequest
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
            'expiry_date'=>'required|date|after_or_equal:effective_date',
            'contract_amount'=>'required|numeric',
            'attachment'=>'nullable',
            'remarks'=>'nullable',
        ];
    }

    public function messages()
    {
        return [];
    }
}
