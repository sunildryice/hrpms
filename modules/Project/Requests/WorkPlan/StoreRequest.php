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
            'project_id' => 'required|exists:projects,id',
            'activity_id' => 'required|exists:project_activities,id',
            'planned_task' => 'nullable|string',
            'reason' => 'nullable|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => 'Please select a project.',
            'activity_id.required' => 'Please select an activity.',
        ];
    }
}
