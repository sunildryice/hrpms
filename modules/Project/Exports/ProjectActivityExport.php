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
            'today' => now(),
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(40);   // Activities
                $sheet->getColumnDimension('B')->setWidth(16);   // Type
                $sheet->getColumnDimension('C')->setWidth(30);   // Deliverables
                $sheet->getColumnDimension('D')->setWidth(26);   // Timeline
                $sheet->getColumnDimension('E')->setWidth(24);   // Members
                $sheet->getColumnDimension('F')->setWidth(20);   // Status
                $sheet->getColumnDimension('G')->setWidth(20);   // Extended
                $sheet->getColumnDimension('H')->setWidth(34);   // Remarks
    
                // Gantt columns - very narrow
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                for ($col = 9; $col <= $highestColumnIndex; $col++) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $sheet->getColumnDimension($columnLetter)->setWidth(3.4);
                }

                // Freeze header (title + years + months + weeks)
                $sheet->freezePane('A5');

                // Header background + bold
                $sheet->getStyle('A1:' . $highestColumn . '4')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E6F3FF'],
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                // Thin borders everywhere
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Alignments
                $sheet->getStyle('E:E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('I:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // $sheet->getStyle('A:' . $highestColumn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A2:{$highestColumn}4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                // Monospace font for Gantt area
                $sheet->getStyle('I:' . $highestColumn)->getFont()->setName('Consolas');

                $stageStyle = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'e6e6e6'],
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                ];

                $themeNameStyle = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'ffeee6'],
                    ],
                ];

                // === Style Stage rows and Activity Theme name cells ===
                foreach ($sheet->getRowIterator(5, $highestRow) as $row) {
                    $rowIndex = $row->getRowIndex();

                    $cellA = $sheet->getCell("A{$rowIndex}");
                    $cellB = $sheet->getCell("B{$rowIndex}");

                    // Check if this is a stage row (merged cells in A and B)
                    if ($cellA->getMergeRange() && $cellA->getMergeRange() === $cellB->getMergeRange()) {
                        $sheet->getStyle("A{$rowIndex}:{$highestColumn}{$rowIndex}")->applyFromArray($stageStyle);
                        continue;
                    }

                    // Check if this is an Activity Theme row
                    $cellBValue = trim((string) $cellB->getValue());
                    if (strtolower($cellBValue) === 'activity theme') {
                        // Apply style **only to column A** (the name cell)
                        $sheet->getStyle("A{$rowIndex}")->applyFromArray($themeNameStyle);
                    }
                }

                // Convert '█' or '██' markers to full colored background (Gantt bars)
                $activeColor = '7699bc';

                foreach ($sheet->getRowIterator(5, $highestRow) as $row) {
                    $rowIndex = $row->getRowIndex();

                    foreach (range(9, $highestColumnIndex) as $colIdx) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                        $cell = $sheet->getCell("{$colLetter}{$rowIndex}");
                        $value = trim((string) $cell->getValue());

                        if ($value !== '') {
                            // Clear the text (hide █ or ██)
                            $cell->setValue('');

                            // Apply solid color background
                            $cell->getStyle()->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => $activeColor],
                                ],
                            ]);
                        }
                    }
                }

                // Overdue conditional formatting (Days left < 0)
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

                $sheet->getStyle("I5:I{$highestRow}")->setConditionalStyles([$conditional]);

                //Height for the first row
                $sheet->getRowDimension(1)->setRowHeight(30);
            },
        ];
    }

    public function title(): string
    {
        return 'Activity Plan';
    }
}