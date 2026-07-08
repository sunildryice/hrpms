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
        $details = ProjectActivityDetail::query()
            ->with([
                'projectActivity:id,project_id,activity_stage_id,activity_level,parent_id,title,status,start_date,completion_date',
                'projectActivity.project:id,title,short_name',
                'projectActivity.stage:id,title',
                'projectActivity.parent:id,title',
            ])
            ->filterByRequest($this->filters)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Report::HumanResources.ProjectActivityDetail.export', compact('details'));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
