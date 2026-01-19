<?php

namespace Modules\LieuLeave\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;

class LieuLeaveBalance extends Model
{

    protected $table = 'lieu_leave_balances';

    protected $fillable = [
        'user_id',
        'earned_date',
        'earned_month',
        'lieu_leave_request_id',
        'off_day_work_id',
        'expires_at',
    ];

    protected $dates = [
        // 'earned_date',
        'earned_month',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function request()
    {
        return $this->belongsTo(LieuLeaveRequest::class, 'lieu_leave_request_id');
    }

    public function leaveRequest()
    {
        return $this->hasOne(LieuLeaveRequest::class, 'lieu_leave_request_id');
    }
}
