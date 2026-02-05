<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Project\Models\Enums\ActivityStatus;

class ProjectActivityStatusLog extends Model
{
    protected $table = 'project_activity_status_logs';

    protected $fillable = [
        'remarks',
        'changed_by',
        'project_activity_id',
        'old_status',
        'new_status',
        'attachment',
    ];

    protected $casts = [
        'old_status' => ActivityStatus::class,
        'new_status' => ActivityStatus::class,
    ];
}
