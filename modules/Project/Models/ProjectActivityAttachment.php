<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;
use Modules\Project\Models\ProjectActivity;

class ProjectActivityAttachment extends Model
{
    protected $table = 'project_activity_attachments';

    protected $fillable = [
        'project_activity_id',
        'title',
        'file_path',
        'created_by',
        'updated_by',
    ];

    public function activity()
    {
        return $this->belongsTo(ProjectActivity::class, 'project_activity_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
