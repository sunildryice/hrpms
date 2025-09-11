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
use Modules\Inventory\Models\Asset;
use Modules\Inventory\Models\Enums\ItemRatePriceRange;

class AssetDispositionExport implements Responsable, ShouldAutoSize, WithStyles, WithStrictNullComparison, FromView
{
    use Exportable;

    private $start_date;
    private $end_date;
    private $item_id;
    private $item_category;
    private $requester;
    private $disposition_type;
    private $office_id;

    public function __construct($start_date, $end_date, $item_id, $item_category, $requester, $disposition_type, $office_id)
    {
        $this->start_date       = $start_date;
        $this->end_date         = $end_date;
        $this->item_id          = $item_id;
        $this->item_category    = $item_category;
        $this->requester        = $requester;
        $this->disposition_type = $disposition_type;
        $this->office_id        = $office_id;
    }

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'asset_disposition_report.xlsx';

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
        $data = Asset::query()->whereHas('dispositionRequest', function ($q) {
            $q->where('status_id', config('constant.APPROVED_STATUS'));
        });

        if (isset($this->start_date) && isset($this->end_date)) {
            if ($this->start_date <= $this->end_date) {
                $data
                ->whereHas('dispositionRequest', function ($q) {
                                $q->where('disposition_date', '>=', $this->start_date);
                                $q->where('disposition_date', '<=', $this->end_date);
                    });
            }
        }

        if (isset($this->requester)) {
            $data
            ->whereHas('dispositionRequest', function ($q) {
                $q->where('requester_id', $this->requester);
            });
        }

        if (isset($this->item_category)) {
            $data->whereHas('inventoryItem', function ($q) {
                $q->where('category_id', $this->item_category);
            });
        }

        if(isset($this->disposition_type)){
            $data->whereHas('dispositionRequest', function ($q) {
                $q->where('disposition_type_id', $this->disposition_type);
            });
        }

        if(isset($this->office_id)){
            $data->whereHas('dispositionRequest', function ($q) {
                $q->where('office_id', $this->office_id);
            });
        }

        if (isset($this->item_id)) {
            $data->whereHas('inventoryItem', function ($q) {
                $q->where('item_id', $this->item_id);
            });
        }

        $data = $data->get();

        $array = [
            'data' => $data,
        ];

        return view('Report::LogisticsProcurement.AssetDisposition.export', $array);
    }
}
