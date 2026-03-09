<?php

namespace Modules\Project\Requests\ProjectActivity;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\Enums\ActivityLevel;

class UpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $level = $this->input('activity_level');

        if (in_array($level, [ActivityLevel::Activity->value, ActivityLevel::SubActivity->value], true)) {
            $parentId = $this->input('parent_id');

            if ($parentId) {
                $parent = ProjectActivity::query()->find($parentId);

                if ($parent?->activity_stage_id) {
                    $this->merge([
                        'activity_stage_id' => $parent->activity_stage_id,
                    ]);
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            'activity_stage_id' => 'required|exists:lkup_activity_stages,id',
            'activity_level' => 'required|string|max:255',
            'parent_id' => 'required_if:activity_level,activity,sub_activity|nullable|exists:project_activities,id',
            'title' => 'required|string|max:255',
            'deliverables' => 'nullable|string',
            'budget_description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'completion_date' => 'nullable|date|after_or_equal:start_date',
            'members' => 'nullable|array|min:1',
            'members.*' => 'exists:users,id',
            'status' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'activity_stage_id.required' => 'The activity stage field is required.',
            'activity_stage_id.exists' => 'The selected activity stage is invalid.',
            'activity_level.required' => 'The activity level field is required.',
            'activity_level.string' => 'The activity level must be a string.',
            'activity_level.max' => 'The activity level may not be greater than 255 characters.',
            'parent_id.required_if' => 'Parent activity is required for activity and sub activity.',
            'parent_id.exists' => 'The selected parent activity is invalid.',
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'deliverables.string' => 'The deliverables must be a string.',
            'budget_description.string' => 'The budget description must be a string.',
            'start_date.date' => 'The start date must be a valid date.',
            'completion_date.date' => 'The completion date must be a valid date.',
            'completion_date.after_or_equal' => 'The completion date must be after or equal to the start date.',
            'members.array' => 'The members field must be an array.',
            'members.min' => 'At least one member must be selected.',
            'members.*.exists' => 'One or more selected members are invalid.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status may not be greater than 255 characters.',
        ];
    }
}
