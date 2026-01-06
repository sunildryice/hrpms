<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;

class ActivityStage extends Model
{
    protected $table = 'lkup_activity_stages';

    protected $fillable = [
        'title',
        'description',
        'activated_at',
        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
