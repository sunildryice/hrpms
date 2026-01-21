<?php 

namespace Modules\Project\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Project\Models\ProjectActivity;
class ActivityExport implements FromCollection, WithHeadings
{
    protected $project;
    
    public function __construct($project)
    {
        $this->project = $project;
    }

    public function collection()
    {
        return ProjectActivity::where('project_id', $this->project->id)->get([
            'title',
            'start_date',
            'completion_date',
            'parent_id',
            'project_id',
        ]);
    }

    public function headings(): array
    {
        return [
            'Title',
            'Start Date',
            'Completion Date',
            'Parent ID',
            'Project ID',
        ];
    }
}