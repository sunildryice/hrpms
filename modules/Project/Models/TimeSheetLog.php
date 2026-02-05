<?php

namespace Modules\Project\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class TimeSheetLog extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'timesheet_logs';

    protected $fillable = [
        'timesheet_id',
        'user_id',
        'log_remarks',
        'status_id',
    ];

    protected $hidden = [];

    public function timesheet()
    {
        return $this->belongsTo(TimeSheet::class, 'timesheet_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }
    
    public function getDesignation()
    {
        return $this->createdBy->employee->tenures()->where('created_at', '<=', $this->created_at)->latest()->first()->getDesignationName() ?? '';
    }
}