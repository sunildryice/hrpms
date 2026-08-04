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
        if ($this->input('event_type') !== 'Recruitment') {
            $this->merge([
                'recruitment_type'   => null,
                'recruitment_method' => null,
            ]);
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
            'event_date'                     => 'required|date',
            'event_type'                     => 'required|in:Recruitment,Orientation',
            'recruitment_type'               => 'nullable|required_if:event_type,Recruitment|in:Direct Hire,Open Vacancy',
            'recruitment_method'             => 'nullable|required_if:event_type,Recruitment|in:Open Call,Headhunt,Direct Appointment',
            'vacancy_for_positions'          => 'required_if:event_type,Recruitment|string|max:255',
            'project_id'                     => 'nullable|exists:projects,id',
            'total_applicants'               => 'nullable|integer|min:0',
            'male_shortlisted'               => 'nullable|integer|min:0',
            'female_shortlisted'             => 'nullable|integer|min:0',
            'total_recruited'                => 'nullable|integer|min:0',
            'orientation_title'              => 'required_if:event_type,Orientation|string|max:255',
            'male_participants'              => 'nullable|integer|min:0',
            'female_participants'            => 'nullable|integer|min:0',
            'remarks'                        => 'nullable|string|max:500',
            'recruitments'                   => 'nullable|array',
            'recruitments.*.member_name'     => 'required_with:recruitments|string|max:255',
            'recruitments.*.gender'          => 'required_with:recruitments|in:Male,Female,Other',
            'recruitments.*.onboard_date'    => 'nullable|date',
            'recruitments.*.position'        => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'event_date'                     => 'Event Date',
            'event_type'                     => 'Event Type',
            'recruitment_type'               => 'Recruitment Type',
            'recruitment_method'             => 'Recruitment Method',
            'vacancy_for_positions'          => 'Vacancy For Positions',
            'project_id'                     => 'Project',
            'total_applicants'               => 'Total Applicants',
            'male_shortlisted'               => 'Male Shortlisted',
            'female_shortlisted'             => 'Female Shortlisted',
            'total_recruited'                => 'Total Recruited',
            'orientation_title'              => 'Orientation Title',
            'male_participants'              => 'Male Participants',
            'female_participants'            => 'Female Participants',
            'remarks'                        => 'Remarks',
            'recruitments.*.member_name'     => 'Recruitment Member Name',
            'recruitments.*.gender'          => 'Gender',
            'recruitments.*.onboard_date'    => 'Onboard Date',
            'recruitments.*.position'        => 'Position',
        ];
    }
}
