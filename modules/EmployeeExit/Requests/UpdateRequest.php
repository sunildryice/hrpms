<?php

namespace Modules\EmployeeExit\Requests;

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
            'last_duty_date'=>'required|date',
            'resignation_date'=>'nullable|date',
            'btn'=>'nullable'
        ];
    }
}
