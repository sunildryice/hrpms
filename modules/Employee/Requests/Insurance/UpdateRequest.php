<?php

namespace Modules\Employee\Requests\Insurance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return route('employees.edit', [$this->employee, 'tab'=>'insurance-details']);
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
            'payroll_fiscal_year_id'=>[
                'required',
                Rule::unique('employee_insurance')->ignore($this->insurance)->where(function($query) {
                    $query->where('employee_id', '=', $this->employee);
                }),
            ],
            'amount'=>'required|numeric',
            'paid_date'=>'required|date',
            'insurer'=>'required',
        ];
    }

    public function messages()
    {
        return [];
    }
}
