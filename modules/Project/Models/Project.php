<?php

namespace Modules\Project\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Privilege\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Project\Models\ProjectActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'title',
        'short_name',
        'description',
        'start_date',
        'completion_date',
        'team_lead_id',
        'focal_person_id',
        'activated_at',
        'show_pms_dashboard',
    ];

    protected $dates = [
        'start_date',
        'completion_date',
        'activated_at',
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id');
    }

    public function focalPerson()
    {
        return $this->belongsTo(User::class, 'focal_person_id', 'id');
    }

    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id', 'id');
    }

    public function isFocalPerson($userId): bool
    {
        return $this->focal_person_id == $userId;
    }

    public function isTeamLead($userId): bool
    {
        return $this->team_lead_id == $userId;
    }

    public function isActivityMember($userId): bool
    {
        return $this->activities()
            ->whereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->exists();
    }

    public function isProjectMember($userId): bool
    {
        return $this->members()
            ->where('user_id', $userId)
            ->exists();
    }

    public function allMembers()
    {
        return collect()
            ->merge($this->members()->get())
            ->when($this->focalPerson, fn($c) => $c->push($this->focalPerson))
            ->when($this->teamLead, fn($c) => $c->push($this->teamLead))
            ->unique('id')
            ->values();
    }

    public function stages()
    {
        return $this->belongsToMany(ActivityStage::class, 'project_activity_stages', 'project_id', 'activity_stage_id');
    }

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class, 'project_id', 'id');
    }

    public function assignedActivities()
    {
        return $this->hasManyThrough(
            ProjectActivity::class,
            User::class,
            'user_id',
            'project_id',
            'id',
            'project_id'
        );
    }

    public function getFormattedCompletionDateAttribute()
    {
        return $this->completion_date ? $this->completion_date->format('M d, Y') : '';
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('M d, Y') : '';
    }

    public function getProjectNameWithShortName()
    {
        return $this->title . ' (' . $this->short_name . ')';
    }

    public function getActiveStatus()
    {
        return $this->activated_at ? 'Active' : 'Inactive';
    }
}
