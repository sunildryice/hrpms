<?php

namespace Modules\Report\Exports\LogisticsProcurement;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\MaintenanceRequest\Models\MaintenanceRequest;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MaintenanceRequestExport implements Responsable, WithHeadings, WithMapping, FromCollection, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
{
    use Exportable;

    private $start_date;
    private $end_date;
    private $rmNumber;
    private $office;
    private $item;

    public function __construct($start_date, $end_date, $rmNumber, $office, $item)
    {
        $this->start_date   = $start_date;
        $this->end_date     = $end_date;
        $this->rmNumber     = $rmNumber;
        $this->office       = $office;
        $this->item         = $item;
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'maintenance_request_report.xlsx';
    
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
                $sheet->setCellValue('A1', 'Maintenance Request Report');
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
            'RM No.',
            'Office',
            'Requested Date',
            'Requested By',
            'Item/Equipment to Repair',
            'Assets Code',
            'Problem/Service For',
            'Qty',
            'Total Tentative Cost',
            'Project',
            'Activity Code',
            'Account Code',
            'Donor Code',
            'Remarks',
        ];
    }

    public function map($record): array
    {
        return [
            $this->counter++,
            $record->getMaintenanceRequestNumber(),
            $record->requester->getOfficeName(),
            $record->created_at->format('M d, Y'),
            $record->getRequester(),
            $record->getItem(),
            $record->item->item_code,
            $record->problem,
            '',
            $record->estimated_cost,
            '',
            $record->getActivityCode(),
            $record->getAccountCode(),
            $record->getDonorCode(),
            $record->remarks
        ];
    }

    public function collection()
    {
        $records = MaintenanceRequest::query();
        $records->where('status_id', config('constant.APPROVED_STATUS'));

        if(isset($this->start_date) && isset($this->end_date)) {
            $start_date = $this->start_date;
            $end_date = $this->end_date;
            if($start_date < $end_date) {
                $records->whereDate('created_at', '>=', $start_date)
                     ->whereDate('created_at', '<', $end_date);
            }
        }

        if (isset($this->rmNumber)) {
            $rmNumber = $this->rmNumber;
            $records->where(DB::raw('CONCAT(prefix, maintenance_number)'), 'LIKE', '%'.$rmNumber.'%')
                ->orWhere(DB::raw('CONCAT_WS("-", prefix, maintenance_number)'), 'LIKE', '%'.$rmNumber.'%');
        }

        if (isset($this->office)) {
            $officeId = $this->office;
            $records->whereHas('requester', function($q) use($officeId) {
                $q->whereHas('employee', function($q) use($officeId) {
                    $q->whereHas('latestTenure', function($q) use($officeId) {
                        $q->where('office_id', $officeId);
                    });
                });
            });
        }

        if (isset($this->item)) {
            $itemId = $this->item;
            $records->where('item_id', $itemId);
        }

        return $records->get();
    }
}
