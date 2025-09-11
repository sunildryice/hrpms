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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Modules\PurchaseRequest\Models\PurchaseRequestItem;

class PurchaseRequestExport implements  Responsable, 
                                        WithHeadings, 
                                        WithMapping, 
                                        FromCollection, 
                                        ShouldAutoSize, 
                                        WithStyles, 
                                        WithCustomStartCell, 
                                        WithEvents
{
    use Exportable;

    private $start_date;
    private $end_date;
    private $office;
    private $pr_number;
    private $requester_user_id;
    private $item_id;

    public function __construct($start_date, $end_date, $office, $pr_number, $requester_user_id, $item_id)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->office = $office;
        $this->pr_number = $pr_number;
        $this->requester_user_id = $requester_user_id;
        $this->item_id = $item_id;
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'purchase_request_report.xlsx';
    
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
                $sheet->setCellValue('A1', 'Purchase Request Log Report');
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
            // 'Period (from)',
            // 'Period (to)',
            'PR No.',
            'Office',
            'Requested Date',
            'Requested By',
            'Required Date',
            'Particulars',
            'Qty',
            'Tentative Cost',
            'Project',
            'Activity Code',
            'Account Code',
            'Donor Code',
            'Remarks'
        ];
    }

    public function map($record): array
    {
        return [
            $this->counter++,
            // $this->start_date ? explode(' ', $this->start_date)[0] : '-',
            // $this->end_date ? explode(' ', $this->end_date)[0] : '-',
            $record->purchaseRequest->getPurchaseRequestNumber(),
            $record->purchaseRequest->office->office_name,
            $record->purchaseRequest->request_date->format('Y-m-d'),
            $record->purchaseRequest->requester->full_name,
            $record->purchaseRequest->required_date->format('Y-m-d'),
            $record->item->title,
            $record->quantity,
            $record->total_price,
            '',
            $record->activityCode->title,
            $record->accountCode->title,
            $record->donorCode->title,
            $record->remarks
        ];
    }

    public function collection()
    {
        $records = PurchaseRequestItem::query();

        $records->with('purchaseRequest')
            ->whereHas('purchaseRequest', function($q) {
                $q->whereNot('approver_id', null);
            });

        if(isset($this->start_date) && isset($this->end_date)) {
            $start_date = $this->start_date;
            $end_date = $this->end_date;
            if($start_date < $end_date) {
                $records->whereHas('purchaseRequest', function($q) use($start_date,$end_date) {
                    $q->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<', $end_date);
                });
            }
        }

        if(isset($this->office)) {
            $office_id = $this->office;
            $records->whereHas('purchaseRequest', function($q) use($office_id) {
                $q->where('office_id', $office_id);
            });
        }

        if(isset($this->pr_number)) {
            $prNumber = $this->pr_number;
            $records->whereHas('purchaseRequest', function($q) use($prNumber) {
                $q->where(DB::raw('CONCAT(prefix, purchase_number)'), 'LIKE', "%$prNumber%");
                $q->orWhere(DB::raw('CONCAT_WS("-", prefix, purchase_number)'), 'LIKE', "%{$prNumber}%");
            });
        }

        if(isset($this->requester)) {
            $requesterUserId = $this->requester;
            $records->whereHas('purchaseRequest', function($q) use($requesterUserId) {
                $q->where('requester_id', $requesterUserId);
            });
        }

        if(isset($this->particulars)) {
            $itemId = $this->particulars;
            $records->where('item_id', $itemId);
        }

        return $records->get();
    }
}
