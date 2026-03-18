<?php

namespace Modules\Report\Exports\HumanResources;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Illuminate\Contracts\View\View;
use Modules\Project\Models\Project;
use Modules\Project\Models\Enums\ActivityStatus;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProjectSummaryExport implements FromView, ShouldAutoSize, WithStyles, WithStrictNullComparison
{
    use Exportable;

    private $projectIds;

    public function __construct($projectIds = null)
    {
        $this->projectIds = $projectIds ? (array) $projectIds : null;
    }

    private $fileName = 'project_summary_report.xlsx';
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
        $query = Project::query()
            ->whereNotNull('activated_at')
            ->withCount([
                'activities as completed_count' => fn($q) => $q->where('status', ActivityStatus::Completed),
                'activities as under_progress_count' => fn($q) => $q->where('status', ActivityStatus::UnderProgress),
                'activities as not_started_count' => fn($q) => $q->where('status', ActivityStatus::NotStarted),
                'activities as no_required_count' => fn($q) => $q->where('status', ActivityStatus::NoRequired),
                'activities as total_activities',
            ]);

        if ($this->projectIds) {
            $query->whereIn('id', $this->projectIds);
        }


        $projects = $query->orderBy('title')->get();

        return view('Report::HumanResources.ProjectSummary.export', [
            'projects' => $projects,
        ]);
    }

}