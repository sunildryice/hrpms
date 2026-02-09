<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkPlanDetailAttachment extends Model
{
    protected $table = 'work_plan_detail_attachments';

    protected $fillable = [
        'work_plan_detail_id',
        'title',
        'file_path',
        'created_by',
        'updated_by',
    ];

    public function workPlanDetail(): BelongsTo
    {
        return $this->belongsTo(WorkPlanDetail::class);
    }
}
