<?php

namespace Modules\WorkFromHome\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'  => ['required', 'string'],
            'send_to' => ['required', 'integer', 'exists:users,id'],

            'deliverables'                  => ['required', 'array', 'min:1'],
            'deliverables.*.project_id'     => ['required', 'integer', 'exists:lkup_project_codes,id'],
            'deliverables.*.task'           => ['required', 'string'],

            'btn' => ['nullable', 'string', 'in:save,submit'],
        ];
    }


    public function attributes()
    {
        return [
            'start_date'               => 'start date',
            'end_date'                 => 'end date',
            'reason'                   => 'reason for off day work',
            'send_to'                  => 'approver',
            'deliverables'             => 'deliverables',
            'deliverables.*.project_id' => 'project',
            'deliverables.*.task'      => 'task',
        ];
    }

    public function messages()
    {
        return [
            'deliverables.required'              => 'Please add at least one deliverable.',
            'deliverables.array'                 => 'Deliverables must be a valid list.',
            'deliverables.min'                   => 'Please add at least one deliverable.',

            'deliverables.*.project_id.required' => 'Project is required for each deliverable.',
            'deliverables.*.project_id.exists'   => 'Please select a valid project.',
            'deliverables.*.task.required'       => 'Task is required for each deliverable.',
        ];
    }
}
