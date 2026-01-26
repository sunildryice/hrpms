<?php

namespace Modules\Project\Requests\ActivityTimeSheet;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'timesheet_date' => 'required|date',
            'hours_spent' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }
}
