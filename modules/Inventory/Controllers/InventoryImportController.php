<?php

namespace Modules\Inventory\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Inventory\Imports\InventoryImport;
use Exception;

class InventoryImportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
    }

    /**
     * Show the form for importing the leave of employees.
     *
     * @return mixed
     */
    public function create()
    {
        return view('Inventory::import');
    }

    /**
     * Import leave of employees in storage.
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        set_time_limit(120);
        $request->validate([
            'inventory' => 'required|max:5120|mimes:xlsx'
        ], [
            'inventory.required' => 'Please choose the file!',
            'inventory.max' => 'File size cannot exceed :max KB',
            'inventory.mimes' => 'Please upload excel file!'
        ]);

        $file = $request->hasFile('inventory') ? $request->file('inventory') : null;

        try {
            Excel::import(new InventoryImport(), $file, null, \Maatwebsite\Excel\Excel::XLSX);

            return redirect()->route('inventories.index')->withSuccessMessage('Inventory records are imported successfully.');
        } catch (Exception $th) {
            return redirect()->back()->withWarningMessage('Please upload inventory sheet as per prescribed format.');
        }
    }
}
