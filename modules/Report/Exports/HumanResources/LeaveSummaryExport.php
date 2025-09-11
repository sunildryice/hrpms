<?php

namespace Modules\Report\Exports\HumanResources;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Employee\Models\Employee;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveTypeRepository;

class LeaveSummaryExport implements Responsable, ShouldAutoSize, WithStyles, WithStrictNullComparison, FromView
{
    use Exportable;

    private $employeeCode;
    private $fiscalYear;
    private $month;

    public function __construct($employeeCode, $fiscalYear, $month)
    {
        $this->employeeCode = $employeeCode;
        $this->fiscalYear = $fiscalYear;
        $this->fiscalYears = app(FiscalYearRepository::class);
        $this->leaves = app(LeaveRepository::class);
        $this->leaveTypes = app(LeaveTypeRepository::class);
        $this->month = $month;
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'leave_record_summary_report.xlsx';

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

        $query = $this->leaves->select(['*'])
            ->whereYear('reported_date', $fiscalYear->start_date);
        if (isset($this->month)) {
            $query->whereMonth('reported_date', $this->month);
        }

        $leaves = $query->get();

        $data = Employee::query();
        $data->whereNotNull('activated_at');
        if (isset($this->employeeCode)) {
            $employeeCode = $this->employeeCode;
            $data->where('employee_code', $employeeCode);
        }
        $data->whereIn('id', $leaves->pluck('employee_id')->toArray());
        $filteredEmployees = $data->get();

        $leaveTypes = $this->leaveTypes->select(['*'])
            ->whereIn('id', $leaves->pluck('leave_type_id')->toArray())->get();

        $array = [
            'filteredEmployees' => $filteredEmployees,
            'leaveTypes' => $leaveTypes,
            'leaves' => $leaves,
            'fiscalYear' => $fiscalYear->title,
            'month' => isset($this->month) ? date('F', mktime(0, 0, 0, $this->month, 10)) : null
        ];

        return view('Report::HumanResources.LeaveSummary.export', $array);
    }
}
