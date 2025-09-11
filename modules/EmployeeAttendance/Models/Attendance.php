<?php

namespace Modules\EmployeeAttendance\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\Department;
use Modules\Master\Models\Designation;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class Attendance extends Model
{
    use HasFactory, ModelEventLogger;

    // Database table used by the model.
    protected $table = 'attendance_masters';

    // Attributes that are mass assignable.
    protected $fillable = [
        'employee_id',
        'department_id',
        'designation_id',
        'office_id',
        'duty_station_id',
        'year',
        'month',
        'remarks',
        'donor_codes',
        'status_id',
        'requester_id',
        'reviewer_id',
        'approver_id',
        'updated_by'
    ];

    // Attributes hidden from models JSON or array.
    protected $hidden = [];

    // Turn the columns into carbon object.
    protected $dates = ['created_at', 'updated_at'];

    public function attendanceDetails()
    {
        return $this->hasMany(AttendanceDetail::class, 'attendance_master_id')
            ->orderBy('attendance_date', 'desc');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id')->withDefault();
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id')->withDefault();
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function dutyStation()
    {
        return $this->employee->latestTenure->dutyStation;
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function logs()
    {
        return $this->hasMany(AttendanceLog::class, 'attendance_master_id');
    }

    public function latestLog()
    {
        return $this->hasOne(AttendanceLog::class, 'attendance_master_id')->withDefault()->latest();
    }

    public function latestSubmittedLog()
    {
        return $this->hasOne(AttendanceLog::class, 'attendance_master_id')->whereStatusId(config('constant.SUBMITTED_STATUS'))->latest()->withDefault();
    }

    public function latestReviewedLog()
    {
        return $this->hasOne(AttendanceLog::class, 'attendance_master_id')->whereStatusId(config('constant.VERIFIED_STATUS'))->latest()->withDefault();
    }

    public function latestApprovedLog()
    {
        return $this->hasOne(AttendanceLog::class, 'attendance_master_id')->whereStatusId(config('constant.APPROVED_STATUS'))->latest()->withDefault();
    }

    public function getSubmittedDate()
    {
        return $this->latestSubmittedLog?->created_at?->format('M d, Y');
    }

    public function getReviewedDate()
    {
        return $this->latestReviewedLog?->created_at?->format('M d, Y');
    }

    public function getApprovedDate()
    {
        return $this->latestApprovedLog?->created_at?->format('M d, Y');
    }

    public function getRequester()
    {
        return $this->requester->getFullName();
    }

    public function getReviewer()
    {
        return $this->reviewer->getFullName();
    }

    public function getApprover()
    {
        return $this->approver->getFullName();
    }

    public function getRequesterDesignation()
    {
        return $this->employee->latestTenure->getDesignationName();
    }

    public function getReviewerDesignation()
    {
        return $this->reviewer->employee->latestTenure->getDesignationName();
    }

    public function getApproverDesignation()
    {
        return $this->approver->employee->latestTenure->getDesignationName();
    }


    public function getYear()
    {
        return $this->year;
    }

    public function getMonth()
    {
        return date("F", mktime(0, 0, 0, $this->month, 10));

    }

    public function getYearMonth()
    {
        return $this->getMonth() . ', ' . $this->getYear();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getTotalChargedHours()
    {
        $totalHour = $totalFraction = 0;
        foreach ($this->attendanceDetails as $detail) {
            $hour = intval($detail->worked_hours);
            $fraction = $detail->worked_hours - $hour;
            $totalHour += $hour;
            $totalFraction += $fraction;
        }
        $totalMinutes = round($totalFraction, 2) * 100;
        $fractionHour = intval($totalMinutes / 60);
        $totalHour += $fractionHour;
        $totalFraction = floatval(($totalMinutes - $fractionHour * 60) / 100);
        return $totalHour + $totalFraction;
    }

    public function getTotalWorkedHours()
    {
        $totalHour = $totalFraction = 0;
        foreach ($this->attendanceDetails as $detail) {
            $hour = intval($detail->worked_hours);
            $fraction = $detail->worked_hours - $hour;
            $totalHour += $hour;
            $totalFraction += $fraction;
        }
        $totalMinutes = round($totalFraction, 2) * 100;
        $fractionHour = intval($totalMinutes / 60);
        $totalHour += $fractionHour;
        $totalFraction = floatval(($totalMinutes - $fractionHour * 60) / 100);
        return $totalHour + $totalFraction;
    }

    public function getDonorCharges()
    {
        $return = [];
        $totalHours = $this->getTotalWorkedHours();
        $totalWorkedHours = (int)$totalHours;
        $totalFractionMinutes = 100 * ($totalHours - $totalWorkedHours);
        $totalWorkedMinutes = $totalFractionMinutes + $totalWorkedHours * 60;

        foreach (explode(',', $this->donor_codes) as $donorCode) {
            $donorCharges = AttendanceDetailDonor::where('donor_id', $donorCode)
                ->whereHas('attendanceDetail', function ($q) {
                    $q->where('attendance_master_id', $this->id);
                })->get();

            $totalHour = $totalFraction = 0;
            foreach ($donorCharges as $detail) {
                $hour = intval($detail->worked_hours);
                $fraction = $detail->worked_hours - $hour;
                $totalHour += $hour;
                $totalFraction += $fraction;
            }
            $totalMinutes = round($totalFraction, 2) * 100;
            $fractionHour = intval($totalMinutes / 60);
            $totalHour += $fractionHour;
            $totalFraction = floatval(($totalMinutes - $fractionHour * 60) / 100);
            $totalDonorCharges = $totalHour + $totalFraction;

            $totalDonorHours = (int)$totalDonorCharges;
            $totalFractionDonorMinutes = 100 * ($totalDonorCharges - $totalDonorHours);
            $totalDonorMinutes = $totalFractionDonorMinutes + $totalDonorHours * 60;

            if ($totalWorkedMinutes != 0) {
                $charged_percentage = round(($totalDonorMinutes / $totalWorkedMinutes) * 100, 2);
            } else {
                $charged_percentage = 0;
            }

            if ($totalDonorCharges) {
                $return[] = [
                    'donor_id' => $donorCode,
                    'charged_hours' => $totalDonorCharges,
                    'charged_percentage' => $charged_percentage
                ];
            }
        }
        return $return;
    }

    public function getTotalSummary()
    {
        $totalWorkedHour = $totalWorkedFraction = $totalWorkedMinutes = 0;
        foreach ($this->attendanceDetails as $detail) {
            $hour = intval($detail->worked_hours);
            $fraction = $detail->worked_hours - $hour;
            $totalWorkedHour += $hour;
            $totalWorkedFraction += $fraction;
            $totalWorkedMinutes += $hour * 60 + $fraction * 100;
        }
        $totalMinutes = round($totalWorkedFraction, 2) * 100;
        $fractionWorkedHour = intval($totalMinutes / 60);
        $totalWorkedHour += $fractionWorkedHour;
        $totalWorkedFraction = floatval(($totalMinutes - $fractionWorkedHour * 60) / 100);
        $totalWorkedHours = $totalWorkedHour + $totalWorkedFraction;

        $totalUnrestrictedHour = $totalUnrestrictedFraction = $totalUnrestrictedMinutes = 0;
        foreach ($this->attendanceDetails as $detail) {
            $unrestrictedHour = intval($detail->unrestricted_hours);
            $unrestrictedFraction = $detail->unrestricted_hours - $unrestrictedHour;
            $totalUnrestrictedHour += $unrestrictedHour;
            $totalUnrestrictedFraction += $unrestrictedFraction;
            $totalUnrestrictedMinutes += $unrestrictedHour * 60 + $unrestrictedFraction * 100;
        }
        $unrestrictedMinutes = round($totalUnrestrictedFraction, 2) * 100;
        $unrestrictedFractionHour = intval($unrestrictedMinutes / 60);
        $totalUnrestrictedHour += $unrestrictedFractionHour;
        $totalUnrestrictedFraction = floatval(($unrestrictedMinutes - $unrestrictedFractionHour * 60) / 100);
        $totalUnrestrictedCharges = $totalUnrestrictedHour + $totalUnrestrictedFraction;

        if ($totalWorkedMinutes != 0) {
            $unrestrictedPercentage = round(($totalUnrestrictedMinutes / $totalWorkedMinutes) * 100, 2);
        } else {
            $unrestrictedPercentage = 0;
        }

        return collect([
            'total_worked_hours' => $totalWorkedHours,
            'total_charged_hours' => $totalWorkedHours,
            'total_unrestricted_hours' => $totalUnrestrictedCharges,
            'total_unrestricted_percentage' => $unrestrictedPercentage,
            'total_charged_percentage' => 100,
        ]);
    }

    public function getTotalUnrestrictedHours()
    {
        $totalHour = $totalFraction = 0;
        foreach ($this->attendanceDetails as $detail) {
            $hour = intval($detail->unrestricted_hours);
            $fraction = $detail->unrestricted_hours - $hour;
            $totalHour += $hour;
            $totalFraction += $fraction;
        }
        $totalMinutes = round($totalFraction, 2) * 100;
        $fractionHour = intval($totalMinutes / 60);
        $totalHour += $fractionHour;
        $totalFraction = floatval(($totalMinutes - $fractionHour * 60) / 100);
        return $totalHour + $totalFraction;
    }

    public function getTotalUnrestrictedPercentage()
    {
        $totalCharge = $this->getTotalChargedHours();
        $totalUnrestrictedHours = DB::table('attendance_details')->where('attendance_master_id', $this->id)->sum('unrestricted_hours');
        return round(($totalCharge == 0 ? 0 : $totalUnrestrictedHours / $totalCharge) * 100, 2);
    }

    public function getTotalChargedPercentage()
    {
        $totalCharge = $this->getTotalChargedHours();
        $donor_charges = $this->getDonorCharges();
        $total_unrestricted_hours = DB::table('attendance_details')->where('attendance_master_id', $this->id)->sum('unrestricted_hours');

        $hours_total = 0;

        foreach ($donor_charges as $donor_charge) {
            $hours_total += isset($donor_charge['charged_hours']) ? $donor_charge['charged_hours'] : 0;
        }

        $hours_total += $total_unrestricted_hours;

        return round(($totalCharge == 0 ? 0 : $hours_total / $totalCharge) * 100, 2);
    }

    public function getLatestRemark()
    {
        return $this->logs->last()->log_remarks;
    }

    public function getYearMonthF(){
        return $this->year.'-'.date('F', mktime(0, 0, 0, $this->month, 10));
    }

    public function getDonorCodes()
    {
        if (isset($this->donor_codes)) {
            return explode(',', $this->donor_codes);
        }

        return [];
    }

}
