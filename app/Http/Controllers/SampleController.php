<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeCodeTestExport;
use App\Imports\EmployeeCodeTestImport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class SampleController extends Controller
{

    public function importPage()
    {
        return view('employeeNameCodeImport');
    }


    /**
     * Get the employee code from employee name.
     * @param Request $request
     * @return mixed
     */
    public function import(Request $request)
    {
        // Format - Simply list the employee names in the first column of an excel sheet
        // And this import will export an excel file with employee name and employee code mapping that matches the imported employee names.

        $request->validate([
            'test_file' => 'required|max:5120|mimes:xlsx'
        ], [
            'test_file.required' => 'Please choose the file!',
            'test_file.max' => 'File size cannot exceed :max KB',
            'test_file.mimes' => 'Please upload excel file!'
        ]);

        $file = $request->hasFile('test_file') ? $request->file('test_file') : null;

        try {
            $import = new EmployeeCodeTestImport();
            Excel::import($import, $file, null, \Maatwebsite\Excel\Excel::XLSX);
            $returnData = $import->returnData();
            return Excel::download(new EmployeeCodeTestExport($returnData), 'employee-name-codes.xlsx');
        } catch (Throwable $th) {
            // dd($th);
            return redirect()->back()->withWarningMessage('Something went wrong!');
        }
    }

}
