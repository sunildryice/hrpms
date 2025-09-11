<?php

namespace Modules\Report\Exports\HumanResources;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Modules\EmployeeExit\Models\ExitHandOverNote;

class EmployeeExitClearanceExport implements Responsable, ShouldAutoSize, WithStyles, WithStrictNullComparison, FromView
{

    public function __construct($startDate, $endDate, $employee, $designation, $dutyStation)
    {
        $this->startDate    = $startDate;
        $this->endDate      = $endDate;
        $this->employee     = $employee;
        $this->designation  = $designation;
        $this->dutyStation  = $dutyStation;
    }

    use Exportable;

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'employee_exit_clearance_report.xlsx';
    
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
        $exitHandoverNotes = ExitHandOverNote::query();

        if($this->startDate != '' && $this->endDate != '') {
            $startDate = $this->startDate;
            $endDate = $this->endDate;
            if($startDate < $endDate) {
                $exitHandoverNotes->whereDate('created_at', '>=', $startDate)
                                    ->whereDate('created_at', '<=', $endDate);
            }
        }

        if ($this->employee != '') {
            $employeeId = $this->employee;
            $exitHandoverNotes->where('employee_id', $employeeId);
        }

        if ($this->designation != '') {
            $designationId = $this->designation;
            $exitHandoverNotes->whereHas('employee', function ($q) use($designationId) {
                $q->whereHas('latestTenure', function ($q) use($designationId) {
                    $q->where('designation_id', $designationId);
                });
            });
        }

        if ($this->dutyStation != '') {
            $dutyStationId = $this->duty_station;
            $exitHandoverNotes->whereHas('employee', function ($q) use($dutyStationId) {
                $q->whereHas('latestTenure', function ($q) use($dutyStationId) {
                    $q->where('duty_station_id', $dutyStationId);
                });
            });
        }

        $data = $exitHandoverNotes->get();

        $array = [
            'exitHandoverNotes'     => $data,
        ];

        return view('Report::HumanResources.EmployeeExitClearance.export', $array);
    }
}
