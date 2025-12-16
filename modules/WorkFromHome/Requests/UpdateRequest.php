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
            'project_ids'      => ['required', 'array', 'min:1'],
            'project_ids.*'    => ['required', 'integer', 'exists:lkup_project_codes,id'],

            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after_or_equal:start_date'],
            'reason'           => ['required', 'string'],
            'send_to'          => ['required', 'integer', 'exists:users,id'],
            'btn'              => ['required', 'string'],

            'deliverables'     => ['required', 'array', 'min:1'],
            'deliverables.*'   => ['required', 'array', 'min:1'],
            'deliverables.*.*' => ['required', 'string', 'max:255'],
        ];
    }
}
