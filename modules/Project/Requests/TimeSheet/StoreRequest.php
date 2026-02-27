<?php

namespace Modules\Project\Requests\TimeSheet;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'timesheet_date' => 'required|date',
            'entries' => 'required|array|min:1',
            'entries.*.project_id' => 'required|exists:projects,id',
            'entries.*.activity_id' => 'required|exists:project_activities,id',
            'entries.*.hours_spent' => 'required|numeric|min:0.01|max:24',
            'entries.*.description' => 'nullable|string',
            'entries.*.attachment' => 'nullable|mimes:png,jpg,pdf|max:5120',
            // global attachment removed
        ];
    }
    public function messages()
    {
        return [
            'entries.required' => 'Please add at least one timesheet entry.',
            'entries.array' => 'Invalid entries format.',
            'entries.*.project_id.required' => 'Project is required for each entry.',
            'entries.*.activity_id.required' => 'Activity/Sub‑activity is required for each entry.',
            'entries.*.hours_spent.required' => 'Hours spent is required for each entry.',
            'entries.*.hours_spent.numeric' => 'Hours spent must be a number.',
            'entries.*.hours_spent.between' => 'Hours spent should be between 0.01 and 24.',
            'entries.*.attachment.mimes' => 'Entry attachment must be png, jpg or pdf.',
            'entries.*.attachment.max' => 'Each entry attachment must not exceed 5MB.',
            'timesheet_date.required' => 'Timesheet date is required.',
            'timesheet_date.date' => 'Timesheet date must be a valid date.',
            // old global messages
            'attachment.mimes' => 'Only png,jpg or pdf files are allowed.',
            'attachment.size' => 'Maximum allowed file size is 5MB.',
        ];
    }
}
