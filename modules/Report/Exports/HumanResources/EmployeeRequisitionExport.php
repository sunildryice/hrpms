<?php

namespace Modules\Report\Exports\HumanResources;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\EmployeeRequest\Models\EmployeeRequest;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class EmployeeRequisitionExport implements Responsable, WithHeadings, WithMapping, FromCollection, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
{
    use Exportable;

    private $start_date;
    private $end_date;
    private $position;
    private $duty_station;
    private $fiscal_year;

    public function __construct($start_date, $end_date, $position, $duty_station, $fiscal_year)
    {
        $this->start_date   = $start_date;
        $this->end_date     = $end_date;
        $this->position     = $position;
        $this->duty_station = $duty_station;
        $this->fiscal_year  = $fiscal_year;
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'employee_requisition_report.xlsx';
    
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $cellRange = 'A1:'.$sheet->getHighestDataColumn().'1';
                $sheet->mergeCells($cellRange);
                $sheet->setCellValue('A1', 'Employee Requisition Report');
                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => [
                                'rgb' => '808080'
                            ]
                        ],
                    ],
                ];
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
            }
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    // Custom function to calculate the total cell range.
    public function getCellRange(Worksheet $sheet)
    {
        $row_count = $sheet->getHighestDataRow(); // returns row count - int, eg: 1 or 2 or 3.
        $column_count = $sheet->getHighestDataColumn(); // returns last column - alphabet, eg: A or D or W.
        $start_cell = $this->startCell();
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

    /**
    * Adding a heading row
    */
    public function headings(): array
    {
        return [
            'S.N.',
            'Ref. No.',
            // 'Period (from)',
            // 'Period (to)',
            'Position Title',
            'Duty Station',
            'Requested Level',
            'Requested Date',
            'Type of Employement',
            'For Fiscal Year',
            'Replacement For',
            'Date Required From',
            'Project',
            'Account Code',
            'Activity Code',
            'Donor Code',
            'Requested By',
            'Requested Date',
            'Approved (Yes/No)',
            'Approved Date',
            'Vacancy Type',
            'Vacancy Portfolio',
            'Vacancy Date',
            'Vacancy Deadline',
            'Recuitment Process',
            'Recruited',
            'Joined Date',
            'Remarks',
        ];
    }

    public function map($record): array
    {
        return [
            $this->counter++,
            $record->id,
            // $this->start_date ? explode(' ', $this->start_date)[0] : '-',
            // $this->end_date ? explode(' ', $this->end_date)[0] : '-',
            $record->position_title,
            $record->getDutyStation(),
            $record->position_level,
            $record->requested_date ?? $record->created_at->format('Y-m-d'),
            $record->employeeType?->title,
            $record->getFiscalYear(),
            $record->replacement_for,
            $record->required_date,
            '',
            $record->getAccountCode(),
            $record->getActivityCode(),
            '',
            $record->getRequesterName(),
            $record->requested_date ?? $record->created_at->format('Y-m-d'),
            $record->approver_id ? 'Yes' : 'No',
            $record->getApprovedDate(),
            '',
            '',
            $record->requested_date ?? $record->created_at->format('Y-m-d'),
            $record->required_date,
            '',
            '',
            '',
            '',
        ];
    }

    public function collection()
    {
        $records = EmployeeRequest::query();
        $records->where('status_id', config('constant.APPROVED_STATUS'));

        if(isset($this->start_date) && isset($this->end_date)) {
            $start_date = $this->start_date;
            $end_date = $this->end_date;
            if($start_date < $end_date) {
                $records->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<', $end_date);
            }
        }

        if(isset($this->position)) {
            if ($this->position !== 'all') {
                $records->where('position_title', $this->position);
            }
        }

        if (isset($this->duty_station)) {
            if ($this->duty_station !== 'all') {
                $records->where('duty_station_id', $this->duty_station);
            }
        }

        if (isset($this->fiscal_year)) {
            $fiscalYearId = $this->fiscal_year;
            $records->where('fiscal_year_id', $fiscalYearId);
        }

        return $records->get();
    }
}
