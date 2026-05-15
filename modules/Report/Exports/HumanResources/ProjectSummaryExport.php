<?php

namespace Modules\Report\Exports\HumanResources;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Illuminate\Contracts\View\View;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Models\Project;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProjectSummaryExport implements FromView, ShouldAutoSize, WithStyles, WithStrictNullComparison
{
    use Exportable;

    private $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $query = Project::query()
            ->with([
                'teamLead:id,full_name',
                'focalPerson:id,full_name',
            ])
            ->withCount([
                'activities as completed_count' => fn($q) => $q->where('status', ActivityStatus::Completed),
                'activities as under_progress_count' => fn($q) => $q->where('status', ActivityStatus::UnderProgress),
                'activities as not_started_count' => fn($q) => $q->where('status', ActivityStatus::NotStarted),
                'activities as no_required_count' => fn($q) => $q->where('status', ActivityStatus::NoRequired),
                'activities as total_activities',
            ]);

        if (!empty($this->filters['team_lead_id'])) {
            $query->where('team_lead_id', $this->filters['team_lead_id']);
        }

        if (!empty($this->filters['focal_person_id'])) {
            $query->where('focal_person_id', $this->filters['focal_person_id']);
        }

        if (!empty($this->filters['project_theme_id'])) {
            $query->where('project_theme_id', $this->filters['project_theme_id']);
        }

        if (!empty($this->filters['approach_id'])) {
            $approachId = (int) $this->filters['approach_id'];
            $query->where(function ($q) use ($approachId) {
                $q->whereJsonContains('approach_ids', $approachId)
                    ->orWhere('approach_ids', 'LIKE', '%"' . $approachId . '"%')
                    ->orWhere('approach_ids', 'LIKE', '%,' . $approachId . ',%');
            });
        }

        if (!empty($this->filters['district_id'])) {
            $districtId = (int) $this->filters['district_id'];
            $query->where(function ($q) use ($districtId) {
                $q->whereJsonContains('district_ids', $districtId)
                    ->orWhere('district_ids', 'LIKE', '%"' . $districtId . '"%')
                    ->orWhere('district_ids', 'LIKE', '%,' . $districtId . ',%');
            });
        }

        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'active') {
                $query->whereNotNull('activated_at');
            } elseif ($this->filters['status'] === 'inactive') {
                $query->whereNull('activated_at');
            }
        }

        $projects = $query->orderBy('title')->get();

        return view('Report::HumanResources.ProjectSummary.export', [
            'projects' => $projects,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle(2)->getFont()->setBold(true);

        $row_count = $sheet->getHighestDataRow();
        $column_count = $sheet->getHighestDataColumn();
        $cellRange = 'A1:' . $column_count . $row_count;

        $sheet->getStyle($cellRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '808080'],
                ],
            ],
        ]);
    }
}