<?php

namespace Modules\LeaveRequest\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Models\User;

class HolidayController extends Controller
{
    public function __construct(protected FiscalYearRepository $fiscalYear)
    {
    }

    public function index(Request $request)
    {
        $authUser = User::find(auth()->id());
        $employee = $authUser->employee;
        $office = $employee->office;

        $maxDate = $this->fiscalYear->getMaxEndDate();

        $start = Carbon::parse(now()->startOfDay());
        $end = Carbon::parse($maxDate)->endOfDay();

        $weekends = [];
        $holidayDatesWithTitle = [];

        //  Generate Weekends 
        $current = $start->copy();
        while ($current->lte($end)) {
            $isWeekend = false;

            if ($office->weekend_type == config('constant.Saturday+Sunday')) {
                if ($current->isSaturday() || $current->isSunday()) {
                    $isWeekend = true;
                }
            } else {
                if ($current->isSaturday()) {
                    $isWeekend = true;
                }
            }

            if ($isWeekend) {
                $weekends[] = $current->toDateString();
            }

            $current->addDay();
        }

        // Get Office Holidays 
        $holidaysQuery = $office->holidays()
            ->where('holiday_date', '>=', now()->toDateString())
            ->when(
                $employee->gender != config('constant.FEMALE'),
                function ($query) {
                    $query->where(function ($q) {
                        $q->whereNull('only_female')
                            ->orWhere('only_female', false);
                    });
                }
            );

        $holidayDatesWithTitle = $holidaysQuery
            ->pluck('title', 'holiday_date')
            ->toArray();

        $holidayDates = array_keys($holidayDatesWithTitle);

        //Both holidays and weekends
        // $disabledDates = array_unique(array_merge($holidayDates, $weekends));

        return response()->json([
            'disabled_dates' => $holidayDates,
            'holidays' => $holidayDatesWithTitle,
            'weekends' => $weekends,
        ]);
    }
}