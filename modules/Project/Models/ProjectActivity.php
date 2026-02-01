<?php

namespace Modules\Project\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Project\Models\ActivityStage;
use Modules\Project\Models\Enums\ActivityLevel;
use Modules\Project\Models\ProjectActivityStatusLog;

class ProjectActivity extends Model
{
    protected $table = 'project_activities';

    protected $fillable = [
        'project_id',
        'activity_stage_id',
        'activity_level',
        'parent_id',
        'title',
        'deliverables',
        'budget_description',
        'status',
        'start_date',
        'completion_date',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'start_date',
        'completion_date',
    ];

    public function parent()
    {
        return $this->belongsTo(ProjectActivity::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProjectActivity::class, 'parent_id');
    }

    public function parentTheme()
    {
        $parentTheme = $this->parent()->where('activity_level', '=', ActivityLevel::Theme->value);

        return $parentTheme;
    }

    public function parentActivity()
    {
        return $this->parent()->where('activity_level', '=', ActivityLevel::Activity->value);
    }


    public function scopeThemes($query)
    {
        return $query->where('activity_level', ActivityLevel::Theme->value);
    }

    public function scopeWithFullHierarchy($query)
    {
        return $query->with([
            'children.activityChildren.subActivityChildren',
            'parentTheme',
            'parentActivity'
        ]);
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function activityChildren()
    {
        return $this->hasMany(ProjectActivity::class, 'parent_id')
            ->where('activity_level', ActivityLevel::Activity->value);
    }

    public function getAncestryAttribute()
    {
        $ancestry = collect();
        $activity = $this;

        while ($activity->parent_id) {
            $activity = $activity->parent;
            $ancestry->prepend($activity);
        }

        return $ancestry;
    }

    public function stage()
    {
        return $this->belongsTo(ActivityStage::class, 'activity_stage_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_activity_members', 'activity_id', 'user_id');
    }

    public function timesheets()
    {
        return $this->hasMany(ActivityTimeSheet::class, 'activity_id');
    }

    public function extensions()
    {
        return $this->hasMany(ProjectActivityExtension::class, 'activity_id')->orderBy('created_at', 'desc');
    }

    public function getDisplayCompletionDateAttribute()
    {
        $latestExtension = $this->extensions()->latest('created_at')->first();
        return $latestExtension ? $latestExtension->extended_completion_date : $this->completion_date;
    }

    public function statusLogs()
    {
        return $this->hasMany(ProjectActivityStatusLog::class, 'project_activity_id');
    }

    public function latestStatusLog()
    {
        return $this->hasOne(ProjectActivityStatusLog::class, 'project_activity_id')->latestOfMany();
    }

    public function parentName()
    {
        return $this->parent ? $this->parent->title : '';
    }

    public function stageName()
    {
        return $this->stage ? $this->stage->title : '';
    }

    public function memberNames()
    {
        return $this->members->pluck('full_name')->join(', ');
    }

    public function isUserAssignedToActivity($userId, $activityId)
    {
        return $this->members()->where('user_id', $userId)->where('activity_id', $activityId)->exists();
    }
}
