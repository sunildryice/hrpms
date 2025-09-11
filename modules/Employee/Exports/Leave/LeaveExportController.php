<?php

namespace Modules\Employee\Exports\Leave;

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
use Modules\Master\Repositories\LeaveTypeRepository;

class LeaveExportController implements Responsable, ShouldAutoSize, WithStyles, WithStrictNullComparison, FromView
{
    use Exportable;

    private $employeeId;
    private $leaves;
    private $leaveTypes;
    private $reportedYear;

    public function __construct($employeeId, $reportedYear = null)
    {
        $this->leaves = app(LeaveRepository::class);
        $this->leaveTypes = app(LeaveTypeRepository::class);
        $this->employeeId = $employeeId;
        $this->reportedYear = $reportedYear ?? date('Y'); // Assign the current year if $reportedYear is null
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'individual_leave_request_report.xlsx';

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
        $sheet->getStyle(4)->getFont()->setBold(true);
        $sheet->getStyle(5)->getFont()->setBold(true);

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
        $leaves = $this->leaves->select('*')
            ->where('employee_id', $this->employeeId)
            ->whereYear('reported_date', $this->reportedYear)
            ->get();
        $leaveTypes = $this->leaveTypes->select(['*'])
            ->whereIn('id', $leaves->pluck('leave_type_id')->toArray())
            ->get();

        return view('Employee::Leave.export', [
            'record' => Employee::find($this->employeeId),
            'leaves' => $leaves,
            'leaveTypes' => $leaveTypes
        ]);
    }
}
