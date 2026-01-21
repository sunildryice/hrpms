<?php

namespace Modules\Project\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
class ActivityExport implements FromView, WithTitle
{
    protected $project;

    public function __construct($project)
    {
        $this->project = $project;
    }

    public function view(): View
    {
        return view('Project::Excel.activity-export', [
            'project' => $this->project,
        ]);
    }
    public function title(): string
    {
        return 'Activity Export';
    }

}