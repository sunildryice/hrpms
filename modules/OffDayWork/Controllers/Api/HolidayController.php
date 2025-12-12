<?php

namespace Modules\OffDayWork\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Models\User;

class HolidayController extends Controller
{
    public function __construct(protected FiscalYearRepository $fiscalYear) {}

    public function index(Request $request)
    {
        $authUser = User::find(auth()->id());

        $maxDate = $this->fiscalYear->getMaxEndDate();

        $start = Carbon::parse(now());
        $end   = Carbon::parse($maxDate);

        $weekends = [];
        while ($start->lte($end)) {
            if ($start->isSunday() && $authUser->employee->office->weekend_type == config('constant.Saturday+Sunday')) {
                $weekends[] = $start->toDateString();
            }

            if ($start->isSaturday()) {
                $weekends[] = $start->toDateString();
            }

            $start->addDay();
        }

        $office = $authUser->employee->office;

        $holidays = $office->holidays()
            ->where('holiday_date', '>=', now())
            ->when(
                $authUser->employee->gender == config('constant.FEMALE'),
                function ($query) {},
                function ($query) {
                    // non-female: exclude female-only holidays
                    $query->where(function ($q) {
                        $q->whereNull('only_female')
                            ->orWhere('only_female', false);
                    });
                }
            )
            ->pluck('title', 'holiday_date')
            ->toArray();
        $enabledDates  = array_merge(array_keys($holidays), $weekends);


        return response()->json([
            'enabled_dates' => $enabledDates,
        ]);
    }
}
