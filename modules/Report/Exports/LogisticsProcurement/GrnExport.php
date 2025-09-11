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

use Modules\Grn\Models\GrnItem;


class GrnExport implements Responsable, WithHeadings, WithMapping, FromCollection, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
{
    use Exportable;

    private $start_date;
    private $end_date;
    private $office;
    private $grnNumber;
    private $poNumber;
    private $vendor;
    private $item;

    public function __construct($start_date, $end_date, $office, $grnNumber, $poNumber, $vendor, $item)
    {
        $this->start_date   = $start_date;
        $this->end_date     = $end_date;
        $this->office       = $office;
        $this->grnNumber    = $grnNumber;
        $this->poNumber     = $poNumber;
        $this->vendor       = $vendor;
        $this->item         = $item;
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'grn_report.xlsx';

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
                $sheet->setCellValue('A1', 'GRN Log Report');
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
            'GRN No.',
            // 'Period (from)',
            // 'Period (to)',
            'Office',
            'PO No.',
            'PR No.',
            'Project',
            'Activity Code',
            'Account Code',
            'Donor Code',
            'Vendor Name',
            'Address',
            'Item',
            'Description',
            'Inventory Type',
            'Item Category',
            'Unit',
            'Quantity',
            'Rate',
            'Amount',
            'VAT',
            'Total Amount',
            'Receiver',
            'Received Date'
        ];
    }

    public function map($record): array
    {
        return [
            $this->counter++,
            $record->grn->prefix.'-'.$record->grn->grn_number,
            // $this->start_date ? explode(' ', $this->start_date)[0] : '-',
            // $this->end_date ? explode(' ', $this->end_date)[0] : '-',
            $record->grn->office->office_name,
            $record->getPONo(),
            $record->getPRNo(),
            '',
            $record->activityCode->title,
            $record->accountCode->title,
            $record->donorCode->getDonorCodeWithDescription(),
            $record->grn->supplier->supplier_name,
            $record->grn->supplier->address1,
            $record->item->title,
            $record->item->category->description,
            $record->item->category->inventoryType->title,
            $record->item->category->title,
            $record->unit->title,
            $record->quantity,
            $record->unit_price,
            $record->total_price,
            $record->vat_amount,
            $record->total_amount,
            '',
            $record->grn->received_date->format('Y-m-d')
        ];
    }

    public function collection()
    {
        $records = GrnItem::query();

        $records->with('grn')
            ->whereHas('grn', function($q) {
                $q->whereNot('approver_id', null);
            });

        if(isset($this->start_date) && isset($this->end_date)) {
            $start_date = $this->start_date;
            $end_date = $this->end_date;
            if($start_date < $end_date) {
                $records->whereHas('grn', function($q) use($start_date,$end_date) {
                    $q->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<', $end_date);
                });
            }
        }

        if(isset($this->office)) {
            $office_id = $this->office;
            $records->whereHas('grn', function($q) use($office_id) {
                $q->where('office_id', $office_id);
            });
        }

        if (isset($this->grnNumber)) {
            $grnNumber = $this->grnNumber;
            $records->whereHas('grn', function($q) use($grnNumber) {
                $q->where(DB::raw('CONCAT(prefix, grn_number)'), 'LIKE', "%$grnNumber%");
                $q->orWhere(DB::raw('CONCAT_WS("-", prefix, grn_number)'), 'LIKE', "%{$grnNumber}%");
            });
        }

        if(isset($this->poNumber)) {
            $poNumber = $this->poNumber;
            $records->whereHas('grn', function($q) use($poNumber) {
                $q->whereHas('purchaseOrder', function($q) use($poNumber) {
                    $q->where(DB::raw('CONCAT(prefix, order_number)'), 'LIKE', "%$poNumber%");
                    $q->orWhere(DB::raw('CONCAT_WS("-", prefix, order_number)'), 'LIKE', "%{$poNumber}%");
                });
            });
        }

        if (isset($this->vendor)) {
            $supplierId = $this->vendor;
            $records->whereHas('grn', function($q) use($supplierId) {
                $q->where('supplier_id', $supplierId);
            });
        }

        if (isset($this->item)) {
            $itemId = $this->item;
            $records->where('item_id', $itemId);
        }

        return $records->get();
    }
}
