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
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;

class ConsolidatedFundRequestExport implements Responsable, ShouldAutoSize, WithStyles, WithStrictNullComparison, FromView
{
    use Exportable;

    private $activityCodes;
    private $fiscalYears;
    private $fundRequestActivities;
    private $year;
    private $month;
    private $office_id;
    private $offices;

    public function __construct(
        $year, 
        $month,
        $office_id
    )
    {
        $this->year                     = $year;
        $this->month                    = $month;
        $this->office_id                = $office_id;
        $this->offices                  = app(OfficeRepository::class);
        $this->activityCodes            = app(ActivityCodeRepository::class);
        $this->fiscalYears              = app(FiscalYearRepository::class);
        $this->fundRequestActivities    = app(FundRequestActivityRepository::class);
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'consolidated_fund_request_report.xlsx';
    
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
        $start_cell = 'A1';
        $end_cell = $column_count.$row_count;
        return $start_cell.':'.$end_cell;   // returns cell range. example: 'A1:A7' or 'A1:W3'
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle(2)->getFont()->setBold(true);
        $sheet->getStyle(3)->getFont()->setBold(true);
        $sheet->getStyle(4)->getFont()->setBold(true);
        $sheet->getStyle(6)->getFont()->setBold(true);
       
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
        $officeIds = $this->office_id;

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
            'activityCodes'         => $activityCodes,
            'fundRequests'          => $fundRequests,
            'fundRequestActivities' => $fundRequestActivities,
            'month'                 => $month,
            'months'                => Helper::getMonthArray(),
            // 'offices'               => $this->offices->getActiveOffices(),
            'offices'               => $filteredOffices,
            'year'                  => $year,
            'years'                 => $this->fiscalYears->getFiscalYears(),
        ];

        return view('Report::Finance.ConsolidatedFundRequest.export', $array);
    }
}
