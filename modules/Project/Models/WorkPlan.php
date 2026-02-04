<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Carbon\Carbon;

class WorkPlan extends Model
{
    protected $table = 'work_plan';

    protected $fillable = [
        'employee_id',
        'from_date',
        'to_date',
    ];

    public static function isEditable($startDate)
    {
        $now = Carbon::now();
        $weekStart = Carbon::parse($startDate)->startOfDay();

        $daysToSaturday = 6 - $weekStart->dayOfWeek;
        $weekEnd = $weekStart->copy()->addDays($daysToSaturday)->endOfDay();

        // 1. Past Week
        if ($now->gt($weekEnd)) {
            return false;
        }

        // 2. Future Week
        if ($now->lt($weekStart)) {
            return true;
        }

        // 3. Current Week (Inside the range)
        // Editable ONLY up to Monday.
        return $now->dayOfWeekIso <= 1;
    }

    public static function isStatusUpdatable($startDate, $endDate = null)
    {
        $now = Carbon::now()->startOfDay();
        $start = Carbon::parse($startDate)->startOfDay();

        if ($endDate) {
            $end = Carbon::parse($endDate)->endOfDay();
        } else {
            // Fallback: Calculate end of the plan week (Saturday)
            $daysToSaturday = 6 - $start->dayOfWeek;
            $end = $start->copy()->addDays($daysToSaturday)->endOfDay();
        }

        // 1. Current Week: Now is within the plan duration
        if ($now->between($start, $end)) {
            return $now->isFriday();
        }

        // 2. Previous Week: Plan ended recently (within the last ~8 days to cover standard week transition)
        // If we are in the week immediately following the plan, we should be able to update it.
        if ($now->gt($end)) {
            // Using 8 days buffer to ensure any day in the current week can edit the immediate past week
            return $now->diffInDays($end) <= 8;
        }

        return false;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function details()
    {
        return $this->hasMany(WorkPlanDetail::class);
    }
}
