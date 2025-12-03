<?php

namespace Modules\Employee\Requests\Tenure;

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
        return route('employees.edit', [$this->employee, 'tab' => 'tenure-details']);
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
            'office_id' => 'required|exists:lkup_offices,id',
            'designation_id' => 'required|exists:lkup_designations,id',
            'department_id' => 'required|exists:lkup_departments,id',
            'supervisor_id' => 'required|exists:employees,id|different:next_line_manager_id',
            'cross_supervisor_id' => 'nullable|exists:employees,id|different:supervisor_id',
            'next_line_manager_id' => 'nullable|exists:employees,id|different:cross_supervisor_id',
            'duty_station' => 'nullable|string',
            'duty_station_id' => 'nullable|exists:lkup_districts,id',
            'joined_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date',
            'remarks' => 'nullable',
        ];
    }
}
