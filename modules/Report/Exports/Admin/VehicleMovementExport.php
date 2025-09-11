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
use Modules\VehicleRequest\Models\VehicleRequest;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class VehicleMovementExport implements Responsable, WithHeadings, WithMapping, FromCollection, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
{
    use Exportable;

    private $start_date;
    private $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'vehicle_movement_report.xlsx';
    
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
                $sheet->setCellValue('A1', 'Vehicle Movement Plan');
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
            'Vehicle Request No.',
            'Office',
            'Vehicle Request Type',
            'Date From',
            'Date To',
            'Travel From',
            'Travel To',
            'User',
            'Purpose Of Travel',
            'Vehicle Type',
            'Tentative Cost',
            'Pick-up Point',
            'Pick-up Time',
            'End Time',
            'Request Approval Date',
            'Vehicle Contractor',
            'Bill No.',
            'Bill Date',
            'Amount',
            'VAT',
            'Total Amount',
            'TDS',
            'Net Payment',
            'Payment Approved Date'
        ];
    }

    public function map($record): array
    {
        return [
            $this->counter++,
            $record->getVehicleRequestNumber(),
            $record->getOfficeName(),
            $record->getVehicleRequestType(),
            $record->getStartDatetime(),
            $record->getEndDateTime(),
            $record->travel_from,
            $record->destination,
            $record->getRequesterName(),
            $record->purpose_of_travel,
            $record->getVehicleTypes(),
            $record->tentative_cost,
            $record->pickup_place,
            $record->pickup_time,
            $record->getEndDatetime(),
            $record->getRequestApprovalDate(),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
    }

    public function collection()
    {
        $records = VehicleRequest::query();
        $records->whereIn('status_id', [config('constant.APPROVED_STATUS')]);

        if(isset($this->start_date) && isset($this->end_date)) {
            $start_date = $this->start_date;
            $end_date = $this->end_date;
            if($start_date < $end_date) {
                $records->whereDate('created_at', '>=', $start_date)
                     ->whereDate('created_at', '<', $end_date);
            }
        }

        return $records->get();
    }
}
