<?php

namespace Modules\Tracker\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use Modules\Tracker\Models\Event;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EventExport implements FromCollection, Responsable, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct() {}

    private string $fileName = 'event_export.xlsx';

    private string $writerType = Excel::XLSX;

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:' . $event->sheet->getHighestDataColumn() . '1')
                    ->applyFromArray(['font' => ['bold' => true]]);
            },
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestDataRow())
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '808080'],
                    ],
                ],
            ]);
        return [];
    }

    public function headings(): array
    {
        return [
            'S.N.',
            'Project',
            'Event Name',
            'Event Type',
            'Organized By',
            'From Date',
            'To Date',
            'Country',
            'Province',
            'District',
            'Local Level',
            'Total Government Participants',
            'Total HERDi Participants',
            'Total Other Participants',
            'Total Participants',
            'Accompanying Members',
            'Action Points',
            'Remarks',
        ];
    }

    public function map($row): array
    {
        static $sn = 0;
        $sn++;

        return [
            $sn,
            $row->getProjectTitle(),
            $row->event_name,
            $row->event_type,
            ucfirst($row->event_organized_by),
            $row->getFromDate(),
            $row->getToDate(),
            $row->country,
            $row->province,
            $row->district,
            $row->city_local_level,
            $row->total_participants_government ?? 0,
            $row->total_herdi_participants ?? 0,
            $row->total_other_participants ?? 0,
            $row->getTotalParticipants(),
            $row->accompanyingMembers->map(fn($e) => $e->getFullName())->implode(', '),
            $row->actionPoints->pluck('action_point')->implode("\n"),
            $row->remarks->pluck('remark')->implode("\n"),
        ];
    }

    public function collection()
    {
        return Event::with(['project', 'accompanyingMembers', 'actionPoints', 'remarks'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
