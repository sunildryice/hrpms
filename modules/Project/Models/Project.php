<?php

namespace Modules\Project\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;
use Modules\Project\Models\Project\ProjectActivity;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'completion_date',
        'team_lead_id',
        'focal_person_id',
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id');
    }

    public function stages()
    {
        return $this->belongsToMany(ActivityStage::class, 'project_activity_stages', 'project_id', 'activity_stage_id');
    }

    public function activities()
    {
        return $this->belongsToMany(ProjectActivity::class, 'project_activities', 'project_id', 'id');
    }
}
