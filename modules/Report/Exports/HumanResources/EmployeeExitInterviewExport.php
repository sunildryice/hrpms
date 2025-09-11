<?php

namespace Modules\Report\Exports\HumanResources;

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
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Models\ExitFeedback;
use Modules\Master\Repositories\ExitQuestionRepository;
use Modules\Master\Repositories\ExitRatingRepository;

class EmployeeExitInterviewExport implements Responsable, ShouldAutoSize, WithStyles, WithStrictNullComparison, FromView
{
    public function __construct($employee, $designation, $dutyStation, $lastWorkingDate)
    {
        $this->employee         = $employee;
        $this->designation      = $designation;
        $this->dutyStation      = $dutyStation;
        $this->lastWorkingDate  = $lastWorkingDate;
        $this->exitQuestions    = app(ExitQuestionRepository::class);
        $this->exitFeedbacks    = app(ExitFeedback::class);
        $this->exitRatings      = app(ExitRatingRepository::class);
        $this->employees        = app(EmployeeRepository::class);
    }

    use Exportable;

    /**
    * It's required to define the fileName within
    * the export class when making use of Responsable.
    */
    private $fileName = 'employee_exit_interview_report.xlsx';
    
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
        $records = $this->employees->with('exitInterviews')
                                    ->whereHas('exitInterviews', function($q){
                                        $q->latest();
                                        $q->where('status_id', config('constant.APPROVED_STATUS'));
                                    })->newQuery();


        if (isset($this->employee)) {
            $employeeCode = $this->employee;
            $records->where('employee_code', $employeeCode);
        }

        if (isset($this->designation)) {
            $designationId = $this->designation;
            $records->whereHas('latestTenure', function ($q) use($designationId) {
                $q->where('designation_id', $designationId);
            });
        }

        if (isset($this->dutyStation)) {
            $dutyStationId = $this->dutyStation;
            $records->whereHas('latestTenure', function ($q) use($dutyStationId) {
                $q->where('duty_station_id', $dutyStationId);
            });
        }

        if (isset($this->lastWorkingDate)) {
            $lastWorkingDate = $this->lastWorkingDate;
            $records->where('last_working_date', '=', $lastWorkingDate);
        }

        $records = $records->get();

        $array = [
            'questions' => $this->exitQuestions->all(),
            'feedbacks' => $this->exitFeedbacks->all(),
            'ratings'   => $this->exitRatings->all(),
            'records'   => $records
        ];

        return view('Report::HumanResources.EmployeeExitInterview.export', $array);
    }
}
