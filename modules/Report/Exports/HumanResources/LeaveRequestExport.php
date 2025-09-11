<?php

namespace Modules\Report\Exports\HumanResources;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Employee\Models\Employee;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveTypeRepository;

class LeaveRequestExport implements Responsable, ShouldAutoSize, WithStyles, WithStrictNullComparison, FromView
{
    use Exportable;

    private $office;
    private $fiscalYear;
    private $month;
    private $employee;
    private $fiscalYears;
    private $leaveRequests;
    private $leaveTypes;

    public function __construct($fiscalYear, $month, $office, $employee,private $requestDate)
    {
        $this->fiscalYear = $fiscalYear;
        $this->month = $month;
        $this->office = $office;
        $this->employee = $employee;
        $this->fiscalYears = app(FiscalYearRepository::class);
        $this->leaveRequests = app(LeaveRequestRepository::class);
        $this->leaveTypes = app(LeaveTypeRepository::class);
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'leave_request_report.xlsx';

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
        $fiscalYear = isset($this->fiscalYear) ? $this->fiscalYears->find($this->fiscalYear) : $this->fiscalYears->getCurrentFiscalYear();

        $query = $this->leaveRequests->select(['*'])
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereYear('request_date', $fiscalYear->start_date);
        if ($this->month) {
            $query->whereMonth('request_date', $this->month);
        }

        if ($this->requestDate) {
            $query->where('request_date', $this->requestDate);
        }

        if ($this->office) {
            $query->whereOfficeId($this->office);
        }
        if ($this->employee) {
            $query->where('requester_id', '=', $this->employee);
        }
        $leaveRequests = $query->orderBy('start_date', 'desc')->get();

        $array = [
            'leaveRequests' => $leaveRequests,
            'fiscalYear' => $fiscalYear->title,
            'month' => isset($this->month) ? date('F', mktime(0, 0, 0, $this->month, 10)) : null
        ];

        return view('Report::HumanResources.LeaveRequest.export', $array);
    }
}
