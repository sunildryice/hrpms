<?php

namespace Modules\Project\Requests\TimeSheet;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'activity_id' => 'required|exists:project_activities,id',
            'timesheet_date' => 'required|date',
            'hours_spent' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'attachment' => 'nullable|mimes:png,jpg,pdf|max:5120',
        ];
    }
    public function messages()
    {
        return [
            'attachment.mimes' => 'Only png,jpg or pdf files are allowed.',
            'attachment.size' => 'Maximum allowed file size is 5MB.',
        ];
    }
}
