<?php

namespace Modules\Project\Requests\ProjectActivity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:not_started,under_progress,no_required,completed',
            'remarks' => 'nullable|string',
            'status_date' => 'nullable|date',
            'documents' => ['array'],
            'documents.*.name' => ['required_with:documents.*.file', 'string'],
            'documents.*.file' => ['required_with:documents.*.name', 'file', 'mimes:pdf,jpeg,png', 'max:5120'],
        ];
    }
}
