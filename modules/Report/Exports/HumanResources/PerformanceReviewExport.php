<?php

namespace Modules\Report\Exports\HumanResources;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use Modules\PerformanceReview\Models\PerformanceReview;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerformanceReviewExport implements FromCollection, Responsable, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    private $start_date;

    private $end_date;

    private $employee;

    private $duty_station;

    private $goal_setting_date;

    private $mid_term_per_date;

    private $final_per_date;

    public function __construct($start_date, $end_date, $employee, $duty_station, $goal_setting_date, $mid_term_per_date, $final_per_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->employee = $employee;
        $this->duty_station = $duty_station;
        $this->goal_setting_date = $goal_setting_date;
        $this->mid_term_per_date = $mid_term_per_date;
        $this->final_per_date = $final_per_date;
    }

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'performance_review_report.xlsx';

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
                $sheet->setCellValue('A1', 'PER Completion List');
                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => [
                                'rgb' => '808080',
                            ],
                        ],
                    ],
                ];
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
            },
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
                        'rgb' => '808080',
                    ],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
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
            'Employee Name',
            'Designation',
            'Duty Station',
            'Supervisor',
            'Fiscal Year',
            'Status',
            'Goal Setting Date',
            'Mid-Term PER Date',
            'Final PER Date',
            'Major Achievements',
            'Major Challenges',
            'Key Goals',
            'Professional Development Plan',
            'Employee Remarks',
            'Supervisor Remarks',
            'Communication/working relationships',
            'Productivity',
            'Leadership',
            'Problem Solving',
            'Accountability',
            'Strengths',
            'Areas for Growth',
            'Overall Performance Evaluation',
            'Employee Comments',
            'Supervisor Comments',
        ];
    }

    public function map($record): array
    {
        return [
            $this->counter++,
            $record->requester->employee->getFullName(),
            $record->requester->employee->latestTenure->getDesignationName(),
            $record->requester->employee->latestTenure->getDutyStation(),
            $record->requester->employee->latestTenure->getSupervisorName(),
            $record->getFiscalYear(),
            $record->getStatus(),
            $record->goal_setting_date?->toFormattedDateString(),
            $record->mid_term_per_date?->toFormattedDateString(),
            $record->final_per_date?->toFormattedDateString(),
            $record->getAnswer(1),
            $record->getAnswer(2),

            $record->getKeyGoalsFields(), //key goals only
            $record->getProfessionalDevelopmentPlan(),

            $record->getKeyGoalsFields(2),
            $record->getKeyGoalsFields(3),
            $record->getAnswer(3),
            $record->getAnswer(4),
            $record->getAnswer(5),
            $record->getAnswer(6),
            $record->getAnswer(7),
            $record->getAnswer(8),
            $record->getAnswer(9),
            $record->getPerformanceEvalAnswer(),
            $record->getAnswer(17),
            $record->getAnswer(18),
        ];
    }

    public function collection()
    {
        $records = PerformanceReview::query();

        // $records = $records->where('status_id', config('constant.APPROVED_STATUS'));

        if (isset($this->start_date) && isset($this->end_date)) {
            $start_date = $this->start_date;
            $end_date = $this->end_date;
            if ($start_date < $end_date) {
                // $records->whereDate('review_from', '>=', $start_date)
                //     ->whereDate('review_to', '<', $end_date);
                $records->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<', $end_date);
            }
        }

        if (isset($this->employee)) {
            $employee_user_id = $this->employee;
            $records->where('requester_id', $employee_user_id);
        }

        if (isset($this->duty_station)) {
            $dutyStationId = $this->duty_station;
            $records->whereHas('requester', function ($q) use ($dutyStationId) {
                $q->whereHas('employee', function ($q) use ($dutyStationId) {
                    $q->whereHas('latestTenure', function ($q) use ($dutyStationId) {
                        $q->where('duty_station_id', $dutyStationId);
                    });
                });
            });
        }

        if (isset($this->goal_setting_date)) {
            $goal_setting_date = $this->goal_setting_date;
            $records->whereDate('goal_setting_date', '=', $goal_setting_date);
        }

        if (isset($this->mid_term_per_date)) {
            $mid_term_per_date = $this->mid_term_per_date;
            $records->orWhereDate('mid_term_per_date', $mid_term_per_date);
        }

        if (isset($this->final_per_date)) {
            $final_per_date = $this->final_per_date;
            $records->orWhereDate('final_per_date', $final_per_date);
        }

        $data = $records->orderBy('fiscal_year_id', 'desc')->get();
        return $data;

    }
}
