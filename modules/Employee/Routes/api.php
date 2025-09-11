<?php

/*
|--------------------------------------------------------------------------
| Application Routes for User Module
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

use Modules\Employee\Controllers\Api\EmployeeController;
use Modules\Employee\Controllers\Api\EmployeeSupervisorController;
use Modules\Employee\Controllers\Api\LeaveController;

Route::middleware(['api', 'logger'])->prefix('api/employee')->group(function () {
    Route::get('supervisor/{employee}', [EmployeeSupervisorController::class, 'getSupervisors'])->name('api.employee.supervisors.index');

    Route::get('supervisor/{employee}', [EmployeeSupervisorController::class, 'getSupervisors'])->name('api.employee.supervisors.index');

    Route::get('{employee}/leaves/{leave}/show', [LeaveController::class, 'show'])->name('api.employees.leaves.show');
    Route::get('{employee}/leaves/fetch', [LeaveController::class, 'fetchLeave'])->name('api.employees.leaves.fetch');
    Route::post('{employee}/check-leaves', [LeaveController::class, 'checkLeave'])->name('api.employees.check.leaves');
});
