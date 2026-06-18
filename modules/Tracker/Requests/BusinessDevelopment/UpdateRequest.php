<?php

namespace Modules\Tracker\Requests\BusinessDevelopment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'thematic_area_id'  => 'nullable|exists:lkup_thematic_areas,id',
            'date'              => 'required|date',
            'call_name'         => 'nullable|string|max:255',
            'donor_name'        => 'nullable|string|max:255',
            'project_type'      => 'nullable|in:Research,Implementation',
            'status'            => 'nullable|in:Scanned,Submitted',
            'result'            => 'nullable|in:Rejected,Awaiting Result,Awarded',
        ];
    }

    public function attributes()
    {
        return [
            'thematic_area_id'  => 'Thematic Area',
            'date'              => 'Date',
            'call_name'         => 'Call Name',
            'donor_name'        => 'Donor Name',
            'project_type'      => 'Project Type',
            'status'            => 'Status',
            'result'            => 'Result',
        ];
    }
}
