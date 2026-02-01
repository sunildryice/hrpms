<?php

namespace Modules\Project\Requests\ProjectActivityExtension;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'extended_completion_date' => 'required|date',
            'reason' => 'nullable|string',
        ];
    }
    public function messages()
    {
        return [
        ];
    }
}
