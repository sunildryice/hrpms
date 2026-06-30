<?php

namespace Modules\Tracker\Requests\HrEvent;

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

    protected function prepareForValidation()
    {
        if ($this->input('vacancy_for_positions') === null) {
            $this->merge(['vacancy_for_positions' => '']);
        }
        if ($this->input('orientation_title') === null) {
            $this->merge(['orientation_title' => '']);
        }
        if ($this->input('project_id') === null || $this->input('project_id') === '') {
            $this->merge(['project_id' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'event_date'            => 'required|date',
            'event_type'            => 'required|in:Recruitment,Orientation',
            'vacancy_for_positions' => 'required_if:event_type,Recruitment|string|max:255',
            'project_id'            => 'nullable|exists:projects,id',
            'total_applicants'      => 'nullable|integer|min:0',
            'male_shortlisted'      => 'nullable|integer|min:0',
            'female_shortlisted'    => 'nullable|integer|min:0',
            'male_recruited'        => 'nullable|integer|min:0',
            'female_recruited'      => 'nullable|integer|min:0',
            'orientation_title'     => 'required_if:event_type,Orientation|string|max:255',
            'male_participants'     => 'nullable|integer|min:0',
            'female_participants'   => 'nullable|integer|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'event_date'            => 'Event Date',
            'event_type'            => 'Event Type',
            'vacancy_for_positions' => 'Vacancy For Positions',
            'project_id'            => 'Project',
            'total_applicants'      => 'Total Applicants',
            'male_shortlisted'      => 'Male Shortlisted',
            'female_shortlisted'    => 'Female Shortlisted',
            'male_recruited'        => 'Male Recruited',
            'female_recruited'      => 'Female Recruited',
            'orientation_title'     => 'Orientation Title',
            'male_participants'     => 'Male Participants',
            'female_participants'   => 'Female Participants',
        ];
    }
}
