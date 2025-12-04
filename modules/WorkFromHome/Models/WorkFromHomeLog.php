<?php

namespace Modules\WorkFromHome\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;
use Modules\Master\Models\Status;

class WorkFromHomeLog extends Model
{
    use HasFactory;

    protected $table = 'work_from_home_logs';

    protected $fillable = [
        'work_from_home_id',
        'user_id',
        'log_remarks',
        'status_id',
        'original_user_id',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }


    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }
}
