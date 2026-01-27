<?php

namespace Modules\Project\Requests\TimeSheet;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
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
