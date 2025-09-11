<?php

namespace Modules\EmployeeAttendance\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\ProjectCode;

class AttendanceDetailDonor extends Model
{
    use HasFactory, ModelEventLogger;

    // Database table used by the model.
    protected $table = 'attendance_detail_donors';

    // Attributes that are mass assignable.
    protected $fillable = [
        'attendance_detail_id',
        'attendance_date',
        'donor_id',
        'worked_hours',
        'project_id',
        'activities',
    ];

    // Attributes hidden from models JSON or array.
    protected $hidden = [];

    // Turn the columns into carbon object.
    protected $dates = ['created_at', 'updated_at'];

    public function attendanceDetail()
    {
        return $this->belongsTo(AttendanceDetail::class, 'attendance_detail_id')->withDefault();
    }

    public function project()
    {
        return $this->belongsTo(ProjectCode::class, 'project_id', 'id')->withDefault();
    }

    public function donor()
    {
        return $this->belongsTo(DonorCode::class, 'donor_id')->withDefault();
    }

    public function getWorkedHours()
    {
        $hours =
            $this->donor->title == config('constant.UNRESTRICTED_DONOR') ?
                $this->attendanceDetail->unrestricted_hours :
            $this->worked_hours;

        return round($hours, 2);
    }
}
