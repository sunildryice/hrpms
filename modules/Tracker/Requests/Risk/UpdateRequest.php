<?php

namespace Modules\Tracker\Requests\Risk;

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
            'project_id'                => 'nullable|exists:projects,id',
            'date_added'                => 'required|date',
            'risk_name'                 => 'required|string|max:255',
            'risk_status_id'            => 'nullable|exists:lkup_risk_status,id',
            'risk_type_id'              => 'nullable|exists:lkup_risk_types,id',
            'risk_probability_id'       => 'nullable|exists:lkup_risk_probabilitis,id',
            'risk_impact_id'            => 'nullable|exists:lkup_risk_impacts,id',
            'risk_rating_id'            => 'nullable|exists:lkup_risk_ratings,id',
            'risk_response_type_id'     => 'nullable|exists:lkup_risk_response_types,id',
            'description_of_risk'       => 'nullable|string',
            'risk_owner'                => 'nullable|string|max:255',
            'mitigating_action'         => 'nullable|string',
            'whats_changed_this_quarter'=> 'nullable|string',
            'remarks'                   => 'nullable|string',
        ];
    }

    public function attributes()
    {
        return [
            'project_id'                => 'Project',
            'date_added'                => 'Date Added',
            'risk_name'                 => 'Risk Name',
            'risk_status_id'            => 'Risk Status',
            'risk_type_id'              => 'Risk Type',
            'risk_probability_id'       => 'Risk Probability',
            'risk_impact_id'            => 'Risk Impact',
            'risk_rating_id'            => 'Risk Rating',
            'risk_response_type_id'     => 'Risk Response Type',
            'description_of_risk'       => 'Description of Risk',
            'risk_owner'                => 'Risk Owner',
            'mitigating_action'         => 'Mitigating Action',
            'whats_changed_this_quarter'=> "What's Changed This Quarter",
            'remarks'                   => 'Remarks',
        ];
    }
}
