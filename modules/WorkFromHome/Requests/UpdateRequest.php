<?php

namespace Modules\WorkFromHome\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\WorkFromHome\Enums\WorkFromHomeDays;
use Modules\WorkFromHome\Enums\WorkFromHomeTypes;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $typeOptions = implode(',', array_keys(WorkFromHomeTypes::options()));
        $WorkFromHomeDayOptions = implode(',', array_keys(WorkFromHomeDays::options()));

        return [
            'type'       => ['required', 'string', "in:{$typeOptions}"],
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'  => ['required', 'string'],
            'send_to' => ['required', 'integer', 'exists:users,id'],

            'deliverables'                  => ['required', 'array', 'min:1'],
            'deliverables.*.project_id'     => ['required', 'integer', 'exists:projects,id'],
            'deliverables.*.activity_id'           => ['required', 'string'],
            'deliverables.*.task'           => ['required', 'string'],
            'deliverables.*.date'           => ['required', 'date', 'after_or_equal:start_date', 'before_or_equal:end_date'],

            'date_types'                  => ['required', 'array'],
            'date_types.*.date'           => ['required', 'date', 'after_or_equal:start_date', 'before_or_equal:end_date'],
            'date_types.*.type'           => ['required', 'string', "in:{$WorkFromHomeDayOptions}"],

            'btn' => ['nullable', 'string', 'in:save,submit'],
        ];
    }

    public function attributes()
    {
        return [
            'type'                     => 'type',
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

            'deliverables.*.project_id.required' => 'Project is required.',
            'deliverables.*.project_id.exists'   => 'Please select a valid project.',
            'deliverables.*.activity_id.required' => 'Activity is required.',
            'deliverables.*.activity_id.exists'  => 'Please select a valid activity.',
            'deliverables.*.task.required'       => 'Task is required.',

            'deliverables.*.date.required'       => 'Date is required .',
            'deliverables.*.date.date'           => 'Date must be a valid date.',
        ];
    }
}
