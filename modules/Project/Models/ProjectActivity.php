<?php

namespace Modules\Project\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Project\Models\ActivityStage;

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
}
