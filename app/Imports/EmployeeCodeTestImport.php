<?php

namespace App\Imports;

use App\Exports\EmployeeCodeTestExport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employee\Models\Employee;

class EmployeeCodeTestImport implements ToCollection
{
    private $rows;

    private $returnData;

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        ++$this->rows;

        $employeeNameAndCodeMapping = [];

        foreach ($rows as $row) {
            $employeeNameFromDb = '';
            $employeeCodeFromDb = '';
            $employeeSuppliedName = isset($row[0]) ? $row[0] : null;

            if ($employeeSuppliedName) {
                $employee = Employee::where('full_name', 'LIKE', '%' . $employeeSuppliedName . '%')->first();
                if ($employee) {
                    $employeeNameFromDb = $employee->full_name;
                    $employeeCodeFromDb = $employee->employee_code;
                }
            }

            array_push($employeeNameAndCodeMapping, [
                'employee_supplied_name'    => $employeeSuppliedName,
                'employee_name'             => $employeeNameFromDb,
                'employee_code'             => $employeeCodeFromDb
            ]); 
        }

        $this->returnData = $employeeNameAndCodeMapping;
    }

    public function returnData()
    {
        return $this->returnData;
    }
}
