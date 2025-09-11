<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;


class EmployeeCodeTestExport implements FromArray
{
    // use Exportable;
    protected $data;

    private $fileName = 'employee_name_code.xlsx';
    
    /**
    * Optional Writer Type
    */
    private $writerType = Excel::XLSX;
    
    /**
    * Optional headers
    */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $array = [];

        $count = count($this->data);

        foreach ($this->data as $key => $data) {

            if ($key == 0) {
                array_push($array, [
                    'Employee Name',
                    'Employee Code'
                ]);
            }

            array_push($array, [
                'employee_name' => $data['employee_name'],
                'employee_code' => $data['employee_code']
            ]);

            if ($key == $count-1) {
                array_push($array, [
                    'End',
                    'End'
                ]);
            }

        }

        return $array;
    }
   
}
