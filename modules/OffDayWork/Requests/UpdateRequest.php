<?php

namespace Modules\OffDayWork\Requests;


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
            'project_id'   => 'required|exists:lkup_project_codes,id',
            'date'   => 'required|date',
            'deliverables' => 'required|array|min:1',
            'reason'       => 'required|string',
            'send_to'  => 'required|exists:users,id',
            'btn' => 'nullable|string|in:save,submit'
        ];
    }
}
