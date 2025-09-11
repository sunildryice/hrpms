<?php

namespace Modules\FundRequest\Requests;

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
        // $valid_date = date('Y-m',strtotime("+1 month"));
        $valid_date = date('Y-m',strtotime("-1 month"));

        return [
            // 'fiscal_year_id'=>'required|exists:lkup_fiscal_years,id',
            'year_month'=>'required|date_format:Y-m|after_or_equal:'.$valid_date,
            // 'district_id'=>'nullable|exists:lkup_districts,id',
            'request_for_office_id' => 'required|exists:lkup_offices,id',
            'project_code_id'=>'nullable|exists:lkup_project_codes,id',
            'remarks'=>'nullable',
            'attachment'=>'nullable|mimes:jpg,png,jpeg,pdf,xls,doc,xlsx,docx|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'year_month.after_or_equal'=>'The year month should not be past date.'
        ];
    }
}
