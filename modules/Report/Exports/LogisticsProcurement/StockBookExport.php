<?php

namespace Modules\Report\Exports\LogisticsProcurement;

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
use Modules\Inventory\Models\InventoryItem;

class StockBookExport implements Responsable, ShouldAutoSize, WithStyles, WithStrictNullComparison, FromView
{
    use Exportable;

    private $start_date;
    private $end_date;
    private $item_id;
    private $item_category;
    private $issued_to;

    public function __construct($start_date, $end_date, $item_id, $item_category, $issued_to)
    {
        $this->start_date       = $start_date;
        $this->end_date         = $end_date;
        $this->item_id          = $item_id;
        $this->item_category    = $item_category;
        $this->issued_to        = $issued_to;
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'stock_book_report.xlsx';
    
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
        $sheet->getStyle(3)->getFont()->setBold(true);
       
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
        $data = InventoryItem::query();
            $data->with(['goodRequestItems', 'distributionRequestItems', 'goodRequestItems.goodRequest', 'distributionRequestItems.distributionRequest']);
            $data->whereHas('item', function ($q) {
                    $q->whereHas('category', function ($q) {
                        $q->whereHas('inventoryType', function ($q) {
                            $q->where('title', '=', 'consumable');
                        });
                    });
                });

            if (isset($this->issued_to)) {
                $data->whereHas('goodRequestItems', function ($q) {
                    $q->whereHas('goodRequest', function ($q) {
                        $q->where('status_id', config('constant.APPROVED_STATUS'));
                        $q->where('created_by', $this->issued_to);
                    });
                });
                $data->orWhereHas('distributionRequestItems', function ($q) {
                    $q->whereHas('distributionRequest', function ($q) {
                        $q->where('status_id', config('constant.APPROVED_STATUS'));
                        $q->where('created_by', $this->issued_to);
                    });
                });
            }

            if (isset($this->start_date) && isset($this->end_date)) {
                if ($this->start_date <= $this->end_date) {
                    $data->whereHas('goodRequestItems', function ($q) {
                        $q->whereHas('goodRequest', function ($q) {
                            $q->whereHas('logs', function ($q) {
                                $q->where('status_id', config('constant.APPROVED_STATUS'));
                                $q->latest();
                                $q->whereDate('created_at', '>=', $this->start_date)
                                ->whereDate('created_at', '<=', $this->end_date);
                            });
                        });
                    });
                    $data->orWhereHas('distributionRequestItems', function ($q) {
                        $q->whereHas('distributionRequest', function ($q) {
                            $q->whereHas('logs', function ($q) {
                                $q->where('status_id', config('constant.APPROVED_STATUS'));
                                $q->latest();
                                $q->whereDate('created_at', '>=', $this->start_date)
                                ->whereDate('created_at', '<=', $this->end_date);
                            });
                        });
                    });
                }
            }

            $data = $data->get();

            if (isset($this->item_id)) {
                $data = $data->where('item_id', $this->item_id);
            }

            if (isset($this->item_category)) {
                $data = $data->where('category_id', $this->item_category);
            }

        $array = [
            'data' => $data,
        ];

        return view('Report::LogisticsProcurement.StockBook.export', $array);
    }
}
