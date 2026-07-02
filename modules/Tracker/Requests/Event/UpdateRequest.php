<?php

namespace Modules\Tracker\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Tracker\Models\Enums\Ethnicity;
use Modules\Tracker\Models\Enums\EventRole;

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
            'project_id'                    => 'required|exists:projects,id',
            'event_organized_by'            => 'required|in:internal,external',
            'event_type'                    => 'nullable|in:orientation,meeting,training,workshop,conference,other',
            'event_name'                    => 'required|string|max:255',
            'from_date'                     => 'required|date_format:Y-m-d',
            'to_date'                       => 'required|date_format:Y-m-d|after_or_equal:from_date',
            'country'                       => 'nullable|string|max:255',
            'province'                      => 'nullable|string|max:255',
            'district'                      => 'nullable|string|max:255',
            'city_local_level'              => 'nullable|string|max:255',
            'organized_by'                  => 'nullable|string|max:255',
            'role'                          => ['nullable', Rule::in(array_map(fn($r) => $r->value, EventRole::cases()))],
            'total_participants_government' => 'nullable|integer|min:0',
            'total_herdi_participants'      => 'nullable|integer|min:0',
            'total_other_participants'      => 'nullable|integer|min:0',
            'roaster_details'               => 'nullable|boolean',
            'roasters'                      => 'nullable|array',
            'roasters.*.id'                 => 'nullable|integer|exists:event_roasters,id',
            'roasters.*.organisation'       => 'required_with:roasters|in:HERDi,Government,Other',
            'roasters.*.organisation_name'  => 'nullable|string|max:255',
            'roasters.*.position'           => 'nullable|string|max:255',
            'roasters.*.ethnicity'          => ['nullable', Rule::in(array_map(fn($e) => $e->value, Ethnicity::cases()))],
            'roasters.*.gender'             => 'nullable|in:Male,Female,Other',
            'deleted_roasters'              => 'nullable|array',
            'deleted_roasters.*'            => 'integer|exists:event_roasters,id',
            'action_points'                 => 'nullable|string',
            'remarks'                       => 'nullable|string',
            'attachment'                    => 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx,xlsx|max:2048',
        ];
    }

    public function attributes()
    {
        return [
            'project_id'                    => 'Project',
            'event_organized_by'            => 'Event Organized By',
            'event_type'                    => 'Event Type',
            'event_name'                    => 'Event Name',
            'from_date'                     => 'From Date',
            'to_date'                       => 'To Date',
            'country'                       => 'Country',
            'province'                      => 'Province',
            'district'                      => 'District',
            'city_local_level'              => 'City / Local Level',
            'organized_by'                  => 'Organized By',
            'role'                          => 'Role',
            'total_participants_government' => 'Total Participants (Government)',
            'total_herdi_participants'      => 'Total HERDi Participants',
            'total_other_participants'      => 'Total Other Participants',
            'roaster_details'               => 'Roaster Details',
            'action_points'                 => 'Action Points',
            'remarks'                       => 'Remarks',
        ];
    }
}
