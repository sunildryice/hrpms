<?php

namespace Modules\Tracker\Requests\HrEvent;

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
            'event_date'            => 'required|date',
            'event_type'            => 'required|in:Recruitment,Orientation',
            'vacancy_for_positions' => 'required|string|max:255',
            'project_id'            => 'nullable|exists:projects,id',
            'total_applicants'      => 'nullable|integer|min:0',
            'male_shortlisted'      => 'nullable|integer|min:0',
            'female_shortlisted'    => 'nullable|integer|min:0',
            'male_recruited'        => 'nullable|integer|min:0',
            'female_recruited'      => 'nullable|integer|min:0',
            'orientation_title'     => 'nullable|string|max:255',
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
