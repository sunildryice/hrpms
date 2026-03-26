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
        // Load ALL activities
        $activities = $this->project->activities()
            ->with(['stage', 'parent', 'members'])
            ->get();

        $data = [];
        $processedIds = [];

        // Group by Stage
        $stages = $activities->groupBy('activity_stage_id');

        foreach ($stages as $stageId => $itemsInStage) {

            // Get Themes
            $themes = $itemsInStage
                ->where('activity_level', 'theme')
                ->sortBy('title');

            foreach ($themes as $theme) {

                // Add Theme
                $data[] = $this->formatRow($theme, count($data) + 1);
                $processedIds[] = $theme->id;

                // Activities under Theme
                $childActivities = $activities
                    ->where('parent_id', $theme->id)
                    ->where('activity_level', 'activity')
                    ->sortBy('title');

                foreach ($childActivities as $activity) {

                    $data[] = $this->formatRow($activity, count($data) + 1);
                    $processedIds[] = $activity->id;

                    // Sub Activities
                    $subActivities = $activities
                        ->where('parent_id', $activity->id)
                        ->where('activity_level', 'sub_activity')
                        ->sortBy('title');

                    foreach ($subActivities as $sub) {
                        $data[] = $this->formatRow($sub, count($data) + 1);
                        $processedIds[] = $sub->id;
                    }
                }
            }
        }

        // Add ANY missing activities
        $remaining = $activities->whereNotIn('id', $processedIds);

        foreach ($remaining as $activity) {
            $data[] = $this->formatRow($activity, count($data) + 1);
        }

        // Remove internal ID before export
        $data = collect($data)->map(function ($row) {
            unset($row['id']);
            return $row;
        });

        // Add empty rows for template (optional)
        for ($i = 1; $i <= 25; $i++) {
            $data->push([
                'sn' => $data->count() + 1,
                'activity_level' => '',
                'stage_name' => '',
                'activity_name' => '',
                'parent_activity' => '',
                'start_date' => '',
                'end_date' => '',
                'members' => '',
                'activity_status' => '',
            ]);
        }

        return $data;
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
            'id' => $activity->id, // important for tracking
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
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ],
                ]);

                $sheet->getStyle('A1:I1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4']
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                ]);

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
        return 'Activity Import Template';
    }
}