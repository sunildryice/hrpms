<?php

namespace Modules\Report\Exports\HumanResources;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\FromView;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class WorkFromHomeExport implements Responsable, ShouldAutoSize, WithStyles, WithStrictNullComparison, FromView
{
    use Exportable;

    private $office, $fiscalYear, $month, $employee, $requestDate;
    private $fiscalYears, $workFromHomes;

    public function __construct($fiscalYear, $month, $office, $employee, $requestDate)
    {
        $this->fiscalYear   = $fiscalYear;
        $this->month        = $month;
        $this->office       = $office;
        $this->employee     = $employee;
        $this->requestDate  = $requestDate;

        $this->fiscalYears  = app(FiscalYearRepository::class);
        $this->workFromHomes = app(WorkFromHomeRepository::class);
    }

    private $fileName = 'work_from_home_report.xlsx';
    private $writerType = Excel::XLSX;
    private $headers = ['Content-Type' => 'text/csv'];

    public function getCellRange(Worksheet $sheet)
    {
        $row_count = $sheet->getHighestDataRow();
        $column_count = $sheet->getHighestDataColumn();
        return 'A1:' . $column_count . $row_count;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle(2)->getFont()->setBold(true);

        $sheet->getStyle($this->getCellRange($sheet))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '808080'],
                ],
            ],
        ]);
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        $fiscalYear = $this->fiscalYear ? $this->fiscalYears->find($this->fiscalYear) : $this->fiscalYears->getCurrentFiscalYear();

        $query = $this->workFromHomes->select(['*'])
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereYear('request_date', $fiscalYear->start_date);

        if ($this->month) {
            $query->whereMonth('request_date', $this->month);
        }
        if ($this->requestDate) {
            $query->where('request_date', $this->requestDate);
        }
        if ($this->office) {
            $query->whereOfficeId($this->office);
        }
        if ($this->employee) {
            $query->where('requester_id', $this->employee);
        }

        $workFromHomes = $query->orderBy('start_date', 'desc')->get();

        return view('Report::HumanResources.WorkFromHome.export', [
            'workFromHomes' => $workFromHomes,
            'fiscalYear'    => $fiscalYear->title,
            'month'         => $this->month ? date('F', mktime(0, 0, 0, $this->month, 10)) : null,
        ]);
    }
}