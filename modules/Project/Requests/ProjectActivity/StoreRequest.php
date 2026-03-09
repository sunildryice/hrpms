<?php

namespace Modules\Project\Requests\ProjectActivity;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\Enums\ActivityLevel;

class StoreRequest extends FormRequest
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
            'parent_id.required_if' => 'Parent activity is required for activity and sub activity.',
        ];
    }
}
