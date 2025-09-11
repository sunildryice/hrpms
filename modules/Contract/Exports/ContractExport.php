<?php

namespace Modules\Contract\Exports;

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
use Modules\Contract\Repositories\ContractRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContractExport implements FromCollection, Responsable, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct()
    {
    }

    private $fileName = 'contract_export.xlsx';

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
                $sheet->setCellValue('A1', 'Contracts');
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

                // Style the totals row
                $totalRow = $sheet->getHighestDataRow(); // Last row
                $totalCellRange = 'A'.$totalRow.':'.$sheet->getHighestDataColumn().$totalRow;
                $sheet->getStyle($totalCellRange)->applyFromArray([
                    'font' => [
                        'bold' => false,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => [
                                'rgb' => '808080',
                            ],
                        ],
                    ],
                ]);
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
        ]);
    }

    /**
     * Adding a heading row
     */
    public function headings(): array
    {
        return [
            __('label.organization-name'),
            __('label.contract-number'),
            __('label.contract-description'),
            __('label.contract-amount'),
            __('label.contract-date'),
            __('label.effective-date'),
            __('label.expiry-date'),
            __('label.remarks'),
        ];
    }

    public function map($record): array
    {
        return [
            $record->supplier->getSupplierNameandVAT(),
            $record->contract_number,
            $record->description,
            $record->getContractAmount(),
            $record->getContractDate(),
            $record->getEffectiveDate(),
            $record->getExpiryDate(),
            $record->remarks,
        ];
    }

    public function collection()
    {
            return app(ContractRepository::class)->with(['supplier', 'latestAmendment'])->select(['*'])->get();
    }
}
