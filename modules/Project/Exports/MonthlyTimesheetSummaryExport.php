<?php

namespace Modules\Project\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonthlyTimesheetSummaryExport implements FromCollection, WithHeadings, WithEvents, WithTitle
{
    public function __construct(
        protected array $allDates,
        protected string $employeeName,
        protected string $yearMonth
    ) {}

    public function collection()
    {
        $rows = [];
        $sn = 1;

        foreach ($this->allDates as $dateKey => $day) {
            $items = $day['items'];
            $carbonDate = $day['carbon'];
            $reason = strip_tags($day['reason'] ?? '');

            if ($items->isEmpty()) {
                $rows[] = [
                    'sn'          => $sn++,
                    'date'        => $carbonDate->format('d M Y'),
                    'project'     => '',
                    'activity'    => '',
                    'description' => $reason ?: 'No Entry',
                    'hours'       => '',
                ];
                continue;
            }

            $projectGroups = $items->groupBy(fn($ts) => optional($ts->project)->id ?? 'unknown');

            foreach ($projectGroups as $projItems) {
                $activityGroups = $projItems->groupBy(fn($ts) => optional($ts->activity)->id ?? 'unknown');

                foreach ($activityGroups as $actItems) {
                    foreach ($actItems as $entry) {
                        $rows[] = [
                            'sn'          => $sn++,
                            'date'        => $carbonDate->format('d M Y'),
                            'project'     => optional($entry->project)->short_name ?? '—',
                            'activity'    => optional($entry->activity)->title ?? '—',
                            'description' => $entry->description ?: '—',
                            'hours'       => number_format($entry->hours_spent ?? 0, 2),
                        ];
                    }
                }
            }

            $reasonText = strip_tags($day['reason'] ?? '');
            $isPartialLeave = str_contains($reasonText, 'First Half') || str_contains($reasonText, 'Second Half');

            if ($isPartialLeave) {
                $rows[] = [
                    'sn'          => '',
                    'date'        => $carbonDate->format('d M Y'),
                    'project'     => '',
                    'activity'    => '',
                    'description' => $reasonText,
                    'hours'       => '',
                ];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return ['SN', 'Date', 'Project', 'Activity', 'Description / Task', 'Hours'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $lastCol = 'F';

                $sheet->getStyle("A1:{$lastCol}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4'],
                    ],
                    'font' => [
                        'bold'  => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                ]);

                $sheet->getStyle("A1:{$lastCol}1")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(16);
                $sheet->getColumnDimension('C')->setWidth(22);
                $sheet->getColumnDimension('D')->setWidth(30);
                $sheet->getColumnDimension('E')->setWidth(55);
                $sheet->getColumnDimension('F')->setWidth(10);

                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:{$lastCol}1");
            },
        ];
    }

    public function title(): string
    {
        return 'Timesheet';
    }
}
