<?php

namespace Modules\Report\Exports\Finance;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\FundRequest\Models\FundRequest;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class FundRequestExport implements Responsable, WithHeadings, WithMapping, FromCollection, ShouldAutoSize, WithStyles
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
    private $fileName = 'fund_request_report.xlsx';
    
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
            'Fund Request No.',
            'Requested By',
            'Fiscal Year',
            'Month',
            'District',
            'Project',
            'Requested Date',
            'Remarks',
        ];
    }

    public function map($record): array
    {
        return [
            $this->counter++,
            $record->getFundRequestNumber(),
            $record->getRequesterName(),
            $record->getFiscalYear(),
            $record->getMonthName(),
            $record->getDistrictName(),
            $record->getProjectCode(),
            $record->created_at->format('M d, Y'),
            $record->remarks
        ];
    }

    public function collection()
    {
        $records = FundRequest::query();
        $records->where('status_id', config('constant.APPROVED_STATUS'));

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
