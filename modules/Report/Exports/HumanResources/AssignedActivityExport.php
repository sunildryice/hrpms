<?php

namespace Modules\Report\Exports\HumanResources;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Modules\Project\Repositories\ProjectActivityRepository;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AssignedActivityExport implements FromView, ShouldAutoSize, WithStyles, WithStrictNullComparison
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private $employee, $project;
    private $activities;

    public function __construct($employee, $project)
    {
        $this->employee = $employee;
        $this->project = $project;
        $this->activities = app(ProjectActivityRepository::class);
    }

    public function view(): View
    {
        $query = $this->activities->getActivitiesDetail();

        if ($this->employee) {
            $query->whereHas('members', fn($q) => $q->where('user_id', $this->employee));
        }

        if ($this->project) {
            $query->where('project_activities.project_id', $this->project);
        }

        $activities = $query->get();

        return view('Report::HumanResources.AssignedActivity.export', [
            'activities' => $activities,
        ]);
    }

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
}