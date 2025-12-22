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
            'project_ids'      => ['required', 'array', 'min:1'],
            'project_ids.*'    => ['required', 'integer', 'exists:lkup_project_codes,id'],

            'date'             => ['required', 'date'],
            'reason'           => ['required', 'string'],
            'send_to'          => ['required', 'integer', 'exists:users,id'],

            'deliverables'     => ['required', 'array', 'min:1'],
            'deliverables.*'   => ['required', 'array', 'min:1'],
            'deliverables.*.*' => ['required', 'string', 'max:255'],

            'btn'              => ['nullable', 'string', 'in:save,submit'],
        ];
    }
}
