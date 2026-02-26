<?php

namespace Modules\Project\Requests\WorkPlan;

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
        return [
            'work_plan_date' => 'required|date',
            'entries' => 'required|array|min:1',
            'entries.*.project_id' => 'required|exists:projects,id',
            'entries.*.activity_id' => 'required|exists:project_activities,id',
            'entries.*.planned_task' => 'nullable|string',
            'entries.*.members' => 'nullable|array',
            'entries.*.members.*' => 'exists:users,id',
            'reason' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'entries.required' => 'Please add at least one work plan entry.',
            'entries.*.project_id.required' => 'Project is required for each entry.',
            'entries.*.activity_id.required' => 'Activity is required for each entry.',
        ];
    }
}
