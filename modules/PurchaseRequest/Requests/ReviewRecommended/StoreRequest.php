<?php

namespace Modules\PurchaseRequest\Requests\ReviewRecommended;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
    public function rules(Request $request)
    {
        return [
            'status_id'=>'required',
            'log_remarks'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'budget_description.required_if'=>'Budget description is required.',
            'status_id.required_if'=>'This field is required when submitted.',
            'log_remarks.required_if'=>'Remarks is required when submitted.',
        ];
    }
}
