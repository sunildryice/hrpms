<?php

namespace Modules\Employee\Requests\Finance;

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
        return route('employees.edit', [$this->employee, 'tab'=>'finance-details']);
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
            'ssf_number'=>'required',
            'cit_number'=>'nullable',
            'pf_number'=>'nullable',
            'account_number'=>'required',
            'bank_name'=>'required',
            'branch_name'=>'required',
            'remote_category'=>'nullable',
        ];
    }

    public function messages()
    {
        return [];
    }
}
