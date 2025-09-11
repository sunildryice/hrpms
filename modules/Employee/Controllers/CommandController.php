<?php

namespace Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CommandController extends Controller
{
    /**
     * Execute artisan command to update employee leave. 
     * @param Request $request
     * @return void
     */
    public function updateEmployeeLeave(Request $request)
    {
        Artisan::call('dryice:update:employee:leave');
        return redirect()->back()->withSuccessMessage('Employee leave updated successfully.');
    }
}