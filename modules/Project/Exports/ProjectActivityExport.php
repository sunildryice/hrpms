<?php

namespace Modules\Project\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Conditional;

class ProjectActivityExport implements FromView, WithEvents, WithTitle
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
            'today'   => now(),
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Fixed widths for main columns (narrow & balanced)
                $sheet->getColumnDimension('A')->setWidth(8);      // SN
                $sheet->getColumnDimension('B')->setWidth(32);     // Activities
                $sheet->getColumnDimension('C')->setWidth(14);     // Type
                $sheet->getColumnDimension('D')->setWidth(26);     // Deliverables
                $sheet->getColumnDimension('E')->setWidth(20);     // Timeline
                $sheet->getColumnDimension('F')->setWidth(20);     // Members
                $sheet->getColumnDimension('G')->setWidth(14);     // Status
                $sheet->getColumnDimension('H')->setWidth(16);     // Extended
                $sheet->getColumnDimension('I')->setWidth(32);     // Remarks
                $sheet->getColumnDimension('J')->setWidth(12);     // Days left
                $sheet->getColumnDimension('K')->setWidth(18);     // Planned Period

                // Very narrow width for Gantt week columns (starting from column L)
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                // Assume Gantt starts from column L (12) onward
                for ($col = 11; $col <= $highestColumnIndex; $col++) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $sheet->getColumnDimension($columnLetter)->setWidth(3); // very narrow for weeks
                }

                // Freeze header rows (first 3 rows: title + year + month/week)
                $sheet->freezePane('A4');

                // Light blue header background (title + main headers)
                $sheet->getStyle('A1:' . $highestColumn . '3')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E6F3FF'],
                    ],
                ]);

                // Thin borders for whole sheet
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Alignments for main columns
                $sheet->getStyle('E:E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('J:J')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('L:' . $highestColumn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Monospace font for all Gantt columns (week cells)
                $sheet->getStyle('L:' . $highestColumn)->getFont()->setName('Consolas');

                // Red background for overdue tasks (Days left < 0)
                $conditional = new Conditional();
                $conditional->setConditionType(Conditional::CONDITION_CELLIS);
                $conditional->setOperatorType(Conditional::OPERATOR_LESSTHAN);
                $conditional->addCondition('0');
                $conditional->getStyle()->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE6E6'],
                    ],
                    'font' => [
                        'color' => ['rgb' => 'B00000'],
                    ],
                ]);

                $sheet->getStyle("J4:J{$highestRow}")->setConditionalStyles([$conditional]);
            },
        ];
    }

    public function title(): string
    {
        return 'Activity Plan';
    }
}