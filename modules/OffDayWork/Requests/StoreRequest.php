<?php

namespace Modules\OffDayWork\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date'    => ['required', 'date'],
            'reason'  => ['required', 'string'],
            'send_to' => ['required', 'integer', 'exists:users,id'],

            'deliverables'                  => ['required', 'array', 'min:1'],
            'deliverables.*.project_id'     => ['required', 'integer', 'exists:projects,id'],
            'deliverables.*.activity_id'  => ['nullable', 'integer', 'exists:project_activities,id'],
            'deliverables.*.task'           => ['required', 'string'],

            'btn' => ['nullable', 'string', 'in:save,submit'],
        ];
    }


    public function attributes()
    {
        return [
            'date'                     => 'off day work date',
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
