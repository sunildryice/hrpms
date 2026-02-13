<?php

namespace Modules\Report\Exports\HumanResources;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Illuminate\Contracts\View\View;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\OffDayWork\Repositories\OffDayWorkRepository;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class OffDayWorkExport implements FromView, ShouldAutoSize, WithStyles, WithStrictNullComparison
{
    use Exportable;

    private $fiscalYear, $month, $office, $employee, $requestDate, $offDayDate;
    private $fiscalYearsRepo;
    private $offDayWorksRepo;

    public function __construct($fiscalYear, $month, $office, $employee, $requestDate, $offDayDate)
    {
        $this->fiscalYear = $fiscalYear;
        $this->month = $month;
        $this->office = $office;
        $this->employee = $employee;
        $this->requestDate = $requestDate;
        $this->offDayDate = $offDayDate;

        $this->fiscalYearsRepo = app(FiscalYearRepository::class);
        $this->offDayWorksRepo = app(OffDayWorkRepository::class);
    }

    private $fileName = 'off_day_work_report.xlsx';
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

    public function view(): View
    {
        $fiscalYear = $this->fiscalYear ? $this->fiscalYearsRepo->find($this->fiscalYear) : $this->fiscalYearsRepo->getCurrentFiscalYear();

        $query = $this->offDayWorksRepo->select(['*'])
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereYear('request_date', $fiscalYear->start_date);

        if ($this->month) {
            $query->whereMonth('request_date', $this->month);
        }
        if ($this->requestDate) {
            $query->whereDate('request_date', $this->requestDate);
        }
        // if ($this->offDayDate) {
        //     $query->whereDate('date', $this->offDayDate);
        // }
        if ($this->office) {
            $query->whereOfficeId($this->office);
        }
        if ($this->employee) {
            $query->where('requester_id', $this->employee);
        }

        $offDayWorks = $query->orderBy('date', 'desc')->get();

        return view('Report::HumanResources.OffDayWork.export', [
            'offDayWorks' => $offDayWorks,
            'fiscalYear' => $fiscalYear->title,
            'month' => $this->month ? date('F', mktime(0, 0, 0, $this->month, 10)) : null,
        ]);
    }

}