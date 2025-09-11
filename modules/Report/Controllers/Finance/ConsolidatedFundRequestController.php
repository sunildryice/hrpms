<?php

namespace Modules\Report\Controllers\Finance;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\FundRequest\Models\FundRequest;
use Modules\FundRequest\Models\FundRequestActivity;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Report\Exports\Finance\ConsolidatedFundRequestExport;
use Modules\Report\Exports\Finance\ConsolidatedFundRequestPrint;

class ConsolidatedFundRequestController extends Controller
{
    private $offices;
    private $activityCodes;
    private $fiscalYears;

    public function __construct(
        OfficeRepository       $offices,
        ActivityCodeRepository $activityCodes,
        FiscalYearRepository   $fiscalYears,
    )
    {
        $this->offices = $offices;
        $this->activityCodes = $activityCodes;
        $this->fiscalYears = $fiscalYears;
    }

    public function index(Request $request)
    {
        $year = $this->fiscalYears->getCurrentFiscalYearTitle();
        $month = (now()->month) + 1;
        $officeIds = [];

        if ($request->has('year') && $request->year) {
            $year = $request->year;
        }
        if ($request->has('month') && $request->month) {
            $month = $request->month;
        }

        if ($request->filled('office_id')) {
            $officeIds = $request->office_id;
        }

        $query = FundRequest::where('year', $year)
            ->where('month', $month)
            ->where('status_id', config('constant.APPROVED_STATUS'));
        
        if (in_array(0, $officeIds)) {
            $officeIds = $this->offices->get()->pluck('id')->toArray();
        }

        if ($officeIds) {
            $query->whereIn('request_for_office_id', $officeIds);
        }
        $fundRequests = $query->get();

        $query = FundRequestActivity::with('fundRequest')
            ->whereHas('fundRequest', function ($q) use ($year, $month, $officeIds) {
                $q->where('year', $year)
                    ->where('month', $month)
                    ->where('status_id', config('constant.APPROVED_STATUS'));
                if ($officeIds) {
                    $q->whereIn('request_for_office_id', $officeIds);
                }
            });
        $fundRequestActivities = $query->get();
        $activityCodeIds = $fundRequestActivities->pluck('activity_code_id')->toArray();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();

        $activityCodes = $activityCodes->filter(function ($act) use ($activityCodeIds) {
            return in_array($act->id, $activityCodeIds);
        })->values();

        $filteredOffices = (in_array(0, $officeIds) || count($officeIds) == 0) ?
            $this->offices->getActiveOffices() :
            $this->offices->whereIn('id', $officeIds)->whereNotNull('activated_at')->orderBy('office_name')->get();

        $array = [
            'activityCodes' => $activityCodes,
            'filteredOffices' => $filteredOffices,
            'fundRequests' => $fundRequests,
            'fundRequestActivities' => $fundRequestActivities,
            'month' => $month,
            'months' => Helper::getMonthArray(),
            'offices' => $this->offices->getActiveOffices(),
            'year' => $year,
            'years' => $this->fiscalYears->getFiscalYears(),
        ];

        return view('Report::Finance.ConsolidatedFundRequest.index', $array);
    }

    public function export(Request $request)
    {
        $year = $request->year ? $request->year : '';
        $month = $request->month ? $request->month : '';
        $office_id = $request->filled('office_id') ? $request->office_id : [];

        return new ConsolidatedFundRequestExport($year, $month, $office_id);
    }

    public function print(Request $request)
    {

        // $year = $request->year ? $request->year : '';
        // $month = $request->month ? $request->month : '';
        // $office_id = $request->filled('office_id') ? $request->office_id : [];
        // return new ConsolidatedFundRequestPrint($year, $month, $office_id);


        $year = $this->fiscalYears->getCurrentFiscalYearTitle();
        $month = (now()->month) + 1;
        $officeIds = [];

        if ($request->has('year') && $request->year) {
            $year = $request->year;
        }
        if ($request->has('month') && $request->month) {
            $month = $request->month;
        }

        if ($request->filled('office_id')) {
            $officeIds = $request->office_id;
        }

        $query = FundRequest::where('year', $year)
            ->where('month', $month)
            ->where('status_id', config('constant.APPROVED_STATUS'));

        if (in_array(0, $officeIds)) {
            $officeIds = $this->offices->get()->pluck('id')->toArray();
        }
    
        
        if ($officeIds) {
            $query->whereIn('request_for_office_id', $officeIds);
        }
        $fundRequests = $query->get();

        $query = FundRequestActivity::with('fundRequest')
            ->whereHas('fundRequest', function ($q) use ($year, $month, $officeIds) {
                $q->where('year', $year)
                    ->where('month', $month)
                    ->where('status_id', config('constant.APPROVED_STATUS'));
                if ($officeIds) {
                    $q->whereIn('request_for_office_id', $officeIds);
                }
            });
        $fundRequestActivities = $query->get();
        $activityCodeIds = $fundRequestActivities->pluck('activity_code_id')->toArray();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();

        $activityCodes = $activityCodes->filter(function ($act) use ($activityCodeIds) {
            return in_array($act->id, $activityCodeIds);
        })->values();

        $filteredOffices = (in_array(0, $officeIds) || count($officeIds) == 0) ?
            $this->offices->getActiveOffices() :
            $this->offices->whereIn('id', $officeIds)->whereNotNull('activated_at')->orderBy('office_name')->get();

        $array = [
            'activityCodes' => $activityCodes,
            'filteredOffices' => $filteredOffices,
            'fundRequests' => $fundRequests,
            'fundRequestActivities' => $fundRequestActivities,
            'month' => $month,
            'months' => Helper::getMonthArray(),
            'offices' => $this->offices->getActiveOffices(),
            'year' => $year,
            'years' => $this->fiscalYears->getFiscalYears(),
        ];

        return view('Report::Finance.ConsolidatedFundRequest.print', $array);        
    }
}
