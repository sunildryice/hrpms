<?php

namespace Modules\Project\Models\Project;

use Illuminate\Database\Eloquent\Model;

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
        'end_date',
        'created_by',
        'updated_by',
    ];
}
