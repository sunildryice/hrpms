<?php

namespace Modules\EmployeeAttendance\Models;

use App\Traits\ModelEventLogger;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Master\Models\Office;

class AttendanceDetail extends Model
{
    use ModelEventLogger;

    // Database table used by the model.
    protected $table = 'attendance_details';

    // Attributes that are mass assignable.
    protected $fillable = [
        'attendance_master_id',
        'office_id',
        'weekend_type_id',
        'attendance_date',
        'checkin',
        'checkout',
        'worked_hours',
        'checkin_from',
        'checkout_from',
        'created_by',
        'updated_by'
    ];

    // Attributes hidden from models JSON or array.
    protected $hidden = [];

    // Turn the columns into carbon object.
    protected $dates = ['attendance_date', 'created_at', 'updated_at'];

    // Casting/Converting the columns into given data type
    protected $casts = [
        'checkin' => 'datetime',
        'checkout' => 'datetime'
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_master_id')->withDefault();
    }

    public function getCheckinTime()
    {
        return $this->checkin?->format('H:i');
    }

    public function getCheckoutTime()
    {
        return $this->checkout?->format('H:i');
    }

    public function getWorkedHours()
    {
        return round($this->worked_hours, 2) ?? 0;
    }

    public function getTotalWorkedHours()
    {
        return DB::table('attendance_details')->where('attendance_master_id', $this->attendance_master_id)->sum('worked_hours');
    }

    public function getChargedHours()
    {
        return round($this->charged_hours, 2) ?? 0;
    }

    public function getUnrestrictedHours()
    {
        return round($this->unrestricted_hours, 2) ?? 0;
    }

    public function getTotalUnrestrictedHours()
    {
        $hours = DB::table('attendance_details')->where('attendance_master_id', $this->attendance_master_id)->sum('unrestricted_hours');
        return round($hours, 2);
    }

    public function getChargedAndUnrestrictedHoursSum()
    {
        $charged_hours = $this->getChargedHours();
        $unrestricted_hours = $this->getUnrestrictedHours();

        return round($charged_hours + $unrestricted_hours, 2);
    }

    public function getTotalChargedHours()
    {
        $totalChargedHours = DB::table('attendance_details')->where('attendance_master_id', $this->attendance_master_id)->sum('charged_hours');
        $totalUnrestrictedHours = DB::table('attendance_details')->where('attendance_master_id', $this->attendance_master_id)->sum('unrestricted_hours');
        return $totalChargedHours + $totalUnrestrictedHours;
    }

    public function getDonorCharges()
    {
        $totalCharge = $this->getTotalChargedHours();

        $charges = DB::table('attendance_detail_donors')
            ->join('attendance_details', 'attendance_detail_donors.attendance_detail_id', '=', 'attendance_details.id')
            ->where('attendance_details.attendance_master_id', $this->attendance_master_id)
            ->select('attendance_detail_donors.*')
            ->select(DB::raw('donor_id as donor'), DB::raw('sum(attendance_detail_donors.worked_hours) as charged_hours'))
            ->groupBy('donor_id')
            ->get()
            ->map(function ($item) use ($totalCharge) {
                return [
                    'donor_id' => $item->donor,
                    'charged_hours' => $item->charged_hours,
                    'charged_percentage' => round(($item->charged_hours / $totalCharge) * 100, 2)
                ];
            });

        return $charges;
    }

    public function getTotalUnrestrictedPercentage()
    {
        $totalCharge = $this->getTotalChargedHours();
        $totalUnrestrictedHours = DB::table('attendance_details')->where('attendance_master_id', $this->attendance_master_id)->sum('unrestricted_hours');
        return round(($totalCharge == 0 ? 0 : $totalUnrestrictedHours / $totalCharge) * 100, 2);
    }

    public function getTotalChargedPercentage()
    {
        $totalCharge = $this->getTotalChargedHours();
        $donor_charges = $this->getDonorCharges();
        $total_unrestricted_hours = DB::table('attendance_details')->where('attendance_master_id', $this->attendance_master_id)->sum('unrestricted_hours');

        $hours_total = 0;

        foreach ($donor_charges as $donor_charge) {
            $hours_total += isset($donor_charge['charged_hours']) ? $donor_charge['charged_hours'] : 0;
        }

        $hours_total += $total_unrestricted_hours;

        return round(($totalCharge == 0 ? 0 : $hours_total / $totalCharge) * 100, 2);
    }

}
