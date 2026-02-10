<?php

namespace Modules\Project\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Project\Models\ActivityStage;
use Modules\Project\Models\Enums\ActivityLevel;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Models\ProjectActivityStatusLog;
use Modules\Project\Models\ProjectActivityAttachment;

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
        'actual_start_date',
        'actual_completion_date',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'start_date',
        'completion_date',
        'actual_start_date',
        'actual_completion_date'
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
        return $this->hasMany(ProjectActivityExtension::class, 'activity_id')
            ->orderBy('created_at', 'desc');
    }

    public function latestExtension()
    {
        return $this->hasOne(ProjectActivityExtension::class, 'activity_id')
            ->orderBy('extended_completion_date', 'desc')
            ->first();
    }

    public function getDisplayCompletionDateAttribute()
    {
        $latestExtension = $this->extensions()->orderBy('extended_completion_date', 'desc')->first();
        return $latestExtension ? $latestExtension->extended_completion_date->format('M d, Y') : ($this->completion_date ? $this->completion_date->format('M d, Y') : "");
    }

    public function getDisplayExtendedCompletionDateAttribute()
    {
        $latestExtension = $this->extensions()->orderBy('extended_completion_date', 'desc')->first();
        return $latestExtension ? $latestExtension->extended_completion_date->format('M d, Y') . "<sub class=\"text-bold\"> (extended) </sub>" : ($this->completion_date ? $this->completion_date->format('M d, Y') : "");
    }

    public function getLatestExtensionAttribute()
    {
        return $this->extensions()->orderBy('extended_completion_date', 'desc')->first();
    }

    public function getEffectiveEndDateAttribute()
    {
        return $this->latest_extension?->extended_completion_date ?? $this->completion_date;
    }

    public function statusLogs()
    {
        return $this->hasMany(ProjectActivityStatusLog::class, 'project_activity_id');
    }

    public function latestStatusLog()
    {
        return $this->hasOne(ProjectActivityStatusLog::class, 'project_activity_id')->latestOfMany();
    }

    public function attachments()
    {
        return $this->hasMany(ProjectActivityAttachment::class, 'project_activity_id');
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

    public function statusBgColor()
    {
        return match ($this->status) {
            ActivityStatus::Completed->value => 'badge bg-success',
            ActivityStatus::UnderProgress->value => 'badge bg-warning',
            ActivityStatus::NotStarted->value => 'badge bg-orange text-white',
            ActivityStatus::NoRequired->value => 'badge bg-danger',
            default => 'badge bg-secondary',
        };
    }

    public function statusLabel()
    {
        return match ($this->status) {
            ActivityStatus::Completed->value => 'Completed',
            ActivityStatus::UnderProgress->value => 'Under Progress',
            ActivityStatus::NotStarted->value => 'Not Started',
            ActivityStatus::NoRequired->value => 'No Longer Required',
            default => '-',
        };
    }
}
