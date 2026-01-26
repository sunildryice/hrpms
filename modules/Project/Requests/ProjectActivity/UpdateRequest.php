<?php

namespace Modules\Project\Requests\ProjectActivity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'activity_stage_id' => 'required|exists:lkup_activity_stages,id',
            'activity_level' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:project_activities,id',
            'title' => 'required|string|max:255',
            'deliverables' => 'nullable|string',
            'budget_description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'completion_date' => 'nullable|date|after_or_equal:start_date',
            'members' => 'nullable|array|min:1',
            'members.*' => 'exists:users,id',
        ];
    }
}
