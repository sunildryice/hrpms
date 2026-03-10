<?php

namespace Modules\Project\Requests\WorkPlan;

use Illuminate\Contracts\Validation\Validator;
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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // the request can be used for both creating multiple entries (store) and
        // updating a single detail (update).  differentiate based on presence of
        // the `entries` array.
        if ($this->has('entries')) {
            return [
                'entries' => 'required|array|min:1',
                'entries.*.work_plan_date' => [
                    '   nullable',
                    'date',
                ],
                'entries.*.project_id' => 'required|exists:projects,id',
                'entries.*.activity_id' => 'nullable|exists:project_activities,id',
                'entries.*.planned_task' => 'required|string|max:500',
                'entries.*.members' => 'nullable|array|min:1',
                'entries.*.members.*' => 'exists:users,id',
                'reason' => 'nullable|string',
            ];
        }

        // single-detail update
        return [
            'work_plan_date' => [
                'nullable',
                'date',
            ],
            'project_id' => 'required|exists:projects,id',
            'activity_id' => 'nullable|exists:project_activities,id',
            'planned_task' => 'required|string|max:500',
            'members' => 'required|array|min:1',
            'members.*' => 'exists:users,id',
            'reason' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'entries.required' => 'Please add at least one work plan entry.',
            // 'entries.*.work_plan_date.required' => 'Date is required for each entry.',
            'entries.*.project_id.required' => 'Project is required for each entry.',
            // 'entries.*.activity_id.required' => 'Activity is required for each entry.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {

        return parent::failedValidation($validator);
    }
}
