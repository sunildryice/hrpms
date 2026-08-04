<?php

namespace Modules\Tracker\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;
use Modules\Project\Models\Project;

class HrEvent extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'hr_events';

    protected $fillable = [
        'project_id',
        'event_date',
        'event_type',
        'recruitment_type',
        'recruitment_method',
        'vacancy_for_positions',
        'total_applicants',
        'male_shortlisted',
        'female_shortlisted',
        'total_recruited',
        'orientation_title',
        'male_participants',
        'female_participants',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'event_date'          => 'date',
        'total_applicants'    => 'integer',
        'male_shortlisted'    => 'integer',
        'female_shortlisted'  => 'integer',
        'total_recruited'     => 'integer',
        'male_participants'   => 'integer',
        'female_participants' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function recruitments()
    {
        return $this->hasMany(HrEventRecruitment::class, 'hr_event_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
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
        return ($this->male_shortlisted ?? 0) + ($this->female_shortlisted ?? 0);
    }

    public function getTotalRecruited()
    {
        return $this->total_recruited ?? 0;
    }

    public function getTotalParticipants()
    {
        return ($this->male_participants ?? 0) + ($this->female_participants ?? 0);
    }

    public function getCreatorName()
    {
        return $this->createdBy->getFullName();
    }
}
