<?php

namespace Modules\Report\Exports\HumanResources;

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
use Modules\Employee\Models\Employee;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeProfileExport implements FromCollection, Responsable, ShouldAutoSize, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    private $start_date;

    private $end_date;

    private $office;

    private $gender;

    private $active;

    public function __construct($start_date, $end_date, $office, $gender, $active)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->office = $office;
        $this->gender = $gender;
        $this->active = $active;
    }

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'employee_profile_report.xlsx';

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
                $sheet->setCellValue('A1', 'Employee Profile Report');
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
            'S.N.',
            'Name of Staff',
            'Staff ID',
            'Joined Date',
            'Position (Latest)',
            'Duty Station (Latest)',
            'Supervisor Name (Latest)',
            'Current Address',
            'Mobile',
            'Office Email',
            'Citizenship No.',
            'PAN No.',
            'SSF No.',
            'CIT No.',
            'DOB',
            'Gender',
            'Blood Group',
            'Marital Status',
            'Bank Details',
            'Probationary Complete',
            'Active Employee',
            'Last Working Date',
        ];
    }

    public function map($record): array
    {
        return [
            $this->counter++,
            $record->getFullName(),
            $record->employee_code,
            $record->latestTenure->getJoinedDate(),
            $record->latestTenure->getDesignationName(),
            $record->getDutyStation(),
            $record->getSupervisorName(),
            $record->address?->getPermanentAddress(),
            $record->mobile_number,
            $record->official_email_address,
            $record->citizenship_number,
            $record->pan_number,
            $record->finance->ssf_number,
            $record->finance->cit_number,
            $record->date_of_birth,
            $record->getGender(),
            $record->medicalCondition?->bloodGroup?->title ?: '',
            $record->getMaritalStatus(),
            $record->getBankDetail(),
            isset($record->probation_complete_date) ? 'Yes' : 'No',
            $record->getActiveStatus(),
            $record->last_working_date,
        ];
    }

    public function collection()
    {
        $records = Employee::query()->with(['medicalCondition.bloodGroup']);

        if (isset($this->start_date) && isset($this->end_date)) {
            $start_date = $this->start_date;
            $end_date = $this->end_date;
            if ($start_date < $end_date) {
                $records->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<', $end_date);
            }
        }

        if (isset($this->office)) {
            $office_id = $this->office;
            $records->where('office_id', $office_id);
        }

        if (isset($this->gender)) {
            $records->where('gender', $this->gender);
        }

        if ($this->active === '1') {
            $records->whereNotNull('activated_at');
        } elseif ($this->active === '0') {
            $records->whereNull('activated_at');
        }

        return $records->get();
    }
}
