<?php

namespace Modules\LeaveRequest\Models;

use App\Traits\ModelEventLogger;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\LeaveMode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class LeaveRequestDay extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'leave_request_days';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'leave_request_id',
        'leave_date',
        'leave_mode_id',
        'leave_duration',
        'leave_remarks',
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
     * Get the leave request of the leave day.
     */
    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id');
    }

    /**
     * Get the createdBy of the leave day.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the leave mode of the leave request day.
     */
    public function leaveMode()
    {
        return $this->belongsTo(LeaveMode::class, 'leave_mode_id')->withDefault();
    }

    public function getLeaveMode()
    {
        return $this->leaveMode->title;
    }

    public function getLeaveDate()
    {
        return Carbon::create($this->leave_date)->toFormattedDateString();
    }

    public function getLeaveTime()
    {
        try {
            [$start, $end] = explode('-', $this->leave_remarks);
            return (new DateTime($start))->format('h:i A') . ' - ' . (new DateTime($end))->format('h:i A');
        } catch (\Throwable) {
            return '';
        }
    }
}
