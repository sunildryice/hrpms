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
                $sheet->getColumnDimension('F')->setWidth(14);   // Status
                $sheet->getColumnDimension('G')->setWidth(20);   // Extended
                $sheet->getColumnDimension('H')->setWidth(34);   // Remarks
                $sheet->getColumnDimension('I')->setWidth(14);   // Days left
    
                // Gantt columns - very narrow
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                for ($col = 10; $col <= $highestColumnIndex; $col++) { // K = 11
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $sheet->getColumnDimension($columnLetter)->setWidth(3.4);
                }

                // Freeze header (title + months + weeks)
                $sheet->freezePane('A4');

                // Header background + bold
                $sheet->getStyle('A1:' . $highestColumn . '3')->applyFromArray([
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
                $sheet->getStyle('J:J')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K:' . $highestColumn)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K:' . $highestColumn)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Monospace font for Gantt area
                $sheet->getStyle('K:' . $highestColumn)->getFont()->setName('Consolas');

                // Convert '█' or '██' markers to full colored background
                $activeColor = '5B9BD5'; // nice blue (you can change)
    
                foreach ($sheet->getRowIterator(4, $highestRow) as $row) {
                    $rowIndex = $row->getRowIndex();

                    foreach (range(10, $highestColumnIndex) as $colIdx) {
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

                $sheet->getStyle("J4:J{$highestRow}")->setConditionalStyles([$conditional]);
            },
        ];
    }

    public function title(): string
    {
        return 'Activity Plan';
    }
}