<?php

namespace Modules\WorkLog\Requests\WorkPlanDailyLog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $valid_date = date('Y-m-d');
        return [
            'activity_area_id'=>'nullable',
            // 'donor_id'=>'nullable',
            'priority_id'=>'nullable',
            'log_date'=>['required',
                            'date',
                            // 'before_or_equal:'.$valid_date,
                            Rule::unique('work_plan_daily_logs')
                            ->ignore($this->work_plan_daily_logs)
                            ->where('work_plan_id', $this->worklog)
                            // ->where('donor_id', $this->donor_id)
                        ],
            'major_activities'=>'required',
            'status'=>'nullable',
            'other_activities'=>'nullable',
            'remarks'=>'nullable',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'log_date.before'=>'Log Date should not be future date.'
        ];
    }
}
