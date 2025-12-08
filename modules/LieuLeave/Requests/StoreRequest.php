<?php

namespace Modules\LieuLeave\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:1',
            'reason' => 'nullable|string|max:255',
        ];
    }
}
