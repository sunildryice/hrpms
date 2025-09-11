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
use Modules\TrainingRequest\Models\TrainingRequest;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TrainingRequestExport implements Responsable, WithHeadings, WithMapping, FromCollection, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
{
    use Exportable;

    private $start_date;
    private $end_date;
    private $employee;
    private $designation;
    private $department;
    private $dutyStation;
    private $trainingName;

    public function __construct($start_date, $end_date, $employee, $designation, $department, $dutyStation, $trainingName)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->employee = $employee;
        $this->designation = $designation;
        $this->department = $department;
        $this->dutyStation = $dutyStation;
        $this->trainingName = $trainingName;
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'training_request_report.xlsx';
    
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
                $sheet->setCellValue('A1', 'Training Request Report');
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
            'Ref No.',
            'Employee Name',
            'Designation',
            'Department',
            'Duty Station',
            'Name of Course / Training',
            'Training Organizer',
            'Date of Course: Begin',
            'Date of Course: End',
            'Time of Course (In days)',
            'Project',
            'Account Code',
            'Activity Code',
            'Donor Code',
            'Training Cost',
            'Approved Date',
            'Training Report',
            'Remarks',
        ];
    }

    public function map($record): array
    {
        return [
            $this->counter++,
            $record->getTrainingRequestNumber(),
            $record->requester->employee->getFullName(),
            $record->requester->employee->getDesignationName(),
            $record->requester->employee->getDepartmentName(),
            $record->requester->employee->getDutyStation(),
            $record->title,
            '',
            $record->getStartDate(),
            $record->getEndDate(),
            $record->getTotalDays(),
            '',
            $record->getAccountCode(),
            $record->getActivityCode(),
            '',
            $record->course_fee,
            $record->getTrainingRequestApprovedDate(),
            $record->getTrainingReportSubmissionStatus(),
            ''
        ];
    }

    public function collection()
    {
        $records = TrainingRequest::query();
        $records->where('status_id', config('constant.APPROVED_STATUS'));

        if(isset($this->start_date) && isset($this->end_date)) {
            $start_date = $this->start_date;
            $end_date = $this->end_date;
            if($start_date < $end_date) {
                $records->whereDate('created_at', '>=', $start_date)
                     ->whereDate('created_at', '<', $end_date);
            }
        }

        if (isset($this->employee)) {
            $employee_user_id = $this->employee;
            $records->where('created_by', $employee_user_id);
        }

        if (isset($this->designation)) {
            $designationId = $this->designation;
            $records->whereHas('requester', function($q) use($designationId) {
                $q->whereHas('employee', function($q) use($designationId) {
                    $q->whereHas('latestTenure', function($q) use($designationId) {
                        $q->where('designation_id', $designationId);
                    });
                });
            });
        }

        if (isset($this->department)) {
            $departmentId = $this->department;
            $records->whereHas('requester', function($q) use($departmentId) {
                $q->whereHas('employee', function($q) use($departmentId) {
                    $q->whereHas('latestTenure', function($q) use($departmentId) {
                        $q->where('department_id', $departmentId);
                    });
                });
            });
        }

        if (isset($this->dutyStation)) {
            $dutyStationId = $this->dutyStation;
            $records->whereHas('requester', function($q) use($dutyStationId) {
                $q->whereHas('employee', function($q) use($dutyStationId) {
                    $q->whereHas('latestTenure', function($q) use($dutyStationId) {
                        $q->where('duty_station_id', $dutyStationId);
                    });
                });
            });
        }

        if (isset($this->training_name)) {
            $trainingName = $this->training_name;
            $records->where('title', 'LIKE', '%'.$trainingName.'%');
        }

        return $records->get();
    }
}
