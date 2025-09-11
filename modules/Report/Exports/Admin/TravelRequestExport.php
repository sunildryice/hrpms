<?php

namespace Modules\Report\Exports\Admin;

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
use Modules\TravelRequest\Models\TravelRequest;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TravelRequestExport implements Responsable, WithHeadings, WithMapping, FromCollection, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
{
    use Exportable;

    private $start_date;
    private $end_date;
    private $employee;
    private $duty_station;
    private $purpose_of_travel;

    public function __construct($start_date, $end_date, $employee, $duty_station, $purpose_of_travel)
    {
        $this->start_date        = $start_date;
        $this->end_date          = $end_date;
        $this->employee          = $employee;
        $this->duty_station      = $duty_station;
        $this->purpose_of_travel = $purpose_of_travel;
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'travel_request_report.xlsx';

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
                $sheet->setCellValue('A1', 'Travel Request Report');
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
            'Travel No.',
            'Employee Name',
            'Designation',
            'Duty Station',
            'Date From',
            'Date To',
            'Total Days',
            'Mode of Travel',
            'Travel Location',
            'Purpose of Travel',
            'Approved',
            'Amended',
            'Travel Claim Submitted Date',
            'Travel Claim Approved Date',
            'Travel Claim Reimbursed Date',
        ];
    }

    public function map($record): array
    {
        return [
            $this->counter++,
            $record->getTravelRequestNumber(),
            $record->getRequesterName(),
            $record->requester->employee->designation->title,
            $record->requester->employee->getDutyStation(),
            $record->getDepartureDate(),
            $record->getReturnDate(),
            $record->getTotalDays(),
            $record->getTravelMode(),
            $record->final_destination,
            $record->purpose_of_travel,
            $record->getIsApproved(),
            $record->getIsAmended(),
            $record->travelClaim?->created_at->format('M d, Y'),
            $record->travelClaim ?->getApprovedDate(),
            '',
        ];
    }

    public function collection()
    {
        // $records = TravelRequestItinerary::query();
        $records = TravelRequest::query();
        $records->where('status_id', config('constant.APPROVED_STATUS'));

        if(isset($this->start_date) && isset($this->end_date)) {
            $start_date = $this->start_date;
            $end_date = $this->end_date;
            if($start_date <= $end_date) {
                $records->whereDate('departure_date', '>=', $start_date)
                          ->whereDate('return_date', '<=', $end_date);

                // $records->whereHas('travelRequest', function($q) use($start_date, $end_date) {
                //     $q->whereDate('departure_date', '>=', $start_date)
                //       ->whereDate('return_date', '<=', $end_date);
                // });
            }
        }

        if (isset($this->employee)) {
            $employee_user_id = $this->employee;
            $records->where('requester_id', $employee_user_id);
        }

        if (isset($this->duty_station)) {
            $dutyStationId = $this->duty_station;
            $records->whereHas('requester', function($q) use($dutyStationId) {
                $q->whereHas('employee', function($q) use($dutyStationId) {
                    $q->whereHas('latestTenure', function($q) use($dutyStationId) {
                        $q->where('duty_station_id', $dutyStationId);
                    });
                });
            });
        }

        if (isset($this->purpose_of_travel)) {
            $purposeOfTravel = $this->purpose_of_travel;
            $records->where('purpose_of_travel', 'LIKE', '%'.$purposeOfTravel.'%');
        }

        return $records->get();
    }
}
