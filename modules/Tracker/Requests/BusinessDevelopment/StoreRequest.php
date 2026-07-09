<?php

namespace Modules\Tracker\Requests\BusinessDevelopment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'thematic_area_id'   => 'nullable|exists:lkup_thematic_areas,id',
            'project_name'       => 'nullable|string|max:255',
            'date'               => 'required|date',
            'call_name'          => 'nullable|string|max:255',
            'url'                => 'nullable|string|max:500',
            'funding_agency'     => 'nullable|string|max:255',
            'contracting_agency' => 'nullable|string|max:255',
            'partnership_type'   => 'nullable|in:Consortium,Single Organization',
            'consortium_lead'    => 'nullable|string|max:255',
            'consortium_partners'=> 'nullable|string|max:255',
            'project_type'       => 'nullable|in:Research,Implementation',
            'status'             => 'nullable|in:Scanned,Submitted',
            'result'             => 'nullable|in:Rejected,Awaiting Result,Awarded',
            'remarks'            => 'nullable|string',
            'attachment'         => 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx,xlsx|max:2048',
        ];
    }

    public function attributes()
    {
        return [
            'thematic_area_id'   => 'Thematic Area',
            'project_name'       => 'Project Name',
            'date'               => 'Date',
            'call_name'          => 'Call Name',
            'url'                => 'URL',
            'funding_agency'     => 'Funding Agency',
            'contracting_agency' => 'Contracting Agency',
            'partnership_type'   => 'Partnership Type',
            'consortium_lead'    => 'Consortium Lead',
            'consortium_partners'=> 'Consortium Partners',
            'project_type'       => 'Project Type',
            'status'             => 'Status',
            'result'             => 'Result',
            'remarks'            => 'Remarks',
        ];
    }
}
