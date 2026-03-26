<?php

namespace Modules\Project\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Modules\Project\Models\Project;

class ProjectActivityDataExport implements FromCollection, WithHeadings, WithEvents, WithTitle
{
    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function collection()
    {
        // Load all activities with necessary relationships
        $activities = $this->project->activities()
            ->with(['stage', 'parent', 'members'])
            ->get();

        $data = [];

        // Group by Stage first (same as Gantt chart)
        $stages = $activities->where('activity_level', 'theme')
            ->groupBy('activity_stage_id')
            ->sortKeys();  

        foreach ($stages as $stageId => $themesInStage) {
            // Sort themes within the stage
            $themes = $themesInStage->sortBy('title');

            foreach ($themes as $theme) {
                $data[] = $this->formatRow($theme, count($data) + 1);

                // Get Activities under this Theme
                $childActivities = $activities
                    ->where('parent_id', $theme->id)
                    ->where('activity_level', 'activity')
                    ->sortBy('title');

                foreach ($childActivities as $activity) {
                    $data[] = $this->formatRow($activity, count($data) + 1);

                    // Get Sub-activities under this Activity
                    $subActivities = $activities
                        ->where('parent_id', $activity->id)
                        ->where('activity_level', 'sub_activity')
                        ->sortBy('title');

                    foreach ($subActivities as $sub) {
                        $data[] = $this->formatRow($sub, count($data) + 1);
                    }
                }
            }
        }

        // Add empty rows at the bottom for new data entry
        for ($i = 1; $i <= 5; $i++) {
            $data[] = [
                'sn' => count($data) + $i,
                'activity_level' => '',
                'stage_name' => '',
                'activity_name' => '',
                'parent_activity' => '',
                'start_date' => '',
                'end_date' => '',
                'members' => '',
                'activity_status' => '',
            ];
        }

        return collect($data);
    }

    private function formatRow($activity, int $sn)
    {
        $memberNames = $activity->members->pluck('full_name')->filter()->implode(', ');

        $statusVal = $activity->status instanceof \BackedEnum
            ? $activity->status->value
            : ($activity->status ?? '');

        $statusVal = strtolower(trim($statusVal));

        $statusText = match ($statusVal) {
            'completed' => 'Completed',
            'under progress', 'under_progress', 'in progress' => 'Under Progress',
            'not started', 'not_started' => 'Not Started',
            'no longer required', 'no_required', 'not required' => 'No Longer Required',
            default => 'Not Started',
        };

        return [
            'sn' => $sn,
            'activity_level' => ucfirst(str_replace('_', ' ', $activity->activity_level ?? '')),
            'stage_name' => $activity->stage?->title ?? '',
            'activity_name' => $activity->title ?? '',
            'parent_activity' => $activity->parent?->title ?? '',
            'start_date' => $activity->start_date?->format('Y-m-d'),
            'end_date' => $activity->completion_date?->format('Y-m-d'),
            'members' => $memberNames,
            'activity_status' => $statusText,
        ];
    }

    public function headings(): array
    {
        return [
            'SN',
            'Activity Level',
            'Stage Name',
            'Activity Name',
            'Parent Activity',
            'Start Date',
            'End Date',
            'Members',
            'Activity Status'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle('A1:I' . $highestRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->getStyle('A1:I1')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                ]);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(60);
                $sheet->getColumnDimension('E')->setWidth(45);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(70);
                $sheet->getColumnDimension('I')->setWidth(20);

                $sheet->getStyle('A1:I1')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:I1');
            },
        ];
    }

    public function title(): string
    {
        return 'Activity';
    }
}