<?php

namespace Modules\Report\Exports\HumanResources;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\Project\Models\ProjectActivityDetail;

class ProjectActivityDetailExport implements FromView, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $query = ProjectActivityDetail::query()
            ->with([
                'projectActivity:id,project_id,activity_stage_id,activity_level,parent_id,title,status,start_date,completion_date',
                'projectActivity.project:id,title,short_name',
                'projectActivity.stage:id,title',
                'projectActivity.parent:id,title',
            ]);

        if (!empty($this->filters['project_id'])) {
            $query->whereHas('projectActivity', function ($q) {
                $q->where('project_id', $this->filters['project_id']);
            });
        }

        if (!empty($this->filters['activity_id'])) {
            $query->where('project_activity_id', $this->filters['activity_id']);
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereHas('projectActivity', function ($q) {
                $q->whereDate('start_date', '>=', $this->filters['from_date']);
            });
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereHas('projectActivity', function ($q) {
                $q->whereDate('completion_date', '<=', $this->filters['to_date']);
            });
        }

        $details = $query->orderBy('created_at', 'desc')->get();

        return view('Report::HumanResources.ProjectActivityDetail.export', compact('details'));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
