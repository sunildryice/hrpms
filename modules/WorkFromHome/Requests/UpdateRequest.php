<?php

namespace Modules\WorkFromHome\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'project_id' => 'required|exists:lkup_project_codes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
            'send_to' => 'required|exists:users,id',
            'deliverables' => 'required|array|min:1',
            'deliverables.*' => 'required|string|max:255',
            'btn' => 'required|string'
        ];
    }
}
