<?php 

namespace Modules\Project\Imports;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Project\Models\ProjectActivity;
class ActivityImport implements ToModel, WithHeadingRow
{
    protected $fiscalYear;
    protected $userCode;

    public function __construct($fiscalYear, $userCode)
    {
        $this->fiscalYear = $fiscalYear;
        $this->userCode = $userCode;
    }

    public function model(array $row)
    {
        return new ProjectActivity([
            'title' => $row['title'],
            'start_date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_date']),
            'completion_date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['completion_date']),
            'parent_id' => $row['parent_id'],
            'project_id' => $row['project_id'],
            'created_by' => $this->userCode,
            'fiscal_year' => $this->fiscalYear,
        ]);
    }
}