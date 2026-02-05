<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectActivityExtension extends Model
{
    protected $table = 'project_activity_extensions';

    protected $fillable = [
        'project_id',
        'activity_id',
        'extended_completion_date',
        'reason',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'extended_completion_date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function activity()
    {
        return $this->belongsTo(ProjectActivity::class, 'activity_id');

    }
}
