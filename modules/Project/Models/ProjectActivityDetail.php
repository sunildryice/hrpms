<?php

namespace Modules\Project\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectActivityDetail extends Model
{
    use HasFactory;

    protected $table = 'project_activity_details';

    protected $fillable = [
        'project_activity_id',
        'key_accomplishment',
        'challenge',
        'lesson_learned',
        'created_by',
        'updated_by',
    ];

    public function projectActivity()
    {
        return $this->belongsTo(ProjectActivity::class, 'project_activity_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
