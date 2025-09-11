<?php

namespace Modules\Report\Exports\LogisticsProcurement;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Excel;
use Modules\Inventory\Models\Asset;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\Inventory\Models\Enums\ItemRatePriceRange;

class AssetBookExport implements FromView, Responsable, ShouldAutoSize, WithStrictNullComparison, WithStyles
{
    use Exportable;

    private $start_date;

    private $end_date;

    private $item_id;

    private $item_category;

    private $issued_to;

    private $issued_to_office;

    public function __construct($start_date, $end_date, $item_id, $item_category, $issued_to, $issued_to_office, protected ?ItemRatePriceRange $priceRange)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->item_id = $item_id;
        $this->item_category = $item_category;
        $this->issued_to = $issued_to;
        $this->issued_to_office = $issued_to_office;
    }

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'asset_book_report.xlsx';

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
                        'rgb' => '808080',
                    ],
                ],
            ],
        ]);
    }

    public function view(): View
    {
        $data = Asset::query()->with([
            'inventoryItem.office',
            'inventoryItem.grn.fiscalYear',
            'inventoryItem.item',
            'inventoryItem.accountCode',
            'inventoryItem.activityCode',
            'inventoryItem.donorCode',
            'inventoryItem.executionType',
            'inventoryItem.supplier',
            'inventoryItem.category',
            'assignedTo',
            'assignedTo.employee.latestTenure.designation',
            'assignedTo.employee.latestTenure.office',
            'latestGoodRequestAsset.goodRequest.approvedLog',
            'latestConditionLog.condition',
            'assignedOffice',
        ])->whereDoesntHave('dispositionRequest', function ($query) {
            $query->where('status_id', config('constant.APPROVED_STATUS'));
        });

        if (isset($this->start_date) && isset($this->end_date)) {
            if ($this->start_date <= $this->end_date) {
                $data->whereIn('status', [config('constant.ASSET_ASSIGNED')])
                    ->whereHas('goodRequestAsset', function ($q) {
                        $q->whereHas('goodRequest', function ($q) {
                            $q->whereHas('logs', function ($q) {
                                $q->where('status_id', config('constant.APPROVED_STATUS'))
                                    ->whereDate('created_at', '>=', $this->start_date)
                                    ->whereDate('created_at', '<=', $this->end_date);
                            });
                        });
                    });
            }
        }

        if (isset($this->issued_to)) {
            $data->where('status', config('constant.ASSET_ASSIGNED'))
                ->where('assigned_user_id', $this->issued_to);
        }

        if (isset($this->issued_to_office)) {
            $data->where('assigned_office_id', $this->issued_to_office);
        }

        if (isset($this->item_category)) {
            $data->whereHas('inventoryItem', function ($q) {
                $q->where('category_id', $this->item_category);
            });
        }

        if (isset($this->item_id)) {
            $data->whereHas('inventoryItem', function ($q) {
                $q->where('item_id', $this->item_id);
            });
        }

        if($this->priceRange){
            $data = $this->priceRange->apply($data);
        }

        $data = $data->get();

        $array = [
            'data' => $data,
        ];

        return view('Report::LogisticsProcurement.AssetBook.export', $array);
    }
}
