<?php

namespace Modules\Tracker\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Project\Models\Project;

class HrEvent extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'hr_events';

    protected $fillable = [
        'project_id',
        'event_date',
        'event_type',
        'vacancy_for_positions',
        'total_applicants',
        'male_shortlisted',
        'female_shortlisted',
        'male_recruited',
        'female_recruited',
        'orientation_title',
        'male_participants',
        'female_participants',
    ];

    protected $casts = [
        'event_date'          => 'date',
        'total_applicants'    => 'integer',
        'male_shortlisted'    => 'integer',
        'female_shortlisted'  => 'integer',
        'male_recruited'      => 'integer',
        'female_recruited'    => 'integer',
        'male_participants'   => 'integer',
        'female_participants' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getProjectTitle()
    {
        return $this->project?->short_name ?? $this->project?->title ?? 'N/A';
    }

    public function getEventDate()
    {
        return $this->event_date?->toFormattedDateString();
    }

    public function getTotalShortlisted()
    {
        return $this->male_shortlisted + $this->female_shortlisted;
    }

    public function getTotalRecruited()
    {
        return $this->male_recruited + $this->female_recruited;
    }

    public function getTotalParticipants()
    {
        return $this->male_participants + $this->female_participants;
    }
}
