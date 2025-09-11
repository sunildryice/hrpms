<?php

namespace Modules\LeaveRequest\Requests\Review;

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
            'status_id' => 'required',
            'log_remarks' => 'required',
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
