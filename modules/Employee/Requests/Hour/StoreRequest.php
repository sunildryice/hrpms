<?php

namespace Modules\Employee\Requests\Hour;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return route('employees.edit', [$this->employee, 'tab'=>'hour-details']);
    }

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
            'start_date'=>'required|date',
            'end_date'=>'required|date',
            'work_percentile'=>'required|numeric|max:100',
            'remarks'=>'nullable',
        ];
    }
}
