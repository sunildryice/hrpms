<?php

namespace Modules\LieuLeave\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class LieuLeaveRequestLog extends Model
{
    protected $table = 'lieu_leave_request_logs';

    protected $fillable = [
        'lieu_leave_request_id',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
