<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;
use Modules\Master\Models\Status;

class TimeSheet extends Model
{
    protected $table = 'timesheets';

    protected $fillable = [
        'year',
        'month',
        'start_date',
        'end_date',
        'month_name',
        'status_id',
        'approver_id',
        'requester_id',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $dates = [
        'start_date',
        'end_date',
    ];

    public function approvedLog()
    {
        return $this->hasOne(TimeSheetLog::class, 'timesheet_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function logs()
    {
        return $this->hasMany(TimeSheetLog::class, 'timesheet_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getStatus()
    {
        return $this->status->title;
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getStartDateFormattedAttribute()
    {
        return $this->start_date ? $this->start_date->format('d M Y') : '—';
    }

    public function getEndDateFormattedAttribute()
    {
        return $this->end_date ? $this->end_date->format('d M Y') : '—';
    }

    public function getRequesterNameAttribute()
    {
        return $this->requester->getFullName() ?? '—';
    }

    public function getApproverNameAttribute()
    {
        return $this->approver->getFullName() ?? '—';
    }

    public function isApproved(): bool
    {
        return $this->status_id === config('constant.APPROVED_STATUS');
    }
}
