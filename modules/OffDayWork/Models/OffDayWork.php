<?php

namespace Modules\OffDayWork\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class OffDayWork extends Model
{
    use HasFactory;

    protected $table = 'off_day_works';

    protected $fillable = [
        'requester_id',
        'approver_id',
        'date',
        'fiscal_year_id',
        'reason',
        'status_id',
    ];

    protected $casts = [
        'date' => 'date',
        'deliverables' => 'array',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function projects()
    {
        return $this->belongsToMany(ProjectCode::class, 'project_off_day_work', 'off_day_work_id', 'project_id')
            ->withPivot('deliverables')
            ->withTimestamps();
    }

    public function getProjectNames()
    {
        return $this->projects->pluck('short_name')->toArray();
    }

    public function logs()
    {
        return $this->hasMany(OffDayWorkLog::class, 'off_day_work_id');
    }

    public function getOffDayWorkDate()
    {
        return Carbon::parse($this->date)->format('M j, Y');
    }


    public function getRequestDate()
    {
        return Carbon::parse($this->request_date)->format('M j, Y');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
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


    public function getRequesterName()
    {

        return $this->requester->employee->getFullName();
    }

    public function getRequestId()
    {
        $offDayWorkNumber = $this->off_day_work_number ? 'ODW-' . $this->off_day_work_number : '';
        $fiscalYear = $this->fiscalYear ? '/' . substr($this->fiscalYear->title, 2) : '';

        return $offDayWorkNumber . $fiscalYear;
    }
}
