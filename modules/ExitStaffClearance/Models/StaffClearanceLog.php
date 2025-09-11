<?php

namespace Modules\ExitStaffClearance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Modules\ExitStaffClearance\Models\StaffClearance;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class StaffClearanceLog extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'exit_staff_clearance_logs';

    protected $fillable = [
        'staff_clearance_id',
        'user_id',
        'original_user_id',
        'log_remarks',
        'status_id'
    ];

    protected $hidden = [];

    protected $dates = [];

    public function staffClearance()
    {
        return $this->belongsTo(StaffClearance::class, 'staff_clearance_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}