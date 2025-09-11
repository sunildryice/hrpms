<?php

namespace Modules\EmployeeAttendance\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceImport implements WithMultipleSheets
{
    public $totalSheet;

    public function __construct($totalSheet)
    {
        $this->totalSheet = $totalSheet;
    }

    public function sheets(): array
    {
        $sheets = [];

        for ($i=0; $i < $this->totalSheet; $i++) { 
            array_push($sheets, new SheetImport());
        }
        
        return $sheets;
    }
}