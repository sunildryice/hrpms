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

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(6);   // SN
                $sheet->getColumnDimension('B')->setWidth(35);  // Activities
                $sheet->getColumnDimension('C')->setWidth(14);  // Type
                $sheet->getColumnDimension('D')->setWidth(28);  // Deliverables
                $sheet->getColumnDimension('E')->setWidth(22);  // Timeline
                $sheet->getColumnDimension('F')->setWidth(22);  // Members
                $sheet->getColumnDimension('G')->setWidth(14);  // Status
                $sheet->getColumnDimension('H')->setWidth(18);  // Extended
                $sheet->getColumnDimension('I')->setWidth(32);  // Remarks
                $sheet->getColumnDimension('J')->setWidth(12);  // Days left

                // Gantt columns - very narrow
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                for ($col = 11; $col <= $highestColumnIndex; $col++) { // K = 11
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

                // Default style for Gantt area
                $sheet->getStyle('K4:' . $highestColumn . $highestRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB'], // very light gray background
                    ],
                ]);

                // Convert marker '█' → colored bar
                foreach ($sheet->getRowIterator(4, $highestRow) as $row) {
                    $rowIndex = $row->getRowIndex();

                    foreach (range(11, $highestColumnIndex) as $colIdx) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                        $cell = $sheet->getCell("{$colLetter}{$rowIndex}");
                        $value = trim($cell->getValue() ?? '');

                        if ($value !== '') {
                            // Remove the character
                            $cell->setValue('');

                            // Apply filled bar color
                            $cell->getStyle()->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => '5B9BD5'], // medium blue
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