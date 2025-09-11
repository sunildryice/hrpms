<?php

namespace Modules\Report\Controllers\Finance;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\FundRequest\Models\FundRequest;
use Modules\FundRequest\Models\FundRequestActivity;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Report\Exports\Finance\MonthlyFundRequestExport;

class MonthlyFundRequestController extends Controller
{
    public function __construct(
        ActivityCodeRepository  $activityCodes,
        DistrictRepository      $districts,
        EmployeeRepository      $employees,
        FiscalYearRepository    $fiscalYears,
        OfficeRepository        $offices,
        UserRepository          $users
    )
    {
        $this->activityCodes    = $activityCodes;
        $this->districts        = $districts;
        $this->employees        = $employees;
        $this->fiscalYears      = $fiscalYears;
        $this->offices          = $offices;
        $this->users            = $users;
    }

    public function index(Request $request)
    {   
        $year       = $this->fiscalYears->getCurrentFiscalYearTitle();
        $month      = (now()->month)+1;
        // $officeId   = $this->offices->getOffices()->first()->id;
        // $districtId = $this->districts->getDistricts()->first()->id;
        $officeId   = '';
        // $districtId = '';
        $userId     = '';

        $requesterName  = '';
        $officeName     = '';
        // $districtName   = '';

        if ($request->has('year') && $request->year) {
            $year = $request->year;
        }
        if ($request->has('month') && $request->month) {
            $month = $request->month;
        }
        if ($request->has('office_id') && $request->office_id) {
            $officeId = $request->office_id;
            $officeName = $this->offices->find($officeId)->getOfficeName();
        }
        // if ($request->has('district_id') && $request->district_id) {
        //     $districtId = $request->district_id;
        //     $districtName = $this->districts->find($districtId)->getDistrictName();
        // }
        if ($request->has('requester') && $request->requester) {
            $userId = $request->requester;
            $requesterName = $this->users->find($userId)->getFullName();
        }

        $fundRequests = FundRequest::query();
        $fundRequests->where('year', $year)
                    ->where('month', $month)
                    ->where('status_id', config('constant.APPROVED_STATUS'));        
        if ($officeId != '') {
            $fundRequests->where('request_for_office_id', $officeId);
        }
        // if ($districtId != '') {
        //     $fundRequests->where('district_id', $districtId);
        // }
        if ($userId != '') {
            $fundRequests->where('created_by', $userId);
        }
        $fundRequests->get();
                                    
        $fundRequestActivities = FundRequestActivity::whereHas('fundRequest', function($q) use($year, $month, $officeId, $userId) {
                                    $q->newQuery();
                                    $q->where('year', $year)
                                    ->where('month', $month)
                                    ->where('status_id', config('constant.APPROVED_STATUS'));
                                    if ($officeId != '') {
                                        $q->where('request_for_office_id', $officeId);
                                    }
                                    // if ($districtId != '') {
                                    //     $q->where('district_id', $districtId);
                                    // }
                                    if ($userId != '') {
                                        $q->where('created_by', $userId);
                                    }
                                })->with('fundRequest')->get();
       
        $array = [
            'activityCodes'         => $this->activityCodes->getActiveActivityCodes(),
            // 'districtId'            => $districtId,
            // 'districts'             => $this->districts->getDistricts(),
            'employees'             => $this->employees->getActiveEmployees(),
            'fundRequests'          => $fundRequests,
            'fundRequestActivities' => $fundRequestActivities,
            'month'                 => $month,
            'months'                => Helper::getMonthArray(),
            'officeId'              => $officeId,
            'offices'               => $this->offices->getOffices(),
            'userId'                => $userId,
            'year'                  => $year,
            'years'                 => $this->fiscalYears->getFiscalYears(),
            'requesterName'         => $requesterName,
            'officeName'            => $officeName,
            // 'districtName'          => $districtName,
        ];

        return view('Report::Finance.MonthlyFundRequest.index', $array);
    }

    public function export(Request $request)
    {
        $year       = $request->year ? $request->year : '';
        $month      = $request->month ? $request->month : '';
        $office     = $request->office_id ? $request->office_id : '';
        // $district   = $request->district_id ? $request->district_id : '';
        $user       = $request->user_id ? $request->user_id : '';

        return new MonthlyFundRequestExport($year, $month, $office, $user);
    }
}
