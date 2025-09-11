<?php

namespace Modules\Report\Exports\Finance;

use App\Helper;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Modules\FundRequest\Models\FundRequest;
use Modules\FundRequest\Models\FundRequestActivity;
use Modules\FundRequest\Repositories\FundRequestActivityRepository;
use Modules\Master\Models\District;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Privilege\Repositories\UserRepository;

class MonthlyFundRequestExport implements Responsable, ShouldAutoSize, WithStyles, WithStrictNullComparison, FromView
{
    use Exportable;

    private $year;
    private $month;
    private $office;
    private $district;
    private $user;

    public function __construct(
        $year, 
        $month,
        $office,
        $user
    )
    {
        $this->year                     = $year;
        $this->month                    = $month;
        $this->office                   = $office;
        $this->user                     = $user;
        $this->activityCodes            = app(ActivityCodeRepository::class);
        $this->fiscalYears              = app(FiscalYearRepository::class);
        $this->offices                  = app(OfficeRepository::class);
        $this->users                    = app(UserRepository::class);
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'monthly_fund_request_report.xlsx';
    
    /**
    * Optional Writer Type
    */
    private $writerType = Excel::XLSX;
    
    /**
    * Optional headers
    */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    private $counter = 1;

    // Custom function to calculate the total cell range.
    public function getCellRange(Worksheet $sheet)
    {
        $row_count = $sheet->getHighestDataRow(); // returns row count - int, eg: 1 or 2 or 3.
        $column_count = $sheet->getHighestDataColumn(); // returns last column - alphabet, eg: A or D or W.
        $start_cell = 'A10';
        $end_cell = $column_count.$row_count;
        return $start_cell.':'.$end_cell;   // returns cell range. example: 'A1:A7' or 'A1:W3'
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle(2)->getFont()->setBold(true);
        $sheet->getStyle(4)->getFont()->setBold(true);
        $sheet->getStyle(5)->getFont()->setBold(true);
        $sheet->getStyle(6)->getFont()->setBold(true);
        $sheet->getStyle(7)->getFont()->setBold(true);
        $sheet->getStyle(8)->getFont()->setBold(true);
        $sheet->getStyle(10)->getFont()->setBold(true);
       
        $sheet->getStyle($this->getCellRange($sheet))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => [
                        'rgb' => '808080'
                    ]
                ],
            ],
        ]);
    }

    public function view(): View
    {
        $year = $this->year;
        $month = $this->month;
        $officeId = $this->office;
        $districtId = $this->district;
        $userId     = $this->user;

        $requesterName  = '';
        $officeName     = '';

        $fundRequests = FundRequest::query();
        $fundRequests->where('year', $year)
                    ->where('month', $month)
                    ->where('status_id', config('constant.APPROVED_STATUS'));        
        if ($officeId != '') {
            $fundRequests->where('request_for_office_id', $officeId);
            $officeName = $this->offices->find($officeId)->getOfficeName();
        }
        // if ($districtId != '') {
        //     $fundRequests->where('district_id', $districtId);
        //     $districtName = $this->districts->find($districtId)->getDistrictName();
        // }
        if ($userId != '') {
            $fundRequests->where('created_by', $userId);
            $requesterName = $this->users->find($userId)->getFullName();
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
            'fundRequests'          => $fundRequests,
            'fundRequestActivities' => $fundRequestActivities,
            'month'                 => $month,
            'year'                  => $year,
            'requesterName'         => $requesterName,
            'officeName'            => $officeName,
        ];

        return view('Report::Finance.MonthlyFundRequest.export', $array);
    }
}
