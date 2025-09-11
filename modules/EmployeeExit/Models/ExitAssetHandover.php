<?php

namespace Modules\EmployeeExit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\Employee\Models\Employee;

class ExitAssetHandover extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exit_asset_handovers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'approver_id',
        'handover_note_id',
        'status_id',
        'remarks',
        'created_by',
        'updated_by',

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the approver of exit asset handover
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

     /**
     * Get created by of the exit asset handover.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get employee of exit asset handover.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    /**
     * Get the exit handover note
     */
    public function exitHandOverNote()
    {
        return $this->belongsTo(ExitHandOverNote::class, 'handover_note_id');
    }


    /**
     * Get the logs for the advance request.
     */
    public function logs()
    {
        return $this->hasMany(ExitAssetHandoverLog::class, 'exit_asset_handover_id');
    }

    public function returnedLog()
    {
        return $this->hasOne(ExitAssetHandoverLog::class, 'exit_asset_handover_id')
            ->where('status_id', config('constant.RETURNED_STATUS'))
            ->latest()->withDefault();
    }

    public function approvedLog()
    {
        return $this->hasOne(ExitAssetHandoverLog::class, 'exit_asset_handover_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->latest();
    }

    public function submittedLog()
    {
        return $this->hasOne(ExitAssetHandoverLog::class, 'exit_asset_handover_id')
            ->where('status_id', config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    /**
     * Get the exit asset handover status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }
    public function getApproverName()
    {
        return $this->approver->getFullName();
    }
    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }
    public function getEmployeeName()
    {
        return $this->employee->getFullName();
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
