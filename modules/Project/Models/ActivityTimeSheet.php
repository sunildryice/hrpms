<?php 

namespace Modules\Project\Models;

use Modules\Privilege\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityTimeSheet extends Model
{
    use HasFactory;

    protected $table = 'project_activity_timesheet';

    protected $fillable = [
        'project_id',
        'activity_id',
        'timesheet_date',
        'hours_spent',
        'description',
        'attachment',
        'created_by',
        'updated_by',
    ];
    protected $dates = [
        'timesheet_date',
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
