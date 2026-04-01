<?php

namespace Modules\PerformanceReview\Requests\PerformanceReview;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

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
        $baseRules = [
            'review_type_id'    => 'required',
            'review_from' => 'required|date',
            'review_to' => 'required|date|after:review_from',
            'deadline_date' => 'required|date',
             'fiscal_year_id'    => 'required',
        ];

        if ($request->employee_all == 'on') {
            $baseRules['employee_id'] = 'nullable';
            $baseRules['employee_all'] = 'required';
        } else {
            $baseRules['employee_id'] = 'required|exists:employees,id';
            $baseRules['employee_all'] = 'nullable';
        }

        // External Reviewer is required only for Annual (1) and Mid-Term (2) Review
        if (in_array($request->review_type_id, [1, 2])) {
            $baseRules['external_reviewer_id'] = 'required|exists:users,id';
        } else {
            $baseRules['external_reviewer_id'] = 'nullable|exists:users,id';
        }

        return $baseRules;
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'Please select an employee or check all employees.',
            'employee_all.required' => 'Please select an employee or check all employees.',
            'external_reviewer_id.required'=> 'External Reviewer is required.',
            'review_from.required' => '\'Review From\' date is required.',
            'review_to.required' => '\'Review To\' date is required.',
            'fiscal_year_id' => 'Fical year is required.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'employee_id' => 'Employee Name',
            'review_type_id' => 'Review Type',
            'review_from' => 'Review From date',
            'review_to' => 'Review To date',
            'fiscal_year_id' => 'Fiscal Year',
        ];
    }
}

